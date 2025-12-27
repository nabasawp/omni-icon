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

use OmniIconDeps\Symfony\Component\TypeInfo\TypeContext\TypeContextFactory;
use OmniIconDeps\Symfony\Component\TypeInfo\TypeResolver\ReflectionParameterTypeResolver;
use OmniIconDeps\Symfony\Component\TypeInfo\TypeResolver\ReflectionPropertyTypeResolver;
use OmniIconDeps\Symfony\Component\TypeInfo\TypeResolver\ReflectionReturnTypeResolver;
use OmniIconDeps\Symfony\Component\TypeInfo\TypeResolver\ReflectionTypeResolver;
use OmniIconDeps\Symfony\Component\TypeInfo\TypeResolver\TypeResolver;
use OmniIconDeps\Symfony\Component\TypeInfo\TypeResolver\TypeResolverInterface;
return static function (ContainerConfigurator $container) {
    $container->services()->set('type_info.type_context_factory', TypeContextFactory::class)->args([service('type_info.resolver.string')->nullOnInvalid(), []])->set('type_info.resolver', TypeResolver::class)->args([service_locator([\ReflectionType::class => service('type_info.resolver.reflection_type'), \ReflectionParameter::class => service('type_info.resolver.reflection_parameter'), \ReflectionProperty::class => service('type_info.resolver.reflection_property'), \ReflectionFunctionAbstract::class => service('type_info.resolver.reflection_return')])])->alias(TypeResolverInterface::class, 'type_info.resolver')->set('type_info.resolver.reflection_type', ReflectionTypeResolver::class)->args([service('type_info.type_context_factory')])->set('type_info.resolver.reflection_parameter', ReflectionParameterTypeResolver::class)->args([service('type_info.resolver.reflection_type'), service('type_info.type_context_factory')])->set('type_info.resolver.reflection_property', ReflectionPropertyTypeResolver::class)->args([service('type_info.resolver.reflection_type'), service('type_info.type_context_factory')])->set('type_info.resolver.reflection_return', ReflectionReturnTypeResolver::class)->args([service('type_info.resolver.reflection_type'), service('type_info.type_context_factory')]);
};
