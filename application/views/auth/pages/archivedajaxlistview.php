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
                "aLengthMenu": [[10, 25, 50, 100, 150, 200, 250, 500], [10, 25, 50, 100, 150, 200, 250, 500]],
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


		$('#search_timestamp').datepicker({ dateFormat: 'yy-mm-dd' });
		$('#search_reporttime').datepicker({ dateFormat: 'yy-mm-dd' });

		$('#search_timestamp').change(function(){
			oTable.fnFilter( this.value, $('tfoot input').index(this) );
		});

		$('#search_reporttime').change(function(){
			oTable.fnFilter( this.value, $('tfoot input').index(this) );
		});

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

			if ($(e.target).is('.printslip')) {
				var delivery_id = e.target.id;
				$('#print_id').val(delivery_id);
				var src = '<?php print base_url() ?>admin/prints/deliveryslip/' + delivery_id;

				$('#print_frame').attr('src',src);
				$('#print_dialog').dialog('open');
			}

            if ($(e.target).is('.locpick')) {
                var buyer_id = e.target.id;
                $('#setloc_dialog').dialog('open');

                var src = '<?php print base_url() ?>admin/prints/mapview/order/' + buyer_id;

                $('#map_frame').attr('src',src);
                $('#setloc_dialog').dialog('open');
            }

            // Update Sahlan
            if ($(e.target).is('.movefoto')){
                var delivery_id = e.target.id;
                $('#mvfoto_id').html(delivery_id);
                
                $.post('<?php print site_url('ajax/getlistimage/id');?>',
                    {delivery_id:delivery_id},
                    function(data) {
                        $('#lsfoto_id').html(data.data);
                    },'json');

                $('#movefoto_dialog').dialog('open');
            }

            if ($(e.target).is('.deletefoto')){
                var delivery_id = e.target.id;
                $('#delfoto_id').html(delivery_id);

                $.post('<?php print site_url('ajax/getlistimage/id');?>',
                    {delivery_id:delivery_id},
                    function(data) {
                        $('#listfoto_id').html(data.data);
                    },'json');

                $('#deletefoto_dialog').dialog('open');
            }

            if ($(e.target).is('.editorder')){
                var delivery_id = e.target.id;
                var delivery_note = $(e.target).data('delivery_note');
                var latitude = $(e.target).data('latitude');
                var longitude = $(e.target).data('longitude');
                var deliverytime = $(e.target).data('deliverytime');

                $('#editorder_id').html(delivery_id);
                $('input[name=chg_delivery_note]').val(delivery_note);
                $('input[name=chg_latitude]').val(latitude);
                $('input[name=chg_longitude]').val(longitude);
                $('input[name=chg_deliverytime]').val(deliverytime);

                $.post('<?php print site_url('ajax/gethistory/id');?>',
                    {delivery_id:delivery_id},
                    function(data) {
                        $('#chg_history').html(data.data);
                    },'json');

                $.post('<?php print site_url('ajax/getlog/id');?>',
                    {delivery_id:delivery_id},
                    function(data) {
                        $('#chg_log').html(data.data);
                    },'json');
            
                $('#editorder_dialog').dialog('open');
            }

            // end

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

		$('#archive_dialog').dialog({
			autoOpen: false,
			height: 300,
			width: 400,
			modal: true,
			buttons: {
				"Archive Delivery Orders": function() {
					var delivery_ids = [];
					i = 0;
					$('.assign_check:checked').each(function(){
						delivery_ids[i] = $(this).val();
						i++;
					});
					$.post('<?php print site_url('admin/delivery/ajaxarchive');?>',{ assignment_date: $('#assign_deliverytime').val(),'delivery_id[]':delivery_ids}, function(data) {
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

        // Update sahlan
        $('#movefoto_dialog').dialog({
            autoOpen: false,
            height: 250,
            width: 600,
            modal: true,
            buttons: {
                "Confirm Changes": function() {
                    var delivery_id = $('#lsfoto_id').val();
                    var ids = [];
                    var count = 0;
                    $('.img-select:checked').each(function(){
                        ids.push(this.value);
                        count++;
                    });
                    if(count > 0){
                        $.post('<?php print site_url('admin/delivery/ajaxmovefoto');?>',{
                            'delivery_id':delivery_id,
                            'parent_id': $('#req_deliveryid').val(),
                            '_id':ids,
                        }, function(data) {
                            if(data.result == 'ok'){
                                //redraw table
                                oTable.fnDraw();
                                $('#movefoto_dialog').dialog( "close" );
                            }
                        },'json');
                    }else{
                        alert('Please select one or more Foto');
                    }
                },
            },
        });

        $('#deletefoto_dialog').dialog({
            autoOpen: false,
            height: 250,
            width: 600,
            modal: true,
            buttons: {
                "Confirm Changes": function() {
                    var delivery_id = $('#delfoto_id').html();
                    var ids = [];
                    var count = 0;
                    $('.img-select:checked').each(function(){
                        ids.push(this.value);
                        count++;
                    });
                    if(count > 0){
                        $.post('<?php print site_url('admin/delivery/ajaxdelfoto');?>',{
                            'delivery_id':delivery_id,
                            'parent_id': $('#req_deliveryid').val(),
                            '_id':ids,
                        }, function(data) {
                            if(data.result == 'ok'){
                                //redraw table
                                oTable.fnDraw();
                                $('#deletefoto_dialog').dialog( "close" );
                            }
                        },'json');
                    }else{
                        alert('Please select one or more Foto');
                    }
                },
            },
        });

        $('#editorder_dialog').dialog({
            autoOpen: false,
            height: 430,
            width: 600,
            modal: true,
            buttons: {
                "Confirm Changes": function() {
                    var delivery_id = $('#editorder_id').html();
                    var delivery_note = $('#receiver').html();
                    var delivery_note = $('#note').html();
                    var latitude = $('#latitude_loc').html();
                    var longitude = $('#longitude_loc').html();
                    var deliverytime = $('#deliverytime_loc').html();
                    var ids = [];
                    var count = 0;
                    $('.log-select:checked').each(function(){
                        ids.push(this.value);
                        
                    });

                    $.post('<?php print site_url('admin/delivery/ajaxeditorder');?>',{
                        'delivery_id':delivery_id,
                        'delivery_note':$('input[name=chg_delivery_note]').val(),
                        'latitude':$('input[name=chg_latitude]').val(),
                        'longitude':$('input[name=chg_longitude]').val(),
                        'deliverytime':$('input[name=chg_deliverytime]').val(),
                        'deliveryId': $('#chg_deliveryId').val(),
                        '_id':ids,
                    }, function(data) {
                        if(data.result == 'ok'){
                            //redraw table
                            oTable.fnDraw();
                            $('#editorder_dialog').dialog( "close" );
                            console.log(ids);
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
        // end update
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

<div id="view_dialog" title="Order Detail" style="overflow:hidden;padding:8px;">
	<input type="hidden" value="" id="print_id" />
	<iframe id="view_frame" name="print_frame" width="100%" height="100%"
    marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto"
    title="Dialog Title">Your browser does not suppr</iframe>
</div>

<div id="setloc_dialog" title="Set Location" style="overflow:hidden;padding:8px;">
    <input type="hidden" value="" id="print_id" />
    <iframe id="map_frame" name="map_frame" width="100%" height="100%"
    marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto"
    title="Dialog Title">Your browser does not suppr</iframe>
</div>

<div id="movefoto_dialog" title="Move Delivery Picture">
    <table style="width:100%;border:0;margin:0;">
        <tr>
            <td style="width:250px;vertical-align:top">
                <strong>Delivery ID : </strong><span id="mvfoto_id"></span><br /><br />
                <div id="lsfoto_id" style="padding-left:10px;"></div>
            </td>
            <td>
                <label for="req_deliveryid">Move foto to Delivery ID</label>
                <input type="textarea" name="parent_id" id="req_deliveryid" style="width:100%;height:100%"></input>
            </td>
        </tr>
    </table>
</div>

<div id="deletefoto_dialog" title="Delete Delivery Picture">
    <table style="width:100%;border:0;margin:0;">
        <tr>
            <td style="width:250px;vertical-align:top">
                <strong>Delivery ID : </strong><span id="delfoto_id"></span><br /><br />
                <div id="listfoto_id" style="padding-left:10px;"></div>
            </td>
            <td>
                <label for="del_deliveryId">Select to Delete foto</label>
                <!-- <textarea name="del_deliveryId" id="del_deliveryId" style="width:100%;height:100%"></textarea> -->
            </td>
        </tr>
    </table>
</div>

<div id="editorder_dialog" title="Change Delivery Order">
    <table style="width:100%;border:0;margin:0;">
        <tr>
            <td style="width:250px;vertical-align:top">
                <strong>Delivery ID : </strong><span id="editorder_id"></span><br /><br />
                <strong>Receiver / Note : </strong><input type="textarea" name="chg_delivery_note" id="receiverId" style="width:100%;height:100%"></input><br><br/>
                <strong>Latitude : </strong><input type="textarea" name="chg_latitude" id="latitudeId" style="width:100%;height:100%"></input><br>
                <strong>Longitude : </strong><input type="textarea" name="chg_longitude" id="longitudeId" style="width:100%;height:100%"></input><br><br/>
                <strong>Delivery Time : </strong><input type="textarea" name="chg_deliverytime" id="deliverytimeId" style="width:100%;height:100%"></input><br>
            </td>
            <td style="width:70px;">
                
            </td>

            <td>
                <strong>History Delivery Note: </strong>
                <div id="chg_history" style="padding-left:20px;"></div>
                <br>
                <strong>History Delivery Log: </strong>
                <div id="chg_log" style="padding-left:20px;"></div>
                <br>
                <label for="req_deliveryid">Move Log to Delivery ID</label>
                <input type="textarea" name="deliveryId" id="chg_deliveryId" style="width:100%;height:100%"></input>
            </td>
        </tr>
    </table>
</div>
