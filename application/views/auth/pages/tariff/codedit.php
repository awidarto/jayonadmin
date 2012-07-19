<div id="form">
	<div class="form_box">
			<form method="post" action="<?php echo site_url('admin/tariff/codedit/'.$user['id'])?>">

			Sequence:<br />
			<input type="text" name="seq" size="50" class="form" value="<?php echo set_value('seq',$user['seq']); ?>" /><?php echo form_error('email'); ?><br /><br />

			Price From:<br />
			<input type="text" name="fullname" size="50" class="form" value="<?php echo set_value('fullname',$user['fullname']); ?>" /><?php echo form_error('fullname'); ?><br /><br />

			Price To:<br />
			<textarea name="address" cols="60" rows="10"><?php echo set_value('address',$user['address']); ?></textarea><br />

			Period From:<br />
			<input type="text" name="phone" size="50" class="form" value="<?php echo set_value('phone',$user['phone']); ?>" /><?php echo form_error('phone'); ?><br /><br />

			Period To:<br />
			<input type="text" name="mobile" size="50" class="form" value="<?php echo set_value('mobile',$user['mobile']); ?>" /><?php echo form_error('mobile'); ?><br /><br />

			<input type="submit" value="Update" name="register" />
			<?php
				print anchor('admin/tariff/cod/'.$user['app_id'],'Cancel');
			?>
			</form>
	</div>
</div>