<div id="form">
	<div class="form_box">
			<form method="post" action="<?php echo site_url('admin/users/editpass/'.$user['id'])?>">
			Username: <strong><?php echo set_value('username',$user['username'])?></strong><br />
			<input type="hidden" name="id" size="50" class="form" value="<?php echo set_value('id'); ?>" /><br /><?php echo form_error('username'); ?><br />
			Password:<br />
				<input type="password" name="password" size="50" class="form" value="<?php echo set_value('password'); ?>" /><?php echo form_error('password'); ?><br /><br />
			Password confirmation:<br />
				<input type="password" name="password_conf" size="50" class="form" value="<?php echo set_value('conf_password'); ?>" /><?php echo form_error('conf_password'); ?><br /><br />
			<input type="submit" value="Update Password" name="update_pass" />
			<?php
				print anchor('admin/users/manage','Cancel');
			?>
			</form>
	</div>
</div>