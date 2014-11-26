<script>
	var asInitVals = new Array();

    var dl = false;

    var dateBlock = '';
    var rescheduled_id = 0;
    var refreshTab;
    var reschedulemode = 'incoming';

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
                    aoData.push({
                        'name':'dl',
                        'value': dl
                    });
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


<?php
        $this->load->view($this->config->item('auth_views_root') . 'pages/partials/common_button_js');
        $this->load->view($this->config->item('auth_views_root') . 'pages/partials/change_button_js');
?>


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

<?php
        $this->load->view($this->config->item('auth_views_root') . 'pages/partials/common_tab_js');
        $this->load->view($this->config->item('auth_views_root') . 'pages/partials/change_tab_js');
?>

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

            if($(e.target).is('.thumb_multi')){
                var delivery_id = e.target.alt;
                var currentTime = new Date();

                var images = [];

                var thumbs = $('.gal_' + delivery_id)

                $('.gal_' + delivery_id).each(function(el){
                    images.push(
                        {
                            href : '<?php print base_url();?>public/receiver/' + $(this).val() + '?' + currentTime.getTime(),
                            title : $(this).val()
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

<?php
        $this->load->view($this->config->item('auth_views_root') . 'pages/partials/common_tab_js');
        $this->load->view($this->config->item('auth_views_root') . 'pages/partials/change_tab_js');
?>

            if ($(e.target).is('.locpick')) {
                var buyer_id = e.target.id;
                $('#setloc_dialog').dialog('open');

                var src = '<?php print base_url() ?>admin/prints/mapview/order/' + buyer_id;

                $('#map_frame').attr('src',src);
                $('#setloc_dialog').dialog('open');
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
            var addinfo = '';
            var count = 0;

            var tosend = $('.assign_check:checked').sort(function(a,b){
                return $(a).data('merchantid') - $(b).data('merchantid');
            });

            var merchants = [];

            var lastid = '';
            tosend.each(function(){
                if( $(this).data('merchantid') !=  lastid ){
                    merchants.push({
                        'id': $(this).data('merchantid'),
                        'name': $(this).data('merchant'),
                        'slipname': $(this).data('slipname')
                    })
                }
                lastid = $(this).data('merchantid');
            });

            //console.log(merchants);

            $.each(merchants, function( k, v){
                addinfo += '<li style="padding:5px;list-style-type:none;border-bottom:thin solid grey;margin-left:0px;">'
                            + v.id + ' <strong>' + v.name + '</strong><br />'
                            + 'CC :<br /><textarea class="ccfield" data-merchant="' + v.id + '"></textarea><br />'
                            + 'Msg :<br /><textarea class="msg" data-merchant="' + v.id + '"></textarea></label>'
                            + '</li>';

            });

            tosend.each(function(){
                console.log(this);
                assigns += '<li style="padding:5px;border-bottom:thin solid grey;margin-left:0px;"><strong>'+ $(this).data('slipname') + '</strong></li>';
                count++;
            });

            if(count > 0){
                $('#send_list').html(assigns);
                $('#send_cc').html(addinfo);
                $('#sendslip_dialog').dialog('open');
            }else{
                alert('Please select one or more delivery orders');
            }
        });

        $('#download-csv').on('click',function(){
            var flt = $('tfoot td input, tfoot td select');
            var dlfilter = [];

            flt.each(function(){
                var name = this.name;
                var val = this.value;
                dlfilter.push({ name : name, value : val });
            });
            console.log(dlfilter);

            var sort = oTable.fnSettings().aaSorting;
            console.log(sort);

            $.post('<?php print base_url() ?>admin/dl/delivered',
                {
                    datafilter : dlfilter,
                    sort : sort[0],
                    sortdir : sort[1]
                },
                function(data) {
                    if(data.status == 'OK'){
                        console.log(data.data.urlcsv);
                        window.location.href = data.data.urlcsv;
                    }
                },'json');

            //return false;
            event.preventDefault();
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
            height: 400,
            width: 800,
            modal: true,
            buttons: {
                "Send Delivery Slips": function() {
                    var delivery_ids = [];
                    var ccfields = [];
                    var merchantids = [];
                    var admincc = $('#admincc').val();
                    var messages = [];

                    i = 0;
                    $('.assign_check:checked').each(function(){
                        delivery_ids[i] = $(this).val();
                        i++;
                    });

                    j = 0;
                    $('.ccfield').each(function(){
                        ccfields[j] = $(this).val();
                        merchantids[j] = $(this).data('merchant');
                        j++;
                    });

                    j = 0;
                    $('.msg').each(function(){
                        messages[j] = $(this).val();
                        j++;
                    });

                    $.post('<?php print site_url('admin/prints/ajaxsendslip');?>',
                        {
                            'delivery_id[]':delivery_ids,
                            'ccfields[]':ccfields,
                            'mids[]':merchantids,
                            'msgs[]':messages,
                            'admincc':admincc
                        }, function(data) {
                        if(data.result == 'OK'){
                            //redraw table
                            oTable.fnDraw();
                            $('#sendslip_dialog').dialog( "close" );
                        }else{
                            alert('Sending failed');
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


        <?php
            $this->load->view($this->config->item('auth_views_root') . 'pages/partials/common_dialog_init');
            $this->load->view($this->config->item('auth_views_root') . 'pages/partials/change_dialog_init');
        ?>

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


        $('#dl_csv').on('click',function(){
            dl = 'csv';
            oTable.fnDraw();
            preventDefault();
        });

	});
</script>
<style type="text/css">
    .locpick{
        display: inline-block;
        padding: 4px;
        background-color: orange;
    }
</style>

<?php if(isset($add_button)):?>
	<div class="button_nav">
		<?php echo anchor($add_button['link'],$add_button['label'],'class="button add"')?>
	</div>
<?php endif;?>
    <div class="button_nav">
        <span id="download-csv" class="button" style="cursor:pointer">
            Download Excel
        </span>
    </div>
<?php print form_checkbox('assign_all',1,FALSE,'id="assign_all"');?> Select All

<?php echo $this->table->generate(); ?>

<div style="text-align:right;margin-top:12px;">
<?php

    print form_button('do_assign','Assign Delivery Date to Selection','id="doAssign"').
    form_button('do_multi','Change Selection','id="doMultiAction"').
    form_button('do_toscan','Mark for Scanning & Assign to Pick Up Device','id="doMarkscan"').
    form_button('do_pickupassign','Assign Pickup Date to Selection','id="doPickup"').
    form_button('do_confirm','Confirm Selection','id="doConfirm"').
    form_button('do_cancel','Cancel Selection','id="doCancel"').
    form_button('do_label','Print Selection Label','id="doLabel"');

?>
</div>

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

<div id="sendslip_dialog" title="Send Delivery Slip" style="overflow:hidden;overflow-y:auto;padding:8px;">
    <table style="width:100%;vertical-align:top;">
        <tr>
            <td style="width:50%;vertical-align:top;">
                Orders :
                <ul id="send_list">
                </ul>
            </td>
            <td>
                CC to :<br /
                use comma to separate multiple email addresses :<br />
                <label for="admincc">Admin CC</label><br />
                <textarea id="admincc" ></textarea>
                <ul id="send_cc">
                </ul>
            </td>
        </tr>
    </table>
</div>


<?php
        $this->load->view($this->config->item('auth_views_root') . 'pages/partials/common_dialog');
        $this->load->view($this->config->item('auth_views_root') . 'pages/partials/change_dialog');
?>

<div id="setloc_dialog" title="Set Location" style="overflow:hidden;padding:8px;">
    <input type="hidden" value="" id="print_id" />
    <iframe id="map_frame" name="map_frame" width="100%" height="100%"
    marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto"
    title="Dialog Title">Your browser does not suppr</iframe>
</div>

