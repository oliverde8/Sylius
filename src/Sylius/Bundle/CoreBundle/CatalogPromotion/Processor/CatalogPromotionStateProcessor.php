<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Processor;

use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\CatalogPromotionEligibilityCheckerInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionTransitions;

final class CatalogPromotionStateProcessor implements CatalogPromotionStateProcessorInterface
{
    public function __construct(
        private CatalogPromotionEligibilityCheckerInterface $catalogPromotionEligibilityChecker,
        private StateMachineInterface $stateMachineFactory,
    ) {
    }

    public function process(CatalogPromotionInterface $catalogPromotion): void
    {
        if ($this->stateMachineFactory->can($catalogPromotion, CatalogPromotionTransitions::GRAPH, CatalogPromotionTransitions::TRANSITION_PROCESS)) {
            $this->stateMachineFactory->apply($catalogPromotion, CatalogPromotionTransitions::GRAPH, CatalogPromotionTransitions::TRANSITION_PROCESS);

            return;
        }

        if (!$this->catalogPromotionEligibilityChecker->isCatalogPromotionEligible($catalogPromotion)) {
            if ($this->stateMachineFactory->can($catalogPromotion, CatalogPromotionTransitions::GRAPH, CatalogPromotionTransitions::TRANSITION_DEACTIVATE)) {
                $this->stateMachineFactory->apply($catalogPromotion, CatalogPromotionTransitions::GRAPH, CatalogPromotionTransitions::TRANSITION_DEACTIVATE);
            }

            return;
        }

        if ($this->stateMachineFactory->can($catalogPromotion, CatalogPromotionTransitions::GRAPH, CatalogPromotionTransitions::TRANSITION_ACTIVATE)) {
            $this->stateMachineFactory->apply($catalogPromotion, CatalogPromotionTransitions::GRAPH, CatalogPromotionTransitions::TRANSITION_ACTIVATE);
        }
    }
}
