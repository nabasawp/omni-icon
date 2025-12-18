<?php

declare(strict_types=1);

namespace OmniIcon\Core\Discovery;

enum DiscoveryCacheStrategy: string
{
    case NONE = 'none';
    case PARTIAL = 'partial';
    case FULL = 'full';
}