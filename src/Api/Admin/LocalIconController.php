<?php

declare (strict_types=1);
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
    public function __construct(private LocalIconService $localIconService)
    {
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
            return new WP_Error('no_file', __('No file uploaded', 'omni-icon'), ['status' => 400]);
        }
        // check mime type with LocalIconService
        $mime_type = $this->localIconService->detect_mime($_FILES['icon']['tmp_name']);
        if ($mime_type !== 'image/svg+xml') {
            return new WP_Error('invalid_file_type', __('Invalid file type. Only SVG files are allowed.', 'omni-icon'), ['status' => 400]);
        }
        // Get optional icon set (subdirectory)
        $icon_set = $request->get_param('icon_set');
        // Upload and sanitize the icon
        $result = $this->localIconService->upload_svg($_FILES['icon'], $icon_set);
        if (!$result['success']) {
            return new WP_Error('upload_failed', $result['message'], ['status' => 400]);
        }
        return new WP_REST_Response($result);
    }
    /**
     * Get all icon sets with metadata
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    #[Route('/sets', 'GET', permission_callback: 'manage_options')]
    public function get_icon_sets(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $sets = $this->localIconService->get_icon_sets();
        return new WP_REST_Response(['success' => \true, 'data' => $sets]);
    }
    /**
     * Get all icons from a specific set
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    #[Route('/sets/(?P<icon_set>[a-zA-Z0-9_-]+)/icons', 'GET', permission_callback: 'manage_options')]
    public function get_icons_by_set(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $icon_set = $request->get_param('icon_set');
        // "local" set is for root directory (pass null)
        $icon_set_param = $icon_set === 'local' ? null : $icon_set;
        $icons = $this->localIconService->get_icons_by_set($icon_set_param);
        return new WP_REST_Response(['success' => \true, 'data' => $icons]);
    }
    /**
     * Get all local icons
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    #[Route('/icons', 'GET', permission_callback: 'manage_options')]
    public function get_all_icons(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $icons = $this->localIconService->get_all_icons();
        return new WP_REST_Response(['success' => \true, 'data' => $icons]);
    }
    /**
     * Move icon to a different set
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    #[Route('/move', 'POST', permission_callback: 'manage_options')]
    public function move_icon(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $icon_name = $request->get_param('icon_name');
        $target_set = $request->get_param('target_set');
        if (empty($icon_name)) {
            return new WP_Error('missing_icon_name', __('Icon name is required', 'omni-icon'), ['status' => 400]);
        }
        $result = $this->localIconService->move_icon($icon_name, $target_set);
        if (!$result['success']) {
            return new WP_Error('move_failed', $result['message'], ['status' => 400]);
        }
        return new WP_REST_Response($result);
    }
    /**
     * Create a new icon set
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    #[Route('/sets/create', 'POST', permission_callback: 'manage_options')]
    public function create_set(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $set_name = $request->get_param('set_name');
        if (empty($set_name)) {
            return new WP_Error('missing_set_name', __('Set name is required', 'omni-icon'), ['status' => 400]);
        }
        $result = $this->localIconService->create_set($set_name);
        if (!$result['success']) {
            return new WP_Error('create_failed', $result['message'], ['status' => 400]);
        }
        return new WP_REST_Response($result);
    }
    /**
     * Rename an icon set
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    #[Route('/sets/(?P<set_name>[a-zA-Z0-9_-]+)/rename', 'POST', permission_callback: 'manage_options')]
    public function rename_set(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $old_name = $request->get_param('set_name');
        $new_name = $request->get_param('new_name');
        if (empty($new_name)) {
            return new WP_Error('missing_new_name', __('New name is required', 'omni-icon'), ['status' => 400]);
        }
        $result = $this->localIconService->rename_set($old_name, $new_name);
        if (!$result['success']) {
            return new WP_Error('rename_failed', $result['message'], ['status' => 400]);
        }
        return new WP_REST_Response($result);
    }
    /**
     * Clear icon cache
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    #[Route('/cache/clear', 'POST', permission_callback: 'manage_options')]
    public function clear_cache(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $this->localIconService->clear_cache();
        return new WP_REST_Response(['success' => \true, 'message' => __('Cache cleared successfully', 'omni-icon')]);
    }
    /**
     * Delete a custom icon
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    #[Route('/(?P<icon_name>[a-zA-Z0-9:_-]+)', 'DELETE', permission_callback: 'manage_options', args: ['icon_name' => ['validate_callback' => [self::class, 'validateIconName'], 'sanitize_callback' => [self::class, 'sanitizeIconName'], 'required' => \true]])]
    public function delete_custom_icon(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $icon_name = $request->get_param('icon_name');
        $result = $this->localIconService->delete_icon($icon_name);
        if (!$result['success']) {
            return new WP_Error('delete_failed', $result['message'], ['status' => 400]);
        }
        return new WP_REST_Response($result);
    }
    /**
     * Validate icon name format
     */
    public static function validateIconName($param, $request, $key): bool
    {
        // Icon name should be in format "prefix:name" or just "name"
        return is_string($param) && preg_match('/^[a-zA-Z0-9:_-]+$/', $param);
    }
    /**
     * Sanitize icon name
     */
    public static function sanitizeIconName($param, $request, $key): string
    {
        return sanitize_text_field($param);
    }
}
