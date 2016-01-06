<?php
    /*

<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.css" />
 <!--[if lte IE 8]>
     <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.ie.css" />
 <![endif]-->

<script src="http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.js"></script>

<script src="http://maps.google.com/maps/api/js?v=3.2&sensor=false"></script>

<?php echo $this->ag_asset->load_script('leaflet-google.js');?>

    */
?>


<script>

    //var locdata = <?php print $locdata;?>;

    //CM_ATTRIB = 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
    //        '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
    //        'Imagery © <a href="http://cloudmade.com">CloudMade</a>';

    //CM_URL = 'http://{s}.tile.cloudmade.com/bc43265d42be42e3bfd603f12a8bf0e9/997/256/{z}/{x}/{y}.png';

    //OSM_URL = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    //OSM_ATTRIB = '&copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap</a> contributors';

    $(document).ready(function() {



        //var map = L.map('map').setView([-6.17742,106.828308], 11);

        //    var googleLayer = new L.Google('ROADMAP');
        //    map.addLayer(googleLayer);

        /*
        L.tileLayer(OSM_URL, {
            attribution: OSM_ATTRIB,
            maxZoom: 18
        }).addTo(map);
        */

        //var marker = L.marker([locdata]).addTo(map);

        /*
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
        */

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
    line-height: 2em;
}

/*google map tile tweak*/
.leaflet-google-layer{
    z-index: 0 !important;
}
.leaflet-map-pane{
    z-index: 100;
}

</style>


<div id="tracker" >
    <table style="padding:0px;margin:0px;">
        <tr>
            <?php
                /*
                    <td>
                        <h3>Device Last Positions</h3>
                        <div id="map" style="width:600px;height:950px;display:block;"></div>
                    </td>

                */
            ?>
            <td style="width:50%;height:100%;vertical-align:top;">
                <h3>Statistics</h3>
                <div id="statistics"  style="width:100%;height:100%;">
                    <span>Incoming Order Data <?php print $period;?></span>
                    <?php print $incomingtab; ?>
                </div>
            </td>
            <td style="width:50%;height:100%;vertical-align:top;">
                <h3>&nbsp;</h3>
                <div id="statistics"  style="width:100%;height:100%;">
                    <span>Pending Order Data <?php print $period;?></span>
                    <?php print $pendingtab; ?>
                </div>
            </td>

        </tr>
        <tr>
            <td colspan="2" style="width:50%;height:100%;vertical-align:top;">
                <h3>&nbsp;</h3>
                <div id="statistics"  style="width:100%;height:100%;">
                    <span>Pick Up to Delivery <?php print $period;?></span>
                    <?php print $pickuptab; ?>
                </div>
            </td>
        </tr>
    </table>
</div>