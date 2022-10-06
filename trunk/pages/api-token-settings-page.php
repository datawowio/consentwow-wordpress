<?php
/**
 * Consent Wow Plugin
 *
 * @package           consent-wow-plugin
 * @author            Consent Wow
 * @copyright         2022 Consent Wow
 * @license           GPL-3.0-or-later
 */
?>
<div class="wrap">
	<h1 class="wp-heading-inline">API Token Settings</h1>
	<form action="options.php" method="post">
		<?php
		settings_fields( 'consentwow_api_token_group' );
		do_settings_sections( WP_CONSENTWOW_SLUG );
		submit_button();
		?>
	</form>
</div>
