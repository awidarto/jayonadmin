<style>
	.red_switch{
		background-color: red;
		padding:2px;
		color:white;
		text-decoration: none;
	}

</style>
<script>
	$(document).ready(function() {

		$('table.dataTable').click(function(e){

			if ($(e.target).is('.onswitch_link')) {				
				var dev_id = e.target.id;
				var currentsw = $('#' + dev_id).html();

				var nextsw = '';

				if(currentsw == 'On'){
					nextsw = 'Off';
				}else{
					nextsw = 'On';
				}

				var answer = confirm("Switch this device " + nextsw + " ?");
				if (answer){
					$.post('<?php print site_url('admin/devices/ajaxtoggle');?>',{'id':dev_id,'switchto':nextsw}, function(data) {
						if(data.result == 'ok'){
							//redraw table
							$('#' + dev_id).html(data.state);
							if(data.state == 'Off'){
								$('#' + dev_id).removeClass('red_switch').addClass('red_switch');
							}else{
								$('#' + dev_id).removeClass('red_switch');
							}

							//oTable.fnDraw();
						}
					},'json');
				}else{
					alert("Switch cancelled");
				}
		   	}

		});


	});
</script>
<?php if(isset($add_button)):?>
	<div class="button_nav">
		<?php echo anchor($add_button['link'],$add_button['label'],'class="button add"')?>
	</div>
<?php endif;?>
<?php echo $this->table->generate(); ?>