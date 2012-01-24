<script>
	var asInitVals = new Array();
	var dateBlock = <?php print getdateblock();?>;
	
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
		/*Delivery process mandatory*/
		$('#date_display').datepicker({
			numberOfMonths: 2,
			showButtonPanel: true,
			dateFormat:'yy-mm-dd',
			onSelect:function(dateText, inst){
				
				//console.log(dateBlock);
				if(dateBlock[dateText] == 'weekend'){
					alert('no delivery on weekend');
				}else{
					$('#assign_deliverytime').val(dateText);
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
			}else{
				select = 1;
				css = '';
				popup = 'working day';
			}
			return [select,css,popup];
		}
		
		//$('#search_deliverytime').datepicker({ dateFormat: 'yy-mm-dd' });
		//$('#assign_deliverytime').datepicker({ dateFormat: 'yy-mm-dd' });
		
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

		$('table.dataTable').click(function(e){
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