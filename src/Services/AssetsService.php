<?php

declare(strict_types=1);

namespace OmniIcon\Services;

use OMNI_ICON;
use OmniIcon\Core\Discovery\Attributes\Hook;
use OmniIcon\Core\Discovery\Attributes\Service;

/**
 * Service for managing plugin assets (scripts and styles)
 */
#[Service]
class AssetsService
{
    public function __construct(
        private readonly ViteService $viteService
    ) {
    }
    /**
     * Enqueue webcomponent on the frontend and admin pages
     */
    #[Hook('wp_enqueue_scripts', priority: 10)]
    #[Hook('admin_enqueue_scripts', priority: 10)]
    public function enqueue_frontend_scripts(): void
    {
        $this->viteService->enqueue_asset(
            'resources/webcomponents/omni-icon.ts',
            [
                'handle' => OMNI_ICON::TEXT_DOMAIN . ':web-component:omni-icon',
                'dependencies' => [
                    // OMNI_ICON::TEXT_DOMAIN . ':web-component-module:error-handler',
                ],
                'in-footer' => false,
            ]
        );
    }
}
