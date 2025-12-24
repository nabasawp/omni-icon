<?php

declare(strict_types=1);

namespace OmniIcon\Core\Logger;

/**
 * Enum for logger component names
 * 
 * Provides type-safe component identifiers for logging
 * 
 * @since 1.0.0
 */
enum LogComponent: string
{
    case ICON_SERVICE = 'IconService';
    case ICONIFY_SERVICE = 'IconifyService';
    case LOCAL_ICON_SERVICE = 'LocalIconService';
    case BUNDLE_ICON_SERVICE = 'BundleIconService';
    case DISCOVERY = 'Discovery';
    case COMMAND_DISCOVERY = 'CommandDiscovery';
    case MIGRATION = 'Migration';
    case MIGRATION_DISCOVERY = 'MigrationDiscovery';
    case CONTAINER = 'Container';
    case DATABASE = 'Database';
    case ASSETS = 'Assets';
    case VITE = 'Vite';
}
