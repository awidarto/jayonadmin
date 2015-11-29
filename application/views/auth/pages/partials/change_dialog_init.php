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
