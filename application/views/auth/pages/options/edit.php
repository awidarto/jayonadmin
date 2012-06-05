<div id="form">
	<div class="form_box">
			<form method="post" action="<?php echo site_url('admin/options/edit/'.$user['id'])?>">
			<?php //echo set_value('key',$user['key']); ?>
			<?php echo $user['keylabel']; ?>
			<input type="hidden" name="key" value="<?php echo set_value('key',$user['key']); ?>" />
			<?php if($user['choices'] == ""):?>

				<input type="text" name="val" id="val" size="50" class="form" value="<?php echo set_value('val',$user['val']); ?>" /><?php echo form_error('email'); ?><br /><br />

			<?php elseif(count(explode("|",$user['choices']) > 0)): ?>
				<?php $opt = explode("|",$user['choices']);?>
				<?php
					$opts = array(); 
					foreach($opt as $op){
						$op = explode(":",$op);
						$opts[$op[1]] = $op[0];
					}
				?>
				<?php print form_dropdown('val',$opts,$user['val']);?>
			<?php endif;?>

			<input type="submit" value="Update" name="register" />
			<?php
				print anchor('admin/options/manage','Cancel');
			?>
			</form>
	</div>
</div>
