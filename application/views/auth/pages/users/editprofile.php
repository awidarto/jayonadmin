<div id="form">	
	<div class="form_box">
			<form method="post" action="<?php echo site_url('admin/users/editprofile')?>">
			Username:<br />
			<input type="text" name="username" size="50" class="form" value="<?php echo set_value('username', $user['username']); ?>" /><br /><?php echo form_error('username'); ?><br />
						
			Email:<br />
			<input type="text" name="email" size="50" class="form" value="<?php echo set_value('email',$user['email']); ?>" /><?php echo form_error('email'); ?><br /><br />

			Full Name:<br />
			<input type="text" name="fullname" size="50" class="form" value="<?php echo set_value('fullname', $user['fullname']); ?>" /><?php echo form_error('fullname'); ?><br /><br />

			Mobile Number:<br />
			<input type="text" name="mobile" size="50" class="form" value="<?php echo set_value('mobile', $user['mobile']); ?>" /><?php echo form_error('mobile'); ?><br /><br />

			<input type="submit" value="Update" name="update" />
			<?php
				print anchor('admin/dashboard','Cancel');
			?>
			</form>
	</div>
</div>