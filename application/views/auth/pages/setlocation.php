<html>
<head>
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

    <?php echo $this->ag_asset->load_script('jquery-1.7.1.min.js');?>

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

        $(document).ready(function(){
            setupMap();
        });
    </script>
</head>
<body>
    <table style="width:100%;border:0;margin:0;">
        <tr>
            <td style="width:500px;vertical-align:top">
                <div id="map" style="width:500px;height:450px;display:block;border:thin solid grey;"></div>
            </td>
            <td>
                Buyer Info :
            </td>
        </tr>
    </table>
</body>
