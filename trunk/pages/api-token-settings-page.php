<?php
/**
 * Consent Wow | PDPA Consent Solution
 *
 * @package           consentwow-consent-solution
 * @author            Consent Wow
 * @copyright         2022 nDataThoth Limited
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
