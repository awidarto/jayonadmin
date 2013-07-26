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
			width:200px;
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
			font-size: 11px;

		}

		tr.sign_space td{
			font-size: 12px;
			height:50px;
			border: thin solid #eee;
			text-align:center;
		}

		tr.sign_name td{
			font-size: 12px;
			border: thin solid #eee;
		}

		tr.spacer td{
			height:8px;
			display: block;
		}

		.fine{
			font-size: 11px;
		}

		td.lsums{
			text-align: right;
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
							<?php if($main_info['show_merchant']):?>
								<tr>
									<td>Merchant Name<hr /><span class="fine">Nama Penjual</span></td>
									<td>
										<?php print $main_info['merchant'].'</span>';?>
									</td>
								</tr>
							<?php endif;?>
							<?php if($main_info['show_shop']):?>
								<tr>
									<td>Store Name<hr /><span class="fine">Nama Toko</span></td>
									<td>
										<?php //print $main_info['app_name'].'<br /><span class="fine">'.$main_info['app_name'].'</span>';?>
										<?php print $main_info['app_name'].'</span>';?>
									</td>
								</tr>
							<?php endif;?>
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
				</td>
				<td id="order_detail">
					<table width="100%" cellpadding="4" cellspacing="0" id="orderInfo">
						<tbody>
							<tr>
								<td colspan="2"><strong>Delivery Info</strong></td>
							</tr>
							<tr>
								<td class="row_label">Delivery Type</td>
								<td>
									<?php print $main_info['delivery_type'];?>
									<?php if($main_info['delivery_type'] == 'CCOD'):?>
										<?php if($main_info['ccod_method'] == '' || $main_info['ccod_method'] == 'full'): ?>
											- Pembayaran Penuh
										<?php elseif($main_info['ccod_method'] == 'installment'): ?>
											- Cicilan
										<?php endif;?>
									<?php endif;?>

									<?php if($main_info['delivery_type'] == 'COD'):?>
										<?php if($main_info['cod_method'] == '' || $main_info['cod_method'] == 'cash'): ?>
											- Tunai
										<?php elseif($main_info['cod_method'] == 'debit'): ?>
											- Debit
										<?php endif;?>
									<?php endif;?>
								</td>
							</tr>
							<tr>
								<td class="row_label">Delivery Number</td>
								<td><?php print $main_info['delivery_id'];?></td>
							</tr>
							<tr>
								<td>Delivery Date</td>
								<td><?php print $main_info['assignment_date'];?> <span id="order_slot">Order Slot: <?php print $main_info['assignment_timeslot'];?></span></td>
							</tr>
							<tr>
								<td colspan="2"><strong>Order Detail</strong></td>
							</tr>


						<?php if($main_info['delivery_type'] == 'PS'):?>
							<tr>
								<td class="row_label">Picked Up From</td>
								<td><?php print ($main_info['recipient_name'] == "")?$main_info['buyer_name']:$main_info['recipient_name'];?></td>
							</tr>
							<tr>
								<td>Pick Up Address</td>
								<td><?php print $main_info['shipping_address'];?></td>
							</tr>
						<?php else: ?>

							<tr>
								<td class="row_label">Delivered To</td>
								<td><?php print ($main_info['recipient_name'] == "")?$main_info['buyer_name']:$main_info['recipient_name'];?></td>
							</tr>
							<tr>
								<td>Shipping Address</td>
								<td><?php print $main_info['shipping_address'];?></td>
							</tr>

						<?php endif; ?>
							<tr>
								<td>Phone</td>
								<td>
                                    <?php
                                        print ($main_info['phone'] !='' && $main_info['phone'] !='-' && !is_null($main_info['phone']) )?$main_info['phone'].'<br />':'';
                                        print ($main_info['mobile1'] !='' && $main_info['mobile1'] !='-' && !is_null($main_info['mobile1']) )?$main_info['mobile1'].'<br />':'';
                                        print ($main_info['mobile2'] !='' && $main_info['mobile2'] !='-' && !is_null($main_info['mobile2']) )?$main_info['mobile2'].'<br />':'';
                                    ?>
                                </td>
							</tr>

							<tr>
								<td>Email</td>
								<td><?php print $main_info['email'];?></td>
							</tr>
							<tr>
								<td>Direction</td>
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
					<table width="100%" cellpadding="4" cellspacing="0" >
						<tbody>
							<tr>
								<td colspan="2"><strong>Package Info</strong></td>
							</tr>
							<tr>
								<td>Dimension<br />
									<span class="fine">( L x W x H , in cm )</span></td>
								<td><?php print $main_info['length'].' x '.$main_info['width'].' x '.$main_info['height'];?></td>
							</tr>
							<tr>
								<td>Weight</td>
								<td><?php print get_weight_range($main_info['weight']);?></td>
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
