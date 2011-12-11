<script>

	$(document).ready(function() {
		
		$('#map').gmap3({
			action:'init',
			options:{
			      center:[-6.17742,106.828308],
			      zoom: 12
			    }
		});
	        
	});

</script>


	<div id="tracker" >
		<table style="padding:0px;margin:0px;">
			<tr>
				<td>
					<h3>Position Tracker</h3>
					<div id="map" style="width:525px;height:400px;display:block;border:thin solid grey;"></div>
				</td>
				<td style="width:100%;height:100%;vertical-align:top;">
					<h3>Position Logs</h3>
					<?php echo $this->table->generate(); ?>
				</td>
			</tr>
		</table>
	</div>
