<div id="form">
	<div id="form_box">
			<form method="post">
			<input type="hidden" name="merchant_id" value="<?php echo $merchant_id ?>" />

			<?php print form_fieldset('Application Info'); ?>

			Application Name:<br />
			<input type="text" name="application_name" size="50" class="form" value="<?php echo set_value('application_name',$user['application_name']); ?>" /><br /><?php echo form_error('application_name'); ?><br />

			Application Domain:<br />
			<input type="text" name="domain" size="50" class="form" value="<?php echo set_value('domain',$user['domain']); ?>" /><br /><?php echo form_error('domain'); ?><br />

			Callback URL:<br />
			<input type="text" name="callback_url" size="50" class="form" value="<?php echo set_value('callback_url',$user['callback_url']); ?>" /><br /><?php echo form_error('callback_url'); ?><br />

			Fetch Detail URL:<br />
			<input type="text" name="fetch_detail_url" size="50" class="form" value="<?php echo set_value('fetch_detail_url',$user['fetch_detail_url']); ?>" /><br /><?php echo form_error('fetch_detail_url'); ?><br />

			Fetch Method:<br />
			<?php echo form_dropdown('fetch_method',$this->config->item('fetch_method'),set_value('fetch_method',$user['fetch_method']))?><br />

			Application Description:<br />
			<textarea name="application_description" cols="60" rows="10"><?php echo set_value('application_description',$user['application_description']); ?></textarea><br />

			<?php print form_fieldset_close(); ?>

			<?php print form_fieldset('Email Customization'); ?>

			Reply To:<br />
			<input type="text" name="reply_to" size="50" class="form" value="<?php echo set_value('reply_to',$user['reply_to']); ?>" /><br /><?php echo form_error('reply_to'); ?><br />

			CC:<br />
			<input type="text" name="cc_to" size="50" class="form" value="<?php echo set_value('cc_to',$user['cc_to']); ?>" /><br /><?php echo form_error('cc_to'); ?><br />

			Header/Logo Image URL:<br />
			<input type="text" name="logo_url" size="50" class="form" value="<?php echo set_value('logo_url',$user['logo_url']); ?>" /><br /><?php echo form_error('logo_url'); ?><br />

			Signature:<br />
			<textarea name="signature" cols="60" rows="10"><?php echo set_value('signature',$user['signature']); ?></textarea><br />

			<?php print form_fieldset_close(); ?>

			<?php print form_fieldset('Warehouse'); ?>

			Contact Person :<br />
			<input type="text" name="contact_person" size="50" class="form" value="<?php echo set_value('contact_person',$user['contact_person']); ?>" /><?php echo form_error('contact_person'); ?><br /><br />

			<?php echo form_checkbox('same_as_personal_address', '1', $user['same_as_personal_address']);?> Same as main merchant address<br /><br />

			Phone Number:<br />
			<input type="text" name="phone" size="50" class="form" value="<?php echo set_value('phone',$user['phone']); ?>" /><?php echo form_error('phone'); ?><br /><br />

			Mobile Number:<br />
			<input type="text" name="mobile" size="50" class="form" value="<?php echo set_value('mobile',$user['mobile']); ?>" /><?php echo form_error('mobile'); ?><br /><br />

			Street:<br />
			<input type="text" name="street" size="50" class="form" value="<?php echo set_value('mobile',$user['street']); ?>" /><?php echo form_error('mobile'); ?><br /><br />

			District:<br />
			<input type="text" name="district" size="50" class="form" value="<?php echo set_value('district',$user['district']); ?>" /><?php echo form_error('district'); ?><br /><br />

			City:<br />
			<input type="text" name="city" size="50" class="form" value="<?php echo set_value('city',$user['city']); ?>" /><?php echo form_error('city'); ?><br /><br />

			Province:<br />
			<input type="text" name="province" size="50" class="form" value="<?php echo set_value('province',$user['province']); ?>" /><?php echo form_error('province'); ?><br /><br />

			Country:<br />
			<input type="text" name="country" size="50" class="form" value="<?php echo set_value('country',$user['country']); ?>" /><?php echo form_error('country'); ?><br /><br />

			ZIP:<br />
			<input type="text" name="zip" size="50" class="form" value="<?php echo set_value('zip',$user['zip']); ?>" /><?php echo form_error('zip'); ?><br /><br />

			<?php print form_fieldset_close(); ?>

			<input type="submit" value="Edit" name="edit" />
			<?php
				if(isset($back_url)){
					print $back_url;
				}
			?>
			</form>
	</div>
</div>