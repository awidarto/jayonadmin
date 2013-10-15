<script>
	var asInitVals = new Array();

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

			if ($(e.target).is('.delete_link')) {
				var user_id = e.target.id;
				var answer = confirm("Are you sure you want to delete this user ?");
				if (answer){
					$.post('<?php print site_url('admin/members/ajaxdelete');?>',{'id':user_id}, function(data) {
						if(data.result == 'ok'){
							//redraw table
							oTable.fnDraw();
							alert(user_id + " deleted");
						}
					},'json');
				}else{
					alert(user_id + " not deleted");
				}
		   	}

		});

		$('#search_timestamp').datepicker({ dateFormat: 'yy-mm-dd' });

		$('#search_timestamp').change(function(){
			oTable.fnFilter( this.value, $('tfoot input').index(this) );
		});


		$('#doSetGroup').click(function(){
			var assigns = '';
			var count = 0;
			$('.assign_check:checked').each(function(){

				var uname = $('#un_'+this.value).html();
				assigns += '<li style="padding:5px;border-bottom:thin solid grey;margin-left:0px;"><strong>'+this.value +' - '+ uname+'</strong></li>';
				count++;
			});

			if(count > 0){
				$('#archive_list').html(assigns);
				$('#setgroup_dialog').dialog('open');
			}else{
				alert('Please select one or more user');
			}
		});

		$('#setgroup_dialog').dialog({
			autoOpen: false,
			height: 300,
			width: 500,
			modal: true,
			buttons: {
				"Set Member Group": function() {
					var user_ids = [];
					i = 0;
					$('.assign_check:checked').each(function(){
						user_ids[i] = $(this).val();
						i++;
					});
					$.post('<?php print site_url('admin/members/ajaxsetgroup');?>',
						{ set_group: $('#set_group').val(),'users[]':user_ids},
						function(data) {
						if(data.result == 'ok'){
							//redraw table
							oTable.fnDraw();
							$('#setgroup_dialog').dialog( "close" );
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
	});
</script>
<?php if(isset($add_button)):?>
	<div class="button_nav">
		<?php echo anchor($add_button['link'],$add_button['label'],'class="button add"')?>
	</div>
<?php endif;?>

<?php print form_checkbox('assign_all',1,FALSE,'id="assign_all"');?> Select All

<?php echo $this->table->generate(); ?>

<?php
	$group_array = array(
		user_group_id('merchant')=>'merchant',
		user_group_id('buyer')=>'buyer'
		);
?>

<div id="setgroup_dialog" title="Set Member Group">
	<table style="width:100%;border:0;margin:0;">
		<tr>
			<td style="width:250px;vertical-align:top">
				Members :
			</td>
			<td>
				Set Group to :
			</td>
		</tr>
		<tr>
			<td style="overflow:auto;width:250px;vertical-align:top">
				<ul id="archive_list" style="border-top:thin solid grey;list-style-type:none;padding-left:0px;"></ul>
			</td>
			<td style="overflow:auto;width:250px;vertical-align:top">
				<?php print form_dropdown('set_groups',$group_array,'','id="set_group"');?>
			</td>
		</tr>
	</table>
</div>
