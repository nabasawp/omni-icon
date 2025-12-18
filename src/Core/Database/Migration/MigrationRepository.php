<?php

declare(strict_types=1);

namespace OmniIcon\Core\Database\Migration;

use OmniIcon\Core\Database\DatabaseInterface;
use OmniIcon\Core\Discovery\Attributes\Service;
use RuntimeException;

#[Service(public: true)]
final class MigrationRepository
{
    private const TABLE_NAME = 'migrations';
    
    public function __construct(
        private readonly DatabaseInterface $database
    ) {
        $this->ensureMigrationTableExists();
    }
    
    /**
     * @return array<string>
     */
    public function getExecutedVersions(): array
    {
        $results = $this->database->query(
            sprintf('SELECT version FROM %s ORDER BY version ASC', $this->database->getTableName(self::TABLE_NAME))
        );
        
        return array_column($results, 'version');
    }
    
    public function markAsExecuted(string $version, int $executionTimeMs): void
    {
        $this->database->insert(self::TABLE_NAME, [
            'version' => $version,
            'executed_at' => date('Y-m-d H:i:s'),
            'execution_time' => $executionTimeMs,
        ]);
    }
    
    public function markAsReverted(string $version): void
    {
        $this->database->delete(self::TABLE_NAME, [
            'version' => $version
        ]);
    }
    
    public function isExecuted(string $version): bool
    {
        return $this->database->exists(self::TABLE_NAME, [
            'version' => $version
        ]);
    }
    
    /**
     * @return array<array{version: string, executed_at: string, execution_time: int}>
     */
    public function getExecutionHistory(): array
    {
        return $this->database->query(
            sprintf('SELECT version, executed_at, execution_time FROM %s ORDER BY executed_at DESC', $this->database->getTableName(self::TABLE_NAME))
        );
    }
    
    public function getLastExecutedVersion(): ?string
    {
        $result = $this->database->fetchOne(
            sprintf('SELECT version FROM %s ORDER BY version DESC LIMIT 1', $this->database->getTableName(self::TABLE_NAME))
        );
        
        return $result ?: null;
    }
    
    /**
     * @param int $limit
     * @return array<string>
     */
    public function getLastExecutedVersions(int $limit): array
    {
        $results = $this->database->query(
            sprintf('SELECT version FROM %s ORDER BY version DESC LIMIT %d', $this->database->getTableName(self::TABLE_NAME), $limit)
        );
        
        return array_column($results, 'version');
    }
    
    private function ensureMigrationTableExists(): void
    {
        if ($this->database->tableExists(self::TABLE_NAME)) {
            return;
        }
        
        $tableName = $this->database->getTableName(self::TABLE_NAME);
        $sql = "CREATE TABLE {$tableName} (
            version VARCHAR(191) PRIMARY KEY,
            executed_at DATETIME NOT NULL,
            execution_time INT NOT NULL,
            INDEX idx_executed_at (executed_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        try {
            $this->database->execute($sql);
        } catch (\Exception $exception) {
            throw new RuntimeException(
                "Failed to create migrations table: " . $exception->getMessage(),
                0,
                $exception
            );
        }
    }
}