<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
<?php echo $this->ag_asset->load_script('gmap3.min.js');?>

<script>

	$(document).ready(function() {
		
		$('#map').gmap3({
			action:'init',
			options:{
			      center:[-6.17742,106.828308],
			      zoom: 12
			    },
			events:{
				click:function(marker, event, data){

					var latlon = event.latLng.toString().replace('(','').replace(')','').replace(' ','').split(',');

					$('#lat').val(latlon[0]);
					$('#lon').val(latlon[1]);

				} 	
			}
			
		});

		$('#location').click(function(){
			$.post('<?php print site_url('mobile/device/ajaxlocation');?>',{
				'lat':$('#lat').val(),
				'lon':$('#lon').val()
			}, function(data) {
				alert(data.status);
			},'json');
		});
	        
	});

</script>

<div id="map" style="width:600px;height:600px;display:block;"></div>

<?php
	print form_input('lat','latitude','id="lat"');
	print form_input('lon','longitude','id="lon"');
	print form_button('location','report loc','id="location"');
?>
