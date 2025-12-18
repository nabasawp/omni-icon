<?php

declare(strict_types=1);

namespace OmniIcon\Core\Database\Migration\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class Migration
{
    public function __construct(
        public string $description,
        public int $priority = 0
    ) {
    }
}