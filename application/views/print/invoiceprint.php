<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
    <title>INVOICE - PT JEXINDO SUKSES MAKMUR</title>
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
        <div id="head">
            <div style="width:450px;display:inline-block;float:left;height:260px;">
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
                    <tr>
                        <td>TOTAL</td>
                        <td></td>
                        <td style="min-width:150px;width:150px;font-size:18px;">Rp <?php print idr($grand_total);?></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-weight:normal;" >
                            <p>
                                Payable to PT JEXINDO SUKSES MAKMUR<br />
                                Account : BCA KCU PONDOK INDAH - 2910332069<br />
                                Payment is due in 2 days based on invoice date<br />
                                Thank you for your business<br />
                                Administrator
                            </p>
                        </td>
                    </tr>
                </table>

            </div>
            <div style="width:400px;display:inline-block;">
                <h1 style="margin-top:0px;">INVOICE</h1>
                <h2>No: JSM-<?php print strtoupper($merchantname) ?>-<?php print $invdatenum ?></h2>
                <?php print $sumtab; ?>
            </div>
        </div>
        <div style="display:block;clear:both;" >
            <?php print $recontab; ?>
        </div>

        <div id="last_query">
            <p>
                *JATUH TEMPO PEMBAYARAN DUA HARI DARI TANGGAL INVOICE<br />
                *INVOICE DIGITAL MERUPAKAN INVOICE UTAMA SEHINGGA SETELAH DIKIRIMKAN INVOICE DIGITAL HARAP MELAKUKAN PEMBAYARAN<br />
                *BUKTI TRANSFER PEMBAYARAN MOHON DIKONFIRMASI KE KANTOR JAYON EXPRESS VIA EMAIL KE eddy.jusuf@jayonexpress.com dan ferdinand.patar@gmail.com
            </p>
        </div>

    </body>
</html>

