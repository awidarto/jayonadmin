<script type="text/javascript">
	
	var base = '<?php print base_url();?>';
	var controller = '<?php print $controller; ?>';

	$(document).ready(function() {
		$('#date_from').datepicker({ dateFormat: 'yy-mm-dd' });
		$('#date_to').datepicker({ dateFormat: 'yy-mm-dd' });
		
		$('#get_week').click(function(){

				var user_scopes = $('#user_scopes').val();
				var year = $('#year_scopes').val();
				var week = $('#week_scopes').val();
				var link = user_scopes +'/'+ year +'/week/'+ week;
				window.location = base + controller + link;

			}
		);

		$('#get_month').click(function(){

				var user_scopes = $('#user_scopes').val();
				var year = $('#year_scopes').val();
				var week = $('#month_scopes').val();
				var link = user_scopes +'/'+ year +'/month/'+ week;
				window.location = base + controller + link;

			}
		);

		$('#get_date_range').click(function(){
				var user_scopes = $('#user_scopes').val();
				var year = $('#year_scopes').val();
				var from = $('#date_from').val();
				var to = $('#date_to').val();
				var link = user_scopes +'/'+ year +'/date/'+ from +'/'+ to ;
				window.location = base + controller + link;
			}
		);



	});



</script>

<style type="text/css">

.action_link{
	cursor:pointer;
	text-decoration: underline;
}

table#recon_select td input{
	width:80px;
}

table#recon_select tr.dark {
	background-color: #aaa;
}

table#recon_select td {
	padding: 3px;
}
</style>

<?php
	
	$opts = array('Global'=>'Global','Merchant'=>'Merchant','Courier'=>'Courier');

	for($i=2012;$i < 2100;$i++){
		$years[$i]=$i;
	}

	for($i=1;$i < 53;$i++){
		$weeks[$i]=$i;
	}

	for($i=1;$i < 13;$i++){

		$mo = mktime(0, 0, 0, $i, 1, 2012);
		$months[$i]=date('F',$mo);
	}


?>

<div id="form" style="width:525px;float:left;">
	<div class="form_box">
		<form method="get">
			<table style="width:500px;" id="recon_select" cellspacing="0">
				<tr>
					<td>Year</td>
					<td><?php print form_dropdown('year_scopes',$years,$year,'id = "year_scopes"');?></td>
					<td>Scope <?php print form_dropdown('user_scopes',$opts,null,'id = "user_scopes"'); ?></td>
					<td></td>
				</tr>
				<tr>
					<td colspan="4"><strong>Filter By :</strong></td>
				</tr>
				<tr class="dark">
					<td>Month</td>
					<td><?php print form_dropdown('month_scopes',$months,$month,'id = "month_scopes"');?></td>
					<td><span id="get_month" class="action_link" >Generate</span></td>
					<td></td>
				</tr>
				<tr>
					<td colspan="4" style="text-align:center;"><strong>OR</strong></td>
				</tr>
				<tr class="dark">
					<td>Week Number</td>
					<td><?php print form_dropdown('week_scopes',$weeks,$week,'id = "week_scopes"');?></td>
					<td><span id="get_week" class="action_link" >Generate</span></td>
					<td></td>
				</tr>
				<tr>
					<td colspan="4" style="text-align:center;"><strong>OR</strong></td>
				</tr>
				<tr class="dark">
					<td>From Date</td>
					<td><?php print form_input(array('name'=>'date_from','id'=>'date_from','class'=>'text','value'=>$from));?></td>
					<td><?php print 'To '.form_input(array('name'=>'date_to','id'=>'date_to','class'=>'text','value'=>$to));?></td>
					<td><span id="get_date_range" class="action_link" >Generate</span></td>
				</tr>
			</table>
		</form>
	</div>
</div>
<div  style="width:525px;float:left;">
	<table style="width:500px;" id="recon_select" cellspacing="0">
		<tr>
			<td colspan="2"><strong>Summary</strong></td>
		</tr>
		<tr class="dark">
			<td>Total Transaction to Date</td>
			<td><?php print $total_to_date;?></td>
		</tr>
		<tr>
			<td>DO</td>
			<td><?php print $total_to_date_do;?></td>
		</tr>
		<tr>
			<td>COD</td>
			<td><?php print $total_to_date_cod;?></td>
		</tr>
		<tr>
			<td>CCOD</td>
			<td><?php print $total_to_date_ccod;?></td>
		</tr>
		<tr>
			<td>PS</td>
			<td><?php print $total_to_date_ps;?></td>
		</tr>
		<tr class="dark">
			<td>Total Transaction to From <?php print $from ;?> to <?php print $to ;?></td>
			<td><?php print $total_in_period;?></td>
		</tr>
		<tr>
			<td>DO</td>
			<td><?php print $total_in_period_do;?></td>
		</tr>
		<tr>
			<td>COD</td>
			<td><?php print $total_in_period_cod;?></td>
		</tr>
		<tr>
			<td>CCOD</td>
			<td><?php print $total_in_period_ccod;?></td>
		</tr>
		<tr>
			<td>PS</td>
			<td><?php print $total_in_period_ps;?></td>
		</tr>
		<tr class="dark">
			<td>Note :</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2">
				Expect slight innacuracies for orders prior to 01 August 2012 due to :<br />
				Changes in business model from COD only ordering to Delivery Only and COD delivery, all orders prior to that date are considered COD by default.<br />
				Promotional period with FOC deliveries for certain merchants.
			</td>
		</tr>
	</table>
</div>

<div class="clear"></div>

<div>
	<h3><?php print $period; ?></h3>
		<table>
			<caption><h4>Transaction Reports <?php print $period;?></h4></caption>
			<tbody>
				<tr>
					<td>
						<div id="statistics"  style="width:100%;height:100%;">
							<span>Total Incoming <?php print $period;?></span>
							<div id="incoming_monthly" class="stat_box">
								<img src="<?php print base_url();?>admin/graphs/rangegraph/incoming/<?php print $from.'/'.$to;?>" alt="monthly_all" />
							</div>
							<span>Delivered <?php print $period;?></span>
							<div id="delivered_monthly" class="stat_box">
								<img src="<?php print base_url();?>admin/graphs/rangegraph/delivered/<?php print $from.'/'.$to;?>" alt="monthly_all" />
							</div>
							<span>Rescheduled <?php print $period;?></span>
							<div id="rescheduled_monthly" class="stat_box">
								<img src="<?php print base_url();?>admin/graphs/rangegraph/rescheduled/<?php print $from.'/'.$to;?>" alt="monthly_all" />
							</div>
						</div>
					</td>
					<td>
						<div id="statistics"  style="width:100%;height:100%;">
							<span>Revoked <?php print $period;?></span>
							<div id="revoked_monthly" class="stat_box">
								<img src="<?php print base_url();?>admin/graphs/rangegraph/revoked/<?php print $from.'/'.$to;?>" alt="monthly_all" />
							</div>
							<span>No Show <?php print $period;?></span>
							<div id="noshow_monthly" class="stat_box">
								<img src="<?php print base_url();?>admin/graphs/rangegraph/noshow/<?php print $from.'/'.$to;?>" alt="monthly_all" />
							</div>
							<span>Archived <?php print $period;?></span>
							<div id="noshow_monthly" class="stat_box">
								<img src="<?php print base_url();?>admin/graphs/rangegraph/archived/<?php print $from.'/'.$to;?>" alt="monthly_all" />
							</div>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
</div>