        $('#doPickup').click(function(){
            var assigns = '';
            var count = 0;
            $('.assign_check:checked').each(function(){

                var deliverydate = $('#'+this.value).html();
                assigns += '<li style="padding:5px;border-bottom:thin solid grey;margin-left:0px;"><strong>'+this.value + '</strong><br />' + deliverydate +'</li>';
                count++;
            });

            if(count > 0){
                $('#trans_pickup_list').html(assigns);
                $('#assign_pickup_dialog').dialog('open');
            }else{
                alert('Please select one or more delivery orders');
            }
        });

        $('#date_pickup_display').datepicker({
            numberOfMonths: 2,
            showButtonPanel: true,
            dateFormat:'yy-mm-dd',
            onSelect:function(dateText, inst){
                if(dateBlock[dateText] == 'weekend'){
                    alert('no delivery on weekend');
                }else{
                    $('#assign_pickuptime').val(dateText);
                }
            },
            beforeShowDay:getBlocking
        });

        $('.multi_date').datepicker({
            numberOfMonths: 2,
            showButtonPanel: false,
            dateFormat:'yy-mm-dd',
            onSelect:function(dateText, inst){
                if(dateBlock[dateText] == 'weekend'){
                    alert('no delivery on weekend');
                }else{
                    $('#assign_pickuptime').val(dateText);
                }
            },
            beforeShowDay:getBlocking
        });

        $('#doMultiAction').click(function(){
            var assigns = '';
            var count = 0;
            $('.assign_check:checked').each(function(){

                var deliverydate = $('#'+this.value).html();
                assigns += '<li style="padding:5px;border-bottom:thin solid grey;margin-left:0px;"><strong>'+this.value + '</strong><br />' + deliverydate +'</li>';
                count++;
            });

            if(count > 0){
                $('#multi_item_list').html(assigns);
                $('#multi_action_dialog').dialog('open');
            }else{
                alert('Please select one or more delivery orders');
            }
        });

        $('.similar_email').on('click',function(){
            var assigns = [];
            var count = 0;
            $('.assign_check:checked').each(function(){
                assigns.push(this.value);
                count++;
            });

            if(count > 0){
                $.post( '<?php print site_url('ajax/mark');?>',{
                    ids:assigns,
                    action:'mark_email'
                },function(data){
                    if(data.status == 'OK'){
                        oTable.fnDraw();
                    }
                },'json');

                console.log(assigns);
            }else{
                alert('Please select one or more delivery orders');
            }
        });

        $('.unmark_similar_email').on('click',function(){
            var assigns = [];
            var count = 0;
            $('.assign_check:checked').each(function(){
                assigns.push(this.value);
                count++;
            });

            if(count > 0){
                $.post( '<?php print site_url('ajax/unmark');?>',{
                    ids:assigns,
                    action:'unmark_email'
                },function(data){
                    if(data.status == 'OK'){
                        oTable.fnDraw();
                    }
                },'json');

                console.log(assigns);
            }else{
                alert('Please select one or more delivery orders');
            }
        });

        $('.similar_phone').on('click',function(){
            var assigns = [];
            var count = 0;
            $('.assign_check:checked').each(function(){
                assigns.push(this.value);
                count++;
            });

            if(count > 0){
                $.post( '<?php print site_url('ajax/mark');?>',{
                    ids:assigns,
                    action:'mark_phone'
                },function(data){
                    if(data.status == 'OK'){
                        oTable.fnDraw();
                    }
                },'json');

                console.log(assigns);
            }else{
                alert('Please select one or more delivery orders');
            }
        });

        $('.unmark_similar_phone').on('click',function(){
            var assigns = [];
            var count = 0;
            $('.assign_check:checked').each(function(){
                assigns.push(this.value);
                count++;
            });

            if(count > 0){
                $.post( '<?php print site_url('ajax/unmark');?>',{
                    ids:assigns,
                    action:'unmark_phone'
                },function(data){
                    if(data.status == 'OK'){
                        oTable.fnDraw();
                    }
                },'json');

                console.log(assigns);
            }else{
                alert('Please select one or more delivery orders');
            }
        });
