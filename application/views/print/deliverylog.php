<html>
<head>
	<title>Delivery Slip</title>
	<style>

		html{margin:0px;}

		body{margin:5px;
			font-size: 12px;
		}

		hr{
			border:0px;
			border-bottom:thin solid #aaa;
		}

		#wrapper{
			width:1000px;
			margin:5px;
			display:block;
			font-family:'Trebuchet Ms', 'Yanone Kaffeesatz', Lato, Lobster, 'Lobster Two','Droid Sans', Helvetica ;
			font-size:13px;
			text-align:left;
		}

		h2{
			margin:0px;
		}

		td{
			font-size: 12px;
			padding:5px;
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

		#order_detail,#merchant_detail,#sign_boxes{
			vertical-align:top;
			padding-top:0px;
			
		}

		#order_detail{
			width:400px;
		}

		#order_detail h2{
			text-align: center;
		}

		#merchant_detail{
			width:300px;
			margin:0px;
			padding:0px;
		}

		table#mainInfo{
			width:100%;
		}

		#mainInfo tr>td:first-child, #orderInfo tr>td:first-child{
			width:150px;
		}

		table#main_table{
			padding:0px;
			margin:0px;
		}

		.row_label{
			width:150px;
		}

		table#signBox{
			font-size: 11px;
			width:200px;
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

		tr.sign_head td{
			border: thin solid #eee;
			text-align:center;
			font-weight: bold;
			font-size: 11px;

		}

		tr.sign_space td{
			font-size: 12px;
			height:50px;
			border: thin solid #eee;
			text-align:center;
		}

		tr.sign_name td{
			font-size: 12px;
			border: thin solid #eee;
		}

		tr.spacer td{
			height:8px;
			display: block;
		}

		.fine{
			font-size: 11px;
		}

		td.lsums{
			text-align: right;
		}

	</style>
</head>
<body>
<div id="wrapper">
<?php
	print $this->table->generate();
?>
</div>
</body>
</html>