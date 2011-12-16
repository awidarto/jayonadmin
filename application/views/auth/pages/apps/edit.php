<div id="form">
	<div id="form_box">
			<form method="post" action="<?php echo site_url($act_url)?>">
			<input type="hidden" name="merchant_id" value="<?php echo $merchant_id ?>" />
			Application Name:<br />
			<input type="text" name="application_name" size="50" class="form" value="<?php echo set_value('application_name',$user['application_name']); ?>" /><br /><?php echo form_error('application_name'); ?><br />

			Application Domain:<br />
			<input type="text" name="domain" size="50" class="form" value="<?php echo set_value('domain',$user['domain']); ?>" /><br /><?php echo form_error('domain'); ?><br />

			Logo URL:<br />
			<input type="text" name="logo_url" size="50" class="form" value="<?php echo set_value('logo_url',$user['logo_url']); ?>" /><br /><?php echo form_error('logo_url'); ?><br />

			Callback URL:<br />
			<input type="text" name="callback_url" size="50" class="form" value="<?php echo set_value('callback_url',$user['callback_url']); ?>" /><br /><?php echo form_error('callback_url'); ?><br />

			Fetch Detail URL:<br />
			<input type="text" name="fetch_detail_url" size="50" class="form" value="<?php echo set_value('fetch_detail_url',$user['fetch_detail_url']); ?>" /><br /><?php echo form_error('fetch_detail_url'); ?><br />

			Fetch Method:<br />
			<?php echo form_dropdown('fetch_method',$this->config->item('fetch_method'),set_value('fetch_method',$user['fetch_method']))?><br />

			Application Description:<br />
			<textarea name="application_description" cols="60" rows="10"><?php echo set_value('application_description',$user['application_description']); ?></textarea><br />

			Signature:<br />
			<textarea name="signature" cols="60" rows="10"><?php echo set_value('signature',$user['signature']); ?></textarea><br />

			<input type="submit" value="Edit" name="edit" />
				<?php
					print $back_url;
				?>
			</form>
	</div>
</div>