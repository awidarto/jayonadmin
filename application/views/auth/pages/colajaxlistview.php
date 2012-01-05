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
			var test = '';
			alert('length '+('.assign_check:checked').length);
			if($('.assign_check:checked').length  == 0){
				alert('Please select one or more delivery order');
			}else{
				$('.assign_check:checked').each(function(){
					test += '<li style="padding:5px;border-bottom:thin solid grey;margin-left:0px;">'+this.value+'</li>';
				});
				$('#trans_list').html(test);
				$('#assign_dialog').dialog('open');
				//alert(test);
			}
		});
		
		$('#getDevices').click(function(){
			if($('#assign_deliverytime').val() == ''){
				alert('Please specify intended delivery time');
			}else{
				alert($('#assign_deliverytime').val());
			}
		});
		
		$('#assign_dialog').dialog({
			autoOpen: false,
			height: 300,
			width: 600,
			modal: true,
			buttons: {
				"Assign to Device": function() {

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
				Delivery Time :<br />
				<input id="assign_deliverytime" type="text" value="">
			</td>
			<td style="width:50%;border:0;margin:0;">
				<?php print form_button('getdevices','Get Devices','id="getDevices"');?>
			</td>
		</tr>
		<tr>
			<td style="overflow:auto;">
				Delivery Orders :<br />
				<ul id="trans_list" style="border-top:thin solid grey;list-style-type:none;padding-left:0px;"></ul>
			</td>
			<td>
			
			</td>
		</tr>
	</table>
</div>