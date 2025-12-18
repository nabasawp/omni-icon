<?php

declare(strict_types=1);

namespace OmniIcon\Core\Database\Migration;

use OmniIcon\Core\Discovery\Attributes\Service;

#[Service(public: true)]
final class MigrationManager
{
    public function __construct(
        private readonly MigrationRegistry $migrationRegistry,
        private readonly MigrationRepository $migrationRepository,
        private readonly MigrationRunner $migrationRunner
    ) {
    }
    
    /**
     * @return array{
     *   total: int,
     *   executed: int,
     *   pending: int,
     *   migrations: array<array{version: string, fqcn: string, description: string, status: string, execution_time: ?int, executed_at: ?string}>
     * }
     */
    public function getStatus(): array
    {
        $allMigrations = $this->migrationRegistry->getAllMigrations();
        $executedVersions = $this->migrationRepository->getExecutedVersions();
        $executionHistory = $this->migrationRepository->getExecutionHistory();
        
        // Index execution history by FQCN for quick lookup
        $executionData = [];
        foreach ($executionHistory as $record) {
            $executionData[$record['version']] = $record;
        }
        
        $migrations = [];
        foreach ($allMigrations as $allMigration) {
            $isExecuted = in_array($allMigration['className'], $executedVersions, true);
            $executionRecord = $executionData[$allMigration['className']] ?? null;
            
            $migrations[] = [
                'version' => $allMigration['version'],
                'fqcn' => $allMigration['className'],
                'description' => $allMigration['description'],
                'status' => $isExecuted ? 'executed' : 'pending',
                'execution_time' => $executionRecord['execution_time'] ?? null,
                'executed_at' => $executionRecord['executed_at'] ?? null
            ];
        }
        
        return [
            'total' => count($allMigrations),
            'executed' => count($executedVersions),
            'pending' => count($allMigrations) - count($executedVersions),
            'migrations' => $migrations
        ];
    }
    
    /**
     * @return array<array{version: string, description: string, status: string}>
     */
    public function listMigrations(): array
    {
        return $this->getStatus()['migrations'];
    }
    
    /**
     * @return array<array{version: string, description: string}>
     */
    public function getPendingMigrations(): array
    {
        $executedVersions = $this->migrationRepository->getExecutedVersions();
        $pendingMigrations = $this->migrationRegistry->getPendingMigrations($executedVersions);
        
        return array_map(function (array $migration) {
            return [
                'version' => $migration['version'],
                'description' => $migration['description']
            ];
        }, $pendingMigrations);
    }
    
    /**
     * @return array<array{version: string, description: string, executed_at: string}>
     */
    public function getExecutionHistory(): array
    {
        return $this->migrationRepository->getExecutionHistory();
    }
    
    /**
     * @param array<string> $versions
     */
    public function run(array $versions = [], bool $dryRun = false): array
    {
        return $this->migrationRunner->runMigrations($versions, $dryRun);
    }
    
    public function rollback(int $steps = 1, ?string $toVersion = null, bool $dryRun = false): array
    {
        return $this->migrationRunner->rollbackMigrations($steps, $toVersion, $dryRun);
    }
    
    public function reset(bool $dryRun = false): array
    {
        $executedVersions = $this->migrationRepository->getExecutedVersions();
        
        if (empty($executedVersions)) {
            return ['rolled_back' => [], 'message' => 'No migrations to reset'];
        }
        
        return $this->rollback(count($executedVersions), null, $dryRun);
    }
    
    public function hasExecutedMigrations(): bool
    {
        return !empty($this->migrationRepository->getExecutedVersions());
    }
    
    public function hasPendingMigrations(): bool
    {
        $executedVersions = $this->migrationRepository->getExecutedVersions();
        $pendingMigrations = $this->migrationRegistry->getPendingMigrations($executedVersions);
        
        return !empty($pendingMigrations);
    }
    
    public function getLastExecutedVersion(): ?string
    {
        return $this->migrationRepository->getLastExecutedVersion();
    }
}