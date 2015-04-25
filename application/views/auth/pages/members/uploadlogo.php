<div id="form">
    <div class="form_box">
            <?php echo form_open_multipart('admin/members/logoupload/'.$id);?>
            <?php print form_fieldset('Merchant Logo'); ?>

            Choose Logo Image ( .jpg, .png):<br />
            <input type="file" name="userfile" size="50" class="form"" /><br />

            <?php print form_fieldset_close(); ?>

            <input type="submit" value="Upload" name="register" />
            <?php
                if(isset($back_url)){
                    print $back_url;
                }
            ?>
            </form>
    </div>
</div>