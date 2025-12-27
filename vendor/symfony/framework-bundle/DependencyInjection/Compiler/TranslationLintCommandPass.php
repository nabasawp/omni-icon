<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler;

use OmniIconDeps\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use OmniIconDeps\Symfony\Component\DependencyInjection\ContainerBuilder;
use OmniIconDeps\Symfony\Component\Translation\TranslatorBagInterface;
use OmniIconDeps\Symfony\Contracts\Translation\TranslatorInterface;
final class TranslationLintCommandPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('console.command.translation_lint') || !$container->has('translator')) {
            return;
        }
        $translatorClass = $container->getParameterBag()->resolveValue($container->findDefinition('translator')->getClass());
        if (!is_subclass_of($translatorClass, TranslatorInterface::class) || !is_subclass_of($translatorClass, TranslatorBagInterface::class)) {
            $container->removeDefinition('console.command.translation_lint');
        }
    }
}
