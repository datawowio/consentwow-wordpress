<?php
/**
 * Consent Wow Consent Solution
 *
 * @package           consent-wow-consent-solution
 * @author            Consent Wow
 * @copyright         2022 Consent Wow
 * @license           GPL-3.0-or-later
 */
require_once plugin_dir_path( WP_CONSENTWOW_FILE ) . 'includes/class-consent-wow-form-list.php';
require_once plugin_dir_path( WP_CONSENTWOW_FILE ) . 'pages/form-notice.php';

$form_list = new Consent_Wow_Form_List();

if ( isset( $_GET['id'] ) && ! empty( $_GET['id'] ) ) {
	$action = 'edit';
	$id = esc_attr( $_GET['id'] );
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
	<h4>ปลั๊กอินนี้สนับสนุนเฉพาะแบบฟอร์มประเภท Contact Form (CF7) คุณสามารถอ่านวิธีการติดตั้งและรายละเอียดการเพิ่มแบบฟอร์มได้ ที่นี่</h4>
	<form action="<?php echo admin_url( 'admin.php?action=consentwow_form_post' ); ?>" method="post">
		<?php consentwow_form_display_notice(); ?>
		<?php if ( $action == 'edit' ) : ?>
		<input type="hidden" name="action" value="consentwow_form_post" />
		<input name="consentwow_form[id]" type="hidden" value="<?php echo $id; ?>" />
		<?php endif; ?>
		<table class="form-table" role="presentation">
			<tbody>
				<tr class="consentwow-form-name-input">
					<th scope="row">
						<label for="consentwow_form_name">ชื่อฟอร์ม</label>
					</th>
					<td>
						<input
							required
							type="text"
							id="consentwow_form_name"
							name="consentwow_form[form_name]"
							placeholder="Contact form"
							class="regular-text"
							value="<?php echo isset( $form['form_name'] ) ? esc_attr( $form['form_name'] ) : ''; ?>"
						/>
					</td>
				</tr>
				<tr class="consentwow-form-id-input">
					<th scope="row">
						<label for="consentwow_form_id">ฟอร์ม ID</label>
					</th>
					<td>
						<input
							required
							type="text"
							id="consentwow_form_id"
							name="consentwow_form[form_id]"
							placeholder="1"
							class="regular-text"
							value="<?php echo isset( $form['form_id'] ) ? esc_attr( $form['form_id'] ) : ''; ?>"
						/>
					</td>
				</tr>
				<tr class="consentwow-consent-input">
					<th>เชื่อมกับข้อมูลของเจ้าของข้อมูล</th>
				</tr>
				<tr class="consentwow-email-input">
					<th scope="row">
						<label for="consentwow_email">Email (UID)</label>
					</th>
					<td>
						<input
							required
							type="text"
							id="consentwow_email"
							name="consentwow_form[email]"
							placeholder="อีเมลแอดเดรส"
							class="regular-text"
							value="<?php echo isset( $form['email'] ) ? esc_attr( $form['email'] ) : ''; ?>"
						/>
					</td>
				</tr>
				<tr class="consentwow-first-name-input">
					<th scope="row">
						<label for="consentwow_first_name">ชื่อจริง</label>
					</th>
					<td>
						<input
							type="text"
							id="consentwow_first_name"
							name="consentwow_form[first_name]"
							placeholder="ชื่อจริง"
							class="regular-text"
							value="<?php echo isset( $form['first_name'] ) ? esc_attr( $form['first_name'] ) : ''; ?>"
						/>
					</td>
				</tr>
				<tr class="consentwow-last-name-input">
					<th scope="row">
						<label for="consentwow_last_name">นามสกุล</label>
					</th>
					<td>
						<input
							type="text"
							id="consentwow_last_name"
							name="consentwow_form[last_name]"
							placeholder="นามสกุล"
							class="regular-text"
							value="<?php echo isset( $form['last_name'] ) ? esc_attr( $form['last_name'] ) : ''; ?>"
						/>
					</td>
				</tr>
				<tr class="consentwow-phone-number-input">
					<th scope="row">
						<label for="consentwow_phone_number">เบอร์โทรศัพท์</label>
					</th>
					<td>
						<input
							type="text"
							id="consentwow_phone_number"
							name="consentwow_form[phone_number]"
							placeholder="เบอร์โทรศัพท์"
							class="regular-text"
							value="<?php echo isset( $form['phone_number'] ) ? esc_attr( $form['phone_number'] ) : ''; ?>"
						/>
					</td>
				</tr>
				<tr class="consentwow-consent-head">
					<th>
						เชื่อมกับข้อมูลความยินยอม
					</th>
				</tr>
				<?php
				if ( isset( $form['consents'] ) && is_array( $form['consents'] ) && count( $form['consents'] ) > 0 ) :
					foreach ( $form['consents'] as $index => $consent_purpose ) :
						if ( isset( $consent_purpose['consent_id'] ) && isset( $consent_purpose['name'] ) ) :
							$consent_id = esc_attr( $consent_purpose['consent_id'] );
							$name = esc_attr( $consent_purpose['name'] );

							$unique_id = $consent_id . '-' . $index;
				?>
				<tr class="consentwow-consent-input" id="consentwow-consent-field-<?php echo $unique_id; ?>">
					<td>
						<input required type="text" name="consentwow_form[consents][<?php echo $unique_id; ?>][consent_id]" class="regular-text" placeholder="ID ของวัตถุประสงค์" style="width: 185px;" value="<?php echo $consent_id; ?>" />
					</td>
					<td>
						<input required type="text" name="consentwow_form[consents][<?php echo $unique_id; ?>][name]" class="regular-text" placeholder="ชื่อวัตุประสงค์ความยินยอม" value="<?php echo $name; ?>" />
						<button type="button" class="button" id="<?php echo $unique_id; ?>" style="background-color: red; border: white; color: white; margin-left: 5px;" onclick="handleRemoveButton(event)">
							X
						</button>
					</td>
				</tr>
				<?php
					endforeach;
				endif;
				?>
			</tbody>
		</table>
		<div class="submit">
			<button type="button" class="button button-primary" style="width:fit-content;" onclick="addConsent()">
				+ เพิ่มการเชื่อมต่อกับข้อมูลความยินยอม
			</button>
		</div>
		<div class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo ( $action == 'add' ) ? 'เพิ่มฟอร์ม' : 'บันทึกฟอร์ม'; ?>">
		</div>
	</form>
</div>
