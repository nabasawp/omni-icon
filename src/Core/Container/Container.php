<?php

declare (strict_types=1);
namespace OmniIcon\Core\Container;

use OMNI_ICON;
use OmniIcon\Core\Database\DatabaseInterface;
use OmniIcon\Core\Database\DatabaseService;
use OmniIconDeps\Psr\Container\ContainerInterface;
use RuntimeException;
use OmniIconDeps\Symfony\Component\DependencyInjection\ContainerBuilder;
use OmniIconDeps\Symfony\Component\DependencyInjection\Definition;
use OmniIconDeps\Symfony\Component\Messenger\Transport\Serialization\PhpSerializer;
use OmniIconDeps\Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use wpdb;
final class Container implements ContainerInterface
{
    private readonly ContainerBuilder $containerBuilder;
    private bool $compiled = \false;
    public function __construct()
    {
        $this->containerBuilder = new ContainerBuilder();
        $this->configure_core_services();
        $this->register_compiler_passes();
    }
    public function get(string $id): mixed
    {
        if (!$this->compiled) {
            $this->compile();
        }
        return $this->containerBuilder->get($id);
    }
    public function has(string $id): bool
    {
        if (!$this->compiled) {
            $this->compile();
        }
        return $this->containerBuilder->has($id);
    }
    public function register(string $id, string $class): Definition
    {
        if ($this->compiled) {
            throw new RuntimeException('Cannot register services after container compilation');
        }
        $definition = new Definition($class);
        $definition->setAutowired(\true);
        $definition->setAutoconfigured(\true);
        $definition->setPublic(\true);
        $this->containerBuilder->setDefinition($id, $definition);
        return $definition;
    }
    public function alias(string $alias, string $id): void
    {
        if ($this->compiled) {
            throw new RuntimeException('Cannot create aliases after container compilation');
        }
        $this->containerBuilder->setAlias($alias, $id);
    }
    public function parameter(string $name, mixed $value): void
    {
        if ($this->compiled) {
            throw new RuntimeException('Cannot set parameters after container compilation');
        }
        /** @phpstan-ignore-next-line */
        $this->containerBuilder->setParameter($name, $value);
    }
    public function compile(): void
    {
        if ($this->compiled) {
            return;
        }
        // Set synthetic services before compilation, e.g. WordPress globals
        $this->set_synthetic_services();
        $this->containerBuilder->compile();
        $this->compiled = \true;
    }
    public function is_compiled(): bool
    {
        return $this->compiled;
    }
    /**
     * @return array<string, array<mixed>>
     */
    public function findTaggedServiceIds(string $tag): array
    {
        if (!$this->compiled) {
            $this->compile();
        }
        return $this->containerBuilder->findTaggedServiceIds($tag);
    }
    private function configure_core_services(): void
    {
        $this->parameter('omni-icon.plugin_dir', OMNI_ICON::DIR);
        $this->parameter('omni-icon.plugin_url', OMNI_ICON::url());
        $this->parameter('omni-icon.version', OMNI_ICON::VERSION);
        // Register WordPress global $wpdb as a service using factory
        $wpdbDefinition = new Definition(wpdb::class);
        $wpdbDefinition->setFactory([self::class, 'createWpdbInstance']);
        $wpdbDefinition->setPublic(\true);
        $this->containerBuilder->setDefinition(wpdb::class, $wpdbDefinition);
        // Register DatabaseInterface alias to DatabaseService
        $databaseAlias = $this->containerBuilder->setAlias(DatabaseInterface::class, DatabaseService::class);
        $databaseAlias->setPublic(\true);
        $this->containerBuilder->setAlias(ContainerInterface::class, 'service_container');
        // Register Symfony Messenger Serializer
        $serializerDefinition = new Definition(PhpSerializer::class);
        $serializerDefinition->setAutowired(\true);
        $serializerDefinition->setPublic(\true);
        $this->containerBuilder->setDefinition(SerializerInterface::class, $serializerDefinition);
    }
    private function register_compiler_passes(): void
    {
    }
    private function set_synthetic_services(): void
    {
        // No longer needed - using factory pattern instead
    }
    /**
     * Factory method to provide WordPress $wpdb global as a service
     */
    public static function createWpdbInstance(): wpdb
    {
        global $wpdb;
        return $wpdb;
    }
}
