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
