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

		$('.onswitch_link').click(function(){
			alert($(this).html());
		});

		$('table.dataTable').click(function(e){

			alert('click');


			if ($(e.target).is('.onswitch_link')) {				
				var dev_id = e.target.id;
				var currentsw = $('#' + dev_id).html();

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
							oTable.fnDraw();
						}
					},'json');
				}else{
					alert("Switch cancelled");
				}
		   	}

		});


	});
</script>

<div class="button_nav">
	<?php echo anchor('admin/devices/add','Add New Device','class="button add"')?>
</div>
<?php echo $this->table->generate(); ?>