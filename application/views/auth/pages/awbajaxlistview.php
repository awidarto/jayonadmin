
<?php echo $this->ag_asset->load_css('font-awesome.min.css');?>

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

    $(document).ready(function() {

        $('#date_from').datepicker({ dateFormat: 'yy-mm-dd' });
        $('#date_to').datepicker({ dateFormat: 'yy-mm-dd' });

        $( '#merchant_name' ).autocomplete({
            source: '<?php print site_url('ajax/getmerchant')?>',
            method: 'post',
            minLength: 2,
            select:function(event,ui){
                $('#merchant_id').val(ui.item.id);
                $('#merchant_id_txt').html(ui.item.id);
                $('#merchant_fullname').val(ui.item.fullname);
                $('#merchant_email').val(ui.item.email);
                $('#merchant_name').val(ui.item.value);
            }
        });


        var oTable = $('.dataTable').dataTable(
            {
                "bProcessing": true,
                "bServerSide": true,
                "sAjaxSource": "<?php print site_url($ajaxurl);?>",
                "oLanguage": { "sSearch": "Search "},
                "sPaginationType": "full_numbers",
                "sDom": 'l<"clear">frtip',
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


        $('#get_date_range').on('click',function(){

            if($('#merchant_id').val() == ''){
                alert('Please specify Merchant and valid Merchant ID');
            }else{
                $('#generating').show();

                $.post('<?php print site_url('admin/awb/ajaxgenerate');?>',
                    {
                        merchant_id : $('#merchant_id').val(),
                        merchant_name : $('#merchant_name').val(),
                        gen_qty : $('#gen_qty').val(),
                        date_from : $('#date_from').val(),
                        date_to : $('#date_to').val()
                    },
                    function(data) {
                        if(data.result = 'OK'){
                            refreshTab();
                        }
                        $('#generating').hide();
                    /*optional stuff to do after success */
                    },'json');

            }

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


<div id="form">
    <table>
        <tr>
            <td>

                <table style="width:500px;" id="recon_select" cellspacing="0" >
                    <tr>
                        <td>Merchant ID :</td>
                        <td colspan="3">
                            <span id="merchant_id_txt"></span><br />
                            <input type="text" value="" id="merchant_name" name="merchant_name" />

                            <input type="hidden" value="" id="merchant_id" name="merchant_id" /><br />
                            <input type="hidden" value="" id="merchant_name" name="merchant_name" /><br />
                            <input type="hidden" value="" id="merchant_email" name="merchant_email" /><br />

                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>Amount Generated :</td>
                        <td colspan="3">
                            <input type="text" value="1000" id="gen_qty" name="gen_qty" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">&nbsp;</td>
                    </tr>

                    <tr class="dark">
                        <td>From Date</td>
                        <td><?php print form_input(array('name'=>'date_from','id'=>'date_from','class'=>'text','value'=>''));?></td>
                        <td><?php print 'To '.form_input(array('name'=>'date_to','id'=>'date_to','class'=>'text','value'=>''));?></td>
                        <td><span id="get_date_range" class="button action_link" style="cursor:pointer;">Generate</span></td>
                    </tr>
                    <tr>
                        <td colspan="4">&nbsp;</td>
                    </tr>

                    <tr id="generating" style="display:none">
                        <td colspan="4">
                            <img src="<?php print base_url();?>assets/images/ajax_loader.gif" />
                            Generating data, please wait, this will take a while.
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">&nbsp;</td>
                    </tr>

                    <tr>
                        <td colspan="4" style="text-align:right;">
                            <span class="button">Download CSV / Excel</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align:right;">
                            <p>
                                Set merchant id / name, from date and to date before downloading CSV / Excel file.
                            </p>
                        </td>
                    </tr>

                </table>


            </td>
            <td style="vertical-align:top;">

            </td>
        </tr>
    </table>
</div>

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
