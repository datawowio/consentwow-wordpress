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

add_action( 'admin_menu', 'consentwow_admin_menu' );
