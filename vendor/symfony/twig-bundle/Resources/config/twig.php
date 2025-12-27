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

use OmniIconDeps\Psr\Container\ContainerInterface;
use OmniIconDeps\Symfony\Bridge\Twig\AppVariable;
use OmniIconDeps\Symfony\Bridge\Twig\DataCollector\TwigDataCollector;
use OmniIconDeps\Symfony\Bridge\Twig\ErrorRenderer\TwigErrorRenderer;
use OmniIconDeps\Symfony\Bridge\Twig\EventListener\TemplateAttributeListener;
use OmniIconDeps\Symfony\Bridge\Twig\Extension\AssetExtension;
use OmniIconDeps\Symfony\Bridge\Twig\Extension\EmojiExtension;
use OmniIconDeps\Symfony\Bridge\Twig\Extension\ExpressionExtension;
use OmniIconDeps\Symfony\Bridge\Twig\Extension\HtmlSanitizerExtension;
use OmniIconDeps\Symfony\Bridge\Twig\Extension\HttpFoundationExtension;
use OmniIconDeps\Symfony\Bridge\Twig\Extension\HttpKernelExtension;
use OmniIconDeps\Symfony\Bridge\Twig\Extension\HttpKernelRuntime;
use OmniIconDeps\Symfony\Bridge\Twig\Extension\ProfilerExtension;
use OmniIconDeps\Symfony\Bridge\Twig\Extension\RoutingExtension;
use OmniIconDeps\Symfony\Bridge\Twig\Extension\SerializerExtension;
use OmniIconDeps\Symfony\Bridge\Twig\Extension\SerializerRuntime;
use OmniIconDeps\Symfony\Bridge\Twig\Extension\StopwatchExtension;
use OmniIconDeps\Symfony\Bridge\Twig\Extension\TranslationExtension;
use OmniIconDeps\Symfony\Bridge\Twig\Extension\WebLinkExtension;
use OmniIconDeps\Symfony\Bridge\Twig\Extension\WorkflowExtension;
use OmniIconDeps\Symfony\Bridge\Twig\Extension\YamlExtension;
use OmniIconDeps\Symfony\Bridge\Twig\Translation\TwigExtractor;
use OmniIconDeps\Symfony\Bundle\TwigBundle\CacheWarmer\TemplateCacheWarmer;
use OmniIconDeps\Symfony\Bundle\TwigBundle\DependencyInjection\Configurator\EnvironmentConfigurator;
use OmniIconDeps\Symfony\Bundle\TwigBundle\TemplateIterator;
use OmniIconDeps\Twig\Cache\ChainCache;
use OmniIconDeps\Twig\Cache\FilesystemCache;
use OmniIconDeps\Twig\Cache\ReadOnlyFilesystemCache;
use OmniIconDeps\Twig\Environment;
use OmniIconDeps\Twig\ExpressionParser\Infix\BinaryOperatorExpressionParser;
use OmniIconDeps\Twig\Extension\CoreExtension;
use OmniIconDeps\Twig\Extension\DebugExtension;
use OmniIconDeps\Twig\Extension\EscaperExtension;
use OmniIconDeps\Twig\Extension\OptimizerExtension;
use OmniIconDeps\Twig\Extension\StagingExtension;
use OmniIconDeps\Twig\ExtensionSet;
use OmniIconDeps\Twig\Loader\ChainLoader;
use OmniIconDeps\Twig\Loader\FilesystemLoader;
use OmniIconDeps\Twig\Profiler\Profile;
use OmniIconDeps\Twig\RuntimeLoader\ContainerRuntimeLoader;
use OmniIconDeps\Twig\Template;
use OmniIconDeps\Twig\TemplateWrapper;
return static function (ContainerConfigurator $container) {
    $container->services()->set('twig', Environment::class)->args([service('twig.loader'), abstract_arg('Twig options')])->call('addGlobal', ['app', service('twig.app_variable')])->call('addRuntimeLoader', [service('twig.runtime_loader')])->configurator([service('twig.configurator.environment'), 'configure'])->tag('container.preload', ['class' => FilesystemCache::class])->tag('container.preload', ['class' => CoreExtension::class])->tag('container.preload', ['class' => EscaperExtension::class])->tag('container.preload', ['class' => OptimizerExtension::class])->tag('container.preload', ['class' => StagingExtension::class])->tag('container.preload', ['class' => BinaryOperatorExpressionParser::class])->tag('container.preload', ['class' => ExtensionSet::class])->tag('container.preload', ['class' => Template::class])->tag('container.preload', ['class' => TemplateWrapper::class])->alias(Environment::class, 'twig')->set('twig.app_variable', AppVariable::class)->call('setEnvironment', [param('kernel.environment')])->call('setDebug', [param('kernel.debug')])->call('setTokenStorage', [service('security.token_storage')->ignoreOnInvalid()])->call('setRequestStack', [service('request_stack')->ignoreOnInvalid()])->call('setLocaleSwitcher', [service('translation.locale_switcher')->ignoreOnInvalid()])->call('setEnabledLocales', [param('kernel.enabled_locales')])->set('twig.template_iterator', TemplateIterator::class)->args([service('kernel'), abstract_arg('Twig paths'), param('twig.default_path'), abstract_arg('File name pattern')])->set('twig.template_cache.runtime_cache', FilesystemCache::class)->args([param('kernel.cache_dir') . '/twig'])->set('twig.template_cache.readonly_cache', ReadOnlyFilesystemCache::class)->args([param('kernel.build_dir') . '/twig'])->set('twig.template_cache.warmup_cache', FilesystemCache::class)->args([param('kernel.build_dir') . '/twig'])->set('twig.template_cache.chain', ChainCache::class)->args([[service('twig.template_cache.readonly_cache'), service('twig.template_cache.runtime_cache')]])->set('twig.template_cache_warmer', TemplateCacheWarmer::class)->args([service(ContainerInterface::class), service('twig.template_iterator'), service('twig.template_cache.warmup_cache')])->tag('kernel.cache_warmer')->tag('container.service_subscriber', ['id' => 'twig'])->set('twig.loader.native_filesystem', FilesystemLoader::class)->args([[], param('kernel.project_dir')])->tag('twig.loader')->set('twig.loader.chain', ChainLoader::class)->set('twig.extension.profiler', ProfilerExtension::class)->args([service('twig.profile'), service('debug.stopwatch')->ignoreOnInvalid()])->set('twig.profile', Profile::class)->set('data_collector.twig', TwigDataCollector::class)->args([service('twig.profile'), service('twig')])->tag('data_collector', ['template' => '@WebProfiler/Collector/twig.html.twig', 'id' => 'twig', 'priority' => 257])->set('twig.extension.trans', TranslationExtension::class)->args([service('translator')->nullOnInvalid()])->tag('twig.extension')->set('twig.extension.assets', AssetExtension::class)->args([service('assets.packages')])->set('twig.extension.routing', RoutingExtension::class)->args([service('router')])->set('twig.extension.yaml', YamlExtension::class)->set('twig.extension.debug.stopwatch', StopwatchExtension::class)->args([service('debug.stopwatch')->ignoreOnInvalid(), param('kernel.debug')])->set('twig.extension.expression', ExpressionExtension::class)->set('twig.extension.emoji', EmojiExtension::class)->set('twig.extension.htmlsanitizer', HtmlSanitizerExtension::class)->args([tagged_locator('html_sanitizer', 'sanitizer')])->set('twig.extension.httpkernel', HttpKernelExtension::class)->set('twig.runtime.httpkernel', HttpKernelRuntime::class)->args([service('fragment.handler'), service('fragment.uri_generator')->ignoreOnInvalid()])->set('twig.extension.httpfoundation', HttpFoundationExtension::class)->args([service('url_helper')])->set('twig.extension.debug', DebugExtension::class)->set('twig.extension.weblink', WebLinkExtension::class)->args([service('request_stack')])->set('twig.translation.extractor', TwigExtractor::class)->args([service('twig')])->tag('translation.extractor', ['alias' => 'twig'])->set('workflow.twig_extension', WorkflowExtension::class)->args([service('workflow.registry')])->set('twig.configurator.environment', EnvironmentConfigurator::class)->args([abstract_arg('date format, set in TwigExtension'), abstract_arg('interval format, set in TwigExtension'), abstract_arg('timezone, set in TwigExtension'), abstract_arg('decimals, set in TwigExtension'), abstract_arg('decimal point, set in TwigExtension'), abstract_arg('thousands separator, set in TwigExtension')])->set('twig.runtime_loader', ContainerRuntimeLoader::class)->args([abstract_arg('runtime locator')])->set('twig.error_renderer.html', TwigErrorRenderer::class)->decorate('error_renderer.html')->args([service('twig'), service('twig.error_renderer.html.inner'), inline_service('bool')->factory([TwigErrorRenderer::class, 'isDebug'])->args([service('request_stack'), param('kernel.debug')])])->set('twig.runtime.serializer', SerializerRuntime::class)->args([service('serializer')])->set('twig.extension.serializer', SerializerExtension::class)->set('controller.template_attribute_listener', TemplateAttributeListener::class)->args([service('twig')])->tag('kernel.event_subscriber');
};
