<?php

declare (strict_types=1);
namespace OmniIcon\Services;

use Exception;
use OMNI_ICON;
use OmniIcon\Core\Discovery\Attributes\Service;
use function OmniIconDeps\Kucrut\Vite\enqueue_asset as vite_enqueue_asset;
use function OmniIconDeps\Kucrut\Vite\register_asset as vite_register_asset;
use function OmniIconDeps\Kucrut\Vite\get_manifest as vite_get_manifest;
use function OmniIconDeps\Kucrut\Vite\generate_development_asset_src as vite_generate_development_asset_src;
/**
 * Utility class for managing Vite assets
 * 
 * TODO: Refactor and optimize methods
 */
#[Service]
class ViteService
{
    public const BUILD_DIR = 'dist';
    public const SOURCE_DIR = 'resources';
    public const MANIFEST_DIR = OMNI_ICON::DIR . self::BUILD_DIR;
    private $manifest;
    public function __construct()
    {
        $this->manifest = vite_get_manifest(self::MANIFEST_DIR);
    }
    public function enqueue_asset(string $asset_path, array $args = []): void
    {
        vite_enqueue_asset(self::MANIFEST_DIR, $asset_path, $args);
    }
    public function register_asset(string $asset_path, array $args = []): void
    {
        vite_register_asset(self::MANIFEST_DIR, $asset_path, $args);
    }
    /**
     * Get manifest data
     *
     * @return object Object containing manifest type and data.
     * @throws Exception When manifest file is not found or invalid.
     */
    public function get_manifest(): object
    {
        return $this->manifest;
    }
    /**
     * Generate development asset path
     *
     * @param string $asset_path Relative path to the asset.
     * @return string Full URL to the development asset.
     */
    public function generate_development_asset_path(string $asset_path): string
    {
        $url = vite_generate_development_asset_src($this->manifest, $asset_path);
        return str_replace($this->manifest->data->origin . '/', OMNI_ICON::DIR, $url);
    }
}
