<script>
	$(document).ready(function() {
		$('#holiday').datepicker({ dateFormat: 'yy-mm-dd' });

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
			<form method="post" action="<?php echo site_url('admin/zones/add')?>">
			District:<br />
			<input type="text" name="district" id="district" size="50" class="form" value="<?php echo set_value('district'); ?>" /><br /><?php echo form_error('district'); ?><br />

			City:<br />
			<input type="text" name="city" id="city" size="50" class="form" value="<?php echo set_value('city'); ?>" /><?php echo form_error('city'); ?><br /><br />

			Province:<br />
			<input type="text" name="province" id="province" size="50" class="form" value="<?php echo set_value('province'); ?>" /><?php echo form_error('province'); ?><br /><br />

			Country:<br />
			<input type="text" name="country" id="country" size="50" class="form" value="<?php echo set_value('country'); ?>" /><?php echo form_error('country'); ?><br /><br />

            ZIP :<br />
            <input type="text" name="zips" id="zips" size="50" class="form" value="<?php echo set_value('zips'); ?>" /><?php echo form_error('country'); ?><br /><br />

			<input type="submit" value="Add" name="register" />
			<?php
				print anchor('admin/zones/manage','Cancel');
			?>
			</form>
	</div>
</div>