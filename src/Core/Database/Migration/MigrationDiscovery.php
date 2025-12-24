<?php

declare(strict_types=1);

namespace OmniIcon\Core\Database\Migration;

use OmniIcon\Core\Container\Container;
use OmniIcon\Core\Database\Migration\Attributes\Migration;
use OmniIcon\Core\Discovery\ClassReflector;
use OmniIcon\Core\Discovery\Discovery;
use OmniIcon\Core\Discovery\DiscoveryItems;
use OmniIcon\Core\Discovery\DiscoveryLocation;
use OmniIcon\Core\Discovery\IsDiscovery;
use OmniIcon\Core\Logger\LogComponent;
use OmniIcon\Core\Logger\LoggerService;

final class MigrationDiscovery implements Discovery
{
    use IsDiscovery;

    /** @var array<array{className: string, version: string, description: string, priority: int}> */
    private static array $migrations = [];

    public function __construct(
        private readonly Container $container,
        private readonly LoggerService $logger,
    ) {
        $this->discoveryItems = new DiscoveryItems();
    }

    /**
     * @param ClassReflector $classReflector
     */
    public function discover(DiscoveryLocation $discoveryLocation, ClassReflector $classReflector): void
    {
        $migrationAttribute = $classReflector->getAttribute(Migration::class);
        
        if ($migrationAttribute === null) {
            return;
        }
        
        $className = $classReflector->getName();
        
        // Check if class implements MigrationInterface
        if (! class_exists($className) || ! is_subclass_of($className, MigrationInterface::class)) {
            $this->logger->warning("Class has Migration attribute but doesn't implement MigrationInterface", [
                'component' => LogComponent::MIGRATION_DISCOVERY,
                'className' => $className,
            ]);
            return;
        }
        
        $timestamp = MigrationRegistry::extractTimestampFromClassName($className);
        
        $this->discoveryItems->add($discoveryLocation, [
            'className' => $className,
            'version' => $timestamp,
            'description' => $migrationAttribute->description,
            'priority' => $migrationAttribute->priority,
        ]);
    }

    public function apply(): void
    {
        // Clear static array to prevent duplicates
        self::$migrations = [];
        
        foreach ($this->discoveryItems as $discoveryItem) {
            /** @var array{className: string, version: string, description: string, priority: int} $item */
            self::$migrations[$discoveryItem['className']] = $discoveryItem; // Use className as key to prevent duplicates
        }
        
        // Sort by version (timestamp)
        usort(self::$migrations, function (array $a, array $b) {
            return strcmp($a['version'], $b['version']);
        });
    }
    
    /**
     * @return array<array{className: string, version: string, description: string, priority: int}>
     */
    public static function getDiscoveredMigrations(): array
    {
        return self::$migrations;
    }
}