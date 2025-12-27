<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Bundle\TwigBundle\DependencyInjection\Compiler;

use OmniIconDeps\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use OmniIconDeps\Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use OmniIconDeps\Symfony\Component\DependencyInjection\ContainerBuilder;
use OmniIconDeps\Symfony\Component\DependencyInjection\Reference;
/**
 * Registers Twig runtime services.
 */
class RuntimeLoaderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('twig.runtime_loader')) {
            return;
        }
        $definition = $container->getDefinition('twig.runtime_loader');
        $mapping = [];
        foreach ($container->findTaggedServiceIds('twig.runtime', \true) as $id => $attributes) {
            $def = $container->getDefinition($id);
            $mapping[$def->getClass()] = new Reference($id);
        }
        $definition->replaceArgument(0, ServiceLocatorTagPass::register($container, $mapping));
    }
}
