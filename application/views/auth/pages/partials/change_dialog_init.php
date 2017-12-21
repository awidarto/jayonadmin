        $('#changestatus_dialog').dialog({
            autoOpen: false,
            height: 250,
            width: 600,
            modal: true,
            buttons: {
                "Confirm Changes": function() {
                    var delivery_id = $('#change_id').html();

                    $.post('<?php print site_url('admin/delivery/ajaxchangestatus');?>',{
                        'delivery_id':delivery_id,
                        'new_status': $('#new_status').val(),
                        'actor': $('#actor').val(),
                        'req_note' : $('#chg_note').val()
                    }, function(data) {
                        if(data.result == 'ok'){
                            //redraw table
                            oTable.fnDraw();
                            $('#changestatus_dialog').dialog( "close" );
                        }
                    },'json');
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
                //allFields.val( "" ).removeClass( "ui-state-error" );
                $('#confirm_list').html('');
            }
        });

        $('#puchangestatus_dialog').dialog({
            autoOpen: false,
            height: 250,
            width: 600,
            modal: true,
            buttons: {
                "Confirm Changes": function() {
                    var delivery_id = $('#puchange_id').html();

                    console.log($('#punew_status').val());

                    $.post('<?php print site_url('admin/delivery/ajaxpuchangestatus');?>',{
                        'delivery_id':delivery_id,
                        'new_status': $('#punew_status').val(),
                        'actor': $('#actor').val(),
                        'req_note' : $('#puchg_note').val()
                    }, function(data) {
                        if(data.result == 'ok'){
                            //redraw table
                            oTable.fnDraw();
                            $('#puchangestatus_dialog').dialog( "close" );
                        }
                    },'json');
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
                //allFields.val( "" ).removeClass( "ui-state-error" );
                $('#confirm_list').html('');
            }
        });

        $('#whchangestatus_dialog').dialog({
            autoOpen: false,
            height: 250,
            width: 600,
            modal: true,
            buttons: {
                "Confirm Changes": function() {
                    var delivery_id = $('#whchange_id').html();

                    $.post('<?php print site_url('admin/delivery/ajaxwhchangestatus');?>',{
                        'delivery_id':delivery_id,
                        'new_status': $('#whnew_status').val(),
                        'actor': $('#actor').val(),
                        'req_note' : $('#whchg_note').val()
                    }, function(data) {
                        if(data.result == 'ok'){
                            //redraw table
                            oTable.fnDraw();
                            $('#whchangestatus_dialog').dialog( "close" );
                        }
                    },'json');
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
                //allFields.val( "" ).removeClass( "ui-state-error" );
                $('#confirm_list').html('');
            }
        });
<!-- //updatesahlan -->
        $('#crchangestatus_dialog').dialog({
            autoOpen: false,
            height: 250,
            width: 600,
            modal: true,
            buttons: {
                "Confirm Changes": function() {
                    var delivery_id = $('#crchange_id').html();

                    $.post('<?php print site_url('admin/delivery/ajaxcrchangestatus');?>',{
                        'delivery_id':delivery_id,
                        'new_status': $('#crnew_status').val(),
                        'actor': $('#actor').val(),
                        'req_note' : $('#crchg_note').val()
                    }, function(data) {
                        if(data.result == 'ok'){
                            //redraw table
                            oTable.fnDraw();
                            $('#crchangestatus_dialog').dialog( "close" );
                        }
                    },'json');
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
                //allFields.val( "" ).removeClass( "ui-state-error" );
                $('#confirm_list').html('');
            }
        });

        $('#movefoto_dialog').dialog({
            autoOpen: false,
            height: 250,
            width: 600,
            modal: true,
            buttons: {
                "Confirm Changes": function() {
                    var delivery_id = $('#lsfoto_id').val();
                    var ids = [];
                    var count = 0;
                    $('.img-select:checked').each(function(){
                        ids.push(this.value);
                        count++;
                    });
                    if(count > 0){
                        $.post('<?php print site_url('admin/delivery/ajaxmovefoto');?>',{
                            'delivery_id':delivery_id,
                            'parent_id': $('#req_deliveryid').val(),
                            '_id':ids,
                        }, function(data) {
                            if(data.result == 'ok'){
                                //redraw table
                                oTable.fnDraw();
                                $('#movefoto_dialog').dialog( "close" );
                            }
                        },'json');
                    }else{
                        alert('Please select one or more Foto');
                    }
                },
            },
        });

        $('#deletefoto_dialog').dialog({
            autoOpen: false,
            height: 250,
            width: 600,
            modal: true,
            buttons: {
                "Confirm Changes": function() {
                    var delivery_id = $('#delfoto_id').html();
                    var ids = [];
                    var count = 0;
                    $('.img-select:checked').each(function(){
                        ids.push(this.value);
                        count++;
                    });
                    if(count > 0){
                        $.post('<?php print site_url('admin/delivery/ajaxdelfoto');?>',{
                            'delivery_id':delivery_id,
                            'parent_id': $('#req_deliveryid').val(),
                            '_id':ids,
                        }, function(data) {
                            if(data.result == 'ok'){
                                //redraw table
                                oTable.fnDraw();
                                $('#deletefoto_dialog').dialog( "close" );
                            }
                        },'json');
                    }else{
                        alert('Please select one or more Foto');
                    }
                },
            },
        });


        $('#editorder_dialog').dialog({
            autoOpen: false,
            height: 550,
            width: 600,
            modal: true,
            buttons: {
                "Confirm Changes": function() {
                    var delivery_id = $('#editorder_id').html();
                    var delivery_note = $('#receiver').html();
                    var delivery_note = $('#note').html();
                    var latitude = $('#latitude_loc').html();
                    var longitude = $('#longitude_loc').html();
                    var deliverytime = $('#deliverytime_loc').html();
                    var ids = [];
                    var count = 0;
                    $('.log-select:checked').each(function(){
                        ids.push(this.value);
                        
                    });

                    $.post('<?php print site_url('admin/delivery/ajaxeditorder');?>',{
                        'delivery_id':delivery_id,
                        'delivery_note':$('input[name=chg_delivery_note]').val(),
                        'latitude':$('input[name=chg_latitude]').val(),
                        'longitude':$('input[name=chg_longitude]').val(),
                        'deliverytime':$('input[name=chg_deliverytime]').val(),
                        'deliveryId': $('#chg_deliveryId').val(),
                        '_id':ids,
                        'status': $('#new_status_note').val(),
                        'note': $('#new_delivery_note').val(),
                    }, function(data) {
                        if(data.result == 'ok'){
                            //redraw table
                            oTable.fnDraw();
                            $('#editorder_dialog').dialog( "close" );
                            console.log(ids);
                        }
                    },'json');
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
                //allFields.val( "" ).removeClass( "ui-state-error" );
                $('#confirm_list').html('');
            }
        });
<!-- end -->
        $('#setzone_dialog').dialog({
            autoOpen: false,
            height: 250,
            width: 600,
            modal: true,
            buttons: {
                "Confirm Changes": function() {
                    var delivery_id = $('#setzone_id').html();

                    $.post('<?php print site_url('ajax/setzone');?>',{
                        'delivery_id':delivery_id,
                        'city': $('#setbuyerdeliverycity').val(),
                        'zone': $('#setbuyerdeliveryzone').val()
                    }, function(data) {
                        if(data.status == 'OK'){
                            //redraw table
                            oTable.fnDraw();
                            $('#setzone_dialog').dialog( "close" );
                        }
                    },'json');
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
                //allFields.val( "" ).removeClass( "ui-state-error" );
                //$('#confirm_list').html('');
            }
        });

        $('#setcity_dialog').dialog({
            autoOpen: false,
            height: 250,
            width: 600,
            modal: true,
            buttons: {
                "Confirm Changes": function() {
                    var delivery_id = $('#puchange_id').html();

                    console.log($('#punew_status').val());

                    $.post('<?php print site_url('admin/delivery/ajaxpuchangestatus');?>',{
                        'delivery_id':delivery_id,
                        'new_status': $('#punew_status').val(),
                        'actor': $('#actor').val(),
                        'req_note' : $('#puchg_note').val()
                    }, function(data) {
                        if(data.result == 'ok'){
                            //redraw table
                            oTable.fnDraw();
                            $('#puchangestatus_dialog').dialog( "close" );
                        }
                    },'json');
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
                //allFields.val( "" ).removeClass( "ui-state-error" );
                $('#confirm_list').html('');
            }
        });


        $('#setbuyerdeliverycity').change(function(){
            //alert($('#buyerdeliverycity').val());
            var city = $('#setbuyerdeliverycity').val();

            $('#set_zone_select').html('Loading zones...');
            $.post('<?php print site_url('ajax/getzoneselect');?>',
                { city: city },
                function(data) {
                    $('#set_zone_select').html(data.data);
                },'json');

        });

        $('#setweight_dialog').dialog({
            autoOpen: false,
            height: 250,
            width: 600,
            modal: true,
            buttons: {
                "Confirm Changes": function() {
                    var delivery_id = $('#setweight_id').html();

                    $.post('<?php print site_url('ajax/setweight');?>',{
                        'delivery_id':delivery_id,
                        'weight':$('#package_weight').val()
                    }, function(data) {
                        if(data.status == 'OK'){
                            //redraw table
                            oTable.fnDraw();
                            $('#setweight_dialog').dialog( "close" );
                        }
                    },'json');
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
                //allFields.val( "" ).removeClass( "ui-state-error" );
                //$('#confirm_list').html('');
            }
        });

        $('#setdeliverydate_dialog').dialog({
            autoOpen: false,
            height: 250,
            width: 600,
            modal: true,
            buttons: {
                "Confirm Changes": function() {
                    var delivery_id = $('#setdeliverydate_id').html();

                    $.post('<?php print site_url('ajax/setdeliverydate');?>',{
                        'delivery_id':delivery_id,
                        'deliverydate': $('#assign_set_deliverytime').val(),
                    }, function(data) {
                        if(data.status == 'OK'){
                            //redraw table
                            oTable.fnDraw();
                            $('#setdeliverydate_dialog').dialog( "close" );
                        }
                    },'json');
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
                //allFields.val( "" ).removeClass( "ui-state-error" );
                //$('#confirm_list').html('');
            }
        });

        $('#assign_set_deliverytime').datepicker({ dateFormat: 'yy-mm-dd' });

        $('#setdeliverytype_dialog').dialog({
            autoOpen: false,
            height: 250,
            width: 600,
            modal: true,
            buttons: {
                "Confirm Changes": function() {
                    var delivery_id = $('#setweight_id').html();

                    $.post('<?php print site_url('ajax/setzone');?>',{
                        'delivery_id':delivery_id,
                        'city': $('#setbuyerdeliverycity').val(),
                        'zone': $('#setbuyerdeliveryzone').val()
                    }, function(data) {
                        if(data.status == 'OK'){
                            //redraw table
                            oTable.fnDraw();
                            $('#setzone_dialog').dialog( "close" );
                        }
                    },'json');
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
                //allFields.val( "" ).removeClass( "ui-state-error" );
                //$('#confirm_list').html('');
            }
        });

        $('#set_delivery_type').change(function(){

            if($('#set_delivery_type').val() == 'COD'){
                $('#sub_cod').show();
                $('#sub_ccod').hide();
                if($('#sub_cod').val() == 'debit'){
                    $('#sub_provider').show();
                }else{
                    $('#sub_provider').hide();
                }

            }else if($('#set_delivery_type').val() == 'CCOD'){
                $('#sub_cod').hide();
                $('#sub_ccod').show();
                $('#sub_provider').show();
            }else{
                $('#sub_cod').hide();
                $('#sub_ccod').hide();
                $('#sub_provider').hide();
            }

            getweightandcod();
        });

        function getweightandcod(){
            var delivery_type = $('#set_delivery_type').val();

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

