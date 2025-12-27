<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\UX\Icons\Twig;

use OmniIconDeps\Twig\Extension\AbstractExtension;
use OmniIconDeps\Twig\TwigFunction;
/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
final class UXIconExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [new TwigFunction('ux_icon', [UXIconRuntime::class, 'renderIcon'], ['is_safe' => ['html']])];
    }
}
