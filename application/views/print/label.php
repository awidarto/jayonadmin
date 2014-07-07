<html>
<head>
    <title>Delivery Slip</title>

<?php echo $this->ag_asset->load_css('colors.css');?>
<style type="text/css">
    .label{
        font-family: Arial, sans-serif;
        min-height:160px;
        height:160px;
    }

    h3{
        margin: 4px 10px;
        font-size: 16px;
    }
    td{
        padding: 4px;
        font-size: 12px;
    }
</style>
</head>
<body>

<?php foreach( $main_info as $address ):?>
    <div class="label" style="width:<?php print 100/$columns ?>%;min-width:<?php print 100/$columns ?>%">
        <table>
            <tr>
                <td style="width:50%;text-align:left">
                    <?php print $address['merchant'] ?>
                </td>
                <td style="width:50%;text-align:right">
                    <?php //if( hide_trx($address['merchant_trans_id']) != '' ):?>
                    <img src="<?php print base_url()?>admin/prints/barcode/<?php print $address['merchant_trans_id'] ?>" alt="<?php print $address['merchant_trans_id'] ?>">
                    <?php //endif;?>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="width:50%;text-align:right">
                    <h3><?php print $address['recipient_name'] ?></h3>
                    <h3><?php print $address['shipping_address'] ?></h3>
                    <h3><?php print $address['buyerdeliverycity'] ?></h3>
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
