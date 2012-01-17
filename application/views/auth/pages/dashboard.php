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
<style>
.stat_box{
	height:140px;
	width:100%;
	border:thin solid #ccc;
	margin-bottom:10px;
}

td {
	vertical-align:top;
}
</style>


<div id="tracker" >
	<table style="padding:0px;margin:0px;">
		<tr>
			<td>
				<h3>Last 5 Positions</h3>
				<div id="map" style="width:600px;height:600px;display:block;"></div>
			</td>
			<td style="width:100%;height:100%;vertical-align:top;">
				<h3>Statistics</h3>
				<div id="statistics"  style="width:100%;height:100%;">
					<div id="incoming_monthly" class="stat_box"></div>
					<div id="delivered_monthly" class="stat_box"></div>
					<div id="rescheduled_monthly" class="stat_box"></div>
					<div id="revoked_monthly" class="stat_box"></div>
				</div>
			</td>
		</tr>
	</table>
</div>