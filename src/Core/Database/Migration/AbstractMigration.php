<?php

declare(strict_types=1);

namespace OmniIcon\Core\Database\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use OmniIcon\Core\Database\DatabaseInterface;
use OmniIcon\Core\Database\Migration\Attributes\Migration;
use ReflectionClass;
use RuntimeException;

abstract class AbstractMigration implements MigrationInterface
{
    private ?Migration $migration = null;
    
    public function getDescription(): string
    {
        return $this->getMigrationAttribute()->description;
    }
    
    public function getPriority(): int
    {
        return $this->getMigrationAttribute()->priority;
    }
    
    /**
     * @param DatabaseInterface $database
     * @param string $name
     * @param callable(Table): void $schemaCallback
     */
    protected function createTable(DatabaseInterface $database, string $name, callable $schemaCallback): void
    {
        $connection = $database->getConnection();
        $tableName = $database->getTableName($name);
        
        // Create new schema and table
        $schema = new Schema();
        $table = $schema->createTable($tableName);
        
        // Allow migration to define table structure with full IDE support
        $schemaCallback($table);
        
        // Generate SQL queries for the current database platform
        $platform = $connection->getDatabasePlatform();
        $queries = $schema->toSql($platform);
        
        // Execute all generated queries
        foreach ($queries as $query) {
            // Apply WordPress charset and collation to CREATE TABLE statements
            if (str_starts_with(strtoupper(trim($query)), 'CREATE TABLE')) {
                $charset = $database->getCharset();
                $collation = $database->getCollation();
                $query .= sprintf(' ENGINE=InnoDB DEFAULT CHARSET=%s COLLATE=%s', $charset, $collation);
            }
            
            $connection->executeStatement($query);
        }
    }
    
    protected function dropTable(DatabaseInterface $database, string $name): void
    {
        $tableName = $database->getTableName($name);
        $database->execute('DROP TABLE IF EXISTS ' . $tableName);
    }
    
    protected function tableExists(DatabaseInterface $database, string $name): bool
    {
        return $database->tableExists($name);
    }
    
    private function getMigrationAttribute(): Migration
    {
        if ($this->migration === null) {
            $reflectionClass = new ReflectionClass($this);
            $attributes = $reflectionClass->getAttributes(Migration::class);
            
            if ($attributes === []) {
                throw new RuntimeException(
                    'Migration class ' . static::class . ' must have a Migration attribute'
                );
            }
            
            $this->migration = $attributes[0]->newInstance();
        }
        
        return $this->migration;
    }
}