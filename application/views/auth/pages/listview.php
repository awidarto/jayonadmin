<script>
	$(document).ready(function() {
	    $('.dataTable').dataTable(
			{

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