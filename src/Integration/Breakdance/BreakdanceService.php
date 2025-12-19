<?php

declare(strict_types=1);

namespace OmniIcon\Integration\Breakdance;

use OMNI_ICON;
use OmniIcon\Core\Discovery\Attributes\Hook;
use OmniIcon\Core\Discovery\Attributes\Service;
use OmniIcon\Services\ViteService;

use function Breakdance\ElementStudio\registerSaveLocation;
use function Breakdance\Util\getDirectoryPathRelativeToPluginFolder;

/**
 * Service for registering and managing Breakdance integration
 */
#[Service]
class BreakdanceService
{
    public function __construct(
        private readonly ViteService $viteService,
    ) {}

    /**
     * Enqueue editor assets for Breakdance
     * 
     * Uses the unofficial action hook to ensure assets are loaded in Breakdance builder
     * @see wp-content/plugins/breakdance/plugin/loader/loader.php
     */
    #[Hook('unofficial_i_am_kevin_geary_master_of_all_things_css_and_html', priority: 1_000_001)]
    public function editor_assets(): void
    {
        // Check if we're in Breakdance builder mode
        if (!isset($_GET['breakdance']) || $_GET['breakdance'] !== 'builder') {
            return;
        }

        // Check if Breakdance is active
        if (!defined('__BREAKDANCE_VERSION')) {
            return;
        }

        // Enqueue omni-icon web component for the editor
        $this->viteService->enqueue_asset(
            'resources/webcomponents/omni-icon.ts',
            [
                'handle' => OMNI_ICON::TEXT_DOMAIN . ':web-component:omni-icon',
                'in-footer' => true,
            ]
        );

        // Enqueue Gutenberg icon block styles (reuse for Breakdance)
        $this->viteService->enqueue_asset('resources/integration/gutenberg/blocks/icon-block/editor.css', [
            'handle' => OMNI_ICON::TEXT_DOMAIN . ':gutenberg-icon-block-editor-styles',
        ]);

        // Enqueue Breakdance editor integration script
        $handle = OMNI_ICON::TEXT_DOMAIN . ':integration-breakdance-editor';
        $this->viteService->enqueue_asset('resources/integration/breakdance/editor.ts', [
            'handle' => $handle,
            'in_footer' => true,
            'dependencies' => [
                'wp-element',
                'wp-components',
                'wp-i18n',
                'wp-data',
                'react',
                'react-dom',
            ],
        ]);

        // Manually output the enqueued scripts since Breakdance doesn't use wp_head
        $wp_scripts = wp_scripts();
        $queue = $wp_scripts->queue;

        foreach ($queue as $handle) {
            if (strpos($handle, OMNI_ICON::TEXT_DOMAIN . ':') !== 0) {
                continue;
            }

            $wp_scripts->do_items($handle);
        }

        // Styles
        $wp_styles = wp_styles();
        $queue = $wp_styles->queue;
        foreach ($queue as $handle) {
            if (strpos($handle, OMNI_ICON::TEXT_DOMAIN . ':') !== 0) {
                continue;
            }

            $wp_styles->do_items($handle);
        }
    }

    #[Hook('breakdance_loaded')]
    public function on_breakdance_loaded(): void
    {
        // Register omni-icon as a Breakdance icon source
        registerSaveLocation(
            getDirectoryPathRelativeToPluginFolder(__DIR__) . '/Elements',
            'OmniIcon\Integration\Breakdance\Elements',
            'element',
            'Omni Icon Elements',
            false
        );

        require_once __DIR__ . '/Elements/OmniIcon/element.php';
    }
}
