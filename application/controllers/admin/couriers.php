<?php

class Couriers extends Application
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
			
		$data = $this->db->get($this->config->item('jayon_couriers_table'));
		$result = $data->result_array();
		$this->table->set_heading('Username', 'Email','Full Name','Mobile','Phone','Group','Actions'); // Setting headings for the table
		
		foreach($result as $value => $key)
		{
			$delete = anchor("admin/couriers/delete/".$key['id']."/", "Delete"); // Build actions links
			$editpass = anchor("admin/couriers/editpass/".$key['id']."/", "Change Password"); // Build actions links
			$edit = anchor("admin/couriers/edit/".$key['id']."/", "Edit"); // Build actions links
			$detail = anchor("admin/couriers/details/".$key['id']."/", $key['username']); // Build detail links
			$this->table->add_row($detail, $key['email'],$key['fullname'],$key['mobile'],$key['phone'],$this->get_group_description($key['group_id']),$edit.' '.$editpass.' '.$delete); // Adding row to table
		}
		$page['page_title'] = 'Manage Couriers';
		$this->ag_auth->view('couriers/manage',$page); // Load the view
	}
	
	function details($id){
		$this->load->library('table');		
	
		$user = $this->get_user($id);
		
		foreach($user as $key=>$val){
			$this->table->add_row($key,$val); // Adding row to table
		}
		
		$page['page_title'] = 'Courier Info';
		$this->ag_auth->view('couriers/details',$page);
	}
	
	public function delete($id)
	{
		$this->db->where('id', $id)->delete($this->config->item('jayon_couriers_table'));
		$page['page_title'] = 'Delete Courier';
		$this->ag_auth->view('couriers/delete_success',$page);
	}

	public function get_user($id){
		$result = $this->db->where('id', $id)->get($this->config->item('jayon_couriers_table'));
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
		$result = $this->db->where('id', $id)->update($this->config->item('jayon_couriers_table'),$data);
		return $this->db->affected_rows();
	}
	
	
	public function add()
	{
		$this->form_validation->set_rules('username', 'Username', 'required|min_length[6]|callback_field_exists');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|matches[password_conf]');
		$this->form_validation->set_rules('password_conf', 'Password Confirmation', 'required|min_length[6]|matches[password]');
		$this->form_validation->set_rules('email', 'Email Address', 'required|min_length[6]|valid_email|callback_field_exists');
		$this->form_validation->set_rules('fullname', 'Full Name', 'required|trim|xss_clean');	
		$this->form_validation->set_rules('address', 'Address', 'required|trim|xss_clean');	
		$this->form_validation->set_rules('phone', 'Phone Number', 'required|trim|xss_clean');   
		$this->form_validation->set_rules('mobile', 'Mobile Number', 'required|trim|xss_clean');
		$this->form_validation->set_rules('group_id', 'Group', 'trim');
				
		if($this->form_validation->run() == FALSE)
		{	
			$data['groups'] = array(group_id('courier')=>'Jayon Courier');
			$data['page_title'] = 'Add Courier';
			$this->ag_auth->view('couriers/add',$data);
		}
		else
		{
			$username = set_value('username');
			$password = $this->ag_auth->salt(set_value('password'));
			$fullname = set_value('fullname');
			$address = set_value('address'); 
			$phone= set_value('phone');
			$mobile= set_value('mobile'); 
			$email = set_value('email');
			$group_id = set_value('group_id');

			$dataset = array(
				'username'=>$username,
				'password'=>$password,
				'fullname'=>$fullname,
				'address'=>$address,
				'phone'=>$phone,
				'mobile'=>$mobile, 
				'email'=>$email,
				'group_id'=>$group_id
			);
			
			if($this->db->insert($this->config->item('jayon_couriers_table'),array('username'=>$username, 'password'=>$password, 'email'=>$email, 'group_id'=>$group_id)) === TRUE)
			{
				$data['message'] = "The user account has now been created.";
				$data['page_title'] = 'Add Courier';
				$data['back_url'] = anchor('admin/couriers/manage','Back to list');
				$this->ag_auth->view('message', $data);
				
			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$data['message'] = "The user account has not been created.";
				$data['page_title'] = 'Add Courier Error';
				$data['back_url'] = anchor('admin/couriers/manage','Back to list');
				$this->ag_auth->view('message', $data);
			}

		} // if($this->form_validation->run() == FALSE)
		
	} // public function register()

	public function edit($id)
	{
		$this->form_validation->set_rules('email', 'Email Address', 'required|min_length[6]|valid_email');
		$this->form_validation->set_rules('fullname', 'Full Name', 'required|trim|xss_clean');	
		$this->form_validation->set_rules('address', 'Address', 'required|trim|xss_clean');	
		$this->form_validation->set_rules('phone', 'Phone Number', 'required|trim|xss_clean');   
		$this->form_validation->set_rules('mobile', 'Mobile Number', 'required|trim|xss_clean');
		$this->form_validation->set_rules('group_id', 'Group', 'trim');
		
		$user = $this->get_user($id);
		$data['user'] = $user;
				
		if($this->form_validation->run() == FALSE)
		{
			$data['groups'] = array(group_id('courier')=>group_desc('courier'));
			$data['page_title'] = 'Edit Courier';
			$this->ag_auth->view('couriers/edit',$data);
		}
		else
		{
			$dataset['email'] = set_value('email');
			$dataset['group_id'] = set_value('group_id');

			$dataset['fullname'] = set_value('fullname');
			$dataset['address'] = set_value('address'); 
			$dataset['phone'] = set_value('phone');
			$dataset['mobile'] = set_value('mobile'); 
			
			
			if($this->db->where('id',$id)->update($this->config->item('jayon_couriers_table'),$dataset) === TRUE)
			//if($this->update_user($id,$dataset) === TRUE)
			{
				$data['message'] = "The user account has now updated.";
				$data['page_title'] = 'Edit Courier';
				$data['back_url'] = anchor('admin/couriers/manage','Back to list');
				$this->ag_auth->view('message', $data);
				
			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$data['message'] = "The user account has not been created.";
				$data['page_title'] = 'Edit Courier';
				$data['back_url'] = anchor('admin/couriers/manage','Back to list');
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
			$data['page_title'] = 'Change Courier Password';
			$this->ag_auth->view('couriers/editpass',$data);
		}
		else
		{
			$result = TRUE;
			$dataset['password'] = $this->ag_auth->salt(set_value('password'));

			//if( $result = $this->update_user($id,$dataset))
			if($this->db->where('id',$id)->update($this->config->item('jayon_couriers_table'),$dataset) === TRUE)
			{
				$data['message'] = "The user password has now updated.";
				$data['page_title'] = 'Edit Courier Success';
				$data['back_url'] = anchor('admin/couriers/manage','Back to list');
				$this->ag_auth->view('message', $data);
				
			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$data['message'] = "The user account failed to update.";
				$data['page_title'] = 'Edit Courier Error';
				$data['back_url'] = anchor('admin/couriers/manage','Back to list');
				$this->ag_auth->view('message', $data);
			}

		} // if($this->form_validation->run() == FALSE)
		
	} // public function register()
	// WOKRING ON PROPER IMPLEMENTATION OF ADDING & EDITING USER ACCOUNTS
}

?>