<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.css" />
 <!--[if lte IE 8]>
     <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.ie.css" />
 <![endif]-->

<?php echo $this->ag_asset->load_css('font-awesome.min.css');?>
<?php echo $this->ag_asset->load_css('leaflet.awesome-markers.css');?>

<script src="http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.js"></script>

<?php
/*
<script src="http://maps.google.com/maps/api/js?v=3.2&sensor=false"></script>
*/
?>

<?php echo $this->ag_asset->load_script('leaflet-google.js');?>
<?php echo $this->ag_asset->load_script('leaflet.awesome-markers.min.js');?>
<?php echo $this->ag_asset->load_script('leaflet.polylineDecorator.min.js');?>


<style type="text/css">
.awesome-marker i {
    color: #333;
    margin-top: 2px;
    display: inline-block;
    font-size: 10px;
}

.redborder{
    border:2px solid red;
    background-color: yellow;
}

/*google map tile tweak*/
.leaflet-google-layer{
    z-index: 0 !important;
}
.leaflet-map-pane{
    z-index: 100;
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
        /*
        var googleLayer = new L.Google('ROADMAP');
        map.addLayer(googleLayer);
        */

        L.tileLayer(OSM_URL, {
            attribution: OSM_ATTRIB,
            maxZoom: 18
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

        var markers = [];
        var paths = [];
        var routelineexists = false;

        map.on('click', function(e) {
            alert(e.latlng); // e is an event object (MouseEvent in this case)
        });

        function routeReset(){
            $('input#routeSeq').val(0);
        }

        $('#routeReset').on('click',function(){
            routeReset();
        });

        function supportsLocalStorage() {
            try {
                return 'localStorage' in window && window['localStorage'] !== null;
            } catch (e) {
                return false;
            }
        }

        function refreshMap(){
            var currtime = new Date();
            lineWeight = $('#lineWeight').val();
            //console.log(currtime.getTime());

            var icon_yellow = L.AwesomeMarkers.icon({
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

            $.post('<?php print site_url('ajaxpos/getroutemarker');?>/' + currtime.getTime() ,
                {
                    'device_identifier':$('#search_device').val(),
                    'timestamp':$('#search_deliverydate').val(),
                    'delivery_status':$('#search_status').val(),
                    'address':$('#search_address').val(),
                    'limit':$('select[name=DataTables_Table_0_length]').val()
                },

                function(data) {
                    if(data.result == 'ok'){

                        var path = data.paths;

                        console.log(path);

                        if(paths.length > 0){

                            for(m = 0; m < paths.length; m++){
                                map.removeLayer(paths[m]);
                            }

                            paths = [];

                        }


                        if(markers.length > 0){

                            for(m = 0; m < markers.length; m++){
                                map.removeLayer(markers[m]);
                            }

                            markers = [];

                        }

                        var polyline = L.polyline( path.poly,
                            {
                                color: 'blue',
                                weight: lineWeight
                            } ).addTo(map);

                        paths.push(polyline);


                        $.each(data.locations,function(){

                            if(this.data.status == 'loc_update'){
                                icon = icon_green;
                            }else if(this.data.status == 'delivered'){
                                icon = icon_yellow;
                            }else{
                                icon =  icon_red;
                            }

                            var content = '<div style="background-color:white;padding:3px;width:150px;">' +
                                '<div class="bg"></div>' +
                                '<div class="text">' + this.data.identifier + '<br />' + this.data.timestamp + '<br />' + this.data.address + '</div>' +
                            '</div>';

                            //if($('#showLocUpdate').is(':checked')){
                                var m = L.marker(new L.LatLng( this.data.lat, this.data.lng ), { icon: icon, id: this.data.id }).on('click',function(e){
                                    console.log(e.target.options.id);
                                    var inid = e.target.options.id;
                                    $('.inseq').removeClass('redborder');
                                    $('#' + inid).addClass('redborder');
                                    if($('#routeMode').is(':checked')){
                                        var sq = parseInt($('#routeSeq').val()) + 1;
                                        $('#routeSeq').val( sq );
                                        $('#' + inid).val($('#routeSeq').val());
                                    }else{
                                        var current = $('#' + inid).val();
                                        $('#routeSeq').val(current);
                                    }

                                }).addTo(map).bindPopup(content);
                                markers.push(m);
                            /*
                            }else{
                                if(this.data.status != 'loc_update'){
                                    var m = L.marker(new L.LatLng( this.data.lat, this.data.lng ), { icon: icon }).addTo(map).bindPopup(content);
                                    markers.push(m);
                                }
                            }
                            */

                        });

                    }
                },'json');

        }

        var oTable = $('.dataTable').dataTable(
            {
                "bProcessing": true,
                "bServerSide": true,
                "sAjaxSource": "<?php print site_url($ajaxurl);?>",
                "oLanguage": { "sSearch": "Search "},
                "iDisplayLength": 50,
                "sPaginationType": "full_numbers",
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
            refreshMap();
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

                var src = '<?php print base_url() ?>admin/prints/mapview/delivery/' + buyer_id;

                $('#map_frame').attr('src',src);
                $('#setloc_dialog').dialog('open');
            }

            if ($(e.target).is('.copyloc')) {

                var loc = e.target.id;

                if (!supportsLocalStorage()) {
                    alert('localStorage not supported, failed to copy');
                    return false;
                }else{
                    localStorage['locCopy'] = loc;
                    alert('Location data copied');
                }

            }

            if ($(e.target).is('.pasteloc')) {
                console.log(e.target);
                var id = e.target.id;
                var loc = localStorage['locCopy'];

                var locs = loc.split('_');

                console.log(locs);

                var lat = locs[0];
                var lon = locs[1];

                alert(id + ' <- ' + lat + ',' + lon);

                console.log(id + ' <- ' + lat + ',' + lon);

                var answer = confirm("Are you sure you want to copy location to this order ?");
                if (answer){


                    $.post('<?php print site_url('ajax/setbuyerloc');?>',
                        {   id : id,
                            latitude: lat,
                            longitude: lon,
                            type: 'delivery' },
                            function(data) {
                                if(data.result == 'OK'){
                                    oTable.fnDraw();
                                    refreshMap()
                                    alert('Location copied to target order');
                                }else{
                                    alert('Copy location canceled');
                                }
                                //alert(data.status);
                            },'json');


                }else{
                    alert('copy cancelled');
                }

            }

            e.preventDefault();
        });

        $('select[name=DataTables_Table_0_length]').on('change',function(){
            refreshMap();
        });

        //Refresh page every x second ( in milis)
        function refresh(){
            refreshMap();
            oTable.fnDraw();
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


        $('#search_deliverydate').datepicker({ dateFormat: 'yy-mm-dd' });

        $('#search_deliverydate').change(function(){
            oTable.fnFilter( this.value, $('tfoot input').index(this) );
            refreshMap();
        });

        $('#search_device').change(function(){
            oTable.fnFilter( this.value, $('tfoot input').index(this) );
            refreshMap();
        });

        $('#showLocUpdate').click(function(){
            refreshMap();
        });

        $('#save_seq').on('click',function(){
            var sequences = [];
            var sequence_id = [];

            $('input.inseq').each(function(){
                sequences.push(this.value);
                sequence_id.push(this.id);
            });

            console.log(sequences);
            console.log(sequence_id);

            $.post('<?php print site_url('ajaxpos/seq');?>',
                {
                    ids : sequence_id,
                    seq : sequences
                }, function(data) {
                if(data.result == 'ok'){
                    //redraw table
                    oTable.fnDraw();
                    refreshMap();
                }
            },'json');


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

        refreshTab = function(){
            oTable.fnDraw();
            refreshMap();
        };

        $('#setloc_dialog').dialog({
            autoOpen: false,
            height: 640,
            width: 1100,
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

    } );


</script>

    <div>
        Track Line Weight <select name="line" id="lineWeight">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4" selected >4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
        </select> |
        <input type="checkbox" checked="checked" id="routeMode" value="1" /> Routing Mode |
         Urutan <input type="text" checked="checked" id="routeSeq" value="0" style="width:25px;" /> |
         <button id="routeReset">Reset</button>


    </div>
    <div id="tracker" >
        <table style="padding:0px;margin:0px;">
            <tr>
                <td style="vertical-align: top;">
                    <h3>Position Tracker</h3>
                    <div id="map" style="width:800px;height:700px;display:block;border:thin solid grey;"></div>
                </td>
                <td style="width:100%;height:100%;vertical-align:top;">
                    <h3>Delivery Orders</h3>
                    <?php echo $this->table->generate(); ?>
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