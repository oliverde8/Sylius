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

namespace Sylius\Bundle\ApiBundle\StateProcessor;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Sylius\Bundle\ApiBundle\Exception\ProvinceCannotBeRemoved;
use Sylius\Component\Addressing\Checker\CountryProvincesDeletionCheckerInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Webmozart\Assert\Assert;

/**
 * @experimental
 *
 * @implements ProcessorInterface<CountryInterface>
 */
final readonly class CountryProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
        private ProcessorInterface $removeProcessor,
        private CountryProvincesDeletionCheckerInterface $countryProvincesDeletionChecker,
    ) {
    }

    /**
     * @param CountryInterface $data
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        Assert::isInstanceOf($data, CountryInterface::class);

        if ($operation instanceof DeleteOperationInterface) {
            return $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        }

        if (!$this->countryProvincesDeletionChecker->isDeletable($data)) {
            throw new ProvinceCannotBeRemoved();
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
