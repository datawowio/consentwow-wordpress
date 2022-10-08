<?php
/**
 * Consent Wow Consent Solution
 *
 * @package           consent-wow-consent-solution
 * @author            Consent Wow
 * @copyright         2022 Consent Wow
 * @license           GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Consent Wow Consent Solution
 * Plugin URI:        https://github.com/datawowio/consentwow-wordpress
 * Description:       PDPA-compliant consent management for your web forms.
 * Version:           1.0.0
 * Requires at least: 4.9.16
 * Requires PHP:      7.4.21
 * Author:            Consent Wow
 * Author URI:        https://consentwow.com/
 * License:           GNU General Public License v3 or later
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Network:           true
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
 * @param string $api_token a string from input consentwow_api_token.
 *
 * @return string sanitized value
 */
function consentwow_sanitize_api_token( $api_token ) {
	$original_value = get_option( 'consentwow_api_token' );

	$api_token = sanitize_text_field( $api_token );
	if ( ! isset( $api_token ) || empty( $api_token ) ) {
		add_settings_error(
			WP_CONSENTWOW_SLUG,
			'settings-notice',
			__( 'API Key is Required.', 'consent-wow-consent-solution' ),
		);

		return $original_value;
	}

	$results = consentwow_fetch_consent_purposes( $api_token );
	if ( is_wp_error( $results ) ) {
		add_settings_error(
			WP_CONSENTWOW_SLUG,
			'settings-notice',
			$results->get_error_message(),
		);

		return $original_value;
	}

	return $api_token;
}

/**
 * Fetch consent purposes from Consent Wow. Note that this function send a
 * request to external service.
 *
 * @param string $api_token An API Token is used in Authorization header of a request.
 *
 * @return mixed consent purpose list from response or an object of WP_Error.
 */
function consentwow_fetch_consent_purposes( $api_token ) {
	$args = array(
		'headers' => array( 'Content-Type' => 'application/json', 'Authorization' => $api_token ),
	);

	$response = wp_remote_get( 'https://api.consentwow.com/api/v1/consent_purposes', $args );
	$status = wp_remote_retrieve_response_code( $response );
	if ( is_array( $response ) && ! is_wp_error( $response ) && $status >= 200 && $status < 300 ) {
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
	} else {
		if ( $status == 401 ) {
			$message = 'API Key is Invalid.';
		} else {
			$message = 'Something went wrong, please try again later or contact our support for more information.';
		}

		return new WP_Error( $status, __( $message, 'consent-wow-consent-solution' ) );
	}

	if ( isset( $body['data'] ) ) {
		$consent_purposes = array_map(
			function ( $consent_purpose ) {
				return array(
					'name'       => $consent_purpose['attributes']['name'],
					'consent_id' => $consent_purpose['attributes']['consent_id'],
				);
			},
			$body['data'],
		);

		set_transient( 'consentwow_consent_purposes', $consent_purposes, 60 );
	}

	return $consent_purposes;
}

/**
 * Display an input for API Token
 */
function consentwow_api_token_settings_fields() {
	$api_token = esc_attr( get_option( 'consentwow_api_token' ) );
	echo '<input required type="text" id="consentwow_api_token" name="consentwow_api_token" class="regular-text" value="' . $api_token . '" />';
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
	$file_contents = file_get_contents( plugin_dir_path( __FILE__ ) . 'images/icon-consentwow.b64' );
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
	add_action( "load-{$hook}", 'consentwow_form_list_load_consent_purposes' );

	/**
	 * Add screen option for form list page.
	 */
  function consentwow_form_list_add_screen_option() {
		$option = 'per_page';

		$args = array(
			'label'   => __( 'Number of Forms Per Page', 'consent-wow-consent-solution' ),
			'default' => 20,
			'option'  => 'consentwow_forms_per_page',
		);

		add_screen_option( $option, $args );
  }

	/**
	 * Handler function for bulk action feature.
	 */
	function consentwow_form_list_handle_bulk_action() {
		if ( isset( $_REQUEST['_wp_http_referer'] ) ) {
			$referer = $_REQUEST['_wp_http_referer'];
		} else {
			$referer = admin_url( 'admin.php?page=' . WP_CONSENTWOW_FORM_LIST_SLUG );
		}

		if ( isset( $_GET['action'] ) && $_GET['action'] == 'delete_all' && isset( $_GET['consentwow_forms'] ) ) {
			$action_url = admin_url( 'admin.php?action=consentwow_form_bulk_action_delete_all' );
			$consentwow_forms = consentwow_sanitize_form_ids( $_GET['consentwow_forms'], $referer );
			$redirect_url = add_query_arg( 'consentwow_forms', $consentwow_forms, $action_url );

			if ( wp_safe_redirect( $redirect_url ) ) {
				exit;
			}
		} else if ( isset( $_GET['action'] ) && $_GET['action'] == -1 ) {
			consentwow_form_add_settings_notice(
				'Invalid Action',
				$referer,
			);
		}
	}

	/**
	 * Loading and caching consent purposes.
	 */
	function consentwow_form_list_load_consent_purposes() {
		$api_token = get_option( 'consentwow_api_token' );

		if ( empty( $api_token ) ) {
			consentwow_form_add_settings_notice( 'You must provide API Token in order to use this plugin' );
			return;
		}

		$consent_purposes = get_transient( 'consentwow_consent_purposes' );
		if ( ! is_array( $consent_purposes ) ) {
			$results = consentwow_fetch_consent_purposes( $api_token );
			if ( ! is_wp_error( $results ) ) {
				set_transient( 'consentwow_consent_purposes', $results );
			} else {
				consentwow_form_add_settings_notice( $results->get_error_message() );
			}
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
	$parent_slug = 'options.php';
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
			__( 'Settings Updated', 'consent-wow-consent-solution' ),
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
	array_unshift( $actions, sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=' . WP_CONSENTWOW_SLUG ), __( 'Settings', 'consent-wow-consent-solution' ) ) );

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
function consentwow_form_add_settings_notice( $message, $redirect_url = null, $type = 'error' ) {
	set_transient(
		'consentwow_form_notice',
		array( 'message' => __( $message, 'consent-wow-consent-solution' ), 'type' => $type ),
	);

	if ( ! empty( $redirect_url ) && wp_safe_redirect( $redirect_url ) ) {
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

	if ( ! isset( $_POST['consentwow_form'] ) || empty( $_POST['consentwow_form'] ) || ! is_array( $_POST['consentwow_form'] ) ) {
		consentwow_form_add_settings_notice( 'Invalid Form Data.', $redirect_url );
	}

	$form = consentwow_sanitize_form_post_data( $_POST['consentwow_form'], $redirect_url );
	$form['updated_date'] = time();

	if ( isset( $form['id'] ) ) {
		$action = 'edit';
		$form_list->update( $form['id'], $form );
	} else {
		$action = 'add';
		$form_list->add( $form );
	}

	$upcase_action = ucwords( $action );
	consentwow_form_add_settings_notice(
		"{$upcase_action} a form successfully",
		admin_url( 'admin.php?page=' . WP_CONSENTWOW_FORM_LIST_SLUG ),
		$type = 'success',
	);
}

/**
 * Sanitize form post data that it is an array and not empty.
 *
 * @param array  $fields       POST data from form-page
 * @param string $redirect_url A Redirect URL when an error occurs
 *
 * @return string Sanitized form data
 */
function consentwow_sanitize_form_post_data( $fields, $redirect_url ) {
	if ( ! is_array( $fields ) || empty( $fields ) ) {
		consentwow_form_add_settings_notice(
			'Invalid Form Data.',
			$redirect_url,
		);
	}

	if ( isset( $fields['id'] ) ) {
		$id = sanitize_text_field( $fields['id'] );
		$form_list = new Consent_Wow_Form_List();
		$form = $form_list->find( $id );
		$redirect_url = admin_url( 'admin.php?page=' . WP_CONSENTWOW_FORM_EDIT_SLUG . '&id=' . $id );

		if ( is_null( $form ) ) {
			consentwow_form_add_settings_notice(
				'Invalid ID.',
				$redirect_url,
			);
		}
	} else {
		$form = array();
	}

	if ( isset( $fields['form_name'] ) ) {
		$form['form_name'] = consentwow_sanitize_required_input( $fields['form_name'], 'Form Name is required.', $redirect_url );
	} else {
		consentwow_form_add_settings_notice( 'Form Name is required.', $redirect_url );
	}

	if ( isset( $fields['form_id'] ) ) {
		$form['form_id'] = consentwow_sanitize_required_input( $fields['form_id'], 'Form ID is required.', $redirect_url );
	} else {
		consentwow_form_add_settings_notice( 'Form ID is required.', $redirect_url );
	}

	if ( isset( $fields['email'] ) ) {
		$form['email'] = consentwow_sanitize_required_input( $fields['email'], 'Email is required.', $redirect_url );
	} else {
		consentwow_form_add_settings_notice( 'Email is required.', $redirect_url );
	}

	if ( isset( $fields['first_name'] ) ) {
		$form['first_name'] = consentwow_sanitize_nullable_input( $fields['first_name'] );
	}

	if ( isset( $fields['last_name'] ) ) {
		$form['last_name'] = consentwow_sanitize_nullable_input( $fields['last_name'] );
	}

	if ( isset( $fields['phone_number'] ) ) {
		$form['phone_number'] = consentwow_sanitize_nullable_input( $fields['phone_number'] );
	}

	if ( isset( $fields['consents'] ) ) {
		$form['consents'] = consentwow_sanitize_consents_input( $fields['consents'] );
	}

	return $form;
}

/**
 * Sanitize required input value. Set error notice and redirect if the value is
 * empty.
 *
 * @param mixed  $value         Input value.
 * @param string $error_message An error message to be set in alert bar if an error occurs.
 * @param string $redirect_url  A URL to redirect if an error occurs.
 *
 * @return mixed A sanitized value of required input.
 */
function consentwow_sanitize_required_input( $value, $error_message, $redirect_url ) {
	if ( ! empty( $value ) ) {
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
 *
 * @return mixed A sanitized value or a null.
 */
function consentwow_sanitize_nullable_input( $value ) {
	if ( ! empty( $value ) ) {
		return sanitize_text_field( $value );
	} else {
		return null;
	}
}

/**
 * Sanitize consents input value.
 *
 * @param array $array Input values from consent mapping inputs.
 *
 * @return array An array of sanitized consent mapping.
 */
function consentwow_sanitize_consents_input( $array ) {
	$consents = array();

	foreach ( $array as $consent ) {
		$sanitized_consent = consentwow_sanitize_consent_input( $consent );

		if ( ! empty( $sanitized_consent ) ) {
			$consents[] = $sanitized_consent;
		}
	}

	return $consents;
}

/**
 * Sanitize consent input value. Return null value if the value is invalid.
 *
 * @param array $array Input value from consent mapping input.
 *
 * @return array Sanitized consent mapping or a null.
 */
function consentwow_sanitize_consent_input( $consent ) {
	if ( ! isset( $consent['consent_id'] ) || ! isset( $consent['name'] ) ) {
		return null;
	}

	$consent_id = sanitize_text_field( $consent['consent_id'] );
	$name       = sanitize_text_field( $consent['name'] );

	if ( empty( $consent_id ) || empty( $name ) ) {
		return null;
	}

	return array(
		'consent_id' => $consent_id,
		'name'       => $name,
	);
}

/**
 * Handler function for deleting a form.
 */
function consentwow_form_delete_action() {
	if ( isset( $_REQUEST['id'] ) && ! empty( $_REQUEST['id'] ) ) {
		$id = sanitize_text_field( $_REQUEST['id'] );
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
	$redirect_url = admin_url( 'admin.php?page=' . WP_CONSENTWOW_FORM_LIST_SLUG );

	if ( ! isset( $_REQUEST['consentwow_forms'] ) || empty( $_REQUEST['consentwow_forms'] ) ) {
		consentwow_form_add_settings_notice(
			'You must select at least 1 form to be deleted.',
			$redirect_url,
		);
	} else {
		$sanitized_form_ids = consentwow_sanitize_form_ids(
			$_REQUEST['consentwow_forms'],
			$redirect_url,
		);

		$form_list = new Consent_Wow_Form_List();
		$form_list->delete_many( $sanitized_form_ids );

		consentwow_form_add_settings_notice(
			'Delete form(s) successfully',
			$redirect_url,
			$type = 'success',
		);
	}
}

/**
 * Sanitize form id list from query string. If param is not an array, set error
 * notice and redirect to given url.
 *
 * @param array  $form_ids     Form id list from query string
 * @param string $redirect_url A Redirect URL when an error occurs
 */
function consentwow_sanitize_form_ids( $form_ids, $redirect_url ) {
	$sanitized_form_ids = array();

	if ( ! is_array( $form_ids ) ) {
		consentwow_form_add_settings_notice(
			'Your input data is invalid',
			$redirect_url,
		);
	}

	foreach ( $form_ids as $form_id ) {
		$sanitized_form_id = sanitize_text_field( $form_id );

		if ( ! empty( $sanitized_form_id ) ) {
			array_push( $sanitized_form_ids, $sanitized_form_id );
		}
	}

	return $sanitized_form_ids;
}

/**
 * Enqueue scripts to Wordpress.
 */
function consentwow_enqueue_scripts() {
	$api_token = get_option( 'consentwow_api_token' );
	$forms = get_option( 'consentwow_forms' );

	if ( ! isset( $api_token ) || empty( $api_token ) || ! isset( $forms ) || empty( $forms ) ) {
		return;
	}

	$script_name  = 'consentwow_script';
	$src          = 'https://cdn.consentwow.com/script.min.js';
	$dependencies = array( 'jquery' );
	$version      = null;
	$in_footer    = false;
	wp_enqueue_script( $script_name, $src, $dependencies, $version, $in_footer );

	$js_forms = json_encode( $forms );
	$data = "
	jQuery(document).ready(function ($) {
		if (window.cswSDK) {
			var forms = {$js_forms};
			var wpcf7Elms = document.querySelectorAll('.wpcf7');
			$.each(wpcf7Elms, function (index, wpcf7Elm) {
				wpcf7Elm.addEventListener(
					'wpcf7submit',
					function (event) {
						var result = $.grep(
							forms,
							function(e) {
								return e.form_id == event.detail.contactFormId;
							}
						);

						if (result.length > 0) {
							var form = result[0];
							var consents = $.map(form.consents || [], function ( value ) {
								return ({
									name: value.name,
									consent_purpose_id: value.consent_id,
								});
							});

							window.cswSDK.submitForm(event, {
								first_name: form.first_name,
								last_name: form.last_name,
								email: form.email,
								phone_number: form.phone_number,
								consents: consents,
							});
						}
					},
					false
				);
			});
		}
	});";

	wp_add_inline_script( $script_name, $data );
}

/**
 * To replace the default ID that generated by WordPress when enqueued script
 * and to add data-cswid.
 *
 * @param string $tag The <script> tag for the enqueued script.
 * @param string $script_name The script's name that registered handle.
 * @param string $src The script's source URL..
 * @return string The $tag that has been modified.
 */
function consentwow_script_loader_tag( $tag, $script_name, $src ) {
	if ( 'consentwow_script' === $script_name ) {
		$api_token = esc_attr( get_option( 'consentwow_api_token' ) );

		return str_replace(
			"src='{$src}'",
			"id='consentWow' type='text/javascript' src='{$src}' data-cswid='{$api_token}'",
			$tag,
		);
	}

	return $tag;
}

/**
 * Load javascript into adding/editing form page.
 */
function consentwow_admin_form_page_scripts( $hook ) {
	$pages = array(
		'consent-wow_page_consentwow-form-new',
		'admin_page_consentwow-form-edit',
	);

	if ( ! in_array( $hook, $pages ) ) {
		return;
	}

	$script_name = 'consentwow_admin_form_page_script';
	$src         = plugin_dir_url( __FILE__ ) . 'js/add-consent.js';
	$deps        = array();
	$version     = null;
	$in_footer   = false;
	wp_enqueue_script( $script_name, $src, $deps, $version, $in_footer );
}

add_action( 'admin_init', 'consentwow_admin_init' );
add_action( 'admin_menu', 'consentwow_admin_menu' );
add_action( 'admin_notices', 'consentwow_admin_notices' );
add_action( 'admin_action_consentwow_form_post', 'consentwow_form_post_action' );
add_action( 'admin_action_consentwow_form_delete', 'consentwow_form_delete_action' );
add_action( 'admin_action_consentwow_form_bulk_action_delete_all', 'consentwow_form_bulk_action_delete_all_action' );
add_action( 'wp_enqueue_scripts', 'consentwow_enqueue_scripts' );
add_action( 'admin_enqueue_scripts', 'consentwow_admin_form_page_scripts' );
add_filter( 'plugin_action_links_' . plugin_basename( WP_CONSENTWOW_FILE ), 'consentwow_settings_action_links' );
add_filter( 'set_screen_option_consentwow_forms_per_page', 'consentwow_form_list_set_screen_option', 10, 3 );
add_filter( 'script_loader_tag', 'consentwow_script_loader_tag', $priority = 1, $accepted_args = 3 );
register_uninstall_hook( __FILE__, 'consentwow_uninstall' );
