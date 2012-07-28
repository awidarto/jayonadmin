<html>
<head>
    <style>

        html{margin:0px;}

        body{margin:5px;
            font-size: 12px;
        }

        #wrapper{
            width:850px;
            margin:5px;
            display:block;
            font-family:'Trebuchet Ms', 'Yanone Kaffeesatz', Lato, Lobster, 'Lobster Two','Droid Sans', Helvetica ;
            font-size:13px;
            text-align:left;
        }

        h2{
            margin:0px;
            padding-top:15px;
        }

        h3{
            text-align: center;
        }

        td{
            font-size: 12px;
        }

        .dataTable{
            width:100%;
            font-family:'Trebuchet Ms', 'Yanone Kaffeesatz', Lato, Lobster, 'Lobster Two','Droid Sans', Helvetica ;
            margin-top:8px;

        }

        .dataTable td{
            border-bottom:thin solid #eee;
            text-align:left;
            font-size: 11px;
        }

        .dataTable th{
            text-align:left;
            padding-right:15px;
            font-size:12px;
            font-weight: bold;
            border-top:thin solid #eee;
            border-bottom:thin solid #eee;
            /*border-left:thin solid #eee;*/
        }

        .dataTable tr>th{
            width:20px;

        }

        .dataTable tr>th:last-child{
            width:100px;
            /*border-right:thin solid #eee;*/
        }

        .dataTable td{
            /*border-left:thin solid #eee;*/
            border-bottom:thin solid #eee;
        }

        .dataTable td:last-child{
            /*border-right:thin solid #eee;*/
            text-align:right;
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

        #order_detail,#merchant_detail{
            vertical-align:top;
            padding-top:0px;
            
        }

        #order_detail h2{
            text-align: center;
        }

        #merchant_detail{
            margin:0px;
            padding:8px;
        }

        #mainInfo tr>td:first-child, #orderInfo tr>td:first-child{
            width:150px;
        }

        table#main_table{
            width:840px;
            padding:0px;
            margin:0px;
        }

        .row_label{
            width:150px;
        }

        table#signBox{
            font-size: 12px;
            margin-top:15px;
            width:840px;
        }

        table#sign tr td:first-child{
            width:75%;
        }

        #signBox th{
            width:100px;
            vertical-align:top;
            border-top: thin solid #eee;
            border-bottom: thin solid #eee;
            /*border-right:thin solid #eee;*/
            margin:2px;
        }

        #sign_name td{
            border-top: thin solid #eee;
            border-bottom: thin solid #eee;
            /*border-right:thin solid #eee;*/
        }

        tr#sign_name td:first-child{
            /*border-left: thin solid #eee;*/
        }

        #signBox th:first-child{
            /*border-left: thin solid #eee;*/
        }

        #mainInfo tr td:last-child, #orderInfo tr td:last-child{
            border-bottom: thin solid #eee;
            /*border-left:thin solid #eee;*/
        }

        #mainInfo td{
            vertical-align:top;
        }

        h2{
            font-size: 18px;
            display: block;
            text-align: center;
        }

        #order_slot{
            margin-left:20px;
            float:right;
        }

        .editable, #note{
            font-weight:bold;
            color:maroon;
        }

        #note{
            font-size: 11px;
            padding:3px;
        }

        .editable input[type='text']{
            height:14px;
        }
	</style>
</head>
<body>
	<div id="wrapper">
		<h2>Jayon Express</h2>			
		<h3>Laporan Rekonsiliasi</h3>				
						
		<table id="mainInfo">
			<tr>
				<td>
                    <?php print $type;?>
				</td>
				<td>
                    <?php print $type_name;?>
				</td>
			</tr>
			<tr>
				<td>
					Period of Reconciliations
				</td>
				<td>
                    <?php print $period; ?>
				</td>
			</tr>
			<tr>
				<td>
					Bank Account
				</td>
				<td>
                    <?php print $bank_account;?>
				</td>
			</tr>
		</table>

		<table>
			<tr>
				<td colspan="2" >Ringkasan Transaksi</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
			</tr>
		</table>
						
        <?php print $recontab;?>
						
		<p>Mohon untuk ditandatangani apabila jumlah rekonsiliasi telah sesuai dengan jumlah transaksi anda melalui layanan Cash On Delivery dari Jayon Express selama periode tersebut diatas</p>
		<p>Transfer ke rekening anda paling cepat 1 hari setelah kami menerima konfirmasi persetujuan kesesuaian jumlah rekonsiliasi</p>				
		<p>Laporan Rekonsiliasi ini bukanlah transaksi penjualan antara Jayon Express Pte.Ltd dengan toko online melainkan merupakan penarikan uang titipan hasil penjualan milik toko online sebagaimana tersebut diatas sehingga bukan merupakan obyek pajak bagi Jayon Express</p>				

		<table id="sign">
			<tr>
				<td>&nbsp;</td>
				<td>Menyetujui Jumlah Rekonsiliasi</td>
			</tr>
			<tr>
                <td>&nbsp;</td>
				<td>Tanggal :</td>
			</tr>
			<tr>
                <td>&nbsp;</td>
				<td></td>
			</tr>
			<tr>
                <td>&nbsp;</td>
				<td class="underlined">Nama</td>
			</tr>
		</table>				

	</div>				
</body>
</html>