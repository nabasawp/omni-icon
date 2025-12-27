<?php

declare (strict_types=1);
namespace OmniIcon\Core\Discovery\Attributes;

use Attribute;
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class Hook
{
    public function __construct(public string $name, public string $type = 'filter', public int $priority = 10, public int $accepted_args = 1)
    {
    }
}
