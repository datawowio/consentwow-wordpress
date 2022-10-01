<?php
/**
 * Consent Wow Script Loader
 *
 * @package consent-wow-script-loader
 */

/**
 * Display an alert bar with the message from form notice and delete the notice
 * afterward.
 */
function consentwow_form_display_notice() {
	$notice = get_transient( 'consentwow_form_notice' );

	if ( isset( $notice ) && ! empty( $notice ) ) : ?>
<div id="consentwow_form_notice" class="notice notice-<?php echo $notice['type'] ?>">
	<p>
		<strong><?php echo $notice['message'] ?></strong>
	</p>
</div>
<?php
		delete_transient( 'consentwow_form_notice' );
	endif;
}
