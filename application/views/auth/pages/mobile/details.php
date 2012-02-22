


<?php
	//print_r($order);
	print '<br />';
	print form_button('delivered','delivered','id="delivered"');
	print '<br />';
	print form_button('revoked','revoked','id="revoked"');
	print '<br />';
	print form_button('noshow','no show','id="noshow"');
	print '<br />';
	print form_button('rescheduled','rescheduled','id="rescheduled"');
?>
Note :<br />
<?php
	print form_textarea('note','notes');
?>


