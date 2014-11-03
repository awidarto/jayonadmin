<?php

class Location extends Application
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

	}

	public function tracker()
	{
		$this->breadcrumb->add_crumb('Location Tracker','admin/location/tracker');

		$data = $this->db->get($this->config->item('location_log_table'));
		$result = $data->result_array();

		$this->table->set_heading(
			'Delivery ID',
			'Application ID',
			'Buyer',
			'Merchant',
			'Merchant Trans ID',
			'Courier',
			'Shipping Address',
			'Phone',
			'Status',
			'Reschedule Ref',
			'Revoke Ref',
			'Actions'
			); // Setting headings for the table

		foreach($result as $value => $key)
		{
			$delete = anchor("admin/location/delete/".$key['id']."/", "Delete"); // Build actions links
			$edit = anchor("admin/location/edit/".$key['id']."/", "Edit"); // Build actions links
			$this->table->add_row(
				$key['delivery_id'],
				$key['application_id'],
				$key['buyer_id'],
				$key['merchant_id'],
				$key['merchant_trans_id'],
				$key['courier_id'],
				$key['shipping_address'],
				$key['phone'],
				$key['status'],
				$key['reschedule_ref'],
				$key['revoke_ref'],
				$edit.' '.$delete
			);
		}
		$page['page_title'] = 'Location Tracker';
		$this->ag_auth->view('location/tracker',$page); // Load the view
	}

	public function ajaxlog(){
		$limit_count = $this->input->post('iDisplayLength');
		$limit_offset = $this->input->post('iDisplayStart');

		$sort_col = $this->input->post('iSortCol_0');
		$sort_dir = $this->input->post('sSortDir_0');

		$columns = array(
			'timestamp',
			'device_id',
			'identifier',
			'courier_id',
			'latitude',
			'longitude',
			'status',
			'notes'
		);

		// get total count result
		$count_all = $this->db->count_all($this->config->item('location_log_table'));

		$count_display_all = $this->db
			->count_all_results($this->config->item('location_log_table'));

		//search column
		if($this->input->post('sSearch') != ''){
			$srch = $this->input->post('sSearch');
			//$this->db->like('buyerdeliveryzone',$srch);
			$this->db->or_like('buyerdeliverytime',$srch);
			$this->db->or_like('delivery_id',$srch);
		}

		if($this->input->post('sSearch_0') != ''){
			$this->db->like($this->config->item('location_log_table').'.timestamp',$this->input->post('sSearch_0'));
		}


		if($this->input->post('sSearch_1') != ''){
			$this->db->like('d.identifier',$this->input->post('sSearch_1'));
		}

		if($this->input->post('sSearch_2') != ''){
			$this->db->like('c.courier',$this->input->post('sSearch_2'));
		}

		if($this->input->post('sSearch_3') != ''){
			$this->db->like($this->config->item('location_log_table').'.status',$this->input->post('sSearch_3'));
		}


		$this->db->select('*,d.identifier as identifier,c.fullname as courier');
		$this->db->join('devices as d',$this->config->item('location_log_table').'.device_id=d.id','left');
		$this->db->join('couriers as c',$this->config->item('location_log_table').'.courier_id=c.id','left');


		$data = $this->db
			->limit($limit_count, $limit_offset)
			->order_by($this->config->item('location_log_table').'.timestamp','desc')
			->order_by($columns[$sort_col],$sort_dir)
			->get($this->config->item('location_log_table'));

		//print $this->db->last_query();

		//->group_by(array('buyerdeliverytime','buyerdeliveryzone'))

		$result = $data->result_array();

		$aadata = array();

		foreach($result as $value => $key)
		{

			$aadata[] = array(
				$key['timestamp'],
				$key['identifier'],
				$key['courier'],
				colorizelatlon($key['latitude']),
				colorizelatlon($key['longitude']),
				$key['status']
			);
		}

		$result = array(
			'sEcho'=> $this->input->post('sEcho'),
			'iTotalRecords'=>$count_all,
			'iTotalDisplayRecords'=> $count_display_all,
			'aaData'=>$aadata,
			'q'=>$this->db->last_query()
		);

		print json_encode($result);
	}

	public function log()
	{

		$this->breadcrumb->add_crumb('Location','admin/location');
		$this->breadcrumb->add_crumb('Location Log','admin/location/log');

		/*
		$devices = $this->db->distinct()
			->select('identifier')
			->get($this->config->item('location_log_table'))
			->result();

		$page['devices'] = $devices;

		$locations = array();

		$paths = array();

		foreach($devices as $d){
			$loc = $this->db
				->select('identifier,timestamp,latitude as lat,longitude as lng')
				->where('identifier',$d->identifier)
				->like('timestamp',date('Y-m-d',time()),'after')
				//->limit(10,0)
				->order_by('timestamp','desc')
				->get($this->config->item('location_log_table'));

			if($loc->num_rows() > 0){
				$loc = $loc->result();
				foreach($loc as $l){
					$locations[] = array(
						'lat'=>(double)$l->lat,
						'lng'=>(double)$l->lng,
						'data'=>array(
								'timestamp'=>$l->timestamp,
								'identifier'=>$l->identifier
							)
						);
					$paths[$d->identifier][] = array((double)$l->lat,(double)$l->lng);
				}
			}
		}

		foreach($paths as $key=>$val){

			$pathcmd[] = "{ action: 'addPolyline',
								options:{
								strokeColor: '".get_device_color($key)."',
								strokeOpacity: 1.0,
								strokeWeight: 2
							},
							path:".json_encode($val)."}";

		}

		$pathcmd = implode(',', $pathcmd);

		$page['pathcmd'] = $pathcmd;
		//print_r($paths);

		$page['locdata'] = json_encode($locations);

		*/

		$this->table->set_heading(
			'Timestamp',
			'Device Identifier',
			'Courier',
			'Latitude',
			'Longitude',
			'Status'
			); // Setting headings for the table

		$devs = array();
		$devs['Search device'] = '-';
		foreach (get_devices() as $dev) {
			$devs[$dev->identifier] = $dev->identifier;
		}

		$dev_search = form_dropdown('search_device',$devs,'Search device','id="search_device"');

		$this->table->set_footing(
			'<input type="text" name="search_deliverytime" id="search_deliverytime" value="Search timestamp" class="search_init" />',
			'<input type="text" name="search_device" id="search_device" value="Search device" class="search_init" />',
			'<input type="text" name="search_courier" id="search_courier" value="Search courier" class="search_init" />',
			'',
			'',
			'<input type="text" name="search_status" id="search_status" value="Search status" class="search_init" />'
			);



		$page['sortdisable'] = '';
		$page['ajaxurl'] = 'ajaxpos/ajaxlog';
		$page['page_title'] = 'Location Log';
		$this->ag_auth->view('location/log',$page); // Load the view

	}

    public function distribution()
    {

        $this->breadcrumb->add_crumb('Reports','admin/reports/statistics');
        $this->breadcrumb->add_crumb('Buyer Distribution','admin/location/distribution');

        $this->table->set_heading(
            'Timestamp',
            'Device Identifier',
            'Courier',
            'Latitude',
            'Longitude',
            'Status'
            ); // Setting headings for the table

        $devs = array();
        $devs['Search device'] = '-';
        foreach (get_devices() as $dev) {
            $devs[$dev->identifier] = $dev->identifier;
        }

        $dev_search = form_dropdown('search_device',$devs,'Search device','id="search_device"');

        $this->table->set_footing(
            '<input type="text" name="search_deliverytime" id="search_deliverytime" value="Search timestamp" class="search_init" />',
            '<input type="text" name="search_device" id="search_device" value="Search device" class="search_init" />',
            '<input type="text" name="search_courier" id="search_courier" value="Search courier" class="search_init" />',
            '',
            '',
            '<input type="text" name="search_status" id="search_status" value="Search status" class="search_init" />'
            );



        $page['sortdisable'] = '';
        $page['ajaxurl'] = 'ajaxpos/ajaxdistrib';
        $page['page_title'] = 'Buyer Distribution';
        $this->ag_auth->view('location/distrib',$page); // Load the view

    }


    public function router()
    {

        $this->breadcrumb->add_crumb('Location','admin/location');
        $this->breadcrumb->add_crumb('Router','admin/location/router');

        $this->table->set_heading(
            'Delivery Date',
            'Seq',
            'Device Identifier',
            'Address',
            'Lat Lon',
            'Status'
            ); // Setting headings for the table

        $devs = array();
        $devs['Search device'] = '-';
        foreach (get_devices() as $dev) {
            $devs[$dev->identifier] = $dev->identifier;
        }

        $dev_search = form_dropdown('search_device',$devs,'Search device','id="search_device"');

        $this->table->set_footing(
            '<input type="text" name="search_deliverydate" id="search_deliverydate" value="Search delivery date" class="search_init" />',
            '<button id="save_seq">Simpan Urutan</button>',
            '<input type="text" name="search_device" id="search_device" value="Search device" class="search_init" />',
            '<input type="text" name="search_address" id="search_address" value="Search address" class="search_init" />',
            '',
            '<input type="text" name="search_status" id="search_status" value="Search status" class="search_init" />'
            );



        $page['sortdisable'] = '';
        $page['ajaxurl'] = 'ajaxpos/ajaxrouter';
        $page['page_title'] = 'Router';
        $this->ag_auth->view('location/router',$page); // Load the view

    }


	public function delete($id)
	{
		$this->db->where('id', $id)->delete($this->ag_auth->config['auth_user_table']);
		$this->ag_auth->view('users/delete_success');
	}

	public function get_group(){
		$this->db->select('id,description');
		$result = $this->db->get($this->ag_auth->config['auth_group_table']);
		foreach($result->result_array() as $row){
			$res[$row['id']] = $row['description'];
		}
		return $res;
	}

	public function get_group_description($id){
		$this->db->select('description');
		if(!is_null($id)){
			$this->db->where('id',$id);
		}
		$result = $this->db->get($this->ag_auth->config['auth_group_table']);
		$row = $result->row();
		return $row->description;
	}

	public function add()
	{
		$this->form_validation->set_rules('username', 'Username', 'required|min_length[6]|callback_field_exists');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|matches[password_conf]');
		$this->form_validation->set_rules('password_conf', 'Password Confirmation', 'required|min_length[6]|matches[password]');
		$this->form_validation->set_rules('email', 'Email Address', 'required|min_length[6]|valid_email|callback_field_exists');
		$this->form_validation->set_rules('group_id', 'Group', 'trim');

		if($this->form_validation->run() == FALSE)
		{
			$data['groups'] = $this->get_group();
			$this->ag_auth->view('users/add',$data);
		}
		else
		{
			$username = set_value('username');
			$password = $this->ag_auth->salt(set_value('password'));
			$email = set_value('email');
			$group_id = set_value('group_id');

			if($this->ag_auth->register($username, $password, $email, $group_id) === TRUE)
			{
				$data['message'] = "The user account has now been created.";
				$this->ag_auth->view('message', $data);

			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$data['message'] = "The user account has not been created.";
				$this->ag_auth->view('message', $data);
			}

		} // if($this->form_validation->run() == FALSE)

	} // public function register()

	public function edit($username)
	{
		$this->form_validation->set_rules('username', 'Username', 'required|min_length[6]|callback_field_exists');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|matches[password_conf]');
		$this->form_validation->set_rules('password_conf', 'Password Confirmation', 'required|min_length[6]|matches[password]');
		$this->form_validation->set_rules('email', 'Email Address', 'required|min_length[6]|valid_email|callback_field_exists');
		$this->form_validation->set_rules('group_id', 'Group', 'trim');

		if($this->form_validation->run() == FALSE)
		{
			$data['groups'] = $this->get_group();
			$this->ag_auth->view('users/add',$data);
		}
		else
		{
			$username = set_value('username');
			$password = $this->ag_auth->salt(set_value('password'));
			$email = set_value('email');
			$group_id = set_value('group_id');

			if($this->ag_auth->register($username, $password, $email, $group_id) === TRUE)
			{
				$data['message'] = "The user account has now been created.";
				$this->ag_auth->view('message', $data);

			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$data['message'] = "The user account has not been created.";
				$this->ag_auth->view('message', $data);
			}

		} // if($this->form_validation->run() == FALSE)

	} // public function register()


	// WOKRING ON PROPER IMPLEMENTATION OF ADDING & EDITING USER ACCOUNTS
}

?>
