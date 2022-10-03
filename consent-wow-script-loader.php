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
define( 'WP_CONSENTWOW_SLUG', 'consentwow-settings.php' );
define( 'WP_CONSENTWOW_FORM_LIST_SLUG', 'consentwow-form-list.php' );
define( 'WP_CONSENTWOW_FORM_NEW_SLUG', 'consentwow-form-new.php' );
define( 'WP_CONSENTWOW_FORM_EDIT_SLUG', 'consentwow-form-edit.php' );
define( 'WP_CONSENTWOW_FILE', __FILE__ );

/**
 * Add admin menu.
 */
function consentwow_admin_menu() {
	consentwow_add_main_menu();
	consentwow_add_form_list_page();
	consentwow_add_form_new_page();
	consentwow_add_form_edit_page();
}

/**
 * Add main menu of Consent Wow settings.
 */
function consentwow_add_main_menu() {
	$page_title    = 'API Token Settings - Consent Wow';
	$menu_title    = 'Consent Wow';
	$submenu_title = 'Settings';
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
	$file_contents = file_get_contents( plugin_dir_path( __FILE__ ) . 'static/images/icon-consentwow.b64' );
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
	$menu_slug   = WP_CONSENTWOW_FORM_LIST_SLUG;
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
function consentwow_add_form_new_page() {
	$parent_slug = WP_CONSENTWOW_SLUG;
	$page_title  = 'Create a new Form - Consent Wow';
	$menu_title  = 'Add New';
	$capability  = 'manage_options';
	$menu_slug   = WP_CONSENTWOW_FORM_NEW_SLUG;
	$callback    = 'consentwow_admin_form_new_page';

	add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback );
}

/**
 * Display Add new Form page.
 */
function consentwow_admin_form_new_page() {
	require_once plugin_dir_path( __FILE__ ) . 'pages/form-new-page.php';
}


/**
 * Add submenu for Edit Form page.
 */
function consentwow_add_form_edit_page() {
	$parent_slug = null;
	$page_title  = 'Edit a Form - Consent Wow';
	$menu_title  = 'Edit Form';
	$capability  = 'manage_options';
	$menu_slug   = WP_CONSENTWOW_FORM_EDIT_SLUG;
	$callback    = 'consentwow_admin_form_edit_page';

	add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback );
}

/**
 * Display Edit a Form page.
 */
function consentwow_admin_form_edit_page() {
	require_once plugin_dir_path( __FILE__ ) . 'pages/form-edit-page.php';
}

/**
 * Link to the configuration page of the plugin & documentation
 *
 * @param string[] $actions An array of plugin action links.
 */
function consentwow_settings_action_links( $actions ) {
	array_unshift( $actions, sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=' . WP_CONSENTWOW_SLUG ), __( 'Settings', 'consentwow' ) ) );

	return $actions;
}

add_action( 'admin_menu', 'consentwow_admin_menu' );
add_filter( 'plugin_action_links_' . plugin_basename( WP_CONSENTWOW_FILE ), 'consentwow_settings_action_links' );
