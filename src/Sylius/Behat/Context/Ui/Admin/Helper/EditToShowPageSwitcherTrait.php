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

namespace Sylius\Behat\Context\Ui\Admin\Helper;

trait EditToShowPageSwitcherTrait
{
    private string $resourceName;

    public function switchToShowPage(): void
    {
        $this->getElement('show_' . $this->resourceName . '_button')->click();
    }

    abstract protected function defineResourceName(): void;
}
