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

		#mainInfo{
			width:500px;
		}

		#mainLogo{
			width:100%;
		}

		.dataTable{
			width:100%;
			font-family:'Trebuchet Ms', 'Yanone Kaffeesatz', Lato, Lobster, 'Lobster Two','Droid Sans', Arial ;

		}

		#mainInfo td{
			border:thin solid #eee;
		}



		h1{
			border-bottom:thin solid #eee;
			text-align:center;
			font-family:'Trebuchet Ms', 'Yanone Kaffeesatz', Lato, Lobster, 'Lobster Two','Droid Sans', Arial ;
		}

		.dataTable td{
			border:thin solid #eee;
		}

		#signBox td{
			border:thin solid #eee;
		}

		table#signBox {
			border:thin solid #eee;
			width:100%;
			font-family:'Trebuchet Ms', 'Yanone Kaffeesatz', Lato, Lobster, 'Lobster Two','Droid Sans', Arial ;
			margin-bottom:35px;
		}

		th{
			font-family:'Trebuchet Ms', 'Yanone Kaffeesatz', Lato, Lobster, 'Lobster Two','Droid Sans', Arial ;
			background-color:black;
			color:white;
			vertical-align:top;
			font-size:14px;
		}

		td{
			font-size:13px;
		}

		#signBox{
			float:right;
		}

		#signBox thead th{
			width:120px;
		}

		#wrapper{
			width:1000px;
			display:block;
			font-family:'Trebuchet Ms', 'Yanone Kaffeesatz', Lato, Lobster, 'Lobster Two','Droid Sans', Arial ;
			font-size:14px;

		}

		#doPrint{
			text-align:center;
			text-decoration: underline;
			background-color: red;
			color: white;
			padding:4px;
			cursor: pointer;
			margin:auto;
		}

	</style>
<div id="wrapper">

	<h1>DELIVERY NOTE</h1>
	<table>
		<tr>
			<td>
				<table border="0" cellpadding="4" cellspacing="0" id="mainLogo">
					<tbody>
						<tr>
							<td><?php print $this->ag_asset->load_image('plogo.png', 'assets/images');?></td>
							<td style="align:right"><?php print $qr;?></td>
						</tr>
					</tbody>
				</table>

			</td>
			<td>
				<table border="0" cellpadding="4" cellspacing="0" id="mainInfo">
					<tbody>
						<tr>
							<td>Delivery Number:</td>
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
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table border="0" cellpadding="4" cellspacing="0" id="mainInfo">
					<tbody>
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
			<td>
				<table border="0" cellpadding="4" cellspacing="0" id="mainInfo">
					<tbody>
						<tr>
							<td>Delivered To:</td>
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
			</td>
		</tr>
	</table>
	<br /><br />

	<?php echo $this->table->generate(); ?>

	<br /><br />

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
			<tr style="height:150px;">
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
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

