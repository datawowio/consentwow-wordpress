<?php
/**
 * Consent Wow Script Loader
 *
 * @package consent-wow-script-loader
 */
require_once plugin_dir_path( WP_CONSENTWOW_FILE ) . 'includes/class-consent-wow-form-list.php';

$form_list = new Consent_Wow_Form_List();

if ( isset( $_GET['id'] ) && ! empty( $_GET['id'] ) ) {
	$action = 'edit';
	$id = $_GET['id'];
	$title = "Edit a Form#{$id}";
	$form = $form_list->find( $id );
}

if ( ! isset( $form ) ) {
	$action = 'add';
	$title = 'Create a new Form';
	$form = array();
}
?>
<div class="wrap">
	<h1 class="wp-heading-inline"><?php echo $title ?></h1>
	<form action="<?php echo admin_url( 'admin.php?action=consentwow_form_post' ); ?>" method="post">
		<?php if ( $action == 'edit' ) : ?>
		<input type="hidden" name="action" value="consentwow_form_post" />
		<input name="consentwow_form[id]" type="hidden" value="<?php echo $id; ?>" />
		<?php endif; ?>
		<table class="form-table" role="presentation">
			<tbody>
				<tr class="consentwow-form-name-input">
					<th scope="row">
						<label for="consentwow_form_name">Form Name</label>
					</th>
					<td>
						<input
							required
							type="text"
							id="consentwow_form_name"
							name="consentwow_form[form_name]"
							class="regular-text"
							value="<?php echo $form['form_name']; ?>"
						/>
					</td>
				</tr>
				<tr class="consentwow-form-id-input">
					<th scope="row">
						<label for="consentwow_form_id">Form ID</label>
					</th>
					<td>
						<input
							required
							type="text"
							id="consentwow_form_id"
							name="consentwow_form[form_id]"
							class="regular-text"
							value="<?php echo $form['form_id']; ?>"
						/>
					</td>
				</tr>
				<tr class="consentwow-email-input">
					<th scope="row">
						<label for="consentwow_email">Email</label>
					</th>
					<td>
						<input
							required
							type="text"
							id="consentwow_email"
							name="consentwow_form[email]"
							class="regular-text"
							value="<?php echo $form['email']; ?>"
						/>
					</td>
				</tr>
				<tr class="consentwow-first-name-input">
					<th scope="row">
						<label for="consentwow_first_name">First Name</label>
					</th>
					<td>
						<input
							type="text"
							id="consentwow_first_name"
							name="consentwow_form[first_name]"
							class="regular-text"
							value="<?php echo $form['first_name']; ?>"
						/>
					</td>
				</tr>
				<tr class="consentwow-last-name-input">
					<th scope="row">
						<label for="consentwow_last_name">Last Name</label>
					</th>
					<td>
						<input
							type="text"
							id="consentwow_last_name"
							name="consentwow_form[last_name]"
							class="regular-text"
							value="<?php echo $form['last_name']; ?>"
						/>
					</td>
				</tr>
				<tr class="consentwow-phone-number-input">
					<th scope="row">
						<label for="consentwow_phone_number">Telephone No.</label>
					</th>
					<td>
						<input
							type="text"
							id="consentwow_phone_number"
							name="consentwow_form[phone_number]"
							class="regular-text"
							value="<?php echo $form['phone_number']; ?>"
						/>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
		</p>
	</form>
</div>
