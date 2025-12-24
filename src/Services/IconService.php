<?php

declare(strict_types=1);

namespace OmniIcon\Services;

use OmniIcon\Core\Discovery\Attributes\Service;
use OmniIcon\Core\Logger\LogComponent;
use OmniIcon\Core\Logger\LoggerService;
use Symfony\UX\Icons\Exception\IconNotFoundException;
use Symfony\UX\Icons\IconRegistryInterface;
use Symfony\UX\Icons\Registry\ChainIconRegistry;

/**
 * Icon service for rendering SVG icons from local uploads, plugin bundles, and Iconify API.
 *
 * @example
 * // Basic usage with local and remote icons
 * $iconService->get_icon('local:my-icon');
 * $iconService->get_icon('omni:livecanvas');
 * $iconService->get_icon('about-us:old-logo');
 * $iconService->get_icon('mdi:home');
 *
 * // With attributes
 * $iconService->get_icon('prefix:name', ['class' => 'icon-large', 'width' => '32', 'height' => '32']);
 */
#[Service]
class IconService
{
    private readonly IconRegistryInterface $registry;

    public function __construct(
        private readonly LocalIconService $localIconService,
        private readonly BundleIconService $bundleIconService,
        private readonly IconifyService $iconifyService,
        private readonly LoggerService $logger,
    ) {
        // Chain registries: local icons take precedence over bundle icons, then on-demand icons
        $this->registry = new ChainIconRegistry([
            $localIconService->get_registry(), // Check local uploaded icons first
            $bundleIconService->get_registry(), // Check plugin bundled icons second
            $iconifyService->get_registry(), // Fallback to on-demand Iconify icons
        ]);
    }

    /**
     * Get an icon from Iconify and return as HTML string.
     *
     * @param string $name Icon name in format "prefix:icon-name" (e.g., "mdi:home", "bi:github")
     * @param array<string, mixed> $attributes Optional HTML attributes to add to the SVG element
     * @return null|string the SVG HTML if exists, or null if couldn't found
     */
    public function get_icon(string $name, array $attributes = []): ?string
    {
        // Validate icon name format
        if (! str_contains($name, ':')) {
            return null;
        }

        try {
            // Fetch icon from registry (with caching)
            $icon = $this->registry->get($name);

            // Add custom attributes if provided
            if (! empty($attributes)) {
                $icon = $icon->withAttributes($attributes);
            }

            return $icon->toHtml();
        } catch (IconNotFoundException $e) {
            $this->logger->warning('Icon not found', [
                'component' => LogComponent::ICON_SERVICE,
                'icon' => $name,
            ]);
            return null;
        } catch (\Exception $e) {
            $this->logger->error('Failed to get icon', [
                'component' => LogComponent::ICON_SERVICE,
                'exception' => $e,
                'icon' => $name,
            ]);
            return null;
        }
    }

    /**
     * Get icon data as array (useful for API responses)
     *
     * @param string $name Icon name in format "prefix:icon-name"
     * @return array{svg: string|null, name: string, prefix: string}|null
     */
    public function get_icon_data(string $name): ?array
    {
        if (! str_contains($name, ':')) {
            return null;
        }

        [$prefix, $icon] = explode(':', $name, 2);

        $svg = $this->get_icon($name);
        
        if ($svg === null) {
            return null;
        }

        return [
            'svg' => $svg,
            'name' => $icon,
            'prefix' => $prefix,
        ];
    }

    /**
     * Get the icon registry instance
     *
     * @return IconRegistryInterface
     */
    public function get_registry(): IconRegistryInterface
    {
        return $this->registry;
    }

    /**
     * Get all available icon sets from local, bundle, and Iconify registries.
     *
     * @return array<string, array{name: string, total: int, samples: array<int, string>}>
     */
    public function get_icon_sets(): array
    {
        $localSets = $this->localIconService->get_icon_sets();
        $bundleSet = $this->bundleIconService->get_icon_set();
        $iconifySets = $this->iconifyService->get_icon_sets();

        return array_merge($localSets, ['omni' => $bundleSet], $iconifySets);
    }

    /**
     * Search for icons - fetches ALL results from local, bundle, and Iconify (limit=999)
     * Iconify results are cached for 5 minutes
     *
     * @param string $query Search query string
     * @return array{results: array<int, array{name: string, prefix: string}>, total: int}
     */
    public function search_icons(string $query): array
    {
        // Get local icons (already cached)
        $localResults = $this->localIconService->search_icons($query);
        
        // Get bundle icons (already cached)
        $bundleResults = $this->bundleIconService->search_icons($query);
        
        // Fetch all results from Iconify (limit=999, cached for 5 minutes)
        $iconifyResults = $this->iconifyService->search_icons($query);
        
        // Merge local icons first, then bundle icons, then Iconify results
        $allResults = array_merge($localResults, $bundleResults, $iconifyResults['results']);
        
        return [
            'results' => $allResults,
            'total' => count($allResults),
        ];
    }
}
