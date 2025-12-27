<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Bridge\Twig\Test\Traits;

use OmniIconDeps\Symfony\Component\Form\FormRenderer;
use OmniIconDeps\Twig\Environment;
use OmniIconDeps\Twig\RuntimeLoader\RuntimeLoaderInterface;
trait RuntimeLoaderProvider
{
    /**
     * @return void
     */
    protected function registerTwigRuntimeLoader(Environment $environment, FormRenderer $renderer)
    {
        $loader = $this->createMock(RuntimeLoaderInterface::class);
        $loader->expects($this->any())->method('load')->willReturnMap([['OmniIconDeps\Symfony\Component\Form\FormRenderer', $renderer]]);
        $environment->addRuntimeLoader($loader);
    }
}
