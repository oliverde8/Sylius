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

namespace spec\Sylius\Bundle\ApiBundle\StateProcessor\Input;

use ApiPlatform\Metadata\Operation;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Command\ChannelCodeAwareInterface;
use Sylius\Bundle\ApiBundle\Command\LocaleCodeAwareInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

final class LocaleCodeAwareInputDataProcessorSpec extends ObjectBehavior
{
    function let(LocaleContextInterface $localeContext): void
    {
        $this->beConstructedWith($localeContext);
    }

    function it_adds_locale_code_if_it_is_not_defined_in_object(
        LocaleContextInterface $localeContext,
        LocaleCodeAwareInterface $command,
        Operation $operation,
    ): void {
        $command->getLocaleCode()->willReturn(null);
        $command->setLocaleCode('en_US')->shouldBeCalled();

        $localeContext->getLocaleCode()->willReturn('en_US');

        $this->process($command, $operation)->shouldReturn([$command, $operation, [], []]);
    }

    function it_does_nothing_if_locale_code_is_defined_in_object(
        LocaleContextInterface $localeContext,
        LocaleCodeAwareInterface $command,
        Operation $operation,
    ): void {
        $command->getLocaleCode()->willReturn('en_US');
        $command->setLocaleCode(Argument::any())->shouldNotBeCalled();

        $localeContext->getLocaleCode()->shouldNotBeCalled();

        $this->process($command, $operation)->shouldReturn([$command, $operation, [], []]);
    }

    function it_can_process_only_locale_code_aware_interface(
        LocaleCodeAwareInterface $command,
        ChannelCodeAwareInterface $wrongCommand,
        Operation $operation,
    ): void {
        $this->supports($command, $operation)->shouldReturn(true);
        $this->supports($wrongCommand, $operation)->shouldReturn(false);
    }
}
