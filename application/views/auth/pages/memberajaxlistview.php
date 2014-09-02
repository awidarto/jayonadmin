<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.css" />
 <!--[if lte IE 8]>
     <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.ie.css" />
 <![endif]-->

<?php echo $this->ag_asset->load_css('font-awesome.min.css');?>
<?php echo $this->ag_asset->load_css('leaflet.awesome-markers.css');?>
<?php echo $this->ag_asset->load_css('MarkerCluster.css');?>
<?php echo $this->ag_asset->load_css('MarkerCluster.Default.css');?>
<!--[if lte IE 8]>
    <?php echo $this->ag_asset->load_css('MarkerCluster.Default.ie.css');?>
<![endif]-->

<?php echo $this->ag_asset->load_css('l.geosearch.css');?>

<script src="http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.js"></script>

<?php echo $this->ag_asset->load_script('leaflet.awesome-markers.min.js');?>
<?php echo $this->ag_asset->load_script('leaflet.polylineDecorator.min.js');?>
<?php echo $this->ag_asset->load_script('leaflet.markercluster.js');?>

<?php echo $this->ag_asset->load_script('lsearch/l.control.geosearch.js');?>
<?php echo $this->ag_asset->load_script('lsearch/l.geosearch.provider.openstreetmap.js');?>
<?php echo $this->ag_asset->load_script('lsearch/l.geosearch.provider.google.js');?>


<style type="text/css">
.awesome-marker i {
    color: #333;
    margin-top: 2px;
    display: inline-block;
    font-size: 10px;
}
</style>

<script>
	var asInitVals = new Array();
    var refreshTab;

    CM_ATTRIB = 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
            '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
            'Imagery © <a href="http://cloudmade.com">CloudMade</a>';

    CM_URL = 'http://{s}.tile.cloudmade.com/bc43265d42be42e3bfd603f12a8bf0e9/997/256/{z}/{x}/{y}.png';

    OSM_URL = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    OSM_ATTRIB = '&copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap</a> contributors';

    function setupMap(){
        var map = L.map('map').setView([-6.17742,106.828308], 10);

        var lineWeight = 4;

        L.tileLayer(OSM_URL, {
            attribution: OSM_ATTRIB,
            maxZoom: 18
        }).addTo(map);

        new L.Control.GeoSearch({
            provider: new L.GeoSearch.Provider.Google(),
            position: 'topcenter',
            showMarker: true
        }).addTo(map);
    }


	$(document).ready(function() {

	    var oTable = $('.dataTable').dataTable(
			{
				"bProcessing": true,
		        "bServerSide": true,
		        "sAjaxSource": "<?php print site_url($ajaxurl);?>",
				"oLanguage": { "sSearch": "Search "},
				"sPaginationType": "full_numbers",
				"sDom": 'T<"clear">lfrtip',
				"oTableTools": {
					"sSwfPath": "<?php print base_url();?>assets/swf/copy_csv_xls_pdf.swf"
				},
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

		$('table.dataTable').click(function(e){

            console.log(e);

            if ($(e.target).is('.locpick')) {
                var buyer_id = e.target.id;
                $('#setloc_dialog').dialog('open');

                var src = '<?php print base_url() ?>admin/prints/mapview/buyer/' + buyer_id;

                $('#map_frame').attr('src',src);
                $('#setloc_dialog').dialog('open');
            }


			if ($(e.target).is('.cancel_link')) {
				var delivery_id = e.target.id;
				var answer = confirm("Are you sure you want to archive this order ?");
				if (answer){
					$.post('<?php print site_url('admin/delivery/ajaxarchive');?>',{'delivery_id':delivery_id}, function(data) {
						if(data.result == 'ok'){
							//redraw table
							oTable.fnDraw();
							alert(delivery_id + " archived");
						}
					},'json');
				}else{
					alert(delivery_id + " not archived");
				}
		   	}

			if ($(e.target).is('.delete_link')) {
				var user_id = e.target.id;
				var answer = confirm("Are you sure you want to delete this user ?");
				if (answer){
					$.post('<?php print site_url('admin/members/ajaxdelete');?>',{'id':user_id}, function(data) {
						if(data.result == 'ok'){
							//redraw table
							oTable.fnDraw();
							alert(user_id + " deleted");
						}
					},'json');
				}else{
					alert(user_id + " not deleted");
				}
		   	}

		});

        refreshTab = function(){
            oTable.fnDraw();
        };

		$('#search_timestamp').datepicker({ dateFormat: 'yy-mm-dd' });

		$('#search_timestamp').change(function(){
			oTable.fnFilter( this.value, $('tfoot input').index(this) );
		});


		$('#setgroup').click(function(){
			var parent = '';
            parent = $('input:radio[name=parent_check]:checked').val();
			var count = 0;
            var children = [];
			$('.child_select:checked').each(function(){
                children.push($(this).val());
				count++;
			});

            console.log(parent);
            console.log(children);


			if(count > 0 && typeof(parent) != 'undefined'){
                var answer = confirm("Grouped buyers will be merged into one record, are you sure ?");
                if (answer){
                    $.post('<?php print site_url('ajax/setbuyergroup');?>',{'parent':parent, 'children':children}, function(data) {
                        if(data.result == 'OK'){
                            //redraw table
                            oTable.fnDraw();
                            alert("Buyers succesfully grouped");
                        }
                    },'json');
                }else{
                    alert("Buyers not grouped");
                }
			}else{
				alert('Please select one or more user, and set one user to become parent record');
			}
		});

		$('#setgroup_dialog').dialog({
			autoOpen: false,
			height: 300,
			width: 500,
			modal: true,
			buttons: {
				"Set Member Group": function() {
					var user_ids = [];
					i = 0;
					$('.assign_check:checked').each(function(){
						user_ids[i] = $(this).val();
						i++;
					});
					$.post('<?php print site_url('admin/members/ajaxsetgroup');?>',
						{ set_group: $('#set_group').val(),'users[]':user_ids},
						function(data) {
						if(data.result == 'ok'){
							//redraw table
							oTable.fnDraw();
							$('#setgroup_dialog').dialog( "close" );
						}
					},'json');
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				//allFields.val( "" ).removeClass( "ui-state-error" );
				$('#archive_list').html('');
			}
		});

        $('#setloc_dialog').dialog({
            autoOpen: false,
            height: 600,
            width: 900,
            modal: true,
            buttons: {
                Save: function(){
                    var nframe = document.getElementById('map_frame');
                    var nframeWindow = nframe.contentWindow;
                    nframeWindow.submitlocation();
                },
                Close: function() {
                    oTable.fnDraw();
                    $( this ).dialog( "close" );
                }
            },
            close: function() {

            }
        });

	});
</script>
<?php if(isset($add_button)):?>
	<div class="button_nav">
		<?php echo anchor($add_button['link'],$add_button['label'],'class="button add"')?>
	</div>
<?php endif;?>

<div class="button_nav" style="text-align:left;">
    <?php print form_checkbox('assign_all',1,FALSE,'id="assign_all"');?> Select All
    <?php if(isset($group_button) && $group_button == true):?>
        <span class="button" id="setgroup" style="cursor:pointer;" >Group Selected Buyers</span>
    <?php endif;?>
</div>
<?php echo $this->table->generate(); ?>

<?php
	$group_array = array(
		user_group_id('merchant')=>'merchant',
		user_group_id('buyer')=>'buyer'
		);
?>

<style type="text/css">
    * .locpick{
        display:block;
        cursor:pointer;
    }
</style>

<div id="setgroup_dialog" title="Set Member Group">
	<table style="width:100%;border:0;margin:0;">
		<tr>
			<td style="width:250px;vertical-align:top">
				Members :
			</td>
			<td>
				Set Group to :
			</td>
		</tr>
		<tr>
			<td style="overflow:auto;width:250px;vertical-align:top">
				<ul id="archive_list" style="border-top:thin solid grey;list-style-type:none;padding-left:0px;"></ul>
			</td>
			<td style="overflow:auto;width:250px;vertical-align:top">
				<?php print form_dropdown('set_groups',$group_array,'','id="set_group"');?>
			</td>
		</tr>
	</table>
</div>

<div id="setloc_dialog" title="Set Location" style="overflow:hidden;padding:8px;">
    <input type="hidden" value="" id="print_id" />
    <iframe id="map_frame" name="map_frame" width="100%" height="100%"
    marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto"
    title="Dialog Title">Your browser does not suppr</iframe>
</div>
