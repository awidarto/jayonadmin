<?php

class Log extends Application
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
		$this->breadcrumb->add_crumb('System','admin/apps/manage');

	}

	public function ajaxaccesslog(){
		$limit_count = $this->input->post('iDisplayLength');
		$limit_offset = $this->input->post('iDisplayStart');

		$sort_col = $this->input->post('iSortCol_0');
		$sort_dir = $this->input->post('sSortDir_0');

		$columns = array(
			'timestamp',
			'accessor_ip',
			'api_key',
			'query',
			'args',
			'result'
		);

		// get total count result
		$count_all = $this->db->count_all($this->config->item('access_log_table'));

		$count_display_all = $this->db
			->count_all_results($this->config->item('access_log_table'));

		//search column
		if($this->input->post('sSearch') != ''){
			$srch = $this->input->post('sSearch');
			//$this->db->like('buyerdeliveryzone',$srch);
			$this->db->or_like('buyerdeliverytime',$srch);
			$this->db->or_like('delivery_id',$srch);
		}

		if($this->input->post('sSearch_0') != ''){
			$this->db->like('timestamp',$this->input->post('sSearch_0'));
		}


		if($this->input->post('sSearch_1') != ''){
			$this->db->like('accessor_ip',$this->input->post('sSearch_1'));
		}

		if($this->input->post('sSearch_2') != ''){
			$this->db->like('api_key',$this->input->post('sSearch_2'));
		}

		if($this->input->post('sSearch_3') != ''){
			$this->db->like('query',$this->input->post('sSearch_3'));
		}

		if($this->input->post('sSearch_4') != ''){
			$this->db->like('args',$this->input->post('sSearch_4'));
		}

		if($this->input->post('sSearch_5') != ''){
			$this->db->like('result',$this->input->post('sSearch_5'));
		}

		/*
		$this->db->select('*,d.identifier as identifier,c.fullname as courier');
		$this->db->join('devices as d',$this->config->item('location_log_table').'.device_id=d.id','left');
		$this->db->join('couriers as c',$this->config->item('location_log_table').'.courier_id=c.id','left');
		*/

		$data = $this->db
			->limit($limit_count, $limit_offset)
			->order_by('id','desc')
			->order_by($columns[$sort_col],$sort_dir)
			->get($this->config->item('access_log_table'));

		//print $this->db->last_query();

		//->group_by(array('buyerdeliverytime','buyerdeliveryzone'))

		$result = $data->result_array();

		$aadata = array();

		foreach($result as $value => $key)
		{

			$aadata[] = array(
				$key['timestamp'],
				$key['accessor_ip'],
				$key['api_key'],
				$key['query'],
				substr($key['args'], 0,40),
				substr($key['result'], 0,50)
			);
		}

		$result = array(
			'sEcho'=> $this->input->post('sEcho'),
			'iTotalRecords'=>$count_all,
			'iTotalDisplayRecords'=> $count_display_all,
			'aaData'=>$aadata
		);

		print json_encode($result);
	}

	public function access()
	{

		$this->breadcrumb->add_crumb('API Access Log','admin/log/access');

		$this->table->set_heading(
			'Timestamp',
			'Accessor IP',
			'API Key',
			'Query Function',
			'Query Params',
			'Result'
		); // Setting headings for the table

		$this->table->set_footing(
			'<input type="text" name="search_deliverytime" id="search_deliverytime" value="Search timestamp" class="search_init" />',
			'<input type="text" name="search_ip" id="search_device" value="Search IP" class="search_init" />',
			'<input type="text" name="search_key" id="search_courier" value="Search API Key" class="search_init" />',
			'<input type="text" name="search_function" id="search_function" value="Search Function" class="search_init" />',
			'<input type="text" name="search_params" id="search_params" value="Search params" class="search_init" />',
			'<input type="text" name="search_result" id="search_result" value="Search result" class="search_init" />'
			);

        $pd = get_print_default();

        if($pd){
            $page['resolution'] = $pd['res'];
            $page['cell_width'] = $pd['cell_width'];
            $page['cell_height'] = $pd['cell_height'];
            $page['columns'] = $pd['col'];
            $page['margin_right'] = $pd['mright'];
            $page['margin_bottom'] = $pd['mbottom'];
            $page['font_size'] = $pd['fsize'];
            $page['code_type'] = $pd['codetype'];
        }else{
            $page['resolution'] = 150;
            $page['cell_width'] = 480;
            $page['cell_height'] = 245;
            $page['columns'] = 2;
            $page['margin_right'] = 18;
            $page['margin_bottom'] = 10;
            $page['font_size'] = 12;
            $page['code_type'] = 'barcode';
        }

		$page['sortdisable'] = '';
		$page['ajaxurl'] = 'admin/log/ajaxaccesslog';
		$page['page_title'] = 'API Access Log';
		$this->ag_auth->view('ajaxlistview',$page); // Load the view

	}

	public function ajaxoutbox(){
		$limit_count = $this->input->post('iDisplayLength');
		$limit_offset = $this->input->post('iDisplayStart');

		$sort_col = $this->input->post('iSortCol_0');
		$sort_dir = $this->input->post('sSortDir_0');

		$columns = array(
			'timestamp',
			'from',
			'to',
			'cc',
			'subject',
			'body',
			'att',
			'delivery_id',
			'status',
			'msg_status'
		);

		// get total count result
		$count_all = $this->db->count_all($this->config->item('jayon_email_outbox_table'));

		$count_display_all = $this->db
			->count_all_results($this->config->item('jayon_email_outbox_table'));

		//search column
		if($this->input->post('sSearch') != ''){
			$srch = $this->input->post('sSearch');
			//$this->db->like('buyerdeliveryzone',$srch);
			$this->db->or_like('buyerdeliverytime',$srch);
			$this->db->or_like('delivery_id',$srch);
		}

		if($this->input->post('sSearch_0') != ''){
			$this->db->like('timestamp',$this->input->post('sSearch_0'));
		}


		if($this->input->post('sSearch_1') != ''){
			$this->db->like('from',$this->input->post('sSearch_1'));
		}

		if($this->input->post('sSearch_2') != ''){
			$this->db->like('to',$this->input->post('sSearch_2'));
		}

		if($this->input->post('sSearch_3') != ''){
			$this->db->like('cc',$this->input->post('sSearch_3'));
		}

		if($this->input->post('sSearch_4') != ''){
			$this->db->like('subject',$this->input->post('sSearch_4'));
		}

		if($this->input->post('sSearch_5') != ''){
			$this->db->like('body',$this->input->post('sSearch_5'));
		}

		if($this->input->post('sSearch_6') != ''){
			$this->db->like('att',$this->input->post('sSearch_6'));
		}

		if($this->input->post('sSearch_7') != ''){
			$this->db->like('delivery_id',$this->input->post('sSearch_7'));
		}

		if($this->input->post('sSearch_8') != ''){
			$this->db->like('status',$this->input->post('sSearch_8'));
		}

		if($this->input->post('sSearch_9') != ''){
			$this->db->like('msg_status',$this->input->post('sSearch_9'));
		}

		/*
		$this->db->select('*,d.identifier as identifier,c.fullname as courier');
		$this->db->join('devices as d',$this->config->item('location_log_table').'.device_id=d.id','left');
		$this->db->join('couriers as c',$this->config->item('location_log_table').'.courier_id=c.id','left');
		*/

		$data = $this->db
			->limit($limit_count, $limit_offset)
			->order_by('timestamp','desc')
			->order_by($columns[$sort_col],$sort_dir)
			->get($this->config->item('jayon_email_outbox_table'));

		//print $this->db->last_query();

		//->group_by(array('buyerdeliverytime','buyerdeliveryzone'))

		$result = $data->result_array();

		$aadata = array();

		foreach($result as $value => $key)
		{

			$aadata[] = array(
				$key['timestamp'],
				$key['from'],
				$key['to'],
				$key['cc'],
				$key['subject'],
				substr($key['body'], 0,100),
				$key['att'],
				$key['delivery_id'],
				$key['status'],
				$key['msg_status']
			);
		}

		$result = array(
			'sEcho'=> $this->input->post('sEcho'),
			'iTotalRecords'=>$count_all,
			'iTotalDisplayRecords'=> $count_display_all,
			'aaData'=>$aadata
		);

		print json_encode($result);
	}

	public function deliverylog($delivery_id){
		$this->table->set_heading(
			'Timestamp',
			'Report Timestamp',
			'Sync ID',
			'Delivery ID',
			'Device ID',
			'Courier ID',
			'Actor',
			'Actor ID',
			'Latitude',
			'Longitude',
			'Status',
			'API Event',
			'Delivery Note',
			'Req By',
			'Req Name',
			'Req Note'
		); // Setting headings for the table

		$data = $this->db
			->where('delivery_id',$delivery_id)
			->order_by('timestamp','desc')
			->get($this->config->item('delivery_log_table'));

		foreach($data->result_array() as $key){
			$this->table->add_row(
				$key['timestamp'],
				$key['report_timestamp'],
				$key['sync_id'],
				$key['delivery_id'],
				$key['device_id'],
				$key['courier_id'],
				$key['actor_type'],
				$key['actor_id'],
				$key['latitude'],
				$key['longitude'],
				$key['status'],
				$key['api_event'],
				$key['notes'],
				$key['req_by'],
				$key['req_name'],
				$key['req_note']
			);
		}

		$this->load->view('print/deliverylog',null); // Load the view

	}

	public function outbox()
	{

		$this->breadcrumb->add_crumb('Email Outbox','admin/log/outbox');

		$this->table->set_heading(
			'Timestamp',
			'From',
			'To',
			'CC',
			'Subject',
			'Body',
			'Attachment',
			'Delivery ID',
			'Delivery Status',
			'Message status'
		); // Setting headings for the table

		$this->table->set_footing(
			'<input type="text" name="search_from" id="search_deliverytime" value="Search timestamp" class="search_init" />',
			'<input type="text" name="search_to" id="search_device" value="Search IP" class="search_init" />',
			'<input type="text" name="search_cc" id="search_courier" value="Search API Key" class="search_init" />',
			'<input type="text" name="search_subject" id="search_function" value="Search Function" class="search_init" />',
			'<input type="text" name="search_body" id="search_body" value="Search params" class="search_init" />',
			'<input type="text" name="search_att" id="search_att" value="Search attachment" class="search_init" />',
			'<input type="text" name="search_delivery_id" id="search_result" value="Search delivery id" class="search_init" />',
			'<input type="text" name="search_status" id="search_result" value="Search delivery status" class="search_init" />',
			'<input type="text" name="search_msg_status" id="search_result" value="Search msg status" class="search_init" />'
			);

		$page['sortdisable'] = '';
		$page['ajaxurl'] = 'admin/log/ajaxoutbox';
		$page['page_title'] = 'Email Outbox';
		$this->ag_auth->view('ajaxlistview',$page); // Load the view

	}

}

?>