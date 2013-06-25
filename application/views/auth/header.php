<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		
		<style type="text/css">
		</style>

		<?php echo $this->ag_asset->load_css('style.css');?>
		<?php echo $this->ag_asset->load_css('menu.css');?>
		<?php echo $this->ag_asset->load_css('oi.css');?>
		<?php echo $this->ag_asset->load_css('jquery.fancybox.css');?>

		<?php echo $this->ag_asset->load_css('datatables_page.css','jquery-datatables');?>
		<?php echo $this->ag_asset->load_css('datatables_table.css','jquery-datatables');?>
		<?php echo $this->ag_asset->load_css('1.9_full_numbers.css','jquery-datatables');?>
		<?php echo $this->ag_asset->load_css('TableTools.css','jquery-datatables');?>
		<?php echo $this->ag_asset->load_css('jquery-ui-1.8.16.custom.css','jquery-ui/flick');?>
		<?php echo $this->ag_asset->load_css('rickshaw.min.css');?>

		
		<?php echo $this->ag_asset->load_script('jquery-1.7.1.min.js');?>
		<?php echo $this->ag_asset->load_script('jquery.datatables.min.js','jquery-datatables');?>
		<?php echo $this->ag_asset->load_script('TableTools.min.js','jquery-datatables');?>
		<?php echo $this->ag_asset->load_script('ZeroClipboard.js','jquery-datatables');?>


		<?php echo $this->ag_asset->load_script('jquery-ui-1.8.16.custom.min.js','jquery-ui');?>
		<?php echo $this->ag_asset->load_script('jquery-ui-timepicker-addon.js','jquery-ui');?>
		<?php echo $this->ag_asset->load_script('jquery.fancybox.pack.js','fancybox');?>
		<?php echo $this->ag_asset->load_script('jquery.simple-color.js');?>
		<?php echo $this->ag_asset->load_script('stickyheader.jquery.js');?>

		<?php echo $this->ag_asset->load_script('d3.min.js','vendor');?>
		<?php echo $this->ag_asset->load_script('d3.layout.min.js','vendor');?>
		<?php echo $this->ag_asset->load_script('rickshaw.min.js');?>

		<?php echo $this->ag_asset->load_script('jquery.jeditable.mini.js');?>

		<title><?php echo $this->config->item('site_title'); ?></title>

		<script>
			$(document).ready(function() {

				$('#flash_message').delay(1500).slideUp('slow');

				function openPhoto(delivery_id,title){
					$.fancybox.open([
					        {
					            href : '<?php print base_url();?>public/receiver/' + delivery_id + '.jpg',                
					            title : title
					        }
					    ]);
				}

			});
		</script>
	
	</head>
	<body>
		<?php if($this->oi->has_messages('')):?>  
	        <div id="flash_message" ><?php echo $this->oi->messages(NULL)?></div>
		<?php endif?>
		<div id="header">
			<div id="logo">
				&nbsp;
			</div>
		</div>
		<?php if(logged_in()):?>
			<div id="nav_bg">
				<?php $this->load->view($this->config->item('auth_views_root') . 'nav'); ?>
			</div>
		<?php endif;?>
		<?php print $this->breadcrumb->output();?>
		<div id="container">
		<?php if(logged_in() === TRUE):?>
			<div id="identity" style="clear:both;display:block;height:35px;">
				<div id="page_title" style="float:left;width:500px;"><h2 style="margin:0px;text-align:left;"><?php print (isset($data['page_title']))?$data['page_title']:'';?></h2></div>
				<div id="user_info"  style="float:right;width:400px;padding:0px;">
					You are currently login as <?php echo username();?><br />
					<?php echo anchor('admin/users/editprofile', 'Edit Personal Profile'); ?>&nbsp;&nbsp;<?php echo anchor('admin/admin/changepass', 'Change password'); ?>&nbsp;&nbsp;<?php echo anchor('logout', 'Logout'); ?>
				</div>
			</div>
		<?php endif;?>
		