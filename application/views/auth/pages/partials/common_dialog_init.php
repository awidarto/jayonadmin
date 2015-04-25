        $('#assign_dialog').dialog({
            autoOpen: false,
            height: 400,
            width: 800,
            modal: true,
            buttons: {
                "Assign Delivery Date": function() {
                    if($('#assign_deliverytime').val() == ''){
                        alert('Please specify date.');
                    }else{
                        var delivery_ids = [];
                        i = 0;
                        $('.assign_check:checked').each(function(){
                            delivery_ids[i] = $(this).val();
                            i++;
                        });
                        $.post('<?php print site_url('admin/delivery/ajaxassigndate');?>',{ assignment_date: $('#assign_deliverytime').val(),'delivery_id[]':delivery_ids}, function(data) {
                            if(data.result == 'ok'){
                                //redraw table
                                oTable.fnDraw();
                                $('#assign_dialog').dialog( "close" );
                            }
                        },'json');
                    }
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

        $('#confirm_dialog').dialog({
            autoOpen: false,
            height: 400,
            width: 600,
            modal: true,
            buttons: {
                "Confirm Delivery Orders": function() {
                    var delivery_ids = [];
                    i = 0;
                    $('.assign_check:checked').each(function(){
                        delivery_ids[i] = $(this).val();
                        i++;
                    });
                    $.post('<?php print site_url('admin/delivery/ajaxconfirm');?>',
                        { assignment_date: $('#assign_deliverytime').val(),
                            'delivery_id[]':delivery_ids,
                            req_by : $('#cf_req_by').val(),
                            req_name : $('#cf_req_name').val(),
                            req_note : $('#cf_req_note').val()
                        }, function(data) {
                        if(data.result == 'ok'){
                            //redraw table
                            oTable.fnDraw();
                            $('#confirm_dialog').dialog( "close" );
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

        $('#cancel_dialog').dialog({
            autoOpen: false,
            height: 400,
            width: 600,
            modal: true,
            buttons: {
                "Cancel Delivery Orders": function() {
                    var delivery_ids = [];
                    i = 0;
                    $('.assign_check:checked').each(function(){
                        delivery_ids[i] = $(this).val();
                        i++;
                    });
                    $.post('<?php print site_url('admin/delivery/ajaxcancel');?>',
                        { assignment_date: $('#assign_deliverytime').val(),
                            'delivery_id[]':delivery_ids,
                            req_by : $('#cl_req_by').val(),
                            req_name :$('#cl_req_name').val(),
                            req_note :$('#cl_req_note').val()
                        }, function(data) {
                        if(data.result == 'ok'){
                            //redraw table
                            oTable.fnDraw();
                            $('#cancel_dialog').dialog( "close" );
                        }
                    },'json');
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
                //allFields.val( "" ).removeClass( "ui-state-error" );
                $('#cancel_list').html('');
            }
        });

        $('#reschedule_dialog').dialog({
            autoOpen: false,
            height: 420,
            width: 600,
            modal: true,
            buttons: {
                "Reschedule Delivery Orders": function() {
                    $.post('<?php print site_url('admin/delivery/ajaxreschedule/incoming');?>',
                        {'delivery_id':rescheduled_id,
                            'buyerdeliverytime':$('#rescheduled_deliverytime').val(),
                            req_by : $('#rs_req_by').val(),
                            req_name : $('#rs_req_name').val(),
                            req_note : $('#rs_req_note').val()
                        },
                        function(data) {
                        if(data.result == 'ok'){
                            //redraw table
                            oTable.fnDraw();
                            $('#reschedule_dialog').dialog( "close" );
                        }
                    },'json');
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
                //allFields.val( "" ).removeClass( "ui-state-error" );
                $('#cancel_list').html('');
            }
        });


        $('#print_dialog').dialog({
            autoOpen: false,
            height: 600,
            width: 1050,
            modal: true,
            buttons: {

                Print: function(){
                    var pframe = document.getElementById('print_frame');
                    var pframeWindow = pframe.contentWindow;
                    pframeWindow.print();
                },
                "Download PDF": function(){
                    var print_id = $('#print_id').val();
                    var src = '<?php print base_url() ?>admin/prints/deliveryslip/' + print_id + '/pdf';
                    window.location = src;
                    //alert(src);
                },

                Close: function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function() {

            }
        });

        $('#label_dialog').dialog({
            autoOpen: false,
            height: 600,
            width: 1050,
            modal: true,
            buttons: {

                Print: function(){
                    var pframe = document.getElementById('label_frame');
                    var pframeWindow = pframe.contentWindow;
                    pframeWindow.print();
                },
                /*
                "Download PDF": function(){
                    var print_id = $('#label_id').val();
                    var col = $('#label_columns').val();
                    var res = $('#label_resolution').val();
                    var cell_height = $('#label_cell_height').val();
                    var cell_width = $('#label_cell_width').val();
                    var mright = $('#label_margin_right').val();
                    var mbottom = $('#label_margin_bottom').val();
                    var src = '<?php print base_url() ?>admin/prints/label/' + print_id + '/' + res + '/' +  cell_height + '/' + cell_width + '/' + col +'/'+ mright +'/'+ mbottom + '/pdf';
                    window.location = src;
                },
                */
                Close: function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function() {

            }
        });


        $('#view_dialog').dialog({
            autoOpen: false,
            height: 600,
            width: 900,
            modal: true,
            buttons: {
                Save: function(){
                    var nframe = document.getElementById('view_frame');
                    var nframeWindow = nframe.contentWindow;
                    nframeWindow.submitorder();
                },
                Print: function(){
                    var pframe = document.getElementById('view_frame');
                    var pframeWindow = pframe.contentWindow;
                    pframeWindow.print();
                },
                Close: function() {
                    oTable.fnDraw();
                    $( this ).dialog( "close" );
                }
            },
            close: function() {

            }
        });

        $('#assign_pickup_dialog').dialog({
            autoOpen: false,
            height: 400,
            width: 800,
            modal: true,
            buttons: {
                "Assign Pickup Date": function() {
                    if($('#assign_pickuptime').val() == ''){
                        alert('Please specify date.');
                    }else{
                        var delivery_ids = [];
                        i = 0;
                        $('.assign_check:checked').each(function(){
                            delivery_ids[i] = $(this).val();
                            i++;
                        });
                        $.post('<?php print site_url('admin/delivery/ajaxassignpickupdate');?>',{ assignment_date: $('#assign_pickuptime').val(),'delivery_id[]':delivery_ids}, function(data) {
                            if(data.result == 'ok'){
                                //redraw table
                                oTable.fnDraw();
                                $('#assign_pickup_dialog').dialog( "close" );
                            }
                        },'json');
                    }
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
                //allFields.val( "" ).removeClass( "ui-state-error" );
                $('#assign_pickuptime').val('');
            }
        });

        $('#multi_action_dialog').dialog({
            autoOpen: false,
            height: 600,
            width: 950,
            modal: true,
            buttons: {
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
                //allFields.val( "" ).removeClass( "ui-state-error" );
                $('#assign_pickuptime').val('');
            }
        });

        //multi action buttons

        $('#doMultiReschedule').on('click',function(){
            var multi_reschedule_id = [];
            $('.assign_check:checked').each(function(){
                multi_reschedule_id.push(this.value);
            });

            console.log(multi_reschedule_id);
            $('#process_reschedule').show();
            $.post('<?php print site_url('admin/delivery/ajaxreschedule');?>/' + reschedulemode,
                {'delivery_id':multi_reschedule_id,
                    'buyerdeliverytime':$('#multi_reschedule').val(),
                    req_by : $('#multi_rs_req_by').val(),
                    req_name : $('#multi_rs_req_name').val(),
                    req_note : $('#multi_rs_req_note').val()
                },
                function(data) {
                if(data.result == 'ok'){
                    $('#process_reschedule').hide();
                    //redraw table
                    oTable.fnDraw();
                    $('#multi_action_dialog').dialog( "close" );
                }else{
                    $('#process_reschedule').hide();
                }
            },'json');

        });

        $('#doMultiChangeStatus').on('click',function(){
            var multi_reschedule_id = [];
            $('.assign_check:checked').each(function(){
                multi_reschedule_id.push(this.value);
            });

            $('#process_chgstatus').show();

            $.post('<?php print site_url('admin/delivery/ajaxchangestatus');?>',{
                'delivery_id':multi_reschedule_id,
                'new_status': $('#multi_new_status').val(),
                'actor': $('#multi_chgactor').val(),
                'req_by' : $('#multi_rs_req_by').val(),
                'req_name' : $('#multi_rs_req_name').val(),
                'req_note' : $('#multi_rs_req_note').val()
            }, function(data) {
                if(data.result == 'ok'){
                    //redraw table
                    oTable.fnDraw();
                    $('#process_chgstatus').hide();
                    $('#multi_action_dialog').dialog( "close" );
                }else{
                    $('#process_chgstatus').hide();
                }
            },'json');

        });

        $('#doMultiPUChangeStatus').on('click',function(){
            var multi_reschedule_id = [];
            $('.assign_check:checked').each(function(){
                multi_reschedule_id.push(this.value);
            });

            $('#process_puchgstatus').show();

            $.post('<?php print site_url('admin/delivery/ajaxpuchangestatus');?>',{
                'delivery_id':multi_reschedule_id,
                'new_status': $('#multi_punew_status').val(),
                'actor': $('#multi_puactor').val(),
                'req_by' : $('#multi_rs_req_by').val(),
                'req_name' : $('#multi_rs_req_name').val(),
                'req_note' : $('#multi_rs_req_note').val()
            }, function(data) {
                if(data.result == 'ok'){
                    //redraw table
                    oTable.fnDraw();
                    $('#process_puchgstatus').hide();
                    $('#multi_action_dialog').dialog( "close" );
                }else{
                    $('#process_puchgstatus').hide();
                }
            },'json');

        });

        $('#doMultiWHChangeStatus').on('click',function(){
            var multi_reschedule_id = [];
            $('.assign_check:checked').each(function(){
                multi_reschedule_id.push(this.value);
            });

            $('#process_whchgstatus').show();

            $.post('<?php print site_url('admin/delivery/ajaxwhchangestatus');?>',{
                'delivery_id':multi_reschedule_id,
                'new_status': $('#multi_whnew_status').val(),
                'actor': $('#multi_whactor').val(),
                'req_by' : $('#multi_rs_req_by').val(),
                'req_name' : $('#multi_rs_req_name').val(),
                'req_note' : $('#multi_rs_req_note').val()
            }, function(data) {
                if(data.result == 'ok'){
                    //redraw table
                    oTable.fnDraw();
                    $('#process_whchgstatus').hide();
                    $('#multi_action_dialog').dialog( "close" );
                }else{
                    $('#process_whchgstatus').hide();
                }
            },'json');

        });

