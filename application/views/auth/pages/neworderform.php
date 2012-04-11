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
            width:150px;
            text-align:right;
        }

        td#order_details input[type="text"]#description,
        td#order_details input[type="text"].item_desc{
            width:250px;
        }

        td#order_details input[type="text"]{
            width:150px;
        }

        input[type="text"]{
            border:thin solid grey;
        }

        .dataTable th{
            text-align:center;
        }

        td.sums{
            text-align:left;
        }

        td.lsums{
            text-align:right;
        }

        .sum_input, #recalc{
            margin-right:30px;
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
    </style>

    <?php echo $this->ag_asset->load_css('jquery-ui-1.8.16.custom.css','jquery-ui/flick');?>

    <?php echo $this->ag_asset->load_script('jquery-1.7.1.min.js');?>
    <?php echo $this->ag_asset->load_script('jquery.datatables.min.js','jquery-datatables');?>

    <?php echo $this->ag_asset->load_script('jquery-ui-1.8.16.custom.min.js','jquery-ui');?>
    <?php echo $this->ag_asset->load_script('jquery-ui-timepicker-addon.js','jquery-ui');?>
    <?php echo $this->ag_asset->load_script('jquery.jeditable.mini.js');?>

    
    <script>

    var dateBlock = <?php print getdateblock();?>;

    var sequence = 0;

    var total_price = 0;
    var total_price_discounted = 0;
    var total_discount = 0;
    var total_charges = 0;
    var total_tax = 0;
    var cod_cost = 0;

    $(document).ready(function() {
        $('.editable').editable('<?php print base_url();?>ajax/editdetail');

        $('#buyerdeliverytime').datetimepicker({
            numberOfMonths: 2,
            showButtonPanel: true,
            dateFormat:'yy-mm-dd',
            timeFormat: 'hh:mm:ss',
            onSelect:function(dateText, inst){
                
                //console.log(dateBlock);
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
            
            //console.log(indate);
            console.log(window.dateBlock);
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
            source: '<?php print site_url('ajax/getbuyer')?>',
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

        $( '#buyer_email' ).autocomplete({
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
                { merchant_id: merchant_id }, 
                function(data) {
                    console.log(data.data);
                    $('#application_id').html(data.data);
                },'json');
        }

        $('#add_item').click(function(){
            sequence++;
            var row = '<tr id="trx_'+ sequence+'">';
                row += '<td class="item_form"><input type="text" class="item_desc" name="description" value="'+ $('#description').val() +'" /></td>';
                row += '<td class="item_form"><input type="text" class="item_qty" name="quantity" value="'+ $('#quantity').val() +'" /></td>';
                row += '<td><input type="text" class="item_unit_price" name="unit_price" value="'+ $('#unit_price').val() +'"  /></td>';
                row += '<td class="item_form"><input type="text" class="item_pct_disc orange" name="unit_pct_disc" value="'+ $('#unit_percent_discount').val() +'" /></td>';
                row += '<td><input type="text" class="item_nom_disc orange" name="unit_nom_disc" value="'+ $('#unit_nominal_discount').val() +'"  /></td>';
                row += '<td><input type="text" class="item_total" name="unit_total" value="'+ $('#unit_total').val() +'"  /><button name="add_item" type="button" id="remove_item" onClick="removeRow(\'trx_'+ sequence+'\');" >-</button></td>';
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


    });

    function submitorder(){
        var result = validate();
        if(result[0]){
            alert("Processing...");
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
            pdata.buyerdeliverytime = $('#buyerdeliverytime').val();
            pdata.direction = $('#direction').val();
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

            $.post('<?php print site_url('ajax/neworder');?>',
                pdata, 
                function(data) {
                    alert(data.status);
                },'json');

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

    function validate(){
            if($('#merchant_id').val() === 'undefined' || $('#merchant_id').val() == '' || $('#merchant_id').val() == 0 || $('#merchant_id').val() == null || $('#merchant_id').val() === 'NaN'){
                return [false,'Merchant Unspecified'];
            }
            /*
            if($('#buyer_id').val() === 'undefined' || $('#buyer_id').val() == '' || $('#buyer_id').val() == 0 || $('#buyer_id').val() == null || $('#buyer_id').val() === 'NaN'){
                return [false,'Buyer Unspecified'];
            }
            */
            if($('#app_id').val() == 0){
                return [false, 'Application Domain Invalid'];
            }
            return [true,''];
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

        total_charges = (total_price - total_discount) + total_tax + cod_cost;

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
                                <td>Online Store:</td>
                                <td>
                                    Merchant ID : <span id="merchant_id_txt"></span><br />
                                    <input type="text" id="merchant_name" name="merchant_name" value="" />
                                    <input type="hidden" value="" id="merchant_id" name="merchant_id" />
                                    <input type="hidden" value="" id="merchant_fullname" name="merchant_fullname" />
                                    <input type="hidden" value="" id="merchant_email" name="merchant_email" />
                                </td>
                            </tr>
                            <tr>
                                <td>Store Detail:</td>
                                <td class="editable" id="application_id">
                                    <select name="app_id" id="app_id">
                                        <option value="0">Select application domain</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"><strong>Delivery Info</strong></td>
                            </tr>
                            <tr>
                                <td>Delivery Date:</td>
                                <td>
                                    <input type="text" id="buyerdeliverytime" name="buyerdeliverytime" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td class="row_label">Delivery City:</td>
                                <td>
                                    <input type="text" id="buyerdeliverycity" name="buyerdeliverycity" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td class="row_label">ZIP:</td>
                                <td>
                                    <input type="text" id="buyerdeliveryzip" name="buyerdeliveryzip" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td class="row_label">Delivery Zone:</td>
                                <td>
                                    <input type="text" id="buyerdeliveryzone" name="buyerdeliveryzone" value="" />
                                </td>
                            </tr>
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
                                <td class="row_label">Buyer Name:</td>
                                <td>
                                    Buyer ID : <span id="buyer_id_txt"></span><br />
                                    <input type="hidden" value="" id="buyer_id" name="buyer_id" />
                                    <input type="text" id="buyer_name" name="buyer_name" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td class="row_label">Buyer Email:</td>
                                <td>
                                    <input type="text" id="buyer_email" name="buyer_email" value="" />
                                    <?php // print form_button(array('name'=>'add_buyer','content'=>'Create New Buyer','id'=>'create_user'));?>
                                </td>
                            </tr>
                            <tr>
                                <td class="row_label">Delivered To:</td>
                                <td>
                                    <input type="text" id="recipient_name" name="recipient_name" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td>Shipping Address:</td>
                                <td>
                                    <textarea id="shipping_address"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>Direction:</td>
                                <td>
                                    <textarea id="direction"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>Phone:</td>
                                <td>
                                    <input type="text" id="phone" name="phone" value="" />
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
                                <th rowspan="2">Total
                                    <select name="currency" id="currency" >
                                        <option value="IDR">IDR</option>
                                        <option value="USD">USD</option>
                                    </select><button name="recalc" type="button" id="recalc" >Recalc</button>
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
                                <td class='item_form' style="text-align:left;"><input type="text" name="unit_total" value="" id="unit_total"  /><button name="add_item" type="button" id="add_item" >+</button></td>
                            </tr>
                            <tr id="calc_data">
                                <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td class='lsums'>Total Price ( before discount )</td><td class='sums'><input type="text" name="total_price" value="" id="total_price" class="sum_input"  /></td>
                            </tr>
                            <tr class="detail_row">
                                <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td class='lsums'>Total Discount<br /><input type="checkbox" id="fixed_discount">Set Fixed</td><td class='sums'><input type="text" name="total_discount" value="" id="total_discount"  class="sum_input orange" /></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td class='lsums'>Tax <input type="text" name="percent_tax" value="" id="percent_tax" class="sum_input" style="width:40px;margin-right:2px;" />% Total Tax </td><td class='sums'><input type="text" name="total_tax" value="" id="total_tax" class="sum_input"  /></td>
                            </tr>
                            <tr class="detail_row">
                                <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td class='lsums'>COD Charges</td><td class='sums'><input type="text" name="cod_cost" value="" id="cod_cost" class="sum_input"  /></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td class='lsums'>&nbsp;</td><td class='lsums'>Total Charges</td><td class='sums'><input type="text" name="total_charges" value="" id="total_charges" class="sum_input"  /></td>
                            </tr>
                        </tbody>
                    </table>
                    <?php //echo $this->table->generate(); ?>
                </td>
            </tr>
        </tbody>
    </table>
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
