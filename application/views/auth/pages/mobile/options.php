<script>

	$(document).ready(function() {

		$('#sync').click(function(){
			$.post('<?php print site_url('mobile/device/ajaxsync');?>',{
				'lat':$('#lat').val(),
				'lon':$('#lon').val()
			}, function(data) {
				alert(data.status);
			},'json');
		});
	        
	});

</script>
<?php
	print form_button('report','send report','id="report"');
	print '<br />';
	print form_button('updatekey','update key','id="updatekey"');
	print '<br />';
	print form_button('synchronize','synchronize','id="sync"');
?>
