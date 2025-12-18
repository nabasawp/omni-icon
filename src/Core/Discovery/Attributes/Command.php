<?php

declare(strict_types=1);

namespace OmniIcon\Core\Discovery\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final readonly class Command
{
    public function __construct(
        public ?string $name = null,
        public ?string $description = null,
        /** @var array<string> */
        public array $aliases = [],
        public ?string $synopsis = null,
        public ?string $when = 'after_wp_load'
    ) {
    }
}