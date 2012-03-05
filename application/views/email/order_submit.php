<html>
<body>
<?php if($app->logo_url != "" ): ?>
	<p><img src="<?php print $app->logo_url?>" alt="<?php print $app->logo_url?>" /></p>
<?php endif;?>
<p>Dear <?php print $fullname?>,</p>

<p>
	Thank you for making purchase at <?php print $merchantname; ?> and choosing Jayon Express Cash-On-Delivery payment option. 
	Below is the summary of your transaction:
</p>
<p>
	Transaction ID : <?php print $merchant_trx_id;?><br />
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
	Your purchase is being processed. We strife to make delivery of your merchandise according to the delivery date and time that you have chosen.
</p>
<?php if($app->signature != "" ): ?>
<p>
	Thank you,
	<?php print $app->signature;?><br /><br />
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
