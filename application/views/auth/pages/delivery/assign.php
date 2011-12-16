<script>
	$(document).ready(function() {
		$( '#assignment_date' ).datepicker({
			dateFormat:'yy-mm-dd'
		});
	} );

</script>
<div id="form">
	<div class="form_box">
			<form method="post" action="<?php echo site_url('admin/delivery/assign/'.$delivery_id)?>">
			Delivery ID: <strong><?php echo $delivery_id;?></strong><br /><br />
			Device:<br />
				<?php echo form_dropdown('device_id',$devices,set_value('device_id'));?>
			<br />
			Delivery Date:<br />
			<input type="text" name="assignment_date" id="assignment_date" size="50" class="form" value="<?php echo set_value('assignment_date'); ?>" /><?php echo form_error('assignment_date'); ?><br /><br />

				<input type="submit" value="Assign" name="assign" />
				<?php
					print $back_url;
				?>
			</form>
	</div>
</div>