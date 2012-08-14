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

	public function statistics($type = null,$year = null, $scope = null, $par1 = null, $par2 = null, $par3 = null){
		//$this->breadcrumb->add_crumb('Reports','admin/reports/daily');
		$this->breadcrumb->add_crumb('Statistics','admin/reports/statistics');

		//$year = date('Y',time());
		//$month = date('m',time());

		//$page['period'] = ' - '.date('M Y',time());


		$type = (is_null($type))?'Global':$type;
		$id = (is_null($type))?'noid':$type;

		$pdf = null;

		if($scope == 'month'){
			$days = cal_days_in_month(CAL_GREGORIAN, $par1, $year);
			$from =	date('d-m-Y', strtotime($year.'/'.$par1.'/1'));
			$to =	date('d-m-Y', strtotime($year.'/'.$par1.'/'.$days));
			$pdf = $par2;

			$data['month'] = $par1;
			$data['week'] = 1;
		}else if($scope == 'week'){
			$from =	date('d-m-Y', strtotime('1 Jan '.$year.' +'.($par1 - 1).' weeks'));
			$to = date('d-m-Y', strtotime('1 Jan '.$year.' +'.$par1.' weeks - 1 day'));
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

		$pdf = null;

		if($scope == 'month'){
			$days = cal_days_in_month(CAL_GREGORIAN, $par1, $year);
			$from =	date('d-m-Y', strtotime($year.'/'.$par1.'/1'));
			$to =	date('d-m-Y', strtotime($year.'/'.$par1.'/'.$days));
			$pdf = $par2;

			$data['month'] = $par1;
			$data['week'] = 1;
		}else if($scope == 'week'){
			$from =	date('d-m-Y', strtotime('1 Jan '.$year.' +'.($par1 - 1).' weeks'));
			$to = date('d-m-Y', strtotime('1 Jan '.$year.' +'.$par1.' weeks - 1 day'));
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

		$pdf = null;

		if($scope == 'month'){
			$days = cal_days_in_month(CAL_GREGORIAN, $par1, $year);
			$from =	date('d-m-Y', strtotime($year.'/'.$par1.'/1'));
			$to =	date('d-m-Y', strtotime($year.'/'.$par1.'/'.$days));
			$pdf = $par2;

			$data['month'] = $par1;
			$data['week'] = 1;
		}else if($scope == 'week'){
			$from =	date('d-m-Y', strtotime('1 Jan '.$year.' +'.($par1 - 1).' weeks'));
			$to = date('d-m-Y', strtotime('1 Jan '.$year.' +'.$par1.' weeks - 1 day'));
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

			$from =	date('d-m-Y', strtotime('1 Jan '.$year.' +'.($i - 1).' weeks'));
			$to = date('d-m-Y', strtotime('1 Jan '.$year.' +'.$i.' weeks - 1 day'));

			$printrecon = '<span class="printrecon" id="'.$from.'_'.$to.'_noid'.'" title="Global" style="cursor:pointer;text-decoration:underline;" >Global</span>';


			$generate = anchor("admin/reports/globalreport/".$from."/".$to, "Global"); // Build actions links
			$merchantlist = anchor("admin/reports/merchants/".$from."/".$to, "By Merchant"); // Build actions links
			$courierlist = anchor("admin/reports/couriers/".$from."/".$to, "By Courier"); // Build actions links
			$aadata[] = array(
				$year,
				$i,
				date('d-m-Y', strtotime('1 Jan '.$year.' +'.($i - 1).' weeks')),
				date('d-m-Y', strtotime('1 Jan '.$year.' +'.$i.' weeks - 1 day')),
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