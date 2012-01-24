<div id="form">
	<div class="form_box">
			<form method="post" action="<?php echo site_url('admin/options/edit/'.$user['id'])?>">
			<?php echo set_value('key',$user['key']); ?>
			<input type="hidden" name="key" value="<?php echo set_value('key',$user['key']); ?>" />
			<input type="text" name="val" id="val" size="50" class="form" value="<?php echo set_value('val',$user['val']); ?>" /><?php echo form_error('email'); ?><br /><br />

			<input type="submit" value="Update" name="register" />
			<?php
				print anchor('admin/options/manage','Cancel');
			?>
			</form>
	</div>
</div>
