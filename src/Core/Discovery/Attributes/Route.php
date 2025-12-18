<?php

declare(strict_types=1);

namespace OmniIcon\Core\Discovery\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class Route
{
    public function __construct(
        public string $path,
        public string|array $methods = 'GET',
        public ?string $name = null,
        public array $middleware = [],
        public ?string $permission_callback = null,
        public array $args = []
    ) {
    }
}