<?php
/**
 * Consent Wow | PDPA Consent Solution
 *
 * @package           consentwow-consent-solution
 * @author            Consent Wow
 * @copyright         2022 Consent Wow
 * @license           GPL-3.0-or-later
 */
require_once plugin_dir_path( WP_CONSENTWOW_FILE ) . 'includes/class-consent-wow-form-list-table.php';
require_once plugin_dir_path( WP_CONSENTWOW_FILE ) . 'pages/form-notice.php';

$form_list_table = new Consent_Wow_Form_List_Table();
$form_list_table->prepare_items();
?>
<div class="wrap">
	<h1 class="wp-heading-inline">Form List Table Page</h1>
	<a href="<?php echo admin_url( 'admin.php?page=' . WP_CONSENTWOW_FORM_NEW_SLUG ); ?>" class="page-title-action">Add New</a>
	<hr class="wp-header-end" />
	<?php consentwow_form_display_notice(); ?>
	<form method="get">
		<input type="hidden" name="page" value="<?php echo WP_CONSENTWOW_FORM_LIST_SLUG; ?>" />
		<?php $form_list_table->display(); ?>
	</form>
</div>
