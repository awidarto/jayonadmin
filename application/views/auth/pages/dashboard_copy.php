<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjAbrYRF0v4nd5DeWPhbVObRwF_cbcE1A&sensor=false">
</script>
<?php echo $this->ag_asset->load_script('gmap3.min.js');?>

<script>

	var locdata = <?php print $locdata;?>;

	$(document).ready(function() {

		$('#map').gmap3(
			{ action:'init',
				options:{
					center:[-6.17742,106.828308],
					zoom: 11
				}
			},
			{ action:'addMarkers',
				radius:100,
				markers: locdata,
				marker: {
					options: {
						//icon: new google.maps.MarkerImage('http://maps.gstatic.com/mapfiles/icon_green.png')
					},
					events:{
						mouseover: function(marker,event,data){
							$(this).gmap3(
								{action:'clear',name:'overlay'},
								{action:'addOverlay',
									latLng:marker.getPosition(),
									content:
										'<div style="background-color:white;padding:3px;border:thin solid #aaa;width:150px;">' +
											'<div class="bg"></div>' +
											'<div class="text">' + data.identifier + '<br />' + data.timestamp + '</div>' +
										'</div>',
									offset: {
										x:-46,
										y:-73
									}
								}
							);
						},
						mouseout: function(){
							$(this).gmap3({action:'clear', name:'overlay'});
						}
					}
				}
			}
		);


	});

</script>
<style>
.stat_box{
	border:0px solid #ccc;
	margin-bottom:10px;
	display: block;
}

td {
	vertical-align:top;
}
</style>


<div id="tracker" >
	<table style="padding:0px;margin:0px;">
		<tr>
			<td>
				<h3>Device Last Positions</h3>
				<div id="map" style="width:600px;height:950px;display:block;"></div>
			</td>
			<td style="width:100%;height:100%;vertical-align:top;">
				<h3>Statistics</h3>
				<div id="statistics"  style="width:100%;height:100%;">
					<span>Total Incoming <?php print $period;?></span>
					<div id="incoming_monthly" class="stat_box">
						<img src="<?php print base_url();?>admin/graphs/monthlygraph/all/half" alt="monthly_all" />
					</div>
					<span>Delivered <?php print $period;?></span>
					<div id="delivered_monthly" class="stat_box">
						<img src="<?php print base_url();?>admin/graphs/monthlygraph/delivered/half" alt="monthly_all" />
					</div>
					<span>Rescheduled <?php print $period;?></span>
					<div id="rescheduled_monthly" class="stat_box">
						<img src="<?php print base_url();?>admin/graphs/monthlygraph/rescheduled/half" alt="monthly_all" />
					</div>
					<span>Revoked <?php print $period;?></span>
					<div id="revoked_monthly" class="stat_box">
						<img src="<?php print base_url();?>admin/graphs/monthlygraph/revoked/half" alt="monthly_all" />
					</div>
					<span>No Show <?php print $period;?></span>
					<div id="noshow_monthly" class="stat_box">
						<img src="<?php print base_url();?>admin/graphs/monthlygraph/noshow/half" alt="monthly_all" />
					</div>
					<span>Archived <?php print $period;?></span>
					<div id="noshow_monthly" class="stat_box">
						<img src="<?php print base_url();?>admin/graphs/monthlygraph/archived/half" alt="monthly_all" />
					</div>
				</div>
			</td>
		</tr>
	</table>
</div>