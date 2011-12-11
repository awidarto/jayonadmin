<div id="form">
	<div class="form_box">
			<form method="post" action="<?php echo site_url('admin/couriers/add')?>">
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

			Address:<br />
			<textarea name="address" cols="60" rows="10"><?php echo set_value('address'); ?></textarea><br />

			Phone Number:<br />
			<input type="text" name="phone" size="50" class="form" value="<?php echo set_value('phone'); ?>" /><?php echo form_error('phone'); ?><br /><br />

			Mobile Number:<br />
			<input type="text" name="mobile" size="50" class="form" value="<?php echo set_value('mobile'); ?>" /><?php echo form_error('mobile'); ?><br /><br />
				
			<input type="submit" value="Add" name="register" />
			<?php
				print anchor('admin/couriers/manage','Cancel');
			?>
			</form>
	</div>
</div>