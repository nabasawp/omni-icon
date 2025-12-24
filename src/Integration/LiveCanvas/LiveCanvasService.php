<?php

declare(strict_types=1);

namespace OmniIcon\Integration\LiveCanvas;

use OMNI_ICON;
use OmniIcon\Core\Discovery\Attributes\Hook;
use OmniIcon\Core\Discovery\Attributes\Service;
use OmniIcon\Services\ViteService;

/**
 * Service for registering and managing LiveCanvas integration
 */
#[Service]
class LiveCanvasService
{
    public function __construct(
        private readonly ViteService $viteService,
    ) {}

    /**
     * Register the LiveCanvas custom blocks
     *
     * Adds an Omni Icon block to LiveCanvas Builder when LiveCanvas is active.
     */
    #[Hook('lc_editor_header', priority: 10)]
    public function register_blocks(): void
    {
        // Check if LiveCanvas is active
        if (!defined('LC_MU_PLUGIN_NAME')) {
            return;
        }

        // Define the block configuration
        $block = [
            'category' => 'Basic',
            'block' => [
                'name' => 'Omni Icon',
                'icon_html' => '<i class="fa fa-star" aria-hidden="true"></i>',
                'template_html' => '<omni-icon name="omni:livecanvas" lc-helper="omni-icon" width="50"></omni-icon>'
            ],
            'options' => ['insertAt' => ['after' => 'Icon']]
        ];

        $blockCat = wp_json_encode($block["category"]);;
        $blockDef = wp_json_encode($block["block"]);;
        $blockOpts = wp_json_encode($block["options"]);

        echo <<<HTML
            <script id="omni-icon-lc-add-block">
            try {
                if (typeof addBlock === "function") {
                    addBlock(
                        {$blockCat},
                        {$blockDef},
                        {$blockOpts}
                    );
                }

                if (typeof addEditable === "function") {
                    addEditable('omni-icon', {
                        selector: 'omni-icon',
                    });
                }
            } catch (e) {
                console.error(e);
            }
            </script>
        HTML;
    }

    #[Hook('lc_define_custom_element')]
    public function define_custom_elements(array $elements): array
    {
        // // register omni-icon
        // $elements['omni-icon'] = [
        //     'callback' => function($attributes, $content) {
        //         return $content;
        //     }
        // ];

        return $elements;
    }

    /**
     * Enqueue editor assets for LiveCanvas
     */
    #[Hook('lc_editor_before_body_closing', priority: 1_000_000)]
    public function editor_assets(): void
    {
        // Enqueue omni-icon web component for the editor
        $this->viteService->enqueue_asset(
            'resources/webcomponents/omni-icon.ts',
            [
                'handle' => OMNI_ICON::TEXT_DOMAIN . ':web-component:omni-icon',
                'in-footer' => true,
            ]
        );

        // Enqueue Gutenberg icon block styles (reuse for LiveCanvas)
        $this->viteService->enqueue_asset('resources/integration/gutenberg/blocks/icon-block/editor.css', [
            'handle' => OMNI_ICON::TEXT_DOMAIN . ':gutenberg-icon-block-editor-styles',
        ]);

        // Enqueue LiveCanvas editor integration script
        $this->viteService->enqueue_asset('resources/integration/livecanvas/editor.ts', [
            'handle' => OMNI_ICON::TEXT_DOMAIN . ':integration-livecanvas-editor',
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

        // Output enqueued assets immediately
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

    /**
     * Enqueue frontend assets for rendering omni-icon on the frontend
     */
    #[Hook('wp_enqueue_scripts', priority: 10)]
    public function frontend_assets(): void
    {
        // Only enqueue if LiveCanvas content exists
        if (!defined('LC_MU_PLUGIN_NAME')) {
            return;
        }

        // Enqueue omni-icon web component
        $this->viteService->enqueue_asset(
            'resources/webcomponents/omni-icon.ts',
            [
                'handle' => OMNI_ICON::TEXT_DOMAIN . ':web-component:omni-icon',
                'in-footer' => true,
            ]
        );
    }

    /**
     * Render custom panel for Omni Icon in LiveCanvas editor
     */
    #[Hook('lc_render_additional_panels', priority: 10)]
    public function render_icon_panel(): void
    {
        ?>
        <!-- Omni Icon Panel -->
        <section item-type="omni-icon">
            <h1><?php echo esc_html__('Omni Icon', 'omni-icon'); ?></h1>
            
            <form class="add-common-form-elements">
                
                <!-- Icon Name Field -->
                <div>
                    <label><?php echo esc_html__('Icon Name', 'omni-icon'); ?></label>
                    <input 
                        type="text" 
                        attribute-name="name" 
                        value="" 
                        placeholder="mdi:home"
                        class="zoomable"
                    >
                    <small><?php echo esc_html__('Format: prefix:name (e.g., mdi:home, fa:github, lucide:star)', 'omni-icon'); ?></small>
                </div>

                <!-- Browse Icons Button -->
                <div style="margin: 10px 0;">
                    <button 
                        type="button" 
                        class="omni-icon-picker-button"
                        style="width: 100%; padding: 8px 16px; background: #0073aa; color: white; border: none; border-radius: 3px; cursor: pointer;"
                    >
                        <?php echo esc_html__('Browse Icons', 'omni-icon'); ?>
                    </button>
                </div>

                <!-- Size Section -->
                <div style="position:relative">
                    <label><?php echo esc_html__('Size', 'omni-icon'); ?></label>
                    <div class="size-feedback"></div>
                    <input value="24" type="range" name="size" min="1" max="1024" step="1">
                </div>

                <!-- Color Widget -->
                <div>
                    <div build_widget_for="color"></div>
                </div>

            </form>
        </section>

        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const PANEL_SELECTOR = 'section[item-type="omni-icon"]';
                const panel = document.querySelector(PANEL_SELECTOR);
                if (!panel) return;

                // WHEN PANEL BECOMES VISIBLE, INITIALIZE THE PANEL FIELDS
                onVisible(PANEL_SELECTOR, () => {
                    console.log('[Omni Icon] Panel opened');
                    
                    const selector = panel.getAttribute("selector");
                    const theSection = $(PANEL_SELECTOR);
                    if (!selector) return;

                    // Get the omni-icon element
                    const omniIconElement = doc.querySelector(selector);
                    if (!omniIconElement) return;

                    // Populate icon name
                    theSection.find('input[attribute-name="name"]').val(omniIconElement.getAttribute('name') || '');

                    // Populate size from width attribute
                    const iconWidth = omniIconElement.getAttribute('width');
                    if (iconWidth) {
                        const sizeValue = parseInt(iconWidth);
                        theSection.find('input[name="size"]').val(sizeValue);
                        theSection.find('.size-feedback').text(sizeValue + 'px');
                    }
                });
            });

            // Use jQuery event delegation like LiveCanvas's SVG icon panel
            $(document).ready(function ($) {
                // Handle icon name changes
                $('#sidepanel').on('input', 'section[item-type=omni-icon] input[attribute-name="name"]', function(event) {
                    event.preventDefault();
                    const theSection = $(this).closest("section[selector]");
                    const selector = theSection.attr("selector");
                    const omniIconElement = doc.querySelector(selector);
                    
                    if (omniIconElement) {
                        omniIconElement.setAttribute('name', $(this).val());
                        updatePreviewSectorial(selector);
                    }
                });

                // Handle size slider changes
                $('#sidepanel').on('input', 'section[item-type=omni-icon] input[name=size]', function(event) {
                    event.preventDefault();
                    const theSection = $(this).closest("section[selector]");
                    const selector = theSection.attr("selector");
                    const omniIconElement = doc.querySelector(selector);
                    
                    if (omniIconElement) {
                        const sizeValue = $(this).val();
                        
                        omniIconElement.setAttribute('width', sizeValue);
                        omniIconElement.setAttribute('height', sizeValue);
                        theSection.find('.size-feedback').text(sizeValue + 'px');
                        
                        // Update the common form field for width/height if it exists
                        theSection.find('.common-form-fields input[attribute-name=width]').val(sizeValue);
                        theSection.find('.common-form-fields input[attribute-name=height]').val(sizeValue);
                        
                        updatePreviewSectorial(selector);
                    }
                });

                // Handle icon picker button
                $('#sidepanel').on('click', 'section[item-type=omni-icon] .omni-icon-picker-button', function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    
                    const theSection = $(this).closest("section[selector]");
                    const selector = theSection.attr("selector");
                    const omniIconElement = doc.querySelector(selector);
                    
                    if (!omniIconElement) return;

                    const currentValue = omniIconElement.getAttribute('name') || '';
                    
                    if (window.omniIconPicker) {
                        window.omniIconPicker.open(currentValue, (iconName) => {
                            // Update the input field
                            theSection.find('input[attribute-name="name"]').val(iconName);
                            
                            // Update doc element
                            omniIconElement.setAttribute('name', iconName);
                            updatePreviewSectorial(selector);
                        });
                    }
                });
            }); // end doc ready
        </script>
        <?php
    }
}
