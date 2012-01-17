<?php
    function set_hilite($urlpattern){
        $hilite = preg_match('/'.$urlpattern.'/',current_url());
        return ($hilite)?'nav_current':'';
    }

?>
	<script> 
		$(document).ready(function() {
			$('#nav li').hover(
			        function () {
			            //show its submenu
			            $('ul', this).slideDown(100);

			        }, 
			        function () {
			            //hide its submenu
			            $('ul', this).slideUp(100);         
			        }
			    );
			/*
			$('#nav li .parent').hover(
		        function () {
					$(this).css('background','white');
					$(this).css('color','black');
		        }, 
		        function () {
					$(this).css('background','transparent');
					$(this).css('color','white');
		        }
				
			);
			*/
		});
	</script>
	<ul id="nav">
	<?php
		//print_r($this->session->userdata);
		if(logged_in())
		{
		?>
			<li class="<?php print set_hilite('admin\/dashboard')?>" ><?php echo anchor('admin/dashboard', 'Dashboard'); ?></li>
			<li class="<?php print set_hilite('admin\/delivery')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/incoming', 'Orders'); } ?>
				<ul>
					<li class="<?php print set_hilite('admin\/delivery\/incoming')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/incoming', 'Incoming Orders'); } ?></li>
					<li class="<?php print set_hilite('admin\/delivery\/zoning')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/zoning', 'Zone Assignment'); } ?></li>
					<li class="<?php print set_hilite('admin\/delivery\/assigned')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/assigned', 'Assigned Orders'); } ?></li>
					<li class="<?php print set_hilite('admin\/delivery\/dispatched')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/dispatched', 'Dispatched Orders'); } ?></li>
					<li class="<?php print set_hilite('admin\/delivery\/delivered')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/delivered', 'Delivered Orders'); } ?></li>
					<li class="<?php print set_hilite('admin\/delivery\/revoked')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/revoked', 'Revoked Orders'); } ?></li>
					<li class="<?php print set_hilite('admin\/delivery\/rescheduled')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/rescheduled', 'Rescheduled Orders'); } ?></li>
					<li class="<?php print set_hilite('admin\/delivery\/log')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/log', 'Delivery Log'); } ?></li>
				</ul>
			</li>
			<li class="<?php print set_hilite('admin\/location\/log')?>" ><?php if(user_group('admin')) { echo anchor('admin/location/log', 'Locations'); } ?>
				<ul>
					<li class="<?php print set_hilite('admin\/location\/log')?>" ><?php if(user_group('admin')) { echo anchor('admin/location/log', 'Location Log'); } ?></li>
					<li class="<?php print set_hilite('admin\/location\/tracker')?>" ><?php if(user_group('admin')) { echo anchor('admin/location/tracker', 'Location Tracker'); } ?></li>
				</ul>
			</li>
			<li class="<?php print set_hilite('admin\/apps')?>" ><?php if(user_group('admin')) { echo anchor('admin/apps/manage', 'System'); } ?>
				<ul>
					<li class="<?php print set_hilite('admin\/apps')?>" ><?php if(user_group('admin')) { echo anchor('admin/apps/manage', 'Application Keys'); } ?></li>
					<li class="<?php print set_hilite('admin\/devices')?>" ><?php if(user_group('admin')) { echo anchor('admin/devices/manage', 'Devices '); } ?></li>
					<li class="<?php print set_hilite('admin\/holidays')?>" ><?php if(user_group('admin')) { echo anchor('admin/holidays/manage', 'Holidays'); } ?></li>
				</ul>
			</li>
			<li class="<?php print set_hilite('admin\/users')?>" ><?php if(user_group('admin')) { echo anchor('admin/users/manage', 'Users'); } ?>
				<ul>
					<li class="<?php print set_hilite('admin\/users')?>" ><?php if(user_group('admin')) { echo anchor('admin/users/manage', 'Administrators'); } ?></li>
					<li class="<?php print set_hilite('admin\/members')?>" ><?php if(user_group('admin')) { echo anchor('admin/members/manage', 'Members'); } ?></li>
					<li class="<?php print set_hilite('admin\/couriers')?>" ><?php if(user_group('admin')) { echo anchor('admin/couriers/manage', 'Couriers'); } ?></li>
				</ul>
			</li>
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
<div class="clear"></div>
