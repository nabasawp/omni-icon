<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Bridge\Twig\Extension;

use OmniIconDeps\Twig\Extension\AbstractExtension;
use OmniIconDeps\Twig\TwigFunction;
/**
 * @author Kévin Dunglas <kevin@dunglas.dev>
 */
final class ImportMapExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [new TwigFunction('importmap', [ImportMapRuntime::class, 'importmap'], ['is_safe' => ['html']])];
    }
}
