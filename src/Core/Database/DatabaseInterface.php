<?php

declare (strict_types=1);
namespace OmniIcon\Core\Database;

use OmniIconDeps\Doctrine\DBAL\Connection;
use OmniIconDeps\Doctrine\DBAL\Query\QueryBuilder;
interface DatabaseInterface
{
    // Raw SQL operations
    public function query(string $sql, array $params = []): array;
    public function execute(string $sql, array $params = []): int;
    public function fetchOne(string $sql, array $params = []): mixed;
    public function fetchColumn(string $sql, array $params = []): mixed;
    // CRUD operations
    public function insert(string $table, array $data): int;
    public function update(string $table, array $data, array $where): int;
    public function delete(string $table, array $where): int;
    public function find(string $table, array $where = []): array;
    public function findOne(string $table, array $where = []): ?array;
    public function exists(string $table, array $where): bool;
    // Query Builder
    public function createQueryBuilder(): QueryBuilder;
    public function select(string ...$columns): QueryBuilder;
    public function from(string $table): QueryBuilder;
    // WordPress-specific helpers
    public function getTableName(string $name): string;
    public function prepare(string $sql, mixed ...$args): string;
    public function getCollation(): string;
    public function getCharset(): string;
    // Transaction support
    public function transaction(callable $callback): mixed;
    public function beginTransaction(): void;
    public function commit(): void;
    public function rollback(): void;
    // Utility methods
    public function getLastInsertId(): string|int;
    public function tableExists(string $table): bool;
    public function getConnection(): Connection;
}
