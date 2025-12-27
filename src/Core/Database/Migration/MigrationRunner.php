<?php

declare (strict_types=1);
namespace OmniIcon\Core\Database\Migration;

use OmniIcon\Core\Database\DatabaseInterface;
use OmniIcon\Core\Discovery\Attributes\Service;
use RuntimeException;
#[Service(public: \true)]
final class MigrationRunner
{
    public function __construct(private readonly DatabaseInterface $database, private readonly \OmniIcon\Core\Database\Migration\MigrationRepository $migrationRepository, private readonly \OmniIcon\Core\Database\Migration\MigrationRegistry $migrationRegistry)
    {
    }
    /**
     * @param array<string> $versions
     */
    public function runMigrations(array $versions = [], bool $dryRun = \false): array
    {
        $executedVersions = $this->migrationRepository->getExecutedVersions();
        // Now returns FQCNs
        if ($versions === []) {
            $migrationsToRun = $this->migrationRegistry->getPendingMigrations($executedVersions);
        } else {
            $migrationsToRun = [];
            foreach ($versions as $version) {
                $migration = $this->migrationRegistry->getMigrationByVersion($version);
                if ($migration === null) {
                    throw new RuntimeException(sprintf('Migration %s not found', $version));
                }
                if (in_array($migration['className'], $executedVersions, \true)) {
                    throw new RuntimeException(sprintf('Migration %s already executed', $migration['className']));
                }
                $migrationsToRun[] = $migration;
            }
        }
        if (empty($migrationsToRun)) {
            return ['executed' => [], 'message' => 'No migrations to run'];
        }
        $executed = [];
        foreach ($migrationsToRun as $migrationToRun) {
            try {
                $result = $this->runSingleMigration($migrationToRun, $dryRun);
                $executed[] = $result;
            } catch (\Exception $e) {
                $error = ['version' => $migrationToRun['version'], 'className' => $migrationToRun['className'], 'description' => $migrationToRun['description'], 'status' => 'failed', 'error' => $e->getMessage()];
                return ['executed' => $executed, 'failed' => $error, 'message' => sprintf('Migration failed: %s - %s', $migrationToRun['className'], $e->getMessage())];
            }
        }
        return ['executed' => $executed, 'message' => count($executed) . ' migration(s) executed successfully'];
    }
    /**
     * @param int $steps Number of migrations to rollback
     * @param string|null $toVersion Rollback to specific version
     */
    public function rollbackMigrations(int $steps = 1, ?string $toVersion = null, bool $dryRun = \false): array
    {
        $executedVersions = $this->migrationRepository->getExecutedVersions();
        if (empty($executedVersions)) {
            return ['rolled_back' => [], 'message' => 'No migrations to rollback'];
        }
        if ($toVersion !== null) {
            $migrationsToRollback = $this->getMigrationsAfterVersion($executedVersions, $toVersion);
        } else {
            $migrationsToRollback = array_slice(array_reverse($executedVersions), 0, $steps);
        }
        if ($migrationsToRollback === []) {
            return ['rolled_back' => [], 'message' => 'No migrations to rollback'];
        }
        $rolledBack = [];
        foreach ($migrationsToRollback as $migrationToRollback) {
            try {
                $migrationData = $this->migrationRegistry->getMigrationByVersion($migrationToRollback);
                if ($migrationData === null) {
                    throw new RuntimeException(sprintf('Migration %s not found in registry', $migrationToRollback));
                }
                $result = $this->rollbackSingleMigration($migrationData, $dryRun);
                $rolledBack[] = $result;
            } catch (\Exception $e) {
                $error = ['version' => $migrationToRollback, 'status' => 'failed', 'error' => $e->getMessage()];
                return ['rolled_back' => $rolledBack, 'failed' => $error, 'message' => sprintf('Rollback failed: %s - %s', $migrationToRollback, $e->getMessage())];
            }
        }
        return ['rolled_back' => $rolledBack, 'message' => count($rolledBack) . ' migration(s) rolled back successfully'];
    }
    /**
     * @param array{className: string, version: string, description: string, priority: int} $migrationData
     */
    private function runSingleMigration(array $migrationData, bool $dryRun = \false): array
    {
        $migration = $this->migrationRegistry->createMigrationInstance($migrationData['className']);
        if ($dryRun) {
            return ['version' => $migrationData['version'], 'className' => $migrationData['className'], 'description' => $migrationData['description'], 'status' => 'dry-run', 'type' => 'up'];
        }
        // TODO: Re-enable transactions after fixing the issue
        $startTime = microtime(\true);
        $migration->up($this->database);
        $endTime = microtime(\true);
        $executionTimeMs = (int) round(($endTime - $startTime) * 1000);
        $this->migrationRepository->markAsExecuted($migrationData['className'], $executionTimeMs);
        return ['version' => $migrationData['version'], 'className' => $migrationData['className'], 'description' => $migrationData['description'], 'status' => 'executed', 'type' => 'up', 'execution_time' => $executionTimeMs];
    }
    /**
     * @param array{className: string, version: string, description: string, priority: int} $migrationData
     */
    private function rollbackSingleMigration(array $migrationData, bool $dryRun = \false): array
    {
        $migration = $this->migrationRegistry->createMigrationInstance($migrationData['className']);
        if ($dryRun) {
            return ['version' => $migrationData['version'], 'className' => $migrationData['className'], 'description' => $migrationData['description'], 'status' => 'dry-run', 'type' => 'down'];
        }
        // TODO: Re-enable transactions after fixing the issue
        $startTime = microtime(\true);
        $migration->down($this->database);
        $endTime = microtime(\true);
        $executionTimeMs = (int) round(($endTime - $startTime) * 1000);
        $this->migrationRepository->markAsReverted($migrationData['className']);
        return ['version' => $migrationData['version'], 'className' => $migrationData['className'], 'description' => $migrationData['description'], 'status' => 'rolled_back', 'type' => 'down', 'execution_time' => $executionTimeMs];
    }
    /**
     * @param array<string> $executedVersions
     * @return array<string>
     */
    private function getMigrationsAfterVersion(array $executedVersions, string $toVersion): array
    {
        $migrationsToRollback = [];
        // Sort executed versions in reverse order (newest first)
        rsort($executedVersions);
        foreach ($executedVersions as $executedVersion) {
            if ($executedVersion === $toVersion) {
                break;
            }
            $migrationsToRollback[] = $executedVersion;
        }
        return $migrationsToRollback;
    }
}
