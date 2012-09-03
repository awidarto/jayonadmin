<?php

class Devices extends Application
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
	
	public function manage()
	{
	    $this->load->library('table');		

		$this->breadcrumb->add_crumb('Devices','admin/devices/manage');
			
		$data = $this->db->get($this->config->item('jayon_devices_table'));
		$result = $data->result_array();
		$this->table->set_heading('Identifier','Status','Description','Device Name','Device Key','Mobile Number','District','City','Actions'); // Setting headings for the table
		
		foreach($result as $value => $key)
		{
			$onstatus = ($key['is_on'] == 1)?'On':'Off';
			$colorclass = ($key['is_on'] == 1)?'':' red_switch';

			$onswitch = '<span id="'.$key['id'].'" class="onswitch_link'.$colorclass.'" style="cursor:pointer;text-decoration:underline;">'.$onstatus.'</span>'; // Build actions links

			$delete = anchor("admin/devices/delete/".$key['id']."/", "Delete"); // Build actions links
			$assign = anchor("admin/devices/assignment/".$key['id']."/", "Assignment"); // Build actions links
			$edit = anchor("admin/devices/edit/".$key['id']."/", "Edit"); // Build actions links
			$this->table->add_row($key['identifier'],$onswitch, $key['descriptor'],$key['devname'],$key['key'],$key['mobile'],$key['district'],$key['city'],$edit.' '.$assign.' '.$delete); // Adding row to table
		}

		$page['add_button'] = array('link'=>'admin/devices/add','label'=>'Add New Device');
		$page['page_title'] = 'Manage Devices';
		$this->ag_auth->view('devicelistview',$page); // Load the view
	}

	public function ajaxtoggle()
	{
		$id = $this->input->post('id');
		$setsw = $this->input->post('switchto');
		$toggle = ($setsw == 'On')?1:0;
		
		$dataset['is_on'] = $toggle;

		if($this->db->where('id',$id)->update($this->config->item('jayon_devices_table'),$dataset) == TRUE){
			print json_encode(array('result'=>'ok','state'=>$setsw));
		}else{
			print json_encode(array('result'=>'failed'));
		}
	}

	public function ajaxassignment($id)
	{
		$limit_count = $this->input->post('iDisplayLength');
		$limit_offset = $this->input->post('iDisplayStart');

		$sort_col = $this->input->post('iSortCol_0');
		$sort_dir = $this->input->post('sSortDir_0');
		
		$columns = array(
			'assignment_date',		 	 	 	 	 	 	 
			'delivery_id',			 	 	 	 	 	 	 
			'application_name',		 	 	
			'buyer_id',			 	 	
			'merchant_id',			 	 	
			'merchant_trans_id',
			'device_id',			 	 	
			'courier_id',			 	 	
			'shipping_address',	 	 				 
			'phone',				 	 	 	 	 	 	 
			'status',				 	 	 	 	 	 	 
			'reschedule_ref',		 	 	 	 	 	 	 
			'revoke_ref'
		);

		// get total count result
		$count_all = $this->db->count_all($this->config->item('assigned_delivery_table'));

		$count_display_all = $this->db->count_all_results($this->config->item('assigned_delivery_table'));
				
		$data = $this->db->where('device_id',$id)->limit($limit_count, $limit_offset)->order_by($columns[$sort_col],$sort_dir)->get($this->config->item('assigned_delivery_table'));
		
		$result = $data->result_array();
		
		$aadata = array();
		
		foreach($result as $value => $key)
		{
			$delete = anchor("admin/delivery/delete/".$key['id']."/", "Delete"); // Build actions links
			$edit = anchor("admin/delivery/edit/".$key['id']."/", "Edit"); // Build actions links
			$printslip = anchor_popup("admin/prints/deliveryslip/".$key['delivery_id'], "Print Slip"); // Build actions links
			
			$app = $this->get_app_info($key['application_key']);
			
			$aadata[] = array(
				$key['assignment_date'],		 	 	 	 	 	 	 
				$key['delivery_id'],			 	 	 	 	 	 	 
				$app['application_name'],		 	 	
				$key['buyer_id'],			 	 	
				$key['merchant_id'],			 	 	
				$key['merchant_trans_id'],
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
		
		$result = array(
			'sEcho'=> $this->input->post('sEcho'),
			'iTotalRecords'=>$count_all,
			'iTotalDisplayRecords'=> $count_display_all,
			'aaData'=>$aadata
		);
		
		print json_encode($result);
	}

	public function assignment($id)
	{
		$this->breadcrumb->add_crumb('Devices','admin/devices/manage');
		$this->breadcrumb->add_crumb('Assigned Delivery Orders','admin/devices/assignment/'.$id);
		
		$data = $this->db->where('device_id',$id)->order_by('assignment_date','desc')->get($this->config->item('assigned_delivery_table'));
		$result = $data->result_array();
		
		$this->table->set_heading(
			'Assignment Date',			 	 	
			'Delivery ID',			 	 	 	 	 	 	 
			'App Name',	 	 	
			//'App Domain',	 	 	
			'Buyer',			 	 	
			'Merchant',			 	 	
			'Merchant Trans ID',		 	 	 	 	 	 	 
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
				$key['assignment_date'],		 	 	 	 	 	 	 
				$key['delivery_id'],			 	 	 	 	 	 	 
				$app['application_name'],		 	 	
				//$app['domain'],		 	 	
				$key['buyer_id'],			 	 	
				$key['merchant_id'],			 	 	
				$key['merchant_trans_id'],
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
		$page['sortdisable'] = '13';
		$page['ajaxurl'] = 'admin/devices/ajaxassignment/'.$id;
		$page['page_title'] = 'Assigned Delivery Orders';
		$this->ag_auth->view('ajaxlistview',$page); // Load the view
	}

	
	public function delete($id)
	{
		$this->db->where('id', $id)->delete($this->config->item('jayon_devices_table'));
		$page['page_title'] = 'Delete Device';
		$this->ag_auth->view('devices/delete_success',$page);
	}

	public function get_user($id){
		$result = $this->db->where('id', $id)->get($this->config->item('jayon_devices_table'));
		if($result->num_rows() > 0){
			return $result->row_array();
		}else{
			return false;
		}
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
	
	public function update_user($id,$data){
		$result = $this->db->where('id', $id)->update($this->config->item('jayon_devices_table'),$data);
		return $this->db->affected_rows();
	}
	
	
	public function add()
	{
		$this->form_validation->set_rules('identifier', 'Identifier', 'required|trim|xss_clean');		 	 	 	 	 	 	 
		$this->form_validation->set_rules('descriptor', 'Description', 'required|trim|xss_clean');			 	 	 	 	 	 	 
		$this->form_validation->set_rules('devname', 'Device Name', 'required|trim|xss_clean');   		 	 	 	 	 	 	 	 
		$this->form_validation->set_rules('color', 'Color', 'trim|xss_clean');   		 	 	 	 	 	 	 	 
		$this->form_validation->set_rules('district', 'Zone', 'trim|xss_clean');   		 	 	 	 	 	 	 	 
		$this->form_validation->set_rules('city', 'City', 'trim|xss_clean');   		 	 	 	 	 	 	 	 
		$this->form_validation->set_rules('mobile', 'Mobile Number', 'required|trim|xss_clean');
				
		if($this->form_validation->run() == FALSE)
		{	
			$data['groups'] = array(group_id('courier')=>'Jayon Device');
			$data['page_title'] = 'Add Device';
			$this->ag_auth->view('devices/add',$data);
		}
		else
		{

			$dataset['identifier'] = set_value('identifier');
			$dataset['descriptor'] = set_value('descriptor');
			$dataset['devname'] = set_value('devname');
			$dataset['color'] = set_value('color');
			$dataset['mobile'] = set_value('mobile'); 
			$dataset['district'] = set_value('district'); 
			$dataset['city'] = set_value('city'); 
			$dataset['key'] = random_string('sha1',40);			
			
			if($this->db->insert($this->config->item('jayon_devices_table'),$dataset) === TRUE)
			{
				$data['message'] = "The device has now been created.";
				$data['page_title'] = 'Add Device';
				$data['back_url'] = anchor('admin/devices/manage','Back to list');
				$this->ag_auth->view('message', $data);
				
			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$data['message'] = "The device has not been created.";
				$data['page_title'] = 'Add Device Error';
				$data['back_url'] = anchor('admin/devices/manage','Back to list');
				$this->ag_auth->view('message', $data);
			}

		} // if($this->form_validation->run() == FALSE)
		
	} // public function register()

	public function edit($id)
	{
		$this->form_validation->set_rules('identifier', 'Identifier', 'required|trim|xss_clean');		 	 	 	 	 	 	 
		$this->form_validation->set_rules('descriptor', 'Description', 'required|trim|xss_clean');			 	 	 	 	 	 	 
		$this->form_validation->set_rules('devname', 'Device Name', 'required|trim|xss_clean');   		 	 	 	 	 	 	 	 
		$this->form_validation->set_rules('color', 'Color', 'trim|xss_clean');   		 	 	 	 	 	 	 	 
		$this->form_validation->set_rules('mobile', 'Mobile Number', 'required|trim|xss_clean');
		$this->form_validation->set_rules('district', 'Zone', 'trim|xss_clean');
		$this->form_validation->set_rules('city', 'City', 'trim|xss_clean');
		
		$user = $this->get_user($id);
		$data['user'] = $user;
				
		if($this->form_validation->run() == FALSE)
		{
			$data['groups'] = array(group_id('courier')=>group_desc('courier'));
			$data['page_title'] = 'Edit Device';
			$this->ag_auth->view('devices/edit',$data);
		}
		else
		{
			$dataset['identifier'] = set_value('identifier');
			$dataset['descriptor'] = set_value('descriptor');
			$dataset['devname'] = set_value('devname');
			$dataset['color'] = set_value('color');
			$dataset['district'] = set_value('district'); 
			$dataset['city'] = set_value('city'); 
			$dataset['mobile'] = set_value('mobile'); 
			
			
			if($this->db->where('id',$id)->update($this->config->item('jayon_devices_table'),$dataset) === TRUE)
			//if($this->update_user($id,$dataset) === TRUE)
			{
				$data['message'] = "The device has now updated.";
				$data['page_title'] = 'Edit Device';
				$data['back_url'] = anchor('admin/devices/manage','Back to list');
				$this->ag_auth->view('message', $data);
				
			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$data['message'] = "The device has not been created.";
				$data['page_title'] = 'Edit Device';
				$data['back_url'] = anchor('admin/devices/manage','Back to list');
				$this->ag_auth->view('message', $data);
			}

		} // if($this->form_validation->run() == FALSE)
		
	} // public function register()

	public function editpass($id)
	{
		$this->form_validation->set_rules('password', 'Password', 'min_length[6]|matches[password_conf]');
		$this->form_validation->set_rules('password_conf', 'Password Confirmation', 'min_length[6]|matches[password]');

		$user = $this->get_user($id);
		$data['user'] = $user;
				
		if($this->form_validation->run() == FALSE)
		{
			$data['groups'] = array(group_id('courier')=>group_desc('courier'));
			$data['page_title'] = 'Change Device Password';
			$this->ag_auth->view('devices/editpass',$data);
		}
		else
		{
			$result = TRUE;
			$dataset['password'] = $this->ag_auth->salt(set_value('password'));

			//if( $result = $this->update_user($id,$dataset))
			if($this->db->where('id',$id)->update($this->config->item('jayon_devices_table'),$dataset) === TRUE)
			{
				$data['message'] = "The user password has now updated.";
				$data['page_title'] = 'Edit Device Success';
				$data['back_url'] = anchor('admin/devices/manage','Back to list');
				$this->ag_auth->view('message', $data);
				
			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$data['message'] = "The user account failed to update.";
				$data['page_title'] = 'Edit Device Error';
				$data['back_url'] = anchor('admin/devices/manage','Back to list');
				$this->ag_auth->view('message', $data);
			}

		} // if($this->form_validation->run() == FALSE)
		
	} 
	
	public function get_app_info($app_key){
		$result = $this->db->where('key',$app_key)->get($this->config->item('applications_table'));
		return $result->row_array();
	}
}

?>