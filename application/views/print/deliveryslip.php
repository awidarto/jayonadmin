<?php
//print_r($main_info);
/*
Array
(
	[id] => 1
	[ordertime] => 0000-00-00 00:00:00
	[assigntime] => 2011-12-12 10:02:41
	[deliverytime] => 0000-00-00 00:00:00
	[assignment_date] => 2011-12-14
	[delivery_id] => 00000017-10-122011-0000000001
	[application_id] => 1
	[application_key] => 23c33397a9b1ecb579c53fe200e26c12709ee379
	[buyer_id] => 1
	[merchant_id] => 17
	[merchant_trans_id] => 123456789
	[courier_id] => 0
	[device_id] => 16
	[shipping_address] => Kompleks DKI D3 Joglo
	[phone] => 02112345678
	[status] => assigned
	[delivery_note] =>
	[undersign] =>
	[latitude] => 0.000000000000
	[longitude] => 0.000000000000
	[reschedule_ref] =>
	[revoke_ref] =>
)

*/
?>
	<style>

		#wrapper{
			width:98%;
			margin:5px;
			display:block;
			font-family:'Trebuchet Ms', 'Yanone Kaffeesatz', Lato, Lobster, 'Lobster Two','Droid Sans', Arial ;
			font-size:14px;
		}

		h2{
			margin:0px;
			padding-top:15px;
		}

		td{
			font-size: 13px;
		}

		.dataTable{
			width:100%;
			font-family:'Trebuchet Ms', 'Yanone Kaffeesatz', Lato, Lobster, 'Lobster Two','Droid Sans', Arial ;
			margin-top:8px;

		}

		.dataTable td{
			border-bottom:thin solid #eee;
			text-align:left;
		}

		.dataTable th{
			text-align:left;
			padding-right:15px;
			font-size:13px;
			font-weight: bold;
			border-top:thin solid #eee;
			border-bottom:thin solid #eee;
			border-left:thin solid #eee;
		}

		.dataTable tr>th{
			width:20px;

		}

		.dataTable tr>th:last-child{
			width:100px;
			border-right:thin solid #eee;
		}

		.dataTable td{
			border-left:thin solid #eee;
			border-bottom:thin solid #eee;
		}

		.dataTable td:last-child{
			border-right:thin solid #eee;
		}

		#jayon_logo{
			vertical-align:top;
			font-family:'Trebuchet Ms', 'Yanone Kaffeesatz', Lato, Lobster, 'Lobster Two','Droid Sans', Arial ;
			font-size: 11px;
			text-align:right;
		}

		#jayon_logo img{
			width:170px;
		}

		#order_detail,#merchant_detail{
			vertical-align:top;
			padding-top:0px;
		}

		#merchant_detail td{
			width:400px;
		}

		#order_detail h2{
			text-align: center;
		}

		#merchant_detail{
			margin:0px;
			padding:8px;
			width:450px;
		}

		#mainInfo, #orderInfo{
			width:100%;
		}

		#mainInfo tr>td:first-child, #orderInfo tr>td:first-child{
			width:150px;
		}

		table#main_table{
			width:100%;
		}

		.row_label{
			width:150px;
		}

		table#signBox{
			font-size: 13px;
			margin-top:15px;
		}

		#signBox th{
			width:150px;
			vertical-align:top;
			border-bottom: thin solid #eee;
			border-right:thin solid #eee;
			margin:2px;
		}

		#sign_name td{
			border-top: thin solid #eee;
			border-bottom: thin solid #eee;
			border-right:thin solid #eee;
		}

		tr#sign_name td:first-child{
			border-left: thin solid #eee;
		}

		#signBox th{
			border-top: thin solid #eee;
			border-bottom: thin solid #eee;
			border-right:thin solid #eee;
		}

		#signBox th:first-child{
			border-left: thin solid #eee;
		}

		#mainInfo tr td:last-child, #orderInfo tr td:last-child{
			border-bottom: thin solid #eee;
			border-left:thin solid #eee;
		}

	</style>
<div id="wrapper">
	<table id="main_table">
		<tbody>
			<tr>
				<td  id="merchant_detail">
					<table border="0" cellpadding="4" cellspacing="0" id="mainLogo">
						<tbody>
							<tr>
								<td id="jayon_logo"><?php 
										print $this->ag_asset->load_image('plogo.png', 'assets/images');?><br />
										<?php print get_option('jex_hq_address');?>
								</td>
								<td style="align:right"><?php print $qr;?></td>
							</tr>
						</tbody>
					</table>
					<table border="0" cellpadding="4" cellspacing="0" id="mainInfo">
						<tbody>
							<tr>
								<td colspan="2"><strong>Delivery Info</strong></td>
							</tr>
							<tr>
								<td class="row_label">Delivery Number:</td>
								<td><?php print $main_info['delivery_id'];?></td>
							</tr>
							<tr>
								<td>Delivery Date:</td>
								<td><?php print $main_info['assignment_date'];?></td>
							</tr>
							<tr>
								<td>Delivery Slot:</td>
								<td><?php print $main_info['assignment_timeslot'];?></td>
							</tr>

							<tr>
								<td colspan="2"><strong>Merchant Info</strong></td>
							</tr>

							<tr>
								<td>Online Store:</td>
								<td><?php print $main_info['merchant_id'];?></td>
							</tr>
							<tr>
								<td>Transaction ID:</td>
								<td><?php print $main_info['merchant_trans_id'];?></td>
							</tr>
							<tr>
								<td>Store Detail:</td>
								<td>&nbsp;</td>
							</tr>

						</tbody>
					</table>
				</td>
				<td id="order_detail"><h2>DELIVERY NOTE</h2><br />
					<table width="100%" cellpadding="4" cellspacing="0" id="orderInfo">
						<tbody>

							<tr>
								<td colspan="2"><strong>Order Detail</strong></td>
							</tr>

							<tr>
								<td class="row_label">Delivered To:</td>
								<td><?php print $main_info['recipient_name'];?></td>
							</tr>
							<tr>
								<td>Shipping Address:</td>
								<td><?php print $main_info['shipping_address'];?></td>
							</tr>
							<tr>
								<td>Contact Number:</td>
								<td><?php print $main_info['phone'];?></td>
							</tr>

						</tbody>
					</table>

					<?php echo $this->table->generate(); ?>
				</td>
			</tr>
		</tbody>
	</table>
	<table border="0" cellpadding="4" cellspacing="0" id="signBox">
		<thead>
			<tr>
				<th>Created By</th>
				<th>Online Store</th>
				<th>Goods Received By</th>
				<th>Cash Received By</th>
				<th>Reporting</th>
				<th>Staff Dispatch Admin</th>
				<th>Finance</th>
				<th>Courier</th>
			</tr>
		</thead>
		<tbody>
			<tr style="height:40px;">
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr id="sign_name">
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</tbody>
	</table>
</div>

