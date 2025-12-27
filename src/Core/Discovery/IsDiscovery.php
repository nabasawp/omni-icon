<?php

declare (strict_types=1);
namespace OmniIcon\Core\Discovery;

trait IsDiscovery
{
    protected \OmniIcon\Core\Discovery\DiscoveryItems $discoveryItems;
    public function getItems(): \OmniIcon\Core\Discovery\DiscoveryItems
    {
        return $this->discoveryItems;
    }
    public function setItems(\OmniIcon\Core\Discovery\DiscoveryItems $discoveryItems): void
    {
        $this->discoveryItems = $discoveryItems;
    }
}
