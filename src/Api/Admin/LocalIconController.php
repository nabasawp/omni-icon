<?php

declare(strict_types=1);

namespace OmniIcon\Api\Admin;

use OmniIcon\Core\Discovery\Attributes\Controller;
use OmniIcon\Core\Discovery\Attributes\Route;
use OmniIcon\Services\LocalIconService;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

#[Controller(namespace: 'omni-icon/v1', prefix: 'admin/local-icon')]
final readonly class LocalIconController
{

    public function __construct(
        private LocalIconService $localIconService
    ) {
    }

    /**
     * Upload a custom SVG icon
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    #[Route('/upload', 'POST', permission_callback: 'manage_options')]
    public function upload_custom_icon(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        // Check if file was uploaded
        if (empty($_FILES['icon'])) {
            return new WP_Error(
                'no_file',
                __('No file uploaded', 'omni-icon'),
                ['status' => 400]
            );
        }

        // check mime type with LocalIconService
        $mime_type = $this->localIconService->detect_mime($_FILES['icon']['tmp_name']);
        if ($mime_type !== 'image/svg+xml') {
            return new WP_Error(
                'invalid_file_type',
                __('Invalid file type. Only SVG files are allowed.', 'omni-icon'),
                ['status' => 400]
            );
        }

        // Get optional icon set (subdirectory)
        $icon_set = $request->get_param('icon_set');

        // Upload and sanitize the icon
        $result = $this->localIconService->upload_svg($_FILES['icon'], $icon_set);

        if (!$result['success']) {
            return new WP_Error(
                'upload_failed',
                $result['message'],
                ['status' => 400]
            );
        }

        return new WP_REST_Response($result);
    }

    /**
     * Delete a custom icon
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    #[Route('/(?P<icon_name>[a-zA-Z0-9:_-]+)', 'DELETE', permission_callback: 'manage_options', args: [
        'icon_name' => [
            'validate_callback' => [self::class, 'validateIconName'],
            'sanitize_callback' => [self::class, 'sanitizeIconName'],
            'required' => true,
        ],
    ])]
    public function delete_custom_icon(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $icon_name = $request->get_param('icon_name');
        
        $result = $this->localIconService->delete_icon($icon_name);

        if (!$result['success']) {
            return new WP_Error(
                'delete_failed',
                $result['message'],
                ['status' => 400]
            );
        }

        return new WP_REST_Response($result);
    }
}