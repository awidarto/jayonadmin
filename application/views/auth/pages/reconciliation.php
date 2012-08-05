<?php
	$opts = array('all','merchant','courier');

	print form_dropdown('user_scopes',$opts);
	

	print 'Year '.form_dropdown('year_scopes',$opts);

	print '<br />';

	print 'Week # '.form_dropdown('week_scopes',$opts);

	print form_button('get_week','Get By Week');
	
	print '<br />';

	print 'From '.form_dropdown('date_from',$opts);

	print 'To '.form_dropdown('date_from',$opts);

	print form_button('get_date','Get By Date Range');
?>