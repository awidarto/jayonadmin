<script>
	var asInitVals = new Array();
	
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
				var src = '<?php print base_url() ?>/admin/prints/deliveryview/' + delivery_id;

				$('#view_frame').attr('src',src);
				$('#view_dialog').dialog('open');
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
						//alert(device_id);
						$.post('<?php print site_url('admin/delivery/ajaxdispatch');?>',{ assignment_device_id: device_id,assignment_courier_id: courier_id,assignment_date: assignment_date }, function(data) {
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

<div id="view_dialog" title="Order Detail" style="overflow:hidden;padding:8px;">
	<input type="hidden" value="" id="print_id" />
	<iframe id="view_frame" name="print_frame" width="100%" height="100%"
    marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto"
    title="Dialog Title">Your browser does not suppr</iframe>
</div>