<?php
/**
 * Consent Wow Script Loader
 *
 * @package consent-wow-script-loader
 */
require_once plugin_dir_path( WP_CONSENTWOW_FILE ) . 'includes/class-consent-wow-form-list-table.php';

$form_list_table = new Consent_Wow_Form_List_Table();
$form_list_table->prepare_items();
?>
<div class="wrap">
	<h1 class="wp-heading-inline">Form List Table Page</h1>
	<a href="<?php echo admin_url( 'admin.php?page=' . WP_CONSENTWOW_FORM_NEW_SLUG ); ?>" class="page-title-action">Add New</a>
	<hr class="wp-header-end" />
	<form method="get">
		<input type="hidden" name="page" value="<?php echo WP_CONSENTWOW_FORM_LIST_SLUG; ?>" />
		<?php $form_list_table->display(); ?>
	</form>
</div>
