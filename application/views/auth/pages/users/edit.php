<div id="form">
	<div class="form_box">
			<form method="post" action="<?php echo site_url('admin/users/edit/'.$user['id'])?>">
			Username: <strong><?php echo $user['username'];?></strong><br /><br />
			Group:<br />
				<?php echo form_dropdown('group_id',$groups,set_value('group_id',$user['group_id']));?>
			<br />
			Email:<br />
			<input type="text" name="email" size="50" class="form" value="<?php echo set_value('email',$user['email']); ?>" /><?php echo form_error('email'); ?><br /><br />
			Full Name:<br />
			<input type="text" name="fullname" size="50" class="form" value="<?php echo set_value('fullname', $user['fullname']); ?>" /><?php echo form_error('fullname'); ?><br /><br />
			Mobile:<br />
			<input type="text" name="mobile" size="50" class="form" value="<?php echo set_value('mobile', $user['mobile']); ?>" /><?php echo form_error('mobile'); ?><br /><br />
				<input type="submit" value="Update" name="register" />
				<?php
					print anchor('admin/users/manage','Cancel');
				?>
			</form>
	</div>
</div>