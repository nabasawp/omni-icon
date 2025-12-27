<?php

declare (strict_types=1);
namespace OmniIcon\Integration\Bricks;

use Bricks\Elements;
use OMNI_ICON;
use OmniIcon\Core\Discovery\Attributes\Hook;
use OmniIcon\Core\Discovery\Attributes\Service;
use OmniIcon\Services\ViteService;
/**
 * Service for registering and managing Bricks integration
 */
#[Service]
class BricksService
{
    public function __construct(private readonly ViteService $viteService)
    {
    }
    /**
     * Register the Bricks elements
     *
     * Registers custom Omni Icon element for Bricks Builder when Bricks is active.
     */
    #[Hook('init', priority: 1000000)]
    public function register_elements(): void
    {
        // Check if Bricks is active
        if (!defined('BRICKS_VERSION')) {
            return;
        }
        // Register the Omni Icon element
        Elements::register_element(__DIR__ . '/Elements/IconElement.php', OMNI_ICON::TEXT_DOMAIN);
    }
    #[Hook('wp_enqueue_scripts', priority: 1000000)]
    public function editor_assets()
    {
        if (!function_exists('bricks_is_builder_main') || !\bricks_is_builder_main()) {
            return;
        }
        // Enqueue omni-icon web component for the editor
        $this->viteService->enqueue_asset('resources/webcomponents/omni-icon.ts', ['handle' => OMNI_ICON::TEXT_DOMAIN . ':web-component:omni-icon', 'in-footer' => \true]);
        // Enqueue Gutenberg icon block styles (reuse for Bricks)
        $this->viteService->enqueue_asset('resources/integration/gutenberg/blocks/icon-block/editor.css', ['handle' => OMNI_ICON::TEXT_DOMAIN . ':gutenberg-icon-block-editor-styles']);
        // Enqueue Bricks editor integration script
        $this->viteService->enqueue_asset('resources/integration/bricks/editor.ts', ['handle' => OMNI_ICON::TEXT_DOMAIN . ':integration-bricks-editor', 'in_footer' => \true, 'dependencies' => ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-block-editor', 'wp-hooks', 'wp-i18n', 'wp-plugins', 'wp-data', 'react', 'react-dom']]);
    }
}
