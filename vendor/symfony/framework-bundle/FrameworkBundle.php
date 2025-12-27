<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Bundle\FrameworkBundle;

use OmniIconDeps\Symfony\Bundle\FrameworkBundle\Console\Application;
use OmniIconDeps\Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\AddDebugLogProcessorPass;
use OmniIconDeps\Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\AssetsContextPass;
use OmniIconDeps\Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\ContainerBuilderDebugDumpPass;
use OmniIconDeps\Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\ErrorLoggerCompilerPass;
use OmniIconDeps\Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\PhpConfigReferenceDumpPass;
use OmniIconDeps\Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\ProfilerPass;
use OmniIconDeps\Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\RemoveUnusedSessionMarshallingHandlerPass;
use OmniIconDeps\Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\TestServiceContainerRealRefPass;
use OmniIconDeps\Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\TestServiceContainerWeakRefPass;
use OmniIconDeps\Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\TranslationLintCommandPass;
use OmniIconDeps\Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\TranslationUpdateCommandPass;
use OmniIconDeps\Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\UnusedTagsPass;
use OmniIconDeps\Symfony\Bundle\FrameworkBundle\DependencyInjection\VirtualRequestStackPass;
use OmniIconDeps\Symfony\Component\Cache\Adapter\ApcuAdapter;
use OmniIconDeps\Symfony\Component\Cache\Adapter\ArrayAdapter;
use OmniIconDeps\Symfony\Component\Cache\Adapter\ChainAdapter;
use OmniIconDeps\Symfony\Component\Cache\Adapter\PhpArrayAdapter;
use OmniIconDeps\Symfony\Component\Cache\Adapter\PhpFilesAdapter;
use OmniIconDeps\Symfony\Component\Cache\DependencyInjection\CacheCollectorPass;
use OmniIconDeps\Symfony\Component\Cache\DependencyInjection\CachePoolClearerPass;
use OmniIconDeps\Symfony\Component\Cache\DependencyInjection\CachePoolPass;
use OmniIconDeps\Symfony\Component\Cache\DependencyInjection\CachePoolPrunerPass;
use OmniIconDeps\Symfony\Component\Config\Resource\ClassExistenceResource;
use OmniIconDeps\Symfony\Component\Console\ConsoleEvents;
use OmniIconDeps\Symfony\Component\Console\DependencyInjection\AddConsoleCommandPass;
use OmniIconDeps\Symfony\Component\DependencyInjection\Compiler\PassConfig;
use OmniIconDeps\Symfony\Component\DependencyInjection\Compiler\RegisterReverseContainerPass;
use OmniIconDeps\Symfony\Component\DependencyInjection\ContainerBuilder;
use OmniIconDeps\Symfony\Component\Dotenv\Dotenv;
use OmniIconDeps\Symfony\Component\ErrorHandler\ErrorHandler;
use OmniIconDeps\Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use OmniIconDeps\Symfony\Component\Form\DependencyInjection\FormPass;
use OmniIconDeps\Symfony\Component\HttpClient\DependencyInjection\HttpClientPass;
use OmniIconDeps\Symfony\Component\HttpFoundation\BinaryFileResponse;
use OmniIconDeps\Symfony\Component\HttpFoundation\Request;
use OmniIconDeps\Symfony\Component\HttpKernel\Bundle\Bundle;
use OmniIconDeps\Symfony\Component\HttpKernel\DependencyInjection\ControllerArgumentValueResolverPass;
use OmniIconDeps\Symfony\Component\HttpKernel\DependencyInjection\FragmentRendererPass;
use OmniIconDeps\Symfony\Component\HttpKernel\DependencyInjection\LoggerPass;
use OmniIconDeps\Symfony\Component\HttpKernel\DependencyInjection\RegisterControllerArgumentLocatorsPass;
use OmniIconDeps\Symfony\Component\HttpKernel\DependencyInjection\RegisterLocaleAwareServicesPass;
use OmniIconDeps\Symfony\Component\HttpKernel\DependencyInjection\RemoveEmptyControllerArgumentLocatorsPass;
use OmniIconDeps\Symfony\Component\HttpKernel\DependencyInjection\ResettableServicePass;
use OmniIconDeps\Symfony\Component\HttpKernel\KernelEvents;
use OmniIconDeps\Symfony\Component\JsonStreamer\DependencyInjection\StreamablePass;
use OmniIconDeps\Symfony\Component\Messenger\DependencyInjection\MessengerPass;
use OmniIconDeps\Symfony\Component\Mime\DependencyInjection\AddMimeTypeGuesserPass;
use OmniIconDeps\Symfony\Component\PropertyInfo\DependencyInjection\PropertyInfoConstructorPass;
use OmniIconDeps\Symfony\Component\PropertyInfo\DependencyInjection\PropertyInfoPass;
use OmniIconDeps\Symfony\Component\Routing\DependencyInjection\AddExpressionLanguageProvidersPass;
use OmniIconDeps\Symfony\Component\Routing\DependencyInjection\RoutingControllerPass;
use OmniIconDeps\Symfony\Component\Routing\DependencyInjection\RoutingResolverPass;
use OmniIconDeps\Symfony\Component\Runtime\SymfonyRuntime;
use OmniIconDeps\Symfony\Component\Scheduler\DependencyInjection\AddScheduleMessengerPass;
use OmniIconDeps\Symfony\Component\Serializer\DependencyInjection\AttributeMetadataPass as SerializerAttributeMetadataPass;
use OmniIconDeps\Symfony\Component\Serializer\DependencyInjection\SerializerPass;
use OmniIconDeps\Symfony\Component\Translation\DependencyInjection\DataCollectorTranslatorPass;
use OmniIconDeps\Symfony\Component\Translation\DependencyInjection\LoggingTranslatorPass;
use OmniIconDeps\Symfony\Component\Translation\DependencyInjection\TranslationDumperPass;
use OmniIconDeps\Symfony\Component\Translation\DependencyInjection\TranslationExtractorPass;
use OmniIconDeps\Symfony\Component\Translation\DependencyInjection\TranslatorPass;
use OmniIconDeps\Symfony\Component\Translation\DependencyInjection\TranslatorPathsPass;
use OmniIconDeps\Symfony\Component\Validator\DependencyInjection\AddAutoMappingConfigurationPass;
use OmniIconDeps\Symfony\Component\Validator\DependencyInjection\AddConstraintValidatorsPass;
use OmniIconDeps\Symfony\Component\Validator\DependencyInjection\AddValidatorInitializersPass;
use OmniIconDeps\Symfony\Component\Validator\DependencyInjection\AttributeMetadataPass;
use OmniIconDeps\Symfony\Component\VarExporter\Internal\Hydrator;
use OmniIconDeps\Symfony\Component\VarExporter\Internal\Registry;
use OmniIconDeps\Symfony\Component\Workflow\DependencyInjection\WorkflowDebugPass;
use OmniIconDeps\Symfony\Component\Workflow\DependencyInjection\WorkflowGuardListenerPass;
use OmniIconDeps\Symfony\Component\Workflow\DependencyInjection\WorkflowValidatorPass;
// Help opcache.preload discover always-needed symbols
class_exists(ApcuAdapter::class);
class_exists(ArrayAdapter::class);
class_exists(ChainAdapter::class);
class_exists(PhpArrayAdapter::class);
class_exists(PhpFilesAdapter::class);
class_exists(Dotenv::class);
class_exists(ErrorHandler::class);
class_exists(Hydrator::class);
class_exists(Registry::class);
/**
 * Bundle.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class FrameworkBundle extends Bundle
{
    public function boot(): void
    {
        $_ENV['DOCTRINE_DEPRECATIONS'] = $_SERVER['DOCTRINE_DEPRECATIONS'] ??= 'trigger';
        if (class_exists(SymfonyRuntime::class)) {
            $handler = get_error_handler();
        } else {
            $handler = [ErrorHandler::register(null, \false)];
        }
        if (\is_array($handler) && $handler[0] instanceof ErrorHandler) {
            $this->container->get('debug.error_handler_configurator')->configure($handler[0]);
        }
        if ($this->container->getParameter('kernel.http_method_override')) {
            Request::enableHttpMethodParameterOverride();
        }
        if ($this->container->hasParameter('kernel.allowed_http_method_override')) {
            Request::setAllowedHttpMethodOverride($this->container->getParameter('kernel.allowed_http_method_override'));
        }
        if ($this->container->hasParameter('kernel.trust_x_sendfile_type_header') && $this->container->getParameter('kernel.trust_x_sendfile_type_header')) {
            BinaryFileResponse::trustXSendfileTypeHeader();
        }
    }
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $registerListenersPass = new RegisterListenersPass();
        $registerListenersPass->setHotPathEvents([KernelEvents::REQUEST, KernelEvents::CONTROLLER, KernelEvents::CONTROLLER_ARGUMENTS, KernelEvents::RESPONSE, KernelEvents::FINISH_REQUEST]);
        if (class_exists(ConsoleEvents::class)) {
            $registerListenersPass->setNoPreloadEvents([ConsoleEvents::COMMAND, ConsoleEvents::TERMINATE, ConsoleEvents::ERROR]);
        }
        $container->addCompilerPass(new AssetsContextPass());
        $container->addCompilerPass(new LoggerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -32);
        $container->addCompilerPass(new RegisterControllerArgumentLocatorsPass());
        $container->addCompilerPass(new RemoveEmptyControllerArgumentLocatorsPass(), PassConfig::TYPE_BEFORE_REMOVING);
        $container->addCompilerPass(new RoutingResolverPass());
        $this->addCompilerPassIfExists($container, RoutingControllerPass::class);
        $this->addCompilerPassIfExists($container, DataCollectorTranslatorPass::class);
        $container->addCompilerPass(new ProfilerPass());
        // must be registered before removing private services as some might be listeners/subscribers
        // but as late as possible to get resolved parameters
        $container->addCompilerPass($registerListenersPass, PassConfig::TYPE_BEFORE_REMOVING);
        $this->addCompilerPassIfExists($container, AddConstraintValidatorsPass::class);
        $this->addCompilerPassIfExists($container, AddValidatorInitializersPass::class);
        $this->addCompilerPassIfExists($container, AttributeMetadataPass::class);
        $this->addCompilerPassIfExists($container, AddConsoleCommandPass::class, PassConfig::TYPE_BEFORE_REMOVING);
        // must be registered before the AddConsoleCommandPass
        $container->addCompilerPass(new TranslationLintCommandPass(), PassConfig::TYPE_BEFORE_REMOVING, 10);
        // must be registered as late as possible to get access to all Twig paths registered in
        // twig.template_iterator definition
        $this->addCompilerPassIfExists($container, TranslatorPass::class, PassConfig::TYPE_BEFORE_OPTIMIZATION, -32);
        $this->addCompilerPassIfExists($container, TranslatorPathsPass::class, PassConfig::TYPE_AFTER_REMOVING);
        $this->addCompilerPassIfExists($container, LoggingTranslatorPass::class);
        $container->addCompilerPass(new AddExpressionLanguageProvidersPass());
        $this->addCompilerPassIfExists($container, TranslationExtractorPass::class);
        $this->addCompilerPassIfExists($container, TranslationDumperPass::class);
        $container->addCompilerPass(new FragmentRendererPass());
        $this->addCompilerPassIfExists($container, SerializerPass::class);
        $this->addCompilerPassIfExists($container, SerializerAttributeMetadataPass::class);
        $this->addCompilerPassIfExists($container, PropertyInfoPass::class);
        $this->addCompilerPassIfExists($container, PropertyInfoConstructorPass::class);
        $container->addCompilerPass(new ControllerArgumentValueResolverPass());
        $container->addCompilerPass(new CachePoolPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 32);
        $container->addCompilerPass(new CachePoolClearerPass(), PassConfig::TYPE_AFTER_REMOVING);
        $container->addCompilerPass(new CachePoolPrunerPass(), PassConfig::TYPE_AFTER_REMOVING);
        $this->addCompilerPassIfExists($container, FormPass::class);
        $this->addCompilerPassIfExists($container, WorkflowGuardListenerPass::class);
        $this->addCompilerPassIfExists($container, WorkflowValidatorPass::class);
        $container->addCompilerPass(new ResettableServicePass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -32);
        $container->addCompilerPass(new RegisterLocaleAwareServicesPass());
        $container->addCompilerPass(new TestServiceContainerWeakRefPass(), PassConfig::TYPE_BEFORE_REMOVING, -32);
        $container->addCompilerPass(new TestServiceContainerRealRefPass(), PassConfig::TYPE_AFTER_REMOVING);
        $this->addCompilerPassIfExists($container, AddMimeTypeGuesserPass::class);
        $this->addCompilerPassIfExists($container, AddScheduleMessengerPass::class);
        $this->addCompilerPassIfExists($container, MessengerPass::class);
        $this->addCompilerPassIfExists($container, HttpClientPass::class);
        $this->addCompilerPassIfExists($container, AddAutoMappingConfigurationPass::class);
        $container->addCompilerPass(new RegisterReverseContainerPass(\true));
        $container->addCompilerPass(new RegisterReverseContainerPass(\false), PassConfig::TYPE_AFTER_REMOVING);
        $container->addCompilerPass(new RemoveUnusedSessionMarshallingHandlerPass());
        // must be registered after MonologBundle's LoggerChannelPass
        $container->addCompilerPass(new ErrorLoggerCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -32);
        $container->addCompilerPass(new VirtualRequestStackPass());
        $container->addCompilerPass(new TranslationUpdateCommandPass(), PassConfig::TYPE_BEFORE_REMOVING);
        $this->addCompilerPassIfExists($container, StreamablePass::class);
        if ($container->getParameter('kernel.debug')) {
            if ($container->hasParameter('.kernel.config_dir') && $container->hasParameter('.kernel.bundles_definition')) {
                $container->addCompilerPass(new PhpConfigReferenceDumpPass($container->getParameter('.kernel.config_dir') . '/reference.php', $container->getParameter('.kernel.bundles_definition')));
            }
            $container->addCompilerPass(new AddDebugLogProcessorPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 2);
            $container->addCompilerPass(new UnusedTagsPass(), PassConfig::TYPE_AFTER_REMOVING);
            $container->addCompilerPass(new ContainerBuilderDebugDumpPass(), PassConfig::TYPE_BEFORE_REMOVING, -255);
            $container->addCompilerPass(new CacheCollectorPass(), PassConfig::TYPE_BEFORE_REMOVING);
            $this->addCompilerPassIfExists($container, WorkflowDebugPass::class);
        }
    }
    /**
     * @internal
     */
    public static function considerProfilerEnabled(): bool
    {
        return !($GLOBALS['app'] ?? null) instanceof Application || empty($_GET) && \in_array('--profile', $_SERVER['argv'] ?? [], \true);
    }
    private function addCompilerPassIfExists(ContainerBuilder $container, string $class, string $type = PassConfig::TYPE_BEFORE_OPTIMIZATION, int $priority = 0): void
    {
        $container->addResource(new ClassExistenceResource($class));
        if (class_exists($class)) {
            $container->addCompilerPass(new $class(), $type, $priority);
        }
    }
}
