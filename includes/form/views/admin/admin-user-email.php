<?php
/**
 * Form View: Input Type User Email
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="ur-input-type-user-email ur-admin-template">

	<div class="ur-label">
		<label><?php echo $this->get_general_setting_data( 'label' ); ?><span style="color:red">*</span></label>

	</div>
	<div class="ur-field" data-field-key="user_email">

		<input type="email" id="ur-input-type-user-email"
			   placeholder="<?php echo $this->get_general_setting_data( 'placeholder' ); ?>"/>

	</div>
	<?php

	UR_User_Email::get_instance()->get_setting();

	?>
</div>

