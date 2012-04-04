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
            width:220px;
            height:75px;
        }

        td#order_details input[type="text"]#quantity{
            width:50px;
        }

        td#order_details input[type="text"]#description{
            width:350px;
        }

        td#order_details input[type="text"]{
            width:220px;
        }

        .dataTable th{
            text-align:center;
        }

        td.sums{
            text-align:right;
        }

        td.item_form{
            text-align:center;
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
            }
        });

        $( '#buyer_name' ).autocomplete({
            source: '<?php print site_url('ajax/getbuyer')?>',
            method: 'post',
            minLength: 2,
            select:function(event,ui){
                $('#buyer_id').val(ui.item.id);
                $('#buyer_id_txt').html(ui.item.id);                
                $('#buyer_email').val(ui.item.email)
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

    });



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
                                    <?php print form_button('add_buyer','Create Buyer');?>
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
                    <?php echo $this->table->generate(); ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
</body>
</html>
