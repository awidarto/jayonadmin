<?php

class Delivery extends Application
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
	
	public function incoming()
	{
		$this->breadcrumb->add_crumb('Incoming Delivery Orders','admin/delivery/incoming');

		$data = $this->db->where('status !=','assigned')->get($this->config->item('incoming_delivery_table'));
		
		$result = $data->result_array();
		
		$this->table->set_heading(
			'Delivery ID',			 	 	 	 	 	 	 
			'App Name',	 	 	
			//'App Domain',	 	 	
			'Zone',
			'Delivery Time',
			'Buyer',			 	 	
			'Merchant',			 	 	
			'Merchant Trans ID',		 	 	 	 	 	 	 
			'Shipping Address',	 	 				 
			'Phone',				 	 	 	 	 	 	 
			'Status',				 	 	 	 	 	 	 
			'Reschedule Ref',		 	 	 	 	 	 	 
			'Revoke Ref',
			'Actions'
			); // Setting headings for the table
		
		foreach($result as $value => $key)
		{
			$delete = anchor("admin/delivery/deleteassigned/".$key['id']."/", "Delete"); // Build actions links
			$edit = anchor("admin/delivery/edit/".$key['id']."/", "Edit"); // Build actions links
			$assign = anchor("admin/delivery/assign/".$key['delivery_id']."/", "Assign"); // Build actions links
			
			$app = $this->get_app_info($key['application_key']);
			
			$this->table->add_row(
				$key['delivery_id'],			 	 	 	 	 	 	 
				$app['application_name'],		 	 	
				//$app['domain'],		 	 	
				$key['buyerdeliveryzone'],			 	 	
				$key['buyerdeliverytime'],			 	 	
				$key['buyer_id'],			 	 	
				$key['merchant_id'],			 	 	
				$key['merchant_trans_id'],		 	 	 	 	 	 	 
				$key['shipping_address'],	 	 				 
				$key['phone'],				 	 	 	 	 	 	 
				$key['status'],				 	 	 	 	 	 	 
				$key['reschedule_ref'],		 	 	 	 	 	 	 
				$key['revoke_ref'],
				($key['status'] == 'confirm')?$assign:''.' '.$edit.' '.$delete
			);
		}

		$page['page_title'] = 'Incoming Delivery Orders';
		$this->ag_auth->view('listview',$page); // Load the view
	}

	public function assigned()
	{
		$this->breadcrumb->add_crumb('Assigned Delivery Orders','admin/delivery/assigned');
		
		$data = $this->db->get($this->config->item('assigned_delivery_table'));
		$result = $data->result_array();
		
		$this->table->set_heading(
			'Delivery ID',			 	 	 	 	 	 	 
			'App Name',	 	 	
			'App Domain',	 	 	
			'Buyer',			 	 	
			'Merchant',			 	 	
			'Merchant Trans ID',		 	 	 	 	 	 	 
			'Assignment Date',			 	 	
			'Device',			 	 	
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
			$delete = anchor("admin/delivery/delete/".$key['id']."/", "Delete"); // Build actions links
			$edit = anchor("admin/delivery/edit/".$key['id']."/", "Edit"); // Build actions links
			$printslip = anchor_popup("admin/prints/deliveryslip/".$key['delivery_id'], "Print Slip"); // Build actions links
			
			$app = $this->get_app_info($key['application_key']);
			
			$this->table->add_row(
				$key['delivery_id'],			 	 	 	 	 	 	 
				$app['application_name'],		 	 	
				$app['domain'],		 	 	
				$key['buyer_id'],			 	 	
				$key['merchant_id'],			 	 	
				$key['merchant_trans_id'],
				$key['assignment_date'],		 	 	 	 	 	 	 
				$key['device_id'],			 	 	
				$key['courier_id'],			 	 	
				$key['shipping_address'],	 	 				 
				$key['phone'],				 	 	 	 	 	 	 
				$key['status'],				 	 	 	 	 	 	 
				$key['reschedule_ref'],		 	 	 	 	 	 	 
				$key['revoke_ref'],
				$printslip.' '.$edit.' '.$delete
			);
		}
		
		$page['page_title'] = 'Assigned Delivery Orders';
		$this->ag_auth->view('listview',$page); // Load the view
	}
	
	public function delivered()
	{
		$this->breadcrumb->add_crumb('Delivered Orders','admin/delivery/delivered');
		
		$data = $this->db->where('status','delivered')->get($this->config->item('delivered_delivery_table'));
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
			$delete = anchor("admin/delivery/delete/".$key['id']."/", "Delete"); // Build actions links
			$edit = anchor("admin/delivery/edit/".$key['id']."/", "Edit"); // Build actions links
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

		$page['page_title'] = 'Delivered Orders';
		$this->ag_auth->view('listview',$page); // Load the view
	}

	public function log()
	{
		$this->breadcrumb->add_crumb('Delivery Log','admin/delivery/log');
		
		$data = $this->db->get($this->config->item('delivery_log_table'));
		$result = $data->result_array();
		
		$this->table->set_heading(
			'Delivery ID',			 	 	 	 	 	 	 
			'Device ID',			 	 	 	 	 	 	 
			'Courier',			 	 	
			'Latitude',	 	 				 
			'Longitude',				 	 	 	 	 	 	 
			'Status',				 	 	 	 	 	 	 
			'Note'
			); // Setting headings for the table
		
		foreach($result as $value => $key)
		{
			$delete = anchor("admin/delivery/delete/".$key['id']."/", "Delete"); // Build actions links
			$edit = anchor("admin/delivery/edit/".$key['id']."/", "Edit"); // Build actions links
			
			$app = $this->get_app_info($key['application_key']);
			
			$this->table->add_row(
				$key['delivery_id'],			 	 	 	 	 	 	 
				$key['device_id'],		 	 	 	 	 	 	 
				$key['courier_id'],			 	 	
				$key['latitude'],	 	 				 
				$key['longitude'],				 	 	 	 	 	 	 
				$key['status'],				 	 	 	 	 	 	 
				$key['note']
			);
		}
		
		$page['page_title'] = 'Delivery Log';
		$this->ag_auth->view('listview',$page); // Load the view
	}
	
	public function deleteassigned($id)
	{
		$this->db->where('id', $id)->delete($this->config->item('assigned_delivery_table'));
		
		$data['page_title'] = 'Delete';
		$data['message'] = "Delivery order is now assigned to device.";
		$data['back_url'] = anchor('admin/delivery/assigned','Back to list');
		$this->ag_auth->view('message', $data);
	}

	public function delete($id)
	{
		$this->db->where('id', $id)->delete($this->config->item('incoming_delivery_table'));

		$data['page_title'] = 'Delete';
		$data['message'] = "Delivery order is now assigned to device.";
		$data['back_url'] = anchor('admin/delivery/incoming','Back to list');
		$this->ag_auth->view('message', $data);
	}
	
	public function get_devices(){
		$this->db->select('id,identifier,descriptor,devname,mobile');
		$result = $this->db->get($this->config->item('jayon_devices_table'));
		foreach($result->result_array() as $row){
			$res[$row['id']] = $row['descriptor'].'['.$row['mobile'].']';
		}
		return $res;
	}

	public function get_device_info($device_id){
		$result = $this->db->where('id',$device_id)->get($this->config->item('jayon_devices_table'));
		return $result->row_array();
	}

	public function get_app_info($app_key){
		$result = $this->db->where('key',$app_key)->get($this->config->item('applications_table'));
		return $result->row_array();
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
			$page['page_title'] = 'Add User';
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
	
	public function assign($delivery_id){
		
		$this->form_validation->set_rules('device_id', 'Device ID', 'required|trim|xss_clean');
		$this->form_validation->set_rules('assignment_date', 'Assignment Date', 'required|trim|xss_clean');
				
		if($this->form_validation->run() == FALSE)
		{
			$data['devices'] = $this->get_devices();
			$data['delivery_id'] = $delivery_id;

			$data['page_title'] = 'Delivery Assigment - '.$delivery_id;
			$data['back_url'] = anchor('admin/delivery/assigned','Back to list');
			$this->ag_auth->view('delivery/assign',$data);
		}
		else
		{
			$device_id = set_value('device_id');
			$assignment_date = set_value('assignment_date');
			
			
			$incoming = $this->db->where('delivery_id',$delivery_id)->get($this->config->item('incoming_delivery_table'));

			$dataset = $incoming->row_array();
			unset($dataset['id']);
			$dataset['device_id'] = $device_id;
			$dataset['status'] = 'assigned';
			$dataset['assigntime'] = date('Y-m-d h:i:s',time());
			$dataset['assignment_date'] = $assignment_date;
			
			$chk = $this->db->where('delivery_id',$delivery_id)->get($this->config->item('assigned_delivery_table'));
			if($chk->num_rows() > 0){
				$order_exist = true; 
			}else{
				$order_exist = false; 
			}
			
			if($order_exist){
				$data['message'] = 'Delivery order: '.$delivery_id.' already assigned. Please use "re-assign" in Assigned Delivery list';
				$data['back_url'] = anchor('admin/delivery/assigned','Back to list');
				$this->ag_auth->view('message', $data);
			}else{
				if($this->db->insert($this->config->item('assigned_delivery_table'),$dataset) === TRUE)
				{
					$this->db->where('delivery_id',$delivery_id)->update($this->config->item('incoming_delivery_table'),array('status'=>'assigned'));
					$data['page_title'] = 'Delivery Assigment - '.$delivery_id;
					$data['message'] = "Delivery order is now assigned to device.";
					$data['back_url'] = anchor('admin/delivery/assigned','Back to list');
					$this->ag_auth->view('message', $data);
				} // if($this->ag_auth->register($username, $password, $email) === TRUE)
				else
				{
					$data['page_title'] = 'Delivery Assigment - '.$delivery_id;
					$data['message'] = "Failed to assign delivery order.";
					$data['back_url'] = anchor('admin/delivery/assigned','Back to list');
					$this->ag_auth->view('message', $data);
				}
			}

		} // if($this->form_validation->run() == FALSE)
		
	}

}

?>