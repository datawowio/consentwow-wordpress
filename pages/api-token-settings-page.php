<?php
/**
 * Consent Wow Script Loader
 *
 * @package consent-wow-script-loader
 */
?>
<div class="wrap">
	<h1 class="wp-heading-inline">API Token Settings</h1>
	<form action="options.php" method="post">
		<?php
		settings_fields( 'consentwow_options_group' );
		do_settings_sections( WP_CONSENTWOW_SLUG );
		submit_button();
		?>
	</form>
</div>
