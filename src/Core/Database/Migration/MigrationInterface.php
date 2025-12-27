<?php

declare (strict_types=1);
namespace OmniIcon\Core\Database\Migration;

use OmniIcon\Core\Database\DatabaseInterface;
interface MigrationInterface
{
    public function up(DatabaseInterface $database): void;
    public function down(DatabaseInterface $database): void;
    public function getDescription(): string;
}
