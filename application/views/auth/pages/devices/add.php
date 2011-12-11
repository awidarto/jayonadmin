<div id="form">
	<div class="form_box">
			<form method="post" action="<?php echo site_url('admin/devices/add')?>">

			Device Identifier / Code:<br />
			<input type="text" name="identifier" size="50" class="form" value="<?php echo set_value('identifier'); ?>" /><?php echo form_error('identifier'); ?><br /><br />

			Device Name:<br />
			<input type="text" name="devname" size="50" class="form" value="<?php echo set_value('devname'); ?>" /><?php echo form_error('devname'); ?><br /><br />

			Device Description:<br />
			<textarea name="descriptor" cols="60" rows="10"><?php echo set_value('descriptor'); ?></textarea><br />

			Mobile Number:<br />
			<input type="text" name="mobile" size="50" class="form" value="<?php echo set_value('mobile'); ?>" /><?php echo form_error('mobile'); ?><br /><br />
				
			<input type="submit" value="Add" name="register" />
			<?php
				print anchor('admin/devices/manage','Cancel');
			?>
			</form>
	</div>
</div>