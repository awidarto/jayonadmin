<script>
	var asInitVals = new Array();
	var dateBlock = <?php print getdateblock();?>;
	var rescheduled_id = 0;


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

		$('#search_deliverytime').datepicker({ dateFormat: 'yy-mm-dd' });

		$('#search_deliverytime').change(function(){
			oTable.fnFilter( this.value, $('tfoot input').index(this) );
		});

		/*Delivery process mandatory*/
		$('#date_display').datepicker({
			numberOfMonths: 2,
			showButtonPanel: true,
			dateFormat:'yy-mm-dd',
			onSelect:function(dateText, inst){
				if(dateBlock[dateText] == 'weekend'){
					alert('no delivery on weekend');
				}else{
					$('#assign_deliverytime').val(dateText);
				}
			},
			beforeShowDay:getBlocking
		});


		$('#rescheduled_deliverytime').datetimepicker({
			numberOfMonths: 2,
			showButtonPanel: true,
			dateFormat:'yy-mm-dd',
			timeFormat: 'hh:mm:ss',
			onSelect:function(dateText, inst){
				if(dateBlock[dateText] == 'weekend'){
					alert('no delivery on weekend');
				}else if(dateBlock[dateText] == 'full'){
					alert('time slot is full');
				}else{
					$('#rescheduled_deliverytime').val(dateText);
				}
			},
			beforeShowDay:getBlocking
		});

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

		//$('#search_deliverytime').datepicker({ dateFormat: 'yy-mm-dd' });
		//$('#assign_deliverytime').datepicker({ dateFormat: 'yy-mm-dd' });

		$('#neworder').click(function(){
			var src = '<?php print base_url() ?>admin/order/neworder';

			$('#neworder_frame').attr('src',src);
			$('#neworder_dialog').dialog('open');
		});

		$('#doAssign').click(function(){
			var assigns = '';
			var count = 0;
			$('.assign_check:checked').each(function(){

				var deliverydate = $('#'+this.value).html();
				assigns += '<li style="padding:5px;border-bottom:thin solid grey;margin-left:0px;"><strong>'+this.value + '</strong><br />' + deliverydate +'</li>';
				count++;
			});

			if(count > 0){
				$('#trans_list').html(assigns);
				$('#assign_dialog').dialog('open');
			}else{
				alert('Please select one or more delivery orders');
			}
		});

		$('#doArchive').click(function(){
			var assigns = '';
			var count = 0;
			$('.assign_check:checked').each(function(){

				var deliverydate = $('#'+this.value).html();
				assigns += '<li style="padding:5px;border-bottom:thin solid grey;margin-left:0px;"><strong>'+this.value + '</strong><br />' + deliverydate +'</li>';
				count++;
			});

			if(count > 0){
				$('#archive_list').html(assigns);
				$('#archive_dialog').dialog('open');
			}else{
				alert('Please select one or more delivery orders');
			}
		});

		$('#doConfirm').click(function(){
			var assigns = '';
			var count = 0;
			$('.assign_check:checked').each(function(){

				var deliverydate = $('#'+this.value).html();
				assigns += '<li style="padding:5px;border-bottom:thin solid grey;margin-left:0px;"><strong>'+this.value + '</strong><br />' + deliverydate +'</li>';
				count++;
			});

			if(count > 0){
				$('#confirm_list').html(assigns);
				$('#confirm_dialog').dialog('open');
			}else{
				alert('Please select one or more delivery orders');
			}
		});


		$('#doCancel').click(function(){
			var assigns = '';
			var count = 0;
			$('.assign_check:checked').each(function(){

				var deliverydate = $('#'+this.value).html();
				assigns += '<li style="padding:5px;border-bottom:thin solid grey;margin-left:0px;"><strong>'+this.value + '</strong><br />' + deliverydate +'</li>';
				count++;
			});

			if(count > 0){
				$('#cancel_list').html(assigns);
				$('#cancel_dialog').dialog('open');
			}else{
				alert('Please select one or more delivery orders');
			}
		});
		//put all action link functions here
		$('table.dataTable').click(function(e){
			if ($(e.target).is('.changestatus')) {
				var delivery_id = e.target.id;
				$('#change_id').html(delivery_id);
				$('#changestatus_dialog').dialog('open');
			}

			if ($(e.target).is('.cancel_link')) {
				var delivery_id = e.target.id;
				var answer = confirm("Are you sure you want to cancel this order ?");
				if (answer){
					$.post('<?php print site_url('admin/delivery/ajaxcancel');?>',{'delivery_id':delivery_id}, function(data) {
						if(data.result == 'ok'){
							//redraw table
							oTable.fnDraw();
							alert(delivery_id + " canceled");
						}
					},'json');
				}else{
					alert(delivery_id + " not canceled");
				}
		   	}

			if ($(e.target).is('.reschedule_link')) {
				var delivery_id = e.target.id;
				rescheduled_id = delivery_id;
				var current_date = $('#cd_'+rescheduled_id).val();

				var assigns = '<li style="padding:5px;border-bottom:thin solid grey;margin-left:0px;"><strong>'+ rescheduled_id + '</strong><br />'+ current_date +'</li>';
				$('#rescheduled_trans_list').html(assigns);
				$('#reschedule_dialog').dialog('open');
		   	}

			if ($(e.target).is('.revoke_link')) {
				var delivery_id = e.target.id;
				var answer = confirm("Are you sure you want to revoke this order ?");
				if (answer){
					$.post('<?php print site_url('admin/delivery/ajaxrevoke');?>',{'delivery_id':delivery_id}, function(data) {
						if(data.result == 'ok'){
							//redraw table
							oTable.fnDraw();
							alert(delivery_id + " revoked");
						}
					},'json');
				}else{
					alert(delivery_id + " not revoked");
				}
		   	}

			if ($(e.target).is('.purge_link')) {
				var delivery_id = e.target.id;
				var answer = confirm("Are you sure you want to purge this order ?");
				if (answer){
					$.post('<?php print site_url('admin/delivery/ajaxpurge');?>',{'delivery_id':delivery_id}, function(data) {
						if(data.result == 'ok'){
							//redraw table
							oTable.fnDraw();
							alert(delivery_id + " purged");
						}
					},'json');
				}else{
					alert(delivery_id + " not purged");
				}
		   	}

			if ($(e.target).is('.view_detail')) {
				var delivery_id = e.target.id;
				var src = '<?php print base_url() ?>admin/prints/deliveryview/' + delivery_id;

				$('#view_frame').attr('src',src);
				$('#view_dialog').dialog('open');
			}


		});

		$('#getDevices').click(function(){
			if($('#assign_deliverytime').val() == ''){
				alert('Please specify intended delivery time');
			}else{
				//alert($('#assign_deliverytime').val());
				$.post('<?php print site_url('admin/delivery/ajaxdevicecap');?>',{ assignment_date: $('#assign_deliverytime').val() }, function(data) {
					$('#dev_list').html(data.html);
				},'json');
			}
		});

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

		$('#neworder_dialog').dialog({
			autoOpen: false,
			height: 600,
			width: 950,
			modal: true,
			buttons: {
				Save: function(){
					var nframe = document.getElementById('neworder_frame');
					var nframeWindow = nframe.contentWindow;
					nframeWindow.submitorder();
				},
				Close: function() {
					oTable.fnDraw();
					$( this ).dialog( "close" );
					$('#sendingorder').hide();
					$('#sendingstatus').hide();
				}
			},
			close: function() {
				$('#sendingorder').hide();
				$('#sendingstatus').hide();
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
<div class="button_nav">
	<span class="button add" id="neworder" style="cursor:pointer;">New Order</span>
</div>
<?php print form_checkbox('assign_all',1,FALSE,'id="assign_all"');?> Select All

<?php if(isset($add_button)):?>
	<div class="button_nav">
		<?php echo anchor($add_button['link'],$add_button['label'],'class="button add"')?>
	</div>
<?php endif;?>
<?php echo $this->table->generate(); ?>

<div style="text-align:right;margin-top:12px;">
<?php

    print form_button('do_assign','Assign Delivery Date to Selection','id="doAssign"').
    form_button('do_confirm','Confirm Selection','id="doConfirm"').
    form_button('do_cancel','Cancel Selection','id="doCancel"');

?>
</div>
<div id="assign_dialog" title="Assign Delivery Date to Selection">
	<table style="width:100%;border:0;margin:0;">
		<tr>
			<td style="width:250px;vertical-align:top">
				Delivery Orders :
			</td>
			<td>
				Delivery Date :<br />
			</td>
		</tr>
		<tr>
			<td style="overflow:auto;width:250px;vertical-align:top">
				<ul id="trans_list" style="border-top:thin solid grey;list-style-type:none;padding-left:0px;"></ul>
			</td>
			<td style="border:0;margin:0;">
				<input id="assign_deliverytime" type="text" value=""><br />
				<div id="date_display"></div>
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

<div id="reschedule_dialog" title="Reschedule Order">
	<table style="width:100%;border:0;margin:0;">
		<tr>
			<td style="width:250px;vertical-align:top">
				Delivery Orders :
			</td>
			<td>
				Reschedule Delivery Date to :<br />
			</td>
		</tr>
		<tr>
			<td style="overflow:auto;width:250px;vertical-align:top">
				<ul id="rescheduled_trans_list" style="border-top:thin solid grey;list-style-type:none;padding-left:0px;"></ul>
			</td>
			<td style="border:0;margin:0;">
				<input id="rescheduled_deliverytime" type="text" value=""><br />
				<div id="date_time_display"></div>
				Requested by :<br />
				<?php print form_dropdown('req_by',$this->config->item('actors_title'),'','id="rs_req_by"');?><br />
				Requester name :<br />
				<?php print form_input('req_name','','id="rs_req_name"');?><br />
				Request Note :<br />
				<?php print form_textarea('req_note','','id="rs_req_note"');?><br />
			</td>
		</tr>
	</table>
</div>

<div id="confirm_dialog" title="Confirm Delivery Orders">
	<table style="width:100%;border:0;margin:0;">
		<tr>
			<td style="width:250px;vertical-align:top">
				Delivery Orders :
			</td>
			<td style="width:250px;vertical-align:top">
				Requested by :
			</td>
		</tr>
		<tr>
			<td style="overflow:auto;width:250px;vertical-align:top">
				<ul id="confirm_list" style="border-top:thin solid grey;list-style-type:none;padding-left:0px;"></ul>
			</td>
			<td style="width:250px;vertical-align:top">
				<?php print form_dropdown('req_by',$this->config->item('actors_title'),'','id="cf_req_by"');?><br />
				Requester name :<br />
				<?php print form_input('req_name','','id="cf_req_name"');?><br />
				Request Note :<br />
				<?php print form_textarea('req_note','','id="cf_req_note"');?><br />
			</td>
		</tr>
	</table>
</div>

<div id="cancel_dialog" title="Cancel Delivery Orders">
	<table style="width:100%;border:0;margin:0;">
		<tr>
			<td style="width:250px;vertical-align:top">
				Delivery Orders :
			</td>
			<td style="width:250px;vertical-align:top">
				Requested by :
			</td>
		</tr>
		<tr>
			<td style="overflow:auto;width:250px;vertical-align:top">
				<ul id="cancel_list" style="border-top:thin solid grey;list-style-type:none;padding-left:0px;"></ul>
			</td>
			<td style="width:250px;vertical-align:top">
				<?php print form_dropdown('req_by',$this->config->item('actors_title'),'','id="cl_req_by"');?><br />
				Requester name :<br />
				<?php print form_input('req_name','','id="cl_req_name"');?><br />
				Request Note :<br />
				<?php print form_textarea('req_note','','id="cl_req_note"');?><br />
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

<div id="neworder_dialog" title="New Order" style="overflow:hidden;padding:8px;">
	<input type="hidden" value="" id="print_id" />
	<div id="sendingorder" style="display:none;">
	    <img src="<?php print base_url();?>assets/images/ajax_loader.gif" /> Processing...
	</div>
	<div id="sendingstatus" style="display:none;">
	</div>
	<iframe id="neworder_frame" name="print_frame" width="100%" height="100%"
    marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto"
    title="New Order">Your browser does not suppr</iframe>
</div>
