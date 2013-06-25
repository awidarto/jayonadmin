<?php

class Reports extends Application
{
	
	public function __construct()
	{
		parent::__construct();
		$this->ag_auth->restrict('admin'); // restrict this controller to admins only
		$this->table_tpl = array(
			'table_open' => '<table border="0" cellpadding="4" cellspacing="0" class="reportTable">'
		);
		$this->table->set_template($this->table_tpl);

		$this->breadcrumb->add_crumb('Home','admin/dashboard');
		$this->breadcrumb->add_crumb('Reports','admin/reports');
		
	}

	public function index(){
		//$this->breadcrumb->add_crumb('Reports','admin/reports/daily');
		$this->breadcrumb->add_crumb('Statistics','admin/reports');

		$year = date('Y',time());
		$month = date('m',time());

		$page['period'] = ' - '.date('M Y',time());

		$page['page_title'] = 'Monthly Statistics';
		$this->ag_auth->view('reports/index',$page); // Load the view
	}


	public function __revenue($type = null,$year = null, $scope = null, $par1 = null, $par2 = null, $par3 = null){

		$type = (is_null($type))?'Global':$type;
		$id = (is_null($type))?'noid':$type;

		if(is_null($scope)){
			$id = 'noid';
			$scope = 'month';
			$year = date('Y',time());
			$par1 = date('m',time());
		}		

		$pdf = null;

		if($scope == 'month'){
			$days = cal_days_in_month(CAL_GREGORIAN, $par1, $year);
			$from =	date('Y-m-d', strtotime($year.'/'.$par1.'/1'));
			$to =	date('Y-m-d', strtotime($year.'/'.$par1.'/'.$days));
			$pdf = $par2;

			$data['month'] = $par1;
			$data['week'] = 1;
		}else if($scope == 'week'){
			$from =	date('Y-m-d', strtotime('1 Jan '.$year.' +'.($par1 - 1).' weeks'));
			$to = date('Y-m-d', strtotime('1 Jan '.$year.' +'.$par1.' weeks - 1 day'));
			$pdf = $par2;

			$data['month'] = 1;
			$data['week'] = $par1;
		}else if($scope == 'date'){
			$from = $par1;
			$to = $par2;
			$pdf = $par3;

			$data['month'] = 1;
			$data['week'] = 1;
		}else{
			$from = date('Y-m-d',time());
			$to = date('Y-m-d',time());
			$pdf = null;			

			$data['month'] = 1;
			$data['week'] = 1;
		}

		$data['year'] = $year;
		$data['from'] = $from;
		$data['to'] = $to;

		$clist = get_merchant(null,false);

		$cs = array('noid'=>'All');
		foreach ($clist as $ckey) {
			$cs[$ckey['id']] = $ckey['merchantname'].' - '.$ckey['fullname'];	
		}

		$data['merchants'] = $cs;
		$data['id'] = $id;

		/* copied from print controller */

		$this->load->library('number_words');

		if($id == 'noid'){
			$data['type_name'] = '-';
			$data['bank_account'] = 'n/a';
			$data['type'] = 'Global';
		}else{
			$user = $this->db->where('id',$id)->get($this->config->item('jayon_members_table'))->row();
			//print $this->db->last_query();
			$data['type'] = $user->merchantname.' - '.$user->fullname;
			$data['type_name'] = $user->fullname;
			$data['bank_account'] = 'n/a';
		}

		$data['period'] = $from.' s/d '.$to;

		$sfrom = date('Y-m-d',strtotime($from));
		$sto = date('Y-m-d',strtotime($to));


		//get ALL DELIVERY  

		$this->db->select('assignment_date,m.merchantname as merchant,delivery_type,status,cod_cost,delivery_cost,count(*) as count, sum(cod_cost) as cod_cost,sum(delivery_cost) as delivery_cost, sum(((total_price-total_discount)+total_tax)) as package_value');
		$this->db->join('members as m',$this->config->item('assigned_delivery_table').'.merchant_id=m.id','left');
		$this->db->from($this->config->item('delivered_delivery_table'));

		$column = 'assignment_date';
		$daterange = sprintf("`%s`between '%s%%' and '%s%%' ", $column, $sfrom, $sto);

		$this->db->where($daterange, null, false);
		$this->db->where($column.' != ','0000-00-00');

		if($id != 'noid'){
			$this->db->where($this->config->item('delivered_delivery_table').'.merchant_id',$id);
		}

		$this->db->and_();
			$this->db->group_start();
				$this->db->where('status',$this->config->item('trans_status_mobile_delivered'));
				//$this->db->or_where('status',$this->config->item('trans_status_mobile_revoked'));
				//$this->db->or_where('status',$this->config->item('trans_status_mobile_noshow'));
				//$this->db->or_where('status',$this->config->item('trans_status_mobile_rescheduled'));
			$this->db->group_end();

		$this->db->group_by('assignment_date,merchant,status');

		$rows = $this->db->get();

		//print $this->db->last_query();

		//get COD DELIVERY  

		$this->db->select('assignment_date,m.merchantname as merchant,status,cod_cost,delivery_cost,delivery_type,count(*) as count, sum(cod_cost) as cod_cost,sum(delivery_cost) as delivery_cost, sum(((total_price-total_discount)+total_tax)) as package_value');
		$this->db->join('members as m',$this->config->item('assigned_delivery_table').'.merchant_id=m.id','left');
		$this->db->from($this->config->item('delivered_delivery_table'));

		$column = 'assignment_date';
		$daterange = sprintf("`%s`between '%s%%' and '%s%%' ", $column, $sfrom, $sto);

		$this->db->where($daterange, null, false);
		$this->db->where($column.' != ','0000-00-00');

		$this->db->where($this->config->item('delivered_delivery_table').'.delivery_type ','COD');

		if($id != 'noid'){
			$this->db->where($this->config->item('delivered_delivery_table').'.merchant_id',$id);
		}

		$this->db->and_();
			$this->db->group_start();
				$this->db->where('status',$this->config->item('trans_status_mobile_delivered'));
				//$this->db->or_where('status',$this->config->item('trans_status_mobile_revoked'));
				//$this->db->or_where('status',$this->config->item('trans_status_mobile_noshow'));
				//$this->db->or_where('status',$this->config->item('trans_status_mobile_rescheduled'));
			$this->db->group_end();

		$this->db->group_by('assignment_date,merchant,status');

		$crows = $this->db->get()->result_array();		

		//print $this->db->last_query();


		$this->table->set_heading(
			'No.',		 	 	
			'Date',
			'Merchant',
			//'Delivery Type',
			'Delivery Count',
			'Total Packet Value',
			'COD Count',
			'%',
			'COD Packet Value',
			'COD Surcharge',
			'Delivery Only',
			'%',
			'Delivery Fee',
			'Status'
		); // Setting headings for the table

		//print_r($rows->result_array());


		$seq = 1;
		$aseq = 0;

		$tcod = 0;
		$tdo = 0;
		$tcodc = 0;
		$tdoc = 0;
		$tpv = 0;
		$tcpv = 0;
		$tdc = 0;

		$rowdate = '';
		$bardate = '';

		foreach ($rows->result_array() as $r) {

			$tcodc += $r['cod_cost'];
			$tdoc += $r['delivery_cost'];
			$tpv += $r['package_value'];

			$r['cod_count'] = 0;
			$r['cod_package_value'] = 0;

			foreach($crows as $c){
				if( $c['assignment_date'] == $r['assignment_date'] &&
					$c['merchant'] == $r['merchant'] &&
					$c['delivery_type'] == $r['delivery_type'] &&
					$c['status'] == $r['status'] )
				{
					$r['cod_count'] = $c['count'];
					$r['cod_package_value'] = $c['package_value'];
				}
			}

			$tcpv += $r['cod_package_value'];

			$r['do_count'] = $r['count'] - $r['cod_count'];

			if($r['cod_count'] > 0){
				$r['cod_pct'] = number_format((($r['cod_count'] / $r['count'])*100),2,',','.').'%';
				$r['do_pct'] = number_format((($r['do_count'] / $r['count'])*100),2,',','.').'%';				
			}else{
				$r['cod_pct'] = '';
				$r['do_pct'] = number_format((($r['do_count'] / $r['count'])*100),2,',','.').'%';				
			}

			$tdc += $r['count'];
			$tcod += $r['cod_count'];
			$tdo += $r['do_count'];

			$datefield = ($bardate == $r['assignment_date'])?'':$r['assignment_date'];

			$this->table->add_row(
				$seq,
				$datefield,		
				$r['merchant'],
				//$r['delivery_type'],		
				$r['count'],
				array('data'=>number_format((int)str_replace('.','',$r['package_value']),2,',','.'),'class'=>'right'),
				$r['cod_count'],
				$r['cod_pct'],
				array('data'=>number_format((int)str_replace('.','',$r['cod_package_value']),2,',','.'),'class'=>'right'),
				array('data'=>number_format((int)str_replace('.','',$r['cod_cost']),2,',','.'),'class'=>'right'),
				$r['do_count'],
				$r['do_pct'],
				array('data'=>number_format((int)str_replace('.','',$r['delivery_cost']),2,',','.'),'class'=>'right'),
				$r['status']
			);				

			$bardate = $r['assignment_date'];

			$seq++;
			$aseq++;
		}

		if($tdc > 0){
			$tcod_pct = number_format((($tcod / $tdc)*100),2,',','.').'%';
			$tdo_pct = number_format((($tdo / $tdc)*100),2,',','.').'%';

			$this->table->add_row(
				array('data'=>'','style'=>'border-top:thin solid grey'),		
				array('data'=>'','style'=>'border-top:thin solid grey'),
				array('data'=>'','style'=>'border-top:thin solid grey'),
				//array('data'=>'','style'=>'border-top:thin solid grey'),
				array('data'=>$tdc,'style'=>'border-top:thin solid grey'),		
				array('data'=>number_format((int)str_replace('.','',$tpv),2,',','.'),'style'=>'border-top:thin solid grey','class'=>'right'),		
				array('data'=>$tcod,'style'=>'border-top:thin solid grey'),
				array('data'=>$tcod_pct,'style'=>'border-top:thin solid grey'),
				array('data'=>number_format((int)str_replace('.','',$tcpv),2,',','.'),'style'=>'border-top:thin solid grey','class'=>'right'),		
				array('data'=>number_format((int)str_replace('.','',$tcodc),2,',','.'),'style'=>'border-top:thin solid grey','class'=>'right'),		
				array('data'=>$tdo,'style'=>'border-top:thin solid grey'),
				array('data'=>$tdo_pct,'style'=>'border-top:thin solid grey'),	
				array('data'=>number_format((int)str_replace('.','',$tdoc),2,',','.'),'style'=>'border-top:thin solid grey','class'=>'right'),
				array('data'=>'','style'=>'border-top:thin solid grey')		
			);				

		}else{
			$this->table->add_row(
				array('data'=>'No Transaction','colspan'=>13,'style'=>'border-bottom:thin solid grey')
			);
			$tcod_pct = '';
			$tdo_pct = '';
		}


		$recontab = $this->table->generate();
		$data['recontab'] = $recontab;

		/* end copy */

		$this->breadcrumb->add_crumb('Revenue by Merchants','admin/reports/reconciliation');

		$page['ajaxurl'] = 'admin/reports/ajaxreconciliation';
		$page['page_title'] = 'Revenue by Merchants';

		$data['controller'] = 'admin/reports/revenue/';

		if($pdf == 'pdf'){
			$html = $this->load->view('print/merchantrecon',$data,true);
			$pdf_name = $type.'_'.$to.'_'.$from.'_'.$id;
			pdf_create($html, $pdf_name.'.pdf','A4','landscape', true); 
		}else if($pdf == 'print'){
			$this->load->view('print/merchantrecon',$data); // Load the view
		}else{
			$this->ag_auth->view('merchantrecon',$data); // Load the view
		}		
	}

	public function _revenue($type = null,$year = null, $scope = null, $par1 = null, $par2 = null, $par3 = null){
		//$this->breadcrumb->add_crumb('Reports','admin/reports/daily');
		$this->breadcrumb->add_crumb('Revenue Report','admin/reports/revenue');

		//$year = date('Y',time());
		//$month = date('m',time());

		//$page['period'] = ' - '.date('M Y',time());


		$type = (is_null($type))?'Global':$type;
		$id = (is_null($type))?'noid':$type;

		if(is_null($scope)){
			$scope = 'month';
			$year = date('Y',time());
			$par1 = date('m',time());
		}		

		$pdf = null;

		if($scope == 'month'){
			$days = cal_days_in_month(CAL_GREGORIAN, $par1, $year);
			$from =	date('Y-m-d', strtotime($year.'/'.$par1.'/1'));
			$to =	date('Y-m-d', strtotime($year.'/'.$par1.'/'.$days));
			$pdf = $par2;

			$data['month'] = $par1;
			$data['week'] = 1;
		}else if($scope == 'week'){
			$from =	date('Y-m-d', strtotime('1 Jan '.$year.' +'.($par1 - 1).' weeks'));
			$to = date('Y-m-d', strtotime('1 Jan '.$year.' +'.$par1.' weeks - 1 day'));
			$pdf = $par2;

			$data['month'] = 1;
			$data['week'] = $par1;
		}else if($scope == 'date'){
			$from = $par1;
			$to = $par2;
			$pdf = $par3;

			$data['month'] = 1;
			$data['week'] = 1;
		}else{
			$from = date('Y-m-d',time());
			$to = date('Y-m-d',time());
			$pdf = null;			

			$data['month'] = 1;
			$data['week'] = 1;
		}

		$data['year'] = $year;
		$data['from'] = $from;
		$data['to'] = $to;

		$data['type'] = $type;
		$data['period'] = $from.' s/d '.$to;

		$page['page_title'] = 'Revenue Report';

		$data['controller'] = 'admin/reports/revenue/';

		if($pdf == 'pdf'){
			$html = $this->load->view('print/revenue',$data,true);
			$pdf_name = $type.'_'.$to.'_'.$from.'_'.$id;
			pdf_create($html, $pdf_name.'.pdf','A4','landscape', true); 
		}else if($pdf == 'print'){
			$this->load->view('print/revenue',$data); // Load the view
		}else{
			$this->ag_auth->view('revenue',$data); // Load the view
		}
	}

	public function statistics($type = null,$year = null, $scope = null, $par1 = null, $par2 = null, $par3 = null){
		//$this->breadcrumb->add_crumb('Reports','admin/reports/daily');
		$this->breadcrumb->add_crumb('Statistics','admin/reports/statistics');

		//$year = date('Y',time());
		//$month = date('m',time());

		//$page['period'] = ' - '.date('M Y',time());


		$type = (is_null($type))?'Global':$type;
		$id = (is_null($type))?'noid':$type;

		if(is_null($scope)){
			$scope = 'month';
			$year = date('Y',time());
			$par1 = date('m',time());
		}		

		$pdf = null;

		if($scope == 'month'){
			$days = cal_days_in_month(CAL_GREGORIAN, $par1, $year);
			$from =	date('Y-m-d', strtotime($year.'/'.$par1.'/1'));
			$to =	date('Y-m-d', strtotime($year.'/'.$par1.'/'.$days));
			$pdf = $par2;

			$data['month'] = $par1;
			$data['week'] = 1;
		}else if($scope == 'week'){
			$from =	date('Y-m-d', strtotime('1 Jan '.$year.' +'.($par1 - 1).' weeks'));
			$to = date('Y-m-d', strtotime('1 Jan '.$year.' +'.$par1.' weeks - 1 day'));
			$pdf = $par2;

			$data['month'] = 1;
			$data['week'] = $par1;
		}else if($scope == 'date'){
			$from = $par1;
			$to = $par2;
			$pdf = $par3;

			$data['month'] = 1;
			$data['week'] = 1;
		}else{
			$from = date('Y-m-d',time());
			$to = date('Y-m-d',time());
			$pdf = null;			

			$data['month'] = 1;
			$data['week'] = 1;
		}

		$data['total_to_date'] = $this->db->count_all_results($this->config->item('incoming_delivery_table'));

		$this->db->where('delivery_type','COD');
		$data['total_to_date_cod'] = $this->db->count_all_results($this->config->item('incoming_delivery_table'));

		$this->db->where('delivery_type','CCOD');
		$data['total_to_date_ccod'] = $this->db->count_all_results($this->config->item('incoming_delivery_table'));

		$this->db->where('delivery_type','PS');
		$data['total_to_date_ps'] = $this->db->count_all_results($this->config->item('incoming_delivery_table'));

		$this->db->where('delivery_type','Delivery Only');
		$data['total_to_date_do'] = $this->db->count_all_results($this->config->item('incoming_delivery_table'));

		$daterange = sprintf("ordertime BETWEEN '%s%%' AND '%s%%'",$from,$to);
		$this->db->where($daterange, NULL, FALSE); 
		$data['total_in_period'] = $this->db->count_all_results($this->config->item('incoming_delivery_table'));

		$this->db->where($daterange, NULL, FALSE);
		$this->db->where('delivery_type','COD');
		$data['total_in_period_cod'] = $this->db->count_all_results($this->config->item('incoming_delivery_table'));

		$this->db->where($daterange, NULL, FALSE);
		$this->db->where('delivery_type','CCOD');
		$data['total_in_period_ccod'] = $this->db->count_all_results($this->config->item('incoming_delivery_table'));

		$this->db->where($daterange, NULL, FALSE);
		$this->db->where('delivery_type','PS');
		$data['total_in_period_ps'] = $this->db->count_all_results($this->config->item('incoming_delivery_table'));

		$this->db->where($daterange, NULL, FALSE);
		$this->db->where('delivery_type','Delivery Only');
		$data['total_in_period_do'] = $this->db->count_all_results($this->config->item('incoming_delivery_table'));


		$data['year'] = $year;
		$data['from'] = $from;
		$data['to'] = $to;

		$data['type'] = $type;
		$data['period'] = $from.' s/d '.$to;

		$page['page_title'] = 'Statistics';

		$data['controller'] = 'admin/reports/statistics/';

		if($pdf == 'pdf'){
			$html = $this->load->view('print/statistics',$data,true);
			$pdf_name = $type.'_'.$to.'_'.$from.'_'.$id;
			pdf_create($html, $pdf_name.'.pdf','A4','landscape', true); 
		}else if($pdf == 'print'){
			$this->load->view('print/statistics',$data); // Load the view
		}else{
			$this->ag_auth->view('statistics',$data); // Load the view
		}
	}

	public function dist($type = null,$year = null, $scope = null, $par1 = null, $par2 = null, $par3 = null){
		//$this->breadcrumb->add_crumb('Reports','admin/reports/daily');
		$this->breadcrumb->add_crumb('Distribution','admin/reports/statistics');

		//$year = date('Y',time());
		//$month = date('m',time());

		//$page['period'] = ' - '.date('M Y',time());


		$type = (is_null($type))?'Global':$type;
		$id = (is_null($type))?'noid':$type;

		if(is_null($scope)){
			$scope = 'month';
			$year = date('Y',time());
			$par1 = date('m',time());
		}		

		$pdf = null;

		if($scope == 'month'){
			$days = cal_days_in_month(CAL_GREGORIAN, $par1, $year);
			$from =	date('Y-m-d', strtotime($year.'/'.$par1.'/1'));
			$to =	date('Y-m-d', strtotime($year.'/'.$par1.'/'.$days));
			$pdf = $par2;

			$data['month'] = $par1;
			$data['week'] = 1;

			$mo = mktime(0, 0, 0, $par1, 1, $year);
			$mo =date('F Y',$mo);

			$ptitle = 'Monthly Distribution Graph - '.$mo.' ( '.$from.' s/d '.$to.' )';

		}else if($scope == 'week'){
			$from =	date('Y-m-d', strtotime('1 Jan '.$year.' +'.($par1 - 1).' weeks'));
			$to = date('Y-m-d', strtotime('1 Jan '.$year.' +'.$par1.' weeks - 1 day'));
			$pdf = $par2;

			$data['month'] = 1;
			$data['week'] = $par1;

			$ptitle = 'Weekly Distribution Graph - Week '.$par1.' '.$year.' ( '.$from.' s/d '.$to.' )';

		}else if($scope == 'date'){
			$from = $par1;
			$to = $par2;
			$pdf = $par3;

			$data['month'] = 1;
			$data['week'] = 1;

			$ptitle = 'Distribution Graph : '.$from.' s/d '.$to;

		}else{
			$from = date('Y-m-d',time());
			$to = date('Y-m-d',time());
			$pdf = null;			

			$data['month'] = 1;
			$data['week'] = 1;

			$ptitle = 'Distribution Graph : '.$from.' s/d '.$to;

		}

		$data['year'] = $year;
		$data['from'] = $from;
		$data['to'] = $to;

		$data['type'] = $type;
		$data['period'] = $ptitle;

		$page['page_title'] = 'Distribution Graph';

		$data['controller'] = 'admin/reports/dist/';

		if($pdf == 'pdf'){
			$html = $this->load->view('print/dist',$data,true);
			$pdf_name = $type.'_'.$to.'_'.$from.'_'.$id;
			pdf_create($html, $pdf_name.'.pdf','A4','landscape', true); 
		}else if($pdf == 'print'){
			$this->load->view('print/dist',$data); // Load the view
		}else{
			$this->ag_auth->view('dist',$data); // Load the view
		}
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

	public function reconciliation($type = null,$year = null, $scope = null, $par1 = null, $par2 = null, $par3 = null){

		$type = (is_null($type))?'Global':$type;
		$id = (is_null($type))?'noid':$type;

		if(is_null($scope)){
			$scope = 'month';
			$year = date('Y',time());
			$par1 = date('m',time());
		}		

		$pdf = null;

		if($scope == 'month'){
			$days = cal_days_in_month(CAL_GREGORIAN, $par1, $year);
			$from =	date('Y-m-d', strtotime($year.'/'.$par1.'/1'));
			$to =	date('Y-m-d', strtotime($year.'/'.$par1.'/'.$days));
			$pdf = $par2;

			$data['month'] = $par1;
			$data['week'] = 1;
		}else if($scope == 'week'){
			$from =	date('Y-m-d', strtotime('1 Jan '.$year.' +'.($par1 - 1).' weeks'));
			$to = date('Y-m-d', strtotime('1 Jan '.$year.' +'.$par1.' weeks - 1 day'));
			$pdf = $par2;

			$data['month'] = 1;
			$data['week'] = $par1;
		}else if($scope == 'date'){
			$from = $par1;
			$to = $par2;
			$pdf = $par3;

			$data['month'] = 1;
			$data['week'] = 1;
		}else{
			$from = date('Y-m-d',time());
			$to = date('Y-m-d',time());
			$pdf = null;			

			$data['month'] = 1;
			$data['week'] = 1;
		}

		$data['year'] = $year;
		$data['from'] = $from;
		$data['to'] = $to;

		/* copied from print controller */

		$this->load->library('number_words');

		if($id == 'noid'){
			$data['type_name'] = '-';
			$data['bank_account'] = 'n/a';
		}else{
			if($type == 'Merchant'){
				$user = $this->db->where('id',$id)->get($this->config->item('jayon_members_table'))->row();
				$data['type_name'] = $user->fullname;
				$data['bank_account'] = ($user->account_number == '')?'n/a':$user->bank.' - '.$user->account_number.' - '.$user->account_name;
			}else if($type == 'Courier'){
				$user = $this->db->where('id',$id)->get($this->config->item('jayon_couriers_table'))->row();
				$data['type_name'] = $user->fullname;
				$data['bank_account'] = 'n/a';
			}
		}

		$data['type'] = $type;
		$data['period'] = $from.' s/d '.$to;

		$sfrom = date('Y-m-d',strtotime($from));
		$sto = date('Y-m-d',strtotime($to));

		$this->db->select($this->config->item('delivered_delivery_table').'.*,b.fullname as buyer,m.merchantname as merchant,a.domain as domain,a.application_name as app_name,d.identifier as device,c.fullname as courier');
		$this->db->from($this->config->item('delivered_delivery_table'));
		$this->db->join('members as b',$this->config->item('assigned_delivery_table').'.buyer_id=b.id','left');
		$this->db->join('members as m',$this->config->item('assigned_delivery_table').'.merchant_id=m.id','left');
		$this->db->join('applications as a',$this->config->item('assigned_delivery_table').'.application_id=a.id','left');
		$this->db->join('devices as d',$this->config->item('assigned_delivery_table').'.device_id=d.id','left');
		$this->db->join('couriers as c',$this->config->item('assigned_delivery_table').'.courier_id=c.id','left');


		$column = 'assignment_date';
		$daterange = sprintf("`%s`between '%s%%' and '%s%%' ", $column, $sfrom, $sto);

		$this->db->where($daterange, null, false);
		$this->db->where($column.' != ','0000-00-00');

		if($id != 'noid'){
			if($type == 'Merchant'){
				$this->db->where($this->config->item('delivered_delivery_table').'.merchant_id',$id);
			}else if($type == 'Courier'){
				$this->db->where($this->config->item('delivered_delivery_table').'.courier_id',$id);
			}
		}

		$this->db->and_();
		$this->db->group_start();
		$this->db->where('status',$this->config->item('trans_status_mobile_delivered'));
		//$this->db->or_where('status',$this->config->item('trans_status_mobile_revoked'));
		//$this->db->or_where('status',$this->config->item('trans_status_mobile_noshow'));
		//$this->db->or_where('status',$this->config->item('trans_status_mobile_rescheduled'));
		$this->db->group_end();

		$rows = $this->db->get();

		//print $this->db->last_query();

		$this->table->set_heading(
			array('data'=>'Delivery Details',
				'colspan'=>'13'
			)	
		);


		if($type == 'Merchant' || $type == 'Global'){
			$this->table->set_heading(
				'No.',		 	 	
				'Merchant Trans ID',	 	 	 	 	 	 	 
				'Delivery ID',
				'Merchant Name',
				'Store',
				'Delivery Date',
				'Status',
				'Goods Price',
				'Disc',
				'Tax',
				'Delivery Chg',
				'COD Surchg',		
				'Payable Value'		
			); // Setting headings for the table

		}else if($type == 'Courier'){
			$this->table->set_heading(
				'No.',		 	 	
				'Merchant Trans ID',	 	 	 	 	 	 	 
				'Delivery ID',
				'Merchant Name',
				'Store',
				'Delivery Date',
				'Status'
				//'Delivery Chg',
				//'COD Surchg',		
				//'Payable Value'		
			); // Setting headings for the table			
		}


		$seq = 1;
		$total_billing = 0;
		$total_delivery = 0;
		$total_cod = 0;

		//print_r($rows->result());

		foreach($rows->result() as $r){

			$total = str_replace(array(',','.'), '', $r->total_price);
			$dsc = str_replace(array(',','.'), '', $r->total_discount);
			$tax = str_replace(array(',','.'), '',$r->total_tax);
			$dc = str_replace(array(',','.'), '',$r->delivery_cost);
			$cod = str_replace(array(',','.'), '',$r->cod_cost);

			$total = (int)$total;
			$dsc = (int)$dsc;
			$tax = (int)$tax;
			$dc = (int)$dc;
			$cod = (int)$cod;

			$payable = 0;


			if($r->status == $this->config->item('trans_status_mobile_delivered')){
				if($type == 'Merchant' || $type == 'Global'){
					$payable = ($total - $dsc) + $tax;
					// + $dc + $cod;				
				}else if($type == 'Courier'){
					$payable = ($dc + $cod) * 0.1;
				}
				$total_billing += (int)str_replace('.','',$payable);
			}else if(
				$r->status == $this->config->item('trans_status_mobile_revoked') ||
				$r->status == $this->config->item('trans_status_mobile_rescheduled') ||
				$r->status == $this->config->item('trans_status_mobile_noshow'))
			{
				//TBA	
			}

			$total_delivery += (int)str_replace('.','',$dc);
			$total_cod += (int)str_replace('.','',$cod);

			if($type == 'Merchant' || $type == 'Global'){
				$this->table->add_row(
					$seq,		
					$r->merchant_trans_id,		
					$r->delivery_id,
					$r->merchant,
					$r->app_name.'<hr />'.$r->domain,
					$r->assignment_date,
					$r->status,
					number_format((int)str_replace('.','',$total),2,',','.'),
					number_format((int)str_replace('.','',$dsc),2,',','.'),
					number_format((int)str_replace('.','',$tax),2,',','.'),
					number_format((int)str_replace('.','',$dc),2,',','.'),			
					number_format((int)str_replace('.','',$cod),2,',','.'),
					number_format((int)str_replace('.','',$payable),2,',','.')
				);

			}else if($type == 'Courier'){
				$this->table->add_row(
					$seq,		
					$r->merchant_trans_id,		
					$r->delivery_id,
					$r->merchant,
					$r->app_name.'<hr />'.$r->domain,
					$r->assignment_date,
					$r->status
					//number_format((int)str_replace('.','',$dc),2,',','.'),			
					//number_format((int)str_replace('.','',$cod),2,',','.'),
					//number_format((int)str_replace('.','',$payable),2,',','.')
				);				
			}

			$seq++;
		}

		if($type == 'Merchant' || $type == 'Global'){
			$total_span = 10;
			$say_span = 12;

		}else if($type == 'Courier'){
			$total_span = 7;
			$say_span = 9;
		}


		$this->table->add_row(
			array('data'=>'Total','colspan'=>$total_span),
			number_format($total_delivery,2,',','.'),
			number_format($total_cod,2,',','.'),
			number_format($total_billing,2,',','.')
		);

		$this->table->add_row(
			'Terbilang',
			array('data'=>'&nbsp;','colspan'=>$say_span)
		);

		if($type == 'Merchant' || $type == 'Global'){
			$this->table->add_row(
				'Payable',
				array('data'=>$this->number_words->to_words($total_billing).' rupiah',
					'colspan'=>$say_span)
			);
		}

		$this->table->add_row(
			'Delivery Charge',
			array('data'=>$this->number_words->to_words($total_delivery).' rupiah',
				'colspan'=>$say_span)
		);

		$this->table->add_row(
			'COD Surcharge',
			array('data'=>$this->number_words->to_words($total_cod).' rupiah',
				'colspan'=>$say_span)
		);

		$recontab = $this->table->generate();
		$data['recontab'] = $recontab;

		/* end copy */

		$this->breadcrumb->add_crumb('Reconciliations','admin/reports/reconciliation');

		$page['ajaxurl'] = 'admin/reports/ajaxreconciliation';
		$page['page_title'] = 'Reconciliations';

		$data['controller'] = 'admin/reports/reconciliation/';

		if($pdf == 'pdf'){
			$html = $this->load->view('print/reconciliation',$data,true);
			$pdf_name = $type.'_'.$to.'_'.$from.'_'.$id;
			pdf_create($html, $pdf_name.'.pdf','A4','landscape', true); 
		}else if($pdf == 'print'){
			$this->load->view('print/reconciliation',$data); // Load the view
		}else{
			$this->ag_auth->view('reconciliation',$data); // Load the view
		}		
	}

	public function ajaxreconciliation($type = null){


		$week = date('W',time());
		$year = date('Y',time());

		$aadata = array();

		for($i = $week; $i > 0; $i--)
		{

			$from =	date('Y-m-d', strtotime('1 Jan '.$year.' +'.($i - 1).' weeks'));
			$to = date('Y-m-d', strtotime('1 Jan '.$year.' +'.$i.' weeks - 1 day'));

			$printrecon = '<span class="printrecon" id="'.$from.'_'.$to.'_noid'.'" title="Global" style="cursor:pointer;text-decoration:underline;" >Global</span>';


			$generate = anchor("admin/reports/globalreport/".$from."/".$to, "Global"); // Build actions links
			$merchantlist = anchor("admin/reports/merchants/".$from."/".$to, "By Merchant"); // Build actions links
			$courierlist = anchor("admin/reports/couriers/".$from."/".$to, "By Courier"); // Build actions links
			$aadata[] = array(
				$year,
				$i,
				date('Y-m-d', strtotime('1 Jan '.$year.' +'.($i - 1).' weeks')),
				date('Y-m-d', strtotime('1 Jan '.$year.' +'.$i.' weeks - 1 day')),
				$printrecon.' '.$merchantlist.' '.$courierlist
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


	public function revenue($type = null,$year = null, $scope = null, $par1 = null, $par2 = null, $par3 = null){

		$type = (is_null($type))?'Global':$type;
		$id = (is_null($type))?'noid':$type;

		if(is_null($scope)){
			$id = 'noid';
			$scope = 'month';
			$year = date('Y',time());
			$par1 = date('m',time());
		}		

		$pdf = null;

		if($scope == 'month'){
			$days = cal_days_in_month(CAL_GREGORIAN, $par1, $year);
			$from =	date('Y-m-d', strtotime($year.'/'.$par1.'/1'));
			$to =	date('Y-m-d', strtotime($year.'/'.$par1.'/'.$days));
			$pdf = $par2;

			$data['month'] = $par1;
			$data['week'] = 1;
		}else if($scope == 'week'){
			$from =	date('Y-m-d', strtotime('1 Jan '.$year.' +'.($par1 - 1).' weeks'));
			$to = date('Y-m-d', strtotime('1 Jan '.$year.' +'.$par1.' weeks - 1 day'));
			$pdf = $par2;

			$data['month'] = 1;
			$data['week'] = $par1;
		}else if($scope == 'date'){
			$from = $par1;
			$to = $par2;
			$pdf = $par3;

			$data['month'] = 1;
			$data['week'] = 1;
		}else{
			$from = date('Y-m-d',time());
			$to = date('Y-m-d',time());
			$pdf = null;			

			$data['month'] = 1;
			$data['week'] = 1;
		}

		$data['year'] = $year;
		$data['from'] = $from;
		$data['to'] = $to;

		$clist = get_merchant(null,false);

		$cs = array('noid'=>'All');
		foreach ($clist as $ckey) {
			$cs[$ckey['id']] = $ckey['merchantname'].' - '.$ckey['fullname'];	
		}

		$data['merchants'] = $cs;
		$data['id'] = $id;

		/* copied from print controller */

		$this->load->library('number_words');

		if($id == 'noid'){
			$data['type_name'] = '-';
			$data['bank_account'] = 'n/a';
			$data['type'] = 'Global';
		}else{
			$user = $this->db->where('id',$id)->get($this->config->item('jayon_members_table'))->row();
			//print $this->db->last_query();
			$data['type'] = $user->merchantname.' - '.$user->fullname;
			$data['type_name'] = $user->fullname;
			$data['bank_account'] = 'n/a';
		}

		$data['period'] = $from.' s/d '.$to;

		$sfrom = date('Y-m-d',strtotime($from));
		$sto = date('Y-m-d',strtotime($to));




		$this->db->distinct();
/*		if($id == 'noid'){
			$this->db->select('assignment_date,delivery_type,count(*) as count, sum(cod_cost) as cod_cost,sum(delivery_cost) as delivery_cost,sum(total_price) as total_price ,sum(total_discount) as total_discount , sum(total_tax) as total_tax,sum(((total_price-total_discount)+total_tax)) as package_value');
		}else{
*/			$this->db->select('assignment_date,merchant_id,delivery_type,count(*) as count, sum(cod_cost) as cod_cost,sum(delivery_cost) as delivery_cost,sum(total_price) as total_price ,sum(total_discount) as total_discount , sum(total_tax) as total_tax,sum(((total_price-total_discount)+total_tax)) as package_value');
//		}
		$this->db->from($this->config->item('delivered_delivery_table'));

		$column = 'assignment_date';
		$daterange = sprintf("`%s`between '%s%%' and '%s%%' ", $column, $sfrom, $sto);

		$this->db->where($daterange, null, false);
		$this->db->where($column.' != ','0000-00-00');

		if($id != 'noid'){
			$this->db->where($this->config->item('delivered_delivery_table').'.merchant_id',$id);
		}

		$this->db->and_();
			$this->db->group_start();
				$this->db->where('status',	 $this->config->item('trans_status_mobile_delivered'));
				$this->db->or_where('status',$this->config->item('trans_status_mobile_revoked'));
				$this->db->or_where('status',$this->config->item('trans_status_mobile_noshow'));
				$this->db->or_where('status',$this->config->item('trans_status_mobile_rescheduled'));
			$this->db->group_end();

/*		if($id == 'noid'){
			$this->db->group_by('assignment_date,delivery_type');
		}else{
*/			$this->db->group_by('assignment_date,merchant_id,delivery_type');
//		}


		$rows = $this->db->get();

		$result = $rows->result_array();
		//print $this->db->last_query();

		$trans = array();


		foreach($result as $r){
			//print_r($r);
/*			if($id == 'noid'){
				$mid = 'noid';
			}else{
*/				//print_r($result);
				$mid = $r['merchant_id'];
				//$mid = 'mid';
//			}
			$trans[$r['assignment_date']][$mid][$r['delivery_type']]['count'] = $r['count'];
			$trans[$r['assignment_date']][$mid][$r['delivery_type']]['cod_cost'] = $r['cod_cost'];
			$trans[$r['assignment_date']][$mid][$r['delivery_type']]['delivery_cost'] = $r['delivery_cost'];
			$trans[$r['assignment_date']][$mid][$r['delivery_type']]['total_price'] = $r['total_price'];
			$trans[$r['assignment_date']][$mid][$r['delivery_type']]['package_value'] = $r['package_value'];
		}

		$status_array = array(
			$this->config->item('trans_status_mobile_delivered'),
			$this->config->item('trans_status_mobile_revoked'),
			$this->config->item('trans_status_mobile_noshow'),
			$this->config->item('trans_status_mobile_rescheduled')
		);

		$type_array = array(
			'COD',
			'CCOD',
			'Delivery Only',
			'PS'
		);

		//print_r($trans);

		foreach ($trans as $key => $value) {

			foreach($value as $k=>$v){

				foreach($type_array as $t){

					//foreach($status_array as $s){

						if(!isset($trans[$key][$k][$t])){
							$trans[$key][$k][$t]['count'] = 0;
							$trans[$key][$k][$t]['cod_cost'] = 0;
							$trans[$key][$k][$t]['delivery_cost'] = 0;
							$trans[$key][$k][$t]['total_price'] = 0;
							$trans[$key][$k][$t]['package_value'] = 0;
						}

					//}

				}

			}

		}


		//print_r($trans);

		$this->table->set_heading(
			'',		 	 	
			'',
			'',
			array('data'=>'DO','colspan'=>'3'),		
			array('data'=>'COD','colspan'=>'4'),		
			array('data'=>'CCOD','colspan'=>'4'),		
			array('data'=>'PS','colspan'=>'3'),		
			
			array('data'=>'Total','colspan'=>'2')	
		); // Setting headings for the table			


		$this->table->set_subheading(
			'No.',		 	 	
			'Date',
			'Merchant',

			'count',
			'dcost',
			'pval',

			'count',
			'dcost',
			'sur',
			'pval',

			'count',
			'dcost',
			'sur',
			'pval',

			'count',
			'pfee',
			'pval',

			'Delivery Count',
			'Package Value'
		); // Setting headings for the table			

		$counter  = 1;

		$total = array();

		$total['Delivery Only']['count'] = 0;
		$total['Delivery Only']['dcost'] = 0;
		$total['Delivery Only']['pval'] = 0; 
		$total['COD']['count'] = 0; 
		$total['COD']['dcost'] = 0; 
		$total['COD']['sur'] = 0; 
		$total['COD']['pval']  = 0;
		$total['CCOD']['count']  = 0;
		$total['CCOD']['dcost']  = 0;
		$total['CCOD']['sur']  = 0;
		$total['CCOD']['pval']  = 0;
		$total['PS']['count']  = 0;
		$total['PS']['pfee']  = 0;
		$total['PS']['pval']  = 0;
		$total['delivered']['count'] = 0;  
		$total['noshow']['count']  = 0;
		$total['rescheduled']['count']  = 0;
		$total['total_delivery_count']  = 0;
		$total['total_package_value']  = 0;

		$lastdate = '';

		foreach($trans as $key=>$val){

			foreach ($val as $k => $v) {

				$r[$key][$k] = $this->_makerevrow($v);

				$this->table->add_row(
					$counter,
					($lastdate == $key)?'':date('d-m-Y',strtotime($key)),
					$cs[$k],
					array('data'=>$r[$key][$k]['Delivery Only']['count'],'class'=>'count'),
					array('data'=>idr($r[$key][$k]['Delivery Only']['dcost']),'class'=>'currency'),		
					array('data'=>idr($r[$key][$k]['Delivery Only']['pval']),'class'=>'currency'),		

					array('data'=>$r[$key][$k]['COD']['count'],'class'=>'count'),
					array('data'=>idr($r[$key][$k]['COD']['dcost']),'class'=>'currency'),
					array('data'=>idr($r[$key][$k]['COD']['sur']),'class'=>'currency'),
					array('data'=>idr($r[$key][$k]['COD']['pval']),'class'=>'currency'),

					array('data'=>$r[$key][$k]['CCOD']['count'],'class'=>'count'),
					array('data'=>idr($r[$key][$k]['CCOD']['dcost']),'class'=>'currency'),
					array('data'=>idr($r[$key][$k]['CCOD']['sur']),'class'=>'currency'),
					array('data'=>idr($r[$key][$k]['CCOD']['pval']),'class'=>'currency'),

					array('data'=>$r[$key][$k]['PS']['count'],'class'=>'count'),
					array('data'=>idr($r[$key][$k]['PS']['pfee']),'class'=>'currency'),
					array('data'=>idr($r[$key][$k]['PS']['pval']),'class'=>'currency'),

					array('data'=>$r[$key][$k]['total_delivery_count'],'class'=>'count'),
					array('data'=>idr($r[$key][$k]['total_package_value']),'class'=>'currency')

				);

					$lastdate = $key;

					$total['Delivery Only']['count'] +=	$r[$key][$k]['Delivery Only']['count'];
					$total['Delivery Only']['dcost'] +=	$r[$key][$k]['Delivery Only']['dcost'];
					$total['Delivery Only']['pval'] += $r[$key][$k]['Delivery Only']['pval'];
					$total['COD']['count'] += $r[$key][$k]['COD']['count'];
					$total['COD']['dcost'] += $r[$key][$k]['COD']['dcost'];
					$total['COD']['sur'] +=	$r[$key][$k]['COD']['sur'];
					$total['COD']['pval'] += $r[$key][$k]['COD']['pval'];
					$total['CCOD']['count'] += $r[$key][$k]['CCOD']['count'];
					$total['CCOD']['dcost'] += $r[$key][$k]['CCOD']['dcost'];
					$total['CCOD']['sur'] += $r[$key][$k]['CCOD']['sur'];
					$total['CCOD']['pval'] += $r[$key][$k]['CCOD']['pval'];
					$total['PS']['count'] += $r[$key][$k]['PS']['count'];
					$total['PS']['pfee'] +=	$r[$key][$k]['PS']['pfee'];
					$total['PS']['pval'] +=	$r[$key][$k]['PS']['pval'];
					$total['total_delivery_count'] += $r[$key][$k]['total_delivery_count'];
					$total['total_package_value'] += $r[$key][$k]['total_package_value'];

				$counter++;			

			}

		}

			$this->table->add_row(
				'',
				'',

				array('data'=>'Total','class'=>'total'),		

				array('data'=>$total['Delivery Only']['count'],'class'=>'total count'),
				array('data'=>idr($total['Delivery Only']['dcost']),'class'=>'total currency'),		
				array('data'=>idr($total['Delivery Only']['pval']),'class'=>'total currency'),		

				array('data'=>$total['COD']['count'],'class'=>'total count'),
				array('data'=>idr($total['COD']['dcost']),'class'=>'total currency'),
				array('data'=>idr($total['COD']['sur']),'class'=>'total currency'),
				array('data'=>idr($total['COD']['pval']),'class'=>'total currency'),

				array('data'=>$total['CCOD']['count'],'class'=>'total count'),
				array('data'=>idr($total['CCOD']['dcost']),'class'=>'total currency'),
				array('data'=>idr($total['CCOD']['sur']),'class'=>'total currency'),
				array('data'=>idr($total['CCOD']['pval']),'class'=>'total currency'),

				array('data'=>$total['PS']['count'],'class'=>'total count'),
				array('data'=>idr($total['PS']['pfee']),'class'=>'total currency'),
				array('data'=>idr($total['PS']['pval']),'class'=>'total currency'),

				array('data'=>$total['total_delivery_count'],'class'=>'total count'),
				array('data'=>idr($total['total_package_value']),'class'=>'total currency')

			);

		$recontab = $this->table->generate();
		$data['recontab'] = $recontab;

		/* end copy */

		$this->breadcrumb->add_crumb('Revenue','admin/reports/reconciliation');

		$page['ajaxurl'] = 'admin/reports/ajaxreconciliation';
		$page['page_title'] = 'Merchant Reconciliations';

		$data['controller'] = 'admin/reports/revenue/';

		if($pdf == 'pdf'){
			$html = $this->load->view('print/revenue',$data,true);
			$pdf_name = $type.'_'.$to.'_'.$from.'_'.$id;
			pdf_create($html, $pdf_name.'.pdf','A4','landscape', true); 
		}else if($pdf == 'print'){
			$this->load->view('print/merchantrecon',$data); // Load the view
		}else{
			$this->ag_auth->view('merchantrecon',$data); // Load the view
		}		
	}

	private function _makerevrow($v){

		//print_r($v);
		
		$r = array();

		$r['COD']['count'] = $v['COD']['count'];
		$r['CCOD']['count'] = $v['CCOD']['count'];
		$r['Delivery Only']['count'] = $v['Delivery Only']['count'];
		$r['PS']['count'] = $v['PS']['count'];

		$r['COD']['dcost'] = $v['COD']['delivery_cost'];

		$r['CCOD']['dcost'] = $v['CCOD']['delivery_cost'];
		
		$r['COD']['sur'] = $v['COD']['cod_cost'];
		$r['CCOD']['sur'] = $v['CCOD']['cod_cost'];

		$r['COD']['pval'] = $v['COD']['package_value'];
		$r['CCOD']['pval'] = $v['CCOD']['package_value'];

		$r['Delivery Only']['pval'] = $v['Delivery Only']['package_value'];
		$r['PS']['pval'] = $v['PS']['package_value'];
		
		$r['Delivery Only']['dcost'] = $v['Delivery Only']['delivery_cost'];

		$r['PS']['pfee'] = $v['PS']['delivery_cost'];

		$r['total_delivery_count'] = $r['COD']['count'] + $r['CCOD']['count'] + $r['Delivery Only']['count'] + $r['PS']['count'];
		$r['total_package_value'] = $r['Delivery Only']['pval'] + $r['COD']['pval'] + $r['CCOD']['pval'] + $r['PS']['pval'];
		

		return $r;
	}


	public function merchantrecon($type = null,$year = null, $scope = null, $par1 = null, $par2 = null, $par3 = null){

		$type = (is_null($type))?'Global':$type;
		$id = (is_null($type))?'noid':$type;

		if(is_null($scope)){
			$id = 'noid';
			$scope = 'month';
			$year = date('Y',time());
			$par1 = date('m',time());
		}		

		$pdf = null;

		if($scope == 'month'){
			$days = cal_days_in_month(CAL_GREGORIAN, $par1, $year);
			$from =	date('Y-m-d', strtotime($year.'/'.$par1.'/1'));
			$to =	date('Y-m-d', strtotime($year.'/'.$par1.'/'.$days));
			$pdf = $par2;

			$data['month'] = $par1;
			$data['week'] = 1;
		}else if($scope == 'week'){
			$from =	date('Y-m-d', strtotime('1 Jan '.$year.' +'.($par1 - 1).' weeks'));
			$to = date('Y-m-d', strtotime('1 Jan '.$year.' +'.$par1.' weeks - 1 day'));
			$pdf = $par2;

			$data['month'] = 1;
			$data['week'] = $par1;
		}else if($scope == 'date'){
			$from = $par1;
			$to = $par2;
			$pdf = $par3;

			$data['month'] = 1;
			$data['week'] = 1;
		}else{
			$from = date('Y-m-d',time());
			$to = date('Y-m-d',time());
			$pdf = null;			

			$data['month'] = 1;
			$data['week'] = 1;
		}

		$data['year'] = $year;
		$data['from'] = $from;
		$data['to'] = $to;

		$clist = get_merchant(null,false);

		$cs = array('noid'=>'All');
		foreach ($clist as $ckey) {
			$cs[$ckey['id']] = $ckey['merchantname'].' - '.$ckey['fullname'];	
		}

		$data['merchants'] = $cs;
		$data['id'] = $id;

		/* copied from print controller */

		$this->load->library('number_words');

		if($id == 'noid'){
			$data['type_name'] = '-';
			$data['bank_account'] = 'n/a';
			$data['type'] = 'Global';
		}else{
			$user = $this->db->where('id',$id)->get($this->config->item('jayon_members_table'))->row();
			//print $this->db->last_query();
			$data['type'] = $user->merchantname.' - '.$user->fullname;
			$data['type_name'] = $user->fullname;
			$data['bank_account'] = 'n/a';
		}

		$data['period'] = $from.' s/d '.$to;

		$sfrom = date('Y-m-d',strtotime($from));
		$sto = date('Y-m-d',strtotime($to));




		$this->db->distinct();
		$this->db->select('assignment_date,delivery_type,status,count(*) as count, sum(cod_cost) as cod_cost,sum(delivery_cost) as delivery_cost,sum(total_price) as total_price ,sum(total_discount) as total_discount , sum(total_tax) as total_tax,sum(((total_price-total_discount)+total_tax)) as package_value');
		$this->db->from($this->config->item('delivered_delivery_table'));

		$column = 'assignment_date';
		$daterange = sprintf("`%s`between '%s%%' and '%s%%' ", $column, $sfrom, $sto);

		$this->db->where($daterange, null, false);
		$this->db->where($column.' != ','0000-00-00');

		if($id != 'noid'){
			$this->db->where($this->config->item('delivered_delivery_table').'.merchant_id',$id);
		}

		$this->db->and_();
			$this->db->group_start();
				$this->db->where('status',	 $this->config->item('trans_status_mobile_delivered'));
				$this->db->or_where('status',$this->config->item('trans_status_mobile_revoked'));
				$this->db->or_where('status',$this->config->item('trans_status_mobile_noshow'));
				$this->db->or_where('status',$this->config->item('trans_status_mobile_rescheduled'));
			$this->db->group_end();

		$this->db->group_by('assignment_date,delivery_type');

		$rows = $this->db->get();

		//print $this->db->last_query();

		$this->table->set_heading(
			array('data'=>'Delivery Details',
				'colspan'=>'4'
			)	
		);

		$trans = array();
		foreach($rows->result_array() as $r){
			//print_r($r);
			$trans[$r['assignment_date']][$r['delivery_type']][$r['status']]['count'] = $r['count'];
			$trans[$r['assignment_date']][$r['delivery_type']][$r['status']]['cod_cost'] = $r['cod_cost'];
			$trans[$r['assignment_date']][$r['delivery_type']][$r['status']]['delivery_cost'] = $r['delivery_cost'];
			$trans[$r['assignment_date']][$r['delivery_type']][$r['status']]['total_price'] = $r['total_price'];
			$trans[$r['assignment_date']][$r['delivery_type']][$r['status']]['package_value'] = $r['package_value'];
		}

		$status_array = array(
			$this->config->item('trans_status_mobile_delivered'),
			$this->config->item('trans_status_mobile_revoked'),
			$this->config->item('trans_status_mobile_noshow'),
			$this->config->item('trans_status_mobile_rescheduled')
		);

		$type_array = array(
			'COD',
			'CCOD',
			'Delivery Only',
			'PS'
		);

		foreach ($trans as $key => $value) {

			foreach($type_array as $t){

				foreach($status_array as $s){

					if(!isset($trans[$key][$t][$s])){
						$trans[$key][$t][$s]['count'] = 0;
						$trans[$key][$t][$s]['cod_cost'] = 0;
						$trans[$key][$t][$s]['delivery_cost'] = 0;
						$trans[$key][$t][$s]['total_price'] = 0;
						$trans[$key][$t][$s]['package_value'] = 0;
					}

				}

			}
		}


		//print_r($trans);

		$this->table->set_heading(
			'',		 	 	
			'',
			
			array('data'=>'DO','colspan'=>'3'),		
			array('data'=>'COD','colspan'=>'4'),		
			array('data'=>'CCOD','colspan'=>'4'),		
			array('data'=>'PS','colspan'=>'3'),		

			array('data'=>'Status','colspan'=>'3'),		
			
			array('data'=>'Total','colspan'=>'2')	
		); // Setting headings for the table			


		$this->table->set_subheading(
			'No.',		 	 	
			'Date',
			
			'count',
			'dcost',
			'pval',

			'count',
			'dcost',
			'sur',
			'pval',

			'count',
			'dcost',
			'sur',
			'pval',

			'count',
			'pfee',
			'pval',

			'Delivered',
			'No Show',
			'Rescheduled',

			'Delivery Count',
			'Package Value'
		); // Setting headings for the table			

		$counter  = 1;

		$total = array();

		$total['Delivery Only']['count'] = 0;
		$total['Delivery Only']['dcost'] = 0;
		$total['Delivery Only']['pval'] = 0; 
		$total['COD']['count'] = 0; 
		$total['COD']['dcost'] = 0; 
		$total['COD']['sur'] = 0; 
		$total['COD']['pval']  = 0;
		$total['CCOD']['count']  = 0;
		$total['CCOD']['dcost']  = 0;
		$total['CCOD']['sur']  = 0;
		$total['CCOD']['pval']  = 0;
		$total['PS']['count']  = 0;
		$total['PS']['pfee']  = 0;
		$total['PS']['pval']  = 0;
		$total['delivered']['count'] = 0;  
		$total['noshow']['count']  = 0;
		$total['rescheduled']['count']  = 0;
		$total['total_delivery_count']  = 0;
		$total['total_package_value']  = 0;

		foreach($trans as $k=>$v){

			$r = $this->_makerow($v);

			$this->table->add_row(
				$counter,
				date('d-m-Y',strtotime($k)),

				array('data'=>$r['Delivery Only']['count'],'class'=>'count'),
				array('data'=>idr($r['Delivery Only']['dcost']),'class'=>'currency'),		
				array('data'=>idr($r['Delivery Only']['pval']),'class'=>'currency'),		

				array('data'=>$r['COD']['count'],'class'=>'count'),
				array('data'=>idr($r['COD']['dcost']),'class'=>'currency'),
				array('data'=>idr($r['COD']['sur']),'class'=>'currency'),
				array('data'=>idr($r['COD']['pval']),'class'=>'currency'),

				array('data'=>$r['CCOD']['count'],'class'=>'count'),
				array('data'=>idr($r['CCOD']['dcost']),'class'=>'currency'),
				array('data'=>idr($r['CCOD']['sur']),'class'=>'currency'),
				array('data'=>idr($r['CCOD']['pval']),'class'=>'currency'),

				array('data'=>$r['PS']['count'],'class'=>'count'),
				array('data'=>idr($r['PS']['pfee']),'class'=>'currency'),
				array('data'=>idr($r['PS']['pval']),'class'=>'currency'),

				array('data'=>$r['delivered']['count'],'class'=>'count'),
				array('data'=>$r['noshow']['count'],'class'=>'count'),
				array('data'=>$r['rescheduled']['count'],'class'=>'count'),

				array('data'=>$r['total_delivery_count'],'class'=>'count'),
				array('data'=>idr($r['total_package_value']),'class'=>'currency')

			);

				$total['Delivery Only']['count'] +=	$r['Delivery Only']['count'];
				$total['Delivery Only']['dcost'] +=	$r['Delivery Only']['dcost'];
				$total['Delivery Only']['pval'] += $r['Delivery Only']['pval'];
				$total['COD']['count'] += $r['COD']['count'];
				$total['COD']['dcost'] += $r['COD']['dcost'];
				$total['COD']['sur'] +=	$r['COD']['sur'];
				$total['COD']['pval'] += $r['COD']['pval'];
				$total['CCOD']['count'] += $r['CCOD']['count'];
				$total['CCOD']['dcost'] += $r['CCOD']['dcost'];
				$total['CCOD']['sur'] += $r['CCOD']['sur'];
				$total['CCOD']['pval'] += $r['CCOD']['pval'];
				$total['PS']['count'] += $r['PS']['count'];
				$total['PS']['pfee'] +=	$r['PS']['pfee'];
				$total['PS']['pval'] +=	$r['PS']['pval'];
				$total['delivered']['count'] += $r['delivered']['count'];
				$total['noshow']['count'] += $r['noshow']['count'];
				$total['rescheduled']['count'] += $r['rescheduled']['count'];
				$total['total_delivery_count'] += $r['total_delivery_count'];
				$total['total_package_value'] += $r['total_package_value'];

			$counter++;
		}

			$this->table->add_row(
				'',
				array('data'=>'Total','class'=>'total'),		

				array('data'=>$total['Delivery Only']['count'],'class'=>'total count'),
				array('data'=>idr($total['Delivery Only']['dcost']),'class'=>'total currency'),		
				array('data'=>idr($total['Delivery Only']['pval']),'class'=>'total currency'),		

				array('data'=>$total['COD']['count'],'class'=>'total count'),
				array('data'=>idr($total['COD']['dcost']),'class'=>'total currency'),
				array('data'=>idr($total['COD']['sur']),'class'=>'total currency'),
				array('data'=>idr($total['COD']['pval']),'class'=>'total currency'),

				array('data'=>$total['CCOD']['count'],'class'=>'total count'),
				array('data'=>idr($total['CCOD']['dcost']),'class'=>'total currency'),
				array('data'=>idr($total['CCOD']['sur']),'class'=>'total currency'),
				array('data'=>idr($total['CCOD']['pval']),'class'=>'total currency'),

				array('data'=>$total['PS']['count'],'class'=>'total count'),
				array('data'=>idr($total['PS']['pfee']),'class'=>'total currency'),
				array('data'=>idr($total['PS']['pval']),'class'=>'total currency'),

				array('data'=>$total['delivered']['count'],'class'=>'total count'),
				array('data'=>$total['noshow']['count'],'class'=>'total count'),
				array('data'=>$total['rescheduled']['count'],'class'=>'total count'),

				array('data'=>$total['total_delivery_count'],'class'=>'total count'),
				array('data'=>idr($total['total_package_value']),'class'=>'total currency')

			);


		$recontab = $this->table->generate();
		$data['recontab'] = $recontab;

		/* end copy */

		$this->breadcrumb->add_crumb('Merchant Reconciliations','admin/reports/reconciliation');

		$page['ajaxurl'] = 'admin/reports/ajaxreconciliation';
		$page['page_title'] = 'Merchant Reconciliations';

		$data['controller'] = 'admin/reports/merchantrecon/';

		if($pdf == 'pdf'){
			$html = $this->load->view('print/merchantrecon',$data,true);
			$pdf_name = $type.'_'.$to.'_'.$from.'_'.$id;
			pdf_create($html, $pdf_name.'.pdf','A4','landscape', true); 
		}else if($pdf == 'print'){
			$this->load->view('print/merchantrecon',$data); // Load the view
		}else{
			$this->ag_auth->view('merchantrecon',$data); // Load the view
		}		
	}


	private function _makerow($v){

		$r = array();

		$r['COD']['count'] = $v['COD']['delivered']['count'] + $v['COD']['revoked']['count'] + $v['COD']['noshow']['count'] + $v['COD']['rescheduled']['count'];
		$r['CCOD']['count'] = $v['CCOD']['delivered']['count'] + $v['CCOD']['revoked']['count'] + $v['CCOD']['noshow']['count'] + $v['CCOD']['rescheduled']['count'];
		$r['Delivery Only']['count'] = $v['Delivery Only']['delivered']['count'] + $v['Delivery Only']['revoked']['count'] + $v['Delivery Only']['noshow']['count'] + $v['Delivery Only']['rescheduled']['count'];
		$r['PS']['count'] = $v['PS']['delivered']['count'] + $v['PS']['revoked']['count'] + $v['PS']['noshow']['count'] + $v['PS']['rescheduled']['count'];

		$r['COD']['dcost'] = $v['COD']['delivered']['delivery_cost'] + $v['COD']['revoked']['delivery_cost'] + $v['COD']['noshow']['delivery_cost'] + $v['COD']['rescheduled']['delivery_cost'];
		$r['CCOD']['dcost'] = $v['CCOD']['delivered']['delivery_cost'] + $v['CCOD']['revoked']['delivery_cost'] + $v['CCOD']['noshow']['delivery_cost'] + $v['CCOD']['rescheduled']['delivery_cost'];
		
		$r['COD']['sur'] = $v['COD']['delivered']['cod_cost'] + $v['COD']['revoked']['cod_cost'] + $v['COD']['noshow']['cod_cost'] + $v['COD']['rescheduled']['cod_cost'];
		$r['CCOD']['sur'] = $v['CCOD']['delivered']['cod_cost'] + $v['CCOD']['revoked']['cod_cost'] + $v['CCOD']['noshow']['cod_cost'] + $v['CCOD']['rescheduled']['cod_cost'];
		$r['COD']['pval'] = $v['COD']['delivered']['package_value'] + $v['COD']['revoked']['package_value'] + $v['COD']['noshow']['package_value'] + $v['COD']['rescheduled']['package_value'];
		$r['CCOD']['pval'] = $v['CCOD']['delivered']['package_value'] + $v['CCOD']['revoked']['package_value'] + $v['CCOD']['noshow']['package_value'] + $v['CCOD']['rescheduled']['package_value'];

		$r['Delivery Only']['pval'] = $v['Delivery Only']['delivered']['package_value'] + $v['Delivery Only']['revoked']['package_value'] + $v['Delivery Only']['noshow']['package_value'] + $v['Delivery Only']['rescheduled']['package_value'];
		$r['PS']['pval'] = $v['PS']['delivered']['package_value'] + $v['PS']['revoked']['package_value'] + $v['PS']['noshow']['package_value'] + $v['PS']['rescheduled']['package_value'];

		
		$r['Delivery Only']['dcost'] = $v['Delivery Only']['delivered']['delivery_cost'] + $v['Delivery Only']['revoked']['delivery_cost'] + $v['Delivery Only']['noshow']['delivery_cost'] + $v['Delivery Only']['rescheduled']['delivery_cost'];
		$r['PS']['pfee'] = $v['PS']['delivered']['delivery_cost'] + $v['PS']['revoked']['delivery_cost'] + $v['PS']['noshow']['delivery_cost'] + $v['PS']['rescheduled']['delivery_cost'];

		$r['delivered']['count'] = $v['COD']['delivered']['count'] + $v['CCOD']['delivered']['count'] + $v['Delivery Only']['delivered']['count'] + $v['PS']['delivered']['count'];
		$r['noshow']['count'] = $v['COD']['noshow']['count'] + $v['CCOD']['noshow']['count'] + $v['Delivery Only']['noshow']['count'] + $v['PS']['noshow']['count'];
		$r['revoked']['count'] = $v['COD']['revoked']['count'] + $v['CCOD']['revoked']['count'] + $v['Delivery Only']['revoked']['count'] + $v['PS']['revoked']['count'];
		$r['rescheduled']['count'] = $v['COD']['rescheduled']['count'] + $v['CCOD']['rescheduled']['count'] + $v['Delivery Only']['rescheduled']['count'] + $v['PS']['rescheduled']['count'];

		$r['total_delivery_count'] = $r['delivered']['count'] + $r['noshow']['count'] + $r['revoked']['count'] + $r['rescheduled']['count'];
		$r['total_package_value'] = $r['Delivery Only']['pval'] + $r['COD']['pval'] + $r['CCOD']['pval'] + $r['PS']['pval'];
		return $r;
	}

	public function courierrecon($type = null,$year = null, $scope = null, $par1 = null, $par2 = null, $par3 = null){

		$type = (is_null($type))?'Global':$type;
		$id = (is_null($type))?'noid':$type;

		if(is_null($scope)){
			$id = 'noid';
			$scope = 'month';
			$year = date('Y',time());
			$par1 = date('m',time());
		}		

		$pdf = null;

		if($scope == 'month'){
			$days = cal_days_in_month(CAL_GREGORIAN, $par1, $year);
			$from =	date('Y-m-d', strtotime($year.'/'.$par1.'/1'));
			$to =	date('Y-m-d', strtotime($year.'/'.$par1.'/'.$days));
			$pdf = $par2;

			$data['month'] = $par1;
			$data['week'] = 1;
		}else if($scope == 'week'){
			$from =	date('Y-m-d', strtotime('1 Jan '.$year.' +'.($par1 - 1).' weeks'));
			$to = date('Y-m-d', strtotime('1 Jan '.$year.' +'.$par1.' weeks - 1 day'));
			$pdf = $par2;

			$data['month'] = 1;
			$data['week'] = $par1;
		}else if($scope == 'date'){
			$from = $par1;
			$to = $par2;
			$pdf = $par3;

			$data['month'] = 1;
			$data['week'] = 1;
		}else{
			$from = date('Y-m-d',time());
			$to = date('Y-m-d',time());
			$pdf = null;			

			$data['month'] = 1;
			$data['week'] = 1;
		}

		$data['year'] = $year;
		$data['from'] = $from;
		$data['to'] = $to;

		$clist = get_courier(null,false);

		$cs = array('noid'=>'All');
		foreach ($clist as $ckey) {
			$cs[$ckey['id']] = $ckey['fullname'];	
		}

		$data['couriers'] = $cs;
		$data['id'] = $id;

		/* copied from print controller */

		$this->load->library('number_words');

		if($id == 'noid'){
			$data['type_name'] = '-';
			$data['bank_account'] = 'n/a';
		}else{
			$user = $this->db->where('id',$id)->get($this->config->item('jayon_couriers_table'))->row();
			//print $this->db->last_query();
			$data['type_name'] = $user->fullname;
			$data['bank_account'] = 'n/a';
		}

		if($id == 'noid'){
			$data['type'] = 'Global';
		}else{
			$cr = get_courier($id,false);
			$data['type'] = $cr['fullname'];
		}
		$data['period'] = $from.' s/d '.$to;

		$sfrom = date('Y-m-d',strtotime($from));
		$sto = date('Y-m-d',strtotime($to));

		$this->db->distinct();
		$this->db->select('assignment_date,status,count(*) as count');
		$this->db->from($this->config->item('delivered_delivery_table'));

		$column = 'assignment_date';
		$daterange = sprintf("`%s`between '%s%%' and '%s%%' ", $column, $sfrom, $sto);

		$this->db->where($daterange, null, false);
		$this->db->where($column.' != ','0000-00-00');

		if($id != 'noid'){
			$this->db->where($this->config->item('delivered_delivery_table').'.courier_id',$id);
		}

		$this->db->and_();
			$this->db->group_start();
				$this->db->where('status',$this->config->item('trans_status_mobile_delivered'));
				$this->db->or_where('status',$this->config->item('trans_status_mobile_revoked'));
				$this->db->or_where('status',$this->config->item('trans_status_mobile_noshow'));
				$this->db->or_where('status',$this->config->item('trans_status_mobile_rescheduled'));
			$this->db->group_end();

		$this->db->group_by('assignment_date');

		$rows = $this->db->get();

		//print $this->db->last_query();

		$this->table->set_heading(
			array('data'=>'Delivery Details',
				'colspan'=>'4'
			)	
		);


		$this->table->set_heading(
			'No.',		 	 	
			'Date',	 	 	 	 	 	 	 
			'Delivered',
			'No Show',
			'Rescheduled',
			'Delivery Count'
		); // Setting headings for the table			

		$seq = 0;
		$rowdate = '';
		$tarray = array();
		foreach($rows->result() as $r){
			if($rowdate == ''){
				$rowdate = $r->assignment_date;
			}
			if($r->assignment_date != $rowdate){
				$seq++;
				$rowdate = $r->assignment_date;
			}

			//print $seq.' '.$r->assignment_date.' '.$rowdate.' '.$r->count."\r\n";
			$tarray[$seq]['assignment_date'] = $r->assignment_date;
			$tarray[$seq][$r->status] = $r->count;


		}

		$seq = 1;
		$aseq = 0;


		$tdl = 0;
		$tns = 0;
		$trs = 0;

		foreach ($tarray as $r) {

			$dl = (isset($r['delivered']))?$r['delivered']:0;
			$ns = (isset($r['noshow']))?$r['noshow']:0;
			$rs = (isset($r['rescheduled']))?$r['rescheduled']:0;

			$tdl += $dl;
			$tns += $ns;
			$trs += $rs;

			$this->table->add_row(
				$seq,		
				date('d M Y',strtotime($r['assignment_date'])),		
				$dl,
				$ns,
				$rs,
				$dl + $ns + $rs
			);				

			$seq++;
			$aseq++;
		}

		$gt = $tdl + $tns + $trs;

		$this->table->add_row(
			'',		
			array('data'=>'Total','style'=>'border-top:thin solid grey'),		
			array('data'=>$tdl,'style'=>'border-top:thin solid grey'),		
			array('data'=>$tns,'style'=>'border-top:thin solid grey'),		
			array('data'=>$trs,'style'=>'border-top:thin solid grey'),		
			array('data'=>$gt,'style'=>'border-top:thin solid grey')		
		);				

		/*
		if($type == 'Merchant' || $type == 'Global'){
			$total_span = 10;
			$say_span = 12;

		}else if($type == 'Courier'){
			$total_span = 7;
			$say_span = 9;
		}


		$this->table->add_row(
			array('data'=>'Total','colspan'=>$total_span),
			number_format($total_delivery,2,',','.'),
			number_format($total_cod,2,',','.'),
			number_format($total_billing,2,',','.')
		);

		$this->table->add_row(
			'Terbilang',
			array('data'=>'&nbsp;','colspan'=>$say_span)
		);

		if($type == 'Merchant' || $type == 'Global'){
			$this->table->add_row(
				'Payable',
				array('data'=>$this->number_words->to_words($total_billing).' rupiah',
					'colspan'=>$say_span)
			);
		}

		$this->table->add_row(
			'Delivery Charge',
			array('data'=>$this->number_words->to_words($total_delivery).' rupiah',
				'colspan'=>$say_span)
		);

		$this->table->add_row(
			'COD Surcharge',
			array('data'=>$this->number_words->to_words($total_cod).' rupiah',
				'colspan'=>$say_span)
		);

		*/

		$recontab = $this->table->generate();
		$data['recontab'] = $recontab;

		/* end copy */

		$this->breadcrumb->add_crumb('Courier Reconciliations','admin/reports/reconciliation');

		$page['ajaxurl'] = 'admin/reports/ajaxreconciliation';
		$page['page_title'] = 'Courier Reconciliations';

		$data['controller'] = 'admin/reports/courierrecon/';

		if($pdf == 'pdf'){
			$html = $this->load->view('print/courierrecon',$data,true);
			$pdf_name = $type.'_'.$to.'_'.$from.'_'.$id;
			pdf_create($html, $pdf_name.'.pdf','A4','landscape', true); 
		}else if($pdf == 'print'){
			$this->load->view('print/courierrecon',$data); // Load the view
		}else{
			$this->ag_auth->view('courierrecon',$data); // Load the view
		}		
	}


	public function devicerecon($type = null,$year = null, $scope = null, $par1 = null, $par2 = null, $par3 = null){

		$type = (is_null($type))?'Global':$type;
		$id = (is_null($type))?'noid':$type;

		if(is_null($scope)){
			$id = 'noid';
			$scope = 'month';
			$year = date('Y',time());
			$par1 = date('m',time());
		}		

		$pdf = null;

		if($scope == 'month'){
			$days = cal_days_in_month(CAL_GREGORIAN, $par1, $year);
			$from =	date('Y-m-d', strtotime($year.'/'.$par1.'/1'));
			$to =	date('Y-m-d', strtotime($year.'/'.$par1.'/'.$days));
			$pdf = $par2;

			$data['month'] = $par1;
			$data['week'] = 1;
		}else if($scope == 'week'){
			$from =	date('Y-m-d', strtotime('1 Jan '.$year.' +'.($par1 - 1).' weeks'));
			$to = date('Y-m-d', strtotime('1 Jan '.$year.' +'.$par1.' weeks - 1 day'));
			$pdf = $par2;

			$data['month'] = 1;
			$data['week'] = $par1;
		}else if($scope == 'date'){
			$from = $par1;
			$to = $par2;
			$pdf = $par3;

			$data['month'] = 1;
			$data['week'] = 1;
		}else{
			$from = date('Y-m-d',time());
			$to = date('Y-m-d',time());
			$pdf = null;			

			$data['month'] = 1;
			$data['week'] = 1;
		}

		$data['year'] = $year;
		$data['from'] = $from;
		$data['to'] = $to;

		$clist = get_courier(null,false);

		$clist = $this->db->where('is_on',1)->get('devices')->result_array();

		$cs = array('noid'=>'All');
		foreach ($clist as $ckey) {
			$cs[$ckey['id']] = $ckey['identifier'];	
		}

		$data['devices'] = $cs;
		$data['id'] = $id;
		//$data['couriers'] = get_courier();

		/* copied from print controller */

		$this->load->library('number_words');

		if($id == 'noid'){
			$data['type_name'] = '-';
			$data['bank_account'] = 'n/a';
		}else{
			$user = $this->db->where('id',$id)->get($this->config->item('jayon_devices_table'))->row();
			//print $this->db->last_query();
			$data['type_name'] = $user->identifier;
			$data['bank_account'] = 'n/a';
		}

		if($id == 'noid'){
			$data['type'] = 'Global';
		}else{
			$dev = $this->db->where('id',$id)->get('devices')->row();
			$data['type'] = $dev->identifier;
		}
		$data['period'] = $from.' s/d '.$to;

		$sfrom = date('Y-m-d',strtotime($from));
		$sto = date('Y-m-d',strtotime($to));

		$this->db->distinct();
		$this->db->select('assignment_date,status,count(*) as count');
		$this->db->from($this->config->item('delivered_delivery_table'));

		$column = 'assignment_date';
		$daterange = sprintf("`%s`between '%s%%' and '%s%%' ", $column, $sfrom, $sto);

		$this->db->where($daterange, null, false);
		$this->db->where($column.' != ','0000-00-00');

		if($id != 'noid'){
			$this->db->where($this->config->item('delivered_delivery_table').'.device_id',$id);
		}

		$this->db->and_();
			$this->db->group_start();
				$this->db->where('status',$this->config->item('trans_status_mobile_delivered'));
				$this->db->or_where('status',$this->config->item('trans_status_mobile_revoked'));
				$this->db->or_where('status',$this->config->item('trans_status_mobile_noshow'));
				$this->db->or_where('status',$this->config->item('trans_status_mobile_rescheduled'));
			$this->db->group_end();

		$this->db->group_by('assignment_date');

		$rows = $this->db->get();

		//print $this->db->last_query();

		$this->table->set_heading(
			array('data'=>'Delivery Details',
				'colspan'=>'4'
			)	
		);


		$this->table->set_heading(
			'No.',		 	 	
			'Date',	 	 	 	 	 	 	 
			'Delivered',
			'No Show',
			'Rescheduled',
			'Delivery Count'
		); // Setting headings for the table			

		$seq = 0;
		$rowdate = '';
		$tarray = array();
		foreach($rows->result() as $r){
			if($rowdate == ''){
				$rowdate = $r->assignment_date;
			}
			if($r->assignment_date != $rowdate){
				$seq++;
				$rowdate = $r->assignment_date;
			}

			//print $seq.' '.$r->assignment_date.' '.$rowdate.' '.$r->count."\r\n";
			$tarray[$seq]['assignment_date'] = $r->assignment_date;
			$tarray[$seq][$r->status] = $r->count;


		}

		$seq = 1;
		$aseq = 0;


		$tdl = 0;
		$tns = 0;
		$trs = 0;

		foreach ($tarray as $r) {

			$dl = (isset($r['delivered']))?$r['delivered']:0;
			$ns = (isset($r['noshow']))?$r['noshow']:0;
			$rs = (isset($r['rescheduled']))?$r['rescheduled']:0;

			$tdl += $dl;
			$tns += $ns;
			$trs += $rs;

			$this->table->add_row(
				$seq,		
				date('d M Y',strtotime($r['assignment_date'])),		
				$dl,
				$ns,
				$rs,
				$dl + $ns + $rs
			);				

			$seq++;
			$aseq++;
		}

		$gt = $tdl + $tns + $trs;

		$this->table->add_row(
			'',		
			array('data'=>'Total','style'=>'border-top:thin solid grey'),		
			array('data'=>$tdl,'style'=>'border-top:thin solid grey'),		
			array('data'=>$tns,'style'=>'border-top:thin solid grey'),		
			array('data'=>$trs,'style'=>'border-top:thin solid grey'),		
			array('data'=>$gt,'style'=>'border-top:thin solid grey')		
		);				

		$recontab = $this->table->generate();
		$data['recontab'] = $recontab;

		/* end copy */

		$this->breadcrumb->add_crumb('Device Reconciliations','admin/reports/devicerecon');

		$page['ajaxurl'] = 'admin/reports/ajaxreconciliation';
		$page['page_title'] = 'Device Reconciliations';

		$data['controller'] = 'admin/reports/devicerecon/';

		if($pdf == 'pdf'){
			$html = $this->load->view('print/devicerecon',$data,true);
			$pdf_name = $type.'_'.$to.'_'.$from.'_'.$id;
			pdf_create($html, $pdf_name.'.pdf','A4','landscape', true); 
		}else if($pdf == 'print'){
			$this->load->view('print/devicerecon',$data); // Load the view
		}else{
			$this->ag_auth->view('devicerecon',$data); // Load the view
		}		
	}

	public function _devicerecon($type = null,$year = null, $scope = null, $par1 = null, $par2 = null, $par3 = null){

		$type = (is_null($type))?'Global':$type;
		$id = (is_null($type))?'noid':$type;

		$pdf = null;

		if($scope == 'month'){
			$days = cal_days_in_month(CAL_GREGORIAN, $par1, $year);
			$from =	date('Y-m-d', strtotime($year.'/'.$par1.'/1'));
			$to =	date('Y-m-d', strtotime($year.'/'.$par1.'/'.$days));
			$pdf = $par2;

			$data['month'] = $par1;
			$data['week'] = 1;
		}else if($scope == 'week'){
			$from =	date('Y-m-d', strtotime('1 Jan '.$year.' +'.($par1 - 1).' weeks'));
			$to = date('Y-m-d', strtotime('1 Jan '.$year.' +'.$par1.' weeks - 1 day'));
			$pdf = $par2;

			$data['month'] = 1;
			$data['week'] = $par1;
		}else if($scope == 'date'){
			$from = $par1;
			$to = $par2;
			$pdf = $par3;

			$data['month'] = 1;
			$data['week'] = 1;
		}else{
			$from = date('Y-m-d',time());
			$to = date('Y-m-d',time());
			$pdf = null;			

			$data['month'] = 1;
			$data['week'] = 1;
		}

		$data['year'] = $year;
		$data['from'] = $from;
		$data['to'] = $to;

		$data['devices'] = array_merge(array('Global'=>'All'),get_device());

		/* copied from print controller */

		$this->load->library('number_words');

		if($id == 'noid'){
			$data['type_name'] = '-';
			$data['bank_account'] = 'n/a';
		}else{
			if($type == 'Merchant'){
				$user = $this->db->where('id',$id)->get($this->config->item('jayon_members_table'))->row();
				$data['type_name'] = $user->fullname;
				$data['bank_account'] = ($user->account_number == '')?'n/a':$user->bank.' - '.$user->account_number.' - '.$user->account_name;
			}else if($type == 'Courier'){
				$user = $this->db->where('id',$id)->get($this->config->item('jayon_couriers_table'))->row();
				$data['type_name'] = $user->fullname;
				$data['bank_account'] = 'n/a';
			}
		}

		$data['type'] = $type;
		$data['period'] = $from.' s/d '.$to;

		$sfrom = date('Y-m-d',strtotime($from));
		$sto = date('Y-m-d',strtotime($to));

		$this->db->select($this->config->item('delivered_delivery_table').'.*,b.fullname as buyer,m.merchantname as merchant,a.domain as domain,a.application_name as app_name,d.identifier as device,c.fullname as courier');
		$this->db->from($this->config->item('delivered_delivery_table'));
		$this->db->join('members as b',$this->config->item('assigned_delivery_table').'.buyer_id=b.id','left');
		$this->db->join('members as m',$this->config->item('assigned_delivery_table').'.merchant_id=m.id','left');
		$this->db->join('applications as a',$this->config->item('assigned_delivery_table').'.application_id=a.id','left');
		$this->db->join('devices as d',$this->config->item('assigned_delivery_table').'.device_id=d.id','left');
		$this->db->join('couriers as c',$this->config->item('assigned_delivery_table').'.courier_id=c.id','left');


		$column = 'assignment_date';
		$daterange = sprintf("`%s`between '%s%%' and '%s%%' ", $column, $sfrom, $sto);

		$this->db->where($daterange, null, false);
		$this->db->where($column.' != ','0000-00-00');

		if($id != 'noid'){
			if($type == 'Merchant'){
				$this->db->where($this->config->item('delivered_delivery_table').'.merchant_id',$id);
			}else if($type == 'Courier'){
				$this->db->where($this->config->item('delivered_delivery_table').'.courier_id',$id);
			}
		}

		$this->db->and_();
		$this->db->group_start();
		$this->db->where('status',$this->config->item('trans_status_mobile_delivered'));
		//$this->db->or_where('status',$this->config->item('trans_status_mobile_revoked'));
		//$this->db->or_where('status',$this->config->item('trans_status_mobile_noshow'));
		//$this->db->or_where('status',$this->config->item('trans_status_mobile_rescheduled'));
		$this->db->group_end();

		$rows = $this->db->get();

		//print $this->db->last_query();

		$this->table->set_heading(
			array('data'=>'Delivery Details',
				'colspan'=>'13'
			)	
		);


		if($type == 'Merchant' || $type == 'Global'){
			$this->table->set_heading(
				'No.',		 	 	
				'Merchant Trans ID',	 	 	 	 	 	 	 
				'Delivery ID',
				'Merchant Name',
				'Store',
				'Delivery Date',
				'Status',
				'Goods Price',
				'Disc',
				'Tax',
				'Delivery Chg',
				'COD Surchg',		
				'Payable Value'		
			); // Setting headings for the table

		}else if($type == 'Courier'){
			$this->table->set_heading(
				'No.',		 	 	
				'Merchant Trans ID',	 	 	 	 	 	 	 
				'Delivery ID',
				'Merchant Name',
				'Store',
				'Delivery Date',
				'Status'
				//'Delivery Chg',
				//'COD Surchg',		
				//'Payable Value'		
			); // Setting headings for the table			
		}


		$seq = 1;
		$total_billing = 0;
		$total_delivery = 0;
		$total_cod = 0;

		//print_r($rows->result());

		foreach($rows->result() as $r){

			$total = str_replace(array(',','.'), '', $r->total_price);
			$dsc = str_replace(array(',','.'), '', $r->total_discount);
			$tax = str_replace(array(',','.'), '',$r->total_tax);
			$dc = str_replace(array(',','.'), '',$r->delivery_cost);
			$cod = str_replace(array(',','.'), '',$r->cod_cost);

			$total = (int)$total;
			$dsc = (int)$dsc;
			$tax = (int)$tax;
			$dc = (int)$dc;
			$cod = (int)$cod;

			$payable = 0;


			if($r->status == $this->config->item('trans_status_mobile_delivered')){
				if($type == 'Merchant' || $type == 'Global'){
					$payable = ($total - $dsc) + $tax;
					// + $dc + $cod;				
				}else if($type == 'Courier'){
					$payable = ($dc + $cod) * 0.1;
				}
				$total_billing += (int)str_replace('.','',$payable);
			}else if(
				$r->status == $this->config->item('trans_status_mobile_revoked') ||
				$r->status == $this->config->item('trans_status_mobile_rescheduled') ||
				$r->status == $this->config->item('trans_status_mobile_noshow'))
			{
				//TBA	
			}

			$total_delivery += (int)str_replace('.','',$dc);
			$total_cod += (int)str_replace('.','',$cod);

			if($type == 'Merchant' || $type == 'Global'){
				$this->table->add_row(
					$seq,		
					$r->merchant_trans_id,		
					$r->delivery_id,
					$r->merchant,
					$r->app_name.'<hr />'.$r->domain,
					$r->assignment_date,
					$r->status,
					number_format((int)str_replace('.','',$total),2,',','.'),
					number_format((int)str_replace('.','',$dsc),2,',','.'),
					number_format((int)str_replace('.','',$tax),2,',','.'),
					number_format((int)str_replace('.','',$dc),2,',','.'),			
					number_format((int)str_replace('.','',$cod),2,',','.'),
					number_format((int)str_replace('.','',$payable),2,',','.')
				);

			}else if($type == 'Courier'){
				$this->table->add_row(
					$seq,		
					$r->merchant_trans_id,		
					$r->delivery_id,
					$r->merchant,
					$r->app_name.'<hr />'.$r->domain,
					$r->assignment_date,
					$r->status
					//number_format((int)str_replace('.','',$dc),2,',','.'),			
					//number_format((int)str_replace('.','',$cod),2,',','.'),
					//number_format((int)str_replace('.','',$payable),2,',','.')
				);				
			}

			$seq++;
		}

		if($type == 'Merchant' || $type == 'Global'){
			$total_span = 10;
			$say_span = 12;

		}else if($type == 'Courier'){
			$total_span = 7;
			$say_span = 9;
		}


		$this->table->add_row(
			array('data'=>'Total','colspan'=>$total_span),
			number_format($total_delivery,2,',','.'),
			number_format($total_cod,2,',','.'),
			number_format($total_billing,2,',','.')
		);

		$this->table->add_row(
			'Terbilang',
			array('data'=>'&nbsp;','colspan'=>$say_span)
		);

		if($type == 'Merchant' || $type == 'Global'){
			$this->table->add_row(
				'Payable',
				array('data'=>$this->number_words->to_words($total_billing).' rupiah',
					'colspan'=>$say_span)
			);
		}

		$this->table->add_row(
			'Delivery Charge',
			array('data'=>$this->number_words->to_words($total_delivery).' rupiah',
				'colspan'=>$say_span)
		);

		$this->table->add_row(
			'COD Surcharge',
			array('data'=>$this->number_words->to_words($total_cod).' rupiah',
				'colspan'=>$say_span)
		);

		$recontab = $this->table->generate();
		$data['recontab'] = $recontab;

		/* end copy */

		$this->breadcrumb->add_crumb('Device Reconciliations','admin/reports/reconciliation');

		$page['ajaxurl'] = 'admin/reports/ajaxreconciliation';
		$page['page_title'] = 'Device Reconciliations';

		$data['controller'] = 'admin/reports/devicerecon/';

		if($pdf == 'pdf'){
			$html = $this->load->view('print/devicerecon',$data,true);
			$pdf_name = $type.'_'.$to.'_'.$from.'_'.$id;
			pdf_create($html, $pdf_name.'.pdf','A4','landscape', true); 
		}else if($pdf == 'print'){
			$this->load->view('print/devicerecon',$data); // Load the view
		}else{
			$this->ag_auth->view('devicerecon',$data); // Load the view
		}		
	}

	public function _merchantrecon($type = null,$year = null, $scope = null, $par1 = null, $par2 = null, $par3 = null){

		$type = (is_null($type))?'Global':$type;
		$id = (is_null($type))?'noid':$type;

		$pdf = null;

		if($scope == 'month'){
			$days = cal_days_in_month(CAL_GREGORIAN, $par1, $year);
			$from =	date('Y-m-d', strtotime($year.'/'.$par1.'/1'));
			$to =	date('Y-m-d', strtotime($year.'/'.$par1.'/'.$days));
			$pdf = $par2;

			$data['month'] = $par1;
			$data['week'] = 1;
		}else if($scope == 'week'){
			$from =	date('Y-m-d', strtotime('1 Jan '.$year.' +'.($par1 - 1).' weeks'));
			$to = date('Y-m-d', strtotime('1 Jan '.$year.' +'.$par1.' weeks - 1 day'));
			$pdf = $par2;

			$data['month'] = 1;
			$data['week'] = $par1;
		}else if($scope == 'date'){
			$from = $par1;
			$to = $par2;
			$pdf = $par3;

			$data['month'] = 1;
			$data['week'] = 1;
		}else{
			$from = date('Y-m-d',time());
			$to = date('Y-m-d',time());
			$pdf = null;			

			$data['month'] = 1;
			$data['week'] = 1;
		}

		$data['year'] = $year;
		$data['from'] = $from;
		$data['to'] = $to;

		$data['merchants'] = array_merge(array('Global'=>'All'),get_merchant());

		/* copied from print controller */

		$this->load->library('number_words');

		if($id == 'noid'){
			$data['type_name'] = '-';
			$data['bank_account'] = 'n/a';
		}else{
			if($type == 'Merchant'){
				$user = $this->db->where('id',$id)->get($this->config->item('jayon_members_table'))->row();
				$data['type_name'] = $user->fullname;
				$data['bank_account'] = ($user->account_number == '')?'n/a':$user->bank.' - '.$user->account_number.' - '.$user->account_name;
			}else if($type == 'Courier'){
				$user = $this->db->where('id',$id)->get($this->config->item('jayon_couriers_table'))->row();
				$data['type_name'] = $user->fullname;
				$data['bank_account'] = 'n/a';
			}
		}

		$data['type'] = $type;
		$data['period'] = $from.' s/d '.$to;

		$sfrom = date('Y-m-d',strtotime($from));
		$sto = date('Y-m-d',strtotime($to));

		$this->db->select($this->config->item('delivered_delivery_table').'.*,b.fullname as buyer,m.merchantname as merchant,a.domain as domain,a.application_name as app_name,d.identifier as device,c.fullname as courier');
		$this->db->from($this->config->item('delivered_delivery_table'));
		$this->db->join('members as b',$this->config->item('assigned_delivery_table').'.buyer_id=b.id','left');
		$this->db->join('members as m',$this->config->item('assigned_delivery_table').'.merchant_id=m.id','left');
		$this->db->join('applications as a',$this->config->item('assigned_delivery_table').'.application_id=a.id','left');
		$this->db->join('devices as d',$this->config->item('assigned_delivery_table').'.device_id=d.id','left');
		$this->db->join('couriers as c',$this->config->item('assigned_delivery_table').'.courier_id=c.id','left');


		$column = 'assignment_date';
		$daterange = sprintf("`%s`between '%s%%' and '%s%%' ", $column, $sfrom, $sto);

		$this->db->where($daterange, null, false);
		$this->db->where($column.' != ','0000-00-00');

		if($id != 'noid'){
			if($type == 'Merchant'){
				$this->db->where($this->config->item('delivered_delivery_table').'.merchant_id',$id);
			}else if($type == 'Courier'){
				$this->db->where($this->config->item('delivered_delivery_table').'.courier_id',$id);
			}
		}

		$this->db->and_();
		$this->db->group_start();
		$this->db->where('status',$this->config->item('trans_status_mobile_delivered'));
		//$this->db->or_where('status',$this->config->item('trans_status_mobile_revoked'));
		//$this->db->or_where('status',$this->config->item('trans_status_mobile_noshow'));
		//$this->db->or_where('status',$this->config->item('trans_status_mobile_rescheduled'));
		$this->db->group_end();

		$rows = $this->db->get();

		//print $this->db->last_query();

		$this->table->set_heading(
			array('data'=>'Delivery Details',
				'colspan'=>'13'
			)	
		);


		if($type == 'Merchant' || $type == 'Global'){
			$this->table->set_heading(
				'No.',		 	 	
				'Merchant Trans ID',	 	 	 	 	 	 	 
				'Delivery ID',
				'Merchant Name',
				'Store',
				'Delivery Date',
				'Status',
				'Goods Price',
				'Disc',
				'Tax',
				'Delivery Chg',
				'COD Surchg',		
				'Payable Value'		
			); // Setting headings for the table

		}else if($type == 'Courier'){
			$this->table->set_heading(
				'No.',		 	 	
				'Merchant Trans ID',	 	 	 	 	 	 	 
				'Delivery ID',
				'Merchant Name',
				'Store',
				'Delivery Date',
				'Status'
				//'Delivery Chg',
				//'COD Surchg',		
				//'Payable Value'		
			); // Setting headings for the table			
		}


		$seq = 1;
		$total_billing = 0;
		$total_delivery = 0;
		$total_cod = 0;

		//print_r($rows->result());

		foreach($rows->result() as $r){

			$total = str_replace(array(',','.'), '', $r->total_price);
			$dsc = str_replace(array(',','.'), '', $r->total_discount);
			$tax = str_replace(array(',','.'), '',$r->total_tax);
			$dc = str_replace(array(',','.'), '',$r->delivery_cost);
			$cod = str_replace(array(',','.'), '',$r->cod_cost);

			$total = (int)$total;
			$dsc = (int)$dsc;
			$tax = (int)$tax;
			$dc = (int)$dc;
			$cod = (int)$cod;

			$payable = 0;


			if($r->status == $this->config->item('trans_status_mobile_delivered')){
				if($type == 'Merchant' || $type == 'Global'){
					$payable = ($total - $dsc) + $tax;
					// + $dc + $cod;				
				}else if($type == 'Courier'){
					$payable = ($dc + $cod) * 0.1;
				}
				$total_billing += (int)str_replace('.','',$payable);
			}else if(
				$r->status == $this->config->item('trans_status_mobile_revoked') ||
				$r->status == $this->config->item('trans_status_mobile_rescheduled') ||
				$r->status == $this->config->item('trans_status_mobile_noshow'))
			{
				//TBA	
			}

			$total_delivery += (int)str_replace('.','',$dc);
			$total_cod += (int)str_replace('.','',$cod);

			if($type == 'Merchant' || $type == 'Global'){
				$this->table->add_row(
					$seq,		
					$r->merchant_trans_id,		
					$r->delivery_id,
					$r->merchant,
					$r->app_name.'<hr />'.$r->domain,
					$r->assignment_date,
					$r->status,
					number_format((int)str_replace('.','',$total),2,',','.'),
					number_format((int)str_replace('.','',$dsc),2,',','.'),
					number_format((int)str_replace('.','',$tax),2,',','.'),
					number_format((int)str_replace('.','',$dc),2,',','.'),			
					number_format((int)str_replace('.','',$cod),2,',','.'),
					number_format((int)str_replace('.','',$payable),2,',','.')
				);

			}else if($type == 'Courier'){
				$this->table->add_row(
					$seq,		
					$r->merchant_trans_id,		
					$r->delivery_id,
					$r->merchant,
					$r->app_name.'<hr />'.$r->domain,
					$r->assignment_date,
					$r->status
					//number_format((int)str_replace('.','',$dc),2,',','.'),			
					//number_format((int)str_replace('.','',$cod),2,',','.'),
					//number_format((int)str_replace('.','',$payable),2,',','.')
				);				
			}

			$seq++;
		}

		if($type == 'Merchant' || $type == 'Global'){
			$total_span = 10;
			$say_span = 12;

		}else if($type == 'Courier'){
			$total_span = 7;
			$say_span = 9;
		}


		$this->table->add_row(
			array('data'=>'Total','colspan'=>$total_span),
			number_format($total_delivery,2,',','.'),
			number_format($total_cod,2,',','.'),
			number_format($total_billing,2,',','.')
		);

		$this->table->add_row(
			'Terbilang',
			array('data'=>'&nbsp;','colspan'=>$say_span)
		);

		if($type == 'Merchant' || $type == 'Global'){
			$this->table->add_row(
				'Payable',
				array('data'=>$this->number_words->to_words($total_billing).' rupiah',
					'colspan'=>$say_span)
			);
		}

		$this->table->add_row(
			'Delivery Charge',
			array('data'=>$this->number_words->to_words($total_delivery).' rupiah',
				'colspan'=>$say_span)
		);

		$this->table->add_row(
			'COD Surcharge',
			array('data'=>$this->number_words->to_words($total_cod).' rupiah',
				'colspan'=>$say_span)
		);

		$recontab = $this->table->generate();
		$data['recontab'] = $recontab;

		/* end copy */

		$this->breadcrumb->add_crumb('Merchant Reconciliations','admin/reports/reconciliation');

		$page['ajaxurl'] = 'admin/reports/ajaxreconciliation';
		$page['page_title'] = 'Merchant Reconciliations';

		$data['controller'] = 'admin/reports/merchantrecon/';

		if($pdf == 'pdf'){
			$html = $this->load->view('print/merchantrecon',$data,true);
			$pdf_name = $type.'_'.$to.'_'.$from.'_'.$id;
			pdf_create($html, $pdf_name.'.pdf','A4','landscape', true); 
		}else if($pdf == 'print'){
			$this->load->view('print/merchantrecon',$data); // Load the view
		}else{
			$this->ag_auth->view('merchantrecon',$data); // Load the view
		}		
	}


	public function merchants($from,$to){
		$this->breadcrumb->add_crumb('Reconciliations','admin/reports/reconciliation');
		$this->breadcrumb->add_crumb('Merchants '.$from.' - '.$to,'admin/reports/merchants');

		$this->table->set_heading(
			 	'Merchant Name',
			 	'Email',
			 	'Full Name',
			 	'Mobile',
			 	'Phone',
			 	'Joined Since',
			 	'Action'
			); // Setting headings for the table

		$page['ajaxurl'] = 'admin/reports/ajaxmerchants/'.$from.'/'.$to;
		$page['page_title'] = 'Merchants Reconciliations';
		$this->ag_auth->view('reconajaxlistview',$page); // Load the view

	}

	public function ajaxmerchants($from,$to){

		$limit_count = $this->input->post('iDisplayLength');
		$limit_offset = $this->input->post('iDisplayStart');

		$sort_col = $this->input->post('iSortCol_0');
		$sort_dir = $this->input->post('sSortDir_0');

		$group_id = user_group_id('merchant');

		$columns = array(
			'email',
			//'password',
			//'merchantname',
			'fullname',
			'street',
			'district',
			'city',
			'province',
			'country',
			'zip',
			'mobile',
			'phone',
			'created',
			'groupname',
			'bank',
			'account_number',
			'account_name',
			'group_id',
			'token',
			'identifier',
			'merchant_request',
			'success',
			'fail'
		);

		// get total count result
		$count_all = $this->db->count_all($this->config->item('jayon_members_table'));

		$count_display_all = $this->db->count_all_results($this->config->item('jayon_members_table'));

		$this->db->select($this->config->item('jayon_members_table').'.*,g.description as groupname');
		$this->db->join('groups as g','members.group_id = g.id','left');

		$search = false;
				//search column
		if($this->input->post('sSearch') != ''){
			$srch = $this->input->post('sSearch');
			//$this->db->like('buyerdeliveryzone',$srch);
			$this->db->or_like('buyerdeliverytime',$srch);
			$this->db->or_like('delivery_id',$srch);
			$search = true;
		}

		if($this->input->post('sSearch_0') != ''){
			$this->db->like('username',$this->input->post('sSearch_0'));
			$search = true;
		}


		if($this->input->post('sSearch_1') != ''){
			$this->db->like('email',$this->input->post('sSearch_1'));
			$search = true;
		}

		if($this->input->post('sSearch_2') != ''){
			$this->db->like('fullname',$this->input->post('sSearch_2'));
			$search = true;
		}

		if($this->input->post('sSearch_3') != ''){
			$this->db->like('street',$this->input->post('sSearch_3'));
			$search = true;
		}

		if($this->input->post('sSearch_4') != ''){
			$this->db->like('district',$this->input->post('sSearch_4'));
			$search = true;
		}

		if($this->input->post('sSearch_5') != ''){
			$this->db->like('city',$this->input->post('sSearch_5'));
			$search = true;
		}
		if($this->input->post('sSearch_6') != ''){
			$this->db->like('province',$this->input->post('sSearch_6'));
			$search = true;
		}

		if($this->input->post('sSearch_7') != ''){
			$this->db->like('country',$this->input->post('sSearch_7'));
			$search = true;
		}

		if($this->input->post('sSearch_8') != ''){
			$this->db->like('zip',$this->input->post('sSearch_8'));
			$search = true;
		}

		if($this->input->post('sSearch_9') != ''){
			$this->db->like('mobile',$this->input->post('sSearch_9'));
			$search = true;
		}

		if($this->input->post('sSearch_10') != ''){
			$this->db->like('phone',$this->input->post('sSearch_10'));
			$search = true;
		}

		if($this->input->post('sSearch_11') != ''){
			$this->db->like('created',$this->input->post('sSearch_11'));
			$search = true;
		}

		if($search){
			//$this->db->and_();
		}		


		$data = $this->db
			->where('group_id',$group_id)
			->where('created < ', $from)
			->limit($limit_count, $limit_offset)
			->order_by($columns[$sort_col],$sort_dir)
			->get($this->config->item('jayon_members_table'));

		//print $this->db->last_query();

		$result = $data->result_array();

		$aadata = array();

		//print_r($result);

		foreach($result as $value => $key)
		{
			$delete = anchor("admin/members/delete/".$key['id']."/", "Delete"); // Build actions links
			$editpass = anchor("admin/members/editpass/".$key['id']."/", "Password"); // Build actions links
			if($key['group_id'] === group_id('merchant')){
				$addapp = anchor("admin/members/merchantmanage/".$key['id']."/", "Applications"); // Build actions links
			}else{
				$addapp = '&nbsp'; // Build actions links
			}
			$edit = anchor("admin/members/edit/".$key['id']."/", "Edit"); // Build actions links
			$detail = form_checkbox('assign[]',$key['id'],FALSE,'class="assign_check"').' '.anchor("admin/members/details/".$key['id']."/", $key['username']); // Build detail links

			//$printrecon = '<span class="printslip" id="'.$key['id'].'" style="cursor:pointer;text-decoration:underline;" >View</span>';
			$printrecon = '<span class="printrecon" id="'.$from.'_'.$to.'_'.$key['id'].'" title="Merchant" data="'.$key['id'].'" style="cursor:pointer;text-decoration:underline;" >View</span>';
			
			$aadata[] = array(
			 	$key['merchantname'],
			 	$key['email'],
			 	$key['fullname'],
			 	$key['mobile'],
			 	$key['phone'],
			 	$key['created'],
			 	$printrecon
			); // Adding row to table

		}

		$result = array(
			'sEcho'=> $this->input->post('sEcho'),
			'iTotalRecords'=>$count_all,
			'iTotalDisplayRecords'=> $count_display_all,
			'aaData'=>$aadata
		);

		print json_encode($result);

	}

	public function couriers($from,$to){
		$this->breadcrumb->add_crumb('Reconciliations','admin/reports/reconciliation');
		$this->breadcrumb->add_crumb('Couriers '.$from.' - '.$to,'admin/reports/couriers');

		$this->table->set_heading('Username', 'Email','Full Name','Mobile','Phone','Actions'); // Setting headings for the table

		$page['ajaxurl'] = 'admin/reports/ajaxcouriers/'.$from.'/'.$to;
		$page['page_title'] = 'Couriers Reconciliations';
		$this->ag_auth->view('reconajaxlistview',$page); // Load the view
	}

	public function ajaxcouriers($from,$to){

		$limit_count = $this->input->post('iDisplayLength');
		$limit_offset = $this->input->post('iDisplayStart');
		
		$sort_col = $this->input->post('iSortCol_0');
		$sort_dir = $this->input->post('sSortDir_0');

		$columns = array(
			'username','email','fullname','mobile','phone'
			);

		// get total count result
		$count_all = $this->db->count_all($this->config->item('jayon_couriers_table'));

		$count_display_all = $this->db->count_all_results($this->config->item('jayon_couriers_table'));
		
		$data = $this->db->limit($limit_count, $limit_offset)->order_by($columns[$sort_col],$sort_dir)->get($this->config->item('jayon_couriers_table'));
		
		//print $this->db->last_query();
		
		$result = $data->result_array();
			
		$aadata = array();
		
		
		foreach($result as $value => $key)
		{
			$delete = anchor("admin/couriers/delete/".$key['id']."/", "Delete"); // Build actions links
			$editpass = anchor("admin/couriers/editpass/".$key['id']."/", "Change Password"); // Build actions links
			$edit = anchor("admin/couriers/edit/".$key['id']."/", "Edit"); // Build actions links
			$detail = anchor("admin/couriers/details/".$key['id']."/", $key['username']); // Build detail links

			//$printrecon = '<span class="printslip" id="'.$key['id'].'" style="cursor:pointer;text-decoration:underline;" >View</span>';
			$printrecon = '<span class="printrecon" id="'.$from.'_'.$to.'_'.$key['id'].'" title="Courier" data="'.$key['id'].'" style="cursor:pointer;text-decoration:underline;" >View</span>';

			$aadata[] = array($detail, $key['email'],$key['fullname'],$key['mobile'],$key['phone'],$printrecon); // Adding row to table
		}
		
		$result = array(
			'sEcho'=> $this->input->post('sEcho'),
			'iTotalRecords'=>$count_all,
			'iTotalDisplayRecords'=> $count_display_all,
			'aaData'=>$aadata
		);
		
		print json_encode($result);
	}

	public function globalreport($from,$to){
		$this->breadcrumb->add_crumb('Reconciliations','admin/reports/reconciliation');
		$this->breadcrumb->add_crumb('Global '.$from.' - '.$to,'admin/reports/globalreport');

		$this->table->set_heading(
			'Year',
			'Week',
			'From',
			'To',
			'Generate'
			); // Setting headings for the table

		$page['ajaxurl'] = 'admin/reports/ajaxglobal/'.$from.'/'.$to;
		$page['page_title'] = 'Global Reconciliations';
		$this->ag_auth->view('reconajaxlistview',$page); // Load the view
	}

}

?>