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
            vertical-align:top;
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

        .editable * input{
            height:20px;
            width:150px;
        }

        textarea{
            height:60px;
        }

        #trx_result{
            font-weight:bold;
            text-align:center;
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

        .fine{
            font-size: 10px;
        }

        .qr{
            width: 75px;
            height:auto;
        }

        .red{
            color: red;
        }

    </style>

    <?php echo $this->ag_asset->load_css('jquery-ui-1.8.16.custom.css','jquery-ui/flick');?>

    <?php echo $this->ag_asset->load_script('jquery-1.7.1.min.js');?>
    <?php echo $this->ag_asset->load_script('jquery.datatables.min.js','jquery-datatables');?>

    <?php echo $this->ag_asset->load_script('jquery-ui-1.8.16.custom.min.js','jquery-ui');?>
    <?php echo $this->ag_asset->load_script('jquery-ui-timepicker-addon.js','jquery-ui');?>
    <?php echo $this->ag_asset->load_script('jquery.jeditable.mini.js');?>


    <script>
        var asInitVals = new Array();
        var dateBlock = <?php print getdateblock();?>;

        $(document).ready(function() {

            $.editable.addInputType('autocomplete', {
                element : $.editable.types.text.element,
                plugin : function(settings, original) {
                    $('input', this).autocomplete(settings.autocomplete.data);
                }
            });

            $('.editable').editable('<?php print base_url();?>ajax/editdetail',{
                cancel    : 'Cancel',
                submit    : 'OK',
                indicator : '<img src="<?php print base_url();?>assets/images/ajax_loader.gif">',
                tooltip   : 'Click to edit...',
                style     : 'inherit'
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

            $('#assignment_date').datepicker({
                numberOfMonths: 2,
                showButtonPanel: true,
                dateFormat:'yy-mm-dd',
                onSelect:function(dateText, inst){
                    if(dateBlock[dateText] == 'weekend'){
                        alert('no delivery on weekend');
                    }else if(dateBlock[dateText] == 'full'){
                        alert('time slot is full');
                    }else{
                        $('#rescheduled_deliverytime').val(dateText);
                    }
                },
                beforeShowDay:getBlocking
            });

            $('#show_merchant').change(function(){

                var currentsw = $('#show_merchant').is(':checked');
                var id = $('#show_merchant').val();

                if(currentsw == true){
                    nextsw = 'On';
                }else{
                    nextsw = 'Off';
                }

                var answer = confirm("Switch merchant name display " + nextsw + " ?");
                if (answer){
                    $.post('<?php print site_url('ajax/toggle');?>',{'id':id,'switchto':nextsw,'field':'show_merchant'}, function(data) {
                        if(data.result == 'ok'){

                        }
                    },'json');
                }else{
                    alert("Switch cancelled");
                }

            });

            $('#show_shop').change(function(){

                var currentsw = $('#show_shop').is(':checked');
                var id = $('#show_shop').val();

                if(currentsw == true){
                    nextsw = 'On';
                }else{
                    nextsw = 'Off';
                }

                var answer = confirm("Switch store name display " + nextsw + " ?");
                if (answer){
                    $.post('<?php print site_url('ajax/toggle');?>',{'id':id,'switchto':nextsw,'field':'show_shop'}, function(data) {
                        if(data.result == 'ok'){

                        }
                    },'json');
                }else{
                    alert("Switch cancelled");
                }

            });

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
                }else{
                    select = 1;
                    css = '';
                    popup = 'working day';
                }
                return [select,css,popup];
            }

            $('#set_weight').click(function(){
                $('#weight_option').show();
            });

            $('#save_weight').click(function(){
                $('#loader').show();
                $.post('<?php print site_url('ajax/saveweight');?>',
                { delivery_id: $('#delivery_id').val(),weight_tariff:$('#package_weight').val()},
                function(data) {
                    $('#loader').hide();
                    if(data.status == 'OK'){
                        $('#weight').html(data.weight_range);
                        $('#weight_option').hide();
                        $('#delivery_cost').html(data.delivery_cost);
                        $('#total_charges').html(data.total_charges);
                        alert('Weight info updated.')
                    }else if(data.status == 'ERR'){
                        $('#weight_option').hide();
                        alert('Failed to update weight info.')
                    }
                },'json');
            });

            $('#cancel_weight').click(function(){
                $('#weight_option').hide();
            });

            $('#set_delivery').click(function(){
                $('#delivery_option').show();
            });

            $('#save_delivery').click(function(){
                $('#loader').show();
                $.post('<?php print site_url('ajax/savedeliverytype');?>',
                { delivery_id: $('#delivery_id').val(),delivery_type:$('#delivery_type_select').val()},
                function(data) {
                    $('#loader').hide();
                    if(data.status == 'OK'){
                        $('#delivery_type').html(data.delivery_type);
                        $('#delivery_option').hide();
                        $('#cod_cost').html(data.cod_cost);
                        $('#total_charges').html(data.total_charges);
                        alert('Delivery type updated.')
                    }else if(data.status == 'ERR'){
                        $('#delivery_option').hide();
                        alert('Failed to update delivery type.')
                    }
                },'json');
            });

            $('#cancel_delivery').click(function(){
                $('#delivery_option').hide();
            });

            // set delivery bearer
            $('#set_delivery_bearer').click(function(){
                $('#delivery_bearer_option').show();
            });

            $('#save_delivery_bearer').click(function(){
                $('#loader').show();
                $.post('<?php print site_url('ajax/savedeliverybearer');?>',
                { delivery_id: $('#delivery_id').val(),delivery_bearer_type:$('#delivery_bearer_select').val()},
                function(data) {
                    $('#loader').hide();
                    if(data.status == 'OK'){
                        $('#delivery_bearer_type').html(data.delivery_bearer_type);
                        $('#delivery_bearer_option').hide();
                        alert('Delivery bearer updated.')
                    }else if(data.status == 'ERR'){
                        $('#delivery_bearer_option').hide();
                        alert('Failed to update delivery bearer.')
                    }
                },'json');
            });

            $('#do_ocr').on('click',function(){
                $.post('<?php print site_url('ajax/ocr');?>',
                { filename: '<?php print $main_info['merchant_trans_id'].'_address.jpg' ?>'},
                function(data) {
                    $('#loader').hide();
                    if(data.status == 'OK'){
                        $('#shipping_address').val(data.result);
                    }else if(data.status == 'OK:EMPTY'){
                        alert('OCR has empty result.')
                    }
                },'json');
            });

            $('#cancel_delivery_bearer').click(function(){
                $('#delivery_bearer_option').hide();
            });

            // set cod bearer
            $('#set_cod_bearer').click(function(){
                $('#cod_bearer_option').show();
            });

            // set cod bearer
            $('#save_cod_bearer').click(function(){
                $('#loader').show();
                $.post('<?php print site_url('ajax/savecodbearer');?>',
                { delivery_id: $('#delivery_id').val(),cod_bearer_type:$('#cod_surcharge_bearer_select').val()},
                function(data) {
                    $('#loader').hide();
                    if(data.status == 'OK'){
                        $('#cod_bearer_type').html(data.cod_bearer_type);
                        $('#cod_bearer_option').hide();
                        alert('COD surcharge bearer updated.')
                    }else if(data.status == 'ERR'){
                        $('#cod_bearer_option').hide();
                        alert('Failed to update COD bearer.')
                    }
                },'json');
            });

            $('#cancel_cod_bearer').click(function(){
                $('#cod_bearer_option').hide();
            });

            $('.rotate-picture').on('click',function(){
                var trx_id = this.id;

                //alert(trx_id);

                $.post('<?php print site_url('ajax/rotateaddressphoto');?>',{'trx_id':trx_id,'is_thumb':0},
                function(data) {
                    if(data.result == 'ok'){

                        $('#address-pic').attr('src',data.url);
                        //redraw table
                        //oTable.fnDraw();
                        alert("Photo of " + data.trx_id + "_address.jpg rotated");
                    }
                },'json');
            });


        });

        function validate(){
            if($('#buyerdeliverycity').val() === 'undefined' || $('#buyerdeliverycity').val() == '' || $('#buyerdeliverycity').val() == 0 || $('#buyerdeliverycity').val() == null || $('#merchant_id').val() === 'NaN'){
                return [false,'City Unspecified'];
            }

            if($('#buyerdeliveryzone').val() === 'undefined' || $('#buyerdeliveryzone').val() == '' || $('#buyerdeliveryzone').val() == 0 || $('#buyerdeliveryzone').val() == null || $('#merchant_id').val() === 'NaN'){
                return [false,'Zone Unspecified'];
            }

            /*
            if($('#buyer_id').val() === 'undefined' || $('#buyer_id').val() == '' || $('#buyer_id').val() == 0 || $('#buyer_id').val() == null || $('#buyer_id').val() === 'NaN'){
                return [false,'Buyer Unspecified'];
            }
            if($('#app_id').val() == 0){
                return [false, 'Application Domain Invalid'];
            }
            */
            return [true,''];
        }



        function submitorder(){
            //alert('submit order');
            var result = validate();
            if(result[0]){
                //alert("Processing...");
                var pdata = {};


                //pdata.api_key = $('#app_id').val();
                //pdata.transaction_id = $('#total_charges').val(); // random generated
                //pdata.buyer_id  = $('#buyer_id').val();
                //pdata.merchant_id  = $('#merchant_id').val();
                //pdata.buyer_name = $('#buyer_name').val();
                pdata.delivery_id = $('#delivery_id').val();
                pdata.recipient_name = $('#recipient_name').val();
                pdata.shipping_address = $('#shipping_address').val();
                pdata.buyerdeliveryzone = $('#buyerdeliveryzone').val();
                pdata.buyerdeliverycity = $('#buyerdeliverycity').val();
                pdata.buyerdeliverytime = $('#buyerdeliverytime').val();
                pdata.assignment_date = $('#assignment_date').val();
                pdata.directions = $('#directions').val();
                pdata.auto_confirm = true; //true
                pdata.email = $('#buyer_email').val();
                pdata.zip = $('#buyerdeliveryzip').val();
                pdata.phone = $('#phone').val();
                pdata.total_price = $('#total_price').val();
                pdata.total_discount = $('#total_discount').val();
                pdata.total_tax = $('#total_tax').val();
                pdata.chargeable_amount = $('#total_charges').val();
                pdata.cod_cost = $('#cod_cost').val();     /* cod_cost 0 if absorbed in price of goods sold, otherwise specify the amount here*/
                pdata.currency = $('#currency').val();   /* currency in 3 digit codes*/
                pdata.status = 'confirmed'; /* status can be : pending or confirm, depending on merchant's workflow */



                if($('#trx_result').html() != 'Transaction Success'){
                    $('#loader').show();
                    $.post('<?php print site_url('ajax/editdetail');?>',
                        pdata,
                        function(data) {
                            $('#loader').hide();
                            if(data.status == 'OK:ORDERUPDATED'){
                                //alert('Transaction Success');
                                $('#trx_result').html('Transaction Success');
                                parent.$('#view_dialog').dialog('close');
                                parent.refreshTab();
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


    </script>

</head>
<body>
<div id="wrapper">
    <table id="main_table">
        <tbody>
            <tr>
                <td id="merchant_detail">

            <?php if(file_exists(FCPATH.'public/pickup/'.$main_info['merchant_trans_id'].'_address.jpg')): ?>
                <img id="address-pic" src="<?php print base_url(); ?>public/pickup/<?php print $main_info['merchant_trans_id'] ?>_address.jpg?<?php print time();?>" style="width:100%;height:auto">
                <div style="text-align:center;padding-top:8px;">
                    <span class="button rotate-picture" id="<?php print $main_info['merchant_trans_id'] ?>" style="cursor:pointer;">Rotate Picture</span>&nbsp;|&nbsp;
                    <span class="button rotate-picture" id="do_ocr" style="cursor:pointer;">OCR / Get Shipping Address from Picture</span>
                </div>
            <?php else : ?>


                    <table border="0" cellpadding="4" cellspacing="0" id="mainInfo">
                        <tbody>

                            <tr>
                                <td colspan="2">
                                    <img class="qr" src="<?php print base_url().'img/qr/'.$qr;?>" alt="<?php print $qr;?>">
                                    <br /><strong>Merchant Info</strong></td>
                            </tr>

                            <tr>
                                <td>
                                    Merchant Name:<br />
                                    <span class="fine"><?php print form_checkbox(array('name'=>'show_merchant','id'=>'show_merchant','value'=>$main_info['delivery_id'],'checked'=>$main_info['show_merchant'] ));?> Show in delivery slip</span>
                                </td>
                                <td>
                                    <?php print $main_info['merchant'];?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Online Store:<br />
                                    <span class="fine"><?php print form_checkbox(array('name'=>'show_shop','id'=>'show_shop','value'=>$main_info['delivery_id'],'checked'=>$main_info['show_shop'] ));?> Show in delivery slip</span>
                                </td>
                                <td>
                                    <?php print $main_info['app_name'];?>
                                </td>
                            </tr>
                            <tr>
                                <td>No Kode Penjualan Toko:</td>
                                <td>
                                    <?php
                                        if(preg_match('/^TRX_/', $main_info['merchant_trans_id'])){
                                            print '';
                                        }else{
                                            print $main_info['merchant_trans_id'];
                                        }
                                    ?>

                                </td>
                            </tr>
<?php
/*
    [mc_email] => ganti@bajuresmi.net.com.id
    [mc_street] => 2345678
    [mc_district] => Kebayoran
    [mc_city] => Jakarta Selatan
    [mc_province] => DKI
    [mc_country] => Indonesia
    [mc_zip] => 1234578
    [mc_phone] => 08765432
    [mc_mobile] => 2345678
    [contact_person]
*/

    //print_r($main_info);
$merchant_info = '';
$merchant_info = ($main_info['m_pic']=='')?$main_info['mc_pic'].'<br />':$main_info['m_pic'].'<br />';
$merchant_info .= ($main_info['m_street']=='')?$main_info['mc_street'].'<br />': $main_info['m_street'].'<br />';
$merchant_info .= ($main_info['m_district'] == '')?$main_info['mc_district'].'<br />':$main_info['m_district'].'<br />';
$merchant_info .= ($main_info['m_city'] == '')?$main_info['mc_city'].',':$main_info['m_city'].',';
$merchant_info .= ($main_info['m_zip']=='')?$main_info['mc_zip'].'<br />':$main_info['m_zip'].'<br />';
$merchant_info .= ($main_info['m_country']=='')?$main_info['mc_country'].'<br />':$main_info['m_country'].'<br />';
$merchant_info .= ($main_info['m_phone'] == '')?'Phone : '.$main_info['mc_phone'].'<br />':'Phone : '.$main_info['m_phone'].'<br />';
//$merchant_info .= ($main_info['m_mobile1'] == '')?'Mobile 1 : '.$main_info['mc_mobile1'].'<br />':'Mobile 1 : '.$main_info['m_mobile1'].'<br />';
//$merchant_info .= ($main_info['m_mobile2'] == '')?'Mobile 2 : '.$main_info['mc_mobile2'].'<br />':'Mobile 2 : '.$main_info['m_mobile2'].'<br />';


?>
                            <tr>
                                <td colspan="2">Store Detail:</td>
                            </tr>
                            <tr>
                                <td colspan="2"><?php print trim($merchant_info);?></td>
                            </tr>
                            <tr>
                                <td colspan="2" id="trx_result"></td>
                            </tr>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </td>
                <td id="order_detail">
                    <table width="100%" cellpadding="4" cellspacing="0" id="orderInfo">
                        <tbody>
                            <tr>
                                <td colspan="2"><strong>Delivery Info</strong></td>
                            </tr>
                            <tr>
                                <td class="row_label">Delivery Number:</td>
                                <td><?php print $main_info['delivery_id'];?>
                                    <input type="hidden" name="delivery_id" value="<?php print $main_info['delivery_id'];?>" id="delivery_id">
                                </td>
                            </tr>
                            <tr>
                                <td>Delivery Date:</td>
                                <td>
                                    <?php print form_input('assignment_date',$main_info['assignment_date'],'id="assignment_date"');?>
                            </tr>
                            <tr>
                                <td>Delivery Slot:</td>
                                <td>
                                    Requested : <?php print get_slot_range($main_info['buyerdeliveryslot']);?><br /><br />
                                    Assigned : <?php print get_slot_range($main_info['assignment_timeslot']);?></td>
                            </tr>
                            <tr>
                                <td class="row_label">Delivery Type:</td>
                                <td>
                                    <span id="delivery_type"><?php print $main_info['delivery_type'];?></span>&nbsp;&nbsp;&nbsp;&nbsp;
                                    <span id="set_delivery" style="cursor:pointer;text-decoration: underline;">set delivery type</span>
                                    <div id="delivery_option" style="display:none">
                                        <?php print $typeselect; ?>&nbsp;&nbsp;&nbsp;&nbsp;<span id="save_delivery" style="cursor:pointer;text-decoration: underline;">save</span>&nbsp;&nbsp;&nbsp;&nbsp;
                                        <span id="cancel_delivery" style="cursor:pointer;text-decoration: underline;">cancel</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="row_label">Delivery City:</td>
                                <td id="city_select">
                                    <?php print $cityselect; ?>
                                    <?php //print form_input('buyerdeliverycity',$main_info['buyerdeliverycity'],'id="buyerdeliverycity"');?>
                                </td>
                            </tr>
                            <tr>
                                <td class="row_label">Delivery Zone:</td>
                                <td id="zone_select">
                                    <?php print $zoneselect; ?>
                                    <?php //print form_input('buyerdeliveryzone',$main_info['buyerdeliveryzone'],'id="buyerdeliveryzone"');?>
                                </td>
                            </tr>
                            <tr>
                                <td>Cost Bearer<hr /><span class="fine">Ongkos Dibayar Oleh</span></td>
                                <td>

                                    <label for"delivery_bearer">Delivery Fee :</label>
                                    <span id="delivery_bearer_type"><?php print ucfirst($main_info['delivery_bearer']) ;?></span>&nbsp;&nbsp;&nbsp;&nbsp;
                                    <span id="set_delivery_bearer" style="cursor:pointer;text-decoration: underline;">set delivery bearer</span>
                                    <div id="delivery_bearer_option" style="display:none">
                                        <select id="delivery_bearer_select">
                                            <option value="merchant">Merchant</option>
                                            <option value="buyer">Buyer</option>
                                        </select>
                                        &nbsp;&nbsp;&nbsp;&nbsp;<span id="save_delivery_bearer" style="cursor:pointer;text-decoration: underline;">save</span>&nbsp;&nbsp;&nbsp;&nbsp;
                                        <span id="cancel_delivery_bearer" style="cursor:pointer;text-decoration: underline;">cancel</span>
                                    </div>

                                    <br />

                                    <label for"cod_bearer">COD / CCOD Surcharge Fee :</label>
                                    <span id="cod_bearer_type"><?php print ucfirst($main_info['cod_bearer']) ;?></span>&nbsp;&nbsp;&nbsp;&nbsp;
                                    <span id="set_cod_bearer" style="cursor:pointer;text-decoration: underline;">set COD bearer</span>
                                    <div id="cod_bearer_option" style="display:none">
                                        <select id="cod_surcharge_bearer_select">
                                            <option value="merchant">Merchant</option>
                                            <option value="buyer">Buyer</option>
                                        </select>
                                        &nbsp;&nbsp;&nbsp;&nbsp;<span id="save_cod_bearer" style="cursor:pointer;text-decoration: underline;">save</span>&nbsp;&nbsp;&nbsp;&nbsp;
                                        <span id="cancel_cod_bearer" style="cursor:pointer;text-decoration: underline;">cancel</span>
                                    </div>

                                </td>
                            </tr>

                            <tr>
                                <td colspan="2"><strong>Order Detail</strong></td>
                            </tr>


                        <?php if($main_info['delivery_type'] == 'PS'):?>
                            <tr>
                                <td class="row_label">Picked Up From</td>
                                <td><?php print ($main_info['recipient_name'] == "")?$main_info['buyer_name']:$main_info['recipient_name'];?></td>
                            </tr>
                            <tr>
                                <td>Pick Up Address</td>
                                <td><?php print $main_info['shipping_address'];?></td>
                            </tr>
                        <?php else: ?>

                            <tr>
                                <td class="row_label">Delivered To:</td>
                                <td><?php print form_input('recipient_name',($main_info['recipient_name'] == "")?$main_info['buyer_name']:$main_info['recipient_name'],'id="recipient_name"');?></td>
                            </tr>
                            <tr>
                                <td>Shipping Address:</td>
                                <td>
                                    <?php print form_textarea('shipping_address',$main_info['shipping_address'],'id="shipping_address"');?>
                                </td>
                            </tr>
                        <?php endif; ?>
                            <tr>
                                <td>How to Get There:</td>
                                <td>
                                    <?php print form_textarea('directions',$main_info['directions'],'id="directions"');?>
                                </td>
                            </tr>
                            <tr>
                                <td>Phone:</td>
                                <td>
                                    <?php
                                        print form_input('phone',$main_info['phone'],'id="phone"').'<br />';
                                        print form_input('mobile1',$main_info['mobile1'],'id="mobile1"').'<br />';
                                        print form_input('mobile2',$main_info['mobile2'],'id="mobile2"').'<br />';
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"><strong>Package Detail</strong></td>
                            </tr>

                            <tr>
                                <td class="row_label">Dimension:</td>
                                <td><?php print $main_info['width'].' cm x '.$main_info['height'].' cm x '.$main_info['length'].' cm';?></td>
                            </tr>
                            <tr>
                                <td class="row_label">Weight:</td>
                                <td><?php print ($main_info['weight'] == 0)?'<span id="weight">Unspecified</span>':'<span id="weight">'.get_weight_range($main_info['weight']).'</span>';?>&nbsp;&nbsp;&nbsp;&nbsp;
                                    <span id="set_weight" style="cursor:pointer;text-decoration: underline;">set weight</span>
                                    <div id="weight_option" style="display:none">
                                        <?php print $weightselect; ?>&nbsp;&nbsp;&nbsp;&nbsp;<span id="save_weight" style="cursor:pointer;text-decoration: underline;">save</span>&nbsp;&nbsp;&nbsp;&nbsp;<span id="cancel_weight" style="cursor:pointer;text-decoration: underline;">cancel</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <?php echo $this->table->generate(); ?>
                </td>
            </tr>
        </tbody>
    </table>
    <div id="loader" style="display:none;">
        <img src="<?php print base_url();?>assets/images/ajax_loader.gif" /> Processing...
    </div>

<!--
    <span id="note">* click to edit maroon colored bold field</span>
    <table border="0" cellpadding="4" cellspacing="0" id="signBox">
        <thead>
            <tr>
                <th>Created By</th>
                <th>Online Store</th>
                <th>Goods Received By</th>
                <th>Cash Received By</th>
                <th>Reporting</th>
                <th>Staff Dispatch Admin</th>
                <th>Finance</th>
                <th>Courier</th>
            </tr>
        </thead>
        <tbody>
            <tr style="height:40px;">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr id="sign_name">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </tbody>
    </table>
-->
</div>
</body>
</html>
