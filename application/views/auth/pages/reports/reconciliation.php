<?php
$week = date('W',time());
$year = date('Y',time());
print 'Week : '.$week.'<br />';

for($i = 1; $i < 53; $i++){
	print 'Week : '.$i.' '.$year.' From : '.date('d-m-Y', strtotime('1 Jan '.$year.' +'.($i - 1).' weeks')).' - ';
	print ' To : '.date('d-m-Y', strtotime('1 Jan '.$year.' +'.$i.' weeks - 1 day')).'<br />';
}


?>

