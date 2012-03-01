<html>
<body>
<p>Dear <?php print $fullname?>,</p>

<p>
	Thank you for making purchase at <?php print $merchantname; ?> and choosing Jayon Express Cash-On-Delivery payment option. 
	Below is the summary of your transaction:
</p>
<p>
	Transaction ID : <?php print $merchant_trx_id;?><br />
	Delivery ID : <?php print $delivery_id;?><br />
</p>
<p>
	Order Detail :<br />
	<?php
	if($detail){
		print $detail->generate(); 
	}else{
		print 'N/A';
	}
	?>
</p>
<p>
	Your purchase is being processed. We strife to make delivery of your merchandise according to the delivery date and time that you have chosen.
</p>
<p>
	Thank you,
	Jayon Express team
</p>
</body>
</html>
