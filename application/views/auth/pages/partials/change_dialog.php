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
