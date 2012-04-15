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

		var data = <?php print $all;?>;
		var selector = '#incoming_monthly';
		creategraph(data,selector);

		var data = <?php print $delivered;?>;
		var selector = '#delivered_monthly';
		creategraph(data,selector);

		var data = <?php print $rescheduled;?>;
		var selector = '#rescheduled_monthly';
		creategraph(data,selector);

		var data = <?php print $revoked;?>;
		var selector = '#revoked_monthly';
		creategraph(data,selector);

		var data = <?php print $noshow;?>;
		var selector = '#noshow_monthly';
		creategraph(data,selector);

		//var data = [ { x: -1893456000, y: 92228531 }, { x: -1577923200, y: 106021568 }, { x: -1262304000, y: 123202660 }, { x: -946771200, y: 132165129 }, { x: -631152000, y: 151325798 }, { x: -315619200, y: 179323175 }, { x: 0, y: 203211926 }, { x: 315532800, y: 226545805 }, { x: 631152000, y: 248709873 }, { x: 946684800, y: 281421906 }, { x: 1262304000, y: 308745538 } ];
		function creategraph(data,selector){

			var data = data;
			var selector = selector;

			var graph = new Rickshaw.Graph( {
			        element: document.querySelector(selector),
			        renderer:'bar',
			        series: [ {
			                color: 'steelblue',
			                data: data
			        } ]
			} );

			var axes = new Rickshaw.Graph.Axis.Time( { graph: graph } );

			var hoverDetail = new Rickshaw.Graph.HoverDetail( {
				graph: graph,
			    xFormatter: function(x) { return x },
			    yFormatter: function(y) { return y + " transactions" }
			} );

			graph.render();

	/*
			var legend = new Rickshaw.Graph.Legend( {
				graph: graph,
				element: document.getElementById('legend')

			} );

			var shelving = new Rickshaw.Graph.Behavior.Series.Toggle( {
				graph: graph,
				legend: legend
			} );

			var axes = new Rickshaw.Graph.Axis.Time( {
				graph: graph
			} );
			axes.render();
	*/	    


		}

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
					<span>Incoming Monthly</span>
					<div id="incoming_monthly" class="stat_box"></div>
					<span>Delivered Monthly</span>
					<div id="delivered_monthly" class="stat_box"></div>
					<span>Rescheduled Monthly</span>
					<div id="rescheduled_monthly" class="stat_box"></div>
					<span>Revoked Monthly</span>
					<div id="revoked_monthly" class="stat_box"></div>
					<span>No Show Monthly</span>
					<div id="noshow_monthly" class="stat_box"></div>
				</div>
			</td>
		</tr>
	</table>
</div>