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
 * Initialize admin settings. Mostly just register settings section for
 * API Token and Form.
 */
function consentwow_admin_init() {
	consentwow_admin_register_api_token();
	add_option( 'consentwow_forms', array() );
	add_option( 'consentwow_forms_next_id', 1 );
}

/**
 * Register settings section for API Token.
 */
function consentwow_admin_register_api_token() {
	$option_group = 'consentwow_api_token_group';
	$option_name  = 'consentwow_api_token';
	$args         = array(
		'type' => 'string',
		'sanitize_callback' => 'consentwow_sanitize_api_token',
		'default' => null,
	);
	register_setting( $option_group, $option_name, $args );

	$id       = 'consentwow-api-token-settings';
	$title    = '';
	$callback = '';
	$page     = WP_CONSENTWOW_SLUG;
	add_settings_section( $id, $title, $callback, $page );

	$id       = 'consentwow_api_token';
	$title    = 'API Token';
	$callback = 'consentwow_api_token_settings_fields';
	$page     = WP_CONSENTWOW_SLUG;
	$section  = 'consentwow-api-token-settings';
	$args     = array(
		'label_for' => 'consentwow_api_token',
		'class' => 'consentwow-api-token-input',
	);
	add_settings_field( $id, $title, $callback, $page, $section, $args );
}

/**
 * Sanitize and validate API Token input before saving.
 *
 * @param String $api_token a string from input consentwow_api_token.
 *
 * @return String sanitized value
 */
function consentwow_sanitize_api_token( $api_token ) {
	return sanitize_text_field( $api_token );
}

/**
 * Display an input for API Token
 */
function consentwow_api_token_settings_fields() {
	$api_token = esc_attr( get_option( 'consentwow_api_token' ) );
	echo '<input type="text" id="consentwow_api_token" name="consentwow_api_token" class="regular-text" value="' . $api_token . '" />';
}

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
 *
 * @return String URL of icon
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

	$hook = add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback );

	add_action( "load-{$hook}", 'consentwow_form_list_handle_bulk_action' );
	add_action( "load-{$hook}", 'consentwow_form_list_add_screen_option' );

  function consentwow_form_list_add_screen_option() {
		$option = 'per_page';

		$args = array(
			'label'   => __( 'Number of Forms Per Page', 'consentwow' ),
			'default' => 20,
			'option'  => 'consentwow_forms_per_page',
		);

		add_screen_option( $option, $args );
  }

	function consentwow_form_list_handle_bulk_action() {
		if ( isset( $_GET['action'] ) && $_GET['action'] == 'delete_all' ) {
			$action_url = admin_url( 'admin.php?action=consentwow_form_bulk_action_delete_all' );
			$consentwow_forms = $_GET['consentwow_forms'];
			$redirect_url = add_query_arg( 'consentwow_forms', $consentwow_forms, $action_url );

			if ( wp_safe_redirect( $redirect_url ) ) {
				exit;
			}
		} else if ( isset( $_GET['action'] ) && $_GET['action'] == -1 ) {
			consentwow_form_add_settings_notice(
				'Invalid Action',
				$_REQUEST['_wp_http_referer'],
			);
		}
	}
}

/**
 * Display Form List page.
 */
function consentwow_admin_form_list_page() {
	require_once plugin_dir_path( __FILE__ ) . 'pages/form-list-page.php';
}

/**
 * Callback to set screen option
 */
function consentwow_form_list_set_screen_option($status, $option, $value) {
	return $value;
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
	require_once plugin_dir_path( __FILE__ ) . 'pages/form-page.php';
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
	require_once plugin_dir_path( __FILE__ ) . 'pages/form-page.php';
}

/**
 * Display a notification when an error occurred in updating settings.
 */
function consentwow_admin_notices() {
	if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] && empty( get_settings_errors( WP_CONSENTWOW_SLUG ) ) ) {
		add_settings_error(
			WP_CONSENTWOW_SLUG,
			'settings-notice',
			__( 'Settings Updated', 'consentwow' ),
			'success',
		);
	}

	settings_errors( WP_CONSENTWOW_SLUG );
}

/**
 * Link to the configuration page of the plugin & documentation
 *
 * @param string[] $actions An array of plugin action links.
 *
 * @return string[] $actions An array of plugin action links including a link to settings page.
 */
function consentwow_settings_action_links( $actions ) {
	array_unshift( $actions, sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=' . WP_CONSENTWOW_SLUG ), __( 'Settings', 'consentwow' ) ) );

	return $actions;
}

/**
 * Uninstall the plugin.
 */
function consentwow_uninstall() {
	delete_option( 'consentwow_api_token' );
	delete_option( 'consentwow_forms' );
	delete_option( 'consentwow_forms_next_id' );
}

/**
 * Set notice message from the form add/edit page.
 *
 * @param string $message      A message to be displayed on the alert bar.
 * @param string $redirect_url An url to redirect after setting the notice.
 * @param string $type         Type of the notice e.g. error, success.
 */
function consentwow_form_add_settings_notice( $message, $redirect_url, $type = 'error' ) {
	set_transient(
		'consentwow_form_notice',
		array( 'message' => __( $message, 'consentwow' ), 'type' => $type ),
	);

	if ( wp_safe_redirect( $redirect_url ) ) {
		exit;
	}
}

require_once plugin_dir_path( WP_CONSENTWOW_FILE ) . 'includes/class-consent-wow-form-list.php';

/**
 * Handler function for creating/updating a form.
 */
function consentwow_form_post_action() {
	$form_list = new Consent_Wow_Form_List();

	$redirect_url = admin_url( 'admin.php?page=' . WP_CONSENTWOW_FORM_NEW_SLUG );

	$fields = $_POST['consentwow_form'];
	if ( ! isset( $fields ) || empty( $fields ) ) {
		consentwow_form_add_settings_notice(
			'Invalid Form Data.',
			$redirect_url,
		);
	}

	if ( isset( $fields['id'] ) ) {
		$id = sanitize_text_field( $fields['id'] );
		$form = $form_list->find( $id );
		$redirect_url = admin_url( 'admin.php?page=' . WP_CONSENTWOW_FORM_NEW_SLUG . '&id=' . $id );
		$action = 'edit';

		if ( ! isset( $form ) ) {
			consentwow_form_add_settings_notice(
				'Invalid ID.',
				$redirect_url,
			);
		}
	} else {
		$form = array();
		$action = 'add';
	}

	$form['form_name'] = consentwow_sanitize_required_input( $fields['form_name'], 'Form Name is required.', $redirect_url );
	$form['form_id'] = consentwow_sanitize_required_input( $fields['form_id'], 'Form ID is required.', $redirect_url );
	$form['email'] = consentwow_sanitize_required_input( $fields['email'], 'Email is required.', $redirect_url );
	$form['first_name'] = consentwow_sanitize_nullable_input( $fields['first_name'] );
	$form['last_name'] = consentwow_sanitize_nullable_input( $fields['last_name'] );
	$form['phone_number'] = consentwow_sanitize_nullable_input( $fields['phone_number'] );
	$form['updated_date'] = time();

	if ( $action === 'add' ) {
		$form_list->add( $form );
	} else {
		$form_list->update( $id, $form );
	}

	$upcase_action = ucwords( $action );
	consentwow_form_add_settings_notice(
		"{$upcase_action} a form successfully",
		admin_url( 'admin.php?page=' . WP_CONSENTWOW_FORM_LIST_SLUG ),
		$type = 'success',
	);
}

/**
 * Sanitize required input value. Set error notice and redirect if the value is
 * empty.
 *
 * @param mixed  $value         Input value.
 * @param string $error_message An error message to be set in alert bar if an error occurs.
 * @param string $redirect_url  A URL to redirect if an error occurs.
 */
function consentwow_sanitize_required_input( $value, $error_message, $redirect_url ) {
	if ( isset( $value ) && ! empty( $value ) ) {
		return sanitize_text_field( $value );
	} else {
		consentwow_form_add_settings_notice(
			$error_message,
			$redirect_url,
		);
	}
}

/**
 * Sanitize nullable input value. Set null value if the value is empty.
 *
 * @param mixed $value Input value.
 */
function consentwow_sanitize_nullable_input( $value ) {
	if ( isset( $value ) && ! empty( $value ) ) {
		return sanitize_text_field( $value );
	} else {
		return null;
	}
}

/**
 * Handler function for deleting a form.
 */
function consentwow_form_delete_action() {
	$id = $_REQUEST['id'];

	if ( isset( $id ) && ! empty( $id ) ) {
		$form_list = new Consent_Wow_Form_List();
		$form_list->delete( $id );
	} else {
		consentwow_form_add_settings_notice(
			'Invalid ID',
			admin_url( 'admin.php?page=' . WP_CONSENTWOW_FORM_LIST_SLUG ),
		);
	}

	consentwow_form_add_settings_notice(
		'Delete a form successfully',
		admin_url( 'admin.php?page=' . WP_CONSENTWOW_FORM_LIST_SLUG ),
		$type = 'success',
	);
}

/**
 * Handler function for deleting many forms from bulk action.
 */
function consentwow_form_bulk_action_delete_all_action() {
	$form_ids = $_REQUEST['consentwow_forms'];
	$redirect_url = admin_url( 'admin.php?page=' . WP_CONSENTWOW_FORM_LIST_SLUG );

	if ( isset( $form_ids ) && empty( $form_ids ) ) {
		consentwow_form_add_settings_notice(
			'You must select at least 1 form to be deleted.',
			$redirect_url,
		);
	}

	$form_list = new Consent_Wow_Form_List();
	$form_list->delete_many( $form_ids );

	consentwow_form_add_settings_notice(
		'Delete form(s) successfully',
		$redirect_url,
		$type = 'success',
	);
}

add_action( 'admin_init', 'consentwow_admin_init' );
add_action( 'admin_menu', 'consentwow_admin_menu' );
add_action( 'admin_notices', 'consentwow_admin_notices' );
add_action( 'admin_action_consentwow_form_post', 'consentwow_form_post_action' );
add_action( 'admin_action_consentwow_form_delete', 'consentwow_form_delete_action' );
add_action( 'admin_action_consentwow_form_bulk_action_delete_all', 'consentwow_form_bulk_action_delete_all_action' );
add_filter( 'plugin_action_links_' . plugin_basename( WP_CONSENTWOW_FILE ), 'consentwow_settings_action_links' );
add_filter( 'set_screen_option_consentwow_forms_per_page', 'consentwow_form_list_set_screen_option', 10, 3 );
register_uninstall_hook( __FILE__, 'consentwow_uninstall' );
