<?php

/**
 * Consent Wow Script Loader
 *
 * @package consent-wow-script-loader
 */
require_once plugin_dir_path( WP_CONSENTWOW_FILE ) . 'includes/class-consent-wow-form-list.php';
require_once plugin_dir_path( WP_CONSENTWOW_FILE ) . 'pages/form-notice.php';

$form_list = new Consent_Wow_Form_List();

if ( isset( $_GET['id'] ) && ! empty( $_GET['id'] ) ) {
	$action = 'edit';
	$id = $_GET['id'];
	$title = "Edit a Form#{$id}";
	$form = $form_list->find($id);
}

if ( ! isset( $form ) ) {
	$action = 'add';
	$title = 'Create a new Form';
	$form = array();
}

?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo $title ?></h1>
    <h4>ปลั๊กอินนี้สนับสนุนเฉพาะแบบฟอร์มประเภท Contact Form (CF7)
        คุณสามารถอ่านวิธีการติดตั้งและรายละเอียดการเพิ่มแบบฟอร์มได้ ที่นี่ </h4>
    <form action="<?php echo admin_url('admin.php?action=consentwow_form_post'); ?>" method="post">
        <?php consentwow_form_display_notice(); ?>
        <?php if ($action == 'edit') : ?>
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
                        <input required type="text" id="consentwow_form_name" name="consentwow_form[form_name]"
                            placeholder="Contact form" class="regular-text"
                            value="<?php echo isset($form['form_name']) ? $form['form_name'] : ''; ?>" />
                    </td>
                </tr>
                <tr class="consentwow-form-id-input">
                    <th scope="row">
                        <label for="consentwow_form_id">ฟอร์ม ID</label>
                    </th>
                    <td>
                        <input required type="text" id="consentwow_form_id" name="consentwow_form[form_id]"
                            placeholder="1" class="regular-text"
                            value="<?php echo isset($form['form_id']) ? $form['form_id'] : ''; ?>" />
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
                        <input required type="text" id="consentwow_email" name="consentwow_form[email]"
                            placeholder="อีเมลแอดเดรส" class="regular-text"
                            value="<?php echo isset($form['email']) ? $form['email'] : ''; ?>" />
                    </td>
                </tr>
                <tr class="consentwow-first-name-input">
                    <th scope="row">
                        <label for="consentwow_first_name">ชื่อจริง</label>
                    </th>
                    <td>
                        <input type="text" id="consentwow_first_name" name="consentwow_form[first_name]"
                            placeholder="ชื่อจริง" class="regular-text"
                            value="<?php echo isset($form['first_name']) ? $form['first_name'] : ''; ?>" />
                    </td>
                </tr>
                <tr class="consentwow-last-name-input">
                    <th scope="row">
                        <label for="consentwow_last_name">นามสกุล</label>
                    </th>
                    <td>
                        <input type="text" id="consentwow_last_name" name="consentwow_form[last_name]"
                            placeholder="นามสกุล" class="regular-text"
                            value="<?php echo isset($form['last_name']) ? $form['last_name'] : ''; ?>" />
                    </td>
                </tr>
                <tr class="consentwow-phone-number-input">
                    <th scope="row">
                        <label for="consentwow_phone_number">เบอร์โทรศัพท์</label>
                    </th>
                    <td>
                        <input type="text" id="consentwow_phone_number" name="consentwow_form[phone_number]"
                            placeholder="เบอร์โทรศัพท์" class="regular-text"
                            value="<?php echo isset($form['phone_number']) ? $form['phone_number'] : ''; ?>" />
                    </td>
                </tr>
                <tr class="consentwow-consent-head">
                    <th>
                        เชื่อมกับข้อมูลความยินยอม
                    </th>
                </tr>
            </tbody>
        </table>
        <div class="submit">
            <button class="button button-primary" style="width:fit-content;" onClick="addConsent()"> +
                เพิ่มการเชื่อมต่อกับข้อมูลความยินยอม </button>
        </div>
        <div class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="เพิ่มฟอร์ม">
        </div>
    </form>

    <script>
    let i = 0;

    const addConsent = () => {
        const contentTable = document.getElementsByTagName('tbody');

        const tag = document.createElement('tr');
        tag.classList.add('consentwow-consent-input');
        tag.id = `consentwow-consent-field-${i}`;

        // add purpose key field
        const purposeKeyCol = document.createElement('td');
        const purposeKeyInput = document.createElement("input");
        purposeKeyInput.type = "text";
        purposeKeyInput.name = `consentwow_form[conset_key_${i}]`;
        purposeKeyInput.classList.add('regular-text');
        purposeKeyInput.placeholder = "ID ของวัตถุประสงค์";
        purposeKeyInput.style = "width:185px;";
        purposeKeyCol.appendChild(purposeKeyInput);
        tag.appendChild(purposeKeyCol);

        // add purpose key field
        const purposeNameCol = document.createElement('td');
        const purposeNameInput = document.createElement("input");
        purposeNameInput.type = "text";
        purposeNameInput.name = `consentwow_form[conset_name_${i}]`;
        purposeNameInput.classList.add('regular-text');
        purposeNameInput.placeholder = "ชื่อวัตุประสงค์ความยินยอม";
        purposeNameCol.appendChild(purposeNameInput);

        // add remove button
        const removeButton = document.createElement("button");
        removeButton.classList.add('button');
        removeButton.style = "background-color:red; border: white; color: white; margin-left: 5px";
        removeButton.textContent = 'X';
        removeButton.id = i;
        removeButton.onclick = e => {
            const id = e.target.id;
            document.getElementById(`consentwow-consent-field-${id}`).remove();
        }

        purposeNameCol.appendChild(removeButton);
        tag.appendChild(purposeNameCol);

        // add child to parent
        contentTable[0].appendChild(tag);
        i += 1;
    }
    </script>
</div>