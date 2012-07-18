<?php

class Reports extends Application
{
	
	public function __construct()
	{
		parent::__construct();
		$this->ag_auth->restrict('admin'); // restrict this controller to admins only
		$this->table_tpl = array(
			'table_open' => '<table border="0" cellpadding="4" cellspacing="0" class="dataTable">'
		);
		$this->table->set_template($this->table_tpl);

		$this->breadcrumb->add_crumb('Home','admin/dashboard');
		$this->breadcrumb->add_crumb('Reports','admin/reports/daily');
		
	}

	public function index(){
		//$this->breadcrumb->add_crumb('Reports','admin/reports/daily');

		$year = date('Y',time());
		$month = date('m',time());

		$page['period'] = ' - '.date('M Y',time());

		$page['page_title'] = 'Report Summary';
		$this->ag_auth->view('reports/index',$page); // Load the view
	}

	public function daily(){

		$this->breadcrumb->add_crumb('Daily Report','admin/reports/daily');

		$page['page_title'] = 'Daily Report';
		$this->ag_auth->view('reports/daily',$page); // Load the view

	}

	public function weekly(){
		$this->breadcrumb->add_crumb('Weekly Report','admin/reports/weekly');

		$page['page_title'] = 'Weekly Report';
		$this->ag_auth->view('reports/weekly',$page); // Load the view
		
	}

	public function monthly(){
		$this->breadcrumb->add_crumb('Monthly Report','admin/reports/monthly');

		$page['page_title'] = 'Monthly Report';
		$this->ag_auth->view('reports/monthly',$page); // Load the view
		
	}

	public function reconciliation(){
		$this->breadcrumb->add_crumb('Reconciliations','admin/reports/reconciliation');

		$this->table->set_heading(
			'Year',
			'Week',
			'From',
			'To',
			'Generate'
			); // Setting headings for the table

		$page['ajaxurl'] = 'admin/reports/ajaxreconsiliation';
		$page['page_title'] = 'Reconciliations';
		$this->ag_auth->view('ajaxlistview',$page); // Load the view
		
	}

	public function ajaxreconsiliation(){


		$week = date('W',time());
		$year = date('Y',time());

		$aadata = array();

		for($i = $week; $i > 0; $i--)
		{

			$from =	date('d-m-Y', strtotime('1 Jan '.$year.' +'.($i - 1).' weeks'));
			$to = date('d-m-Y', strtotime('1 Jan '.$year.' +'.$i.' weeks - 1 day'));

			$generate = anchor("admin/reports/globalreport/".$from."/".$to, "Global"); // Build actions links
			$merchantlist = anchor("admin/reports/merchants/".$from."/".$to, "By Merchant"); // Build actions links
			$courierlist = anchor("admin/reports/couriers/".$from."/".$to, "By Courier"); // Build actions links
			$aadata[] = array(
				$year,
				$i,
				date('d-m-Y', strtotime('1 Jan '.$year.' +'.($i - 1).' weeks')),
				date('d-m-Y', strtotime('1 Jan '.$year.' +'.$i.' weeks - 1 day')),
				$generate.' '.$merchantlist.' '.$courierlist
			); // Adding row to table
		}

		$count_all = count($aadata);

		$count_display_all = count($aadata);

		$result = array(
			'sEcho'=> $this->input->post('sEcho'),
			'iTotalRecords'=>$count_all,
			'iTotalDisplayRecords'=> $count_display_all,
			'aaData'=>$aadata
		);

		print json_encode($result); // Load the view
	}

	public function merchants($from,$to){
		$this->breadcrumb->add_crumb('Reconciliations','admin/reports/reconciliation');

		$this->table->set_heading(
			'Year',
			'Week',
			'From',
			'To',
			'Generate'
			); // Setting headings for the table

		$page['ajaxurl'] = 'admin/reports/ajaxmerchants/'.$from.'/'.$to;
		$page['page_title'] = 'Merchant Reconciliations';
		$this->ag_auth->view('ajaxlistview',$page); // Load the view

	}

	public function ajaxmerchants($from,$to){

	}

	public function couriers($from,$to){
		$this->breadcrumb->add_crumb('Reconciliations','admin/reports/reconciliation');

		$this->table->set_heading(
			'Year',
			'Week',
			'From',
			'To',
			'Generate'
			); // Setting headings for the table

		$page['ajaxurl'] = 'admin/reports/ajaxcouriers/'.$from.'/'.$to;
		$page['page_title'] = 'Reconciliations';
		$this->ag_auth->view('ajaxlistview',$page); // Load the view
	}

	public function ajaxcouriers($id){

	}

	public function globalreport($from,$to){

	}

}

?>