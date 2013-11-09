
<form method="post" action="<?php echo site_url('import/commit')?>">

    <input type="submit" value="Commit" name="commit" />

    <?php
        print anchor('import','Cancel');
    ?>

    <div id="table">
        <?php print $this->table->generate($cells);?>
    </div>

</form>

<script type="text/javascript">

$(document).ready(function(){
        $('#select_all').click(function(){
            if($('#select_all').is(':checked')){
                $('.selector').attr('checked', true);
            }else{
                $('.selector').attr('checked', false);
            }
        });
});

</script>