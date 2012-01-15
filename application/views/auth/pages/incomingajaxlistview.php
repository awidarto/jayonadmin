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
		
		$('#search_deliverytime').datepicker({ dateFormat: 'yy-mm-dd' });
		
		$('#search_deliverytime').change(function(){
			oTable.fnFilter( this.value, $('tfoot input').index(this) );
		});

		/*Delivery process mandatory*/
		$('#search_deliverytime').datepicker({ dateFormat: 'yy-mm-dd' });
		$('#assign_deliverytime').datepicker({ dateFormat: 'yy-mm-dd' });
		
		$('#doAssign').click(function(){
			var assigns = '';
			var count = 0;
			$('.assign_check:checked').each(function(){
				assigns += '<li style="padding:5px;border-bottom:thin solid grey;margin-left:0px;">'+this.value+'</li>';
				count++;
			});
			
			if(count > 0){
				$('#trans_list').html(assigns);
				$('#assign_dialog').dialog('open');
			}else{
				alert('Please select one or more delivery orders');
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
			height: 300,
			width: 400,
			modal: true,
			buttons: {
				"Assign Delivery Date": function() {
					if($('#assign_deliverytime').val() == ''){
						alert('Please specify date.');
					}else{
						$.post('<?php print site_url('admin/delivery/ajaxassigndate');?>',{ assignment_date: $('#assign_deliverytime').val(),'delivery_id':$('.assign_check:checked').val() }, function(data) {
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

<div id="assign_dialog" title="Assign Delivery Date to Selection">
	<table style="width:100%;border:0;margin:0;">
		<tr>
			<td style="width:50%;border:0;margin:0;">
				Delivery Date :<br />
				<input id="assign_deliverytime" type="text" value=""><br />
				Delivery Orders :
			</td>
		</tr>
		<tr>
			<td style="overflow:auto;">
				<ul id="trans_list" style="border-top:thin solid grey;list-style-type:none;padding-left:0px;"></ul>
			</td>
		</tr>
	</table>
</div>