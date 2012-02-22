<div id="form">
	
	<?php if(empty($username)) { ?>
	<h2>Add</h2>
	<?php } else { ?>
	<h2>Update</h2>
	<?php } ?>
	
	<div class="form_box">
			<form method="post" action="<?php echo site_url('admin/users/add')?>">
			<?php if(empty($username)) { ?>
			Username:<br />
			<input type="text" name="username" size="50" class="form" value="<?php echo set_value('username'); ?>" /><br /><?php echo form_error('username'); ?><br />


			Password:<br />
			<input type="password" name="password" size="50" class="form" value="<?php echo set_value('password'); ?>" /><?php echo form_error('password'); ?><br /><br />
			Password confirmation:<br />
			<input type="password" name="password_conf" size="50" class="form" value="<?php echo set_value('conf_password'); ?>" /><?php echo form_error('conf_password'); ?><br /><br />
			<?php } ?>
			Group:<br />
			<?php echo form_dropdown('group_id',$groups,set_value('group_id'))?>
			<br />
			Email:<br />
			<?php if(empty($username)){ ?>
				<input type="text" name="email" size="50" class="form" value="<?php echo set_value('email'); ?>" /><?php echo form_error('email'); ?><br /><br />
			<?php }else{ ?>
				<input type="text" name="email" size="50" class="form" value="<?php echo set_value('email', $email); ?>" /><?php echo form_error('email'); ?><br /><br />
			<?php } ?>
				
			<?php if(empty($username)) { ?>
				<input type="submit" value="Add" name="register" />
			<?php } else { ?>
				<input type="submit" value="Update" name="register" />
			<?php } ?>
			</form>
	</div>
</div>