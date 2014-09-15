<script type="text/javascript">

    var base = '<?php print base_url();?>';
    var controller = '<?php print $controller; ?>';

    $(document).ready(function() {
        $('#date_from').datepicker({ dateFormat: 'yy-mm-dd' });
        $('#date_to').datepicker({ dateFormat: 'yy-mm-dd' });
        $('#release_date').datepicker({ dateFormat: 'yy-mm-dd' });

        $('#get_week').click(function(){

                var user_scopes = $('#user_scopes').val();
                var zone_scopes = $('#zone_scopes').val();
                var year = $('#year_scopes').val();
                var week = $('#week_scopes').val();
                var link = user_scopes +'/'+ zone_scopes +'/'+ year +'/week/'+ week;
                window.location = base + controller + link;

            }
        );

        $('#get_month').click(function(){

                var user_scopes = $('#user_scopes').val();
                var zone_scopes = $('#zone_scopes').val();
                var year = $('#year_scopes').val();
                var week = $('#month_scopes').val();
                var link = user_scopes +'/'+ zone_scopes +'/'+ year +'/month/'+ week;
                window.location = base + controller + link;

            }
        );

        $('#get_date_range').click(function(){
                var user_scopes = $('#user_scopes').val();
                var zone_scopes = $('#zone_scopes').val();
                var year = $('#year_scopes').val();
                var from = $('#date_from').val();
                var to = $('#date_to').val();
                var link = user_scopes +'/'+ zone_scopes +'/'+ year +'/date/'+ from +'/'+ to ;
                window.location = base + controller + link;
            }
        );

        $('#show_last').on('click',function(){
            $('#last_query').toggle();
        });

        $('#act_regenerate').on('click',function(){
            $('#generating').show();
            var mo = $('#month_period').val();
            var yr = $('#year_period').val();

            <?php
                if($select_title == 'Device'){
                    $gen = 'dev';
                }else{
                    $gen = 'rev';
                }
            ?>

            $.post( base + 'gen/<?php print $gen; ?>/' + mo +'/' + yr,
                {},
                function(data){
                    if(data.result == 'OK'){
                        $('#generating').hide();
                        alert('Report regenerated, refresh page if necessary');
                    }
                },
                'json' );
        });

        $('#inv_preview').on('click',function(){
            var invdate = $('#release_date').val();
            var current_url = '<?php print current_url() ?>';
            //window.location = current_url + '/print/' + invdate;
            window.open( current_url + '/print/' + invdate ,'_blank');
        });

        $('#inv_generate').on('click',function(){

            var params = {
                'type': '<?= $getparams['type'] ?>',
                'zone': '<?= $getparams['zone'] ?>',
                'year': '<?= $getparams['year'] ?>',
                'scope': '<?= $getparams['scope'] ?>',
                'par1': '<?= $getparams['par1'] ?>',
                'par2': '<?= $getparams['par2'] ?>',
                'par3': '<?= $getparams['par3'] ?>',
                'par4': '<?= $getparams['par4'] ?>',
            };

            if(params.par2 == 'pdf'){
                params.par3 = $('#release_date').val();
            }else if(params.par3 == 'pdf'){
                params.par4 = $('#release_date').val();
            }

            console.log(params);

            $('#generatinginvoice').show();

            $.post( base + 'admin/reports/genmanifest',
                params,
                function(data){
                    if(data.result == 'OK'){
                        $('#generatinginvoice').hide();
                        alert('Manifest generated');

                        $('#genresult').html( '<a href="' + base + 'public/manifests/' + data.file + '" target="_blank">' + data.file +'</a>' );
                    }
                },
                'json' );


        });
        //$("#toClone thead").sticky({topSpacing:0});

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

table#recon_select tr.dark {
    background-color: #aaa;
}

table#recon_select td {
    padding: 3px;
}

.dataTable * td, .dataTable * th{
    text-align: center;
}

.dataTable * td.right{
    text-align: right;
}

.floatingHeader {
  position: fixed;
  top: 0;
  visibility: hidden;
}

#toClone{
    margin-top: 0px;
}

.hide {
        display:none;
    }
div.stickyHeader {
    top:0;
    position:fixed;
    _position:absolute;
}

#generating{
    color: red;
    font-weight: bold;
    background-color: yellow;
}

.button{
    cursor: pointer;
}

#main-tab table td{
    border: thin solid #EEE;
}

#main-tab table td.currency{
    text-align: right;
}

span.bearer{
    display: inline-block;
    position: absolute;
    bottom: 0px;
    right: 0px;
    padding: 2px;
    background-color: brown;
    color: white;
}

td.cod{
    font-size: 13px;
    font-weight: bold;
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
            <table>
                <tr>
                    <td>

                        <table style="width:500px;" id="recon_select" cellspacing="0" >
                            <tr>
                                <td><?php print (isset($select_title))?$select_title:'Device'; ?></td>
                                <td colspan="3"><?php print form_dropdown('user_scopes',$merchants,$id,'id = "user_scopes"'); ?></td>
                            </tr>
                            <tr>
                                <td><?php print (isset($zone_select_title))?$zone_select_title:'Zone'; ?></td>
                                <td colspan="3"><?php print form_dropdown('zone_scopes',$zones,$zone,'id = "zone_scopes"'); ?></td>
                            </tr>
                            <tr>
                                <td>Year</td>
                                <td colspan="3"><?php print form_dropdown('year_scopes',$years,$year,'id = "year_scopes"');?></td>
                            </tr>
                            <tr>
                                <td colspan="4"><strong>Filter By :</strong></td>
                            </tr>
                            <tr class="dark">
                                <td>Month</td>
                                <td><?php print form_dropdown('month_scopes',$months,$month,'id = "month_scopes"');?></td>
                                <td><span id="get_month" class="action_link" >Generate</span></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="4" style="text-align:center;"><strong>OR</strong></td>
                            </tr>
                            <tr class="dark">
                                <td>Week Number</td>
                                <td><?php print form_dropdown('week_scopes',$weeks,$week,'id = "week_scopes"');?></td>
                                <td><span id="get_week" class="action_link" >Generate</span></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="4" style="text-align:center;"><strong>OR</strong></td>
                            </tr>
                            <tr class="dark">
                                <td>From Date</td>
                                <td><?php print form_input(array('name'=>'date_from','id'=>'date_from','class'=>'text','value'=>$from));?></td>
                                <td><?php print 'To '.form_input(array('name'=>'date_to','id'=>'date_to','class'=>'text','value'=>$to));?></td>
                                <td><span id="get_date_range" class="action_link" >Generate</span></td>
                            </tr>

                            <tr>
                                <td colspan="4" style="text-align:right;"><?php print anchor(uri_string().'/csv', 'Download CSV');?></td>
                            </tr>

                            <tr>
                                <td>Period</td>
                                <td>
                                    <?php print form_dropdown('month_period',$months,$month,'id = "month_period"');?>
                                </td>
                                <td>
                                    <?php print form_dropdown('year_period',$years,$year,'id = "year_period"');?>
                                </td>
                                <td>
                                    <span id="act_regenerate" class="action_link" >Regenerate Report Data</span>
                                </td>

                            </tr>
                            <tr id="generating" style="display:none">
                                <td colspan="4">
                                    <img src="<?php print base_url();?>assets/images/ajax_loader.gif" />
                                    Regenerating data, please wait, this will take a while.
                                </td>
                            </tr>


                        </table>


                    </td>
                    <td style="vertical-align:top;">

                        <table style="width:500px;" id="recon_select" cellspacing="0" >
                            <tr>

                                <td style="text-align:right;">Manifest Date</td>
                                <td><?php print form_input(array('name'=>'release_date','id'=>'release_date','class'=>'text','value'=>''));?>
                                &nbsp;&nbsp;<span class="button" id="inv_preview">Preview</span>&nbsp;<span class="button" id="inv_generate">Terbitkan</span>
                                &nbsp;&nbsp;<span id="generatinginvoice" style="display:none;">Creating manifest...</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <span id="genresult"></span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>

<div  id="main-tab">
    <h3><?php print $type.' '.$period; ?></h3>
    <?php print $recontab; ?>
</div>

<span id="show_last">Query</span>
<div id="last_query" style="display:none;">
    <p>
        <?php print $last_query; ?>
    </p>
</div>

