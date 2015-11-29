            if ($(e.target).is('.changestatus')) {
                var delivery_id = e.target.id;
                $('#change_id').html(delivery_id);
                $('#changestatus_dialog').dialog('open');
            }

            if ($(e.target).is('.puchangestatus')) {
                var delivery_id = e.target.id;
                $('#puchange_id').html(delivery_id);
                $('#puchangestatus_dialog').dialog('open');
            }

            if ($(e.target).is('.whchangestatus')) {
                var delivery_id = e.target.id;
                $('#whchange_id').html(delivery_id);
                $('#whchangestatus_dialog').dialog('open');
            }

            if ($(e.target).is('.set_zone')) {
                var delivery_id = e.target.id;
                console.log(delivery_id);

                var city = $(e.target).data('city');

                console.log(city);

                $('#setbuyerdeliverycity').val(city);

                $.post('<?php print site_url('ajax/getzoneselect/set');?>',
                    { city: city },
                    function(data) {
                        $('#set_zone_select').html(data.data);
                    },'json');

                $('#setzone_id').html(delivery_id);
                $('#setzone_dialog').dialog('open');
            }

            if ($(e.target).is('.set_city')) {
                var delivery_id = e.target.id;
                console.log(delivery_id);
                $('#setcity_id').html(delivery_id);
                $('#setcity_dialog').dialog('open');
            }
