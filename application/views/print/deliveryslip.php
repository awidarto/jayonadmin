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
			width:500px;
			font-family:'Trebuchet Ms', 'Yanone Kaffeesatz', Lato, Lobster, 'Lobster Two','Droid Sans', Arial ;
		}

		th{
			font-family:'Trebuchet Ms', 'Yanone Kaffeesatz', Lato, Lobster, 'Lobster Two','Droid Sans', Arial ;
			background-color:black;
			color:white;
		}
		
		#signBox{
			float:right;
		}
		
		#wrapper{
			width:1000px;
			display:block;
			font-family:'Trebuchet Ms', 'Yanone Kaffeesatz', Lato, Lobster, 'Lobster Two','Droid Sans', Arial ;
		}
		
	</style>
<div id="wrapper">

	<h1>Delivery Slip</h1>
	
	<table border="0" cellpadding="4" cellspacing="0" id="mainInfo">
	<tbody>
	<tr>
		<td>Delivery Number:</td>
		<td><?php print $main_info['delivery_id'];?></td>
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
	<br /><br />

	<?php echo $this->table->generate(); ?>

	<br /><br />

	<table border="0" cellpadding="4" cellspacing="0" id="signBox">
	<thead>
		<tr>
			<th>Delivered By:</th>
			<th>Received By:</th>
		</tr>
	</thead>
	<tbody>
	<tr style="height:150px;">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>Date:</td>
		<td>Date:</td>
	</tr>
	</tbody>
	</table>
</div>
