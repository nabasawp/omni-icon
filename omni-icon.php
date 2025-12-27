<?php

/**
 * @wordpress-plugin
 * Plugin Name:         Omni Icon
 * Plugin URI:          https://github.com/nabasa-dev/omni-icon
 * Description:         Add Iconify icon blocks to Gutenberg editor with symfony_ux integration. Browse and use thousands of icons powered by Iconify.
 * Text Domain:         omni-icon
 * Version:             0.0.1
 * Requires at least:   6.0
 * Requires PHP:        8.2
 * Author:              Omni Icon
 * Author URI:          https://github.com/nabasa-dev
 * License:             GPL-2.0-or-later
 *
 * @package             OmniIcon
 * @author              Joshua Gugun Siagian <suabahasa@gmail.com>
 */

declare(strict_types=1);

namespace OmniIcon;

defined('ABSPATH') || exit;

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    if (file_exists(__DIR__ . '/vendor/scoper-autoload.php')) {
        require_once __DIR__ . '/vendor/scoper-autoload.php';
    } else {
        require_once __DIR__ . '/vendor/autoload.php';
    }

    Plugin::get_instance()->boot();
}
