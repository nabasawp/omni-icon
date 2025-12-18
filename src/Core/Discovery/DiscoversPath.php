<?php

declare(strict_types=1);

namespace OmniIcon\Core\Discovery;

interface DiscoversPath
{
    public function discoverPath(DiscoveryLocation $discoveryLocation, string $path): void;
}
