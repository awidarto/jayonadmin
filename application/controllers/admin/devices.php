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
		
	}
	
	public function manage()
	{
	    $this->load->library('table');		
			
		$data = $this->db->get($this->config->item('jayon_devices_table'));
		$result = $data->result_array();
		$this->table->set_heading('Identifier', 'Description','Device Name','Mobile Number','Actions'); // Setting headings for the table
		
		foreach($result as $value => $key)
		{
			$delete = anchor("admin/devices/delete/".$key['id']."/", "Delete"); // Build actions links
			$assign = anchor("admin/devices/assign/".$key['id']."/", "Assignment"); // Build actions links
			$edit = anchor("admin/devices/edit/".$key['id']."/", "Edit"); // Build actions links
			$this->table->add_row($key['identifier'], $key['descriptor'],$key['devname'],$key['mobile'],$edit.' '.$assign.' '.$delete); // Adding row to table
		}
		$page['page_title'] = 'Manage Devices';
		$this->ag_auth->view('devices/manage',$page); // Load the view
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
			$dataset['mobile'] = set_value('mobile'); 
			
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
		$this->form_validation->set_rules('mobile', 'Mobile Number', 'required|trim|xss_clean');
		
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
		
	} // public function register()
	// WOKRING ON PROPER IMPLEMENTATION OF ADDING & EDITING USER ACCOUNTS
}

?>