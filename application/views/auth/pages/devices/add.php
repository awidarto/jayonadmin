<script>
$(document).ready(function() {
	$( '#city' ).autocomplete({
		source: '<?php print site_url('ajax/getcities')?>',
		method: 'post',
		minLength: 2
	});
	
	function split( val ) {
		return val.split( /,\s*/ );
	}
	function extractLast( term ) {
		return split( term ).pop();
	}
	
	// don't navigate away from the field on tab when selecting an item
	$( "#zones" ).bind( "keydown", function( event ) {
					if ( event.keyCode === $.ui.keyCode.TAB &&
							$( this ).data( "autocomplete" ).menu.active ) {
						event.preventDefault();
					}
				})
				.autocomplete({
					source: function( request, response ) {
						$.getJSON( "<?php print site_url('ajax/getzone')?>", {
							term: extractLast( request.term )
						}, response );
					},
					search: function() {
						// custom minLength
						var term = extractLast( this.value );
						if ( term.length < 2 ) {
							return false;
						}
					},
					focus: function() {
						// prevent value inserted on focus
						return false;
					},
					select: function( event, ui ) {
						var terms = split( this.value );
						// remove the current input
						terms.pop();
						// add the selected item
						terms.push( ui.item.value );
						// add placeholder to get the comma-and-space at the end
						terms.push( "" );
						this.value = terms.join( ", " );
						return false;
					}
				});
	
	
});
</script>
<div id="form">
	<div class="form_box">
			<form method="post" action="<?php echo site_url('admin/devices/add')?>">

			Device Identifier / Code:<br />
			<input type="text" name="identifier" size="50" class="form" value="<?php echo set_value('identifier'); ?>" /><?php echo form_error('identifier'); ?><br /><br />

			Device Name:<br />
			<input type="text" name="devname" size="50" class="form" value="<?php echo set_value('devname'); ?>" /><?php echo form_error('devname'); ?><br /><br />

			Mobile Number:<br />
			<input type="text" name="mobile" size="50" class="form" value="<?php echo set_value('mobile'); ?>" /><?php echo form_error('mobile'); ?><br /><br />

			Zones:<br />
			<textarea name="district" id="zones" cols="60" rows="4"><?php echo set_value('district'); ?></textarea><?php echo form_error('district'); ?><br />

			City:<br />
			<input type="text" name="city" id="city" size="50" class="form" value="<?php echo set_value('city'); ?>" /><?php echo form_error('city'); ?><br /><br />

			Device Description:<br />
			<textarea name="descriptor" cols="60" rows="10"><?php echo set_value('descriptor'); ?></textarea><br />
				
			<input type="submit" value="Add" name="register" />
			<?php
				print anchor('admin/devices/manage','Cancel');
			?>
			</form>
	</div>
</div>