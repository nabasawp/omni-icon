<?php

declare (strict_types=1);
namespace OmniIcon\Core\Discovery;

interface Discovery
{
    public function discover(\OmniIcon\Core\Discovery\DiscoveryLocation $discoveryLocation, \OmniIcon\Core\Discovery\ClassReflector $classReflector): void;
    public function apply(): void;
    public function getItems(): \OmniIcon\Core\Discovery\DiscoveryItems;
    public function setItems(\OmniIcon\Core\Discovery\DiscoveryItems $discoveryItems): void;
}
