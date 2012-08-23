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
		$this->db->select('assignment_date,status,cod_cost,delivery_cost,count(*) as count, sum(cod_cost) as cod_cost,sum(delivery_cost) as delivery_cost, sum(((total_price-total_discount)+total_tax)) as package_value');
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
				$this->db->or_where('status',$this->config->item('trans_status_mobile_revoked'));
				$this->db->or_where('status',$this->config->item('trans_status_mobile_noshow'));
				$this->db->or_where('status',$this->config->item('trans_status_mobile_rescheduled'));
			$this->db->group_end();

		$this->db->group_by('assignment_date,cod_cost');

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
			'Packet Value',
			'COD Count',
			'COD Packet Value',
			'COD Surcharge',
			'Delivery Only',
			'Delivery Fee',
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
				$tarray[$seq]['cod_package_value'] = 0;
				$tarray[$seq]['package_value'] = 0;
			}

			if($r->assignment_date != $rowdate){
				$seq++;
				$rowdate = $r->assignment_date;
				$tarray[$seq]['cod_package_value'] = 0;
				$tarray[$seq]['package_value'] = 0;
			}

			//print $seq.' '.$r->assignment_date.' '.$rowdate.' '.$r->count."\r\n";
			$tarray[$seq]['assignment_date'] = $r->assignment_date;
			$tarray[$seq][$r->status] = $r->count;
			$tarray[$seq]['cod_count'] = ($r->cod_cost > 0 )?$r->count:0;
			$tarray[$seq]['do_count'] = ($r->cod_cost > 0 )?0:$r->count;

			$tarray[$seq]['cod_cost'] = $r->cod_cost;
			$tarray[$seq]['do_cost'] = $r->delivery_cost;
			$tarray[$seq]['package_value'] += $r->package_value;
			$tarray[$seq]['cod_package_value'] += ($r->cod_cost > 0 )?$r->package_value:0;

		}

		$seq = 1;
		$aseq = 0;


		$tdl = 0;
		$tns = 0;
		$trs = 0;
		$tcod = 0;
		$tdo = 0;
		$tcodc = 0;
		$tdoc = 0;
		$tpv = 0;
		$tcpv = 0;

		foreach ($tarray as $r) {

			$dl = (isset($r['delivered']))?$r['delivered']:0;
			$ns = (isset($r['noshow']))?$r['noshow']:0;
			$rs = (isset($r['rescheduled']))?$r['rescheduled']:0;

			$tdl += $dl;
			$tns += $ns;
			$trs += $rs;
			$tcod += $r['cod_count'];
			$tdo += $r['do_count'];

			$tcodc += $r['cod_cost'];
			$tdoc += $r['do_cost'];
			$tpv += $r['package_value'];
			$tcpv += $r['cod_package_value'];

			$this->table->add_row(
				$seq,		
				date('d M Y',strtotime($r['assignment_date'])),		
				array('data'=>number_format((int)str_replace('.','',$r['package_value']),2,',','.'),'class'=>'right'),
				$r['cod_count'],
				array('data'=>number_format((int)str_replace('.','',$r['cod_package_value']),2,',','.'),'class'=>'right'),
				array('data'=>number_format((int)str_replace('.','',$r['cod_cost']),2,',','.'),'class'=>'right'),
				$r['do_count'],
				array('data'=>number_format((int)str_replace('.','',$r['do_cost']),2,',','.'),'class'=>'right'),
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
			array('data'=>number_format((int)str_replace('.','',$tpv),2,',','.'),'style'=>'border-top:thin solid grey','class'=>'right'),		
			array('data'=>$tcod,'style'=>'border-top:thin solid grey'),		
			array('data'=>number_format((int)str_replace('.','',$tcpv),2,',','.'),'style'=>'border-top:thin solid grey','class'=>'right'),		
			array('data'=>number_format((int)str_replace('.','',$tcodc),2,',','.'),'style'=>'border-top:thin solid grey','class'=>'right'),		
			array('data'=>$tdo,'style'=>'border-top:thin solid grey'),		
			array('data'=>number_format((int)str_replace('.','',$tdoc),2,',','.'),'style'=>'border-top:thin solid grey','class'=>'right'),		
			array('data'=>$tdl,'style'=>'border-top:thin solid grey'),		
			array('data'=>$tns,'style'=>'border-top:thin solid grey'),		
			array('data'=>$trs,'style'=>'border-top:thin solid grey'),		
			array('data'=>$gt,'style'=>'border-top:thin solid grey')		
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