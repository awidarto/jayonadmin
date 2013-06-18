<html>
<head>
    <title>Delivery Details</title>
    <style>

        html{margin:0px;}

        body{margin:5px;
            font-size: 12px;
        }

        #wrapper{
            width:850px;
            margin:5px;
            display:block;
            font-family:'Trebuchet Ms', 'Yanone Kaffeesatz', Lato, Lobster, 'Lobster Two','Droid Sans', Helvetica ;
            font-size:13px;
            text-align:left;
        }

        h2{
            margin:0px;
            padding-top:15px;
        }

        td{
            font-size: 12px;
        }

        .dataTable{
            width:100%;
            font-family:'Trebuchet Ms', 'Yanone Kaffeesatz', Lato, Lobster, 'Lobster Two','Droid Sans', Helvetica ;
            margin-top:8px;

        }

        .dataTable td{
            border-bottom:thin solid #eee;
            text-align:left;
            font-size: 11px;
        }

        .dataTable th{
            text-align:left;
            padding-right:15px;
            font-size:12px;
            font-weight: bold;
            border-top:thin solid #eee;
            border-bottom:thin solid #eee;
            /*border-left:thin solid #eee;*/
        }

        .dataTable tr>th{
            width:20px;

        }

        .dataTable tr>th:last-child{
            width:100px;
            /*border-right:thin solid #eee;*/
        }

        .dataTable td{
            /*border-left:thin solid #eee;*/
            border-bottom:thin solid #eee;
        }

        .dataTable td:last-child{
            /*border-right:thin solid #eee;*/
            text-align:right;
        }

        #jayon_logo{
            vertical-align:top;
            font-family:'Trebuchet Ms', 'Yanone Kaffeesatz', Lato, Lobster, 'Lobster Two','Droid Sans', Helvetica ;
            font-size: 11px;
            text-align:left;
        }

        #jayon_logo img{
            width:170px;
        }

        #order_detail,#merchant_detail{
            vertical-align:top;
            padding-top:0px;
            
        }

        #order_detail h2{
            text-align: center;
        }

        #merchant_detail{
            vertical-align:top;
            margin:0px;
            padding:8px;
        }

        #mainInfo tr>td:first-child, #orderInfo tr>td:first-child{
            width:150px;
        }

        table#main_table{
            width:840px;
            padding:0px;
            margin:0px;
        }

        .row_label{
            width:150px;
        }

        table#signBox{
            font-size: 12px;
            margin-top:15px;
            width:840px;
        }

        #signBox th{
            width:100px;
            vertical-align:top;
            border-top: thin solid #eee;
            border-bottom: thin solid #eee;
            /*border-right:thin solid #eee;*/
            margin:2px;
        }

        #sign_name td{
            border-top: thin solid #eee;
            border-bottom: thin solid #eee;
            /*border-right:thin solid #eee;*/
        }

        tr#sign_name td:first-child{
            /*border-left: thin solid #eee;*/
        }

        #signBox th:first-child{
            /*border-left: thin solid #eee;*/
        }

        #mainInfo tr td:last-child, #orderInfo tr td:last-child{
            border-bottom: thin solid #eee;
            /*border-left:thin solid #eee;*/
        }

        #mainInfo td{
            vertical-align:top;
        }

        h2{
            font-size: 18px;
            display: block;
            text-align: center;
        }

        #order_slot{
            margin-left:20px;
            float:right;
        }

        .editable, #note{
            font-weight:bold;
            color:maroon;
        }

        #note{
            font-size: 11px;
            padding:3px;
        }

        .editable form input{
            font-size: 12px;
        }

        table#main_table input[type="text"]{
            width:150px;
        }

        table#main_table textarea{
            width:200px;
            height:40px;
        }

        td#order_details input[type="text"]#quantity,
        td#order_details input[type="text"].item_qty,
        td#order_details input[type="text"].item_pct_disc,
        td#order_details input[type="text"]#unit_percent_discount{
            width:40px;
            text-align:right;
        }

        td#order_details input[type="text"]#unit_nominal_discount,
        td#order_details input[type="text"]#unit_price,
        td#order_details input[type="text"].item_unit_price,
        td#order_details input[type="text"].item_total,
        td#order_details input[type="text"].item_nom_disc{
            width:100px;
            text-align:right;
        }

        td#order_details input[type="text"].item_total{
            width:130px;
            text-align:right;
        }

        td#order_details input[type="text"]#description,
        td#order_details input[type="text"].item_desc{
            width:250px;
        }

        td#order_details input[type="text"]{
            width:130px;
        }

        input[type="text"]{
            border:thin solid grey;
        }

        .dataTable th{
            text-align:center;
        }

        td.sums{
            vertical-align: top;
            text-align:left;
        }

        td.lsums{
            vertical-align: top;
            text-align:right;
        }

        .sum_input, #recalc{
            margin-right:0px;
            text-align:right;
        }

        .item_price{
            text-align:right;
        }

        td.item_form{
            text-align:center;
        }

        tr#detail_row td{
            background-color: #aaa;
        }

        .orange{
            background-color: orange;
        }

        button, select{
            font-size: 11px;
            border:thin solid grey;
        }

        #trx_result{
            font-weight:bold;
        }

        #loader{
            position:absolute;
            top:350px;
            left:400px;
            width:100px;
            border:thin solid grey;
            height:20px;
            background-color: white;
            font-family:'Trebuchet Ms', 'Yanone Kaffeesatz', Lato, Lobster, 'Lobster Two','Droid Sans', Helvetica ;
            font-size: 12px;
            padding:2px;
            text-align:center;
            vertical-align: middle;

        }

        table#main_table input[type="text"].short{
            width:35px;
        }

        table.tariff thead tr th{
            font-size: 12px;
            font-weight: bold;
            text-align: center;
        }

        table.tariff * td{
            text-align: left;            
        }

        table.tariff{
            width:100%;
        }

        .fine{
            font-size: 11px;
        }  

        td.overquota, td.overquota .ui-state-default, td.full .ui-state-default{
            background-image: none;
            background-color: orange;
            color:#eee;
            opacity: 1;
        }

        td.holiday, td.holiday .ui-state-default, td.weekend .ui-state-default{
            background-image: none;
            background-color: red;
            color:#eee;
            opacity: 1;
        }

        td.holiday, td.overquota{
            padding:2px;
        }

        td.ui-state-disabled, td.ui-widget-content .ui-state-disabled, td.ui-widget-header .ui-state-disabled {
            opacity: 1;
        }

    </style>

    <?php echo $this->ag_asset->load_css('jquery-ui-1.8.16.custom.css','jquery-ui/flick');?>
    <?php echo $this->ag_asset->load_css('jquery.ui.timepicker.css');?>

    <?php echo $this->ag_asset->load_script('jquery-1.7.1.min.js');?>
    <?php echo $this->ag_asset->load_script('jquery.datatables.min.js','jquery-datatables');?>

    <?php echo $this->ag_asset->load_script('jquery-ui-1.8.16.custom.min.js','jquery-ui');?>
    <?php echo $this->ag_asset->load_script('jquery-ui-timepicker-addon.js','jquery-ui');?>
    <?php echo $this->ag_asset->load_script('jquery.jeditable.mini.js');?>
    <?php echo $this->ag_asset->load_script('jquery.ui.timepicker.js');?>

    
    <script>

    var dateBlock = <?php print getdateblock();?>;

    var slotmax = <?php print $slotmax;?>;

    var sequence = 0;

    var total_price = 0;
    var total_price_discounted = 0;
    var total_discount = 0;
    var total_charges = 0;
    var total_tax = 0;
    var cod_cost = 0;
    var percent_tax = 0;

    var lastorder = <?php print get_option('auto_lock_hours')*60*60*1000;?>;

    var cod_surcharge_table = <?php print $codhash; ?>;    

    var current_app = 0;
    var delivery_fee = 0;

    $(document).ready(function() {
        $('.editable').editable('<?php print base_url();?>ajax/editdetail');

        $('#delivery_type').change(function(){
            if($('#delivery_type').val() == 'PS'){
                $('#buyer_name_label').html('Supplier Name<hr /><span class="fine">Nama Supplier</span>');
                $('#buyer_email_label').html('Supplier Email<hr /><span class="fine">Alamat Email Supplier</span>');
                $('#buyer_delivered_to_label').html('Supplier Personnel<hr /><span class="fine">Petugas Supplier<br />harap sebutkan jabatan dan titel jika ada</span>');
                $('#buyer_shipping_label').html('Pick Up Address<hr /><span class="fine">Alamat Pengambilan<br />harap sebutkan nama gedung dan lantai jika ada.</span>');
                $('#delivery_tariff_label').html('Pickup Tariff<hr /><span class="fine">Tarif Pengambilan</span>');
            }else{
                $('#buyer_name_label').html('Buyer Name<hr /><span class="fine">Nama Pembeli</span>');
                $('#buyer_email_label').html('Buyer Email<hr /><span class="fine">Alamat Email Pembeli</span>');
                $('#buyer_delivered_to_label').html('Delivered To<hr /><span class="fine">Nama Penerima<br />harap sebutkan jabatan dan titel jika ada</span>');
                $('#buyer_shipping_label').html('Shipping Address<hr /><span class="fine">Alamat Pengiriman<br />harap sebutkan nama gedung dan lantai jika ada.</span>');
                $('#delivery_tariff_label').html('Delivery Tariff<hr /><span class="fine">Tarif Pengiriman</span>');
            }

            if($('#delivery_type').val() == 'COD'){
                $('#sub_cod').show();
                $('#sub_ccod').hide();

            }else if($('#delivery_type').val() == 'CCOD'){
                $('#sub_cod').hide();
                $('#sub_ccod').show();
            }else{
                $('#sub_cod').hide();
                $('#sub_ccod').hide();
            }


            getweightandcod();
        });        

        $('#buyerdeliverydate').datepicker({
            numberOfMonths: 2,
            showButtonPanel: true,
            dateFormat:'yy-mm-dd',
            timeFormat: 'hh:mm:ss',
            stepMinute:30,
            onSelect:function(dateText, inst){
                if(dateBlock[dateText] == 'weekend'){
                    alert('no delivery on weekend');
                }else if(dateBlock[dateText] == 'full'){
                    alert('time slot is full');
                }else if(dateBlock[dateText] == 'holiday'){
                    alert('date is holiday');
                }else if(dateBlock[dateText] == 'overquota'){
                    alert('daily capacity exceeded');
                }else{
                    $('#rescheduled_deliverytime').val(dateText);
                }
            },
            beforeShowDay:getBlocking
        });

        $('#buyerdeliverytime').timepicker({
            hours: { starts: 8, ends: 22 },
            minutes: { interval: 60 },
            rows: 2,
            showMinutes:false,
            showPeriodLabels: false,
            minuteText: 'Min',
            defaultTime: '08:00',
            onHourShow:OnHourShowCallback,
            onSelect: function(time, inst) {
                $('#'+ inst.id).val('');
                //console.log('onSelect triggered with time : ' + time + ' for instance id : ' + inst.id);
                
                var buyerdate = $('#buyerdeliverydate').val();

                //console.log(buyerdate);
                if(buyerdate === undefined || buyerdate == ''){
                    $('#'+ inst.id).val('');
                    alert('Please pick a date first');
                }else{
                    var now = new Date();
                    var then = new Date(buyerdate +' '+time + ':00');
                    //console.log('now : ' + now.getTime());
                    //console.log('then : ' + then.getTime());

                    var leeway = then.getTime() - now.getTime();
                    if(leeway < 0){
                        alert('Please do not specify past date');
                    }else{
                        if(leeway < lastorder){
                            $('#'+ inst.id).val('');
                            alert('Specified delivery time is less than <?php print get_option('auto_lock_hours');?> hours from now. Please select another time.');
                        }else{
                            $('#'+ inst.id).val(time + ':00');
                        }
                    }

                }

            }
        });

        /*
        $('#buyerdeliverytime').change(
            function(){
                var buyerdate = $('#buyerdeliverydate').val();

                var validatetime = new RegExp('^([01]\d|2[0-3]):?([0-5]\d)$');

                console.log(validatetime.exec($(this).val()));

                //console.log(buyerdate);
                if(buyerdate === undefined || buyerdate == ''){
                    $(this).val('');
                    alert('Please pick a date first');
                }else if($(this).val().length == 5 && validatetime.exec($(this).val())){
                    var now = new Date();

                    var then = new Date(buyerdate + ' ' + $(this).val());
                    console.log('now : ' + now.getTime());
                    console.log('then : ' + then.getTime());

                    var leeway = then.getTime() - now.getTime();
                    if(leeway < 0){
                        alert('Please do not specify past date');
                    }else{
                        if(leeway < lastorder){
                            $(this).val('');
                            alert('Specified delivery time is less than <?php print get_option('auto_lock_hours');?> hours from now. Please select another time.');
                        }
                    }

                }else if($(this).val().length == 5){
                    alert('Invalid time format, please use hh:mm, in 24 hours format');
                }

            }
        );
        */

        function OnHourShowCallback(hour) {
            if (hour == 13 || hour == 18) {
                return false; // not valid
            }
            return true; // valid
        }

        function getBlocking(d){
            /*
                $.datepicker.formatDate('yy-mm-dd', d);
            */
            var curr_date = d.getDate();
            var curr_month = d.getMonth() + 1; //months are zero based
            var curr_year = d.getFullYear();
        
            curr_date = (curr_date < 10)?"0" + curr_date : curr_date;
            curr_month = (curr_month < 10)?"0" + curr_month : curr_month;
            var indate = curr_year + '-' + curr_month + '-' + curr_date;

            var select = 1;
            var css = 'open';
            var popup = 'working day';
            
            if(window.dateBlock[indate] == 'weekend'){
                select = 0;
                css = 'weekend';
                popup = 'weekend';
            }else if(window.dateBlock[indate] == 'holiday'){
                select = 0;
                css = 'weekend';
                popup = 'holiday';
            }else if(window.dateBlock[indate] == 'blocked'){
                select = 0;
                css = 'blocked';
                popup = 'zero time slot';
            }else if(window.dateBlock[indate] == 'full'){
                select = 0;
                css = 'blocked';
                popup = 'zero time slot';
            }else if(window.dateBlock[indate] == 'overquota'){
                select = 0;
                css = 'blocked';
                popup = 'daily capacity exceeded';
            }else{
                select = 1;
                css = '';
                popup = 'working day';
            }
            return [select,css,popup];
        }

        $( '#merchant_name' ).autocomplete({
            source: '<?php print site_url('ajax/getmerchant')?>',
            method: 'post',
            minLength: 2,
            select:function(event,ui){
                $('#merchant_id').val(ui.item.id);
                $('#merchant_id_txt').html(ui.item.id);                
                $('#merchant_fullname').val(ui.item.fullname);                
                $('#merchant_email').val(ui.item.email); 
                getapp(ui.item.id);               
            }
        });

        $( '#buyer_name' ).autocomplete({
            /*source: '<?php print site_url('ajax/getbuyer')?>',*/
            source:function(request,response){
                var request_data = {
                    term: request.term,
                    merchant_id: $('#merchant_id').val()
                };

                var url = '<?php print site_url('ajax/getbuyer')?>';

                $.post(url, request_data, function (data, status, xhr) {
                     response(data);
                },'json');
            },
            method: 'post',
            minLength: 2,
            select:function(event,ui){
                $('#buyer_id').val(ui.item.id);
                $('#buyer_id_txt').html(ui.item.id);                
                $('#buyer_email').val(ui.item.email);
                $('#shipping_address').val(ui.item.shipping);
                $('#phone').val(ui.item.phone);
            }
        });

        $('#buyer_email' ).autocomplete({
            source: '<?php print site_url('ajax/getbuyeremail')?>',
            method: 'post',
            minLength: 2,
            select:function(event,ui){
                $('#buyer_id').val(ui.item.id);
                $('#buyer_id_txt').html(ui.item.id);
                $('#buyer_name').val(ui.item.fullname)
                $('#shipping_address').val(ui.item.shipping);
                $('#phone').val(ui.item.phone);
            }
        });

        $( '#buyerdeliverycity' ).autocomplete({
            source: '<?php print site_url('ajax/getcities')?>',
            method: 'post',
            minLength: 2
        });

        $( '#buyerdeliveryzone' ).autocomplete({
            source: '<?php print site_url('ajax/getzone')?>',
            method: 'post',
            minLength: 2
        });

        function getapp(merchant_id){
            $.post('<?php print site_url('ajax/getappselect');?>',
                { merchant_id: merchant_id,delivery_type:$('#delivery_type').val() }, 
                function(data) {
                    $('#application_id').html(data.data);
                },'json');
        }

        $('#add_item').click(function(){
            sequence++;
            var row = '<tr id="trx_'+ sequence+'">';
                row += '<td class="item_form"><input type="text" class="item_desc" name="description" value="'+ $('#description').val() +'" /></td>';
                row += '<td class="item_form"><input type="text" class="item_qty" name="quantity" value="'+ $('#quantity').val() +'" /></td>';
                row += '<td class="item_form"><input type="text" class="item_unit_price" name="unit_price" value="'+ $('#unit_price').val() +'"  /></td>';
                row += '<td class="item_form"><input type="text" class="item_pct_disc orange" name="unit_pct_disc" value="'+ $('#unit_percent_discount').val() +'" /></td>';
                row += '<td class="item_form"><input type="text" class="item_nom_disc orange" name="unit_nom_disc" value="'+ $('#unit_nominal_discount').val() +'"  /></td>';
                row += '<td><input type="text" class="item_total" name="unit_total" value="'+ $('#unit_total').val() +'"  /></td><td><button name="add_item" type="button" id="remove_item" onClick="removeRow(\'trx_'+ sequence+'\');" >Del</button></td>';
                row += '</tr>';

            $('#calc_data').before(row);

            calculate();

            $('#description').val('');
            $('#quantity').val('');
            $('#unit_price').val('');
            $('#unit_percent_discount').val('');
            $('#unit_nominal_discount').val('');
            $('#unit_total').val('');
        });

        $('#recalc').click(function(){
            calculate();
        });


        $('#createuser_dialog').dialog({
            autoOpen: false,
            height: 400,
            width: 600,
            modal: true,
            buttons: {
                "Create Buyer": function() {
                    //do something
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
                //allFields.val( "" ).removeClass( "ui-state-error" );
                $('#assign_deliverytime').val('');
            }
        });

        $('#create_user').click(function(){
            $('#createuser_dialog').dialog('open');
        });

        $('#buyerdeliverycity').change(function(){
            //alert($('#buyerdeliverycity').val());
            var city = $('#buyerdeliverycity').val();

            if(city != 0){
                $('#zone_select').html('Loading zones...');
                $.post('<?php print site_url('ajax/getzoneselect');?>',
                    { city: city }, 
                    function(data) {
                        $('#zone_select').html(data.data);
                    },'json');
            }

        });

        $('#application_id').change(function(e){
            var val = $(e.target).val();
            current_app = val;
            getweightandcod();
            calculate();
        });

        $('#weight_selection').change(function(e){
            delivery_fee = $(e.target).val();
            $('#delivery_cost_txt').html(delivery_fee);
            $('#delivery_cost').val(delivery_fee);
            calculate();            
        });

        $('#delivery_type').change(function(){
            getweightandcod();
            calculate();
        });

        function getweightandcod(){
            var delivery_type = $('#delivery_type').val();

            //console.log(current_app);

            if(delivery_type == 'COD' || delivery_type == 'CCOD'){
                $.post('<?php print site_url('ajax/getcoddata');?>',
                    { app_key: current_app }, 
                    function(data) {
                        $('#cod_tab_data').html(data.data.table);
                        cod_surcharge_table = $.parseJSON(data.data.codhash);
                        calculate();
                    },'json');

                $('#cod_line').show();
                $('#cod_tab').show();
            }else{
                $('#cod_line').hide();
                $('#cod_tab').hide();
                $('#cod_cost_txt').html(0);
                $('#cod_cost').val(0);
            }

            if(delivery_type == 'PS'){
                $.post('<?php print site_url('ajax/getpickupdata');?>',
                    { app_key: current_app }, 
                    function(data) {
                        $('#delivery_tab_data').html(data.data.table);
                        $('#weight_selection').html(data.data.selector);
                    },'json');

            }else{
                $.post('<?php print site_url('ajax/getweightdata');?>',
                    { app_key: current_app }, 
                    function(data) {
                        $('#delivery_tab_data').html(data.data.table);
                        $('#weight_selection').html(data.data.selector);
                    },'json');

            }

        }

        function getzone(city){
            $.post('<?php print site_url('ajax/getzoneselect');?>',
                { city: city }, 
                function(data) {
                    $('#zone_select').html(data.data);
                },'json');
        }

    });

    function submitorder(){
        var result = validate();
        if(result[0]){
            //alert("Processing...");
            var pdata = {};


            pdata.api_key = $('#app_id').val();
            //pdata.transaction_id = $('#total_charges').val(); // random generated
            pdata.buyer_id  = $('#buyer_id').val();
            pdata.merchant_id  = $('#merchant_id').val();
            pdata.buyer_name = $('#buyer_name').val();
            pdata.recipient_name = $('#recipient_name').val();
            pdata.shipping_address = $('#shipping_address').val();
            pdata.buyerdeliveryzone = $('#buyerdeliveryzone').val();
            pdata.buyerdeliverycity = $('#buyerdeliverycity').val();
            pdata.buyerdeliverytime = $('#buyerdeliverydate').val();
            pdata.buyerdeliveryslot = $('#buyerdeliverytime').val();
            pdata.direction = $('#direction').val();
            pdata.auto_confirm = true; //true
            pdata.email = $('#buyer_email').val();
            pdata.zip = $('#buyerdeliveryzip').val();
            pdata.mobile1 = $('#mobile1').val();
            pdata.mobile2 = $('#mobile2').val();
            pdata.phone = $('#phone').val();
            pdata.total_price = $('#total_price').val();
            pdata.total_discount = $('#total_discount').val();
            pdata.total_tax = $('#total_tax').val();
            pdata.chargeable_amount = $('#total_charges').val();
            pdata.cod_cost = $('#cod_cost').val();     /* cod_cost 0 if absorbed in price of goods sold, otherwise specify the amount here*/
            pdata.delivery_cost = $('#delivery_cost').val();
            pdata.delivery_type = $('#delivery_type').val();
            pdata.currency = $('#currency').val();   /* currency in 3 digit codes*/
            pdata.status = 'confirmed'; /* status can be : pending or confirm, depending on merchant's workflow */
            pdata.width = $('#package_width').val();
            pdata.height = $('#package_height').val();
            pdata.length = $('#package_length').val();
            pdata.weight = $('#package_weight').val();

            pdata.show_shop = ($('#show_shop').is(':checked'))?1:0;
            pdata.show_merchant = ($('#show_merchant').is(':checked'))?1:0;

            if($('#cod_surcharge_bearer').is(':checked')){
                pdata.bearer_cod = 'buyer';
            }else{
                pdata.bearer_cod = 'merchant';                
            }

            if($('#delivery_bearer').is(':checked')){
                pdata.bearer_delivery = 'buyer';
            }else{
                pdata.bearer_delivery = 'merchant';
            }


            pdata.cod_method = $('#sub_cod').val();
            pdata.ccod_method = $('#sub_ccod').val();

            var udescs = [];
            var uqtys = [];
            var uprices = [];
            var upctdisc = [];
            var unomdisc = [];
            var utotals = [];

            i = 0;
            $('.item_desc').each(function(){
                udescs[i] = $(this).val();
                i++;
            }); 

            i = 0;
            $('.item_qty').each(function(){
                uqtys[i] = $(this).val();
                i++;
            }); 

            i = 0;
            $('.item_unit_price').each(function(){
                uprices[i] = $(this).val();
                i++;
            });

            i = 0;
            $('.item_pct_disc').each(function(){
                upctdisc[i] = $(this).val();
                i++;
            });

            i = 0;
            $('.item_nom_disc').each(function(){
                unomdisc[i] = $(this).val();
                i++;
            });

            i = 0;
            $('.item_total').each(function(){
                utotals[i] = $(this).val();
                i++;
            });


            pdata.udescs = udescs;
            pdata.uqtys = uqtys;
            pdata.uprices = uprices;
            pdata.upctdisc = upctdisc;
            pdata.unomdisc = unomdisc;
            pdata.utotals = utotals;

            if($('#trx_result').html() != 'Transaction Success'){
                //$('#loader').show();
                $.post('<?php print site_url('ajax/neworder');?>',
                    pdata, 
                    function(data) {
                        //$('#loader').hide();
                        $('#sendingorder', window.parent.document).hide();
                        if(data.status == 'OK:ORDERPOSTED'){
                            //alert('Transaction Success');
                            $('#sendingstatus', window.parent.document).html('Transaction Success');
                            $('#sendingstatus', window.parent.document).show();
                            //$('#trx_result').html('Transaction Success');
                            //$('#neworder_dialog', window.parent.document).dialog( "close" );
                        }else{
                            $('#sendingstatus', window.parent.document).html('Transaction Failed');
                            $('#sendingstatus', window.parent.document).show();                            
                        }
                        //alert(data.status);
                    },'json');
            }else{
                alert('Order already posted, please close dialog and start over.');
            }

        }else{
            alert(result[1]);
        }
    }

    function removeRow(rowId){
        var trxid = 'tr#trx_'+rowId;
        $('#'+rowId).remove();
        calculate();
        sequence--;
    }

    function getCODcharge(total_price){

        if(total_price == 0){
            return 0;
        }

        for( i = 0; i < cod_surcharge_table.length;i++){
            var curr = cod_surcharge_table[i];
            if(curr.from_price <= total_price && total_price <= curr.to_price){
                return parseInt(curr.surcharge);
            }
        }
    }

    function validate(){

            var validisplay = '';

            if($('#merchant_id').val() === 'undefined' || $('#merchant_id').val() == '' || $('#merchant_id').val() == 0 || $('#merchant_id').val() == null || $('#merchant_id').val() === 'NaN'){
                validisplay += 'Merchant Unspecified\r\n';
                //return [false,'Merchant Unspecified'];
            }
            /*
            if($('#buyer_id').val() === 'undefined' || $('#buyer_id').val() == '' || $('#buyer_id').val() == 0 || $('#buyer_id').val() == null || $('#buyer_id').val() === 'NaN'){
                return [false,'Buyer Unspecified'];
            }
            */
            if($('#app_id').val() == 0){
                validisplay += 'Invalid Store ID\r\n';
                //return [false, 'Application Domain Invalid'];
            }

            if($('#buyerdeliverycity').val() == 0){
                validisplay += 'City Unspecified\r\n';
                //return [false, 'Please specify City'];
            }

            if($('#buyerdeliveryzone').val() === 'undefined' || $('#buyerdeliveryzone').val() == '' || $('#buyerdeliveryzone').val() == 0 || $('#buyerdeliveryzone').val() == null || $('#buyerdeliveryzone').val() === 'NaN'){
                validisplay += 'Zone Unspecified\r\n';
                //return [false,'Please specify Zone'];
            }

            if($('#package_weight').val() == ''){
                validisplay += 'Weight Unspecified\r\n';
                //return [false, 'Weight Unspecified'];
            }

            if($('#delivery_type').val() == 0){
                validisplay += 'Delivery Type Unspecified\r\n';
                //return [false, 'Please specify Delivery Type'];
            }

            if($('#package_width').val() === 'undefined' || $('#package_width').val() == '' || $('#package_width').val() == 0 || $('#package_width').val() == null || $('#package_width').val() === 'NaN'){
                validisplay += 'Width Unspecified\r\n';
                //return [false,'Width Unspecified'];
            }

            if($('#package_height').val() === 'undefined' || $('#package_height').val() == '' || $('#package_height').val() == 0 || $('#package_height').val() == null || $('#package_height').val() === 'NaN'){
                validisplay += 'Height Unspecified\r\n';
                //return [false,'Height Unspecified'];
            }

            if($('#package_length').val() === 'undefined' || $('#phone').val() == '' || $('#phone').val() == 0 || $('#phone').val() == null || $('#phone').val() === 'NaN'){
                validisplay += 'Contact Number / Phone Unspecified\r\n';
                //return [false,'Contact Number / Phone Unspecified'];
            }

            if($('#phone').val() === 'undefined' || $('#package_length').val() == '' || $('#package_length').val() == 0 || $('#package_length').val() == null || $('#package_length').val() === 'NaN'){
                validisplay += 'Length Unspecified\r\n';
                //return [false,'Length Unspecified'];
            }

            if($('#package_width').val() > <?php print get_option('max_width');?>){
                validisplay += 'Max Width Exceeded\r\n';
                //return [false,'Max Width Exceeded'];
            }

            if($('#package_height').val() > <?php print get_option('max_height');?>){
                validisplay += 'Max Height Exceeded\r\n';
                //return [false,'Max Height Exceeded'];
            }

            if($('#package_length').val() > <?php print get_option('max_length');?>){
                validisplay += 'Max Length Exceeded\r\n';
                //return [false,'Max Length Exceeded'];
            }

            if($('#direction').val() === 'undefined' || $('#direction').val() == '' || $('#direction').val() == 0 || $('#direction').val() == null || $('#direction').val() === 'NaN'){
                validisplay += 'Direction Unspecified\r\n';
                //return [false,'Direction Unspecified'];
            }

            if( $('#show_merchant').is(':checked') == false && $('#show_shop').is(':checked') == false ){
                validisplay += 'One or both Merchant name and Store name must be shown\r\n';
                //return [false,'Max Length Exceeded'];
            }

            var timeslot = $('#buyerdeliverytime').val();

            var tslot = slotmax[timeslot];

            if(tslot == 0){
                validisplay += 'Time Slot Unspecified\r\n';                
            }else{
                var delidate = $('#buyerdeliverydate').val() + ' ' + tslot;

                var now = new Date();
                var then = new Date(delidate);
                //console.log('now : ' + now.getTime());
                //console.log('then : ' + then.getTime());

                var leeway = then.getTime() - now.getTime();
                if(leeway < 0){
                    validisplay += 'Please do not specify past date\r\n';                
                }else{
                    if(leeway < lastorder){
                        validisplay += 'Specified delivery time is less than <?php print get_option('auto_lock_hours');?> hours from now. Please select another date & time.\r\n';
                    }
                }

            }

            if(validisplay == ''){
                return [true,''];
            }else{
                return [false,validisplay];
            }
    }

    function calculate(){

        var qtys = [];
        var uprice = [];
        var upct = [];
        var unom = [];
        var utotal = [];
        var itemtotal = [];

        i = 0;
        $('.item_qty').each(function(){
            qtys[i] = $(this).val();
            i++;
        }); 

        i = 0;
        $('.item_unit_price').each(function(){
            uprice[i] = $(this).val();
            i++;
        });


        total_price = 0;
        total_discount = 0;

        i = 0;
        $('.item_total').each(function(){
            var unit_total = qtys[i] * uprice[i];
            utotal[i] = unit_total;
            $(this).val(unit_total);
            total_price += unit_total;
            i++;
        });

        //calculate unit discount
        i = 0;
        $('.item_pct_disc').each(function(){
            upct[i] = $(this).val();
            i++;
        });

        i = 0;
        $('.item_nom_disc').each(function(){
            if($(this).val() === 'undefined' || $(this).val() == '' || $(this).val() == 0 || $(this).val() == null || $(this).val() === 'NaN'){
                if(upct[i] === 'undefined' || upct[i] == '' || upct[i] == 0 || upct[i] == null || upct[i] === 'NaN'){
                    var disc = 0;                            
                }else{
                    var disc = utotal[i] * (upct[i]/100);            
                }
                unom[i] = disc;
            }else{
                unom[i] = $(this).val();
                upct[i] = (unom[i] / utotal[i]) * 100;
            }
            $(this).val(unom[i]);
            total_discount += parseInt(unom[i]);
            i++;
        });

        i = 0;
        $('.item_pct_disc').each(function(){
            $(this).val(upct[i]);
            i++;
        });

        if(!($('#cod_cost').val() === 'undefined' || $('#cod_cost').val() == '')){
            cod_cost = parseInt($('#cod_cost').val());
        }else{
            cod_cost = 0;
        }

        if(!($('#percent_tax').val() === 'undefined' || $('#percent_tax').val() == '' || $('#percent_tax').val() == 0 || $('#percent_tax').val() == null || $('#percent_tax').val() === 'NaN')){
            percent_tax = parseInt($('#percent_tax').val());
            if(percent_tax >= 100){
                percent_tax = 10;
            }
            total_tax = (total_price - total_discount) * (percent_tax / 100);
            $('#total_tax').val(total_tax);
        }else{
            total_tax = 0;
        }

        if($('#fixed_discount').is(':checked')){
            total_discount = parseInt($('#total_discount').val());
        }

        delivery_cost = ($('#package_weight').val() == 0)?0:parseInt(delivery_fee);

        total_value = (parseInt(total_price) - parseInt(total_discount)) + parseInt(total_tax);


        total_charges = (parseInt(total_price) - parseInt(total_discount)) + parseInt(total_tax) + parseInt(delivery_cost);

        if($('#delivery_type').val() == 'COD' || $('#delivery_type').val() == 'CCOD'){
            cod_cost = parseInt(getCODcharge(parseInt(total_value)));
            total_charges += cod_cost;
        }else{
            cod_cost = 0;
        }

        $('#cod_cost_txt').html(cod_cost);
        $('#cod_cost').val(cod_cost);

        $('#total_price').val(total_price);
        $('#total_discount').val(total_discount);
        $('#total_charges').val(total_charges);        

    }

    </script>

</head>
<body>
<div id="wrapper">
    <table id="main_table">
        <tbody>
            <tr>
                <td id="merchant_detail">
                    <table border="0" cellpadding="4" cellspacing="0" id="mainInfo">
                        <tbody>

                            <tr>
                                <td colspan="2"><strong>Merchant Info</strong></td>
                            </tr>

                            <tr>
                                <td>Merchant Name<hr /><span class="fine">Nama Merchant</span></td>
                                <td>
                                    Merchant ID : <span id="merchant_id_txt"></span><br />
                                    <input type="text" id="merchant_name" name="merchant_name" value="" /><br />
                                    <span class="fine">
                                        <?php print form_checkbox(array('name'=>'show_merchant','id'=>'show_merchant','value'=>'show_merchant','checked'=>TRUE ));?> Show merchant name in delivery slip</span>
                                    <input type="hidden" value="" id="merchant_id" name="merchant_id" />
                                    <input type="hidden" value="" id="merchant_fullname" name="merchant_fullname" />
                                    <input type="hidden" value="" id="merchant_email" name="merchant_email" />
                                </td>
                            </tr>
                            <tr>
                                <td>Store Name<hr /><span class="fine">Nama Toko</span></td>
                                <td id="application_id">
                                    <select name="app_id" id="app_id">
                                        <option value="0">Select application domain</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>
                                    <span class="fine"><?php print form_checkbox(array('name'=>'show_shop','id'=>'show_shop','value'=>'show_shop','checked'=>TRUE ));?> Show store name in delivery slip</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"><strong>Delivery Info</strong></td>
                            </tr>
                            <tr>
                                <td>Delivery Date:</td>
                                <td>
                                    <input type="text" id="buyerdeliverydate" name="buyerdeliverydate" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td>Delivery Time:</td>
                                <td>
                                    <?php print $slotselect;?>
                                </td>
                            </tr>
                            <tr>
                                <td class="row_label">Delivery City<hr /><span class="fine">Kota</span></td>
                                <td id="city_select">
                                    <?php print $cityselect;?>
                                </td>
                            </tr>
                            <tr>
                                <td class="row_label">ZIP<hr /><span class="fine">Kode Pos</span></td>
                                <td>
                                    <input type="text" id="buyerdeliveryzip" name="buyerdeliveryzip" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td class="row_label">Delivery Zone<hr /><span class="fine">Zona / Kecamatan</span></td>
                                <td id="zone_select">
                                </td>
                            </tr>
                            <tr>
                                <td>Cost Bearer<hr /><span class="fine">Ongkos Dibayar Oleh</span></td>
                                <td>
                                    <label for"delivery_bearer">Delivery Fee :</label><br />
                                        <?php print form_checkbox(array('name'=>'delivery_bearer','id'=>'delivery_bearer','value'=>'buyer','checked'=>TRUE ));?> Bill buyer / tagihkan ke buyer
                                    <br />
                                    <label for="cod_surcharge_bearer">COD / CCOD Surcharges:</label><br />
                                        <?php print form_checkbox(array('name'=>'cod_surcharge_bearer','id'=>'cod_surcharge_bearer','value'=>'buyer','checked'=>TRUE ));?> Bill buyer / tagihkan ke buyer
                                </td>
                            </tr>
                            <tr>
                                <td id="delivery_tariff_label">Delivery Tariff<hr /><span class="fine">Tarif Pengiriman</span></td>
                                <td id="delivery_tab_data">
                                    <?php // print $weighttable;?>
                                </td>
                            </tr>
                            <tr id="cod_tab" style="display:none">
                                <td>COD Surcharge<hr /><span class="fine">Tarif Jasa COD</span></td>
                                <td id="cod_tab_data">
                                    <?php //print $codtable;?>
                                </td>

                        </tbody>
                    </table>
                </td>
                <td id="order_detail">
                    <table width="100%" cellpadding="4" cellspacing="0" id="orderInfo">
                        <tbody>
                            <tr>
                                <td colspan="2"><strong>Order Detail</strong></td>
                            </tr>
                            <tr>
                                <td class="row_label">Delivery Type<hr /><span class="fine">Jenis Pengiriman</span></td>
                                <td id="type_select">
                                    <?php print $typeselect;?> 
                                    <select name="sub_cod" id ="sub_cod" style="display:none">
                                        <option value="cash">Tunai</option>
                                        <option value="debit">Debit</option>
                                    </select>
                                    <select name="sub_ccod" id="sub_ccod" style="display:none">
                                        <option value="full">Pembayaran Penuh</option>
                                        <option value="installment">Cicilan</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="row_label" id="buyer_name_label">Buyer Name<hr /><span class="fine">Nama Pembeli</span></td>
                                <td>
                                    ID : <span id="buyer_id_txt"></span><br />
                                    <input type="hidden" value="" id="buyer_id" name="buyer_id" />
                                    <input type="text" id="buyer_name" name="buyer_name" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td class="row_label" id="buyer_email_label">Buyer Email<hr /><span class="fine">Alamat Email Pembeli</span></td>
                                <td>
                                    <input type="text" id="buyer_email" name="buyer_email" value="" />
                                    <?php // print form_button(array('name'=>'add_buyer','content'=>'Create New Buyer','id'=>'create_user'));?>
                                </td>
                            </tr>
                            <tr>
                                <td class="row_label" id="buyer_delivered_to_label">Delivered To<hr /><span class="fine">Nama Penerima<br />harap sebutkan jabatan dan titel jika ada</span></td>
                                <td>
                                    <input type="text" id="recipient_name" name="recipient_name" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td id="buyer_shipping_label">Shipping Address<hr /><span class="fine">Alamat Pengiriman<br />harap sebutkan nama gedung dan lantai jika ada.</span></td>
                                <td>
                                    <textarea id="shipping_address"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>How to Get There<hr /><span class="fine">Petunjuk Jalan</span></td>
                                <td>
                                    <textarea id="direction"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>Mobile 1<hr /><span class="fine">Mobile 1</span></td>
                                <td>
                                    <input type="text" id="mobile1" name="mobile1" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td>Mobile 2<hr /><span class="fine">Mobile 2</span></td>
                                <td>
                                    <input type="text" id="mobile2" name="mobile2" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td>Phone<hr /><span class="fine">Telepon</span></td>
                                <td>
                                    <input type="text" id="phone" name="phone" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td>Package Dimension<hr /><span class="fine">Dimensi Paket</span></td>
                                <td>
                                    Width / Lebar : <input class="short" type="text" id="package_width" name="package_width" value="" /> cm ( max <?php print get_option('max_width');?> cm )<br />
                                    Height / Tinggi : <input class="short" type="text" id="package_height" name="package_height" value="" /> cm ( max <?php print get_option('max_height');?> cm )<br />
                                    Length / Panjang : <input class="short" type="text" id="package_length" name="package_length" value="" /> cm ( max <?php print get_option('max_length');?> cm ) 
                                </td>
                            </tr>
                            <tr>
                                <td>Package Weight<hr /><span class="fine">Berat Paket</span></td>
                                <td id="weight_selection">
                                    <?php //print $weightselect; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" id="order_details">
                    <table border="0" cellpadding="4" cellspacing="0" class="dataTable">
                        <thead>
                            <tr syle="vertical-align:top;">
                                <th rowspan="2">Description</th>
                                <th rowspan="2">Quantity</th>
                                <th rowspan="2">Unit Price</th>
                                <th rowspan="1" colspan="2">Unit Discount</th>
                                <th rowspan="2" colspan="2">Total<br />
                                    <select name="currency" id="currency" >
                                        <option value="IDR">IDR</option>
                                        <option value="USD">USD</option>
                                    </select><button name="recalc" type="button" id="recalc" >Recalculate</button>
                                </th>
                            </tr>
                            <tr syle="vertical-align:top;">
                                <th>in %</th>
                                <th>in nominal</th>
                            </tr>
                        </thead>
                        <tbody id="detail_body">
                            <tr id="detail_row">
                                <td class='item_form'><input type="text" name="description" value="" id="description"  /></td>
                                <td class='item_form'><input type="text" name="quantity" value="" id="quantity"  /></td>
                                <td class='item_form'><input type="text" name="unit_price" value="" id="unit_price"  /></td>
                                <td class='item_form'><input type="text" name="unit_percent_discount" value="" id="unit_percent_discount" class="orange" /></td>
                                <td class='item_form'><input type="text" name="unit_nominal_discount" value="" id="unit_nominal_discount" class="orange" /></td>
                                <td class='item_form' style="text-align:left;"><input type="text" name="unit_total" value="" id="unit_total"  /></td><td><button name="add_item" type="button" id="add_item" >Add</button></td>
                            </tr>
                            <tr id="calc_data">
                                <td>&nbsp;</td><td colspan="2" id="trx_result">&nbsp;</td><td>&nbsp;</td><td class='lsums'>Total Price ( before discount )</td><td class='sums'><input type="text" name="total_price" value="" id="total_price" class="sum_input"  /></td><td>&nbsp;</td>
                            </tr>
                            <tr class="detail_row">
                                <td colspan="4" rowspan="5">
                                    <ol>
                                        <li>
                                                Fill in item description, quantity, unit price and discount ( if any ) into the appropriate form field above, then click "Add" button<hr />
                                            <span class="fine">
                                                Masukkan nama barang, jumlah, harga per unit dan diskon ( jika ada ), kemudian klik "Add" untuk menambahkan ke daftar order 
                                            </span>
                                        </li>
                                        <li>
                                                Should there are any changes made thereafter, click "Recalculate" to get the correct price calculation<hr />
                                            <span class="fine">
                                                Jika ada perubahan terhadap data yang sudah ditambahkan, klik "Recalculate" untuk menghitung ulang harga dan biaya
                                            </span>
                                        </li>
                                        <li>
                                            
                                                Deleting a row of item can be done by clicking "Del" button, and will automatically recalculate the price<hr />
                                            <span class="fine">
                                                Untuk menghapus barang dari daftar order, klik "Del" di bagian paling kanan baris yang barang bersangkutan, dan secara otomatis akan dilakukan perhitungan ulang
                                            </span>
                                        </li>
                                    </ol>
                                </td><td class='lsums'>Total Discount<br /><input type="checkbox" id="fixed_discount">Set Fixed</td><td class='sums'><input type="text" name="total_discount" value="" id="total_discount"  class="sum_input orange" /></td><td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td class='lsums'>Tax <input type="text" name="percent_tax" value="" id="percent_tax" class="sum_input" style="width:40px;margin-right:2px;" />% Total Tax </td><td class='sums'><input type="text" name="total_tax" value="" id="total_tax" class="sum_input"  /></td><td>&nbsp;</td>
                            </tr>
                            <tr class="detail_row">
                                <td class='lsums'>Delivery Charge</td><td class='sums' style="text-align:right"><span id="delivery_cost_txt"></span><input type="hidden" name="delivery_cost" value="" id="delivery_cost" class="sum_input"  /><input type="hidden" name="cod_cost" value="" id="cod_cost" class="sum_input"  /></td><td>&nbsp;</td>
                            </tr>
                            <tr class="detail_row" id="cod_line">
                                <td class='lsums'>COD Surcharge</td><td class='sums'  style="text-align:right"><span id="cod_cost_txt"></span></td><td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td class='lsums'>Total Charges</td><td class='sums'><input type="text" name="total_charges" value="" id="total_charges" class="sum_input"  /></td><td>&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                    <?php //echo $this->table->generate(); ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div id="loader" style="display:none;">
    <img src="<?php print base_url();?>assets/images/ajax_loader.gif" /> Processing...
</div>
<div id="createuser_dialog" title="Create New Buyer">
    <table style="width:100%;border:0;margin:0;">
        <tr>
            <td style="width:50%;border:0;margin:0;vertical-align:top">
                Full Name:<br />
                <input type="text" name="fullname" class="form" value="" /><br />
                
                Email:<br />
                <input type="text" name="email" class="form" value="" /><br />

                Phone Number:<br />
                <input type="text" name="phone" class="form" value="" /><br />

                Mobile Number:<br />
                <input type="text" name="mobile" class="form" value="" /><br />

            </td>
            <td style="width:50%;border:0;margin:0;vertical-align:top">
                Street:<br />
                <input type="text" name="street" class="form" value="" /><br />

                District:<br />
                <input type="text" name="district" class="form" value="" /><br />

                City:<br />
                <input type="text" name="city" class="form" value="" /><br />

                ZIP:<br />
                <input type="text" name="zip" class="form" value="" /><br />

                Province:<br />
                <input type="text" name="province" class="form" value="" /><br />

                Country:<br />
                <input type="text" name="country" class="form" value="" /><br />

            </td>
        </tr>
    </table>
</div>

</body>
</html>
