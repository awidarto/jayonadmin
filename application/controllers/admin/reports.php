<?php

class Reports extends Application
{

	public function __construct()
	{
		parent::__construct();
		$this->ag_auth->restrict('admin'); // restrict this controller to admins only
		$this->table_tpl = array(
			'table_open' => '<table border="0" cellpadding="4" cellspacing="0" class="reportTable stickyHeader" id="toClone">'
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


        if($pdf == 'csv'){

            //$this->db->select('assignment_date,merchant_id,delivery_type,status,count(*) as count, sum(cod_cost) as cod_cost,sum(delivery_cost) as delivery_cost,sum(total_price) as total_price ,sum(total_discount) as total_discount , sum(total_tax) as total_tax,sum(((total_price-total_discount)+total_tax)) as package_value, members.merchantname as merchantname, members.fullname as merchantfullname');

            //$this->db->join($this->config->item('jayon_members_table'), $this->config->item('jayon_members_table').'.id = '.$this->config->item('incoming_delivery_table').'.merchant_id', 'left');

            $result = $this->db->get()->result_array();

            // Open the output stream
            $fh = fopen('php://output', 'w');

            // Start output buffering (to capture stream contents)
            ob_start();

            // Loop over the * to export
            if (! empty($result)) {
                $headers = array_keys($result[0]);
                    fputcsv($fh, $headers);
                foreach ($result as $item) {
                    fputcsv($fh, $item);
                }
            }

            // Get the contents of the output buffer
            $string = ob_get_clean();

            $filename = str_replace('/', '_', uri_string()).'.csv';

            // Output CSV-specific headers
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false);
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '";');
            header('Content-Transfer-Encoding: binary');

            exit($string);
        }




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

	public function revenue($type = null, $status = null ,$year = null, $scope = null, $par1 = null, $par2 = null, $par3 = null){

		$type = (is_null($type))?'Global':$type;
		$id = (is_null($type))?'noid':$type;
        $status = (is_null($status))?'all':$status;

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

        $data['statuslist'] = array_merge(array('all'=>'All'), $this->config->item('status_list') );
        $data['stid'] = $status;

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



		// get assignment_date, merchant_id,delivery_type

		$this->db->distinct();

		$this->db->select('assignment_date,merchant_id,delivery_type,status,count(*) as count, sum(cod_cost) as cod_cost,sum(delivery_cost) as delivery_cost,sum(total_price) as total_price ,sum(total_discount) as total_discount , sum(total_tax) as total_tax,sum(((total_price-total_discount)+total_tax)) as package_value');
		//$this->db->select('assignment_date,merchant_id,delivery_type,status');

		$this->db->from($this->config->item('delivered_delivery_table'));

		$column = 'assignment_date';
		$daterange = sprintf("`%s`between '%s%%' and '%s%%' ", $column, $sfrom, $sto);

		$this->db->where($daterange, null, false);
		$this->db->where($column.' != ','0000-00-00');

		if($id != 'noid'){
			$this->db->where($this->config->item('delivered_delivery_table').'.merchant_id',$id);
		}

        if($status != 'all'){
            $this->db->where('status', $status);
        }else{

    		$this->db->and_();
			$this->db->group_start();
				$this->db->where('status',	 $this->config->item('trans_status_mobile_delivered'));
				$this->db->or_where('status',$this->config->item('trans_status_mobile_revoked'));
				$this->db->or_where('status',$this->config->item('trans_status_mobile_noshow'));
				$this->db->or_where('status',$this->config->item('trans_status_mobile_rescheduled'));
			$this->db->group_end();

			$this->db->group_by('assignment_date,merchant_id,delivery_type,status');
			//$this->db->group_by('assignment_date,merchant_id,delivery_type,status');

        }


        /* raw query
            SELECT DISTINCT `assignment_date`, `merchant_id`, `delivery_type`, `status`, count(*) as count, sum(cod_cost) as cod_cost, sum(delivery_cost) as delivery_cost, sum(total_price) as total_price, sum(total_discount) as total_discount, sum(total_tax) as total_tax, sum(((total_price-total_discount)+total_tax)) as package_value, m.merchantname as merchantname FROM (`delivery_order_active`) LEFT JOIN members as m ON merchant_id = m.id WHERE `assignment_date`between '2014-02-01%' and '2014-02-28%' AND `assignment_date` != '0000-00-00' AND ( `status` = 'delivered' OR `status` = 'revoked' OR `status` = 'noshow' OR `status` = 'rescheduled' ) GROUP BY `assignment_date`, `merchant_id`, `delivery_type`, `status`
        */
        //print $this->db->last_query();

        if($pdf == 'csv'){

            $this->db->select('assignment_date,merchant_id,delivery_type,status,count(*) as count, sum(cod_cost) as cod_cost,sum(delivery_cost) as delivery_cost,sum(total_price) as total_price ,sum(total_discount) as total_discount , sum(total_tax) as total_tax,sum(((total_price-total_discount)+total_tax)) as package_value, members.merchantname as merchantname, members.fullname as merchantfullname');

            $this->db->join($this->config->item('jayon_members_table'), $this->config->item('jayon_members_table').'.id = '.$this->config->item('incoming_delivery_table').'.merchant_id', 'left');

            $result = $this->db->get()->result_array();

            // Open the output stream
            $fh = fopen('php://output', 'w');

            // Start output buffering (to capture stream contents)
            ob_start();

            // Loop over the * to export
            if (! empty($result)) {
                $headers = array_keys($result[0]);
                    fputcsv($fh, $headers);
                foreach ($result as $item) {
                    fputcsv($fh, $item);
                }
            }

            // Get the contents of the output buffer
            $string = ob_get_clean();

            $filename = str_replace('/', '_', uri_string()).'.csv';

            // Output CSV-specific headers
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false);
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '";');
            header('Content-Transfer-Encoding: binary');

            exit($string);
        }


        $rows = $this->db->get();

        $result = $rows->result_array();

        $last_query = $this->db->last_query();
		//print_r($result);

		//exit();

		$trans = array();


		foreach($result as $r){

			/*
			$this->db->select('count(*) as count, sum(cod_cost) as cod_cost,sum(delivery_cost) as delivery_cost,sum(total_price) as total_price ,sum(total_discount) as total_discount , sum(total_tax) as total_tax,sum(((total_price-total_discount)+total_tax)) as package_value');

			$this->db->from($this->config->item('delivered_delivery_table'));

			$this->db->where('assignment_date',$r['assignment_date']);
			$this->db->where('merchant_id',$r['merchant_id']);
			$this->db->where('delivery_type',$r['delivery_type']);
			$this->db->where('status',$r['status']);


			$irows = $this->db->get()->result_array();
			*/

			$trans[$r['assignment_date']][$r['merchant_id']][ trim($r['delivery_type']) ][ trim($r['status']) ]['count'] = $r['count'];
			$trans[$r['assignment_date']][$r['merchant_id']][ trim($r['delivery_type']) ][ trim($r['status']) ]['cod_cost'] = $r['cod_cost'];
			$trans[$r['assignment_date']][$r['merchant_id']][ trim($r['delivery_type']) ][ trim($r['status']) ]['delivery_cost'] = $r['delivery_cost'];
			$trans[$r['assignment_date']][$r['merchant_id']][ trim($r['delivery_type']) ][ trim($r['status']) ]['total_price'] = $r['total_price'];
			$trans[$r['assignment_date']][$r['merchant_id']][ trim($r['delivery_type']) ][ trim($r['status']) ]['package_value'] = $r['package_value'];
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

                //print_r($v);

				foreach($type_array as $t){

					foreach($status_array as $s){

						if(!isset($trans[$key][$k][$t][$s])){
							$trans[$key][$k][$t][$s]['count'] = 0;
							$trans[$key][$k][$t][$s]['cod_cost'] = 0;
							$trans[$key][$k][$t][$s]['delivery_cost'] = 0;
							$trans[$key][$k][$t][$s]['total_price'] = 0;
							$trans[$key][$k][$t][$s]['package_value'] = 0;
						}

					}

				}

			}

		}


		//print_r($trans);

		//exit();

		$this->table->set_heading(
			'',
			'',
			'',
			array('data'=>'DO','colspan'=>'3'),
			array('data'=>'COD','colspan'=>'4'),
			array('data'=>'CCOD','colspan'=>'4'),
			array('data'=>'PS','colspan'=>'3'),

			array('data'=>'Total','colspan'=>'3')
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

			'Revenue',
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
		$total['jex']['revenue'] = 0;
		$total['total_delivery_count']  = 0;
		$total['total_package_value']  = 0;

		$lastdate = '';

		foreach($trans as $key=>$val){

			foreach ($val as $k => $v) {

				$r[$key][$k] = $this->_makerevrow($v);

				$revtotal = ( $r[$key][$k]['Delivery Only']['dcost'] + $r[$key][$k]['COD']['dcost'] + $r[$key][$k]['COD']['sur'] + $r[$key][$k]['CCOD']['dcost'] + $r[$key][$k]['CCOD']['sur'] + $r[$key][$k]['PS']['pfee']);


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

					array('data'=>idr($revtotal),'class'=>'currency'),
					array('data'=>$r[$key][$k]['total_delivery_count'],'class'=>'count'),
					array('data'=>idr($r[$key][$k]['total_package_value']),'class'=>'currency')

				);

					$lastdate = $key;

					$total['Delivery Only']['count'] += (int) $r[$key][$k]['Delivery Only']['count'];
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

					$total['jex']['revenue'] += $revtotal;
					$total['total_delivery_count'] += $r[$key][$k]['total_delivery_count'];
					$total['total_package_value'] += $r[$key][$k]['total_package_value'];

				$counter++;

			}

		}

			$this->table->add_row(
				'',
				'',

				array('data'=>'Totals','class'=>'total'),

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

				array('data'=>idr($total['jex']['revenue']),'class'=>'total currency'),
				array('data'=>$total['total_delivery_count'],'class'=>'total count'),
				array('data'=>idr($total['total_package_value']),'class'=>'total currency')

			);

			$this->table->add_row(
				'',
				'',

				array('data'=>'Percentage (%)','class'=>'total'),

				array('data'=>($total['Delivery Only']['count'] == 0)?idr(0):idr(($total['Delivery Only']['count'] / $total['total_delivery_count'])* 100),'class'=>'total count c-orange'),
				array('data'=>($total['Delivery Only']['count'] == 0)?idr(0):idr($total['Delivery Only']['dcost'] / $total['jex']['revenue'] * 100),'class'=>'total currency c-maroon'),
				array('data'=>($total['Delivery Only']['pval'] == 0)?idr(0):idr($total['Delivery Only']['pval'] / $total['total_package_value'] * 100),'class'=>'total currency c-maroon'),

				array('data'=>($total['COD']['count'] == 0)?idr(0):idr($total['COD']['count'] / $total['total_delivery_count'] * 100),'class'=>'total count c-orange'),
				array('data'=>($total['COD']['dcost'] == 0)?idr(0):idr($total['COD']['dcost'] / $total['jex']['revenue'] * 100 ),'class'=>'total currency c-maroon'),
				array('data'=>($total['COD']['sur'] == 0)?idr(0):idr($total['COD']['sur'] / $total['jex']['revenue'] * 100),'class'=>'total currency c-maroon'),
				array('data'=>($total['COD']['pval'] == 0)?idr(0):idr($total['COD']['pval'] / $total['total_package_value'] * 100),'class'=>'total currency c-maroon'),

				array('data'=>($total['CCOD']['count'] == 0)?idr(0):idr($total['CCOD']['count'] / $total['total_delivery_count'] * 100),'class'=>'total count c-orange'),
				array('data'=>($total['CCOD']['dcost'] == 0)?idr(0):idr($total['CCOD']['dcost'] / $total['jex']['revenue'] * 100),'class'=>'total currency c-maroon'),
				array('data'=>($total['CCOD']['sur'] == 0)?idr(0):idr($total['CCOD']['sur'] / $total['jex']['revenue'] * 100),'class'=>'total currency c-maroon'),
				array('data'=>($total['CCOD']['pval'] == 0)?idr(0):idr($total['CCOD']['pval'] / $total['total_package_value'] * 100),'class'=>'total currency c-maroon'),

				array('data'=>($total['PS']['count'] == 0)?idr(0):idr($total['PS']['count'] / $total['total_delivery_count'] * 100),'class'=>'total count c-orange'),
				array('data'=>($total['PS']['pfee'] == 0)?idr(0):idr($total['PS']['pfee'] / $total['jex']['revenue'] * 100),'class'=>'total currency c-maroon'),
				array('data'=>($total['PS']['pval'] == 0)?idr(0):idr($total['PS']['pval'] / $total['total_package_value'] * 100),'class'=>'total currency c-maroon'),

				'',
				'',
				''
			);


			$this->table->add_row(
				'',
				'',

				array('data'=>'Summary','class'=>'total'),

				array('data'=>$total['Delivery Only']['count'] + $total['COD']['count'] + $total['CCOD']['count'] + $total['PS']['count'],'class'=>'total count'),
				array('data'=>idr($total['Delivery Only']['dcost'] + $total['COD']['dcost'] + $total['CCOD']['dcost']),'class'=>'total currency'),
				array('data'=>idr($total['Delivery Only']['pval'] + $total['COD']['pval'] + $total['CCOD']['pval'] + $total['PS']['pval']),'class'=>'total currency'),

				'',
				'',
				array('data'=>idr($total['COD']['sur'] + $total['CCOD']['sur']),'class'=>'total currency'),
				'',

				'',
				'',
				'',
				'',

				'',
				array('data'=>idr($total['PS']['pfee']),'class'=>'total currency'),
				'',

				array('data'=>idr($total['jex']['revenue']),'class'=>'total currency'),
				array('data'=>$total['total_delivery_count'],'class'=>'total count'),
				array('data'=>idr($total['total_package_value']),'class'=>'total currency')

			);


		$recontab = $this->table->generate();
		$data['recontab'] = $recontab;

		/* end copy */

		$this->breadcrumb->add_crumb('Revenue','admin/reports/reconciliation');

		$page['ajaxurl'] = 'admin/reports/ajaxreconciliation';
		$page['page_title'] = 'Merchant Reconciliations';
        $data['select_title'] = 'Merchant';

		$data['controller'] = 'admin/reports/revenue/';

        $data['last_query'] = $last_query;

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


    public function revenuegen($type = null,$status = null,$year = null, $scope = null, $par1 = null, $par2 = null, $par3 = null){

        $type = (is_null($type))?'Global':$type;
        $id = (is_null($type))?'noid':$type;
        $status = (is_null($status))?'all':$status;

        if(is_null($scope)){
            $id = 'noid';
            $scope = 'month';
            $year = date('Y',time());
            $par1 = date('m',time());
        }

        $pdf = null;

        if($scope == 'month'){
            $days = cal_days_in_month(CAL_GREGORIAN, $par1, $year);
            $from = date('Y-m-d', strtotime($year.'/'.$par1.'/1'));
            $to =   date('Y-m-d', strtotime($year.'/'.$par1.'/'.$days));
            $pdf = $par2;

            $data['month'] = $par1;
            $data['week'] = 1;
        }else if($scope == 'week'){
            $from = date('Y-m-d', strtotime('1 Jan '.$year.' +'.($par1 - 1).' weeks'));
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

        $data['statuslist'] = array_merge(array('all'=>'All'), $this->config->item('status_list') );
        $data['stid'] = $status;

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

        $this->db->from($this->config->item('jayon_revenue_table'));


        $column = 'assignment_date';
        $daterange = sprintf("`%s`between '%s%%' and '%s%%' ", $column, $sfrom, $sto);

        $this->db->where($daterange, null, false);
        $this->db->where($column.' != ','0000-00-00');

        if($id != 'noid'){
            $this->db->where('merchant_id',$id);
        }

        if($status != 'all'){
                $this->db->where('status',   $status);
        }else{

            $this->db->and_();
            $this->db->group_start();
                $this->db->where('status',   $this->config->item('trans_status_mobile_delivered'));
                /*
                $this->db->or_where('status',$this->config->item('trans_status_mobile_revoked'));
                $this->db->or_where('status',$this->config->item('trans_status_mobile_noshow'));
                $this->db->or_where('status',$this->config->item('trans_status_mobile_rescheduled'));
                */
            $this->db->group_end();
        }
        //print $this->db->last_query();

        if($pdf == 'csv'){

            $result = $this->db->get()->result_array();

            // Open the output stream
            $fh = fopen('php://output', 'w');

            // Start output buffering (to capture stream contents)
            ob_start();

            // Loop over the * to export
            if (! empty($result)) {
                $headers = array_keys($result[0]);
                    fputcsv($fh, $headers);
                foreach ($result as $item) {
                    fputcsv($fh, $item);
                }
            }

            // Get the contents of the output buffer
            $string = ob_get_clean();

            $filename = str_replace('/', '_', uri_string()).'.csv';

            // Output CSV-specific headers
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false);
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '";');
            header('Content-Transfer-Encoding: binary');

            exit($string);
        }


        $rows = $this->db->get();

        $trans = $rows->result_array();

        $last_query = $this->db->last_query();
        //print_r($result);


        //exit();

        //print_r($trans);

        //exit();

        $this->table->set_heading(
            '',
            '',
            '',
            array('data'=>'DO','colspan'=>'3'),
            array('data'=>'COD','colspan'=>'4'),
            array('data'=>'CCOD','colspan'=>'4'),
            array('data'=>'PS','colspan'=>'3'),

            array('data'=>'Total','colspan'=>'3')
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

            'Revenue',
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
        $total['jex']['revenue'] = 0;
        $total['total_delivery_count']  = 0;
        $total['total_package_value']  = 0;

        $lastdate = '';

        foreach($trans as $r){

            $revtotal = ( $r['do_delivery_cost'] + $r['cod_delivery_cost'] + $r['cod_cod_cost'] + $r['ccod_delivery_cost'] + $r['ccod_cod_cost'] + $r['ps_delivery_cost']);
            $total_count = $r['do_count'] + $r['cod_count'] + $r['ccod_count'] + $r['ps_count'];

            $total_value = $r['do_total_price'] + $r['cod_total_price'] + $r['ccod_total_price'] + $r['ps_total_price'];


            $this->table->add_row(
                $counter,
                ($lastdate == $r['assignment_date'])?'': date( 'd-m-Y' ,strtotime($r['assignment_date']) ) ,
                $r['merchant_name'],
                array('data'=>$r['do_count'],'class'=>'count'),
                array('data'=>idr($r['do_delivery_cost']),'class'=>'currency'),
                array('data'=>idr($r['do_total_price']),'class'=>'currency'),

                array('data'=>$r['cod_count'],'class'=>'count'),
                array('data'=>idr($r['cod_delivery_cost']),'class'=>'currency'),
                array('data'=>idr($r['cod_cod_cost']),'class'=>'currency'),
                array('data'=>idr($r['cod_total_price']),'class'=>'currency'),

                array('data'=>$r['ccod_count'],'class'=>'count'),
                array('data'=>idr($r['ccod_delivery_cost']),'class'=>'currency'),
                array('data'=>idr($r['ccod_cod_cost']),'class'=>'currency'),
                array('data'=>idr($r['ccod_total_price']),'class'=>'currency'),

                array('data'=>$r['ps_count'],'class'=>'count'),
                array('data'=>idr($r['ps_delivery_cost']),'class'=>'currency'),
                array('data'=>idr($r['ps_total_price']),'class'=>'currency'),

                array('data'=>idr($revtotal),'class'=>'currency'),
                array('data'=>$total_count,'class'=>'count'),
                array('data'=>idr($total_value),'class'=>'currency')

            );

                $lastdate = $r['assignment_date'];

                $total['Delivery Only']['count'] += (int) $r['do_count'];
                $total['Delivery Only']['dcost'] += $r['do_delivery_cost'];
                $total['Delivery Only']['pval'] += $r['do_total_price'];
                $total['COD']['count'] += $r['cod_count'];
                $total['COD']['dcost'] += $r['cod_delivery_cost'];
                $total['COD']['sur'] += $r['cod_cod_cost'];
                $total['COD']['pval'] += $r['cod_total_price'];
                $total['CCOD']['count'] += $r['ccod_count'];
                $total['CCOD']['dcost'] += $r['ccod_delivery_cost'];
                $total['CCOD']['sur'] += $r['ccod_cod_cost'];
                $total['CCOD']['pval'] += $r['ccod_total_price'];
                $total['PS']['count'] += $r['ps_count'];
                $total['PS']['pfee'] += $r['ps_delivery_cost'];
                $total['PS']['pval'] += $r['ps_total_price'];

                $total['jex']['revenue'] += $revtotal;
                $total['total_delivery_count'] += $total_count;
                $total['total_package_value'] += $total_value;

            $counter++;

        }

            $this->table->add_row(
                '',
                '',

                array('data'=>'Totals','class'=>'total'),

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

                array('data'=>idr($total['jex']['revenue']),'class'=>'total currency'),
                array('data'=>$total['total_delivery_count'],'class'=>'total count'),
                array('data'=>idr($total['total_package_value']),'class'=>'total currency')

            );

            $this->table->add_row(
                '',
                '',

                array('data'=>'Percentage (%)','class'=>'total'),

                array('data'=>($total['Delivery Only']['count'] == 0)?idr(0):idr(($total['Delivery Only']['count'] / $total['total_delivery_count'])* 100),'class'=>'total count c-orange'),
                array('data'=>($total['Delivery Only']['count'] == 0)?idr(0):idr($total['Delivery Only']['dcost'] / $total['jex']['revenue'] * 100),'class'=>'total currency c-maroon'),
                array('data'=>($total['Delivery Only']['pval'] == 0)?idr(0):idr($total['Delivery Only']['pval'] / $total['total_package_value'] * 100),'class'=>'total currency c-maroon'),

                array('data'=>($total['COD']['count'] == 0)?idr(0):idr($total['COD']['count'] / $total['total_delivery_count'] * 100),'class'=>'total count c-orange'),
                array('data'=>($total['COD']['dcost'] == 0)?idr(0):idr($total['COD']['dcost'] / $total['jex']['revenue'] * 100 ),'class'=>'total currency c-maroon'),
                array('data'=>($total['COD']['sur'] == 0)?idr(0):idr($total['COD']['sur'] / $total['jex']['revenue'] * 100),'class'=>'total currency c-maroon'),
                array('data'=>($total['COD']['pval'] == 0)?idr(0):idr($total['COD']['pval'] / $total['total_package_value'] * 100),'class'=>'total currency c-maroon'),

                array('data'=>($total['CCOD']['count'] == 0)?idr(0):idr($total['CCOD']['count'] / $total['total_delivery_count'] * 100),'class'=>'total count c-orange'),
                array('data'=>($total['CCOD']['dcost'] == 0)?idr(0):idr($total['CCOD']['dcost'] / $total['jex']['revenue'] * 100),'class'=>'total currency c-maroon'),
                array('data'=>($total['CCOD']['sur'] == 0)?idr(0):idr($total['CCOD']['sur'] / $total['jex']['revenue'] * 100),'class'=>'total currency c-maroon'),
                array('data'=>($total['CCOD']['pval'] == 0)?idr(0):idr($total['CCOD']['pval'] / $total['total_package_value'] * 100),'class'=>'total currency c-maroon'),

                array('data'=>($total['PS']['count'] == 0)?idr(0):idr($total['PS']['count'] / $total['total_delivery_count'] * 100),'class'=>'total count c-orange'),
                array('data'=>($total['PS']['pfee'] == 0)?idr(0):idr($total['PS']['pfee'] / $total['jex']['revenue'] * 100),'class'=>'total currency c-maroon'),
                array('data'=>($total['PS']['pval'] == 0)?idr(0):idr($total['PS']['pval'] / $total['total_package_value'] * 100),'class'=>'total currency c-maroon'),

                '',
                '',
                ''
            );


            $this->table->add_row(
                '',
                '',

                array('data'=>'Summary','class'=>'total'),

                array('data'=>$total['Delivery Only']['count'] + $total['COD']['count'] + $total['CCOD']['count'] + $total['PS']['count'],'class'=>'total count'),
                array('data'=>idr($total['Delivery Only']['dcost'] + $total['COD']['dcost'] + $total['CCOD']['dcost']),'class'=>'total currency'),
                array('data'=>idr($total['Delivery Only']['pval'] + $total['COD']['pval'] + $total['CCOD']['pval'] + $total['PS']['pval']),'class'=>'total currency'),

                '',
                '',
                array('data'=>idr($total['COD']['sur'] + $total['CCOD']['sur']),'class'=>'total currency'),
                '',

                '',
                '',
                '',
                '',

                '',
                array('data'=>idr($total['PS']['pfee']),'class'=>'total currency'),
                '',

                array('data'=>idr($total['jex']['revenue']),'class'=>'total currency'),
                array('data'=>$total['total_delivery_count'],'class'=>'total count'),
                array('data'=>idr($total['total_package_value']),'class'=>'total currency')

            );


        $recontab = $this->table->generate();
        $data['recontab'] = $recontab;


        $this->table->clear();

        //$tmpl = array( 'table_open'  => '<table style="width:500px;" border="0" cellpadding="0" cellspacing="0" class="mytable">' );


        //$this->table->set_template($tmpl);

            $this->table->set_heading(
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

                'Revenue',
                'Delivery Count',
                'Package Value'
            ); // Setting headings for the table

            $this->table->add_row(
                array('data'=>'Summary','class'=>'total'),

                array('data'=>$total['Delivery Only']['count'] + $total['COD']['count'] + $total['CCOD']['count'] + $total['PS']['count'],'class'=>'total count'),
                array('data'=>idr($total['Delivery Only']['dcost'] + $total['COD']['dcost'] + $total['CCOD']['dcost']),'class'=>'total currency'),
                array('data'=>idr($total['Delivery Only']['pval'] + $total['COD']['pval'] + $total['CCOD']['pval'] + $total['PS']['pval']),'class'=>'total currency'),

                '',
                '',
                array('data'=>idr($total['COD']['sur'] + $total['CCOD']['sur']),'class'=>'total currency'),
                '',

                '',
                '',
                '',
                '',

                '',
                array('data'=>idr($total['PS']['pfee']),'class'=>'total currency'),
                '',

                array('data'=>idr($total['jex']['revenue']),'class'=>'total currency'),
                array('data'=>$total['total_delivery_count'],'class'=>'total count'),
                array('data'=>idr($total['total_package_value']),'class'=>'total currency')

            );


        $sumtab = $this->table->generate();
        $data['sumtab'] = $sumtab;


        /* end copy */

        $this->breadcrumb->add_crumb('Revenue ( Manual Generated )','admin/reports/reconciliation');

        $page['ajaxurl'] = 'admin/reports/ajaxreconciliation';
        $page['page_title'] = 'Merchant Reconciliations';
        $data['select_title'] = 'Merchant';

        $data['controller'] = 'admin/reports/revenuegen/';

        $data['last_query'] = $last_query;

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


    public function devicerecongen($type = null,$status = null,$year = null, $scope = null, $par1 = null, $par2 = null, $par3 = null){

        $type = (is_null($type))?'Global':$type;
        $id = (is_null($type))?'noid':$type;
        $status = (is_null($status))?'all':$status;


        if(is_null($scope)){
            $id = 'noid';
            $scope = 'month';
            $year = date('Y',time());
            $par1 = date('m',time());
        }

        $pdf = null;

        if($scope == 'month'){
            $days = cal_days_in_month(CAL_GREGORIAN, $par1, $year);
            $from = date('Y-m-d', strtotime($year.'/'.$par1.'/1'));
            $to =   date('Y-m-d', strtotime($year.'/'.$par1.'/'.$days));
            $pdf = $par2;

            $data['month'] = $par1;
            $data['week'] = 1;
        }else if($scope == 'week'){
            $from = date('Y-m-d', strtotime('1 Jan '.$year.' +'.($par1 - 1).' weeks'));
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

        $clist = get_device_list();

        $cs = array('noid'=>'All');
        foreach ($clist as $ckey) {
            $cs[$ckey->id] = $ckey->identifier;
        }

        $data['merchants'] = $cs;
        $data['id'] = $id;

        $data['statuslist'] = array_merge(array('all'=>'All'), $this->config->item('status_list') );
        $data['stid'] = $status;

        /* copied from print controller */

        $this->load->library('number_words');

        if($id == 'noid'){
            $data['type_name'] = '-';
            $data['bank_account'] = 'n/a';
            $data['type'] = 'Global';
        }else{
            $user = $this->db->where('id',$id)->get($this->config->item('jayon_devices_table'))->row();
            //print $this->db->last_query();
            $data['type'] = $user->identifier;
            $data['type_name'] = $user->identifier;
            $data['bank_account'] = 'n/a';
        }

        $data['period'] = $from.' s/d '.$to;

        $sfrom = date('Y-m-d',strtotime($from));
        $sto = date('Y-m-d',strtotime($to));

        $this->db->from($this->config->item('jayon_devicerecap_table'));


        $column = 'assignment_date';
        $daterange = sprintf("`%s`between '%s%%' and '%s%%' ", $column, $sfrom, $sto);

        $this->db->where($daterange, null, false);
        $this->db->where($column.' != ','0000-00-00');

        if($id != 'noid'){
            $this->db->where('device_id',$id);
        }

        if($status != 'all'){
            $this->db->where('status',   $status);
        }else{
            $this->db->and_();
            $this->db->group_start();
                $this->db->where('status',   $this->config->item('trans_status_mobile_delivered'));
                /*
                $this->db->or_where('status',$this->config->item('trans_status_mobile_revoked'));
                $this->db->or_where('status',$this->config->item('trans_status_mobile_noshow'));
                $this->db->or_where('status',$this->config->item('trans_status_mobile_rescheduled'));
                */
            $this->db->group_end();

        }


        //print $this->db->last_query();

        if($pdf == 'csv'){

            $result = $this->db->get()->result_array();

            // Open the output stream
            $fh = fopen('php://output', 'w');

            // Start output buffering (to capture stream contents)
            ob_start();

            // Loop over the * to export
            if (! empty($result)) {
                $headers = array_keys($result[0]);
                    fputcsv($fh, $headers);
                foreach ($result as $item) {
                    fputcsv($fh, $item);
                }
            }

            // Get the contents of the output buffer
            $string = ob_get_clean();

            $filename = str_replace('/', '_', uri_string()).'.csv';

            // Output CSV-specific headers
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false);
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '";');
            header('Content-Transfer-Encoding: binary');

            exit($string);
        }


        $rows = $this->db->get();

        $trans = $rows->result_array();

        $last_query = $this->db->last_query();
        //print_r($result);


        //exit();

        //print_r($trans);

        //exit();

        $this->table->set_heading(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            array('data'=>'DO','colspan'=>'3'),
            array('data'=>'COD','colspan'=>'4'),
            array('data'=>'CCOD','colspan'=>'4'),
            array('data'=>'PS','colspan'=>'3'),

            array('data'=>'Total','colspan'=>'3')
        ); // Setting headings for the table


        $this->table->set_subheading(
            'No.',
            'Date',
            'Incoming',
            'Assigned',
            'Pending',
            '% Delivered',
            'Device',

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

            'Revenue',
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
        $total['jex']['revenue'] = 0;
        $total['total_delivery_count']  = 0;
        $total['total_package_value']  = 0;

        $lastdate = '';

        $tinc = 0;

        $t_assigned = 0;
        $t_pending = 0;

        foreach($trans as $r){

            $revtotal = ( $r['do_delivery_cost'] + $r['cod_delivery_cost'] + $r['cod_cod_cost'] + $r['ccod_delivery_cost'] + $r['ccod_cod_cost'] + $r['ps_delivery_cost']);
            $total_count = $r['do_count'] + $r['cod_count'] + $r['ccod_count'] + $r['ps_count'];

            $total_value = $r['do_total_price'] + $r['cod_total_price'] + $r['ccod_total_price'] + $r['ps_total_price'];

            if($lastdate == $r['assignment_date']){
                $inc = '';
            }else{
                $inc = $this->db->like('ordertime', $r['assignment_date'],'after')->count_all_results($this->config->item('delivered_delivery_table'));
                $tinc += $inc;
            }

            $assignment_count = $this->db->like('assignment_date', $r['assignment_date'],'after')
                                    ->where('device_id',$r['device_id'])
                                    ->count_all_results($this->config->item('delivered_delivery_table'));
            $pending_count = $assignment_count - $total_count;


            $delivered_pct = ($total_count / $assignment_count) * 100;

            $t_assigned += $assignment_count;
            $t_pending += $pending_count;

            $this->table->add_row(
                $counter,
                ($lastdate == $r['assignment_date'])?'': date( 'd-m-Y' ,strtotime($r['assignment_date']) ) ,
                $inc,
                $assignment_count,
                $pending_count,
                number_format($delivered_pct,2),
                $r['device_name'],
                array('data'=>$r['do_count'],'class'=>'count'),
                array('data'=>idr($r['do_delivery_cost']),'class'=>'currency'),
                array('data'=>idr($r['do_total_price']),'class'=>'currency'),

                array('data'=>$r['cod_count'],'class'=>'count'),
                array('data'=>idr($r['cod_delivery_cost']),'class'=>'currency'),
                array('data'=>idr($r['cod_cod_cost']),'class'=>'currency'),
                array('data'=>idr($r['cod_total_price']),'class'=>'currency'),

                array('data'=>$r['ccod_count'],'class'=>'count'),
                array('data'=>idr($r['ccod_delivery_cost']),'class'=>'currency'),
                array('data'=>idr($r['ccod_cod_cost']),'class'=>'currency'),
                array('data'=>idr($r['ccod_total_price']),'class'=>'currency'),

                array('data'=>$r['ps_count'],'class'=>'count'),
                array('data'=>idr($r['ps_delivery_cost']),'class'=>'currency'),
                array('data'=>idr($r['ps_total_price']),'class'=>'currency'),

                array('data'=>idr($revtotal),'class'=>'currency'),
                array('data'=>$total_count,'class'=>'count'),
                array('data'=>idr($total_value),'class'=>'currency')

            );

                $lastdate = $r['assignment_date'];

                $total['Delivery Only']['count'] += (int) $r['do_count'];
                $total['Delivery Only']['dcost'] += $r['do_delivery_cost'];
                $total['Delivery Only']['pval'] += $r['do_total_price'];
                $total['COD']['count'] += $r['cod_count'];
                $total['COD']['dcost'] += $r['cod_delivery_cost'];
                $total['COD']['sur'] += $r['cod_cod_cost'];
                $total['COD']['pval'] += $r['cod_total_price'];
                $total['CCOD']['count'] += $r['ccod_count'];
                $total['CCOD']['dcost'] += $r['ccod_delivery_cost'];
                $total['CCOD']['sur'] += $r['ccod_cod_cost'];
                $total['CCOD']['pval'] += $r['ccod_total_price'];
                $total['PS']['count'] += $r['ps_count'];
                $total['PS']['pfee'] += $r['ps_delivery_cost'];
                $total['PS']['pval'] += $r['ps_total_price'];

                $total['jex']['revenue'] += $revtotal;
                $total['total_delivery_count'] += $total_count;
                $total['total_package_value'] += $total_value;

            $counter++;

        }
            $t_delivered_pct = ($total['total_delivery_count'] / $t_assigned) * 100;
            $this->table->add_row(
                '',
                '',
                $tinc,
                $t_assigned,
                $t_pending,
                number_format($t_delivered_pct,2),
                array('data'=>'Totals','class'=>'total'),

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

                array('data'=>idr($total['jex']['revenue']),'class'=>'total currency'),
                array('data'=>$total['total_delivery_count'],'class'=>'total count'),
                array('data'=>idr($total['total_package_value']),'class'=>'total currency')

            );

            $this->table->add_row(
                '',
                '',
                '',
                '',
                '',
                '',
                array('data'=>'Percentage (%)','class'=>'total'),

                array('data'=>($total['Delivery Only']['count'] == 0)?idr(0):idr(($total['Delivery Only']['count'] / $total['total_delivery_count'])* 100),'class'=>'total count c-orange'),
                array('data'=>($total['Delivery Only']['count'] == 0)?idr(0):idr($total['Delivery Only']['dcost'] / $total['jex']['revenue'] * 100),'class'=>'total currency c-maroon'),
                array('data'=>($total['Delivery Only']['pval'] == 0)?idr(0):idr($total['Delivery Only']['pval'] / $total['total_package_value'] * 100),'class'=>'total currency c-maroon'),

                array('data'=>($total['COD']['count'] == 0)?idr(0):idr($total['COD']['count'] / $total['total_delivery_count'] * 100),'class'=>'total count c-orange'),
                array('data'=>($total['COD']['dcost'] == 0)?idr(0):idr($total['COD']['dcost'] / $total['jex']['revenue'] * 100 ),'class'=>'total currency c-maroon'),
                array('data'=>($total['COD']['sur'] == 0)?idr(0):idr($total['COD']['sur'] / $total['jex']['revenue'] * 100),'class'=>'total currency c-maroon'),
                array('data'=>($total['COD']['pval'] == 0)?idr(0):idr($total['COD']['pval'] / $total['total_package_value'] * 100),'class'=>'total currency c-maroon'),

                array('data'=>($total['CCOD']['count'] == 0)?idr(0):idr($total['CCOD']['count'] / $total['total_delivery_count'] * 100),'class'=>'total count c-orange'),
                array('data'=>($total['CCOD']['dcost'] == 0)?idr(0):idr($total['CCOD']['dcost'] / $total['jex']['revenue'] * 100),'class'=>'total currency c-maroon'),
                array('data'=>($total['CCOD']['sur'] == 0)?idr(0):idr($total['CCOD']['sur'] / $total['jex']['revenue'] * 100),'class'=>'total currency c-maroon'),
                array('data'=>($total['CCOD']['pval'] == 0)?idr(0):idr($total['CCOD']['pval'] / $total['total_package_value'] * 100),'class'=>'total currency c-maroon'),

                array('data'=>($total['PS']['count'] == 0)?idr(0):idr($total['PS']['count'] / $total['total_delivery_count'] * 100),'class'=>'total count c-orange'),
                array('data'=>($total['PS']['pfee'] == 0)?idr(0):idr($total['PS']['pfee'] / $total['jex']['revenue'] * 100),'class'=>'total currency c-maroon'),
                array('data'=>($total['PS']['pval'] == 0)?idr(0):idr($total['PS']['pval'] / $total['total_package_value'] * 100),'class'=>'total currency c-maroon'),

                '',
                '',
                ''
            );


            $this->table->add_row(
                '',
                '',
                '',
                '',
                '',
                '',
                array('data'=>'Summary','class'=>'total'),

                array('data'=>$total['Delivery Only']['count'] + $total['COD']['count'] + $total['CCOD']['count'] + $total['PS']['count'],'class'=>'total count'),
                array('data'=>idr($total['Delivery Only']['dcost'] + $total['COD']['dcost'] + $total['CCOD']['dcost']),'class'=>'total currency'),
                array('data'=>idr($total['Delivery Only']['pval'] + $total['COD']['pval'] + $total['CCOD']['pval'] + $total['PS']['pval']),'class'=>'total currency'),

                '',
                '',
                array('data'=>idr($total['COD']['sur'] + $total['CCOD']['sur']),'class'=>'total currency'),
                '',

                '',
                '',
                '',
                '',

                '',
                array('data'=>idr($total['PS']['pfee']),'class'=>'total currency'),
                '',

                array('data'=>idr($total['jex']['revenue']),'class'=>'total currency'),
                array('data'=>$total['total_delivery_count'],'class'=>'total count'),
                array('data'=>idr($total['total_package_value']),'class'=>'total currency')

            );


        $recontab = $this->table->generate();
        $data['recontab'] = $recontab;

        /* end copy */

        $this->breadcrumb->add_crumb('Device Reconciliations ( Manual Generated )','admin/reports/devicerecongen');

        $page['ajaxurl'] = 'admin/reports/ajaxreconciliation';
        $page['page_title'] = 'Device Reconciliations';
        $data['select_title'] = 'Device';

        $data['controller'] = 'admin/reports/devicerecongen/';

        $data['last_query'] = $last_query;

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



    public function invoices($type = null,$deliverytype = null,$status = null,$year = null, $scope = null, $par1 = null, $par2 = null, $par3 = null,$par4 = null){

        $type = (is_null($type))?'Global':$type;
        $id = (is_null($type))?'noid':$type;
        $deliverytype = (is_null($deliverytype))?'noid':$deliverytype;
        $status = (is_null($status))?'all':$status;

        if(is_null($scope)){
            $id = 'noid';
            $scope = 'month';
            $year = date('Y',time());
            $par1 = date('m',time());
        }

        $data['getparams'] = array(
            'type'=> $type ,
            'deliverytype'=>$deliverytype,
            'status'=>$status,
            'year'=> $year ,
            'scope'=>$scope ,
            'par1'=> $par1 ,
            'par2'=> $par2 ,
            'par3'=> $par3 ,
            'par4'=> $par4
            );

        $pdf = null;

        if($scope == 'month'){
            $days = cal_days_in_month(CAL_GREGORIAN, $par1, $year);
            $from = date('Y-m-d', strtotime($year.'/'.$par1.'/1'));
            $to =   date('Y-m-d', strtotime($year.'/'.$par1.'/'.$days));
            $pdf = $par2;
            $invdate = $par3;

            $data['getparams']['par2'] = 'pdf';

            $data['month'] = $par1;
            $data['week'] = 1;
        }else if($scope == 'week'){
            $from = date('Y-m-d', strtotime('1 Jan '.$year.' +'.($par1 - 1).' weeks'));
            $to = date('Y-m-d', strtotime('1 Jan '.$year.' +'.$par1.' weeks - 1 day'));
            $pdf = $par2;
            $invdate = $par3;

            $data['getparams']['par2'] = 'pdf';

            $data['month'] = 1;
            $data['week'] = $par1;
        }else if($scope == 'date'){
            $from = $par1;
            $to = $par2;
            $pdf = $par3;
            $invdate = $par4;

            $data['getparams']['par3'] = 'pdf';

            $data['month'] = 1;
            $data['week'] = 1;
        }else{
            $from = date('Y-m-d',time());
            $to = date('Y-m-d',time());
            $pdf = null;
            $invdate = null;

            $data['getparams']['par2'] = 'pdf';

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

        $data['statuslist'] = array_merge(array('all'=>'All'), $this->config->item('status_list') );
        $data['stid'] = $status;

        $data['deliverytypes'] = $this->config->item('deliverytype_selector');

        /* copied from print controller */

        $this->load->library('number_words');

        if($id == 'noid'){
            $data['type_name'] = '-';
            $data['bank_account'] = 'n/a';
            $data['type'] = 'Global';

            $data['merchantname'] = 'All Merchant';

        }else{
            $user = $this->db->where('id',$id)->get($this->config->item('jayon_members_table'))->row();
            //print $this->db->last_query();
            $data['type'] = $user->merchantname.' - '.$user->fullname;
            $data['type_name'] = $user->fullname;
            $data['bank_account'] = 'n/a';

            $data['merchantname'] = $user->merchantname;
        }

        if(is_null($invdate)){
            $data['invdate'] = '-';
            $data['invdatenum'] = '-';
        }else{
            $data['invdate'] = iddate($invdate);
            $data['invdatenum'] = date('dmY',mysql_to_unix($invdate)) ;
        }

        if($deliverytype == 'noid'){
            $data['dtype'] = 'All Type';
        }else{
            $data['dtype'] = $deliverytype;
        }

        $data['period'] = $from.' s/d '.$to;

        $sfrom = date('Y-m-d',strtotime($from));
        $sto = date('Y-m-d',strtotime($to));

        $this->db->select('assignment_date,delivery_id,'.$this->config->item('assigned_delivery_table').'.merchant_id as merchant_id,buyer_name,merchant_trans_id,m.merchantname as merchant_name, m.fullname as fullname, a.application_name as app_name, a.domain as domain ,delivery_type,status,cod_cost,delivery_cost,total_price,total_tax,fulfillment_code,total_discount,chargeable_amount,actual_weight,application_id,application_key')
            ->join('members as m',$this->config->item('incoming_delivery_table').'.merchant_id=m.id','left')
            ->join('applications as a',$this->config->item('assigned_delivery_table').'.application_id=a.id','left')
            ->join('devices as d',$this->config->item('assigned_delivery_table').'.device_id=d.id','left')
            ->join('couriers as c',$this->config->item('assigned_delivery_table').'.courier_id=c.id','left')
            //->like('assignment_date',$date,'before')
            ->from($this->config->item('incoming_delivery_table'));

        $column = 'assignment_date';
        $daterange = sprintf("`%s`between '%s%%' and '%s%%' ", $column, $sfrom, $sto);

        $this->db->where($daterange, null, false);
        $this->db->where($column.' != ','0000-00-00');

        if($id != 'noid'){
            $this->db->where($this->config->item('assigned_delivery_table').'.merchant_id',$id);
        }

        if($deliverytype != 'noid'){
            if($deliverytype == 'DO'){
                $deliverytype = 'Delivery Only';
                $this->db->where($this->config->item('assigned_delivery_table').'.delivery_type',$deliverytype);
            }else if($deliverytype == 'COD'){
                $this->db->like($this->config->item('assigned_delivery_table').'.delivery_type',$deliverytype,'before');
            }
        }

        if($status != 'all'){
                $this->db->where('status',   $status);
        }else{
            $this->db->and_();
            $this->db->group_start();
                $this->db->where('status',   $this->config->item('trans_status_mobile_delivered'));
                /*
                $this->db->or_where('status',$this->config->item('trans_status_mobile_revoked'));
                $this->db->or_where('status',$this->config->item('trans_status_mobile_noshow'));
                $this->db->or_where('status',$this->config->item('trans_status_mobile_rescheduled'));
                */
            $this->db->group_end();

        }

        $this->db->order_by('delivery_type', 'asc');
        $this->db->order_by('assignment_date', 'asc');

        //print $this->db->last_query();

        if($pdf == 'csv'){

            $result = $this->db->get()->result_array();


            foreach ($result as $r){

                if($r['total_price'] == 0 || is_null($r['total_price']) || $r['total_price'] == ''){
                    if($r['chargeable_amount'] > 0){
                        $r['total_price'] = $r['chargeable_amount'];
                    }
                }

                $app_id = $r['application_id'];

                if($r['delivery_type'] == 'COD' || $r['delivery_type'] == 'CCOD'){
                    if($r['cod_cost'] == 0 || is_null($r['cod_cost']) || $r['cod_cost'] == ''){
                        try{
                            //$app_id = get_app_id_from_key($r['application_key']);
                            $r['cod_cost'] = get_cod_tariff($r['total_price'],$app_id);
                        }catch(Exception $e){

                        }
                    }
                }else{
                    $r['cod_cost'] = 0;
                }



                if($r['delivery_cost'] == 0 || is_null($r['delivery_cost']) || $r['delivery_cost'] == ''){
                    try{
                        $r['delivery_cost'] = get_weight_tariff($r['actual_weight'], $r['delivery_type'] ,$app_id);
                    }catch(Exception $e){

                    }
                }

                # code...
            }

            // Open the output stream
            $fh = fopen('php://output', 'w');

            // Start output buffering (to capture stream contents)
            ob_start();

            // Loop over the * to export
            if (! empty($result)) {
                $headers = array_keys($result[0]);
                    fputcsv($fh, $headers);
                foreach ($result as $item) {
                    fputcsv($fh, $item);
                }
            }

            // Get the contents of the output buffer
            $string = ob_get_clean();

            $filename = str_replace('/', '_', uri_string()).'.csv';

            // Output CSV-specific headers
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false);
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '";');
            header('Content-Transfer-Encoding: binary');

            exit($string);
        }


        $rows = $this->db->get();

        $trans = $rows->result();

        $last_query = $this->db->last_query();
        //print_r($result);


        //exit();

        //print_r($trans);

        $xls = array();

        //exit();
        if($pdf == 'print' || $pdf == 'pdf' || $pdf == 'xls'){
            $this->table->set_heading(
                'No.',
                'Delivery Time',
                'Delivery ID',
                'Type',
                'Delivery Fee',
                'COD Surchg',
                'Buyer',
                'Kode Toko',
                'Fulfillment / Order ID',
                'Status'
            ); // Setting headings for the table

            $xls[] = array(
                'No.',
                'Delivery Time',
                'Delivery ID',
                'Type',
                'Delivery Fee',
                'COD Surchg',
                'Buyer',
                'Kode Toko',
                'Fulfillment / Order ID',
                'Status'
            );

        }else{
            $this->table->set_heading(
                'No.',
                'No Kode Penjualan Toko',
                'Fulfillment / Order ID',
                'Delivery ID',
                'Merchant Name',
                'Store',
                'Delivery Date',
                'Buyer Name',
                'Delivery Type',
                'Status',
                'Total Price',
                'Disc',
                'Tax',
                'Delivery Chg',
                'COD Surchg',
                'Total Charge',
                'GMV'
            ); // Setting headings for the table

        }


        $seq = 1;
        $total_billing = 0;
        $total_delivery = 0;
        $total_cod = 0;
        $total_cod_val = 0;

        $total_payable = 0;

        $lastdate = '';

        foreach($rows->result() as $r){

            $app_id = $r->application_id;

            if($r->total_price == 0 || is_null($r->total_price) || $r->total_price == ''){
                if($r->chargeable_amount > 0){
                    $r->total_price = $r->chargeable_amount;
                }
            }

            /*
            if($r->delivery_type == 'COD' || $r->delivery_type == 'CCOD'){
                if($r->cod_cost == 0 || is_null($r->cod_cost) || $r->cod_cost == ''){
                    try{
                        //$app_id = get_app_id_from_key($r->application_key);
                        $r->cod_cost = get_cod_tariff($r->total_price,$app_id);
                    }catch(Exception $e){

                    }
                }

            }else{
                $r->cod_cost = 0;
            }


            if($r->delivery_cost == 0 || is_null($r->delivery_cost) || $r->delivery_cost == ''){
                try{
                    $r->delivery_cost = get_weight_tariff($r->actual_weight, $r->delivery_type ,$app_id);
                    //$r->delivery_cost = get_cod_tariff($r->total_price,$r->application_id);
                }catch(Exception $e){

                }
            }
            */

            //$total = str_replace(array(',','.'), '', $r->total_price);
            //$dsc = str_replace(array(',','.'), '', $r->total_discount);
            //$tax = str_replace(array(',','.'), '',$r->total_tax);
            //$dc = str_replace(array(',','.'), '',$r->delivery_cost);
            //$cod = str_replace(array(',','.'), '',$r->cod_cost);
            //$charge = str_replace(array(',','.'), '',$r->chargeable_amount);

            $total =  $r->total_price;
            $dsc =  $r->total_discount;
            $tax = $r->total_tax;
            $dc = $r->delivery_cost;
            $cod = $r->cod_cost;
            $charge = $r->chargeable_amount;

            $total = (is_nan( (double)$total))?0:(double)$total;
            $dsc = (is_nan((double)$dsc))?0:(double)$dsc;
            $tax = (is_nan((double)$tax))?0:(double)$tax;
            $dc = (is_nan((double)$dc))?0:(double)$dc;
            $cod = (is_nan((double)$cod))?0:(double)$cod;
            $charge = (is_nan((double)$charge))?0:(double)$charge;

            if($total == 0 && $charge > 0){
                $total = $charge;
            }

            $payable = 0;

            $payable = ($total - $dsc) + $tax;

            $total_payable += ($total - $dsc) + $tax;

            $total_delivery += (int)str_replace('.','',$dc);
            $total_cod += (int)str_replace('.','',$cod);
            //$total_billing += (int)str_replace('.','',$payable);

            //$codval = ($r->delivery_type == 'COD'|| $r->delivery_type == 'CCOD')?$payable:0;

            if($r->delivery_type == 'COD'|| $r->delivery_type == 'CCOD'){
                $codval = ($total - $dsc) + $tax + $dc + $cod;
            }else{
                //$cod = 0;
                $codval = $dc;
            }

            $total_cod_val += $codval;

            //$payable = str_replace('.','',$payable);

            $total_billing = $total_billing + (double)$payable;

            if($pdf == 'print' || $pdf == 'pdf' || $pdf == 'xls'){

                $this->table->add_row(
                    $seq,
                    date('d-m-Y',strtotime($r->assignment_date)),
                    $this->short_did($r->delivery_id),
                    $r->delivery_type,
                    array('data'=>idr($dc),'class'=>'currency'),
                    array('data'=>idr($cod),'class'=>'currency'),
                    $r->buyer_name,
                    $this->hide_trx($r->merchant_trans_id),
                    $r->fulfillment_code,
                    $r->status
                );

                $xls[] = array(
                    $seq,
                    date('d-m-Y',strtotime($r->assignment_date)),
                    $this->short_did($r->delivery_id),
                    $r->delivery_type,
                    idr($dc,false),
                    idr($cod,false),
                    $r->buyer_name,
                    $this->hide_trx($r->merchant_trans_id),
                    $r->fulfillment_code,
                    $r->status
                );


            }else{
                $this->table->add_row(
                    $seq,
                    $this->hide_trx($r->merchant_trans_id),
                    $r->fulfillment_code,
                    $this->short_did($r->delivery_id),
                    $r->fullname.'<hr />'.$r->merchant_name,
                    $r->app_name.'<hr />'.$r->domain,
                    date('d-m-Y',strtotime($r->assignment_date)),
                    $r->buyer_name,
                    $r->delivery_type,
                    $r->status,
                    array('data'=>idr($total),'class'=>'currency'),
                    array('data'=>idr($dsc),'class'=>'currency'),
                    array('data'=>idr($tax),'class'=>'currency'),
                    array('data'=>idr($dc),'class'=>'currency'),
                    array('data'=>idr($cod),'class'=>'currency'),
                    array('data'=>idr($codval),'class'=>'currency'),
                    array('data'=>idr($payable),'class'=>'currency')
                );


            }



            $seq++;
        }

            if($pdf == 'print' || $pdf == 'pdf' || $pdf == 'xls'){
                $this->table->add_row(
                    '',
                    '',
                    '',
                    '',
                    idr($total_delivery,false),
                    idr($total_cod,false),
                    '',
                    '',
                    ''
                );

                $xls[] = array(
                    '',
                    '',
                    '',
                    '',
                    idr($total_delivery,false),
                    idr($total_cod,false),
                    '',
                    '',
                    ''
                );

            }else{
                $this->table->add_row(
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    array('data'=>'Rp '.idr($total_delivery),'class'=>'currency total'),
                    array('data'=>'Rp '.idr($total_cod),'class'=>'currency total'),
                    array('data'=>'Rp '.idr($total_cod_val),'class'=>'currency total'),
                    array('data'=>'Rp '.idr($total_payable),'class'=>'currency')
                );
            }



        if($pdf == 'print' || $pdf == 'pdf'){

            $total_span = 2;
            $say_span = 4;

        }else{

            $total_span = 2;
            $say_span = 11;

        }


        $this->table->add_row(
            'Terbilang',
            array('data'=>'&nbsp;','colspan'=>$say_span)
        );

        if($type == 'Merchant' || $type == 'Global'){
            $this->table->add_row(
                array('data'=>'Payable',
                    'colspan'=>$total_span),
                array('data'=>idr($total_billing),
                    'colspan'=>$total_span,'class'=>'currency'),
                array('data'=>$this->number_words->to_words((double)$total_billing).' rupiah',
                    'colspan'=>$say_span)
            );
        }

        $this->table->add_row(
            array('data'=>'Delivery Charge',
                'colspan'=>$total_span),
            array('data'=>idr($total_delivery),
                'colspan'=>$total_span,'class'=>'currency'),
            array('data'=>$this->number_words->to_words($total_delivery).' rupiah',
                'colspan'=>$say_span)
        );

        $this->table->add_row(
            array('data'=>'COD Surcharge',
                'colspan'=>$total_span),
            array('data'=>idr($total_cod),
                'colspan'=>$total_span,'class'=>'currency'),
            array('data'=>$this->number_words->to_words($total_cod).' rupiah',
                'colspan'=>$say_span)
        );

        $this->table->add_row(
            array('data'=>'Grand Total',
                'colspan'=>$total_span),
            array('data'=>idr($total_delivery + $total_cod),
                'colspan'=>$total_span,'class'=>'currency'),
            array('data'=>$this->number_words->to_words($total_delivery + $total_cod).' rupiah',
                'colspan'=>$say_span)
        );

        $recontab = $this->table->generate();
        $data['recontab'] = $recontab;

        /* TOP SUMMARY TABLE */

        $this->table->clear();

        $tmpl = array( 'table_open'  => '<table style="width:400px;" border="0" cellpadding="0" cellspacing="0" class="mytable">' );

        $this->table->set_template($tmpl);

        $this->table->add_row(
            array('data'=>'<h4>Summary</h4>','colspan'=>2)
        );

        if($type == 'Merchant' || $type == 'Global'){
            $this->table->add_row(
                array('data'=>'Payable'),
                array('data'=>idr((double)$total_billing),'class'=>'currency')
            );
        }

        $this->table->add_row(
            array('data'=>'Delivery Charge'),
            array('data'=>idr($total_delivery),'class'=>'currency')
        );

        $this->table->add_row(
            array('data'=>'COD Surcharge'),
            array('data'=>idr($total_cod),'class'=>'currency')
        );

        $this->table->add_row(
            array('data'=>'Grand Total'),
            array('data'=>idr($total_delivery + $total_cod),'class'=>'currency')
        );

        $sumtab = $this->table->generate();
        $data['sumtab'] = $sumtab;


        /* end copy */

        $this->breadcrumb->add_crumb('Invoice','admin/reports/invoices');

        $page['ajaxurl'] = 'admin/reports/ajaxreconciliation';
        $page['page_title'] = 'Merchant Reconciliations';
        $data['select_title'] = 'Merchant';

        $data['controller'] = 'admin/reports/invoices/';

        $data['last_query'] = $last_query;

        $data['grand_total'] = $total_delivery + $total_cod;


        $data['merchantname'] = str_replace( array('http','www.',':','/','.com','.net','.co.id'),'',$data['merchantname']);

        $mname = strtoupper(str_replace(' ','_',$data['merchantname']));

        $pdffilename = 'JSM-'.$mname.'-'.$data['invdatenum'];

        $total_transfer  = $total_delivery + $total_cod;

        $txd['_gmv'] = idr((double)$total_billing,false);
        $txd['_total_delivery'] = idr($total_delivery + $total_cod,false);
        $txd['_total_do'] = idr($total_delivery,false);
        $txd['_total_cod'] = idr($total_cod,false);
        $txd['_total_transfer'] = idr($total_transfer,false);


        $txd['_att_to'] = $data['merchantname'];
        $txd['_date_from'] = $data['from'];
        $txd['_date_to'] = $data['to'];
        $txd['_doc_date'] = $data['invdate'];
        $txd['_doc_no'] = $data['invdatenum'];


        $txd['_total_do_say'] = $this->number_words->to_words((double) $total_delivery ).' rupiah';
        $txd['_total_cod_say'] = $this->number_words->to_words((double) $total_cod ).' rupiah';

        $txd['_total_transfer_say'] = $this->number_words->to_words((double) $total_transfer ).' rupiah';

        $txd['_account'] = $data['bank_account'];
        $txd['_payable'] = $data['merchantname'];
        $txd['_sender'] = 'Administrator';


        $xls = $this->compile_xls($xls, $txd, FCPATH.'public/xlstemplate/invoice.xlsx');

        if($pdf == 'pdf' || $pdf == 'xls'){

            if($pdf == 'pdf'){
                $html = $this->load->view('print/invoiceprint',$data,true);
                $pdf_name = $pdffilename;
                $pdfbuf = pdf_create($html, $pdf_name,'A4','landscape', false);

                file_put_contents(FCPATH.'public/invoices/'.$pdf_name.'.pdf', $pdfbuf);

            }else{

                $pdf_name = $pdffilename;

                $this->load->library('xlswrite');
                $xlswrite = new Xlswrite();
                $xlswrite->setActiveSheetIndex(0);


                $colnames = $this->config->item('xls_columns');

                $colindex = 0;

                //print_r($colnames);

                for($i = 0;$i < count($xls);$i++ ){
                    for($j = 0; $j < count($xls[$i]);$j++ ){
                        $cellname = $colnames[$j];
                        $cellname .= ($i+1);
                        //print $cellname .' = '.$xls[$i][$j]."\r\n";
                        $xlswrite->getActiveSheet()->SetCellValue($cellname, $xls[$i][$j] );
                    }
                }

                $xlswrite->xlsx(FCPATH.'public/invoices/'.$pdf_name.'.xlsx');

            }

            $data['invdate'] = iddate($invdate);
            $data['invdatenum'] = date('dmY',mysql_to_unix($invdate));


            $invdata = array(
                'merchant_id'=>$type,
                'merchantname'=>$data['merchantname'],
                'period_from'=>$data['from'],
                'period_to'=>$data['to'],
                'release_date'=>$invdate,
                'invoice_number'=>$pdffilename,
                'note'=>'',
                'filename'=>$pdffilename
            );

            $inres = $this->db->insert($this->config->item('invoice_table'),$invdata);


            if($pdf == 'pdf'){
                return array(file_exists(FCPATH.'public/invoices/'.$pdf_name.'.pdf'), $pdf_name.'.pdf');
            }else{
                return array(file_exists(FCPATH.'public/invoices/'.$pdf_name.'.xlsx'), $pdf_name.'.xlsx');
            }

        }else if($pdf == 'print'){
            $this->load->view('print/invoiceprint',$data); // Load the view
        }else{
            $this->ag_auth->view('invoicegenerator',$data); // Load the view
        }
    }

    public function geninvoice(){
        $type = null;
        $deliverytype = null;
        $status = null;
        $year = null;
        $scope = null;
        $par1 = null;
        $par2 = null;
        $par3 = null;
        $par4 = null;

        $type = $this->input->post('type');
        $deliverytype = $this->input->post('deliverytype');
        $status = $this->input->post('status');
        $year = $this->input->post('year');
        $scope = $this->input->post('scope');
        $par1 = $this->input->post('par1');
        $par2 = $this->input->post('par2');
        $par3 = $this->input->post('par3');
        $par4 = $this->input->post('par4');

        $result = $this->invoices($type ,$deliverytype,$status,$year, $scope, $par1, $par2, $par3,$par4);

        $result[0] = ($result[0])?'OK':'FAILED';

        print json_encode(array('result'=>$result[0], 'file'=>$result[1]));

    }

    //manifest

    public function manifests($type = null,$deliverytype = null,$zone = null,$merchant = null,$year = null, $scope = null, $par1 = null, $par2 = null, $par3 = null,$par4 = null){

        $type = (is_null($type))?'Global':$type;
        $id = (is_null($type))?'noid':$type;
        $mid = (is_null($merchant))?'noid':$merchant;
        $deliverytype = (is_null($deliverytype))?'noid':$deliverytype;

        if(is_null($scope)){
            $id = 'noid';
            $scope = 'month';
            $year = date('Y',time());
            $par1 = date('m',time());
        }

        $data['getparams'] = array(
            'type'=> $type ,
            'deliverytype'=>$deliverytype,
            'zone'=> $zone,
            'merchant'=> $merchant,
            'year'=> $year ,
            'scope'=>$scope ,
            'par1'=> $par1 ,
            'par2'=> $par2 ,
            'par3'=> $par3 ,
            'par4'=> $par4
            );

        $pdf = null;

        if($scope == 'month'){
            $days = cal_days_in_month(CAL_GREGORIAN, $par1, $year);
            $from = date('Y-m-d', strtotime($year.'/'.$par1.'/1'));
            $to =   date('Y-m-d', strtotime($year.'/'.$par1.'/'.$days));
            $pdf = $par2;
            $invdate = $par3;

            $data['getparams']['par2'] = 'pdf';

            $data['month'] = $par1;
            $data['week'] = 1;
        }else if($scope == 'week'){
            $from = date('Y-m-d', strtotime('1 Jan '.$year.' +'.($par1 - 1).' weeks'));
            $to = date('Y-m-d', strtotime('1 Jan '.$year.' +'.$par1.' weeks - 1 day'));
            $pdf = $par2;
            $invdate = $par3;

            $data['getparams']['par2'] = 'pdf';

            $data['month'] = 1;
            $data['week'] = $par1;
        }else if($scope == 'date'){
            $from = $par1;
            $to = $par2;
            $pdf = $par3;
            $invdate = $par4;

            $data['getparams']['par3'] = 'pdf';

            $data['month'] = 1;
            $data['week'] = 1;
        }else{
            $from = date('Y-m-d',time());
            $to = date('Y-m-d',time());
            $pdf = null;
            $invdate = null;

            $data['getparams']['par2'] = 'pdf';

            $data['month'] = 1;
            $data['week'] = 1;
        }

        $data['year'] = $year;
        $data['from'] = $from;
        $data['to'] = $to;

        $clist = get_device_list();

        $cs = array('noid'=>'All');
        foreach ($clist as $ckey) {
            $cs[$ckey->id] = $ckey->identifier;
        }

        $data['zone'] = urldecode($zone);
        $data['merchants'] = $cs;
        $data['id'] = $id;


        $mclist = get_merchant(null,false);

        $mcs = array('noid'=>'All');
        foreach ($mclist as $mckey) {
            $mcs[$mckey['id']] = $mckey['merchantname'].' - '.$mckey['fullname'];
        }

        $data['deliverytypes'] = $this->config->item('deliverytype_selector');

        $data['merchantlist'] = $mcs;
        $data['mid'] = $mid;

        /* copied from print controller */

        $this->load->library('number_words');

        if($id == 'noid'){
            $data['type_name'] = '-';
            $data['bank_account'] = 'n/a';
            $data['type'] = 'Global';

            $data['merchantname'] = 'All Device';

        }else{
            $user = $this->db->where('id',$id)->get($this->config->item('jayon_devices_table'))->row();
            //print $this->db->last_query();
            $data['type'] = $user->identifier;
            $data['type_name'] = $user->identifier;
            $data['bank_account'] = 'n/a';

            $data['merchantname'] = $user->identifier;
        }

        if($deliverytype == 'noid'){
            $data['dtype'] = 'All Type';
        }else{
            $data['dtype'] = $deliverytype;
        }

        if($mid == 'noid'){
            $data['merchantinfo'] = 'All Merchant';
        }else{
            $member = $this->db->where('id',$mid)->get($this->config->item('jayon_members_table'))->row();
            //print $this->db->last_query();
            //$data['type'] = $member->merchantname.' - '.$member->fullname;
            //$data['type_name'] = $member->fullname;
            //$data['bank_account'] = 'n/a';

            $data['merchantinfo'] = $member->merchantname;
        }

        if($data['zone'] == 'all'){
            $data['zone'] = 'All zones';
        }

        if(is_null($invdate)){
            $data['invdate'] = '-';
            $data['invdatenum'] = '-';
        }else{
            $data['invdate'] = iddate($invdate);
            $data['invdatenum'] = date('dmY',mysql_to_unix($invdate)) ;
        }

        $data['period'] = $from.' s/d '.$to;

        $sfrom = date('Y-m-d',strtotime($from));
        $sto = date('Y-m-d',strtotime($to));

        $mtab = $this->config->item('assigned_delivery_table');

        $this->db->select('assignment_date,delivery_id,'.$mtab.'.merchant_id as merchant_id,cod_bearer,delivery_bearer,buyer_name,buyerdeliveryzone,pending_count,c.fullname as courier_name,'.$mtab.'.phone,'.$mtab.'.mobile1,'.$mtab.'.mobile2,merchant_trans_id,m.merchantname as merchant_name, m.fullname as fullname, a.application_name as app_name, a.domain as domain ,delivery_type,shipping_address,status,pickup_status,warehouse_status,cod_cost,delivery_cost,total_price,chargeable_amount,total_tax,total_discount')
            ->join('members as m',$this->config->item('incoming_delivery_table').'.merchant_id=m.id','left')
            ->join('applications as a',$this->config->item('assigned_delivery_table').'.application_id=a.id','left')
            ->join('devices as d',$this->config->item('assigned_delivery_table').'.device_id=d.id','left')
            ->join('couriers as c',$this->config->item('assigned_delivery_table').'.courier_id=c.id','left')
            //->like('assignment_date',$date,'before')
            ->from($this->config->item('incoming_delivery_table'));

        $column = 'assignment_date';
        $daterange = sprintf("`%s`between '%s%%' and '%s%%' ", $column, $sfrom, $sto);

        $this->db->where($daterange, null, false);
        $this->db->where($column.' != ','0000-00-00');

        if($id != 'noid'){
            $this->db->where($this->config->item('assigned_delivery_table').'.device_id',$id);
        }

        if($deliverytype != 'noid'){
            if($deliverytype == 'DO'){
                $deliverytype = 'Delivery Only';
                $this->db->where($this->config->item('assigned_delivery_table').'.delivery_type',$deliverytype);
            }else if($deliverytype == 'COD'){
                $this->db->like($this->config->item('assigned_delivery_table').'.delivery_type',$deliverytype,'before');
            }
        }

        if($mid != 'noid'){
            $this->db->where($this->config->item('assigned_delivery_table').'.merchant_id',$mid);
        }

        if($zone != 'all'){
            $zone = urldecode($zone);
            $this->db->where($this->config->item('assigned_delivery_table').'.buyerdeliveryzone',$zone);
        }

        $this->db->order_by('buyerdeliverycity','asc')->order_by('buyerdeliveryzone','asc');

        $this->db->and_();
        $this->db->group_start()
            ->where('status',$this->config->item('trans_status_admin_courierassigned'))
            ->or_where('status',$this->config->item('trans_status_mobile_pickedup'))
            ->or_where('status',$this->config->item('trans_status_mobile_enroute'))
            ->or_()
                ->group_start()
                    ->where('status',$this->config->item('trans_status_new'))
                    ->where('pending_count >', 0)
                ->group_end()
            ->group_end();


        //print $this->db->last_query();

        if($pdf == 'csv'){

            $result = $this->db->get()->result_array();

            // Open the output stream
            $fh = fopen('php://output', 'w');

            // Start output buffering (to capture stream contents)
            ob_start();

            // Loop over the * to export
            if (! empty($result)) {
                $headers = array_keys($result[0]);
                    fputcsv($fh, $headers);
                foreach ($result as $item) {
                    fputcsv($fh, $item);
                }
            }

            // Get the contents of the output buffer
            $string = ob_get_clean();

            $filename = str_replace('/', '_', uri_string()).'.csv';

            // Output CSV-specific headers
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false);
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '";');
            header('Content-Transfer-Encoding: binary');

            exit($string);
        }


        $rows = $this->db->get();

        $trans = $rows->result();

        $last_query = $this->db->last_query();
        //print_r($result);


        //exit();

        //print_r($trans);

        //exit();

        if($pdf == 'print' || $pdf == 'pdf'){
            $this->table->set_heading(
                'No.',
                'Zone',
                'TOKO ONLINE',
                'Type',
                'Status',
                'KEPADA',
                'Total Price',
                'Delivery Charge',
                'COD Surcharge',
                'Total Charge',
                'ALAMAT',
                'Phone',
                'No Kode Penjualan Toko',
                array('data'=>'PENERIMA PAKET','colspan'=>2)


            ); // Setting headings for the table

            $this->table->set_subheading(
                array('data'=>'Mohon tunjukkan kartu identitas untuk di foto sebagai bagian bukti penerimaan','style'=>'text-align:center;','colspan'=>13),
                /*
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                */
                array('data'=>'TANDA TANGAN','style'=>'min-width:100px;'),
                array('data'=>'NAMA','style'=>'min-width:100px;')

            ); // Setting headings for the table

        }else{

            $this->table->set_heading(
                'No.',
                'Zone',
                'TOKO ONLINE',
                'Type',
                'Status',
                'KEPADA',
                'Total Price',
                'Delivery Charge',
                'COD Surcharge',
                'Total Charge',
                'ALAMAT',
                'Phone',
                'No Kode Penjualan Toko',
                array('data'=>'PENERIMA PAKET','colspan'=>2)


            ); // Setting headings for the table

            $this->table->set_subheading(
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                array('data'=>'TANDA TANGAN','style'=>'min-width:100px;'),
                array('data'=>'NAMA','style'=>'min-width:100px;')

            ); // Setting headings for the table

        }



        $seq = 1;
        $total_billing = 0;
        $total_delivery = 0;
        $total_cod = 0;

        $d = 0;
        $gt = 0;

        $lastdate = '';

        $courier_name = '';

        $counts = array(
                'Delivery Only'=>0,
                'COD'=>0,
                'CCOD'=>0,
                'PS'=>0,
                'pending'=>0
            );

        foreach($rows->result() as $r){

            $counts[$r->delivery_type] += 1;

            if( $r->pending_count > 0){
                $counts['pending'] += 1;
            }

            $courier_name = $r->courier_name;
            //$total = str_replace(array(',','.'), '', $r->total_price);
            //$dsc = str_replace(array(',','.'), '', $r->total_discount);
            //$tax = str_replace(array(',','.'), '',$r->total_tax);
            //$dc = str_replace(array(',','.'), '',$r->delivery_cost);
            //$cod = str_replace(array(',','.'), '',$r->cod_cost);

            $total = $r->total_price;
            $dsc = $r->total_discount;
            $tax = $r->total_tax;
            $dc = $r->delivery_cost;
            $cod = $r->cod_cost;

            $total = (is_nan((double)$total))?0:(double)$total;
            $dsc = (is_nan((double)$dsc))?0:(double)$dsc;
            $tax = (is_nan((double)$tax))?0:(double)$tax;
            $dc = (is_nan((double)$dc))?0:(double)$dc;
            $cod = (is_nan((double)$cod))?0:(double)$cod;

            $payable = 0;


            $details = $this->db->where('delivery_id',$r->delivery_id)->order_by('unit_sequence','asc')->get($this->config->item('delivery_details_table'));

            $details = $details->result_array();


            $d = 0;
            $gt = 0;

            foreach($details as $value => $key)
            {

                $u_total = $key['unit_total'];
                $u_discount = $key['unit_discount'];
                $gt += (is_nan((double)$u_total))?0:(double)$u_total;
                $d += (is_nan((double)$u_discount))?0:(double)$u_discount;

            }

            if($gt == 0 ){
                if($total > 0 && $payable)
                $gt = $total;
            }

            $payable = $gt;

            $total_delivery += (double)$dc;
            $total_cod += (double)$cod;
            $total_billing += (double)$payable;

            $db = '';
            if($r->delivery_bearer == 'merchant'){
                $dc = 0;
                $db = 'M';
            }else{
                $db = 'B';
            }

            //force all DO to zero

            $cb = '';
            if($r->cod_bearer == 'merchant'){
                $cod = 0;
                $cb = 'M';
            }else{
                $cb = 'B';
            }

            $codclass = '';

            if($r->delivery_type == 'COD' || $r->delivery_type == 'CCOD'){
                $chg = ($gt - $dsc) + $tax + $dc + $cod;

                //$chg = $gt + $dc + $cod;

                $codclass = 'cod';

            }else{
                $dc = 0;
                $cod = 0;
                $chg = $dc;
            }




            if($pdf == 'print' || $pdf == 'pdf'){

                $this->table->add_row(
                    $seq,
                    $r->buyerdeliveryzone,
                    $r->merchant_name,
                    array('data'=>colorizetype($r->delivery_type),'class'=>'currency '.$codclass),
                    $r->status.'<br /><br />'.$r->pickup_status.'<br /><br />'.$r->warehouse_status,
                    $r->buyer_name,
                    array('data'=>( $payable == 0 )?0:idr($payable),'class'=>'currency '.$codclass),
                    array('data'=>( $dc == 0 )?0:idr($dc),'class'=>'currency '.$codclass,'style'=>'position:relative;'),
                    array('data'=>( $cod == 0 )?0:idr($cod),'class'=>'currency '.$codclass,'style'=>'position:relative;'),
                    array('data'=>( $chg == 0 )?0:idr($chg),'class'=>'currency '.$codclass),
                    $r->shipping_address,
                    $this->split_phone($r->phone).'<br />'.$this->split_phone($r->mobile1).'<br />'.$this->split_phone($r->mobile2),
                    $this->hide_trx($r->merchant_trans_id),
                    '',
                    ''
                );


            }else{
                $this->table->add_row(
                    $seq,
                    $r->buyerdeliveryzone,
                    $r->merchant_name,
                    array('data'=>colorizetype($r->delivery_type),'class'=>'currency '.$codclass),
                    $r->status.'<br /><br />'.$r->pickup_status.'<br /><br />'.$r->warehouse_status,
                    $r->buyer_name,
                    array('data'=>( $payable == 0 )?0:idr($payable),'class'=>'currency '.$codclass),
                    array('data'=>( $dc == 0 )?0:idr($dc).'<span class="bearer">'.$db.'</span>','class'=>'currency '.$codclass,'style'=>'position:relative;'),
                    array('data'=>( $cod == 0 )?0:idr($cod).'<span class="bearer">'.$cb.'</span>','class'=>'currency '.$codclass,'style'=>'position:relative;'),
                    array('data'=>( $chg == 0 )?0:idr($chg),'class'=>'currency '.$codclass),
                    $r->shipping_address,
                    $this->split_phone($r->phone).'<br />'.$this->split_phone($r->mobile1).'<br />'.$this->split_phone($r->mobile2),
                    $this->hide_trx($r->merchant_trans_id),
                    '',
                    ''
                );


            }

            $seq++;
        }

        /*

            if($pdf == 'print' || $pdf == 'pdf'){
                $this->table->add_row(
                    '',
                    '',
                    '',
                    '',
                    array('data'=>'Rp '.idr($total_delivery),'class'=>'currency total'),
                    array('data'=>'Rp '.idr($total_cod),'class'=>'currency total'),
                    '',
                    '',
                    ''
                );
            }
        */



        if($pdf == 'print' || $pdf == 'pdf'){

            $total_span = 2;
            $say_span = 6;

        }else{

            $total_span = 12;
            $say_span = 13;

        }

        /*
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
            array('data'=>'Delivery Charge',
                'colspan'=>$total_span),
            array('data'=>$this->number_words->to_words($total_delivery).' rupiah',
                'colspan'=>$say_span)
        );

        $this->table->add_row(
            array('data'=>'COD Surcharge',
                'colspan'=>$total_span),
            array('data'=>$this->number_words->to_words($total_cod).' rupiah',
                'colspan'=>$say_span)
        );

        $this->table->add_row(
            array('data'=>'Grand Total',
                'colspan'=>$total_span),
            array('data'=>$this->number_words->to_words($total_delivery + $total_cod).' rupiah',
                'colspan'=>$say_span)
        );
        */

        $recontab = $this->table->generate();
        $data['recontab'] = $recontab;

        $data['summary_count'] = $counts;
        /* end copy */

        $this->breadcrumb->add_crumb('Manifest','admin/reports/manifests');

        $page['ajaxurl'] = 'admin/reports/ajaxreconciliation';
        $page['page_title'] = 'Manifest';
        $data['select_title'] = 'Device';
        $data['zone_select_title'] = 'Zone';


        $data['controller'] = 'admin/reports/manifests/';

        $data['last_query'] = $last_query;

        $data['grand_total'] = $total_delivery + $total_cod;


        $data['zones'] = array_merge( array('all'=>'All'), get_zone_options() ) ;

        $data['courier_name'] = $courier_name;

        $data['merchantname'] = str_replace( array('http','www.',':','/','.com','.net','.co.id'),'',$data['merchantname']);
        $data['merchantinfo'] = str_replace( array('http','www.',':','/','.com','.net','.co.id'),'',$data['merchantinfo']);

        $zonename = strtoupper(str_replace(' ', '_', $data['zone']));
        $mname = strtoupper(str_replace(' ','_',$data['merchantname']));
        $minfo = strtoupper(str_replace(' ','_',$data['merchantinfo']));

        $pdffilename = 'JSM-'.$mname.'-'.$minfo.'-'.$zonename.'-'.$data['invdatenum'];

        if($pdf == 'pdf'){
            $html = $this->load->view('print/manifestprint',$data,true);
            $pdf_name = $pdffilename;
            $pdfbuf = pdf_create($html, $pdf_name,'A3','landscape', false);

            file_put_contents(FCPATH.'public/manifests/'.$pdf_name.'.pdf', $pdfbuf);

            $data['invdate'] = iddate($invdate);
            $data['invdatenum'] = date('dmY',mysql_to_unix($invdate));


            $invdata = array(
                'merchant_id'=>$type,
                'merchantname'=>$data['merchantname'],
                'merchantinfo'=>$data['merchantinfo'],
                'period_from'=>$data['from'],
                'period_to'=>$data['to'],
                'release_date'=>$invdate,
                'invoice_number'=>$pdffilename,
                'note'=>'',
                'filename'=>$pdffilename
            );

            $inres = $this->db->insert($this->config->item('manifest_table'),$invdata);

            return array(file_exists(FCPATH.'public/manifests/'.$pdf_name.'.pdf'), $pdf_name.'.pdf');

        }else if($pdf == 'print'){
            $this->load->view('print/manifestprint',$data); // Load the view
        }else{
            $this->ag_auth->view('manifestgenerator',$data); // Load the view
        }
    }

    public function genmanifest(){
        $type = null;
        $deliverytype = null;
        $zone = null;
        $merchant = null;
        $year = null;
        $scope = null;
        $par1 = null;
        $par2 = null;
        $par3 = null;
        $par4 = null;

        $type = $this->input->post('type');
        $deliverytype = $this->input->post('deliverytype');
        $zone = $this->input->post('zone');
        $merchant = $this->input->post('merchant');
        $year = $this->input->post('year');
        $scope = $this->input->post('scope');
        $par1 = $this->input->post('par1');
        $par2 = $this->input->post('par2');
        $par3 = $this->input->post('par3');
        $par4 = $this->input->post('par4');

        $result = $this->manifests($type,$deliverytype ,$zone,$merchant,$year, $scope, $par1, $par2, $par3,$par4);

        $result[0] = ($result[0])?'OK':'FAILED';

        print json_encode(array('result'=>$result[0], 'file'=>$result[1]));

    }

    //delivery time report

    public function deliverytime($type = null,$deliverytype = null,$zone = null,$merchant = null,$status = null,$year = null, $scope = null, $par1 = null, $par2 = null, $par3 = null,$par4 = null){

        $type = (is_null($type))?'Global':$type;
        $id = (is_null($type))?'noid':$type;
        $mid = (is_null($merchant))?'noid':$merchant;
        $deliverytype = (is_null($deliverytype))?'noid':$deliverytype;
        $status = (is_null($status))?'all':$status;

        if(is_null($scope)){
            $id = 'noid';
            $scope = 'month';
            $year = date('Y',time());
            $par1 = date('m',time());
        }

        $data['getparams'] = array(
            'type'=> $type ,
            'deliverytype'=>$deliverytype,
            'zone'=> $zone,
            'merchant'=> $merchant,
            'status'=> $status,
            'year'=> $year ,
            'scope'=>$scope ,
            'par1'=> $par1 ,
            'par2'=> $par2 ,
            'par3'=> $par3 ,
            'par4'=> $par4
            );

        $pdf = null;

        if($scope == 'month'){
            $days = cal_days_in_month(CAL_GREGORIAN, $par1, $year);
            $from = date('Y-m-d', strtotime($year.'/'.$par1.'/1'));
            $to =   date('Y-m-d', strtotime($year.'/'.$par1.'/'.$days));
            $pdf = $par2;
            $invdate = $par3;

            $data['getparams']['par2'] = 'pdf';

            $data['month'] = $par1;
            $data['week'] = 1;
        }else if($scope == 'week'){
            $from = date('Y-m-d', strtotime('1 Jan '.$year.' +'.($par1 - 1).' weeks'));
            $to = date('Y-m-d', strtotime('1 Jan '.$year.' +'.$par1.' weeks - 1 day'));
            $pdf = $par2;
            $invdate = $par3;

            $data['getparams']['par2'] = 'pdf';

            $data['month'] = 1;
            $data['week'] = $par1;
        }else if($scope == 'date'){
            $from = $par1;
            $to = $par2;
            $pdf = $par3;
            $invdate = $par4;

            $data['getparams']['par3'] = 'pdf';

            $data['month'] = 1;
            $data['week'] = 1;
        }else{
            $from = date('Y-m-d',time());
            $to = date('Y-m-d',time());
            $pdf = null;
            $invdate = null;

            $data['getparams']['par2'] = 'pdf';

            $data['month'] = 1;
            $data['week'] = 1;
        }

        $data['year'] = $year;
        $data['from'] = $from;
        $data['to'] = $to;

        $clist = get_device_list();

        $cs = array('noid'=>'All');
        foreach ($clist as $ckey) {
            $cs[$ckey->id] = $ckey->identifier;
        }

        $data['zone'] = urldecode($zone);
        $data['merchants'] = $cs;
        $data['id'] = $id;


        $mclist = get_merchant(null,false);

        $mcs = array('noid'=>'All');
        foreach ($mclist as $mckey) {
            $mcs[$mckey['id']] = $mckey['merchantname'].' - '.$mckey['fullname'];
        }

        $data['deliverytypes'] = $this->config->item('deliverytype_selector');

        $data['merchantlist'] = $mcs;
        $data['mid'] = $mid;

        $data['statuslist'] = array_merge(array('all'=>'All'), $this->config->item('status_list') );
        $data['stid'] = $status;

        /* copied from print controller */

        $this->load->library('number_words');

        if($id == 'noid'){
            $data['type_name'] = '-';
            $data['bank_account'] = 'n/a';
            $data['type'] = 'Global';

            $data['merchantname'] = 'All Device';

        }else{
            $user = $this->db->where('id',$id)->get($this->config->item('jayon_devices_table'))->row();
            //print $this->db->last_query();
            $data['type'] = $user->identifier;
            $data['type_name'] = $user->identifier;
            $data['bank_account'] = 'n/a';

            $data['merchantname'] = $user->identifier;
        }


        if($deliverytype == 'noid'){
            $data['dtype'] = 'All Type';
        }else{
            $data['dtype'] = $deliverytype;
        }

        if($mid == 'noid'){
            $data['merchantinfo'] = 'All Merchant';
        }else{
            $member = $this->db->where('id',$mid)->get($this->config->item('jayon_members_table'))->row();
            //print $this->db->last_query();
            //$data['type'] = $member->merchantname.' - '.$member->fullname;
            //$data['type_name'] = $member->fullname;
            //$data['bank_account'] = 'n/a';

            $data['merchantinfo'] = $member->merchantname;
        }

        if($data['zone'] == 'all'){
            $data['zone'] = 'All zones';
        }

        if(is_null($invdate)){
            $data['invdate'] = '-';
            $data['invdatenum'] = '-';
        }else{
            $data['invdate'] = iddate($invdate);
            $data['invdatenum'] = date('dmY',mysql_to_unix($invdate)) ;
        }

        $data['period'] = $from.' s/d '.$to;

        $sfrom = date('Y-m-d',strtotime($from));
        $sto = date('Y-m-d',strtotime($to));

        $mtab = $this->config->item('assigned_delivery_table');

        $this->db->select('assignment_date,ordertime,deliverytime,delivery_note,pending_count,recipient_name,delivery_id,'.$mtab.'.merchant_id as merchant_id,cod_bearer,delivery_bearer,buyer_name,buyerdeliveryzone,c.fullname as courier_name,'.$mtab.'.phone,'.$mtab.'.mobile1,'.$mtab.'.mobile2,merchant_trans_id,m.merchantname as merchant_name, m.fullname as fullname, a.application_name as app_name, a.domain as domain ,delivery_type,shipping_address,status,pickup_status,warehouse_status,cod_cost,delivery_cost,total_price,total_tax,total_discount')
            ->join('members as m',$this->config->item('incoming_delivery_table').'.merchant_id=m.id','left')
            ->join('applications as a',$this->config->item('assigned_delivery_table').'.application_id=a.id','left')
            ->join('devices as d',$this->config->item('assigned_delivery_table').'.device_id=d.id','left')
            ->join('couriers as c',$this->config->item('assigned_delivery_table').'.courier_id=c.id','left')
            //->like('assignment_date',$date,'before')
            ->from($this->config->item('incoming_delivery_table'));

        $column = 'ordertime';
        $daterange = sprintf("`%s`between '%s%%' and '%s%%' ", $column, $sfrom, $sto);

        $this->db->where($daterange, null, false);
        $this->db->where($column.' != ','0000-00-00');

        if($id != 'noid'){
            $this->db->where($this->config->item('assigned_delivery_table').'.device_id',$id);
        }


        if($deliverytype != 'noid'){
            if($deliverytype == 'DO'){
                $deliverytype = 'Delivery Only';
                $this->db->where($this->config->item('assigned_delivery_table').'.delivery_type',$deliverytype);
            }else if($deliverytype == 'COD'){
                $this->db->like($this->config->item('assigned_delivery_table').'.delivery_type',$deliverytype,'before');
            }
        }

        if($mid != 'noid'){
            $this->db->where($this->config->item('assigned_delivery_table').'.merchant_id',$mid);
        }

        if($zone != 'all'){
            $zone = urldecode($zone);
            $this->db->where($this->config->item('assigned_delivery_table').'.buyerdeliveryzone',$zone);
        }

        $this->db->order_by('buyerdeliverycity','asc')->order_by('buyerdeliveryzone','asc');


        if($status != 'all'){
            $this->db->where($this->config->item('assigned_delivery_table').'.status',$status);
        }else{
            $this->db->and_();

            $this->db->group_start()
                ->where('status',   $this->config->item('trans_status_mobile_delivered'))
                //->where('status',$this->config->item('trans_status_admin_courierassigned'))
                ->or_where('status',$this->config->item('trans_status_new'))
                ->or_where('status',$this->config->item('trans_status_rescheduled'))
                ->or_where('status',$this->config->item('trans_status_mobile_return'))
                //->or_where('status',$this->config->item('trans_status_mobile_enroute'))
                /*
                ->or_()
                    ->group_start()
                        ->where('status',$this->config->item('trans_status_new'))
                        ->where('pending_count >', 0)
                    ->group_end()
                */

                ->group_end();

        }


        //print $this->db->last_query();

        if($pdf == 'ocsv'){


            $result = $this->db->get()->result_array();

            if(!empty($result)){
                $addhead = array('order2assign'=>'order2assign', 'assign2delivery'=>'assign2delivery', 'order2delivery'=>'order2delivery');

                $order2deliverydays = 0;
                $order2assigndays = 0;
                $assign2deliverydays = 0;

                for($i = 0; $i < count($result) ; $i++){
                    /*
                    if($i == 0){
                        $result[$i] = array_merge($addhead, $result[$i]);
                    }else{
                    */
                        $ordertime = new DateTime($result[$i]['ordertime']);
                        $assignment_date = new DateTime($result[$i]['assignment_date']);
                        $deliverytime = new DateTime($result[$i]['deliverytime']);

                        $order2assign = $ordertime->diff($assignment_date);

                        $assign2delivery = $assignment_date->diff($deliverytime);

                        $order2delivery = $ordertime->diff($deliverytime);

                        if(is_null($deliverytime) || $deliverytime == ''){
                            $assign2delivery->d = 0;
                            $order2delivery->d = 0;
                            $order2assigndays += (int)$order2assign->d ;
                        }else{
                            $order2assigndays += (int)$order2assign->d ;
                            $assign2deliverydays += (int)$assign2delivery->d ;
                            $order2deliverydays += (int)$order2delivery->d;
                        }

                        $addres = array('order2assign'=>$order2assign->d,
                            'assign2delivery'=>$assign2delivery->d,
                            'order2delivery'=>$order2delivery->d);
                        $result[$i]['shipping_address'] = str_replace(array('"',"/n","/r"), '', $result[$i]['shipping_address']);
                        $result[$i] = array_merge($addres, $result[$i]);

                    //}

                }

            }

            // Open the output stream
            $fh = fopen('php://output', 'w');

            // Start output buffering (to capture stream contents)
            ob_start();

            // Loop over the * to export
            if (! empty($result)) {
                $headers = array_keys($result[0]);
                    fputcsv($fh, $headers,',','"');
                foreach ($result as $item) {
                    fputcsv($fh, $item,',','"');
                }
            }

            // Get the contents of the output buffer
            $string = ob_get_clean();

            $filename = str_replace('/', '_', uri_string()).'.csv';

            // Output CSV-specific headers
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false);
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '";');
            header('Content-Transfer-Encoding: binary');

            exit($string);
        }


        $rows = $this->db->get();

        $trans = $rows->result();

        $last_query = $this->db->last_query();
        //print_r($result);


        //exit();

        //print_r($trans);

        //exit();

        if($pdf == 'print' || $pdf == 'pdf'){
            $this->table->set_heading(
                'No.',
                'TOKO ONLINE',
                'Type',
                'Tgl Upload',
                //'Upload -> Kirim',
                'Tgl Kirim',
                'Kirim -> Diterima',
                'Tgl Diterima',
                //'Upload -> Diterima',
                'Status',
                'Pending',
                'Catatan',
                'ALAMAT',
                'Delivery ID<hr />No Kode Penjualan Toko'

            ); // Setting headings for the table
            /*
            $this->table->set_subheading(
                array('data'=>'Mohon tunjukkan kartu identitas untuk di foto sebagai bagian bukti penerimaan','style'=>'text-align:center;','colspan'=>13),

                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',

                array('data'=>'TANDA TANGAN','style'=>'min-width:100px;'),
                array('data'=>'NAMA','style'=>'min-width:100px;')

            );*/ // Setting headings for the table

        }else if($pdf == 'csv'){
            $csv_header = array(
                'No.',
                'TOKO ONLINE',
                'Type',
                'Tgl Upload',
                //'Upload -> Kirim',
                'Tgl Kirim',
                'Kirim -> Diterima',
                'Tgl Diterima',
                //'Upload -> Diterima',
                'Status',
                'Pending',
                'Catatan',
                'ALAMAT',
                'Delivery ID',
                'No Kode Penjualan Toko'
            );


        }else{

            $this->table->set_heading(
                'No.',
                'TOKO ONLINE',
                'Type',
                'Tgl Upload',
                //'Upload -> Kirim',
                'Tgl Kirim',
                'Kirim -> Diterima',
                'Tgl Diterima',
                //'Upload -> Diterima',
                'Status',
                'Pending',
                'Catatan',
                'ALAMAT',
                'Delivery ID<hr />No Kode Penjualan Toko'
            ); // Setting headings for the table

            /*
            $this->table->set_subheading(
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                array('data'=>'TANDA TANGAN','style'=>'min-width:100px;'),
                array('data'=>'NAMA','style'=>'min-width:100px;')

            ); */ // Setting headings for the table

        }



        $seq = 1;
        $total_billing = 0;
        $total_delivery = 0;
        $total_cod = 0;

        $d = 0;
        $gt = 0;

        $lastdate = '';

        $courier_name = '';

        $order2assigndays = 0;
        $assign2deliverydays = 0;
        $order2deliverydays = 0;

        $csv_data = array();

        foreach($rows->result() as $r){


            $courier_name = $r->courier_name;
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

            $otime = date('Y-m-d',strtotime($r->ordertime));
            $dtime = date('Y-m-d',strtotime($r->deliverytime));

            $ordertime = new DateTime($otime);
            $assignment_date = new DateTime($r->assignment_date);
            $deliverytime = new DateTime($dtime);

            $order2assign = $ordertime->diff($assignment_date);

            $assign2delivery = $assignment_date->diff($deliverytime);

            $order2delivery = $ordertime->diff($deliverytime);

            if(is_null($deliverytime) || $deliverytime == ''){
                $assign2delivery->d = 0;
                $order2delivery->d = 0;
                $order2assigndays += (int)$order2assign->d ;
            }else{
                $order2assigndays += (int)$order2assign->d ;
                $assign2deliverydays += (int)$assign2delivery->d ;
                $order2deliverydays += (int)$order2delivery->d;
            }



            $details = $this->db->where('delivery_id',$r->delivery_id)
                            ->and_()
                            ->group_start()
                                ->where('status',   $this->config->item('trans_status_mobile_delivered'))
                                ->or_where('status',$this->config->item('trans_status_new'))
                                ->or_where('status',$this->config->item('trans_status_rescheduled'))
                                ->or_where('status',$this->config->item('trans_status_mobile_return'))
                            ->group_end()
                            ->order_by('timestamp','desc')
                            ->get($this->config->item('delivery_log_table'));

            $details = $details->result_array();


            $d = 0;
            $gt = 0;

            $notes = '';

            foreach($details as $d )
            {
                $n = '';
                if($d['api_event'] == 'admin_change_status'){
                    $n = $d['req_note'];
                }else{
                    if($d['notes'] != ''){
                        $n = $d['notes'];
                    }
                }

                if($n != ''){
                    if($pdf == 'csv'){
                        $notes .= $d['timestamp']."\n";
                        $notes .= $d['status']."\n";
                        $notes .= $n." |\n";
                    }else{
                        $notes .= $d['timestamp'].'<br />';
                        $notes .= '<b>'.$d['status'].'</b><br />';
                        $notes .= $n.'<br />';

                    }
                }

            }

            /*
            $payable = $gt;

            $total_delivery += (int)str_replace('.','',$dc);
            $total_cod += (int)str_replace('.','',$cod);
            $total_billing += (int)str_replace('.','',$payable);

            $db = '';
            if($r->delivery_bearer == 'merchant'){
                $dc = 0;
                $db = 'M';
            }else{
                $db = 'B';
            }

            //force all DO to zero

            $cb = '';
            if($r->cod_bearer == 'merchant'){
                $cod = 0;
                $cb = 'M';
            }else{
                $cb = 'B';
            }

            $codclass = '';

            if($r->delivery_type == 'COD' || $r->delivery_type == 'CCOD'){
                $chg = ($gt - $dsc) + $tax + $dc + $cod;

                //$chg = $gt + $dc + $cod;

                $codclass = 'cod';

            }else{
                $dc = 0;
                $cod = 0;
                $chg = $dc;
            }
            */



            if($pdf == 'print' || $pdf == 'pdf'){
                $this->table->add_row(
                    $seq,
                    $r->merchant_name,
                    array('data'=>colorizetype($r->delivery_type),'class'=>'currency'),
                    $r->ordertime,
                    //$order2assign->d,
                    $r->assignment_date,
                    $assign2delivery->d,
                    $r->deliverytime,
                    //$order2delivery->d,
                    $r->status,
                    $r->pending_count,
                    $notes,
                    '<b>'.$r->recipient_name.'</b><br />'.$r->shipping_address.'<br />'.$this->split_phone($r->phone).'<br />'.$this->split_phone($r->mobile1).'<br />'.$this->split_phone($r->mobile2),
                    $r->delivery_id.'<hr />'.$this->hide_trx($r->merchant_trans_id)
                );
            }else if($pdf == 'csv'){
                $csv_data[] = array(
                    $seq,
                    $r->merchant_name,
                    $r->delivery_type,
                    $r->ordertime,
                    //$order2assign->d,
                    $r->assignment_date,
                    $assign2delivery->d,
                    $r->deliverytime,
                    //$order2delivery->d,
                    $r->status,
                    $r->pending_count,
                    $notes,
                    $r->recipient_name.' | '.str_replace(array(",",'"',"\n","\r"), '', $r->shipping_address ).' '.$this->split_phone($r->phone).' '.$this->split_phone($r->mobile1).' '.$this->split_phone($r->mobile2),
                    $r->delivery_id,
                    $this->hide_trx($r->merchant_trans_id)
                );
            }else{


                $this->table->add_row(
                    $seq,
                    $r->merchant_name,
                    array('data'=>colorizetype($r->delivery_type),'class'=>'currency'),
                    $r->ordertime,
                    //$order2assign->d,
                    $r->assignment_date,
                    $assign2delivery->d,
                    $r->deliverytime,
                    //$order2delivery->d,
                    $r->status,
                    $r->pending_count,
                    $notes,
                    '<b>'.$r->recipient_name.'</b><br />'.$r->shipping_address.'<br />'.$this->split_phone($r->phone).'<br />'.$this->split_phone($r->mobile1).'<br />'.$this->split_phone($r->mobile2),
                    $r->delivery_id.'<hr />'.$this->hide_trx($r->merchant_trans_id)
                );


            }

            $seq++;
        }

        $csv_data[] = array(
            '',
            '',
            '',
            'Rata-rata ( dlm satuan hari )',
            //number_format($order2assigndays / $seq, 2, ',','.' ),
            //'',
            number_format($assign2deliverydays / $seq, 2, ',','.' ),
            '',
            //number_format($order2deliverydays / $seq, 2, ',','.' ),
            '',
            '',
            '',
            '',
            '',
            ''
        );

        if($pdf == 'csv'){
                        // Open the output stream
            $fh = fopen('php://output', 'w');

            // Start output buffering (to capture stream contents)
            ob_start();

            // Loop over the * to export
            if (! empty($csv_data)) {
                $headers = $csv_header;
                    fputcsv($fh, $headers,',','"');
                foreach ($csv_data as $item) {
                    fputcsv($fh, $item,',','"');
                }
            }

            // Get the contents of the output buffer
            $string = ob_get_clean();

            $filename = str_replace('/', '_', uri_string()).'.csv';

            // Output CSV-specific headers
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false);
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '";');
            header('Content-Transfer-Encoding: binary');

            exit($string);

        }

        $this->table->add_row(
            '',
            '',
            '',
            //number_format($order2assigndays / $seq, 2, ',','.' ),
            '',
            'Rata-rata<br />( dlm satuan hari )',
            number_format($assign2deliverydays / $seq, 2, ',','.' ),
            '',
            //number_format($order2deliverydays / $seq, 2, ',','.' ),
            '',
            '',
            '',
            '',
            '',
            ''
        );



        if($pdf == 'print' || $pdf == 'pdf'){

            $total_span = 2;
            $say_span = 6;

        }else{

            $total_span = 12;
            $say_span = 13;

        }

        /*
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
            array('data'=>'Delivery Charge',
                'colspan'=>$total_span),
            array('data'=>$this->number_words->to_words($total_delivery).' rupiah',
                'colspan'=>$say_span)
        );

        $this->table->add_row(
            array('data'=>'COD Surcharge',
                'colspan'=>$total_span),
            array('data'=>$this->number_words->to_words($total_cod).' rupiah',
                'colspan'=>$say_span)
        );

        $this->table->add_row(
            array('data'=>'Grand Total',
                'colspan'=>$total_span),
            array('data'=>$this->number_words->to_words($total_delivery + $total_cod).' rupiah',
                'colspan'=>$say_span)
        );
        */

        $recontab = $this->table->generate();
        $data['recontab'] = $recontab;

        /* end copy */

        $this->breadcrumb->add_crumb('Delivery Time','admin/reports/deliverytime');

        $page['ajaxurl'] = 'admin/reports/ajaxreconciliation';
        $page['page_title'] = 'Manifest';
        $data['select_title'] = 'Device';
        $data['zone_select_title'] = 'Zone';


        $data['controller'] = 'admin/reports/deliverytime/';

        $data['last_query'] = $last_query;

        $data['grand_total'] = $total_delivery + $total_cod;


        $data['zones'] = array_merge( array('all'=>'All'), get_zone_options() ) ;

        $data['courier_name'] = $courier_name;

        $data['merchantname'] = str_replace( array('http','www.',':','/','.com','.net','.co.id'),'',$data['merchantname']);
        $data['merchantinfo'] = str_replace( array('http','www.',':','/','.com','.net','.co.id'),'',$data['merchantinfo']);

        $zonename = strtoupper(str_replace(' ', '_', $data['zone']));
        $mname = strtoupper(str_replace(' ','_',$data['merchantname']));
        $minfo = strtoupper(str_replace(' ','_',$data['merchantinfo']));

        $pdffilename = 'JSM-TR-'.$mname.'-'.$minfo.'-'.$zonename.'-'.$data['invdatenum'];

        if($pdf == 'pdf'){
            $html = $this->load->view('print/deliverytimeprint',$data,true);
            $pdf_name = $pdffilename;
            $pdfbuf = pdf_create($html, $pdf_name,'A3','landscape', false);

            file_put_contents(FCPATH.'public/deliverytime/'.$pdf_name.'.pdf', $pdfbuf);

            $data['invdate'] = iddate($invdate);
            $data['invdatenum'] = date('dmY',mysql_to_unix($invdate));


            $invdata = array(
                'merchant_id'=>$type,
                'merchantname'=>$data['merchantname'],
                'merchantinfo'=>$data['merchantinfo'],
                'period_from'=>$data['from'],
                'period_to'=>$data['to'],
                'release_date'=>$invdate,
                'invoice_number'=>$pdffilename,
                'note'=>'',
                'filename'=>$pdffilename
            );

            $inres = $this->db->insert($this->config->item('deliverytime_table'),$invdata);

            return array(file_exists(FCPATH.'public/deliverytime/'.$pdf_name.'.pdf'), $pdf_name.'.pdf');

        }else if($pdf == 'print'){
            $this->load->view('print/deliverytimeprint',$data); // Load the view
        }else{
            $this->ag_auth->view('deliverytimegenerator',$data); // Load the view
        }
    }

    public function gendeliverytime(){
        $type = null;
        $deliverytype = null;
        $zone = null;
        $merchant = null;
        $status = null;
        $year = null;
        $scope = null;
        $par1 = null;
        $par2 = null;
        $par3 = null;
        $par4 = null;

        $type = $this->input->post('type');
        $deliverytype = $this->input->post('deliverytype');
        $zone = $this->input->post('zone');
        $merchant = $this->input->post('merchant');
        $status = $this->input->post('status');
        $year = $this->input->post('year');
        $scope = $this->input->post('scope');
        $par1 = $this->input->post('par1');
        $par2 = $this->input->post('par2');
        $par3 = $this->input->post('par3');
        $par4 = $this->input->post('par4');

        $result = $this->deliverytime($type, $deliverytype ,$zone,$merchant,$status,$year, $scope, $par1, $par2, $par3,$par4);

        $result[0] = ($result[0])?'OK':'FAILED';

        print json_encode(array('result'=>$result[0], 'file'=>$result[1]));

    }


    public function courierrecap($type = null,$status = null,$year = null, $scope = null, $par1 = null, $par2 = null, $par3 = null){

        $type = (is_null($type))?'Global':$type;
        $id = (is_null($type))?'noid':$type;
        $status = (is_null($status))?'all':$status;

        if(is_null($scope)){
            $id = 'noid';
            $scope = 'month';
            $year = date('Y',time());
            $par1 = date('m',time());
        }

        $pdf = null;

        if($scope == 'month'){
            $days = cal_days_in_month(CAL_GREGORIAN, $par1, $year);
            $from = date('Y-m-d', strtotime($year.'/'.$par1.'/1'));
            $to =   date('Y-m-d', strtotime($year.'/'.$par1.'/'.$days));
            $pdf = $par2;

            $data['month'] = $par1;
            $data['week'] = 1;
        }else if($scope == 'week'){
            $from = date('Y-m-d', strtotime('1 Jan '.$year.' +'.($par1 - 1).' weeks'));
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

        $data['merchants'] = $cs;
        $data['id'] = $id;

        /* copied from print controller */

        $this->load->library('number_words');

        if($id == 'noid'){
            $data['type_name'] = '-';
            $data['bank_account'] = 'n/a';
            $data['type'] = 'Global';
        }else{
            $user = $this->db->where('id',$id)->get($this->config->item('jayon_couriers_table'))->row();
            //print $this->db->last_query();
            $data['type'] = $user->fullname;
            $data['type_name'] = $user->fullname;
            $data['bank_account'] = 'n/a';
        }

        $data['period'] = $from.' s/d '.$to;

        $sfrom = date('Y-m-d',strtotime($from));
        $sto = date('Y-m-d',strtotime($to));

        $this->db->select('assignment_date,delivery_id,'.$this->config->item('assigned_delivery_table').'.merchant_id as merchant_id,buyer_name,merchant_trans_id,m.merchantname as merchant_name, m.fullname as fullname,d.identifier as device_name, c.fullname as courier_name ,a.application_name as app_name, a.domain as domain ,delivery_type,status,cod_cost,delivery_cost,total_price,total_tax,total_discount')
            ->join('members as m',$this->config->item('incoming_delivery_table').'.merchant_id=m.id','left')
            ->join('applications as a',$this->config->item('assigned_delivery_table').'.application_id=a.id','left')
            ->join('devices as d',$this->config->item('assigned_delivery_table').'.device_id=d.id','left')
            ->join('couriers as c',$this->config->item('assigned_delivery_table').'.courier_id=c.id','left')
            //->like('assignment_date',$date,'before')
            ->from($this->config->item('incoming_delivery_table'));

        $column = 'assignment_date';
        $daterange = sprintf("`%s`between '%s%%' and '%s%%' ", $column, $sfrom, $sto);

        $this->db->where($daterange, null, false);
        $this->db->where($column.' != ','0000-00-00');

        if($id != 'noid'){
            $this->db->where($this->config->item('assigned_delivery_table').'.courier_id',$id);
        }

        $this->db->where('status',   $this->config->item('trans_status_mobile_delivered'));

        /*
        $this->db->and_();
            $this->db->group_start();
                $this->db->where('status',   $this->config->item('trans_status_mobile_delivered'));
                $this->db->or_where('status',$this->config->item('trans_status_mobile_revoked'));
                $this->db->or_where('status',$this->config->item('trans_status_mobile_noshow'));
                $this->db->or_where('status',$this->config->item('trans_status_mobile_rescheduled'));
            $this->db->group_end();
        */
        //print $this->db->last_query();

        if($pdf == 'csv'){

            $result = $this->db->get()->result_array();

            // Open the output stream
            $fh = fopen('php://output', 'w');

            // Start output buffering (to capture stream contents)
            ob_start();

            // Loop over the * to export
            if (! empty($result)) {
                $headers = array_keys($result[0]);
                    fputcsv($fh, $headers);
                foreach ($result as $item) {
                    fputcsv($fh, $item);
                }
            }

            // Get the contents of the output buffer
            $string = ob_get_clean();

            $filename = str_replace('/', '_', uri_string()).'.csv';

            // Output CSV-specific headers
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false);
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '";');
            header('Content-Transfer-Encoding: binary');

            exit($string);
        }


        $rows = $this->db->get();

        $trans = $rows->result();

        $last_query = $this->db->last_query();
        //print_r($result);


        //exit();

        //print_r($trans);

        //exit();

        $this->table->set_heading(
            'No.',
            'Device Name',
            'Courier',
            'No Kode Penjualan Toko',
            'Delivery ID',
            'Store',
            'Delivery Date',
            'Buyer Name',
            'Delivery Type',
            'Status',
            'Package Value',
            'Disc',
            'Tax',
            'Delivery Chg',
            'COD Surchg',
            'Payable Value'
        ); // Setting headings for the table

        $seq = 1;
        $total_billing = 0;
        $total_delivery = 0;
        $total_cod = 0;

        $lastdate = '';

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

            if($r->status == 'delivered'){
                $payable = ($total - $dsc) + $tax;

                $total_delivery += (int)str_replace('.','',$dc);
                $total_cod += (int)str_replace('.','',$cod);
                $total_billing += (int)str_replace('.','',$payable);

            }

            $this->table->add_row(
                $seq,
                $r->device_name,
                $r->courier_name,
                $this->hide_trx($r->merchant_trans_id),
                $this->short_did($r->delivery_id),
                $r->app_name.'<hr />'.$r->domain,
                date('d-m-Y',strtotime($r->assignment_date)),
                $r->buyer_name,
                $r->delivery_type,
                $r->status,
                array('data'=>idr($total),'class'=>'currency'),
                array('data'=>idr($dsc),'class'=>'currency'),
                array('data'=>idr($tax),'class'=>'currency'),
                array('data'=>idr($dc),'class'=>'currency'),
                array('data'=>idr($cod),'class'=>'currency'),
                array('data'=>idr($payable),'class'=>'currency')
            );

            $seq++;
        }

        $total_span = 13;
        $say_span = 14;

        $this->table->add_row(
            array('data'=>'Total','colspan'=>$total_span),
            array('data'=>idr($total_delivery),'class'=>'total currency'),
            array('data'=>idr($total_cod),'class'=>'total currency'),
            array('data'=>idr($total_billing),'class'=>'total currency')
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

        $data['statuslist'] = array_merge(array('all'=>'All'), $this->config->item('status_list') );
        $data['stid'] = $status;

        /* end copy */

        $this->breadcrumb->add_crumb('Courier Recap','admin/reports/courierrecap');

        $page['ajaxurl'] = 'admin/reports/ajaxreconciliation';
        $page['page_title'] = 'Courier Recap';

        $data['select_title'] = 'Courier';

        $data['controller'] = 'admin/reports/courierrecap/';

        $data['last_query'] = $last_query;

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

    public function zonerevenue($type = null,$year = null, $scope = null, $par1 = null, $par2 = null, $par3 = null){

        $type = (is_null($type))?'Global':$type;
        $id = (is_null($type))?'noid':$type;

        $id = str_replace('%20',' ', $id);

        if(is_null($scope)){
            $id = 'noid';
            $scope = 'month';
            $year = date('Y',time());
            $par1 = date('m',time());
        }

        $pdf = null;

        if($scope == 'month'){
            $days = cal_days_in_month(CAL_GREGORIAN, $par1, $year);
            $from = date('Y-m-d', strtotime($year.'/'.$par1.'/1'));
            $to =   date('Y-m-d', strtotime($year.'/'.$par1.'/'.$days));
            $pdf = $par2;

            $data['month'] = $par1;
            $data['week'] = 1;
        }else if($scope == 'week'){
            $from = date('Y-m-d', strtotime('1 Jan '.$year.' +'.($par1 - 1).' weeks'));
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


        //$clist = get_zones(null,false);
        $clist = get_zone_options();

        $cs = array('noid'=>'All');
        $cs = array_merge($cs, $clist);
        /*
        foreach ($clist as $ckey) {
            $cs[$ckey['district']] = $ckey['district'].' - '.$ckey['city'];
        }
        */

        //print_r($cs);

        $data['merchants'] = $cs;

        $data['id'] = $id;

        //print $id;

        /* copied from print controller */

        $this->load->library('number_words');

        if($id == 'noid'){
            $data['type_name'] = '-';
            $data['bank_account'] = 'n/a';
            $data['type'] = 'Global';
        }else{
            $user = $this->db->where('district',$id)->get($this->config->item('jayon_zones_table'))->row();

            //print $this->db->last_query();

            //print_r($user);

            $data['type'] = $user->district.' - '.$user->city;
            $data['type_name'] = $user->district;
            $data['bank_account'] = 'n/a';
        }

        $data['period'] = $from.' s/d '.$to;

        $sfrom = date('Y-m-d',strtotime($from));
        $sto = date('Y-m-d',strtotime($to));

        // get assignment_date, merchant_id,delivery_type

        $this->db->distinct();

        $this->db->select('assignment_date,buyerdeliveryzone,buyerdeliverycity,delivery_type,status,count(*) as count, sum(cod_cost) as cod_cost,sum(delivery_cost) as delivery_cost,sum(total_price) as total_price ,sum(total_discount) as total_discount , sum(total_tax) as total_tax,sum(((total_price-total_discount)+total_tax)) as package_value');
        //$this->db->select('assignment_date,merchant_id,delivery_type,status');

        $this->db->from($this->config->item('delivered_delivery_table'));

        $column = 'assignment_date';
        $daterange = sprintf("`%s`between '%s%%' and '%s%%' ", $column, $sfrom, $sto);

        $this->db->where($daterange, null, false);
        $this->db->where($column.' != ','0000-00-00');

        if($id != 'noid'){
            $this->db->where($this->config->item('delivered_delivery_table').'.buyerdeliveryzone',$id);
        }

        $this->db->and_();
            $this->db->group_start();
                $this->db->where('status',   $this->config->item('trans_status_mobile_delivered'));
                $this->db->or_where('status',$this->config->item('trans_status_mobile_revoked'));
                $this->db->or_where('status',$this->config->item('trans_status_mobile_noshow'));
                $this->db->or_where('status',$this->config->item('trans_status_mobile_rescheduled'));
            $this->db->group_end();

            $this->db->group_by('assignment_date,buyerdeliveryzone,delivery_type,status');
            //$this->db->group_by('assignment_date,merchant_id,delivery_type,status');
        if($pdf == 'csv'){

            $result = $this->db->get()->result_array();

            // Open the output stream
            $fh = fopen('php://output', 'w');

            // Start output buffering (to capture stream contents)
            ob_start();

            // Loop over the * to export
            if (! empty($result)) {
                $headers = array_keys($result[0]);
                    fputcsv($fh, $headers);
                foreach ($result as $item) {
                    fputcsv($fh, $item);
                }
            }

            // Get the contents of the output buffer
            $string = ob_get_clean();

            $filename = str_replace('/', '_', uri_string()).'.csv';

            // Output CSV-specific headers
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false);
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '";');
            header('Content-Transfer-Encoding: binary');

            exit($string);
        }


        $rows = $this->db->get();

        $result = $rows->result_array();
        //print $this->db->last_query();

        $last_query = $this->db->last_query();

        //print_r($result);

        //exit();

        $trans = array();

        $cities = array();

        foreach($result as $r){

            /*
            $this->db->select('count(*) as count, sum(cod_cost) as cod_cost,sum(delivery_cost) as delivery_cost,sum(total_price) as total_price ,sum(total_discount) as total_discount , sum(total_tax) as total_tax,sum(((total_price-total_discount)+total_tax)) as package_value');

            $this->db->from($this->config->item('delivered_delivery_table'));

            $this->db->where('assignment_date',$r['assignment_date']);
            $this->db->where('merchant_id',$r['merchant_id']);
            $this->db->where('delivery_type',$r['delivery_type']);
            $this->db->where('status',$r['status']);


            $irows = $this->db->get()->result_array();
            */

            $trans[$r['assignment_date']][$r['buyerdeliveryzone']][$r['delivery_type']][$r['status']]['count'] = $r['count'];
            $trans[$r['assignment_date']][$r['buyerdeliveryzone']][$r['delivery_type']][$r['status']]['cod_cost'] = $r['cod_cost'];
            $trans[$r['assignment_date']][$r['buyerdeliveryzone']][$r['delivery_type']][$r['status']]['delivery_cost'] = $r['delivery_cost'];
            $trans[$r['assignment_date']][$r['buyerdeliveryzone']][$r['delivery_type']][$r['status']]['total_price'] = $r['total_price'];
            $trans[$r['assignment_date']][$r['buyerdeliveryzone']][$r['delivery_type']][$r['status']]['package_value'] = $r['package_value'];

            $cities[$r['buyerdeliveryzone']] = $r['buyerdeliverycity'];

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

                    foreach($status_array as $s){

                        if(!isset($trans[$key][$k][$t][$s])){
                            $trans[$key][$k][$t][$s]['count'] = 0;
                            $trans[$key][$k][$t][$s]['cod_cost'] = 0;
                            $trans[$key][$k][$t][$s]['delivery_cost'] = 0;
                            $trans[$key][$k][$t][$s]['total_price'] = 0;
                            $trans[$key][$k][$t][$s]['package_value'] = 0;
                        }

                    }

                }

            }

        }


        //print_r($trans);

        //exit();

        $this->table->set_heading(
            '',
            '',
            '',
            '',
            array('data'=>'DO','colspan'=>'3'),
            array('data'=>'COD','colspan'=>'4'),
            array('data'=>'CCOD','colspan'=>'4'),
            array('data'=>'PS','colspan'=>'3'),

            array('data'=>'Total','colspan'=>'3')
        ); // Setting headings for the table


        $this->table->set_subheading(
            'No.',
            'Date',
            'Zone',
            'City',

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

            'Revenue',
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
        $total['jex']['revenue'] = 0;
        $total['total_delivery_count']  = 0;
        $total['total_package_value']  = 0;

        $lastdate = '';


        //print_r($trans);

        //print_r($cities);

        foreach($trans as $key=>$val){

            foreach ($val as $k => $v) {

                $r[$key][$k] = $this->_makerevrow($v);

                $revtotal = ( $r[$key][$k]['Delivery Only']['dcost'] + $r[$key][$k]['COD']['dcost'] + $r[$key][$k]['COD']['sur'] + $r[$key][$k]['CCOD']['dcost'] + $r[$key][$k]['CCOD']['sur'] + $r[$key][$k]['PS']['pfee']);

                $this->table->add_row(
                    $counter,
                    ($lastdate == $key)?'':date('d-m-Y',strtotime($key)),
                    //$cs[$k],
                    $k,
                    $cities[$k],
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

                    array('data'=>idr($revtotal),'class'=>'currency'),
                    array('data'=>$r[$key][$k]['total_delivery_count'],'class'=>'count'),
                    array('data'=>idr($r[$key][$k]['total_package_value']),'class'=>'currency')

                );

                    $lastdate = $key;

                    $total['Delivery Only']['count'] += $r[$key][$k]['Delivery Only']['count'];
                    $total['Delivery Only']['dcost'] += $r[$key][$k]['Delivery Only']['dcost'];
                    $total['Delivery Only']['pval'] += $r[$key][$k]['Delivery Only']['pval'];
                    $total['COD']['count'] += $r[$key][$k]['COD']['count'];
                    $total['COD']['dcost'] += $r[$key][$k]['COD']['dcost'];
                    $total['COD']['sur'] += $r[$key][$k]['COD']['sur'];
                    $total['COD']['pval'] += $r[$key][$k]['COD']['pval'];
                    $total['CCOD']['count'] += $r[$key][$k]['CCOD']['count'];
                    $total['CCOD']['dcost'] += $r[$key][$k]['CCOD']['dcost'];
                    $total['CCOD']['sur'] += $r[$key][$k]['CCOD']['sur'];
                    $total['CCOD']['pval'] += $r[$key][$k]['CCOD']['pval'];
                    $total['PS']['count'] += $r[$key][$k]['PS']['count'];
                    $total['PS']['pfee'] += $r[$key][$k]['PS']['pfee'];
                    $total['PS']['pval'] += $r[$key][$k]['PS']['pval'];

                    $total['jex']['revenue'] += $revtotal;
                    $total['total_delivery_count'] += $r[$key][$k]['total_delivery_count'];
                    $total['total_package_value'] += $r[$key][$k]['total_package_value'];

                $counter++;

            }

        }

            $this->table->add_row(
                '',
                '',
                '',

                array('data'=>'Totals','class'=>'total'),

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

                array('data'=>idr($total['jex']['revenue']),'class'=>'total currency'),
                array('data'=>$total['total_delivery_count'],'class'=>'total count'),
                array('data'=>idr($total['total_package_value']),'class'=>'total currency')

            );

            $this->table->add_row(
                '',
                '',
                '',

                array('data'=>'Percentage (%)','class'=>'total'),

                array('data'=>($total['Delivery Only']['count'] == 0)?idr(0):idr(($total['Delivery Only']['count'] / $total['total_delivery_count'])* 100),'class'=>'total count c-orange'),
                array('data'=>($total['Delivery Only']['count'] == 0)?idr(0):idr($total['Delivery Only']['dcost'] / $total['jex']['revenue'] * 100),'class'=>'total currency c-maroon'),
                array('data'=>($total['Delivery Only']['pval'] == 0)?idr(0):idr($total['Delivery Only']['pval'] / $total['total_package_value'] * 100),'class'=>'total currency c-maroon'),

                array('data'=>($total['COD']['count'] == 0)?idr(0):idr($total['COD']['count'] / $total['total_delivery_count'] * 100),'class'=>'total count c-orange'),
                array('data'=>($total['COD']['dcost'] == 0)?idr(0):idr($total['COD']['dcost'] / $total['jex']['revenue'] * 100 ),'class'=>'total currency c-maroon'),
                array('data'=>($total['COD']['sur'] == 0)?idr(0):idr($total['COD']['sur'] / $total['jex']['revenue'] * 100),'class'=>'total currency c-maroon'),
                array('data'=>($total['COD']['pval'] == 0)?idr(0):idr($total['COD']['pval'] / $total['total_package_value'] * 100),'class'=>'total currency c-maroon'),

                array('data'=>($total['CCOD']['count'] == 0)?idr(0):idr($total['CCOD']['count'] / $total['total_delivery_count'] * 100),'class'=>'total count c-orange'),
                array('data'=>($total['CCOD']['dcost'] == 0)?idr(0):idr($total['CCOD']['dcost'] / $total['jex']['revenue'] * 100),'class'=>'total currency c-maroon'),
                array('data'=>($total['CCOD']['sur'] == 0)?idr(0):idr($total['CCOD']['sur'] / $total['jex']['revenue'] * 100),'class'=>'total currency c-maroon'),
                array('data'=>($total['CCOD']['pval'] == 0)?idr(0):idr($total['CCOD']['pval'] / $total['total_package_value'] * 100),'class'=>'total currency c-maroon'),

                array('data'=>($total['PS']['count'] == 0)?idr(0):idr($total['PS']['count'] / $total['total_delivery_count'] * 100),'class'=>'total count c-orange'),
                array('data'=>($total['PS']['pfee'] == 0)?idr(0):idr($total['PS']['pfee'] / $total['jex']['revenue'] * 100),'class'=>'total currency c-maroon'),
                array('data'=>($total['PS']['pval'] == 0)?idr(0):idr($total['PS']['pval'] / $total['total_package_value'] * 100),'class'=>'total currency c-maroon'),

                '',
                '',
                ''
            );


            $this->table->add_row(
                '',
                '',
                '',

                array('data'=>'Summary','class'=>'total'),

                array('data'=>$total['Delivery Only']['count'] + $total['COD']['count'] + $total['CCOD']['count'] + $total['PS']['count'],'class'=>'total count'),
                array('data'=>idr($total['Delivery Only']['dcost'] + $total['COD']['dcost'] + $total['CCOD']['dcost']),'class'=>'total currency'),
                array('data'=>idr($total['Delivery Only']['pval'] + $total['COD']['pval'] + $total['CCOD']['pval'] + $total['PS']['pval']),'class'=>'total currency'),

                '',
                '',
                array('data'=>idr($total['COD']['sur'] + $total['CCOD']['sur']),'class'=>'total currency'),
                '',

                '',
                '',
                '',
                '',

                '',
                array('data'=>idr($total['PS']['pfee']),'class'=>'total currency'),
                '',

                array('data'=>idr($total['jex']['revenue']),'class'=>'total currency'),
                array('data'=>$total['total_delivery_count'],'class'=>'total count'),
                array('data'=>idr($total['total_package_value']),'class'=>'total currency')

            );


        $recontab = $this->table->generate();
        $data['recontab'] = $recontab;

        /* end copy */

        $this->breadcrumb->add_crumb('Zone Revenue','admin/reports/zonerevenue');

        $page['ajaxurl'] = 'admin/reports/ajaxreconciliation';
        $page['page_title'] = 'Zone Revenue';
        $data['select_title'] = 'Zone';

        $data['controller'] = 'admin/reports/zonerevenue/';

        $data['last_query'] = $last_query;

        if($pdf == 'pdf'){
            $html = $this->load->view('print/revenue',$data,true);
            $pdf_name = $type.'_'.$to.'_'.$from.'_'.$id;
            pdf_create($html, $pdf_name.'.pdf','A4','landscape', true);
        }else if($pdf == 'print'){
            $this->load->view('print/merchantrecon',$data); // Load the view
        }else{
            $this->ag_auth->view('zonereport',$data); // Load the view
        }
    }

    public function cityrevenue($type = null,$year = null, $scope = null, $par1 = null, $par2 = null, $par3 = null){

        $type = (is_null($type))?'Global':$type;
        $id = (is_null($type))?'noid':$type;

        $id = str_replace('%20',' ', $id);

        if(is_null($scope)){
            $id = 'noid';
            $scope = 'month';
            $year = date('Y',time());
            $par1 = date('m',time());
        }

        $pdf = null;

        if($scope == 'month'){
            $days = cal_days_in_month(CAL_GREGORIAN, $par1, $year);
            $from = date('Y-m-d', strtotime($year.'/'.$par1.'/1'));
            $to =   date('Y-m-d', strtotime($year.'/'.$par1.'/'.$days));
            $pdf = $par2;

            $data['month'] = $par1;
            $data['week'] = 1;
        }else if($scope == 'week'){
            $from = date('Y-m-d', strtotime('1 Jan '.$year.' +'.($par1 - 1).' weeks'));
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


        //$clist = get_zones(null,false);
        $clist = get_city_options();

        $cs = array('noid'=>'All');
        $cs = array_merge($cs, $clist);
        /*
        foreach ($clist as $ckey) {
            $cs[$ckey['district']] = $ckey['district'].' - '.$ckey['city'];
        }
        */

        //print_r($cs);

        $data['merchants'] = $cs;

        $data['id'] = $id;

        //print $id;

        /* copied from print controller */

        $this->load->library('number_words');

        if($id == 'noid'){
            $data['type_name'] = '-';
            $data['bank_account'] = 'n/a';
            $data['type'] = 'Global';
        }else{
            $user = $this->db->where('city',$id)->get($this->config->item('jayon_zones_table'))->row();

            //print $this->db->last_query();

            //print_r($user);

            $data['type'] = $user->city;
            $data['type_name'] = $user->city;
            $data['bank_account'] = 'n/a';
        }

        $data['period'] = $from.' s/d '.$to;

        $sfrom = date('Y-m-d',strtotime($from));
        $sto = date('Y-m-d',strtotime($to));

        // get assignment_date, merchant_id,delivery_type

        $this->db->distinct();

        $this->db->select('assignment_date,buyerdeliverycity,delivery_type,status,count(*) as count, sum(cod_cost) as cod_cost,sum(delivery_cost) as delivery_cost,sum(total_price) as total_price ,sum(total_discount) as total_discount , sum(total_tax) as total_tax,sum(((total_price-total_discount)+total_tax)) as package_value');
        //$this->db->select('assignment_date,merchant_id,delivery_type,status');

        $this->db->from($this->config->item('delivered_delivery_table'));

        $column = 'assignment_date';
        $daterange = sprintf("`%s`between '%s%%' and '%s%%' ", $column, $sfrom, $sto);

        $this->db->where($daterange, null, false);
        $this->db->where($column.' != ','0000-00-00');

        if($id != 'noid'){
            $this->db->where($this->config->item('delivered_delivery_table').'.buyerdeliverycity',$id);
        }

        $this->db->and_();
            $this->db->group_start();
                $this->db->where('status',   $this->config->item('trans_status_mobile_delivered'));
                $this->db->or_where('status',$this->config->item('trans_status_mobile_revoked'));
                $this->db->or_where('status',$this->config->item('trans_status_mobile_noshow'));
                $this->db->or_where('status',$this->config->item('trans_status_mobile_rescheduled'));
            $this->db->group_end();

            $this->db->group_by('assignment_date,buyerdeliveryzone,delivery_type,status');
            //$this->db->group_by('assignment_date,merchant_id,delivery_type,status');

        if($pdf == 'csv'){


            $result = $this->db->get()->result_array();

            // Open the output stream
            $fh = fopen('php://output', 'w');

            // Start output buffering (to capture stream contents)
            ob_start();

            // Loop over the * to export
            if (! empty($result)) {
                $headers = array_keys($result[0]);
                    fputcsv($fh, $headers);
                foreach ($result as $item) {
                    fputcsv($fh, $item);
                }
            }

            // Get the contents of the output buffer
            $string = ob_get_clean();

            $filename = str_replace('/', '_', uri_string()).'.csv';

            // Output CSV-specific headers
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false);
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '";');
            header('Content-Transfer-Encoding: binary');

            exit($string);
        }


        $rows = $this->db->get();

        $result = $rows->result_array();
        //print $this->db->last_query();

        $last_query = $this->db->last_query();

        //print_r($result);

        //exit();

        $trans = array();


        foreach($result as $r){

            /*
            $this->db->select('count(*) as count, sum(cod_cost) as cod_cost,sum(delivery_cost) as delivery_cost,sum(total_price) as total_price ,sum(total_discount) as total_discount , sum(total_tax) as total_tax,sum(((total_price-total_discount)+total_tax)) as package_value');

            $this->db->from($this->config->item('delivered_delivery_table'));

            $this->db->where('assignment_date',$r['assignment_date']);
            $this->db->where('merchant_id',$r['merchant_id']);
            $this->db->where('delivery_type',$r['delivery_type']);
            $this->db->where('status',$r['status']);


            $irows = $this->db->get()->result_array();
            */

            $trans[$r['assignment_date']][$r['buyerdeliverycity']][$r['delivery_type']][$r['status']]['count'] = $r['count'];
            $trans[$r['assignment_date']][$r['buyerdeliverycity']][$r['delivery_type']][$r['status']]['cod_cost'] = $r['cod_cost'];
            $trans[$r['assignment_date']][$r['buyerdeliverycity']][$r['delivery_type']][$r['status']]['delivery_cost'] = $r['delivery_cost'];
            $trans[$r['assignment_date']][$r['buyerdeliverycity']][$r['delivery_type']][$r['status']]['total_price'] = $r['total_price'];
            $trans[$r['assignment_date']][$r['buyerdeliverycity']][$r['delivery_type']][$r['status']]['package_value'] = $r['package_value'];
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

                    foreach($status_array as $s){

                        if(!isset($trans[$key][$k][$t][$s])){
                            $trans[$key][$k][$t][$s]['count'] = 0;
                            $trans[$key][$k][$t][$s]['cod_cost'] = 0;
                            $trans[$key][$k][$t][$s]['delivery_cost'] = 0;
                            $trans[$key][$k][$t][$s]['total_price'] = 0;
                            $trans[$key][$k][$t][$s]['package_value'] = 0;
                        }

                    }

                }

            }

        }


        //print_r($trans);

        //exit();

        $this->table->set_heading(
            '',
            '',
            '',
            array('data'=>'DO','colspan'=>'3'),
            array('data'=>'COD','colspan'=>'4'),
            array('data'=>'CCOD','colspan'=>'4'),
            array('data'=>'PS','colspan'=>'3'),

            array('data'=>'Total','colspan'=>'3')
        ); // Setting headings for the table


        $this->table->set_subheading(
            'No.',
            'Date',
            'City',

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

            'Revenue',
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
        $total['jex']['revenue'] = 0;
        $total['total_delivery_count']  = 0;
        $total['total_package_value']  = 0;

        $lastdate = '';

        foreach($trans as $key=>$val){

            foreach ($val as $k => $v) {

                $r[$key][$k] = $this->_makerevrow($v);

                $revtotal = ( $r[$key][$k]['Delivery Only']['dcost'] + $r[$key][$k]['COD']['dcost'] + $r[$key][$k]['COD']['sur'] + $r[$key][$k]['CCOD']['dcost'] + $r[$key][$k]['CCOD']['sur'] + $r[$key][$k]['PS']['pfee']);


                $this->table->add_row(
                    $counter,
                    ($lastdate == $key)?'':date('d-m-Y',strtotime($key)),
                    //$cs[$k],
                    $k,
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

                    array('data'=>idr($revtotal),'class'=>'currency'),
                    array('data'=>$r[$key][$k]['total_delivery_count'],'class'=>'count'),
                    array('data'=>idr($r[$key][$k]['total_package_value']),'class'=>'currency')

                );

                    $lastdate = $key;

                    $total['Delivery Only']['count'] += $r[$key][$k]['Delivery Only']['count'];
                    $total['Delivery Only']['dcost'] += $r[$key][$k]['Delivery Only']['dcost'];
                    $total['Delivery Only']['pval'] += $r[$key][$k]['Delivery Only']['pval'];
                    $total['COD']['count'] += $r[$key][$k]['COD']['count'];
                    $total['COD']['dcost'] += $r[$key][$k]['COD']['dcost'];
                    $total['COD']['sur'] += $r[$key][$k]['COD']['sur'];
                    $total['COD']['pval'] += $r[$key][$k]['COD']['pval'];
                    $total['CCOD']['count'] += $r[$key][$k]['CCOD']['count'];
                    $total['CCOD']['dcost'] += $r[$key][$k]['CCOD']['dcost'];
                    $total['CCOD']['sur'] += $r[$key][$k]['CCOD']['sur'];
                    $total['CCOD']['pval'] += $r[$key][$k]['CCOD']['pval'];
                    $total['PS']['count'] += $r[$key][$k]['PS']['count'];
                    $total['PS']['pfee'] += $r[$key][$k]['PS']['pfee'];
                    $total['PS']['pval'] += $r[$key][$k]['PS']['pval'];

                    $total['jex']['revenue'] += $revtotal;
                    $total['total_delivery_count'] += $r[$key][$k]['total_delivery_count'];
                    $total['total_package_value'] += $r[$key][$k]['total_package_value'];

                $counter++;

            }

        }

            $this->table->add_row(
                '',
                '',

                array('data'=>'Totals','class'=>'total'),

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

                array('data'=>idr($total['jex']['revenue']),'class'=>'total currency'),
                array('data'=>$total['total_delivery_count'],'class'=>'total count'),
                array('data'=>idr($total['total_package_value']),'class'=>'total currency')

            );

            $this->table->add_row(
                '',
                '',

                array('data'=>'Percentage (%)','class'=>'total'),

                array('data'=>($total['Delivery Only']['count'] == 0)?idr(0):idr(($total['Delivery Only']['count'] / $total['total_delivery_count'])* 100),'class'=>'total count c-orange'),
                array('data'=>($total['Delivery Only']['count'] == 0)?idr(0):idr($total['Delivery Only']['dcost'] / $total['jex']['revenue'] * 100),'class'=>'total currency c-maroon'),
                array('data'=>($total['Delivery Only']['pval'] == 0)?idr(0):idr($total['Delivery Only']['pval'] / $total['total_package_value'] * 100),'class'=>'total currency c-maroon'),

                array('data'=>($total['COD']['count'] == 0)?idr(0):idr($total['COD']['count'] / $total['total_delivery_count'] * 100),'class'=>'total count c-orange'),
                array('data'=>($total['COD']['dcost'] == 0)?idr(0):idr($total['COD']['dcost'] / $total['jex']['revenue'] * 100 ),'class'=>'total currency c-maroon'),
                array('data'=>($total['COD']['sur'] == 0)?idr(0):idr($total['COD']['sur'] / $total['jex']['revenue'] * 100),'class'=>'total currency c-maroon'),
                array('data'=>($total['COD']['pval'] == 0)?idr(0):idr($total['COD']['pval'] / $total['total_package_value'] * 100),'class'=>'total currency c-maroon'),

                array('data'=>($total['CCOD']['count'] == 0)?idr(0):idr($total['CCOD']['count'] / $total['total_delivery_count'] * 100),'class'=>'total count c-orange'),
                array('data'=>($total['CCOD']['dcost'] == 0)?idr(0):idr($total['CCOD']['dcost'] / $total['jex']['revenue'] * 100),'class'=>'total currency c-maroon'),
                array('data'=>($total['CCOD']['sur'] == 0)?idr(0):idr($total['CCOD']['sur'] / $total['jex']['revenue'] * 100),'class'=>'total currency c-maroon'),
                array('data'=>($total['CCOD']['pval'] == 0)?idr(0):idr($total['CCOD']['pval'] / $total['total_package_value'] * 100),'class'=>'total currency c-maroon'),

                array('data'=>($total['PS']['count'] == 0)?idr(0):idr($total['PS']['count'] / $total['total_delivery_count'] * 100),'class'=>'total count c-orange'),
                array('data'=>($total['PS']['pfee'] == 0)?idr(0):idr($total['PS']['pfee'] / $total['jex']['revenue'] * 100),'class'=>'total currency c-maroon'),
                array('data'=>($total['PS']['pval'] == 0)?idr(0):idr($total['PS']['pval'] / $total['total_package_value'] * 100),'class'=>'total currency c-maroon'),

                '',
                '',
                ''
            );


            $this->table->add_row(
                '',
                '',

                array('data'=>'Summary','class'=>'total'),

                array('data'=>$total['Delivery Only']['count'] + $total['COD']['count'] + $total['CCOD']['count'] + $total['PS']['count'],'class'=>'total count'),
                array('data'=>idr($total['Delivery Only']['dcost'] + $total['COD']['dcost'] + $total['CCOD']['dcost']),'class'=>'total currency'),
                array('data'=>idr($total['Delivery Only']['pval'] + $total['COD']['pval'] + $total['CCOD']['pval'] + $total['PS']['pval']),'class'=>'total currency'),

                '',
                '',
                array('data'=>idr($total['COD']['sur'] + $total['CCOD']['sur']),'class'=>'total currency'),
                '',

                '',
                '',
                '',
                '',

                '',
                array('data'=>idr($total['PS']['pfee']),'class'=>'total currency'),
                '',

                array('data'=>idr($total['jex']['revenue']),'class'=>'total currency'),
                array('data'=>$total['total_delivery_count'],'class'=>'total count'),
                array('data'=>idr($total['total_package_value']),'class'=>'total currency')

            );


        $recontab = $this->table->generate();
        $data['recontab'] = $recontab;

        /* end copy */

        $this->breadcrumb->add_crumb('City Revenue','admin/reports/zonerevenue');

        $page['ajaxurl'] = 'admin/reports/ajaxreconciliation';
        $page['page_title'] = 'City Revenue';

        $data['controller'] = 'admin/reports/cityrevenue/';

        $data['last_query'] = $last_query;

        if($pdf == 'pdf'){
            $html = $this->load->view('print/revenue',$data,true);
            $pdf_name = $type.'_'.$to.'_'.$from.'_'.$id;
            pdf_create($html, $pdf_name.'.pdf','A4','landscape', true);
        }else if($pdf == 'print'){
            $this->load->view('print/merchantrecon',$data); // Load the view
        }else{
            $this->ag_auth->view('cityreport',$data); // Load the view
        }
    }

	private function _makerevrow($v){

		//print_r($v);

		$r = array();

		$r['COD']['count'] = $v['COD']['delivered']['count'];
		$r['CCOD']['count'] = $v['CCOD']['delivered']['count'];
		$r['Delivery Only']['count'] = $v['Delivery Only']['delivered']['count'];
		$r['PS']['count'] = $v['PS']['delivered']['count'];

		$r['COD']['dcost'] = $v['COD']['delivered']['delivery_cost'];

		$r['CCOD']['dcost'] = $v['CCOD']['delivered']['delivery_cost'];

		$r['COD']['sur'] = $v['COD']['delivered']['cod_cost'];
		$r['CCOD']['sur'] = $v['CCOD']['delivered']['cod_cost'];

		$r['COD']['pval'] = $v['COD']['delivered']['package_value'];
		$r['CCOD']['pval'] = $v['CCOD']['delivered']['package_value'];

		$r['Delivery Only']['pval'] = $v['Delivery Only']['delivered']['package_value'];
		$r['PS']['pval'] = $v['PS']['delivered']['package_value'];

		$r['Delivery Only']['dcost'] = $v['Delivery Only']['delivered']['delivery_cost'];

		$r['PS']['pfee'] = $v['PS']['delivered']['delivery_cost'];

		$r['total_delivery_count'] = $r['COD']['count'] + $r['CCOD']['count'] + $r['Delivery Only']['count'] + $r['PS']['count'];
		$r['total_package_value'] = $r['Delivery Only']['pval'] + $r['COD']['pval'] + $r['CCOD']['pval'] + $r['PS']['pval'];


		return $r;
	}


	public function merchantrecon($type = null,$status = null,$year = null, $scope = null, $par1 = null, $par2 = null, $par3 = null){

		$type = (is_null($type))?'Global':$type;
		$id = (is_null($type))?'noid':$type;

        $status = (is_null($status))?'all':$status;

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
			$this->db->where($this->config->item('delivered_delivery_table').'.buyerdeliveryzone',$id);
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

        $last_query = $this->db->last_query();

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

        $data['statuslist'] = array_merge(array('all'=>'All'), $this->config->item('status_list') );
        $data['stid'] = $status;

		/* end copy */

		$this->breadcrumb->add_crumb('Merchant Reconciliations','admin/reports/reconciliation');

		$page['ajaxurl'] = 'admin/reports/ajaxreconciliation';
		$page['page_title'] = 'Merchant Reconciliations';
        $data['select_title'] = 'Merchant';

		$data['controller'] = 'admin/reports/merchantrecon/';

        $data['last_query'] = $last_query;

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

        if($pdf == 'csv'){


            $result = $this->db->get()->result_array();

            // Open the output stream
            $fh = fopen('php://output', 'w');

            // Start output buffering (to capture stream contents)
            ob_start();

            // Loop over the * to export
            if (! empty($result)) {
                $headers = array_keys($result[0]);
                    fputcsv($fh, $headers);
                foreach ($result as $item) {
                    fputcsv($fh, $item);
                }
            }

            // Get the contents of the output buffer
            $string = ob_get_clean();

            $filename = str_replace('/', '_', uri_string()).'.csv';

            // Output CSV-specific headers
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false);
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '";');
            header('Content-Transfer-Encoding: binary');

            exit($string);
        }



		$rows = $this->db->get();

        $last_query = $this->db->last_query();

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
        $data['select_title'] = 'Courier';

		$data['controller'] = 'admin/reports/courierrecon/';

        $data['last_query'] = $last_query;

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


	public function devicerecon($type = null,$status = null,$year = null, $scope = null, $par1 = null, $par2 = null, $par3 = null){

		$type = (is_null($type))?'Global':$type;
		$id = (is_null($type))?'noid':$type;
        $status = (is_null($status))?'all':$status;

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

        $data['statuslist'] = array_merge(array('all'=>'All'), $this->config->item('status_list') );
        $data['stid'] = $status;

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

        if($status != 'all'){
                $this->db->where('status',$status);
        }else{
            $this->db->and_();
            $this->db->group_start();
                $this->db->where('status',$this->config->item('trans_status_mobile_delivered'));
                $this->db->or_where('status',$this->config->item('trans_status_mobile_revoked'));
                $this->db->or_where('status',$this->config->item('trans_status_mobile_noshow'));
                $this->db->or_where('status',$this->config->item('trans_status_mobile_rescheduled'));
            $this->db->group_end();

            $this->db->group_by('assignment_date,status');
        }

        if($pdf == 'csv'){

            $result = $this->db->get()->result_array();

            // Open the output stream
            $fh = fopen('php://output', 'w');

            // Start output buffering (to capture stream contents)
            ob_start();

            // Loop over the * to export
            if (! empty($result)) {
                $headers = array_keys($result[0]);
                    fputcsv($fh, $headers);
                foreach ($result as $item) {
                    fputcsv($fh, $item);
                }
            }

            // Get the contents of the output buffer
            $string = ob_get_clean();

            $filename = str_replace('/', '_', uri_string()).'.csv';

            // Output CSV-specific headers
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false);
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '";');
            header('Content-Transfer-Encoding: binary');

            exit($string);
        }


		$rows = $this->db->get();

        $last_query = $this->db->last_query();

        //get incoming counts
        /*
        $sql = 'SELECT DISTINCT DATE_FORMAT( ordertime,  \'%Y-%m-%d\' ) AS incoming, COUNT( * ) as count FROM  `delivery_order_active` WHERE ordertime BETWEEN ? AND ? GROUP BY incoming';

        $ql = $this->db->query($sql, array($from.' 00:00:00', $to.' 11:59:59'));

        $incomings = $ql->result_array();



        $incoming_array = array();

        foreach($incomings as $i){
            $incoming_array[$i['incoming']] = $i['count'];
        }

        print_r($incoming_array);
        */

        //print $this->db->last_query();



		$this->table->set_heading(
			array('data'=>'Delivery Details',
				'colspan'=>'4'
			)
		);


		$this->table->set_heading(
			'No.',
			'Date',
            'Incoming',
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

        $tinc = 0;
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

            $inc = $this->db->like('ordertime', $r['assignment_date'],'after')->count_all_results($this->config->item('delivered_delivery_table'));

            $tinc += $inc;

			$this->table->add_row(
				$seq,
				date('d M Y',strtotime($r['assignment_date'])),
                $inc,
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
            array('data'=>$tinc,'style'=>'border-top:thin solid grey'),
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

        $data['last_query'] = $last_query;

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
		$this->db->join('members as m',$this->config->item('assigned_delivery_table').'.buyerdeliveryzone=m.id','left');
		$this->db->join('applications as a',$this->config->item('assigned_delivery_table').'.application_id=a.id','left');
		$this->db->join('devices as d',$this->config->item('assigned_delivery_table').'.device_id=d.id','left');
		$this->db->join('couriers as c',$this->config->item('assigned_delivery_table').'.courier_id=c.id','left');


		$column = 'assignment_date';
		$daterange = sprintf("`%s`between '%s%%' and '%s%%' ", $column, $sfrom, $sto);

		$this->db->where($daterange, null, false);
		$this->db->where($column.' != ','0000-00-00');

		if($id != 'noid'){
			if($type == 'Merchant'){
				$this->db->where($this->config->item('delivered_delivery_table').'.buyerdeliveryzone',$id);
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
		$this->db->join('members as m',$this->config->item('assigned_delivery_table').'.buyerdeliveryzone=m.id','left');
		$this->db->join('applications as a',$this->config->item('assigned_delivery_table').'.application_id=a.id','left');
		$this->db->join('devices as d',$this->config->item('assigned_delivery_table').'.device_id=d.id','left');
		$this->db->join('couriers as c',$this->config->item('assigned_delivery_table').'.courier_id=c.id','left');


		$column = 'assignment_date';
		$daterange = sprintf("`%s`between '%s%%' and '%s%%' ", $column, $sfrom, $sto);

		$this->db->where($daterange, null, false);
		$this->db->where($column.' != ','0000-00-00');

		if($id != 'noid'){
			if($type == 'Merchant'){
				$this->db->where($this->config->item('delivered_delivery_table').'.buyerdeliveryzone',$id);
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

    public function hide_trx($trx_id){
        if(preg_match('/^TRX_/', $trx_id)){
            return '';
        }else{
            return $trx_id;
        }
    }

    public function short_did($did){
        $did = explode('-',$did);
        return array_pop($did);
    }

    public function split_phone($phone){
        return str_replace(array('/','#','|'), '<br />', $phone);
    }

    public function compile_xls($data, $tmpl_data ,$template_path, $ext = '.xlsx'){
            $this->load->library('xls');
            $this->xls->setSkipEmpty(false);
            $xdata = $this->xls->load($template_path,$ext);

            //print_r($xdata['Worksheet']['cells']);

            $cells = $xdata['Worksheet']['cells'];

            $splice_at = 0;

            for($i = 0; $i < count($cells); $i++){
                for($j = 0;$j < count($cells[$i]);$j++){
                    if(isset($tmpl_data[$cells[$i][$j]])){
                        $cells[$i][$j] = $tmpl_data[$cells[$i][$j]];
                    }

                    if( $cells[$i][$j] == '_space_' ){
                        $cells[$i][$j] = '';
                    }

                    if($cells[$i][$j] == '_table_'){
                        $splice_at = $i;
                    }
                }
            }

            //print $splice_at;

            //if pos is start, just merge them
            if($splice_at == 0) {
                $cells = array_merge($cells, $data);
            } else {

                if($splice_at >= (count($cells) - 1 )) {
                    $cells = array_merge($cells, $data);
                } else {
                    //split into head and tail, then merge head+inserted bit+tail
                    $head = array_slice($cells, 0, $splice_at);
                    $tail = array_slice($cells, $splice_at + 1 );
                    $cells = array_merge($head, $data, $tail);
                }
            }


        return $cells;

    }


}

?>