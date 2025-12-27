<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Component\DependencyInjection\Loader\Configurator;

use OmniIconDeps\Symfony\Component\ObjectMapper\Metadata\ObjectMapperMetadataFactoryInterface;
use OmniIconDeps\Symfony\Component\ObjectMapper\Metadata\ReflectionObjectMapperMetadataFactory;
use OmniIconDeps\Symfony\Component\ObjectMapper\ObjectMapper;
use OmniIconDeps\Symfony\Component\ObjectMapper\ObjectMapperInterface;
return static function (ContainerConfigurator $container) {
    $container->services()->set('object_mapper.metadata_factory', ReflectionObjectMapperMetadataFactory::class)->alias(ObjectMapperMetadataFactoryInterface::class, 'object_mapper.metadata_factory')->set('object_mapper', ObjectMapper::class)->args([service('object_mapper.metadata_factory'), service('property_accessor')->ignoreOnInvalid(), tagged_locator('object_mapper.transform_callable'), tagged_locator('object_mapper.condition_callable')])->alias(ObjectMapperInterface::class, 'object_mapper');
};
