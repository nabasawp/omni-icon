<?php

declare (strict_types=1);
namespace OmniIcon\Core\Discovery\Attributes;

use Attribute;
#[Attribute(Attribute::TARGET_CLASS)]
final readonly class Service
{
    public function __construct(
        public ?string $id = null,
        public bool $singleton = \true,
        /** @var array<string> */
        public array $tags = [],
        public ?string $alias = null,
        public bool $public = \false
    )
    {
    }
}
