<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
<?php echo $this->ag_asset->load_script('gmap3.min.js');?>

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
				<h3>Last 5 Positions</h3>
				<div id="map" style="width:600px;height:950px;display:block;"></div>
			</td>
			<td style="width:100%;height:100%;vertical-align:top;">
				<h3>Statistics</h3>
				<div id="statistics"  style="width:100%;height:100%;">
					<span>Total Incoming <?php print $period;?></span>
					<div id="incoming_monthly" class="stat_box">
						<img src="<?php print base_url();?>admin/admin/monthlygraph" alt="monthly_all" />
					</div>
					<span>Delivered <?php print $period;?></span>
					<div id="delivered_monthly" class="stat_box">
						<img src="<?php print base_url();?>admin/admin/monthlygraph/delivered" alt="monthly_all" />
					</div>
					<span>Rescheduled <?php print $period;?></span>
					<div id="rescheduled_monthly" class="stat_box">
						<img src="<?php print base_url();?>admin/admin/monthlygraph/rescheduled" alt="monthly_all" />
					</div>
					<span>Revoked <?php print $period;?></span>
					<div id="revoked_monthly" class="stat_box">
						<img src="<?php print base_url();?>admin/admin/monthlygraph/revoked" alt="monthly_all" />
					</div>
					<span>No Show <?php print $period;?></span>
					<div id="noshow_monthly" class="stat_box">
						<img src="<?php print base_url();?>admin/admin/monthlygraph/noshow" alt="monthly_all" />
					</div>
					<span>Archived <?php print $period;?></span>
					<div id="noshow_monthly" class="stat_box">
						<img src="<?php print base_url();?>admin/admin/monthlygraph/archived" alt="monthly_all" />
					</div>
				</div>
			</td>
		</tr>
	</table>
</div>