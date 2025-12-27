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

use OmniIconDeps\Psr\Cache\CacheItemPoolInterface;
use OmniIconDeps\Symfony\Bundle\FrameworkBundle\CacheWarmer\SerializerCacheWarmer;
use OmniIconDeps\Symfony\Component\Cache\Adapter\PhpArrayAdapter;
use OmniIconDeps\Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use OmniIconDeps\Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use OmniIconDeps\Symfony\Component\ErrorHandler\ErrorRenderer\SerializerErrorRenderer;
use OmniIconDeps\Symfony\Component\PropertyInfo\Extractor\SerializerExtractor;
use OmniIconDeps\Symfony\Component\Serializer\Encoder\CsvEncoder;
use OmniIconDeps\Symfony\Component\Serializer\Encoder\DecoderInterface;
use OmniIconDeps\Symfony\Component\Serializer\Encoder\EncoderInterface;
use OmniIconDeps\Symfony\Component\Serializer\Encoder\JsonEncoder;
use OmniIconDeps\Symfony\Component\Serializer\Encoder\XmlEncoder;
use OmniIconDeps\Symfony\Component\Serializer\Encoder\YamlEncoder;
use OmniIconDeps\Symfony\Component\Serializer\Mapping\ClassDiscriminatorFromClassMetadata;
use OmniIconDeps\Symfony\Component\Serializer\Mapping\ClassDiscriminatorResolverInterface;
use OmniIconDeps\Symfony\Component\Serializer\Mapping\Factory\CacheClassMetadataFactory;
use OmniIconDeps\Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use OmniIconDeps\Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use OmniIconDeps\Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use OmniIconDeps\Symfony\Component\Serializer\Mapping\Loader\LoaderChain;
use OmniIconDeps\Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use OmniIconDeps\Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use OmniIconDeps\Symfony\Component\Serializer\NameConverter\SnakeCaseToCamelCaseNameConverter;
use OmniIconDeps\Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use OmniIconDeps\Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use OmniIconDeps\Symfony\Component\Serializer\Normalizer\ConstraintViolationListNormalizer;
use OmniIconDeps\Symfony\Component\Serializer\Normalizer\DataUriNormalizer;
use OmniIconDeps\Symfony\Component\Serializer\Normalizer\DateIntervalNormalizer;
use OmniIconDeps\Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use OmniIconDeps\Symfony\Component\Serializer\Normalizer\DateTimeZoneNormalizer;
use OmniIconDeps\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use OmniIconDeps\Symfony\Component\Serializer\Normalizer\FormErrorNormalizer;
use OmniIconDeps\Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use OmniIconDeps\Symfony\Component\Serializer\Normalizer\MimeMessageNormalizer;
use OmniIconDeps\Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use OmniIconDeps\Symfony\Component\Serializer\Normalizer\NumberNormalizer;
use OmniIconDeps\Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use OmniIconDeps\Symfony\Component\Serializer\Normalizer\ProblemNormalizer;
use OmniIconDeps\Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use OmniIconDeps\Symfony\Component\Serializer\Normalizer\TranslatableNormalizer;
use OmniIconDeps\Symfony\Component\Serializer\Normalizer\UidNormalizer;
use OmniIconDeps\Symfony\Component\Serializer\Normalizer\UnwrappingDenormalizer;
use OmniIconDeps\Symfony\Component\Serializer\Serializer;
use OmniIconDeps\Symfony\Component\Serializer\SerializerInterface;
return static function (ContainerConfigurator $container) {
    $container->parameters()->set('serializer.mapping.cache.file', '%kernel.build_dir%/serialization.php');
    $container->services()->set('serializer', Serializer::class)->args([[], [], []])->alias(SerializerInterface::class, 'serializer')->alias(NormalizerInterface::class, 'serializer')->alias(DenormalizerInterface::class, 'serializer')->alias(EncoderInterface::class, 'serializer')->alias(DecoderInterface::class, 'serializer')->alias('serializer.property_accessor', 'property_accessor')->set('serializer.mapping.class_discriminator_resolver', ClassDiscriminatorFromClassMetadata::class)->args([service('serializer.mapping.class_metadata_factory')])->alias(ClassDiscriminatorResolverInterface::class, 'serializer.mapping.class_discriminator_resolver')->set('serializer.normalizer.constraint_violation_list', ConstraintViolationListNormalizer::class)->args([1 => service('serializer.name_converter.metadata_aware')])->autowire(\true)->tag('serializer.normalizer', ['built_in' => \true, 'priority' => -915])->set('serializer.normalizer.mime_message', MimeMessageNormalizer::class)->args([service('serializer.normalizer.property')])->tag('serializer.normalizer', ['built_in' => \true, 'priority' => -915])->set('serializer.normalizer.datetimezone', DateTimeZoneNormalizer::class)->tag('serializer.normalizer', ['built_in' => \true, 'priority' => -915])->set('serializer.normalizer.dateinterval', DateIntervalNormalizer::class)->tag('serializer.normalizer', ['built_in' => \true, 'priority' => -915])->set('serializer.normalizer.data_uri', DataUriNormalizer::class)->args([service('mime_types')->nullOnInvalid()])->tag('serializer.normalizer', ['built_in' => \true, 'priority' => -920])->set('serializer.normalizer.datetime', DateTimeNormalizer::class)->tag('serializer.normalizer', ['built_in' => \true, 'priority' => -910])->set('serializer.normalizer.json_serializable', JsonSerializableNormalizer::class)->args([null, null])->tag('serializer.normalizer', ['built_in' => \true, 'priority' => -950])->set('serializer.normalizer.problem', ProblemNormalizer::class)->args([param('kernel.debug'), '$translator' => service('translator')->nullOnInvalid()])->tag('serializer.normalizer', ['built_in' => \true, 'priority' => -890])->set('serializer.denormalizer.unwrapping', UnwrappingDenormalizer::class)->args([service('serializer.property_accessor')])->tag('serializer.normalizer', ['built_in' => \true, 'priority' => 1000])->set('serializer.normalizer.uid', UidNormalizer::class)->tag('serializer.normalizer', ['built_in' => \true, 'priority' => -890])->set('serializer.normalizer.translatable', TranslatableNormalizer::class)->args(['$translator' => service('translator')])->tag('serializer.normalizer', ['built_in' => \true, 'priority' => -920])->set('serializer.normalizer.form_error', FormErrorNormalizer::class)->tag('serializer.normalizer', ['built_in' => \true, 'priority' => -915])->set('serializer.normalizer.object', ObjectNormalizer::class)->args([service('serializer.mapping.class_metadata_factory'), service('serializer.name_converter.metadata_aware'), service('serializer.property_accessor'), service('property_info')->ignoreOnInvalid(), service('serializer.mapping.class_discriminator_resolver')->ignoreOnInvalid(), null, abstract_arg('default context, set in the SerializerPass'), service('property_info')->ignoreOnInvalid()])->tag('serializer.normalizer', ['built_in' => \true, 'priority' => -1000])->set('serializer.normalizer.property', PropertyNormalizer::class)->args([service('serializer.mapping.class_metadata_factory'), service('serializer.name_converter.metadata_aware'), service('property_info')->ignoreOnInvalid(), service('serializer.mapping.class_discriminator_resolver')->ignoreOnInvalid(), null])->set('serializer.denormalizer.array', ArrayDenormalizer::class)->tag('serializer.normalizer', ['built_in' => \true, 'priority' => -990])->set('serializer.mapping.chain_loader', LoaderChain::class)->args([[]])->set('serializer.mapping.attribute_loader', AttributeLoader::class)->args([\true, []])->set('serializer.mapping.class_metadata_factory', ClassMetadataFactory::class)->args([service('serializer.mapping.chain_loader')])->alias(ClassMetadataFactoryInterface::class, 'serializer.mapping.class_metadata_factory')->set('serializer.mapping.cache_warmer', SerializerCacheWarmer::class)->args([abstract_arg('The serializer metadata loaders'), param('serializer.mapping.cache.file')])->tag('kernel.cache_warmer')->set('serializer.mapping.cache.symfony', CacheItemPoolInterface::class)->factory([PhpArrayAdapter::class, 'create'])->args([param('serializer.mapping.cache.file'), service('cache.serializer')])->set('serializer.mapping.cache_class_metadata_factory', CacheClassMetadataFactory::class)->decorate('serializer.mapping.class_metadata_factory')->args([service('serializer.mapping.cache_class_metadata_factory.inner'), service('serializer.mapping.cache.symfony')])->set('serializer.encoder.xml', XmlEncoder::class)->tag('serializer.encoder', ['built_in' => \true])->set('serializer.encoder.json', JsonEncoder::class)->args([null, null])->tag('serializer.encoder', ['built_in' => \true])->set('serializer.encoder.yaml', YamlEncoder::class)->args([null, null])->tag('serializer.encoder', ['built_in' => \true])->set('serializer.encoder.csv', CsvEncoder::class)->tag('serializer.encoder', ['built_in' => \true])->set('serializer.name_converter.camel_case_to_snake_case', CamelCaseToSnakeCaseNameConverter::class)->set('serializer.name_converter.snake_case_to_camel_case', SnakeCaseToCamelCaseNameConverter::class)->set('serializer.name_converter.metadata_aware.abstract', MetadataAwareNameConverter::class)->abstract()->args([service('serializer.mapping.class_metadata_factory')])->set('serializer.name_converter.metadata_aware')->parent('serializer.name_converter.metadata_aware.abstract')->set('property_info.serializer_extractor', SerializerExtractor::class)->args([service('serializer.mapping.class_metadata_factory')])->tag('property_info.list_extractor', ['priority' => -999])->alias('error_renderer', 'error_renderer.serializer')->alias('error_renderer.serializer', 'error_handler.error_renderer.serializer')->set('error_handler.error_renderer.serializer', SerializerErrorRenderer::class)->args([service('serializer'), inline_service()->factory([SerializerErrorRenderer::class, 'getPreferredFormat'])->args([service('request_stack')]), inline_service(ErrorRendererInterface::class)->factory([\Closure::class, 'fromCallable'])->args([[service('error_renderer.default'), 'render']])->lazy(), inline_service()->factory([HtmlErrorRenderer::class, 'isDebug'])->args([service('request_stack'), param('kernel.debug')])])->set('serializer.normalizer.backed_enum', BackedEnumNormalizer::class)->tag('serializer.normalizer', ['built_in' => \true, 'priority' => -915])->set('serializer.normalizer.number', NumberNormalizer::class)->tag('serializer.normalizer', ['built_in' => \true, 'priority' => -915]);
};
