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
