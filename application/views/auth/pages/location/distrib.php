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
    //var locdata = <?php //print $locdata;?>;

    CM_ATTRIB = 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
            '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
            'Imagery © <a href="http://cloudmade.com">CloudMade</a>';

    CM_URL = 'http://{s}.tile.cloudmade.com/bc43265d42be42e3bfd603f12a8bf0e9/997/256/{z}/{x}/{y}.png';

    OSM_URL = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    OSM_ATTRIB = '&copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap</a> contributors';

    $(document).ready(function() {

        var map = L.map('map').setView([-6.17742,106.828308], 12);

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

        $('#lineWeight').on('change',function(){
            refreshMap();
        });

        var lg;
        var icsize = new L.Point(19,47);
        var icanchor = new L.Point(9,20);
        var shanchor = new L.Point(4,5);
        /*
        $('#map').gmap3({
            action:'init',
            options:{
                  center:[-6.17742,106.828308],
                  zoom: 11
                }
        });
        */

        var clusters = L.markerClusterGroup({
            spiderfyOnMaxZoom: true
        });

        var markers = [];
        var paths = [];

        function refreshMap(){
            var currtime = new Date();
            lineWeight = $('#lineWeight').val();
            //console.log(currtime.getTime());

            var icon_blue = L.AwesomeMarkers.icon({
                icon: 'icon-gift',
                color: 'blue',
                iconSize: icsize,
                iconAnchor: icanchor,
                shadowAnchor: shanchor
            });
            var icon_green = L.AwesomeMarkers.icon({
                icon: 'icon-location-arrow',
                color: 'green',
                iconSize: icsize,
                iconAnchor: icanchor,
                shadowAnchor: shanchor
            });
            var icon_red = L.AwesomeMarkers.icon({
                icon: 'icon-exchange',
                color: 'red',
                iconSize: icsize,
                iconAnchor: icanchor,
                shadowAnchor: shanchor
            });

            $.post('<?php print site_url('ajaxpos/getdistmarker');?>/' + currtime.getTime() ,
                {
                    'device_identifier':$('#search_device').val(),
                    'timefrom':$('#search_deliverytime_from').val(),
                    'timeto':$('#search_deliverytime_to').val(),
                    'courier':$('#search_courier').val(),
                    'status':$('#search_status').val()
                },

                function(data) {
                    if(data.result == 'ok'){


                        if(paths.length > 0){

                            for(m = 0; m < paths.length; m++){
                                map.removeLayer(paths[m]);
                            }

                            paths = [];

                        }

                        if(markers.length > 0){

                            map.removeLayer(clusters);
                            /*
                            for(m = 0; m < markers.length; m++){
                                map.removeLayer(markers[m]);
                            }
                            */

                            markers = [];

                        }

                        /*
                        $.each(data.paths, function(){
                            var polyline = L.polyline( this.poly,
                                {
                                    color: this.color,
                                    weight: lineWeight
                                } ).addTo(map);

                            paths.push(polyline);
                        });
                        */

                        $.each(data.locations,function(){

                            if(this.data.status == 'loc_update'){
                                icon = icon_green;
                            }else if(this.data.status == 'delivered'){
                                icon = icon_yellow;
                            }else{
                                icon =  icon_green;
                            }

                            icon =  icon_blue;

                            var content = '<div style="background-color:white;padding:3px;width:150px;">' +
                                '<div class="bg"></div>' +
                                '<div class="text">' + this.data.identifier + '<br />' + this.data.timestamp + '<br />' + this.data.address + '<br />Direction : ' + this.data.directions + '<br />Note : ' + this.data.note + '</div>' +
                            '</div>';

                            var m = L.marker(new L.LatLng( this.data.lat, this.data.lng ), { icon: icon }).addTo(clusters).bindPopup(content);

                            markers.push(m);

                            /*
                            if($('#showLocUpdate').is(':checked')){

                            }else{
                                if(this.data.status != 'loc_update'){
                                    var m = L.marker(new L.LatLng( this.data.lat, this.data.lng ), { icon: icon }).addTo(map).bindPopup(content);
                                    markers.push(m);
                                }
                            }
                            */

                        });

                        clusters.addTo(map);
                    }
                },'json');

        }

        //Refresh page every x second ( in milis)
        function refresh(){
            refreshMap();
            setTimeout(refresh, <?php print get_option('map_refresh_rate');?> * 1000);
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


        $('#search_deliverytime_from').datepicker({ dateFormat: 'yy-mm-dd' });
        $('#search_deliverytime_to').datepicker({ dateFormat: 'yy-mm-dd' });

        $('#search_deliverytime_from').change(function(){
            refreshMap();
        });

        $('#search_deliverytime_to').change(function(){
            refreshMap();
        });

        $('#search_device').change(function(){
            refreshMap();
        });

        $('#showLocUpdate').click(function(){
            refreshMap();
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

    <div>
        <!--
        <input type="checkbox" checked="checked" id="showLocUpdate" value="1" /> Show Periodic Update Point |
        Track Line Weight <select name="line" id="lineWeight">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4" selected >4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
        </select>
        -->
        <label for="">From</label><input id="search_deliverytime_from" class="date">
        <label for="">To</label><input id="search_deliverytime_to" class="date">
    </div>
    <div id="tracker" >
        <table style="padding:0px;margin:0px;">
            <tr>
                <td style="vertical-align: top;">
                    <h3>Position Tracker</h3>
                    <div id="map" style="width:100%;height:700px;display:block;border:thin solid grey;"></div>
                </td>
            </tr>
        </table>
    </div>
