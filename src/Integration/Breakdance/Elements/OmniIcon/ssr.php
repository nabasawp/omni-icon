<?php
/**
 * @var array $propertiesData
 */

defined('ABSPATH') || exit;

use OmniIcon\Plugin;
use OmniIcon\Services\IconService;

$content = $propertiesData['content'] ?? [];

$icon = $content['icon'] ?? null;
$icon_name = $icon['name'] ?? '';

// Build attributes array
$attributes = ['name' => $icon_name];

$width = $icon['width'] ?? null;
if ($width !== null && isset($width['style'])) {
    $attributes['width'] = $width['style'];
}

$height = $icon['height'] ?? null;
if ($height !== null && isset($height['style'])) {
    $attributes['height'] = $height['style'];
}

$color = $icon['color'] ?? null;
if ($color !== null) {
    $attributes['color'] = $color;
}

// Get the IconService to fetch SVG for SSR
$container = Plugin::get_instance()->container();
$iconService = $container->get(IconService::class);
$svg = $iconService->get_icon($icon_name, $attributes);

// Build the omni-icon element with SSR content
$attr_string = '';
foreach ($attributes as $key => $value) {
    if ($value !== false && $value !== null && $value !== '') {
        $attr_string .= sprintf(' %s="%s"', esc_attr($key), esc_attr($value));
    }
}

// Output the complete omni-icon element
if ($svg !== null) {
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Attributes are escaped above, SVG is sanitized by IconService
    echo sprintf('<omni-icon data-prerendered%s>%s</omni-icon>', $attr_string, $svg);
} else {
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Attributes are escaped above
    echo sprintf('<omni-icon%s></omni-icon>', $attr_string);
}