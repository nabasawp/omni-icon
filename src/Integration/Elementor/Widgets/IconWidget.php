<?php

declare (strict_types=1);
namespace OmniIcon\Integration\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use OMNI_ICON;
use OmniIcon\Plugin;
use OmniIcon\Services\IconService;
/**
 * Omni Icon widget for Elementor
 *
 * Has feature parity with the Gutenberg Icon Block and Bricks Icon Element, supporting:
 * - Icon selection via prefix:name format (e.g., mdi:home, fa:github, lucide:star) and Icon Picker Modal / UI
 * - Custom width and height dimensions
 * - Custom color styling
 *
 * The widget extends Elementor's base Widget_Base class and renders icons using the
 * omni-icon web component.
 *
 * @see https://developers.elementor.com/docs/widgets/
 */
class IconWidget extends Widget_Base
{
    /**
     * Get widget name.
     *
     * @return string Widget name.
     */
    public function get_name()
    {
        return 'omni-icon';
    }
    /**
     * Get widget title.
     *
     * @return string Widget title.
     */
    public function get_title()
    {
        return esc_html__('Omni Icon', 'omni-icon');
    }
    /**
     * Get widget icon.
     *
     * @return string Widget icon.
     */
    public function get_icon()
    {
        return 'eicon-star';
    }
    /**
     * Get widget categories.
     *
     * @return array Widget categories.
     */
    public function get_categories()
    {
        return ['omni-icon', 'basic'];
    }
    /**
     * Get widget keywords.
     *
     * @return array Widget keywords.
     */
    public function get_keywords()
    {
        return ['icon', 'iconify', 'svg', 'omni', 'symbol'];
    }
    /**
     * Register widget controls.
     */
    protected function register_controls()
    {
        // Content Section
        $this->start_controls_section('content_section', ['label' => esc_html__('Icon', 'omni-icon'), 'tab' => Controls_Manager::TAB_CONTENT]);
        // Icon Name control
        $this->add_control('icon_name', ['label' => esc_html__('Icon Name', 'omni-icon'), 'type' => Controls_Manager::TEXT, 'default' => 'mdi:home', 'placeholder' => 'mdi:home', 'description' => esc_html__('Format: prefix:name (e.g., mdi:home, fa:github, lucide:star)', 'omni-icon'), 'dynamic' => ['active' => \true]]);
        // Browse Icons Button
        $this->add_control('icon_picker_button', ['type' => Controls_Manager::RAW_HTML, 'raw' => sprintf('<button type="button" class="oiel-icon-picker-button elementor-button elementor-button-default" style="width: 100%%; padding: 10px; margin-top: 10px;">%s</button>', esc_html__('Browse Icons', 'omni-icon')), 'content_classes' => 'oiel-icon-picker-wrapper']);
        $this->end_controls_section();
        // Style Section
        $this->start_controls_section('style_section', ['label' => esc_html__('Style', 'omni-icon'), 'tab' => Controls_Manager::TAB_STYLE]);
        // Icon Color
        $this->add_control('icon_color', ['label' => esc_html__('Color', 'omni-icon'), 'type' => Controls_Manager::COLOR, 'default' => 'currentColor', 'selectors' => ['{{WRAPPER}} omni-icon' => 'color: {{VALUE}};']]);
        // Icon Width
        $this->add_responsive_control('icon_width', ['label' => esc_html__('Width', 'omni-icon'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', 'em', 'rem'], 'range' => ['px' => ['min' => 16, 'max' => 512, 'step' => 1], 'em' => ['min' => 1, 'max' => 32, 'step' => 0.1], 'rem' => ['min' => 1, 'max' => 32, 'step' => 0.1]], 'default' => ['unit' => 'px', 'size' => 24]]);
        // Icon Height
        $this->add_responsive_control('icon_height', ['label' => esc_html__('Height', 'omni-icon'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', 'em', 'rem'], 'range' => ['px' => ['min' => 16, 'max' => 512, 'step' => 1], 'em' => ['min' => 1, 'max' => 32, 'step' => 0.1], 'rem' => ['min' => 1, 'max' => 32, 'step' => 0.1]], 'default' => ['unit' => 'px', 'size' => 24]]);
        // Alignment
        $this->add_responsive_control('align', ['label' => esc_html__('Alignment', 'omni-icon'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'omni-icon'), 'icon' => 'eicon-text-align-left'], 'center' => ['title' => esc_html__('Center', 'omni-icon'), 'icon' => 'eicon-text-align-center'], 'right' => ['title' => esc_html__('Right', 'omni-icon'), 'icon' => 'eicon-text-align-right']], 'default' => 'left', 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};']]);
        $this->end_controls_section();
    }
    /**
     * Render widget output on the frontend.
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $icon_name = $settings['icon_name'] ?? '';
        // Show placeholder if no icon name is set
        if (empty($icon_name)) {
            echo '<div class="elementor-alert elementor-alert-info">';
            echo esc_html__('Please enter an icon name in the format: prefix:name', 'omni-icon');
            echo '</div>';
            return;
        }
        // Get icon attributes
        $attributes = [];
        $attributes['name'] = $icon_name;
        // Handle dimensions
        $width = $settings['icon_width']['size'] ?? 24;
        $width_unit = $settings['icon_width']['unit'] ?? 'px';
        $height = $settings['icon_height']['size'] ?? 24;
        $height_unit = $settings['icon_height']['unit'] ?? 'px';
        // If one dimension is set and the other isn't, use the set one for both
        if (!empty($width) && empty($height)) {
            $height = $width;
            $height_unit = $width_unit;
        } elseif (!empty($height) && empty($width)) {
            $width = $height;
            $width_unit = $height_unit;
        }
        if (!empty($width)) {
            $attributes['width'] = $width . $width_unit;
        }
        if (!empty($height)) {
            $attributes['height'] = $height . $height_unit;
        }
        // Add color if not default (color is handled via CSS selectors in the control)
        $color = $settings['icon_color'] ?? 'currentColor';
        if ($color !== 'currentColor' && !empty($color)) {
            $attributes['color'] = $color;
        }
        // Get the IconService to fetch SVG for SSR
        $container = Plugin::get_instance()->container();
        $iconService = $container->get(IconService::class);
        $svg = $iconService->get_icon($icon_name, $attributes);
        // Build attribute string for omni-icon element
        $attrString = '';
        foreach ($attributes as $key => $value) {
            if ($value !== \false && $value !== null) {
                $attrString .= sprintf(' %s="%s"', esc_attr($key), esc_attr($value));
            }
        }
        // Render omni-icon with SSR support
        if ($svg !== null) {
            echo sprintf('<omni-icon data-prerendered%s>%s</omni-icon>', $attrString, $svg);
        } else {
            echo sprintf('<omni-icon%s></omni-icon>', $attrString);
        }
    }
    /**
     * Render widget output in the editor (live preview).
     */
    protected function content_template()
    {
        ?>
        <#
        const iconName = settings.icon_name || '';
        
        if (!iconName) {
            #>
            <div class="elementor-alert elementor-alert-info">
                <?php 
        echo esc_html__('Please enter an icon name in the format: prefix:name', 'omni-icon');
        ?>
            </div>
            <#
            return;
        }

        // Build attributes
        let width = settings.icon_width?.size || 24;
        let widthUnit = settings.icon_width?.unit || 'px';
        let height = settings.icon_height?.size || 24;
        let heightUnit = settings.icon_height?.unit || 'px';

        // If one dimension is set and the other isn't, use the set one for both
        if (width && !height) {
            height = width;
            heightUnit = widthUnit;
        } else if (height && !width) {
            width = height;
            widthUnit = heightUnit;
        }

        const color = settings.icon_color || 'currentColor';
        
        // Build attribute string
        let attrs = `name="${iconName}"`;
        if (width) attrs += ` width="${width}${widthUnit}"`;
        if (height) attrs += ` height="${height}${heightUnit}"`;
        if (color && color !== 'currentColor') attrs += ` color="${color}"`;
        #>
        <omni-icon {{{ attrs }}}></omni-icon>
        <?php 
    }
}
