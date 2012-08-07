<script>

	$(document).ready(function() {
        $('.date').datepicker({
            numberOfMonths: 2,
            showButtonPanel: true,
            dateFormat:'yy-mm'});
	});

</script>

<div id="form">
	<div class="form_box">
			<form method="post" action="<?php echo site_url('admin/prepaid/addcredit/'.$app_id)?>">

			Credit Value:<br />
			<input type="text" name="credit" size="50" class="form" value="<?php echo set_value('credit'); ?>" /><?php echo form_error('credit'); ?><br /><br />

			Period:<br />
			<input type="text" name="period" size="50" class="form date" value="<?php echo set_value('period'); ?>" /><?php echo form_error('period'); ?><br /><br />

			<input type="submit" value="Add" name="addcod" />
			<?php
				print anchor('admin/prepaid/addcredit/'.$app_id,'Cancel');
			?>
			</form>
	</div>
</div>
