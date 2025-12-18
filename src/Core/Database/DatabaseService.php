<?php

declare(strict_types=1);

namespace OmniIcon\Core\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use OMNI_ICON;
use OmniIcon\Core\Discovery\Attributes\Service;
use PDO;
use RuntimeException;
use wpdb;

#[Service]
final class DatabaseService implements DatabaseInterface
{
    private Connection $connection;

    private string $tablePrefix;

    public function __construct(wpdb $wpdb)
    {
        $this->tablePrefix = $wpdb->prefix . OMNI_ICON::DB_TABLE_PREFIX;

        $dbHost = DB_HOST;
        $host = $dbHost;
        $port = 3306;

        if (str_contains($dbHost, ':')) {
            [$host, $portStr] = explode(':', $dbHost, 2);
            $port = (int) $portStr;
        }

        $this->connection = DriverManager::getConnection([
            'driver' => 'pdo_mysql',
            'host' => $host,
            'port' => $port,
            'dbname' => DB_NAME,
            'user' => DB_USER,
            'password' => DB_PASSWORD,
            'charset' => defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4',
            'collation' => defined('DB_COLLATE') ? DB_COLLATE : 'utf8mb4_unicode_ci',
            'driverOptions' => [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ]
        ]);
    }

    // Raw SQL operations
    public function query(string $sql, array $params = []): array
    {
        try {
            $result = $this->connection->executeQuery($sql, $params);
            return $result->fetchAllAssociative();
        } catch (Exception $exception) {
            throw new RuntimeException("Database query failed: " . $exception->getMessage(), 0, $exception);
        }
    }

    public function execute(string $sql, array $params = []): int
    {
        try {
            return $this->connection->executeStatement($sql, $params);
        } catch (Exception $exception) {
            throw new RuntimeException("Database execute failed: " . $exception->getMessage(), 0, $exception);
        }
    }

    public function fetchOne(string $sql, array $params = []): mixed
    {
        try {
            return $this->connection->fetchOne($sql, $params);
        } catch (Exception $exception) {
            throw new RuntimeException("Database fetchOne failed: " . $exception->getMessage(), 0, $exception);
        }
    }

    public function fetchColumn(string $sql, array $params = []): mixed
    {
        try {
            return $this->connection->fetchOne($sql, $params);
        } catch (Exception $exception) {
            throw new RuntimeException("Database fetchColumn failed: " . $exception->getMessage(), 0, $exception);
        }
    }

    // CRUD operations
    public function insert(string $table, array $data): int
    {
        try {
            $affectedRows = $this->connection->insert($this->getTableName($table), $data);
            return $affectedRows;
        } catch (Exception $exception) {
            throw new RuntimeException("Database insert failed: " . $exception->getMessage(), 0, $exception);
        }
    }

    public function update(string $table, array $data, array $where): int
    {
        try {
            return $this->connection->update($this->getTableName($table), $data, $where);
        } catch (Exception $exception) {
            throw new RuntimeException("Database update failed: " . $exception->getMessage(), 0, $exception);
        }
    }

    public function delete(string $table, array $where): int
    {
        try {
            return $this->connection->delete($this->getTableName($table), $where);
        } catch (Exception $exception) {
            throw new RuntimeException("Database delete failed: " . $exception->getMessage(), 0, $exception);
        }
    }

    public function find(string $table, array $where = []): array
    {
        $queryBuilder = $this->createQueryBuilder()
            ->select('*')
            ->from($this->getTableName($table));

        foreach ($where as $column => $value) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq($column, $queryBuilder->createNamedParameter($value)));
        }

        return $this->query($queryBuilder->getSQL(), $queryBuilder->getParameters());
    }

    public function findOne(string $table, array $where = []): ?array
    {
        $results = $this->find($table, $where);
        return $results === [] ? null : $results[0];
    }

    public function exists(string $table, array $where): bool
    {
        $queryBuilder = $this->createQueryBuilder()
            ->select('1')
            ->from($this->getTableName($table))
            ->setMaxResults(1);

        foreach ($where as $column => $value) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq($column, $queryBuilder->createNamedParameter($value)));
        }

        $result = $this->fetchOne($queryBuilder->getSQL(), $queryBuilder->getParameters());
        return $result !== false && $result !== null;
    }

    // Query Builder
    public function createQueryBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }

    public function select(string ...$columns): QueryBuilder
    {
        return $this->createQueryBuilder()->select(...$columns);
    }

    public function from(string $table): QueryBuilder
    {
        return $this->createQueryBuilder()->from($this->getTableName($table));
    }

    // WordPress-specific helpers
    public function getTableName(string $name): string
    {
        return $this->tablePrefix . $name;
    }

    public function prepare(string $sql, mixed ...$args): string
    {
        // Use Doctrine's parameter binding for safety
        // This is a simplified implementation - for complex cases, use parameterized queries
        $paramIndex = 0;
        return preg_replace_callback('/\?/', function() use ($args, &$paramIndex) {
            if (!isset($args[$paramIndex])) {
                throw new RuntimeException('Not enough parameters for prepared statement');
            }

            $value = $args[$paramIndex++];

            if (is_string($value)) {
                return $this->connection->quote($value);
            }

            if (is_int($value) || is_float($value)) {
                return (string) $value;
            }

            if (is_null($value)) {
                return 'NULL';
            }

            if (is_bool($value)) {
                return $value ? '1' : '0';
            }

            return $this->connection->quote((string) $value);
        }, $sql);
    }

    // Transaction support
    public function transaction(callable $callback): mixed
    {
        try {
            return $this->connection->transactional($callback);
        } catch (Exception $exception) {
            throw new RuntimeException("Database transaction failed: " . $exception->getMessage(), 0, $exception);
        }
    }

    public function beginTransaction(): void
    {
        try {
            $this->connection->beginTransaction();
        } catch (Exception $exception) {
            throw new RuntimeException("Failed to begin transaction: " . $exception->getMessage(), 0, $exception);
        }
    }

    public function commit(): void
    {
        try {
            $this->connection->commit();
        } catch (Exception $exception) {
            throw new RuntimeException("Failed to commit transaction: " . $exception->getMessage(), 0, $exception);
        }
    }

    public function rollback(): void
    {
        try {
            $this->connection->rollBack();
        } catch (Exception $exception) {
            throw new RuntimeException("Failed to rollback transaction: " . $exception->getMessage(), 0, $exception);
        }
    }

    // Utility methods
    public function getLastInsertId(): string|int
    {
        return $this->connection->lastInsertId();
    }

    public function tableExists(string $table): bool
    {
        try {
            $schemaManager = $this->connection->createSchemaManager();
            return $schemaManager->tablesExist([$this->getTableName($table)]);
        } catch (Exception $exception) {
            throw new RuntimeException("Failed to check table existence: " . $exception->getMessage(), 0, $exception);
        }
    }

    public function getCollation(): string
    {
        global $wpdb;
        return $wpdb->collate ?: 'utf8mb4_unicode_ci';
    }

    public function getCharset(): string
    {
        global $wpdb;
        return $wpdb->charset ?: 'utf8mb4';
    }

    // Internal helper to get the underlying Doctrine connection
    // Useful for advanced operations or migrations
    public function getConnection(): Connection
    {
        return $this->connection;
    }
}