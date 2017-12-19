<div id="changestatus_dialog" title="Change Delivery Status">
    <table style="width:100%;border:0;margin:0;">
        <tr>
            <td style="width:250px;vertical-align:top">
                <strong>Delivery ID : </strong><span id="change_id"></span><br /><br />
                <?php
                    $status_list = $this->config->item('status_changes');
                    $status_list = array_keys($status_list);

                    $sl = array();
                    foreach($status_list as $s){
                        $sl[$s]=$s;
                    }

                    $actor = $this->config->item('actors_title');


                    print 'Actor <br />';
                    print form_dropdown('actor',$actor,'','id="actor"').'<br /><br />';
                    print ' New Status<br />';
                    print form_dropdown('new_status',$sl,'','id="new_status"');

                ?>
            </td>
            <td>
                <label for="chg_note">Note</label>
                <textarea name="chg_note" id="chg_note" style="width:100%;height:100%"></textarea>
            </td>
        </tr>
    </table>
</div>

<div id="puchangestatus_dialog" title="Change Pick Up Status">
    <table style="width:100%;border:0;margin:0;">
        <tr>
            <td style="width:250px;vertical-align:top">
                <strong>Delivery ID : </strong><span id="puchange_id"></span><br /><br />
                <?php
                    $status_list = $this->config->item('pickup_status_changes');
                    $status_list = array_keys($status_list);

                    $sl = array();
                    foreach($status_list as $s){
                        $sl[$s]=$s;
                    }

                    $actor = $this->config->item('actors_title');


                    print 'Actor <br />';
                    print form_dropdown('actor',$actor,'','id="actor"').'<br /><br />';
                    print ' New Status<br />';
                    print form_dropdown('new_status',$sl,'','id="punew_status"');

                ?>
            </td>
            <td>
                <label for="puchg_note">Note</label>
                <textarea name="puchg_note" id="puchg_note" style="width:100%;height:100%"></textarea>
            </td>
        </tr>
    </table>
</div>

<div id="whchangestatus_dialog" title="Change Warehouse Status">
    <table style="width:100%;border:0;margin:0;">
        <tr>
            <td style="width:250px;vertical-align:top">
                <strong>Delivery ID : </strong><span id="whchange_id"></span><br /><br />
                <?php
                    $status_list = $this->config->item('warehouse_status_changes');
                    $status_list = array_keys($status_list);

                    $sl = array();
                    foreach($status_list as $s){
                        $sl[$s]=$s;
                    }

                    $actor = $this->config->item('actors_title');


                    print 'Actor <br />';
                    print form_dropdown('actor',$actor,'','id="actor"').'<br /><br />';
                    print ' New Status<br />';
                    print form_dropdown('new_status',$sl,'','id="whnew_status"');

                ?>
            </td>
            <td>
                <label for="whchg_note">Note</label>
                <textarea name="whchg_note" id="whchg_note" style="width:100%;height:100%"></textarea>
            </td>
        </tr>
    </table>
</div>
<!-- updatesahlan -->
<div id="crchangestatus_dialog" title="Change Courir Status">
    <table style="width:100%;border:0;margin:0;">
        <tr>
            <td style="width:250px;vertical-align:top">
                <strong>Delivery ID : </strong><span id="crchange_id"></span><br /><br />
                <?php
                    $status_list = $this->config->item('courier_status_changes');
                    $status_list = array_keys($status_list);

                    $sl = array();
                    foreach($status_list as $s){
                        $sl[$s]=$s;
                    }

                    $actor = $this->config->item('actors_title');


                    print 'Actor <br />';
                    print form_dropdown('actor',$actor,'','id="actor"').'<br /><br />';
                    print ' New Status<br />';
                    print form_dropdown('new_status',$sl,'','id="crnew_status"');

                ?>
            </td>
            <td>
                <label for="crchg_note">Note</label>
                <textarea name="crchg_note" id="crchg_note" style="width:100%;height:100%"></textarea>
            </td>
        </tr>
    </table>
</div>
<!-- end -->
<div id="setzone_dialog" title="Set Zone">
    <strong>Delivery ID : </strong><span id="setzone_id"></span><br /><br />
    <table style="width:100%;border:0;margin:0;">
        <tr>
            <td style="width:250px;vertical-align:top">
                City
                <div id="set_city_select">
                    <?php
                        $city = get_city_options();
                        $zone = array(''=>'Select Zone');
                        $cityselect = form_dropdown('buyerdeliverycity',$city,null,'id="setbuyerdeliverycity"');
                        $zoneselect = form_dropdown('buyerdeliveryzone',$zone,null,'id="setbuyerdeliveryzone"');
                        print $cityselect;
                    ?>
                </div>
            </td>
            <td >
                Zone
                <div id="set_zone_select">
                    <?php
                        print $zoneselect;
                    ?>
                </div>
            </td>
        </tr>
    </table>
</div>

<div id="setcity_dialog" title="Set City">
    <table style="width:100%;border:0;margin:0;">
        <tr>
            <td style="width:250px;vertical-align:top">
                <strong>Delivery ID : </strong><span id="setcity_id"></span><br /><br />
                <?php
                    $status_list = $this->config->item('pickup_status_changes');
                    $status_list = array_keys($status_list);

                    $sl = array();
                    foreach($status_list as $s){
                        $sl[$s]=$s;
                    }

                    $actor = $this->config->item('actors_title');


                    print 'Actor <br />';
                    print form_dropdown('actor',$actor,'','id="actor"').'<br /><br />';
                    print ' New Status<br />';
                    print form_dropdown('new_status',$sl,'','id="punew_status"');

                ?>
            </td>
            <td>
                <label for="puchg_note">Note</label>
                <textarea name="puchg_note" id="puchg_note" style="width:100%;height:100%"></textarea>
            </td>
        </tr>
    </table>
</div>

<div id="setdeliverydate_dialog" title="Set Delivery Date">
    <strong>Delivery ID : </strong><span id="setdeliverydate_id"></span><br /><br />
    <table style="width:100%;border:0;margin:0;">
        <tr>
            <td >
                <div id="set_deliverydate_select">
                    <input id="assign_set_deliverytime" type="text" value=""><br />
                </div>
            </td>
        </tr>
    </table>
</div>

<div id="setweight_dialog" title="Set Weight">
    <strong>Delivery ID : </strong><span id="setweight_id"></span><br /><br />
    <table style="width:100%;border:0;margin:0;">
        <tr>
            <td >
                Weight
                <div id="set_weight_select">
                </div>
            </td>
        </tr>
    </table>
</div>

<div id="setdeliverytype_dialog" title="Set Delivery Type">
    <strong>Delivery ID : </strong><span id="setdeliverytype_id"></span><br /><br />
    <table style="width:100%;border:0;margin:0;">
        <tr>
            <td >
                Delivery Type
                <div id="set_type_select">
                    <?php

                        $delivery_type = array(
                            '0'=>'Select delivery type',
                            'COD'=>'COD',
                            'CCOD'=>'Credit Card On Delivery',
                            'Delivery Only'=>'Delivery Only',
                            'PS'=>'Pick Up Supply'
                        );

                        $typeselect = form_dropdown('delivery_type',$delivery_type,null,'id="set_delivery_type"');

                        print $typeselect;
                    ?>
                    <select name="sub_cod" id ="sub_cod" style="display:none">
                        <option value="cash">Tunai</option>
                        <option value="debit">Debit</option>
                    </select>
                    <select name="sub_ccod" id="sub_ccod" style="display:none">
                        <option value="full">Pembayaran Penuh</option>
                        <option value="installment">Cicilan</option>
                    </select>
                    <select name="sub_provider" id="sub_provider" style="display:none">
                        <option value="BCA">BCA</option>
                        <option value="Mandiri">Bank Mandiri</option>
                    </select>
                </div>
            </td>
            <td>
                <div id="weight_selection">
                </div>
                <div id="weight_selection">
                </div>
            </td>
        </tr>
    </table>
</div>
<!-- update sahlan -->
<div id="movefoto_dialog" title="Move Delivery Picture">
    <table style="width:100%;border:0;margin:0;">
        <tr>
            <td style="width:250px;vertical-align:top">
                <strong>Delivery ID : </strong><span id="mvfoto_id"></span><br /><br />
                <div id="lsfoto_id" style="padding-left:10px;"></div>
            </td>
            <td>
                <label for="req_deliveryid">Move foto to Delivery ID</label>
                <input type="textarea" name="parent_id" id="req_deliveryid" style="width:100%;height:100%"></input>
            </td>
        </tr>
    </table>
</div>

<div id="deletefoto_dialog" title="Delete Delivery Picture">
    <table style="width:100%;border:0;margin:0;">
        <tr>
            <td style="width:250px;vertical-align:top">
                <strong>Delivery ID : </strong><span id="delfoto_id"></span><br /><br />
                <div id="listfoto_id" style="padding-left:10px;"></div>
            </td>
            <td>
                <label for="del_deliveryId">Select to Delete foto</label>
                <!-- <textarea name="del_deliveryId" id="del_deliveryId" style="width:100%;height:100%"></textarea> -->
            </td>
        </tr>
    </table>
</div>

<div id="editorder_dialog" title="Change Delivery Order">
    <table style="width:100%;border:0;margin:0;">
        <tr>
            <td style="width:250px;vertical-align:top">
                <strong>Delivery ID : </strong><span id="editorder_id"></span><br /><br />
                <strong>Receiver / Note : </strong><input type="textarea" name="chg_delivery_note" id="receiverId" style="width:100%;height:100%"></input><br><br/>
                <strong>Latitude : </strong><input type="textarea" name="chg_latitude" id="latitudeId" style="width:100%;height:100%"></input><br>
                <strong>Longitude : </strong><input type="textarea" name="chg_longitude" id="longitudeId" style="width:100%;height:100%"></input><br><br/>
                <strong>Delivery Time : </strong><input type="textarea" name="chg_deliverytime" id="deliverytimeId" style="width:100%;height:100%"></input><br>
            </td>
            <td style="width:70px;">
                
            </td>

            <td>
                <strong>History Delivery Note: </strong>
                <div id="chg_history" style="padding-left:20px;"></div>
                <br>
                <strong>History Delivery Log: </strong>
                <div id="chg_log" style="padding-left:20px;"></div>
                <br>
                <label for="req_deliveryid">Move Log to Delivery ID</label>
                <input type="textarea" name="deliveryId" id="chg_deliveryId" style="width:100%;height:100%"></input>
            </td>
        </tr>
    </table>
</div>
<!-- end -->
