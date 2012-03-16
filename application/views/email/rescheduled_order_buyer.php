<html>
<body>
<?php if($logo_url != "" ): ?>
	<p><img src="<?php print $logo_url?>" alt="<?php print $logo_url?>" /></p>
<?php endif;?>
<p>Dear <?php print $buyerfullname?>,</p>

<p>
	Thank you for making purchase at <?php print $merchantname; ?> and choosing Jayon Express Cash-On-Delivery payment option.<br />
	However, as per request from merchant, your delivery date has been rescheduled to :<br />
	<?php print $new_date;?>
</p>
<p>
	Below is the summary of your transaction:
</p>
<p>
	Transaction ID : <?php print $merchant_trans_id;?><br />
	Delivery ID : <?php print $delivery_id;?><br />
</p>
	<?php
	if($detail):?>
<p>
	Order Detail :<br />
	<?php print $detail->generate();?>
</p>
	<?php endif ?>
<p>
	Delivery date changes and rescheduling shall proceed under both merchant and buyer consent, therefore, 
	if you think this changes is without your consent and approval, please contact you merchant to resolve and get a date and time you can agreed upon. 
</p>
<?php if($signature != "" ): ?>
<p>
	Thank you,
	<?php print $signature;?><br /><br />
	Supported by Jayon Express team
</p>
<?php else:?>
<p>
	Thank you,
	Jayon Express team
</p>
<?php endif;?>

</body>
</html>