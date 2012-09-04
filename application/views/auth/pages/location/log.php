<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
<?php echo $this->ag_asset->load_script('gmap3.min.js');?>

<script>
	var asInitVals = new Array();
	//var locdata = <?php //print $locdata;?>;
	
	$(document).ready(function() {
		$('#map').gmap3({
			action:'init',
			options:{
			      center:[-6.17742,106.828308],
			      zoom: 11
			    }
		});

		function refreshMap(){

			$.post('<?php print site_url('ajax/getmapmarker');?>',null, 
				function(data) {
					if(data.result == 'ok'){
						$('#map').gmap3({
							action:'clear'
						});

						$.each(data.paths,function(){
							$('#map').gmap3({
								action:'addPolyline',
								options:{
									strokeColor: this.color,
									strokeOpacity: 1.0,
									strokeWeight: 2
								},
								path: this.poly
							});

						});

						$.each(data.locations,function(){

							$('#map').gmap3({
								action:'addMarker',
								latLng:[this.data.lat, this.data.lng],
								marker: {
									options: {
										//icon: new google.maps.MarkerImage('http://maps.gstatic.com/mapfiles/icon_green.png')
									},
									data:{identifier:this.data.identifier,timestamp:this.data.timestamp},
									events:{
										mouseover: function(marker,event,data){
											console.log(data);
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
							});

						});
					}
				},'json');

		}







	    /*
		$('#map').gmap3({
			action:'init',
			options:{
			      center:[-6.17742,106.828308],
			      zoom: 11
			    }
			},
			<?php //print $pathcmd;?>
			,
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
			*/
		
	    var oTable = $('.dataTable').dataTable(
			{
				"bProcessing": true,
		        "bServerSide": true,
		        "sAjaxSource": "<?php print site_url($ajaxurl);?>",
				"oLanguage": { "sSearch": "Search "},
			<?php if($this->config->item('infinite_scroll')):?>
				"bScrollInfinite": true,
			    "bScrollCollapse": true,
			    "sScrollY": "500px",
			<?php endif; ?>
			<?php if(isset($sortdisable)):?>
				"aoColumnDefs": [ 
				            { "bSortable": false, "aTargets": [ <?php print $sortdisable; ?> ] }
				 ],
			<?php endif;?>
			    "fnServerData": function ( sSource, aoData, fnCallback ) {
		            $.ajax( {
		                "dataType": 'json', 
		                "type": "POST", 
		                "url": sSource, 
		                "data": aoData, 
		                "success": fnCallback
		            } );
		        }
			}
		);

		$('tfoot input').keyup( function () {
			/* Filter on the column (the index) of this element */
			oTable.fnFilter( this.value, $('tfoot input').index(this) );
		} );

		/*
		 * Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
		 * the footer
		 */
		$('tfoot input').each( function (i) {
			asInitVals[i] = this.value;
		} );

		$('tfoot input').focus( function () {
			if ( this.className == 'search_init' )
			{
				this.className = '';
				this.value = '';
			}
		} );

		$('tfoot input').blur( function (i) {
			if ( this.value == '' )
			{
				this.className = 'search_init';
				this.value = asInitVals[$('tfoot input').index(this)];
			}
		} );

		//Refresh page every x second ( in milis)
		function refresh(){
			refreshMap();
			oTable.fnDraw();
			setTimeout(refresh, 60000);
		}

		refresh();		



		$( '#assign_courier' ).autocomplete({
			source: '<?php print site_url('ajax/getcourier')?>',
			method: 'post',
			minLength: 2,
			select:function(event,ui){
				$('#assign_courier_id').val(ui.item.id);
				$('#assign_courier_id_txt').html(ui.item.id);
				
			}
		});

		$( '#device_id' ).autocomplete({
			source: '<?php print site_url('ajax/getdevice')?>',
			method: 'post',
			minLength: 2
		});

		
		$('#search_deliverytime').datepicker({ dateFormat: 'yy-mm-dd' });
		
		$('#search_deliverytime').change(function(){
			oTable.fnFilter( this.value, $('tfoot input').index(this) );
		});

		/*Delivery process mandatory*/
		$('#search_deliverytime').datepicker({ dateFormat: 'yy-mm-dd' });
		$('#assign_deliverytime').datepicker({ dateFormat: 'yy-mm-dd' });
		
		$('#doDispatch').click(function(){
			if($('.device_id:checked').val() == undefined || $(".assign_date:checked").val() == undefined ){
				alert('Please specify Date AND Device.');
			}else{
				var device_id = $('.device_id:checked').val();
				var device_name = $('.device_id:checked').attr('title');
				var assignment_date = $(".assign_date:checked").val();

				$('#assign_device').val(device_id);
				$('#assign_date').val(assignment_date);

				$('#delivery_date').html(assignment_date);
				$('#device_identifier').html(device_name);

				$('#assign_dialog').dialog('open');
			}
		});
		
		$('#assign_dialog').dialog({
			autoOpen: false,
			height: 200,
			width: 300,
			modal: true,
			buttons: {
				"Dispatch Device": function() {
					if($("#assign_date").val() == ''){
						alert('Please specify Courier.')
					}else{
						var device_id = $("#assign_device").val();
						var courier_id = $("#assign_courier_id").val();
						var assignment_date = $("#assign_date").val();
						//alert(device_id);
						$.post('<?php print site_url('admin/delivery/ajaxdispatch');?>',{ assignment_device_id: device_id,assignment_courier_id: courier_id,assignment_date: assignment_date }, function(data) {
							if(data.result == 'ok'){
								//redraw table
								oTable.fnDraw();
								$('#assign_dialog').dialog( "close" );
							}
						},'json');
					}
				},
				Cancel: function() {
					$('#assign_courier').val('');
					$('#assign_courier_id_txt').html('');
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				//allFields.val( "" ).removeClass( "ui-state-error" );
				$('#assign_courier').val('');
				$('#assign_courier_id_txt').html('');
				$('#assign_deliverytime').val('');
			}
		});
		
	} );
	
	
</script>


	<div id="tracker" >
		<table style="padding:0px;margin:0px;">
			<tr>
				<td style="vertical-align: top;">
					<h3>Position Tracker</h3>
					<div id="map" style="width:800px;height:700px;display:block;border:thin solid grey;"></div>
				</td>
				<td style="width:100%;height:100%;vertical-align:top;">
					<h3>Position Logs</h3>
					<?php echo $this->table->generate(); ?>
				</td>
			</tr>
		</table>
	</div>
