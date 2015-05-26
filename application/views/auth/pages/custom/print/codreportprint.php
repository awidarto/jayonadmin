<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
    <title>COD REPORT - PT JEXINDO SUKSES MAKMUR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

        <style type="text/css">

            body{
                width:100%;
                font-size: 12px;
                font-family: arial,sans-serif;
                margin: auto;
            }

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

            #jayon_logo{
                vertical-align:top;
                font-family:'Trebuchet Ms', 'Yanone Kaffeesatz', Lato, Lobster, 'Lobster Two','Droid Sans', Helvetica ;
                font-size: 11px;
                text-align:left;
            }

            #jayon_logo img{
                width:170px;
            }

            table#address{
                width: 420px;
                min-width: 400px;
            }

            table#address td{
                padding-left: 8px;
                font-weight: bold;
            }

            table.reportTable thead th{
                text-align: left;
                background-color: #ccc;
            }

            table.reportTable{
                width: 100%;
            }

            .total{
                font-weight: bold;
                font-size: 14px;
            }

            table.reportTable thead th:first-child {
                width:25px;
                text-align: left;
            }

            @media screen{
                body{
                    width:1002px;
                    margin-right: auto;
                    margin-left: auto;
                }
            }

        </style>


    </head>

    <body>
        <div id="head" style="display:block;" >
            <div style="width:450px;display:inline-block;float:left;height:290px;">

                <table border="0" cellpadding="0" cellspacing="0" id="mainLogo">
                    <tbody>
                        <tr>
                            <td id="jayon_logo"><?php
                                    //print $this->ag_asset->load_image('plogo.png', 'assets/images');
                                    print '<img  src="'.base_url().'assets/images/plogo.png?'.time().'" alt="JEX" />';
                                    ?><br />
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left:8px;">
                                <b>PT JEXINDO SUKSES MAKMUR</b>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table id="address" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>KEPADA</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>TANGGAL</td>
                        <td><?php print $invdate ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>PERIODE PENGIRIMAN</td>
                        <td style="text-align:left;" colspan="2"><?php print iddate($from, false) ;?> s/d <?php print iddate($to) ;?></td>
                    </tr>
                    <?php
                        $total_transfered = $total_cod_val - $grand_total;
                    ?>
                    <tr>
                        <td>TOTAL CHARGE TO BE TRANSFERRED</td>
                        <td></td>
                        <td style="min-width:150px;width:150px;font-size:18px;">Rp <?php print idr($total_transfered);?></td>
                    </tr>
                    <tr>
                        <td>TERBILANG</td>
                        <td colspan="2"><?php print $this->number_words->to_words((double)$total_transfered).' rupiah';?></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-weight:normal;" >
                            <p>
                                Payable to <?php print $merchantname; ?><br />
                                Account : <?php print $bank_account; ?><br />
                                Payment is due 3 days after COD<br />
                                Report validation is send to us in written<br />
                                Thank you for your business<br />
                                Administrator
                            </p>
                        </td>
                    </tr>
                </table>

            </div>
            <div style="width:400px;display:inline-block;">
                <h1 style="margin-top:0px;">COD REPORT</h1>
                <h2>No: COD-<?php print strtoupper($merchantname) ?>-<?php print $invdatenum ?></h2>
            </div>
        </div>
        <div style="display:block;clear:both;" />
        <div style="display:block;clear:both;" >
            <?php print $recontab; ?>
        </div>

    </body>
</html>

