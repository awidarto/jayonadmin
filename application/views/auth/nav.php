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
		if(logged_in() && set_hilite('mobile') == '')
		{
		?>
			<li class="<?php print set_hilite('admin\/dashboard')?>" ><?php echo anchor('admin/dashboard', 'Dashboard'); ?></li>
			<li class="<?php print set_hilite('admin\/delivery')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/incoming', 'Orders <span class="badge" id="total_changed"></span>'); } ?>
				<ul>
					<li class="<?php print set_hilite('admin\/delivery\/incoming')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/incoming', 'Incoming Orders <span class="badge" id="total_changed_incoming"></span>'); } ?></li>
                    <?php
                    /*
                    <li class="<?php print set_hilite('admin\/delivery\/pickup')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/pickup', 'Incoming Orders PU <span class="badge" id="total_changed_incoming"></span>'); } ?></li>
					*/
                    ?>
                    <li class="<?php print set_hilite('admin\/delivery\/canceled')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/canceled', 'Canceled Orders'); } ?></li>
					<li class="<?php print set_hilite('admin\/delivery\/zoning')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/zoning', 'Device Zone Assignment'); } ?></li>
					<li class="<?php print set_hilite('admin\/delivery\/assigned')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/courierassign', 'Courier Assignment'); } ?></li>
					<li class="<?php print set_hilite('admin\/delivery\/dispatched')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/dispatched', 'In Progress Orders'); } ?></li>
					<li class="<?php print set_hilite('admin\/delivery\/delivered')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/delivered', 'Delivery Status'); } ?></li>
					<?php /*
                    <li class="<?php print set_hilite('admin\/delivery\/revoked')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/revoked', 'Revoked Orders'); } ?></li>
                    <li class="<?php print set_hilite('admin\/delivery\/rescheduled')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/rescheduled', 'Rescheduled Orders'); } ?></li>
                    */?>
					<li class="<?php print set_hilite('admin\/delivery\/archived')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/archived', 'Order Archive'); } ?></li>
					<li class="<?php print set_hilite('admin\/delivery\/running')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/running', 'Last 30 Days'); } ?></li>
					<li class="<?php print set_hilite('admin\/delivery\/log')?>" ><?php if(user_group('admin')) { echo anchor('admin/delivery/log', 'Delivery Log'); } ?></li>
				</ul>
			</li>
			<li class="<?php print set_hilite('admin\/location\/log')?>" ><?php if(user_group('admin')) { echo anchor('admin/location/log', 'Locations'); } ?>
				<ul>
                    <li class="<?php print set_hilite('admin\/location\/router')?>" ><?php if(user_group('admin')) { echo anchor('admin/location/router', 'Router'); } ?></li>
					<li class="<?php print set_hilite('admin\/location\/log')?>" ><?php if(user_group('admin')) { echo anchor('admin/location/log', 'Location Log'); } ?></li>
				</ul>
			</li>
            <?php
            /*
            <li class="<?php print set_hilite('admin\/admanager')?>" ><?php if(user_group('admin')) { echo anchor('admin/admanager', 'Advertising'); } ?>
                <ul>
                    <li class="<?php print set_hilite('admin\/admanager')?>" ><?php if(user_group('admin')) { echo anchor('admin/admanager', 'Ad Manager'); } ?></li>
                    <li class="<?php print set_hilite('admin\/adcategory')?>" ><?php if(user_group('admin')) { echo anchor('admin/adcategory', 'Ad Category'); } ?></li>
                </ul>
            </li>
            */
            ?>
			<li class="<?php print set_hilite('admin\/apps\/manage')?>" ><?php if(user_group('admin')) { echo anchor('admin/apps/manage', 'System'); } ?>
				<ul>
					<?php if(user_group('admin')): ?><li class="<?php print set_hilite('admin\/apps\/manage')?>" ><?php if(user_group('admin')) { echo anchor('admin/apps/manage', 'Application Keys'); } ?></li><?php endif;?>
					<?php if(user_group('admin')): ?><li class="<?php print set_hilite('admin\/devices')?>" ><?php if(user_group('admin')) { echo anchor('admin/devices/manage', 'Devices '); } ?></li><?php endif;?>
					<?php if(user_group('admin')): ?><li class="<?php print set_hilite('admin\/holidays')?>" ><?php if(user_group('admin')) { echo anchor('admin/holidays/manage', 'Holidays'); } ?></li><?php endif;?>
					<?php if(user_group('admin')): ?><li class="<?php print set_hilite('admin\/zones')?>" ><?php if(user_group('admin')) { echo anchor('admin/zones/manage', 'Zones'); } ?></li><?php endif;?>
					<?php if(user_group('admin')): ?><li class="<?php print set_hilite('admin\/slots\/manage')?>" ><?php if(user_group('admin')) { echo anchor('admin/slots/manage', 'Time Slots'); } ?></li><?php endif;?>
					<?php if(user_group('admin')): ?><li class="<?php print set_hilite('admin\/log\/access')?>" ><?php if(user_group('admin')) { echo anchor('admin/log/access', 'API Access Log'); } ?></li><?php endif;?>
					<?php if(user_group('admin')): ?><li class="<?php print set_hilite('admin\/log\/outbox')?>" ><?php if(user_group('admin')) { echo anchor('admin/log/outbox', 'Email Outbox'); } ?></li><?php endif;?>
					<?php if(user_group('admin')): ?><li class="<?php print set_hilite('admin\/options')?>" ><?php if(user_group('admin')) { echo anchor('admin/options/manage', 'Options'); } ?></li><?php endif;?>
				</ul>
			</li>
			<li class="<?php print set_hilite('admin\/reports\/statistics')?>" ><?php if(user_group('admin')) { echo anchor('admin/reports/statistics', 'Reports'); } ?>
				<ul>
					<li class="<?php print set_hilite('admin\/reports\/statistics')?>" ><?php if(user_group('admin')) { echo anchor('admin/reports/statistics', 'Statistics'); } ?></li>
					<li class="<?php print set_hilite('admin\/reports\/dist')?>" ><?php if(user_group('admin')) { echo anchor('admin/reports/dist', 'Distributions'); } ?></li>
					<li class="<?php print set_hilite('admin\/reports\/reconciliation')?>" ><?php if(user_group('admin')) { echo anchor('admin/reports/reconciliation', 'Global Reconciliation'); } ?></li>
					<li class="<?php print set_hilite('admin\/reports\/courierrecon')?>" ><?php if(user_group('admin')) { echo anchor('admin/reports/courierrecon', 'Courier Reconciliation'); } ?></li>
                    <li class="<?php print set_hilite('admin\/reports\/courierrecap')?>" ><?php if(user_group('admin')) { echo anchor('admin/reports/courierrecap', 'Courier Recap'); } ?></li>
					<li class="<?php print set_hilite('admin\/reports\/merchantrecon')?>" ><?php if(user_group('admin')) { echo anchor('admin/reports/merchantrecon', 'Merchant Reconciliation'); } ?></li>
					<li class="<?php print set_hilite('admin\/reports\/devicerecon$')?>" ><?php if(user_group('admin')) { echo anchor('admin/reports/devicerecon', 'Device Reconciliation'); } ?></li>
                    <li class="<?php print set_hilite('admin\/reports\/devicerecongen')?>" ><?php if(user_group('admin')) { echo anchor('admin/reports/devicerecongen', 'Device Reconciliation ( Manual )'); } ?></li>
					<li class="<?php print set_hilite('admin\/reports\/revenue$')?>" ><?php if(user_group('admin')) { echo anchor('admin/reports/revenue', 'Revenue Report'); } ?></li>
                    <li class="<?php print set_hilite('admin\/reports\/revenuegen')?>" ><?php if(user_group('admin')) { echo anchor('admin/reports/revenuegen', 'Revenue Report ( Manual )'); } ?></li>
                    <li class="<?php print set_hilite('admin\/reports\/invoices')?>" ><?php if(user_group('admin')) { echo anchor('admin/reports/invoices', 'Invoices'); } ?></li>
                    <li class="<?php print set_hilite('custom\/codreport\/report')?>" ><?php if(user_group('admin')) { echo anchor('custom/codreport/report', 'COD Report'); } ?></li>
                    <li class="<?php print set_hilite('admin\/invoices\/listing')?>" ><?php if(user_group('admin')) { echo anchor('admin/invoices/listing', 'Released Invoices'); } ?></li>
                    <li class="<?php print set_hilite('admin\/reports\/manifests')?>" ><?php if(user_group('admin')) { echo anchor('admin/reports/manifests', 'Manifests'); } ?></li>
                    <li class="<?php print set_hilite('admin\/reports\/deliverytime')?>" ><?php if(user_group('admin')) { echo anchor('admin/reports/deliverytime', 'Delivery Time'); } ?></li>
                    <li class="<?php print set_hilite('admin\/reports\/zonerevenue')?>" ><?php if(user_group('admin')) { echo anchor('admin/reports/zonerevenue', 'Zone Revenue Report'); } ?></li>
                    <li class="<?php print set_hilite('admin\/reports\/cityrevenue')?>" ><?php if(user_group('admin')) { echo anchor('admin/reports/cityrevenue', 'City Revenue Report'); } ?></li>
                    <li class="<?php print set_hilite('admin\/location\/distribution')?>" ><?php if(user_group('admin')) { echo anchor('admin/location/distribution', 'Buyer Distributions'); } ?></li>
				</ul>
			</li>
            <li class="<?php print set_hilite('custom\/cod\/report')?>" ><?php if(user_group('admin')) { echo anchor('custom/cod/report', 'Custom'); } ?>
                <ul>
                    <li class="<?php print set_hilite('custom\/cod\/report')?>" ><?php if(user_group('admin')) { echo anchor('custom/cod/report', 'COD Reconciliation'); } ?></li>
                    <li class="<?php print set_hilite('custom\/orderstatus\/report')?>" ><?php if(user_group('admin')) { echo anchor('custom/orderstatus/report', 'Order Status Report'); } ?></li>
                    <li class="<?php print set_hilite('custom\/orderrecon\/report')?>" ><?php if(user_group('admin')) { echo anchor('custom/orderrecon/report', 'Order Cost Reconciliation'); } ?></li>
                    <li class="<?php print set_hilite('custom\/pickuprecon\/report')?>" ><?php if(user_group('admin')) { echo anchor('custom/pickuprecon/report', 'Pickup Reconciliation'); } ?></li>
                    <li class="<?php print set_hilite('custom\/retur\/report')?>" ><?php if(user_group('admin')) { echo anchor('custom/retur/report', 'Return Report'); } ?></li>
                    <li class="<?php print set_hilite('admin\/docs\/listing')?>" ><?php if(user_group('admin')) { echo anchor('admin/docs/listing', 'Released Documents'); } ?></li>
                    <li class="<?php print set_hilite('admin\/awb\/listing')?>" ><?php if(user_group('admin')) { echo anchor('admin/awb/listing', 'AWB Generator'); } ?></li>
                </ul>
            </li>
			<li class="<?php print set_hilite('admin\/users')?>" ><?php if(user_group('admin')) { echo anchor('admin/users/manage', 'Users'); } ?>
				<ul>
					<li class="<?php print set_hilite('admin\/users')?>" ><?php if(user_group('admin')) { echo anchor('admin/users/manage', 'Administrators'); } ?></li>
					<li class="<?php print set_hilite('admin\/members\/merchant')?>" ><?php if(user_group('admin')) { echo anchor('admin/members/merchant', 'Merchants'); } ?></li>
					<li class="<?php print set_hilite('admin\/members\/buyer')?>" ><?php if(user_group('admin')) { echo anchor('admin/members/buyer', 'Buyers'); } ?></li>
					<li class="<?php print set_hilite('admin\/couriers')?>" ><?php if(user_group('admin')) { echo anchor('admin/couriers/manage', 'Couriers'); } ?></li>
				</ul>
			</li>
		<?php
		}else if(set_hilite('mobile') != '') {
			?>
			<li class="<?php print set_hilite('mobile\/device$')?>" ><?php if(user_group('admin')) { echo anchor('mobile/device', 'Orders'); } ?></li>
			<li class="<?php print set_hilite('mobile\/device\/location')?>" ><?php if(user_group('admin')) { echo anchor('mobile/device/location', 'Location'); } ?></li>
			<li class="<?php print set_hilite('mobile\/device\/options')?>" ><?php if(user_group('admin')) { echo anchor('mobile/device/options', 'Options'); } ?></li>

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
	<script type="text/javascript">
    	$(document).ready(function(){

    		var seconds = new Date().getTime() / 1000;

    		function getChanges(){
    			var times = new Date().getTime();
    			$.post('<?php print base_url() ?>admin/uichanges?'+ times,{ lastupdate: seconds },function(data){
    				$('#total_changed').html(data.total_changed);
    				seconds = new Date().getTime() / 1000;
    			}, 'json');
    		}

    		getChanges();

    		self.setInterval(function(){
                getChanges();
            },20000);
    	});
	</script>
<div class="clear"></div>
