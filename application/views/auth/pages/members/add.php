<div id="form">
	<div class="form_box">
			<form method="post" >

			<?php print form_fieldset('Personal Info'); ?>

			Username:<br />
			<input type="text" name="username" size="50" class="form" value="<?php echo set_value('username'); ?>" /><br /><?php echo form_error('username'); ?><br />

			Password:<br />
			<input type="password" name="password" size="50" class="form" value="<?php echo set_value('password'); ?>" /><?php echo form_error('password'); ?><br /><br />

			Password confirmation:<br />
			<input type="password" name="password_conf" size="50" class="form" value="<?php echo set_value('conf_password'); ?>" /><?php echo form_error('conf_password'); ?><br /><br />

			Group:<br />
			<?php echo form_dropdown('group_id',$groups,set_value('group_id'))?>
			<br />

			Email:<br />
			<input type="text" name="email" size="50" class="form" value="<?php echo set_value('email'); ?>" /><?php echo form_error('email'); ?><br /><br />

			Full Name:<br />
			<input type="text" name="fullname" size="50" class="form" value="<?php echo set_value('fullname'); ?>" /><?php echo form_error('fullname'); ?><br /><br />

			Street:<br />
			<input type="text" name="street" size="50" class="form" value="<?php echo set_value('mobile'); ?>" /><?php echo form_error('mobile'); ?><br /><br />

			District:<br />
			<input type="text" name="district" size="50" class="form" value="<?php echo set_value('district'); ?>" /><?php echo form_error('district'); ?><br /><br />

			City:<br />
			<input type="text" name="city" size="50" class="form" value="<?php echo set_value('city'); ?>" /><?php echo form_error('city'); ?><br /><br />

			Province:<br />
			<input type="text" name="province" size="50" class="form" value="<?php echo set_value('province'); ?>" /><?php echo form_error('province'); ?><br /><br />

			Country:<br />
			<input type="text" name="country" size="50" class="form" value="<?php echo set_value('country'); ?>" /><?php echo form_error('country'); ?><br /><br />

			ZIP:<br />
			<input type="text" name="zip" size="50" class="form" value="<?php echo set_value('zip'); ?>" /><?php echo form_error('zip'); ?><br /><br />

			Phone Number:<br />
			<input type="text" name="phone" size="50" class="form" value="<?php echo set_value('phone'); ?>" /><?php echo form_error('phone'); ?><br /><br />

			Mobile Number:<br />
			<input type="text" name="mobile" size="50" class="form" value="<?php echo set_value('mobile'); ?>" /><?php echo form_error('mobile'); ?><br /><br />

			<?php print form_fieldset_close(); ?>

			<?php print form_fieldset('Merchant Info'); ?>

			Merchant Name:<br />
			<input type="text" name="merchantname" size="50" class="form" value="<?php echo set_value('merchantname'); ?>" /><?php echo form_error('merchantname'); ?><br /><br />

			Bank:<br />
			<input type="text" name="bank" size="50" class="form" value="<?php echo set_value('bank'); ?>" /><?php echo form_error('bank'); ?><br /><br />

			Account Name:<br />
			<input type="text" name="account_name" size="50" class="form" value="<?php echo set_value('account_name'); ?>" /><?php echo form_error('account_name'); ?><br /><br />

			Account Number:<br />
			<input type="text" name="account_number" size="50" class="form" value="<?php echo set_value('account_number'); ?>" /><?php echo form_error('account_number'); ?><br /><br />
			
			<?php echo form_checkbox('same_as_personal_address', '1', true);?> Same as personal address<br /><br />

			Street:<br />
			<input type="text" name="mc_street" size="50" class="form" value="<?php echo set_value('mc_street'); ?>" /><?php echo form_error('mc_street'); ?><br /><br />

			District:<br />
			<input type="text" name="mc_district" size="50" class="form" value="<?php echo set_value('mc_district'); ?>" /><?php echo form_error('mc_district'); ?><br /><br />

			City:<br />
			<input type="text" name="mc_city" size="50" class="form" value="<?php echo set_value('mc_city'); ?>" /><?php echo form_error('mc_city'); ?><br /><br />

			Province:<br />
			<input type="text" name="mc_province" size="50" class="form" value="<?php echo set_value('mc_province'); ?>" /><?php echo form_error('mc_province'); ?><br /><br />

			Country:<br />
			<input type="text" name="mc_country" size="50" class="form" value="<?php echo set_value('mc_country'); ?>" /><?php echo form_error('mc_country'); ?><br /><br />

			ZIP:<br />
			<input type="text" name="mc_zip" size="50" class="form" value="<?php echo set_value('mc_zip'); ?>" /><?php echo form_error('mc_zip'); ?><br /><br />

			Phone Number:<br />
			<input type="text" name="mc_phone" size="50" class="form" value="<?php echo set_value('mc_phone'); ?>" /><?php echo form_error('mc_phone'); ?><br /><br />

			Mobile Number:<br />
			<input type="text" name="mc_mobile" size="50" class="form" value="<?php echo set_value('mc_mobile'); ?>" /><?php echo form_error('mc_mobile'); ?><br /><br />

			<?php echo form_checkbox('mc_unlimited_time', '1',true);?> Unlimited Order Time<br /><br />

			First Order Time:<br />
			<input type="text" name="mc_first_order" size="50" class="form" value="<?php echo set_value('mc_first_order'); ?>" /><?php echo form_error('mc_first_order'); ?><br /><br />

			Last Order Time:<br />
			<input type="text" name="mc_last_order" size="50" class="form" value="<?php echo set_value('mc_last_order'); ?>" /><?php echo form_error('mc_last_order'); ?><br /><br />
			
			<?php print form_fieldset_close(); ?>

			<input type="submit" value="Add" name="register" />
			<?php
				if(isset($back_url)){
					print $back_url;
				}
			?>
			</form>
	</div>
</div>