<script>
	var asInitVals = new Array();
	var dateBlock = <?php print getdateblock();?>;
	var rescheduled_id = 0;

	$(document).ready(function() {

	    var oTable = $('.dataTable').dataTable(
			{
				"bProcessing": true,
		        "bServerSide": true,
		        "sAjaxSource": "<?php print site_url($ajaxurl);?>",
				"oLanguage": { "sSearch": "Search "},
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

		$('table.dataTable').click(function(e){
			if ($(e.target).is('.cancel_link')) {
				var delivery_id = e.target.id;
				var answer = confirm("Are you sure you want to archive this order ?");
				if (answer){
					$.post('<?php print site_url('admin/delivery/ajaxarchive');?>',{'delivery_id':delivery_id}, function(data) {
						if(data.result == 'ok'){
							//redraw table
							oTable.fnDraw();
							alert(delivery_id + " archived");
						}
					},'json');
				}else{
					alert(delivery_id + " not archived");
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

			if ($(e.target).is('.view_detail')) {
				var delivery_id = e.target.id;
				var src = '<?php print base_url() ?>/admin/prints/deliveryview/' + delivery_id;

				$('#view_frame').attr('src',src);
				$('#view_dialog').dialog('open');
			}

		});

		$('#rescheduled_deliverytime').datetimepicker({
			numberOfMonths: 2,
			showButtonPanel: true,
			dateFormat:'yy-mm-dd',
			timeFormat: 'hh:mm:ss',
			onSelect:function(dateText, inst){
				
				//console.log(dateBlock);
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
			
			//console.log(indate);
			console.log(window.dateBlock);
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

		$('#doArchive').click(function(){
			var assigns = '';
			var count = 0;
			$('.assign_check:checked').each(function(){

				var deliverydate = $('#dt_'+this.value).html();
				var status = $('#st_'+this.value).html();
				assigns += '<li style="padding:5px;border-bottom:thin solid grey;margin-left:0px;"><strong>'+this.value + '</strong><br />' + deliverydate +' '+ status+'</li>';
				count++;
			});
			
			if(count > 0){
				$('#archive_list').html(assigns);
				$('#archive_dialog').dialog('open');
			}else{
				alert('Please select one or more delivery orders');
			}
		});

		$('#archive_dialog').dialog({
			autoOpen: false,
			height: 300,
			width: 400,
			modal: true,
			buttons: {
				"Archive Delivery Orders": function() {
					var delivery_ids = [];
					var laststatus = [];
					i = 0;
					$('.assign_check:checked').each(function(){
						delivery_ids[i] = $(this).val();
						laststatus[i] = $(this).attr('title');
						i++;
					}); 
					$.post('<?php print site_url('admin/delivery/ajaxarchive');?>',{ assignment_date: $('#assign_deliverytime').val(),'delivery_id[]':delivery_ids,'laststatus[]':laststatus}, function(data) {
						if(data.result == 'ok'){
							//redraw table
							oTable.fnDraw();
							$('#archive_dialog').dialog( "close" );
						}
					},'json');
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				//allFields.val( "" ).removeClass( "ui-state-error" );
				$('#archive_list').html('');
			}
		});

		$('#reschedule_dialog').dialog({
			autoOpen: false,
			height: 480,
			width: 650,
			modal: true,
			buttons: {
				"Reschedule Delivery Orders": function() {
					$.post('<?php print site_url('admin/delivery/ajaxfullreschedule');?>',
						{'delivery_id':rescheduled_id,
							'buyerdeliverytime':$('#rescheduled_deliverytime').val(),
							req_by : $('#rs_req_by').val(),
							req_name : $('#rs_req_name').val(),
							req_note : $('#rs_req_note').val(),
							recipient_name : $('#rs_rec_name').val(),	 	 	 	 	 	 	 
							shipping_address : $('#rs_ship_addr').val(),
							shipping_zip : $('#rs_ship_zip').val()
						},
						function(data) {
						if(data.result == 'ok'){
							//redraw table
							oTable.fnDraw();
							$('#reschedule_dialog').dialog( "close" );
							alert('Order rescheduled as new delivery order, you may check the Incoming Orders.');
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

		$('#view_dialog').dialog({
			autoOpen: false,
			height: 600,
			width: 900,
			modal: true,
			buttons: {
				Print: function(){
					var pframe = document.getElementById('print_frame');
					var pframeWindow = pframe.contentWindow;
					pframeWindow.print();
				}, 
				Close: function() {
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				
			}
		});

	});
</script>
<?php if(isset($add_button)):?>
	<div class="button_nav">
		<?php echo anchor($add_button['link'],$add_button['label'],'class="button add"')?>
	</div>
<?php endif;?>
<?php echo $this->table->generate(); ?>

<div id="archive_dialog" title="Archive Delivery Orders">
	<table style="width:100%;border:0;margin:0;">
		<tr>
			<td style="width:250px;vertical-align:top">
				Delivery Orders :
			</td>
		</tr>
		<tr>
			<td style="overflow:auto;width:250px;vertical-align:top">
				<ul id="archive_list" style="border-top:thin solid grey;list-style-type:none;padding-left:0px;"></ul>
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
				Administrative Info :
			</td>
		</tr>
		<tr>
			<td style="overflow:auto;width:250px;vertical-align:top">
				<ul id="rescheduled_trans_list" style="border-top:thin solid grey;list-style-type:none;padding-left:0px;"></ul>
				Reschedule Delivery Date to :<br />
				<input id="rescheduled_deliverytime" type="text" value=""><br />
				<div id="date_time_display"></div>
				Change Recipient Name to* :<br />
				<?php print form_input('rec_name','','id="rs_rec_name"');?><br />
				Change Shipping Address to* :<br />
				<?php print form_textarea('ship_addr','','id="rs_ship_addr"');?><br />
				Change ZIP to* :<br />
				<?php print form_input('ship_zip','','id="rs_ship_zip"');?><br />
				* Please leave blank for no changes.
			</td>
			<td style="border:0;margin:0;vertical-align:top;padding:2px;">
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

<div id="view_dialog" title="Order Detail" style="overflow:hidden;padding:8px;">
	<input type="hidden" value="" id="print_id" />
	<iframe id="view_frame" name="print_frame" width="100%" height="100%"
    marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto"
    title="Dialog Title">Your browser does not suppr</iframe>
</div>
