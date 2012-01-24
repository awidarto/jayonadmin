<script>
	$(document).ready(function() {
		$('#holiday').datepicker({ dateFormat: 'yy-mm-dd' });
	});
</script>

<div id="form">
	<div class="form_box">
			<form method="post" action="<?php echo site_url('admin/holidays/add')?>">
			Holiday Name:<br />
			<input type="text" name="holidayname" size="50" class="form" value="<?php echo set_value('holidayname'); ?>" /><br /><?php echo form_error('username'); ?><br />

			Holiday Date:<br />
			<input type="text" name="holiday" id="holiday" size="50" class="form" value="<?php echo set_value('holiday'); ?>" /><?php echo form_error('email'); ?><br /><br />

			<input type="submit" value="Add" name="register" />
			<?php
				print anchor('admin/holidays/manage','Cancel');
			?>
			</form>
	</div>
</div>