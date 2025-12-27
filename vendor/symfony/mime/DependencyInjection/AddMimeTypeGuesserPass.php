<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Component\Mime\DependencyInjection;

use OmniIconDeps\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use OmniIconDeps\Symfony\Component\DependencyInjection\ContainerBuilder;
use OmniIconDeps\Symfony\Component\DependencyInjection\Reference;
/**
 * Registers custom mime types guessers.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class AddMimeTypeGuesserPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->has('mime_types')) {
            $definition = $container->findDefinition('mime_types');
            foreach ($container->findTaggedServiceIds('mime.mime_type_guesser', \true) as $id => $attributes) {
                $definition->addMethodCall('registerGuesser', [new Reference($id)]);
            }
        }
    }
}
