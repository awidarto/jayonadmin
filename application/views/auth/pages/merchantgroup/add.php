<div id="form">
	<div class="form_box">
			<form method="post" action="<?php echo site_url('admin/merchantgroup/add')?>">
			Groupname:<br />
			<input type="text" name="groupname" size="50" class="form" value="<?php echo set_value('groupname'); ?>" /><br /><?php echo form_error('groupname'); ?><br />

			Description:<br />
			<input type="text" name="description" size="50" class="form" value="<?php echo set_value('description'); ?>" /><?php echo form_error('description'); ?><br /><br />

			<input type="submit" value="Add" name="register" />
			
			<?php
				print anchor('admin/merchantgroup/manage','Cancel');
			?>
			</form>
	</div>
</div>