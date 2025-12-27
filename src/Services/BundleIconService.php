<?php

declare (strict_types=1);
namespace OmniIcon\Services;

use OMNI_ICON;
use OmniIcon\Core\Discovery\Attributes\Service;
use OmniIconDeps\Symfony\Component\Cache\Adapter\FilesystemAdapter;
use OmniIconDeps\Symfony\Contracts\Cache\ItemInterface;
use OmniIconDeps\Symfony\UX\Icons\Registry\LocalSvgIconRegistry;
use OmniIconDeps\Symfony\UX\Icons\IconRegistryInterface;
/**
 * Service for managing plugin bundled SVG icons from flat directory structure.
 * 
 * All icons from the plugin's svg/ folder are available with the "omni:" prefix
 * 
 * Directory structure:
 * svg/
 * ├── livecanvas.svg        -> omni:livecanvas
 * ├── windpress.svg         -> omni:windpress
 * └── yabe-webfont.svg      -> omni:yabe-webfont
 */
#[Service]
class BundleIconService
{
    private readonly string $svg_dir;
    private readonly string $svg_url;
    private readonly IconRegistryInterface $registry;
    private readonly FilesystemAdapter $cache;
    public function __construct()
    {
        // Set up plugin SVG directory
        $this->svg_dir = OMNI_ICON::DIR . 'svg';
        $this->svg_url = OMNI_ICON::url() . 'svg';
        // Initialize cache
        $wp_upload_dir = wp_upload_dir();
        $cache_dir = $wp_upload_dir['basedir'] . OMNI_ICON::CACHE_DIR;
        wp_mkdir_p($cache_dir);
        $this->cache = new FilesystemAdapter('bundle_icons', 300, $cache_dir);
        // Create local icon registry for plugin bundled icons
        $this->registry = new LocalSvgIconRegistry($this->svg_dir, ['omni' => $this->svg_dir]);
    }
    public function get_registry(): IconRegistryInterface
    {
        return $this->registry;
    }
    /**
     * Get the SVG directory path
     */
    public function get_svg_dir(): string
    {
        return $this->svg_dir;
    }
    /**
     * Get the SVG directory URL
     */
    public function get_svg_url(): string
    {
        return $this->svg_url;
    }
    /**
     * Clear all caches
     */
    public function clear_cache(): void
    {
        $this->cache->clear();
    }
    /**
     * Get cache key based on directory modification time for auto-invalidation
     */
    private function get_cache_key(string $prefix): string
    {
        if (!is_dir($this->svg_dir)) {
            return $prefix . '_0';
        }
        $mtime = filemtime($this->svg_dir);
        return "{$prefix}_{$mtime}";
    }
    /**
     * Get all available bundle icons.
     * 
     * @return array{name: string, total: int, samples: array<int, string>}
     */
    public function get_icon_set(): array
    {
        $cache_key = $this->get_cache_key('bundle_icon_set');
        return $this->cache->get($cache_key, function (ItemInterface $item) {
            $item->expiresAfter(300);
            // 5 minutes
            $icons = $this->scan_directory();
            // $samples = array_slice($icons, 0, 6);
            return ['name' => 'Bundled Omni Icons', 'total' => count($icons), 'samples' => array_map(static fn($file) => pathinfo(basename($file), \PATHINFO_FILENAME), $icons)];
        });
    }
    /**
     * Get all bundle icons
     *
     * @return array<int, array{name: string, filename: string, icon_name: string, url: string, path: string}>
     */
    public function get_all_icons(): array
    {
        $cache_key = $this->get_cache_key('bundle_icons');
        return $this->cache->get($cache_key, function (ItemInterface $item) {
            $item->expiresAfter(300);
            // 5 minutes
            $files = $this->scan_directory();
            $icons = [];
            foreach ($files as $file) {
                $filename = basename($file);
                $name = pathinfo($filename, \PATHINFO_FILENAME);
                $icons[] = ['name' => $name, 'filename' => $filename, 'icon_name' => "omni:{$name}", 'url' => $this->svg_url . '/' . $filename, 'path' => $file];
            }
            return $icons;
        });
    }
    /**
     * Scan directory for SVG files
     *
     * @return array<int, string> Array of file paths
     */
    private function scan_directory(): array
    {
        if (!is_dir($this->svg_dir)) {
            return [];
        }
        $pattern = $this->svg_dir . '/*.svg';
        $files = glob($pattern, \GLOB_NOSORT);
        return $files !== \false ? $files : [];
    }
    /**
     * Get icon content by icon name
     *
     * @param string $icon_name Icon name in format "omni:name"
     * @return string|null The SVG content or null if not found
     */
    public function get_icon_content(string $icon_name): ?string
    {
        if (!str_contains($icon_name, ':')) {
            return null;
        }
        [$prefix, $name] = explode(':', $icon_name, 2);
        if ($prefix !== 'omni') {
            return null;
        }
        $filename = $name . '.svg';
        $file_path = $this->svg_dir . '/' . $filename;
        if (!file_exists($file_path)) {
            return null;
        }
        $content = file_get_contents($file_path);
        return $content === \false ? null : $content;
    }
    /**
     * Search icons by name query
     *
     * @param string $query Search query
     * @return array<int, array{name: string, prefix: string}>
     */
    public function search_icons(string $query): array
    {
        // If prefix is specified and it's not "omni", return empty
        if (str_contains($query, ':')) {
            [$prefix, $search_term] = explode(':', $query, 2);
            if ($prefix !== 'omni') {
                return [];
            }
        } else {
            $search_term = $query;
        }
        $all_icons = $this->get_all_icons();
        return array_values(array_map(static fn($icon) => ['name' => $icon['name'], 'prefix' => 'omni'], array_filter($all_icons, static fn($icon) => str_contains($icon['name'], $search_term))));
    }
}
