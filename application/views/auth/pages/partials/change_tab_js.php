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
