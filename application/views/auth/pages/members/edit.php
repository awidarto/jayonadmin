<div id="form">
	<div class="form_box">
			<form method="post" action="<?php echo site_url('admin/members/edit/'.$user['id'])?>">
			Username: <strong><?php echo $user['username'];?></strong><br /><br />
			Group:<br />
				<?php echo form_dropdown('group_id',$groups,set_value('group_id',$user['group_id']));?>
			<br />
			Email:<br />
			<input type="text" name="email" size="50" class="form" value="<?php echo set_value('email',$user['email']); ?>" /><?php echo form_error('email'); ?><br /><br />

			Full Name:<br />
			<input type="text" name="fullname" size="50" class="form" value="<?php echo set_value('fullname',$user['fullname']); ?>" /><?php echo form_error('fullname'); ?><br /><br />

			Merchant Name:<br />
			<input type="text" name="merchantname" size="50" class="form" value="<?php echo set_value('merchantname',$user['merchantname']); ?>" /><?php echo form_error('merchantname'); ?><br /><br />

			Bank:<br />
			<input type="text" name="bank" size="50" class="form" value="<?php echo set_value('bank',$user['bank']); ?>" /><?php echo form_error('bank'); ?><br /><br />

			Account Name:<br />
			<input type="text" name="account_name" size="50" class="form" value="<?php echo set_value('account_name',$user['account_name']); ?>" /><?php echo form_error('account_name'); ?><br /><br />

			Account Number:<br />
			<input type="text" name="account_number" size="50" class="form" value="<?php echo set_value('account_number',$user['account_number']); ?>" /><?php echo form_error('account_number'); ?><br /><br />

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

			Phone Number:<br />
			<input type="text" name="phone" size="50" class="form" value="<?php echo set_value('phone',$user['phone']); ?>" /><?php echo form_error('phone'); ?><br /><br />

			Mobile Number:<br />
			<input type="text" name="mobile" size="50" class="form" value="<?php echo set_value('mobile',$user['mobile']); ?>" /><?php echo form_error('mobile'); ?><br /><br />

			<input type="submit" value="Update" name="register" />
			<?php
				print anchor('admin/members/manage','Cancel');
			?>
			</form>
	</div>
</div>