<?php

declare (strict_types=1);
namespace OmniIcon\Core\Database\Migration;

use OmniIcon\Core\Discovery\Attributes\Service;
#[Service(public: \true)]
final class MigrationRegistry
{
    /** @var array<array{className: string, version: string, description: string, priority: int}> */
    private array $migrations = [];
    public function __construct()
    {
        $this->migrations = \OmniIcon\Core\Database\Migration\MigrationDiscovery::getDiscoveredMigrations();
    }
    /**
     * @return array<array{className: string, version: string, description: string, priority: int}>
     */
    public function getAllMigrations(): array
    {
        return $this->migrations;
    }
    /**
     * @return array<string>
     */
    public function getAllVersions(): array
    {
        return array_column($this->migrations, 'version');
    }
    public function getMigrationByVersion(string $version): ?array
    {
        // If version contains backslashes, it's a FQCN - extract timestamp
        if (str_contains($version, '\\')) {
            $version = self::extractTimestampFromClassName($version);
        }
        foreach ($this->migrations as $migration) {
            if ($migration['version'] === $version) {
                return $migration;
            }
        }
        return null;
    }
    public function createMigrationInstance(string $className): \OmniIcon\Core\Database\Migration\MigrationInterface
    {
        if (!class_exists($className)) {
            throw new \RuntimeException(sprintf('Migration class %s does not exist', $className));
        }
        $instance = new $className();
        if (!$instance instanceof \OmniIcon\Core\Database\Migration\MigrationInterface) {
            throw new \RuntimeException(sprintf('Migration class %s must implement MigrationInterface', $className));
        }
        return $instance;
    }
    /**
     * @param array<string> $executedVersions
     * @return array<array{className: string, version: string, description: string, priority: int}>
     */
    public function getPendingMigrations(array $executedVersions): array
    {
        return array_filter($this->migrations, function (array $migration) use ($executedVersions) {
            return !in_array($migration['className'], $executedVersions, \true);
        });
    }
    /**
     * @param array<string> $executedVersions
     * @return array<array{className: string, version: string, description: string, priority: int}>
     */
    public function getExecutedMigrations(array $executedVersions): array
    {
        return array_filter($this->migrations, function (array $migration) use ($executedVersions) {
            return in_array($migration['className'], $executedVersions, \true);
        });
    }
    public static function extractTimestampFromClassName(string $className): string
    {
        $shortClassName = substr($className, strrpos($className, '\\') + 1);
        return substr($shortClassName, 7);
        // Remove "Version" prefix
    }
}
