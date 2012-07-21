<script>

	$(document).ready(function() {
        $('.date').datepicker({
            numberOfMonths: 2,
            showButtonPanel: true,
            dateFormat:'yy-mm-dd'});
	});

</script>

<div id="form">
	<div class="form_box">
			<form method="post" action="<?php echo site_url('admin/tariff/addcod/'.$app_id)?>">

			Sequence:<br />
			<input type="text" name="seq" size="50" class="form" value="<?php echo set_value('seq'); ?>" /><?php echo form_error('seq'); ?><br /><br />

			Price From:<br />
			<input type="text" name="from_price" size="50" class="form" value="<?php echo set_value('from_price'); ?>" /><?php echo form_error('from_price'); ?><br /><br />

			Price To:<br />
			<input type="text" name="to_price" size="50" class="form" value="<?php echo set_value('to_price'); ?>" /><?php echo form_error('from_price'); ?><br /><br />

			Surcharge:<br />
			<input type="text" name="surcharge" size="50" class="form" value="<?php echo set_value('surcharge'); ?>" /><?php echo form_error('surcharge'); ?><br /><br />

			Period From:<br />
			<input type="text" name="period_from" size="50" class="form date" value="<?php echo set_value('period_from'); ?>" /><?php echo form_error('period_from'); ?><br /><br />

			Period To:<br />
			<input type="text" name="period_to" size="50" class="form date" value="<?php echo set_value('period_to'); ?>" /><?php echo form_error('period_to'); ?><br /><br />

			<input type="submit" value="Add" name="addcod" />
			<?php
				print anchor('admin/tariff/cod/'.$app_id,'Cancel');
			?>
			</form>
	</div>
</div>
