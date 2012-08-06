<script type="text/javascript">
	$(document).ready(function() {
		$('#date_from').datepicker({ dateFormat: 'yy-mm-dd' });
		$('#date_to').datepicker({ dateFormat: 'yy-mm-dd' });
		
		$('#get_week').click(function(){
				alert('week link');
			}
		);

		$('#get_date_range').click(function(){
				alert('date link');
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

</style>

<?php
	
	$opts = array('Global','Merchant','Courier');

	for($i=2012;$i < 2100;$i++){
		$years[$i]=$i;
	}

?>

<div id="form">
	<div class="form_box">
		<form method="get">
			<table style="width:500px;" id="recon_select">
				<tr>
					<td>User Type</td>
					<td><?php print form_dropdown('user_scopes',$opts,array('id'=>'user_scopes')); ?></td>
					<td>Year<?php print form_dropdown('year_scopes',$years);?></td>
					<td></td>
				</tr>
				<tr>
					<td colspan="4"><strong>Generate By :</strong></td>
				</tr>
				<tr>
					<td>Week Number</td>
					<td><?php print form_dropdown('week_scopes',$years);?></td>
					<td><span id="get_week" class="action_link" >Get By Week</span></td>
					<td></td>
				</tr>
				<tr>
					<td colspan="4" style="text-align:center;"><strong>OR</strong></td>
				</tr>
				<tr>
					<td>From Date</td>
					<td><?php print form_input(array('name'=>'date_from','id'=>'date_from','class'=>'text'));?></td>
					<td><?php print 'To '.form_input(array('name'=>'date_to','id'=>'date_to','class'=>'text'));?></td>
					<td><span id="get_date_range" class="action_link" >Get By Date Range</span></td>
				</tr>
			</table>
		</form>
	</div>
</div>
<div>
	<?php print $recontab; ?>
</div>
