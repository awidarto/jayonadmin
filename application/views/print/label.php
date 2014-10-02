<html>
<head>
    <title>Delivery Slip</title>

<?php echo $this->ag_asset->load_css('colors.css');?>
<style type="text/css">
    body{
        font-size: 12pt;
    }
    .label{
        float: left;
        font-family: Arial, sans-serif;
        max-height:<?php print $cell_height;?>px;
        min-height:<?php print $cell_height;?>px;
        height:<?php print $cell_height;?>px;

        max-width:<?php print $cell_width;?>px;
        min-width:<?php print $cell_width;?>px;
        width:<?php print $cell_width;?>px;

        margin-right: <?php print $margin_right;?>px;
        margin-bottom: <?php print $margin_bottom;?>px;
        display: table-cell;

        border: thin ridge #ddd;
        padding: 4px;/* add padding offset = padding * column count */
    }

    @media print {
        .label{
            border: none;
        }
    }

    .label table{
        width: 100%;
        height: 100%;
        border: none;
        font-size: <?php print $font_size;?>pt;
    }

    h3{
        margin: 4px 10px;
        font-size: 1.1em;
    }

    td{
        padding: 4px;
        font-size: .8em;
        word-wrap: break-word;
    }

    p{
        margin-bottom: 4px;
        margin-top: 4px;
    }

    p.shipping{
        word-wrap:break-word;
        <?php if($code_type == 'barcode'):?>
            display: inline-block;
        <?php endif ?>
        max-width:<?php print $cell_width;?>px;
    }

    img.barcode{
        max-width: 98%;
        height:auto;
    }

    img.qr{
        max-width: 80px;
        height:auto;
    }

    .code-container{
        display: inline-block;
        float: left;
    }

    img.logo{
        max-height:25px;
    }

    <?php
        $container = ($cell_width * $columns) + ($margin_right * $columns) + $margin_right + ( 4 * $columns ) + 20;
    ?>

    #container{
        width: <?php print $container;?>px;
        max-width: <?php print $container;?>px;
        display: block;
    }

</style>
</head>
<body>
<div id="container">

<?php foreach( $main_info as $address ):?>
    <?php
        // assume resolution in 72 ppi
        /*
        $paper_pw = 1100;
        if($columns > 1){
            $min_width = ((int) floor($paper_pw/$columns)).'px';
            $width = ($min_width - 10).'px';
        }else{
            $min_width = ((int) floor($paper_pw/2)).'px';
            $width = ($min_width - 10).'px';
        }
        $resolution;
        $cell_width;
        $cell_height;
        $columns;
        $margin_right;
        $margin_bottom;
        */
    ?>
    <div class="label">
        <table>
            <tr>
                <td style="width:50%;text-align:left">
                    <?php
                        $logo = get_logo($address['merchant_id']);
                        if($logo['exist'] == true){
                            print '<img class="logo" src="'.$logo['logo'].'" />';
                        }else{
                            print $address['merchant'];
                        }
                    ?>
                </td>
                <td style="width:50%;text-align:right">
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:right">
                    <?php if($code_type == 'qr') : ?>
                    <div class="code-container">
                        <img class="qr" src="<?php print base_url()?>img/qr/<?php print base64_encode($address['delivery_id'].'|'.$address['merchant_trans_id']) ?>" alt="<?php print $address['merchant_trans_id'] ?>">
                    </div>
                    <?php endif; ?>
                    <div>
                        <h3><?php print $address['recipient_name'] ?></h3>
                        <p class="shipping"><?php print $address['shipping_address'] ?></p>
                        <p><?php print $address['buyerdeliverycity'] ?></p>
                        <?php if($code_type == 'barcode') : ?>
                        <img class="barcode" src="<?php print base_url()?>img/barcode/<?php print base64_encode($address['merchant_trans_id']) ?>" alt="<?php print $address['merchant_trans_id'] ?>">
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width:50%;text-align:left">
                    <?php print colorizetype( $address['delivery_type'], 'Jayon ' )?>
                </td>
                <td style="width:50%;text-align:right;border:thin solid black;">
                    <?php //print $address['buyerdeliveryzone'] ?>
                </td>
            </tr>
        </table>
    </div>
<?php endforeach; ?>

</div>

</body>
</html>
