<?php

declare(strict_types=1);

namespace OmniIcon\Core\Discovery\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class Controller
{
    public function __construct(
        public string $namespace = 'omni-icon/v1',
        public string $prefix = '',
        public array $middleware = []
    ) {
    }
}