<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Component\HttpKernel\DependencyInjection;

use OmniIconDeps\Symfony\Component\DependencyInjection\Compiler\MergeExtensionConfigurationPass as BaseMergeExtensionConfigurationPass;
use OmniIconDeps\Symfony\Component\DependencyInjection\ContainerBuilder;
/**
 * Ensures certain extensions are always loaded.
 *
 * @author Kris Wallsmith <kris@symfony.com>
 */
class MergeExtensionConfigurationPass extends BaseMergeExtensionConfigurationPass
{
    /**
     * @param string[] $extensions
     */
    public function __construct(private array $extensions)
    {
    }
    public function process(ContainerBuilder $container): void
    {
        foreach ($this->extensions as $extension) {
            if (!\count($container->getExtensionConfig($extension))) {
                $container->loadFromExtension($extension, []);
            }
        }
        parent::process($container);
    }
}
