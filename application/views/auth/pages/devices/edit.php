<div id="form">
	<div class="form_box">
			<form method="post" action="<?php echo site_url('admin/devices/edit/'.$user['id'])?>">

			Device Identifier / Code:<br />
			<input type="text" name="identifier" size="50" class="form" value="<?php echo set_value('identifier',$user['identifier']); ?>" /><?php echo form_error('identifier'); ?><br /><br />

			Device Name:<br />
			<input type="text" name="devname" size="50" class="form" value="<?php echo set_value('devname',$user['devname']); ?>" /><?php echo form_error('devname'); ?><br /><br />

			Device Description:<br />
			<textarea name="descriptor" cols="60" rows="10"><?php echo set_value('descriptor',$user['descriptor']); ?></textarea><br />

			Mobile Number:<br />
			<input type="text" name="mobile" size="50" class="form" value="<?php echo set_value('mobile',$user['mobile']); ?>" /><?php echo form_error('mobile'); ?><br /><br />

			<input type="submit" value="Update" name="register" />
			<?php
				print anchor('admin/devices/manage','Cancel');
			?>
			</form>
	</div>
</div>