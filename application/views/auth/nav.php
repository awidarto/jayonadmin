<?php
    function set_hilite($urlpattern){
        $hilite = preg_match('/'.$urlpattern.'/',current_url());
        return ($hilite)?'nav_current':'';
    }

?>
<div id="nav_container">
	<ul id="navigation">
	<?php
		//print_r($this->session->userdata);
		if(logged_in())
		{
		?>
			<li class="<?php print set_hilite('admin\/dashboard')?>" ><?php echo anchor('admin/dashboard', 'Dashboard<br />&nbsp;'); ?></li>
			<li class="<?php print set_hilite('admin\/delivery\/incoming')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/incoming', 'Incoming<br />Orders'); } ?></li>
			<li class="<?php print set_hilite('admin\/delivery\/zoning')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/zoning', 'Zone<br />Assignment'); } ?></li>
			<li class="<?php print set_hilite('admin\/delivery\/assigned')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/assigned', 'Assigned<br />Orders'); } ?></li>
			<li class="<?php print set_hilite('admin\/delivery\/delivered')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/delivered', 'Delivered<br />Orders'); } ?></li>
			<li class="<?php print set_hilite('admin\/delivery\/revoked')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/revoked', 'Revoked<br />Orders'); } ?></li>
			<li class="<?php print set_hilite('admin\/delivery\/rescheduled')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/rescheduled', 'Rescheduled<br />Orders'); } ?></li>
			<li class="<?php print set_hilite('admin\/delivery\/log')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/log', 'Delivery<br />Log'); } ?></li>
			<li class="<?php print set_hilite('admin\/location\/tracker')?>" ><?php if(user_group('admin')) { echo anchor('admin/location/tracker', 'Location<br />Tracker'); } ?></li>
			<li class="<?php print set_hilite('admin\/location\/log')?>" ><?php if(user_group('admin')) { echo anchor('admin/location/log', 'Location<br />Log'); } ?></li>
			<li class="<?php print set_hilite('admin\/apps')?>" ><?php if(user_group('admin')) { echo anchor('admin/apps/manage', 'Application<br />Keys'); } ?></li>
			<li class="<?php print set_hilite('admin\/members')?>" ><?php if(user_group('admin')) { echo anchor('admin/members/manage', 'Members<br />&nbsp;'); } ?></li>
			<li class="<?php print set_hilite('admin\/couriers')?>" ><?php if(user_group('admin')) { echo anchor('admin/couriers/manage', 'Couriers<br />&nbsp;'); } ?></li>
			<li class="<?php print set_hilite('admin\/devices')?>" ><?php if(user_group('admin')) { echo anchor('admin/devices/manage', 'Devices<br />&nbsp;'); } ?></li>
			<li class="<?php print set_hilite('admin\/users')?>" ><?php if(user_group('admin')) { echo anchor('admin/users/manage', 'Admins<br />&nbsp;'); } ?></li>
			<li class="<?php print set_hilite('admin\/holidays')?>" ><?php if(user_group('admin')) { echo anchor('admin/holidays/manage', 'Holidays<br />&nbsp;'); } ?></li>
		<?php
		}
		else
		{
		?>
			<li class="<?php print set_hilite('login')?>"><?php echo anchor('login', 'Login'); ?></li>
		<?php
			/*<li class="<?php print set_hilite('register')?>"><?php echo anchor('register', 'Register'); ?></li>*/
		}
	
	?>
	</ul>
</div>
<div class="clear"></div>
