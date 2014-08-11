<script>
	var asInitVals = new Array();
	var reassign_id = '';

	$(document).ready(function() {

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

		$( '#assign_courier' ).autocomplete({
			source: '<?php print site_url('ajax/getcourier')?>',
			method: 'post',
			minLength: 2,
			select:function(event,ui){
				$('#assign_courier_id').val(ui.item.id);
				$('#assign_courier_id_txt').html(ui.item.id);
			}
		});

		$( '#device_id' ).autocomplete({
			source: '<?php print site_url('ajax/getdevice')?>',
			method: 'post',
			minLength: 2
		});

		$('table.dataTable').click(function(e){

			if ($(e.target).is('.view_detail')) {
				var delivery_id = e.target.id;
				var src = '<?php print base_url() ?>admin/prints/deliveryview/' + delivery_id;

				$('#view_frame').attr('src',src);
				$('#view_dialog').dialog('open');
			}

            if ($(e.target).is('.changestatus')) {
                var delivery_id = e.target.id;
                $('#change_id').html(delivery_id);
                $('#changestatus_dialog').dialog('open');
            }

			if ($(e.target).is('.reassign')) {
				var delivery_id = e.target.id;

				$.post('<?php print site_url('ajax/getorder');?>',{ delivery_id:delivery_id }, function(data) {
					if(data.result == 'ok'){

						$('#reassign_delivery_id').html(data.data.delivery_id);
						$('#disp_deliverycity').html(data.data.assignment_city);
						$('#disp_deliveryzone').html(data.data.assignment_zone);
						$('#disp_deliverydate').html(data.data.assignment_city);
						$('#current_device').html(data.data.device);

						var date_assign = data.data.assignment_date;

						var zone_assign = data.data.assignment_zone;

						var city_assign = data.data.buyerdeliverycity;

						$.post('<?php print site_url('admin/delivery/ajaxdevicecap');?>',{
								assignment_date: date_assign,
								assignment_zone: zone_assign,
								assignment_city: city_assign
							}, function(data) {
								$('#dev_list').html(data.html);
							},'json');
						$('#device_reassign_dialog').dialog('open');
					}
				},'json');
			}

		});

		$('#search_deliverytime').datepicker({ dateFormat: 'yy-mm-dd' });

		$('#search_deliverytime').change(function(){
			oTable.fnFilter( this.value, $('tfoot input').index(this) );
		});

		/*Delivery process mandatory*/
		$('#search_deliverytime').datepicker({ dateFormat: 'yy-mm-dd' });
		$('#assign_deliverytime').datepicker({ dateFormat: 'yy-mm-dd' });

		$('#doDispatch').click(function(){
			if($('.device_id:checked').val() == undefined || $(".assign_date:checked").val() == undefined ){
				alert('Please specify Date AND Device.');
			}else{
				var device_id = $('.device_id:checked').val();
				var device_name = $('.device_id:checked').attr('title');
				var assignment_date = $(".assign_date:checked").val();

				$('#assign_device').val(device_id);
				$('#assign_date').val(assignment_date);

				$('#delivery_date').html(assignment_date);
				$('#device_identifier').html(device_name);

				$('#assign_dialog').dialog('open');
			}
		});

		$('#assign_dialog').dialog({
			autoOpen: false,
			height: 200,
			width: 300,
			modal: true,
			buttons: {
				"Dispatch Device": function() {
					if($("#assign_date").val() == ''){
						alert('Please specify Courier.')
					}else{
						var device_id = $("#assign_device").val();
						var courier_id = $("#assign_courier_id").val();
						var assignment_date = $("#assign_date").val();
						var id_class = "."+assignment_date+"-"+device_id;


						//console.log($(id_class).html());

						var delivery_ids = new Array();

						$(id_class).each(function(){
							var d = $(this).html();
							delivery_ids.push(d);
						});

						//console.log(delivery_ids);
						//alert(device_id);

						$.post('<?php print site_url('admin/delivery/ajaxdispatch');?>',{ assignment_device_id: device_id,assignment_courier_id: courier_id,assignment_date: assignment_date,delivery_id: delivery_ids }, function(data) {
							if(data.result == 'ok'){
								//redraw table
								oTable.fnDraw();
								$('#assign_dialog').dialog( "close" );
							}
						},'json');

					}
				},
				Cancel: function() {
					$('#assign_courier').val('');
					$('#assign_courier_id_txt').html('');
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				//allFields.val( "" ).removeClass( "ui-state-error" );
				$('#assign_courier').val('');
				$('#assign_courier_id_txt').html('');
				$('#assign_deliverytime').val('');
			}
		});

        $('#changestatus_dialog').dialog({
            autoOpen: false,
            height: 250,
            width: 400,
            modal: true,
            buttons: {
                "Confirm Delivery Orders": function() {
                    var delivery_id = $('#change_id').html();

                    $.post('<?php print site_url('admin/delivery/ajaxchangestatus');?>',{
                        'delivery_id':delivery_id,
                        'new_status': $('#new_status').val(),
                        'actor': $('#actor').val()
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

		$('#device_reassign_dialog').dialog({
			autoOpen: false,
			height: 300,
			width: 500,
			modal: true,
			buttons: {
				"Re-Assign to Device": function() {

					$.post('<?php print site_url('ajax/reassign');?>',
						{
							delivery_id:$('#reassign_delivery_id').html(),
							assignment_date:$('#disp_deliverydate').html(),
							assignment_device_id: $("input[name='dev_id']:checked").val(),
							assignment_timeslot: $('.timeslot:checked').val(),
							courier_id: 'unassigned'
						},
						function(data) {
							if(data.status == 'OK:REASSIGNED'){
								//redraw table
								oTable.fnDraw();
								$('#device_reassign_dialog').dialog( 'close' );
							}
						},'json');
				},
				Cancel: function() {
					$('#dev_list').html('');
					$( this ).dialog( 'close' );
				}
			},
			close: function() {
				//allFields.val( "" ).removeClass( "ui-state-error" );
				$('#assign_deliverytime').val('');
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
<?php echo $this->table->generate(); ?>

<div id="assign_dialog" title="Assign Selection to Device">
	<table style="width:100%;border:0;margin:0;">
		<tr>
			<td style="width:50%;border:0;margin:0;">
				Date :
				<input id="assign_date" type="hidden" value=""><strong><span id="delivery_date"></span></strong><br />
				Device :
				<input id="assign_device" type="hidden" value=""><strong><span id="device_identifier"></span></strong><br />
				Courier ID: <strong><span id="assign_courier_id_txt"></span></strong><br />
				<input id="assign_courier" type="text" value=""><br />
				<input id="assign_courier_id" type="hidden" value=""><br />
			</td>
		</tr>
	</table>
</div>

<div id="device_reassign_dialog" title="Re-Assign Device">
	<table style="margin: 0px;border: 0px;">
		<tr>
			<td>
				Delivery ID : <span id="reassign_delivery_id" style="font-weight: bold"></span><br />
				City : <span id="disp_deliverycity" style="font-weight: bold"></span><br />
				Delivery Date : <span id="disp_deliverydate" style="font-weight: bold" ></span><br />
				Current Device : <span id="current_device" style="font-weight: bold" ></span>
			</td>
		</tr>
		<tr>
			<td>
				Available Devices :<br />
				<ul id="dev_list" style="border-top:thin solid grey;list-style-type:none;padding-left:0px;">
					Loading...
				</ul>
			</td>
		</tr>
	</table>
</div>

<div id="changestatus_dialog" title="Change Delivery Orders">
    <table style="width:100%;border:0;margin:0;">
        <tr>
            <td style="width:250px;vertical-align:top">
                <strong>Delivery ID : </strong><span id="change_id"></span><br /><br />
                <?php
                    $status_list = $this->config->item('status_colors');
                    $status_list = array_keys($status_list);

                    $sl = array();
                    foreach($status_list as $s){
                        $sl[$s]=$s;
                    }

                    $actor = $this->config->item('actors_title');


                    print 'Actor <br />';
                    print form_dropdown('actor',$actor,'','id="actor"').'<br /><br />';
                    print ' New Status<br />';
                    print form_dropdown('new_status',$sl,'','id="new_status"');

                ?>
            </td>
        </tr>
    </table>
</div>


<div id="view_dialog" title="Order Detail" style="overflow:hidden;padding:8px;">
	<input type="hidden" value="" id="print_id" />
	<iframe id="view_frame" name="print_frame" width="100%" height="100%"
    marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto"
    title="Dialog Title">Your browser does not suppr</iframe>
</div>