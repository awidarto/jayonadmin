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

		$('#search_deliverytime').datepicker({ dateFormat: 'yy-mm-dd' });

		$('#search_deliverytime').change(function(){
			oTable.fnFilter( this.value, $('tfoot input').index(this) );
		});

		$('table.dataTable').click(function(e){
            /*
			if($(e.target).is('.thumb')){
				var delivery_id = e.target.alt;
				var currentTime = new Date();
				$.fancybox.open([
			        {
			            href : '<?php print base_url();?>public/receiver/' + delivery_id + '.jpg?' + currentTime.getTime(),
			            title : delivery_id
			        }
			    ]);

			}
            */

            if($(e.target).is('.thumb')){
                var delivery_id = e.target.alt;
                var currentTime = new Date();

                $.fancybox.open([
                        {
                            href : '<?php print base_url();?>public/receiver/' + delivery_id + '.jpg?' + currentTime.getTime(),
                            title : delivery_id
                        }
                    ]);

            }

            if($(e.target).is('.thumb_pending')){
                var delivery_id = e.target.alt;
                var currentTime = new Date();

                var images = [];

                var thumbs = $('.gal_' + delivery_id)

                $('.gal_' + delivery_id).each(function(el){
                    images.push(
                        {
                            href : '<?php print base_url();?>public/receiver/' + $(this).val() + '?' + currentTime.getTime(),
                            title : delivery_id
                        }
                    );
                });

                $.fancybox.open(images);

            }


            if($(e.target).is('.sign')){
                var delivery_id = e.target.alt;
                var currentTime = new Date();
                $.fancybox.open([
                    {
                        href : '<?php print base_url();?>public/receiver/' + delivery_id + '_sign.jpg?' + currentTime.getTime(),
                        title : delivery_id
                    }
                ]);

            }

			if($(e.target).is('.rotate')){
				var delivery_id = e.target.id;
				$.post('<?php print site_url('ajax/rotatephoto');?>',{'delivery_id':delivery_id,'is_thumb':0},
				function(data) {
					if(data.result == 'ok'){
						//redraw table
						//oTable.fnDraw();
						alert("Photo of " + data.delivery_id + " rotated");
					}
				},'json');

				$.post('<?php print site_url('ajax/rotatephoto');?>',{'delivery_id':delivery_id,'is_thumb':1},
				function(data) {
					if(data.result == 'ok'){
						oTable.fnDraw();
						alert("Thumbnail of " + data.delivery_id + " rotated");
					}
				},'json');
			}


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

			if ($(e.target).is('.printslip')) {
				var delivery_id = e.target.id;
				$('#print_id').val(delivery_id);
				var src = '<?php print base_url() ?>admin/prints/deliveryslip/' + delivery_id;

				$('#print_frame').attr('src',src);
				$('#print_dialog').dialog('open');
			}

			if ($(e.target).is('.view_detail')) {

				var delivery_id = e.target.id;
				var src = '<?php print base_url() ?>admin/prints/deliveryview/' + delivery_id;

				$('#view_frame').attr('src',src);
				$('#view_dialog').dialog('open');
			}

			if ($(e.target).is('.view_log')) {
				var delivery_id = e.target.id;
				var src = '<?php print base_url() ?>admin/log/deliverylog/' + delivery_id;

				$('#view_dialog').attr('title','Delivery Log : ' + delivery_id);
				$('#ui-dialog-title-view_dialog').html('Delivery Log : ' + delivery_id);
				$('#view_frame').attr('src',src);
				$('#view_dialog').dialog('open');
			}

			if ($(e.target).is('.changestatus')) {
				var delivery_id = e.target.id;
				var device_id = e.target.dev_id;

				console.log(e.target);

				$('#changedev_id').val(device_id);
				$('#change_id').html(delivery_id);
				$('#changestatus_dialog').dialog('open');
			}

		});

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

        $('#doSending').click(function(){
            var assigns = '';
            var count = 0;
            $('.assign_check:checked').each(function(){
                assigns += '<li style="padding:5px;border-bottom:thin solid grey;margin-left:0px;"><strong>'+this.value + '</strong></li>';
                count++;
            });

            if(count > 0){
                $('#send_list').html(assigns);
                $('#sendslip_dialog').dialog('open');
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

        $('#sendslip_dialog').dialog({
            autoOpen: false,
            height: 300,
            width: 400,
            modal: true,
            buttons: {
                "Send Delivery Slips": function() {
                    var delivery_ids = [];
                    i = 0;
                    $('.assign_check:checked').each(function(){
                        delivery_ids[i] = $(this).val();
                        i++;
                    });
                    $.post('<?php print site_url('admin/prints/ajaxsendslip');?>',
                        {
                            'delivery_id[]':delivery_ids
                        }, function(data) {
                        if(data.result == 'OK'){
                            //redraw table
                            oTable.fnDraw();
                            $('#sendslip_dialog').dialog( "close" );
                        }
                    },'json');
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                    $('#send_list').html('');
                }
            },
            close: function() {
                //allFields.val( "" ).removeClass( "ui-state-error" );
                $('#send_list').html('');
            }
        });


		$('#print_dialog').dialog({
			autoOpen: false,
			height: 400,
			width: 1050,
			modal: true,
			buttons: {
				Print: function(){
					var pframe = document.getElementById('print_frame');
					var pframeWindow = pframe.contentWindow;
					pframeWindow.print();
				},
				"Download PDF": function(){
					var print_id = $('#print_id').val();
					var src = '<?php print base_url() ?>admin/prints/deliveryslip/' + print_id + '/pdf';
					window.location = src;
					//alert(src);
				},
				Close: function() {
					$( this ).dialog( "close" );
				}
			},
			close: function() {

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
					var device_id = $('#changedev_id').val();

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
				Print: function(){
					var pframe = document.getElementById('print_frame');
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

<div id="print_dialog" title="Print" style="overflow:hidden;padding:8px;">
	<input type="hidden" value="" id="print_id" />
	<iframe id="print_frame" name="print_frame" width="100%" height="100%"
    marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto"
    title="Dialog Title">Your browser does not suppr</iframe>
</div>

<div id="view_dialog" title="Order Detail" style="overflow:hidden;padding:8px;">
	<input type="hidden" value="" id="print_id" />
	<iframe id="view_frame" name="print_frame" width="100%" height="100%"
    marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto"
    title="Dialog Title">Your browser does not suppr</iframe>
</div>

<div id="sendslip_dialog" title="Send Delivery Slip" style="overflow:hidden;padding:8px;">
    <ul id="send_list">

    </ul>
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

					print form_hidden('changedev_id','','id="changedev_id"');

					print 'Actor <br />';
					print form_dropdown('actor',$actor,'','id="actor"').'<br /><br />';
					print ' New Status<br />';
					print form_dropdown('new_status',$sl,'','id="new_status"');

				?>
			</td>
		</tr>
	</table>
</div>

