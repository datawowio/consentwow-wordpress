<?php
/**
 * Consent Wow Script Loader
 *
 * @package consent-wow-script-loader
 *
 * @wordpress-plugin
 * Plugin Name: Consent Wow Script Loader
 * Plugin URI:  https://github.com/datawowio/consentwow-wordpress
 * Description: An easy way to manage consent on web pages.
 * Version:     1.0.0
 * Author:      Consent Wow
 * Author URI:  https://consentwow.com/
 * License:     GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

define( 'WP_CONSENTWOW_VERSION', '1.0.0' );
define( 'WP_CONSENTWOW_SLUG', 'consentwow-settings' );
define( 'WP_CONSENTWOW_FILE', __FILE__ );

/**
 * Add admin menu.
 */
function consentwow_admin_menu() {
	consentwow_add_main_menu();
	consentwow_add_form_list_page();
	consentwow_add_new_form_page();
}

/**
 * Add main menu of Consent Wow settings.
 */
function consentwow_add_main_menu() {
	$page_title    = 'API Token Settings - Consent Wow';
	$menu_title    = 'Consent Wow';
	$submenu_title = 'API Token Settings';
	$capability    = 'manage_options';
	$menu_slug     = WP_CONSENTWOW_SLUG;
	$callback      = 'consentwow_admin_api_token_settings_page';
	$icon_url      = consentwow_admin_menu_icon();

	add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $callback, $icon_url );
	add_submenu_page( $menu_slug, $page_title, $submenu_title, $capability, $menu_slug, $callback );
}

/**
 * Display API token settings page.
 */
function consentwow_admin_api_token_settings_page() {
	require_once plugin_dir_path( __FILE__ ) . 'pages/api-token-settings-page.php';
}

/**
 * Admin menu icon.
 */
function consentwow_admin_menu_icon() {
	$file_contents = file_get_contents( plugin_dir_path( __FILE__ ) . 'static/images/icon-cookiewow.b64' );
	return "data:image/svg+xml;base64,$file_contents";
}

/**
 * Add submenu for Form List page.
 */
function consentwow_add_form_list_page() {
	$parent_slug = WP_CONSENTWOW_SLUG;
	$page_title  = 'All Forms - Consent Wow';
	$menu_title  = 'All Forms';
	$capability  = 'manage_options';
	$menu_slug   = $parent_slug . '-form-list';
	$callback    = 'consentwow_admin_form_list_page';

	add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback );
}

/**
 * Display Form List page.
 */
function consentwow_admin_form_list_page() {
	require_once plugin_dir_path( __FILE__ ) . 'pages/form-list-page.php';
}

/**
 * Add submenu for Add new Form page.
 */
function consentwow_add_new_form_page() {
	$parent_slug = WP_CONSENTWOW_SLUG;
	$page_title  = 'Create a new Form - Consent Wow';
	$menu_title  = 'Add new form';
	$capability  = 'manage_options';
	$menu_slug   = $parent_slug . '-new-form';
	$callback    = 'consentwow_admin_new_form_page';

	add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback );
}

/**
 * Display Add new Form page.
 */
function consentwow_admin_new_form_page() {
	require_once plugin_dir_path( __FILE__ ) . 'pages/new-form-page.php';
}

add_action( 'admin_menu', 'consentwow_admin_menu' );
