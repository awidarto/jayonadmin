<div id="reschedule_dialog" title="Reschedule Order">
    <table style="width:100%;border:0;margin:0;">
        <tr>
            <td style="width:250px;vertical-align:top">
                Delivery Orders :
            </td>
            <td>
                Reschedule Delivery Date to :<br />
            </td>
        </tr>
        <tr>
            <td style="overflow:auto;width:250px;vertical-align:top">
                <ul id="rescheduled_trans_list" style="border-top:thin solid grey;list-style-type:none;padding-left:0px;"></ul>
            </td>
            <td style="border:0;margin:0;">
                <input id="rescheduled_deliverytime" type="text" value=""><br />
                <div id="date_time_display"></div>
                Requested by :<br />
                <?php print form_dropdown('req_by',$this->config->item('actors_title'),'','id="rs_req_by"');?><br />
                Requester name :<br />
                <?php print form_input('req_name','','id="rs_req_name"');?><br />
                Request Note :<br />
                <?php print form_textarea('req_note','','id="rs_req_note"');?><br />
            </td>
        </tr>
    </table>
</div>

<div id="confirm_dialog" title="Confirm Delivery Orders">
    <table style="width:100%;border:0;margin:0;">
        <tr>
            <td style="width:250px;vertical-align:top">
                Delivery Orders :
            </td>
            <td style="width:250px;vertical-align:top">
                Requested by :
            </td>
        </tr>
        <tr>
            <td style="overflow:auto;width:250px;vertical-align:top">
                <ul id="confirm_list" style="border-top:thin solid grey;list-style-type:none;padding-left:0px;"></ul>
            </td>
            <td style="width:250px;vertical-align:top">
                <?php print form_dropdown('req_by',$this->config->item('actors_title'),'','id="cf_req_by"');?><br />
                Requester name :<br />
                <?php print form_input('req_name','','id="cf_req_name"');?><br />
                Request Note :<br />
                <?php print form_textarea('req_note','','id="cf_req_note"');?><br />
            </td>
        </tr>
    </table>
</div>

<div id="cancel_dialog" title="Cancel Delivery Orders">
    <table style="width:100%;border:0;margin:0;">
        <tr>
            <td style="width:250px;vertical-align:top">
                Delivery Orders :
            </td>
            <td style="width:250px;vertical-align:top">
                Requested by :
            </td>
        </tr>
        <tr>
            <td style="overflow:auto;width:250px;vertical-align:top">
                <ul id="cancel_list" style="border-top:thin solid grey;list-style-type:none;padding-left:0px;"></ul>
            </td>
            <td style="width:250px;vertical-align:top">
                <?php print form_dropdown('req_by',$this->config->item('actors_title'),'','id="cl_req_by"');?><br />
                Requester name :<br />
                <?php print form_input('req_name','','id="cl_req_name"');?><br />
                Request Note :<br />
                <?php print form_textarea('req_note','','id="cl_req_note"');?><br />
            </td>
        </tr>
    </table>
</div>

<div id="view_dialog" title="Order Detail" style="overflow:hidden;padding:8px;">
    <input type="hidden" value="" id="print_id" />
    <iframe id="view_frame" name="print_frame" width="100%" height="100%"
    marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto"
    title="Dialog Title">Your browser does not suppr</iframe>
</div>

<div id="print_dialog" title="Print Delivery Slip" style="overflow:hidden;padding:8px;">
    <input type="hidden" value="" id="print_id" />
    <iframe id="print_frame" name="print_frame" width="100%" height="100%"
    marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto"
    title="Dialog Title">Your browser does not suppr</iframe>
</div>

<div id="label_dialog" title="Print Label" style="overflow:hidden;padding:8px;">
    <div style="border-bottom:thin solid #ccc;">
        Print options :
        <label>Res
                <input type="text" class="label-opt" value="<?php print $resolution ?>" id="label_resolution" /> ppi
        </label>
        <label>Width
                <input type="text" class="label-opt" value="<?php print $cell_width ?>" id="label_cell_width" /> px
        </label>
        <label>Height
                <input type="text" class="label-opt" value="<?php print $cell_height ?>" id="label_cell_height" /> px
        </label>
        <label>Columns
                <input type="text" class="label-opt" value="<?php print $columns ?>" id="label_columns" />
        </label>
        <label>Right
                <input type="text" class="label-opt" value="<?php print $margin_right ?>" id="label_margin_right" /> px
        </label>
        <label>Bottom
                <input type="text" class="label-opt" value="<?php print $margin_bottom ?>" id="label_margin_bottom" /> px
        </label>
        <label>Font Size
                <input type="text" class="label-opt" value="<?php print $font_size ?>" id="label_font_size" /> pt
        </label>

        <label>Code Type
                <?php print form_dropdown('', array( 'barcode'=>'Barcode', 'qr'=>'QR Code' ), $code_type, 'id="label_code_type"'  ) ?>
        </label>

        <button id="label_refresh">refresh</button>
        <button id="label_default">make default</button>
    </div>
    <input type="hidden" value="" id="label_id" />
    <iframe id="label_frame" name="label_frame" width="100%" height="90%"
    marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto"
    title="Dialog Title">Your browser does not suppr</iframe>
</div>

<div id="assign_pickup_dialog" title="Assign Pickup Date to Selection">
    <table style="width:100%;border:0;margin:0;">
        <tr>
            <td style="width:250px;vertical-align:top">
                Delivery Orders :
            </td>
            <td>
                Pickup Date :<br />
            </td>
        </tr>
        <tr>
            <td style="overflow:auto;width:250px;vertical-align:top">
                <ul id="trans_pickup_list" style="border-top:thin solid grey;list-style-type:none;padding-left:0px;"></ul>
            </td>
            <td style="border:0;margin:0;">
                <input id="assign_pickuptime" type="text" value=""><br />
                <div id="date_pickup_display"></div>
            </td>
        </tr>
    </table>
</div>

<div id="multi_action_dialog" title="Change Selection">
    <table style="width:100%;border:0;margin:0;">
        <tr>
            <td style="width:250px;vertical-align:top">
                Delivery Orders :
            </td>
            <td>
                Action
            </td>
            <td>
                Requester
            </td>
        </tr>
        <tr>
            <td style="overflow:auto;width:250px;vertical-align:top">
                <ul id="multi_item_list" style="border-top:thin solid grey;list-style-type:none;padding-left:0px;"></ul>
            </td>
            <td style="border:0;margin:0;vertical-align:top">
                <b>Reschedule To</b><br />
                <input id="multi_reschedule" class="multi_date" type="text" value=""><br /><br />
                <button id="doMultiReschedule" class="ui-button-text">Reschedule</button>
                &nbsp;&nbsp;<span id="process_reschedule" style="display:none;">processing...</span>
                <br />
                <hr>
                <b>Change Status</b>
                <br />
                <?php
                    $status_list = $this->config->item('status_changes');
                    $status_list = array_keys($status_list);

                    $sl = array();
                    foreach($status_list as $s){
                        $sl[$s]=$s;
                    }

                    $actor = $this->config->item('actors_title');


                    print 'Actor <br />';
                    print form_dropdown('actor',$actor,'','id="multi_chgactor"').'<br />';
                    print ' New Status<br />';
                    print form_dropdown('new_status',$sl,'','id="multi_new_status"');

                ?>
                <button id="doMultiChangeStatus" class="ui-button-text">Change</button>
                &nbsp;&nbsp;<span id="process_chgstatus" style="display:none;">processing...</span>
                <br /><br />
                <hr>
                <b>Change Pick Up Status</b>
                <br />
                <?php
                    $status_list = $this->config->item('pickup_status_changes');
                    $status_list = array_keys($status_list);

                    $sl = array();
                    foreach($status_list as $s){
                        $sl[$s]=$s;
                    }

                    $actor = $this->config->item('actors_title');


                    print 'Actor <br />';
                    print form_dropdown('actor',$actor,'','id="multi_puactor"').'<br />';
                    print ' New Status<br />';
                    print form_dropdown('new_status',$sl,'','id="multi_punew_status"');

                ?>
                <button id="doMultiPUChangeStatus" class="ui-button-text">Change</button>
                &nbsp;&nbsp;<span id="process_puchgstatus" style="display:none;">processing...</span>
                <br /><br />
                <hr>
                <b>Change Warehouse Status</b>
                <br />
                <?php
                    $status_list = $this->config->item('warehouse_status_changes');
                    $status_list = array_keys($status_list);

                    $sl = array();
                    foreach($status_list as $s){
                        $sl[$s]=$s;
                    }

                    $actor = $this->config->item('actors_title');


                    print 'Actor <br />';
                    print form_dropdown('actor',$actor,'','id="multi_whactor"').'<br />';
                    print ' New Status<br />';
                    print form_dropdown('new_status',$sl,'','id="multi_whnew_status"');

                ?>
                <button id="doMultiWHChangeStatus" class="ui-button-text">Change</button>
                &nbsp;&nbsp;<span id="process_whchgstatus" style="display:none;">processing...</span>

            </td>
            <td style="overflow:auto;max-width:350px;width:350px;vertical-align:top">
                Requested by :<br />
                <?php print form_dropdown('req_by',$this->config->item('actors_title'),'','id="multi_rs_req_by"');?><br />
                Requester name :<br />
                <?php print form_input('req_name','','id="multi_rs_req_name"');?><br />
                Request Note :<br />
                <?php print form_textarea('req_note','','id="multi_rs_req_note"');?><br />

            </td>
        </tr>
    </table>
</div>
