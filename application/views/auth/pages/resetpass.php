<div id="login" style="width:300px;">
	<h2>Reset Password</h2>
	<div class="form_box">
			<form method="POST">
			Email:<br />
			<input type="text" name="email" value="<?php echo set_value('email'); ?>" size="50" class="form" /><?php echo form_error('email'); ?><br /><br />
			<input type="submit" value="Reset Password" name="login" />&nbsp;<span><?php print anchor('login','Back to login');?></span>
			</form>
	</div>
</div>