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

            if ($(e.target).is('.crchangestatus')) {
                var delivery_id = e.target.id;
                $('#crchange_id').html(delivery_id);
                $('#crchangestatus_dialog').dialog('open');
            }

            if ($(e.target).is('.set_zone')) {
                var delivery_id = e.target.id;
                console.log(delivery_id);

                var city = $(e.target).data('city');
                var zone = $(e.target).data('zone');

                console.log(city);

                $('#setbuyerdeliverycity').val(city);

                $.post('<?php print site_url('ajax/getzoneselect/set');?>',
                    { city: city, zone:zone },
                    function(data) {
                        $('#set_zone_select').html(data.data);
                    },'json');

                $('#setzone_id').html(delivery_id);
                $('#setzone_dialog').dialog('open');
            }

            if ($(e.target).is('.set_weight')) {
                var delivery_id = e.target.id;
                var current_app = $(e.target).data('app');
                var current_weight = $(e.target).data('weight');
                console.log(delivery_id);

                var weight = $(e.target).data('weight');

                console.log(weight);

                $.post('<?php print site_url('ajax/getweightdata');?>',
                    { app_key: current_app, weight:current_weight },
                    function(data) {
                        $('#set_weight_select').html(data.data.selector);
                        $('#package_weight').val(weight);
                    },'json');

                $('#setweight_id').html(delivery_id);
                $('#setweight_dialog').dialog('open');
            }

            if ($(e.target).is('.set_deliverytype')) {
                var delivery_id = e.target.id;
                console.log(delivery_id);
                $('#setdeliverytype_id').html(delivery_id);
                $('#setdeliverytype_dialog').dialog('open');
            }

            if ($(e.target).is('.set_city')) {
                var delivery_id = e.target.id;
                console.log(delivery_id);
                $('#setcity_id').html(delivery_id);
                $('#setcity_dialog').dialog('open');
            }

            if($(e.target).is('.set_assignmentdate')){
                var delivery_id = e.target.id;
                var current_date = $(e.target).data('assignmentdate');

                console.log('delivery id : ' + delivery_id);

                $('#assign_set_deliverytime').val(current_date);

                $('#setdeliverydate_id').html(delivery_id);
                $('#setdeliverydate_dialog').dialog('open');

            }
