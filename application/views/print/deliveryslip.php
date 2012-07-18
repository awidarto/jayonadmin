<html>
<head>
	<title>Delivery Slip</title>
	<style>

		html{margin:0px;}

		body{margin:5px;
			font-size: 12px;
		}

		hr{
			border:0px;
			border-bottom:thin solid #aaa;
		}

		#wrapper{
			width:1000px;
			margin:5px;
			display:block;
			font-family:'Trebuchet Ms', 'Yanone Kaffeesatz', Lato, Lobster, 'Lobster Two','Droid Sans', Helvetica ;
			font-size:13px;
			text-align:left;
		}

		h2{
			margin:0px;
		}

		td{
			font-size: 12px;
			padding:5px;
		}

		.dataTable{
			width:100%;
			font-family:'Trebuchet Ms', 'Yanone Kaffeesatz', Lato, Lobster, 'Lobster Two','Droid Sans', Helvetica ;
			margin-top:8px;

		}

		.dataTable td{
			border-bottom:thin solid #eee;
			text-align:left;
			font-size: 11px;
		}

		.dataTable th{
			text-align:left;
			padding-right:15px;
			font-size:12px;
			font-weight: bold;
			border-top:thin solid #eee;
			border-bottom:thin solid #eee;
			/*border-left:thin solid #eee;*/
		}

		.dataTable tr>th{
			width:20px;

		}

		.dataTable tr>th:last-child{
			width:100px;
			/*border-right:thin solid #eee;*/
		}

		.dataTable td{
			/*border-left:thin solid #eee;*/
			border-bottom:thin solid #eee;
		}

		.dataTable td:last-child{
			/*border-right:thin solid #eee;*/
			text-align:right;
		}

		#jayon_logo{
			vertical-align:top;
			font-family:'Trebuchet Ms', 'Yanone Kaffeesatz', Lato, Lobster, 'Lobster Two','Droid Sans', Helvetica ;
			font-size: 11px;
			text-align:left;
		}

		#jayon_logo img{
			width:170px;
		}

		#order_detail,#merchant_detail,#sign_boxes{
			vertical-align:top;
			padding-top:0px;
			
		}

		#order_detail{
			width:400px;
		}

		#order_detail h2{
			text-align: center;
		}

		#merchant_detail{
			width:300px;
			margin:0px;
			padding:0px;
		}

		table#mainInfo{
			width:100%;
		}

		#mainInfo tr>td:first-child, #orderInfo tr>td:first-child{
			width:150px;
		}

		table#main_table{
			padding:0px;
			margin:0px;
		}

		.row_label{
			width:150px;
		}

		table#signBox{
			font-size: 11px;
			width:300px;
		}

		#mainInfo tr td:last-child, #orderInfo tr td:last-child{
			border-bottom: thin solid #eee;
			/*border-left:thin solid #eee;*/
		}

		#mainInfo td{
			vertical-align:top;
		}

		h2{
			font-size: 18px;
			display: block;
			text-align: center;
		}

		#order_slot{
			margin-left:20px;
			float:right;
		}

		tr.sign_head td{
			border: thin solid #eee;
			text-align:center;
			font-weight: bold;
		}

		tr.sign_space td{
			height:50px;
			border: thin solid #eee;
			text-align:center;
		}

		tr.sign_name td{
			font-size: 14px;
			border: thin solid #eee;
		}

		tr.spacer td{
			height:8px;
			display: block;
		}

		.fine{
			font-size: 11px;
		}

	</style>
</head>
<body>
<div id="wrapper">
	<table id="main_table">
		<tbody>
			<tr>
				<td id="merchant_detail">
					<table border="0" cellpadding="4" cellspacing="0" id="mainLogo">
						<tbody>
							<tr><h2>DELIVERY NOTE</h2><br />
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
								<td colspan="2"><strong>Merchant Info</strong></td>
							</tr>

							<tr>
								<td>Store Name<hr /><span class="fine">Nama Toko</span></td>
								<td><?php print $main_info['app_name'].'<br /><span class="fine">'.$main_info['app_domain'].'</span>';?></td>
							</tr>
							<tr>
								<td>Transaction ID<hr /><span class="fine">Kode Transaksi</span></td>
								<td><?php print $main_info['merchant_trans_id'];?></td>
							</tr>
<?php 
/*
    [mc_email] => ganti@bajuresmi.net.com.id
    [mc_street] => 2345678
    [mc_district] => Kebayoran
    [mc_city] => Jakarta Selatan
    [mc_province] => DKI
    [mc_country] => Indonesia
    [mc_zip] => 1234578
    [mc_phone] => 08765432
    [mc_mobile] => 2345678
    [contact_person]
*/

    //print_r($main_info);
$merchant_info = '';
$merchant_info = ($main_info['m_pic']=='')?$main_info['mc_pic'].'<br />':$main_info['m_pic'].'<br />';
$merchant_info .= ($main_info['m_street']=='')?$main_info['mc_street'].'<br />': $main_info['m_street'].'<br />';
$merchant_info .= ($main_info['m_district'] == '')?$main_info['mc_district'].'<br />':$main_info['m_district'].'<br />';
$merchant_info .= ($main_info['m_city'] == '')?$main_info['mc_city'].',':$main_info['m_city'].',';
$merchant_info .= ($main_info['m_zip']=='')?$main_info['mc_zip'].'<br />':$main_info['m_zip'].'<br />';
$merchant_info .= ($main_info['m_country']=='')?$main_info['mc_country'].'<br />':$main_info['m_country'].'<br />';
$merchant_info .= ($main_info['m_phone'] == '')?'Phone : '.$main_info['mc_phone']:'Phone : '.$main_info['m_phone'];


?>							
							<tr>
								<td colspan="2">Store Detail</td>
							</tr>
							<tr>
								<td colspan="2"><?php print trim($merchant_info);?></td>
							</tr>
						</tbody>
					</table>
					<table width="100%" cellpadding="4" cellspacing="0" id="orderInfo">
						<tbody>
							<tr>
								<td colspan="2"><strong>Package Info</strong></td>
							</tr>
							<tr>
								<td class="row_label">Dimension ( L x W x H )<hr />
									<span class="fine">Dimensi ( P x L x T )</span></td>
								<td><?php print $main_info['length'].' cm x '.$main_info['width'].' cm x '.$main_info['height'];?> cm</td>
							</tr>
							<tr>
								<td>Weight<hr /><span class="fine">Berat</span></td>
								<td><?php print get_weight_range($main_info['weight']);?></td>
							</tr>
						</tbody>
					</table>
				</td>
				<td id="order_detail">
					<table width="100%" cellpadding="4" cellspacing="0" id="orderInfo">
						<tbody>
							<tr>
								<td colspan="2"><strong>Delivery Info</strong></td>
							</tr>
							<tr>
								<td class="row_label">Delivery Type<hr /><span class="fine">Jenis Pengiriman</span></td>
								<td><?php print $main_info['delivery_type'];?></td>
							</tr>
							<tr>
								<td class="row_label">Delivery Number<hr /><span class="fine">No. Pengiriman</span></td>
								<td><?php print $main_info['delivery_id'];?></td>
							</tr>
							<tr>
								<td>Delivery Date<hr /><span class="fine">Tanggal Pengiriman</span></td>
								<td><?php print $main_info['assignment_date'];?> <span id="order_slot">Order Slot: <?php print $main_info['assignment_timeslot'];?></span></td>
							</tr>
							<tr>
								<td colspan="2"><strong>Order Detail</strong></td>
							</tr>

							<tr>
								<td class="row_label">Delivered To<hr /><span class="fine">Penerima</span></td>
								<td><?php print ($main_info['recipient_name'] == "")?$main_info['buyer_name']:$main_info['recipient_name'];?></td>
							</tr>
							<tr>
								<td>Shipping Address<hr /><span class="fine">Alamat Tujuan</span></td>
								<td><?php print $main_info['shipping_address'];?></td>
							</tr>
							<tr>
								<td>Phone<hr /><span class="fine">Telepon</span></td>
								<td><?php print $main_info['phone'];?></td>
							</tr>

							<tr>
								<td>Email<hr /><span class="fine">Alamat Email</span></td>
								<td><?php print $main_info['email'];?></td>
							</tr>
							<tr>
								<td>Direction<hr /><span class="fine">Petunjuk Jalan</span></td>
								<td><?php print $main_info['directions'];?></td>
							</tr>
						</tbody>
					</table>

					<?php echo $this->table->generate(); ?>
				</td>
				
				<td id="sign_boxes">
					<table border="0" cellpadding="4" cellspacing="0" id="signBox">
						<tbody>
							<tr class="sign_head">
								<td>Created By</td>
								<td>Online Store</td>
							</tr>
							<tr class="sign_space">
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<tr class="sign_name">
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<tr class="sign_head">
								<td>Goods Received By</td>
								<td>Cash Received By</td>
							</tr>
							<tr class="sign_space">
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<tr class="sign_name">
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<tr class="sign_head">
								<td>Reporting</td>
								<td>Staff Dispatch Admin</td>
							</tr>
							<tr class="sign_space">
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<tr class="sign_name">
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<tr class="sign_head">
								<td>Finance</td>
								<td>Courier</td>
							</tr>
							<tr class="sign_space">
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<tr class="sign_name">
								<td>&nbsp;</td>
								<td><?php print $main_info['courier_name'];?></td>
							</tr>
						</tbody>
					</table>


				</td>
			</tr>
		</tbody>
	</table>
</div>
</body>
</html>
