<script>
	$(document).ready(function() {
	    $('.dataTable').dataTable(
			{
				"sPaginationType": "full_numbers",
				"sDom": 'T<"clear">lfrtip',
                "aLengthMenu": [[10, 25, 50, 100, 150, 200, 250, 500], [10, 25, 50, 100, 150, 200, 250, 500]],
				"oTableTools": {
					"sSwfPath": "<?php print base_url();?>assets/swf/copy_csv_xls_pdf.swf"
				},
			}
		);
	} );
</script>
<?php if(isset($add_button)):?>
	<div class="button_nav">
		<?php echo anchor($add_button['link'],$add_button['label'],'class="button add"')?>
	</div>
<?php endif;?>
<?php echo $this->table->generate(); ?>