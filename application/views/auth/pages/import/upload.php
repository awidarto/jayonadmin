<style type="text/css">

label input[type=text]{
    width:25px;
}

</style>
<div id="form">
    <form method="post" enctype="multipart/form-data" action="<?php echo site_url('admin/import/upload')?>">
    Select file (.xls/.xlsx) :
    <br /><br />
    <input type="file" name="userfile" size="50" class="form"  />
    <br /><br />
    Merchant ID : <span id="merchant_id_txt"></span><br />
    <input type="text" value="" id="merchant_name" name="merchant_name" /><br />


    <label for="header_index">Column Label Row Number
        <input type="text" value="<?php print $this->config->item('import_label_default')?>" id="label_index" name="label_index" /><br />
    </label>
    <label for="header_index">Header Row Number
        <input type="text" value="<?php print $this->config->item('import_header_default')?>" id="header_index" name="header_index" /><br />
    </label>
    <label for="data_index">Data Row Starts at Number
        <input type="text" value="<?php print $this->config->item('import_data_default')?>" id="data_index" name="data_index" /><br />
    </label>

    <input type="hidden" value="" id="merchant_id" name="merchant_id" /><br />
    <input type="hidden" value="" id="merchant_fullname" name="merchant_fullname" /><br />
    <input type="hidden" value="" id="merchant_email" name="merchant_email" /><br />
    <br /><br />
    <input type="submit" value="Upload" name="upload" />
    </form>
</div>

<script type="text/javascript">

$(document).ready(function(){

        $( '#merchant_name' ).autocomplete({
            source: '<?php print site_url('ajax/getmerchant')?>',
            method: 'post',
            minLength: 2,
            select:function(event,ui){
                $('#merchant_id').val(ui.item.id);
                $('#merchant_id_txt').html(ui.item.id);
                $('#merchant_fullname').val(ui.item.fullname);
                $('#merchant_email').val(ui.item.email);
                $('#merchant_name').val(ui.item.value);
            }
        });

});

</script>