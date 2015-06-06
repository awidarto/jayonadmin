<div id="form">
	<div class="form_box">
			<form method="post">

			<?php print form_fieldset('Personal Info'); ?>

			<!--Username: <strong><?php echo $user['username'];?></strong><br /><br />-->
			Username:<br />
			<input type="text" name="username" size="50" class="form" value="<?php echo set_value('username',$user['username']); ?>" /><?php echo form_error('email'); ?><br /><br />

			Group:<br />
				<?php echo form_dropdown('group_id',$groups,set_value('group_id',$user['group_id']));?>
			<br />

			Email:<br />
			<input type="text" name="email" size="50" class="form" value="<?php echo set_value('email',$user['email']); ?>" /><?php echo form_error('email'); ?><br /><br />

			Full Name:<br />
			<input type="text" name="fullname" size="50" class="form" value="<?php echo set_value('fullname',$user['fullname']); ?>" /><?php echo form_error('fullname'); ?><br /><br />

			Street:<br />
			<input type="text" name="street" size="50" class="form" value="<?php echo set_value('mobile',$user['street']); ?>" /><?php echo form_error('mobile'); ?><br /><br />

			District:<br />
			<input type="text" name="district" size="50" class="form" value="<?php echo set_value('district',$user['district']); ?>" /><?php echo form_error('district'); ?><br /><br />

			City:<br />
			<input type="text" name="city" size="50" class="form" value="<?php echo set_value('city',$user['city']); ?>" /><?php echo form_error('city'); ?><br /><br />

			Province:<br />
			<input type="text" name="province" size="50" class="form" value="<?php echo set_value('province',$user['province']); ?>" /><?php echo form_error('province'); ?><br /><br />

			Country:<br />
			<input type="text" name="country" size="50" class="form" value="<?php echo set_value('country',$user['country']); ?>" /><?php echo form_error('country'); ?><br /><br />

			ZIP:<br />
			<input type="text" name="zip" size="50" class="form" value="<?php echo set_value('zip',$user['zip']); ?>" /><?php echo form_error('zip'); ?><br /><br />

			Phone Number:<br />
			<input type="text" name="phone" size="50" class="form" value="<?php echo set_value('phone',$user['phone']); ?>" /><?php echo form_error('phone'); ?><br /><br />

			Mobile Number:<br />
			<input type="text" name="mobile" size="50" class="form" value="<?php echo set_value('mobile',$user['mobile']); ?>" /><?php echo form_error('mobile'); ?><br /><br />

			<?php print form_fieldset_close(); ?>

			<?php print form_fieldset('Merchant Info'); ?>

			Merchant Name:<br />
			<input type="text" name="merchantname" size="50" class="form" value="<?php echo set_value('merchantname',$user['merchantname']); ?>" /><?php echo form_error('merchantname'); ?><br /><br />

			Bank:<br />
			<input type="text" name="bank" size="50" class="form" value="<?php echo set_value('bank',$user['bank']); ?>" /><?php echo form_error('bank'); ?><br /><br />

			Account Name:<br />
			<input type="text" name="account_name" size="50" class="form" value="<?php echo set_value('account_name',$user['account_name']); ?>" /><?php echo form_error('account_name'); ?><br /><br />

			Account Number:<br />
			<input type="text" name="account_number" size="50" class="form" value="<?php echo set_value('account_number',$user['account_number']); ?>" /><?php echo form_error('account_number'); ?><br /><br />

			<?php echo form_checkbox('same_as_personal_address', '1', $user['same_as_personal_address']);?> Same as personal address<br /><br />

			Street:<br />
			<input type="text" name="mc_street" size="50" class="form" value="<?php echo set_value('mc_mobile',$user['mc_street']); ?>" /><?php echo form_error('mc_street'); ?><br /><br />

			District:<br />
			<input type="text" name="mc_district" size="50" class="form" value="<?php echo set_value('mc_district',$user['mc_district']); ?>" /><?php echo form_error('mc_district'); ?><br /><br />

			City:<br />
			<input type="text" name="mc_city" size="50" class="form" value="<?php echo set_value('mc_city',$user['mc_city']); ?>" /><?php echo form_error('mc_city'); ?><br /><br />

			ZIP:<br />
			<input type="text" name="mc_zip" size="50" class="form" value="<?php echo set_value('mc_zip',$user['mc_zip']); ?>" /><?php echo form_error('mc_zip'); ?><br /><br />

			Province:<br />
			<input type="text" name="mc_province" size="50" class="form" value="<?php echo set_value('mc_province',$user['mc_province']); ?>" /><?php echo form_error('mc_province'); ?><br /><br />

			Country:<br />
			<input type="text" name="mc_country" size="50" class="form" value="<?php echo set_value('mc_country',$user['mc_country']); ?>" /><?php echo form_error('mc_country'); ?><br /><br />

			Phone Number:<br />
			<input type="text" name="mc_phone" size="50" class="form" value="<?php echo set_value('mc_phone',$user['mc_phone']); ?>" /><?php echo form_error('mc_phone'); ?><br /><br />

			Mobile Number:<br />
			<input type="text" name="mc_mobile" size="50" class="form" value="<?php echo set_value('mc_mobile',$user['mc_mobile']); ?>" /><?php echo form_error('mc_mobile'); ?><br /><br />

			<?php echo form_checkbox('mc_unlimited_time', '1', $user['mc_unlimited_time']);?> Unlimited Order Time<br /><br />

			First Order Time:<br />
			<input type="text" name="mc_first_order" size="50" class="form" value="<?php echo set_value('mc_first_order',$user['mc_first_order']); ?>" /><?php echo form_error('mc_first_order'); ?><br /><br />

			Last Order Time:<br />
			<input type="text" name="mc_last_order" size="50" class="form" value="<?php echo set_value('mc_last_order',$user['mc_last_order']); ?>" /><?php echo form_error('mc_last_order'); ?><br /><br />

            Pickup Setting :<br />
            <?php echo form_checkbox('mc_toscan', '1', $user['mc_toscan']);?> Use barcode scan for pick up<br /><br />

            Pick Up Time:<br />
            <input type="text" name="mc_pickup_time" size="50" class="form" value="<?php echo set_value('mc_pickup_time',$user['mc_pickup_time']); ?>" /><?php echo form_error('mc_pickup_time'); ?><br /><br />

            Pick Up Cut Off Time:<br />
            <input type="text" name="mc_pickup_cutoff" size="50" class="form" value="<?php echo set_value('mc_pickup_cutoff',$user['mc_pickup_cutoff']); ?>" /><?php echo form_error('mc_pickup_cutoff'); ?><br /><br />

            <table>
                <tr>
                    <td>
                        Cost Bearer<hr />
                        <label for"delivery_bearer">Delivery Fee :</label><br />
                            <?php print form_checkbox(array('name'=>'delivery_bearer','id'=>'delivery_bearer','value'=>'buyer','checked'=>($user['mc_delivery_bearer'] == 'buyer')?TRUE:FALSE ));?> Bill buyer / tagihkan ke buyer
                        <br />
                        <label for="cod_surcharge_bearer">COD / CCOD Surcharges:</label><br />
                            <?php print form_checkbox(array('name'=>'cod_surcharge_bearer','id'=>'cod_surcharge_bearer','value'=>'buyer','checked'=>($user['mc_cod_bearer'] == 'buyer')?TRUE:FALSE ));?> Bill buyer / tagihkan ke buyer
                    </td>
                </tr>
            </table>


			<?php print form_fieldset_close(); ?>

			<input type="submit" value="Update" name="register" />
				<?php
					print $back_url;
				?>
			</form>
	</div>
</div>