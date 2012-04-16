<table>
	<caption><h4>Transaction Reports <?php print $period;?></h4></caption>
	<tbody>
		<tr>
			<td>
				<div id="statistics"  style="width:100%;height:100%;">
					<span>Total Incoming <?php print $period;?></span>
					<div id="incoming_monthly" class="stat_box">
						<img src="<?php print base_url();?>admin/graphs/monthlygraph" alt="monthly_all" />
					</div>
					<span>Delivered <?php print $period;?></span>
					<div id="delivered_monthly" class="stat_box">
						<img src="<?php print base_url();?>admin/graphs/monthlygraph/delivered" alt="monthly_all" />
					</div>
					<span>Rescheduled <?php print $period;?></span>
					<div id="rescheduled_monthly" class="stat_box">
						<img src="<?php print base_url();?>admin/graphs/monthlygraph/rescheduled" alt="monthly_all" />
					</div>
				</div>
			</td>
			<td>
				<div id="statistics"  style="width:100%;height:100%;">
					<span>Revoked <?php print $period;?></span>
					<div id="revoked_monthly" class="stat_box">
						<img src="<?php print base_url();?>admin/graphs/monthlygraph/revoked" alt="monthly_all" />
					</div>
					<span>No Show <?php print $period;?></span>
					<div id="noshow_monthly" class="stat_box">
						<img src="<?php print base_url();?>admin/graphs/monthlygraph/noshow" alt="monthly_all" />
					</div>
					<span>Archived <?php print $period;?></span>
					<div id="noshow_monthly" class="stat_box">
						<img src="<?php print base_url();?>admin/graphs/monthlygraph/archived" alt="monthly_all" />
					</div>
				</div>

			</td>
		</tr>
	</tbody>
</table>
