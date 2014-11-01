<script>
	var asInitVals = new Array();
    var dateBlock = <?php print getdateblock();?>;
    var rescheduled_id = 0;
    var refreshTab;
    var reschedulemode = 'incoming';

	$(document).ready(function() {
        $('#assign_all').click(function(){
            if($('#assign_all').is(':checked')){
                $('.assign_check').attr('checked', true);
            }else{
                $('.assign_check').attr('checked', false);
            }
        });

	    var oTable = $('.dataTable').dataTable(
			{
				"bProcessing": true,
		        "bServerSide": true,
		        "sAjaxSource": "<?php print site_url($ajaxurl);?>",
				"oLanguage": { "sSearch": "Search "},
				"sPaginationType": "full_numbers",
				"sDom": 'T<"clear">lfrtip',
				"oTableTools": {
					"sSwfPath": "<?php print base_url();?>assets/swf/copy_csv_xls_pdf.swf"
				},
			<?php if($this->config->item('infinite_scroll')):?>
				"bScrollInfinite": true,
			    "bScrollCollapse": true,
			    "sScrollY": "500px",
			<?php endif; ?>
			<?php if(isset($sortdisable)):?>
				"aoColumnDefs": [
				    { "bSortable": false, "aTargets": [ <?php print $sortdisable; ?> ] }
				 ],
			<?php endif;?>
			    "fnServerData": function ( sSource, aoData, fnCallback ) {
		            $.ajax( {
		                "dataType": 'json',
		                "type": "POST",
		                "url": sSource,
		                "data": aoData,
		                "success": fnCallback
		            } );
		        }
			}
		);

		$('tfoot input').keyup( function () {
			/* Filter on the column (the index) of this element */
			oTable.fnFilter( this.value, $('tfoot input').index(this) );
		} );

		/*
		 * Support functions to provide a little bit of 'user friendlyness' to the textboxes in
		 * the footer
		 */
		$('tfoot input').each( function (i) {
			asInitVals[i] = this.value;
		} );

		$('tfoot input').focus( function () {
			if ( this.className == 'search_init' )
			{
				this.className = '';
				this.value = '';
			}
		} );

		$('tfoot input').blur( function (i) {
			if ( this.value == '' )
			{
				this.className = 'search_init';
				this.value = asInitVals[$('tfoot input').index(this)];
			}
		} );

        function getBlocking(d){
            /*
                $.datepicker.formatDate('yy-mm-dd', d);
            */
            var curr_date = d.getDate();
            var curr_month = d.getMonth() + 1; //months are zero based
            var curr_year = d.getFullYear();

            curr_date = (curr_date < 10)?"0" + curr_date : curr_date;
            curr_month = (curr_month < 10)?"0" + curr_month : curr_month;
            var indate = curr_year + '-' + curr_month + '-' + curr_date;

            var select = 1;
            var css = 'open';
            var popup = 'working day';

            if(window.dateBlock[indate] == 'weekend'){
                select = 0;
                css = 'weekend';
                popup = 'weekend';
            }else if(window.dateBlock[indate] == 'holiday'){
                select = 0;
                css = 'weekend';
                popup = 'holiday';
            }else if(window.dateBlock[indate] == 'blocked'){
                select = 0;
                css = 'blocked';
                popup = 'zero time slot';
            }else if(window.dateBlock[indate] == 'full'){
                select = 0;
                css = 'blocked';
                popup = 'zero time slot';
            }else{
                select = 1;
                css = '';
                popup = 'working day';
            }
            return [select,css,popup];
        }

		$( '#assign_deliveryzone' ).autocomplete({
			source: '<?php print site_url('ajax/getzone')?>',
			method: 'post',
			minLength: 2
		});

		$( '#assign_deliverycity' ).autocomplete({
			source: '<?php print site_url('ajax/getcities')?>',
			method: 'post',
			minLength: 2
		});


		$('#search_deliverytime').datepicker({ dateFormat: 'yy-mm-dd' });

		$('#search_deliverytime').change(function(){
			oTable.fnFilter( this.value, $('tfoot input').index(this) );
		});

		/*Delivery process mandatory*/
		$('#search_deliverytime').datepicker({ dateFormat: 'yy-mm-dd' });
		$('#assign_deliverytime').datepicker({ dateFormat: 'yy-mm-dd' });

<?php
        $this->load->view($this->config->item('auth_views_root') . 'pages/partials/common_button_js');
        $this->load->view($this->config->item('auth_views_root') . 'pages/partials/change_button_js');
?>

        $('table.dataTable').click(function(e){

<?php
        $this->load->view($this->config->item('auth_views_root') . 'pages/partials/common_tab_js');
        $this->load->view($this->config->item('auth_views_root') . 'pages/partials/change_tab_js');
?>

			if ($(e.target).is('.view_detail')) {
				var delivery_id = e.target.id;
				var src = '<?php print base_url() ?>admin/prints/deliveryview/' + delivery_id;

				$('#view_frame').attr('src',src);
				$('#view_dialog').dialog('open');
			}

            if ($(e.target).is('.locpick')) {
                var buyer_id = e.target.id;
                $('#setloc_dialog').dialog('open');

                var src = '<?php print base_url() ?>admin/prints/mapview/order/' + buyer_id;

                $('#map_frame').attr('src',src);
                $('#setloc_dialog').dialog('open');
            }

		});

		$('#doAssign').click(function(){
			var assigns = '';
			var date_assign = $('.assign_date:checked').val();
			var city_assign = $('.assign_city:checked').val();

			console.log(date_assign);
			console.log(city_assign);



			if(date_assign == '' || city_assign == '' ){

				alert('Please select one or more delivery orders');

			}else{

				$('#disp_deliverycity').html(city_assign);
				$('#disp_deliverytime').html(date_assign);

				//$('.assign_check:checked').each(function(){

				var city_assign_class = city_assign.replace(' ','_');

				console.log(city_assign_class);

				$('.' + date_assign +'_'+ city_assign_class).each(function(){

					var zone = date_assign + ' | ' +$('#'+this.value).html() +' | '+ city_assign;

					zone += '<input type="checkbox" name="assign_check_dev[]" value="'+this.value+'" class="id_assign">';

					assigns += '<li style="padding:5px;border-bottom:thin solid grey;margin-left:0px;"><strong>'+this.value + '</strong> <br /> '+ zone +'</li>';
				});

				$.post('<?php print site_url('admin/delivery/ajaxdevicecap');?>',{ assignment_date: date_assign,assignment_zone: $('#assign_deliveryzone').val(),assignment_city: city_assign }, function(data) {
					$('#dev_list').html(data.html);
				},'json');

				$('#trans_list').html(assigns);
				$('#assign_device_dialog').dialog('open');

			}
		});

		$('#getDevices').click(function(){
			if($('#assign_deliverytime').val() == ''){
				alert('Please specify intended delivery time');
			}else{
				//alert($('#assign_deliverytime').val());
				$.post('<?php print site_url('admin/delivery/ajaxdevicecap');?>',{ assignment_date: $('#assign_deliverytime').val(),assignment_zone: $('#assign_deliveryzone').val(),assignment_city: $('#assign_deliverycity').val() }, function(data) {
					$('#dev_list').html(data.html);
				},'json');
			}
		});

		$('#assign_dialog').dialog({
			autoOpen: false,
			height: 300,
			width: 800,
			modal: true,
			buttons: {
				"Assign to Device": function() {
					var device_id = $("input[name='dev_id']:checked").val();

					if($('#assign_deliverytime').val() == '' || device_id == '' || device_id == undefined){
						alert('Please specify date and or device.');
					}else{
						var delivery_ids = [];
						i = 0;
						$('.id_assign:checked').each(function(){
							delivery_ids[i] = $(this).val();
							i++;
						});
						$.post('<?php print site_url('admin/delivery/ajaxassignzone');?>',
							{
								assignment_device_id: device_id,
								'delivery_id[]':delivery_ids,
								assignment_timeslot: $('.timeslot:checked').val(),
								assignment_zone: $('#assign_deliveryzone').val(),
								assignment_city: $('#disp_deliverycity').html() },
								function(data) {
								if(data.result == 'ok'){
									//redraw table
									oTable.fnDraw();
									$('#assign_dialog').dialog( "close" );
								}
						},'json');
					}

				},
				Cancel: function() {
					$('#dev_list').html("");
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				//allFields.val( "" ).removeClass( "ui-state-error" );
				$('#assign_deliverytime').val('');
			}
		});

        <?php
            $this->load->view($this->config->item('auth_views_root') . 'pages/partials/common_dialog_init');
            $this->load->view($this->config->item('auth_views_root') . 'pages/partials/change_dialog_init');
        ?>

        $('#assign_device_dialog').dialog({
            autoOpen: false,
            height: 300,
            width: 800,
            modal: true,
            buttons: {
                "Assign to Device": function() {
                    var device_id = $("input[name='dev_id']:checked").val();
                    if($('#assign_deliverytime').val() == '' || device_id == '' || device_id == undefined){
                        alert('Please specify date and or device.');
                    }else{
                        var delivery_ids = [];
                        i = 0;
                        $('.id_assign:checked').each(function(){
                            delivery_ids[i] = $(this).val();
                            i++;
                        });
                        $.post('<?php print site_url('admin/delivery/ajaxassignzone');?>',
                            {
                                assignment_device_id: device_id,
                                'delivery_id[]':delivery_ids,
                                assignment_timeslot: $('.timeslot:checked').val(),
                                assignment_zone: $('#assign_deliveryzone').val(),
                                assignment_city: $('#disp_deliverycity').html() },
                                function(data) {
                                if(data.result == 'ok'){
                                    //redraw table
                                    oTable.fnDraw();
                                    $('#assign_device_dialog').dialog( "close" );
                                }
                        },'json');
                    }

                },
                Cancel: function() {
                    $('#dev_list').html("");
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
                //allFields.val( "" ).removeClass( "ui-state-error" );
                $('#assign_deliverytime').val('');
            }
        });

        $('#setloc_dialog').dialog({
            autoOpen: false,
            height: 600,
            width: 900,
            modal: true,
            buttons: {
                Save: function(){
                    var nframe = document.getElementById('map_frame');
                    var nframeWindow = nframe.contentWindow;
                    nframeWindow.submitlocation();
                },
                Close: function() {
                    oTable.fnDraw();
                    $( this ).dialog( "close" );
                }
            },
            close: function() {

            }
        });

		/*
		function refresh(){
			oTable.fnDraw();
			setTimeout(refresh, 10000);
		}

		refresh();
		*/
	} );


</script>
<?php if(isset($add_button)):?>
	<div class="button_nav">
		<?php echo anchor($add_button['link'],$add_button['label'],'class="button add"')?>
	</div>
<?php endif;?>
<?php print form_checkbox('assign_all',1,FALSE,'id="assign_all"');?> Select All

<?php echo $this->table->generate(); ?>

<div style="text-align:right;margin-top:12px;">
<?php

    print form_button('do_assign','Assign Delivery Date to Selection','id="doAssign"').
    form_button('do_multi','Change Selection','id="doMultiAction"').
    form_button('do_toscan','Mark for Scanning & Assign to Pick Up Device','id="doMarkscan"').
    form_button('do_pickupassign','Assign Pickup Date to Selection','id="doPickup"').
    form_button('do_confirm','Confirm Selection','id="doConfirm"').
    form_button('do_cancel','Cancel Selection','id="doCancel"').
    form_button('do_label','Print Selection Label','id="doLabel"');

?>
</div>

<div id="assign_device_dialog" title="Assign Selection to Device">
	<table style="width:100%;border:0;margin:0;">
		<tr>
			<td style="width:50%;border:0;margin:0;vertical-align: top">
				<h4>Delivery Orders :</h4>
				<ul id="trans_list" style="border-top:thin solid grey;list-style-type:none;padding-left:0px;"></ul>
			</td>
			<td style="width:50%;border:0;margin:0;vertical-align: top">
				<table style="margin: 0px;border: 0px;">
					<tr>
						<td>
							City : <span id="disp_deliverycity" style="font-weight: bold"></span>
						</td>
						<td>
							Delivery Time : <span id="disp_deliverytime" style="font-weight: bold" ></span>
						</td>
					</tr>
				</table>
				<ul id="dev_list" style="border-top:thin solid grey;list-style-type:none;padding-left:0px;"></ul>
			</td>
		</tr>
	</table>
</div>

<?php
        $this->load->view($this->config->item('auth_views_root') . 'pages/partials/common_dialog');
        $this->load->view($this->config->item('auth_views_root') . 'pages/partials/change_dialog');
?>

<div id="setloc_dialog" title="Set Location" style="overflow:hidden;padding:8px;">
    <input type="hidden" value="" id="print_id" />
    <iframe id="map_frame" name="map_frame" width="100%" height="100%"
    marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto"
    title="Dialog Title">Your browser does not suppr</iframe>
</div>
