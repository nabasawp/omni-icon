<?php

declare (strict_types=1);
namespace OmniIcon\Integration\Elementor;

use OMNI_ICON;
use OmniIcon\Core\Discovery\Attributes\Hook;
use OmniIcon\Core\Discovery\Attributes\Service;
use OmniIcon\Services\ViteService;
/**
 * Service for registering and managing Elementor integration
 */
#[Service]
class ElementorService
{
    public function __construct(private readonly ViteService $viteService)
    {
    }
    /**
     * Register the Elementor widgets
     *
     * Registers custom Omni Icon widget for Elementor when Elementor is active.
     */
    #[Hook('elementor/widgets/register', priority: 10)]
    public function register_widgets($widgets_manager): void
    {
        // Check if Elementor is active
        if (!did_action('elementor/loaded')) {
            return;
        }
        // Require the widget file
        require_once __DIR__ . '/Widgets/IconWidget.php';
        // Register the widget
        $widgets_manager->register(new \OmniIcon\Integration\Elementor\Widgets\IconWidget());
    }
    /**
     * Enqueue editor assets for Elementor
     */
    #[Hook('elementor/editor/after_enqueue_scripts', priority: 10)]
    public function editor_assets(): void
    {
        // Enqueue omni-icon web component for the editor
        $this->viteService->enqueue_asset('resources/webcomponents/omni-icon.ts', ['handle' => OMNI_ICON::TEXT_DOMAIN . ':web-component:omni-icon', 'in-footer' => \true]);
        // Enqueue Gutenberg icon block styles (reuse for Elementor)
        $this->viteService->enqueue_asset('resources/integration/gutenberg/blocks/icon-block/editor.css', ['handle' => OMNI_ICON::TEXT_DOMAIN . ':gutenberg-icon-block-editor-styles']);
        // Enqueue Elementor editor integration script
        $this->viteService->enqueue_asset('resources/integration/elementor/editor.ts', ['handle' => OMNI_ICON::TEXT_DOMAIN . ':integration-elementor-editor', 'in_footer' => \true, 'dependencies' => ['wp-element', 'wp-components', 'wp-i18n', 'wp-data', 'react', 'react-dom']]);
    }
    /**
     * Enqueue frontend assets for rendering omni-icon on the frontend
     */
    #[Hook('elementor/frontend/after_enqueue_scripts', priority: 10)]
    public function frontend_assets(): void
    {
        // Enqueue omni-icon web component
        $this->viteService->enqueue_asset('resources/webcomponents/omni-icon.ts', ['handle' => OMNI_ICON::TEXT_DOMAIN . ':web-component:omni-icon', 'in-footer' => \true]);
    }
    /**
     * Register custom categories for Elementor
     */
    #[Hook('elementor/elements/categories_registered', priority: 10)]
    public function register_categories($elements_manager): void
    {
        $elements_manager->add_category('omni-icon', ['title' => esc_html__('Omni Icon', 'omni-icon'), 'icon' => 'fa fa-star']);
    }
}
