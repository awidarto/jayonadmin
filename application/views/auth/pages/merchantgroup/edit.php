<div id="form">
	<div class="form_box">
			<form method="post">
			Groupname:<br />
			<input type="text" name="groupname" size="50" class="form" value="<?php echo set_value('groupname',$user['groupname']); ?>" /><br /><?php echo form_error('groupname'); ?><br />

			Description:<br />
			<input type="text" name="description" size="50" class="form" value="<?php echo set_value('description' ,$user['description']); ?>" /><?php echo form_error('description'); ?><br /><br />

			<input type="submit" value="Update" name="register" />
			
			<?php
				print anchor('admin/merchantgroup/manage','Cancel');
			?>
			</form>
	</div>
</div>