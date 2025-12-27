<?php

declare (strict_types=1);
namespace OmniIcon\Admin;

use OMNI_ICON;
use OmniIcon\Core\Discovery\Attributes\Hook;
use OmniIcon\Core\Discovery\Attributes\Service;
use OmniIcon\Services\ViteService;
/**
 * Service for registering and managing admin pages
 */
#[Service]
class AdminPage
{
    public function __construct(private readonly ViteService $viteService)
    {
    }
    /**
     * Register the admin menu page
     */
    #[Hook('admin_menu', priority: 10)]
    public function add_admin_menu(): void
    {
        $hook = add_menu_page(__('Omni Icon', 'omni-icon'), __('Omni Icon', 'omni-icon'), 'manage_options', OMNI_ICON::TEXT_DOMAIN, fn() => $this->render(), 'data:image/svg+xml;base64,' . base64_encode(file_get_contents(dirname(OMNI_ICON::FILE) . '/omni-icon.svg')), 100);
        add_action('load-' . $hook, fn() => $this->init_hooks());
    }
    /**
     * Get the URL to the admin page
     */
    public static function get_page_url(): string
    {
        return add_query_arg(['page' => OMNI_ICON::TEXT_DOMAIN], admin_url('admin.php'));
    }
    /**
     * Render the admin page
     */
    private function render(): void
    {
        do_action('a!omni-icon/admin:render.before');
        echo '<div id="omni-icon-app"></div>';
        do_action('a!omni-icon/admin:render.after');
    }
    /**
     * Initialize hooks for the admin page
     */
    private function init_hooks(): void
    {
        add_action('admin_enqueue_scripts', fn() => $this->enqueue_scripts(), 10);
    }
    /**
     * Enqueue scripts for the admin page
     */
    private function enqueue_scripts(): void
    {
        do_action('a!omni-icon/admin:enqueue_scripts.before');
        // Enqueue admin app
        $this->viteService->enqueue_asset('resources/admin/admin-app/index.jsx', ['handle' => 'omni-icon-admin', 'in-footer' => \true, 'dependencies' => ['react', 'react-dom', 'wp-element', 'wp-components', 'wp-i18n', 'wp-data']]);
        // Pass data to JavaScript
        wp_localize_script('omni-icon-admin', 'omniIconAdmin', ['apiUrl' => rest_url('omni-icon/v1/admin/local-icon'), 'nonce' => wp_create_nonce('wp_rest'), 'version' => OMNI_ICON::VERSION]);
        do_action('a!omni-icon/admin:enqueue_scripts.after');
    }
}
