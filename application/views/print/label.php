<html>
<head>
    <title>Delivery Slip</title>

<?php echo $this->ag_asset->load_css('colors.css');?>
<style type="text/css">
    .label{
        float: left;
        font-family: Arial, sans-serif;
        min-height:175px;
        height:180px;
        margin-bottom: 20px;
        display: table;
    }

    .label table{
        width: 75%;
    }

    h3{
        margin: 4px 10px;
        font-size: 16px;
    }
    td{
        padding: 4px;
        font-size: 12px;
    }

    p.shipping{
        word-wrap:break-word;
    }

    img.barcode{
        height:auto;
    }
</style>
</head>
<body>

<?php foreach( $main_info as $address ):?>
    <?php
        // assume resolution in 72 ppi
        $paper_pw = 1100;
        if($columns > 1){
            $min_width = ((int) floor($paper_pw/$columns)).'px';
            $width = ($min_width - 10).'px';
        }else{
            $min_width = ((int) floor($paper_pw/2)).'px';
            $width = ($min_width - 10).'px';
        }
    ?>
    <div class="label" style="width:<?php print $width ?>;min-width:<?php print $min_width ?>; ">
        <table width="<?php print $width ?>">
            <tr>
                <td style="width:50%;text-align:left">
                    <?php print $address['merchant'] ?>
                </td>
                <td style="width:50%;text-align:right">
                </td>
            </tr>
            <tr>
                <td colspan="2" style="width:50%;text-align:right">
                    <h3><?php print $address['recipient_name'] ?>&nbsp;</h3>
                    <p class="shipping"><?php print $address['shipping_address'] ?>&nbsp;</p>
                    <p><?php print $address['buyerdeliverycity'] ?>&nbsp;</p>
                    <img class="barcode" src="<?php print base_url()?>admin/prints/barcode/<?php print $address['merchant_trans_id'] ?>" alt="<?php print $address['merchant_trans_id'] ?>">
                </td>
            </tr>
            <tr>
                <td style="width:50%;text-align:left">
                    <?php print colorizetype( $address['delivery_type'] )?>
                </td>
                <td style="width:50%;text-align:right;border:thin solid black;">
                    <?php print $address['buyerdeliveryzone'] ?>
                </td>
            </tr>
        </table>
    </div>
<?php endforeach; ?>
</body>
</html>
