<?php

declare(strict_types=1);

namespace OmniIcon\Core\Discovery;

trait IsDiscovery
{
    protected DiscoveryItems $discoveryItems;

    public function getItems(): DiscoveryItems
    {
        return $this->discoveryItems;
    }

    public function setItems(DiscoveryItems $discoveryItems): void
    {
        $this->discoveryItems = $discoveryItems;
    }
}
