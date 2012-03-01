<html>
<body>
<p>Dear <?php print $fullname?>,</p>

<p>
	Your order made at <?php print $ordertime; ?> with transaction id of <?php print $merchant_trx_id;?>
	has been processed and is scheduled for delivery.
</p>
<p>
	Your delivery id is <?php print $delivery_id;?>. You may check the progress at Jayon Express’ web site.
</p>
<p>
	Thank you,
	Jayon Express team
</p>
</body>
</html>