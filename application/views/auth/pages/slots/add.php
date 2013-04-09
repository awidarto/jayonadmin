<script>
	$(document).ready(function() {

		$( '#district' ).autocomplete({
			source: '<?php print site_url('ajax/getzone')?>',
			method: 'post',
			minLength: 2
		});

		$( '#city' ).autocomplete({
			source: '<?php print site_url('ajax/getcities')?>',
			method: 'post',
			minLength: 2
		});

		$( '#province' ).autocomplete({
			source: '<?php print site_url('ajax/getprovinces')?>',
			method: 'post',
			minLength: 2
		});

		$( '#country' ).autocomplete({
			source: '<?php print site_url('ajax/getcountries')?>',
			method: 'post',
			minLength: 2
		});

	});
	
	
</script>

<div id="form">
	<div class="form_box">
			<form method="post" action="<?php echo site_url('admin/slots/add')?>">

			Sequence:<br />
			<input type="text" name="seq" id="seq" size="50" class="form" value="<?php echo set_value('seq'); ?>" /><?php echo form_error('sequence'); ?><br /><br />

			Time From:<br />
			<input type="text" name="time_from" id="time_from" size="50" class="form" value="<?php echo set_value('time_from'); ?>" /><br /><?php echo form_error('time_from'); ?><br />

			Time To:<br />
			<input type="text" name="time_to" id="time_to" size="50" class="form" value="<?php echo set_value('time_to'); ?>" /><?php echo form_error('time_to'); ?><br /><br />

			Slot No:<br />
			<input type="text" name="slot_no" id="slot_no" size="50" class="form" value="<?php echo set_value('slot_no'); ?>" /><?php echo form_error('slot_no'); ?><br /><br />

			<input type="submit" value="Add" name="register" />
			<?php
				print anchor('admin/slots/manage','Cancel');
			?>
			</form>
	</div>
</div>