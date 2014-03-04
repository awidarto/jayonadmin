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

        $('#show_last').on('click',function(){
            $('#last_query').toggle();
        });


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

<div id="form">
	<div class="form_box">
		<form method="get">
			<table style="width:500px;" id="recon_select">
				<tr>
					<td>Courier</td>
					<td><?php print form_dropdown('user_scopes',$couriers,null,'id = "user_scopes"'); ?></td>
					<td>Year<?php print form_dropdown('year_scopes',$years,$year,'id = "year_scopes"');?></td>
					<td></td>
				</tr>
				<tr>
					<td colspan="4"><strong>Generate By :</strong></td>
				</tr>
				<tr>
					<td>Month</td>
					<td><?php print form_dropdown('month_scopes',$months,$month,'id = "month_scopes"');?></td>
					<td><span id="get_month" class="action_link" >Get By Month</span></td>
					<td></td>
				</tr>
				<tr>
					<td colspan="4" style="text-align:center;"><strong>OR</strong></td>
				</tr>
				<tr>
					<td>Week Number</td>
					<td><?php print form_dropdown('week_scopes',$weeks,$week,'id = "week_scopes"');?></td>
					<td><span id="get_week" class="action_link" >Get By Week</span></td>
					<td></td>
				</tr>
				<tr>
					<td colspan="4" style="text-align:center;"><strong>OR</strong></td>
				</tr>
				<tr>
					<td>From Date</td>
					<td><?php print form_input(array('name'=>'date_from','id'=>'date_from','class'=>'text','value'=>$from));?></td>
					<td><?php print 'To '.form_input(array('name'=>'date_to','id'=>'date_to','class'=>'text','value'=>$to));?></td>
					<td><span id="get_date_range" class="action_link" >Get By Date Range</span></td>
				</tr>
			</table>
		</form>
	</div>
</div>
<div>
	<h3><?php print $type.' '.$period; ?></h3>
	<?php print $recontab; ?>
</div>

</div>
