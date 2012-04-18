<?php

class Users extends Application
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
		$this->breadcrumb->add_crumb('Users','admin/users/manage');
		
		
	}
	
	public function manage()
	{
	    $this->load->library('table');		
			
		$this->breadcrumb->add_crumb('Administrators','admin/users/manage');
			
		$data = $this->db->get($this->ag_auth->config['auth_user_table']);
		$result = $data->result_array();
		$this->table->set_heading('Username', 'Email','Full Name','Mobile Number','Group','Actions'); // Setting headings for the table
		
		foreach($result as $value => $key)
		{
			$delete = anchor("admin/users/delete/".$key['id']."/", "Delete"); // Build actions links
			$editpass = anchor("admin/users/editpass/".$key['id']."/", "Change Password"); // Build actions links
			$edit = anchor("admin/users/edit/".$key['id']."/", "Edit"); // Build actions links
			$detail = anchor("admin/users/details/".$key['id']."/", $key['username']); // Build detail links
			$this->table->add_row($detail, $key['email'],$key['fullname'],$key['mobile'],$this->get_group_description($key['group_id']),$edit.' '.$editpass.' '.$delete); // Adding row to table
		}
		$page['add_button'] = array('link'=>'admin/users/add','label'=>'Add New Admin');
		$page['page_title'] = 'Manage Admin';
		$this->ag_auth->view('listview',$page); // Load the view
	}

	function details($id){
		$this->load->library('table');		
	
		$user = $this->get_user($id);
		
		foreach($user as $key=>$val){
			$this->table->add_row($key,$val); // Adding row to table
		}
		
		$page['page_title'] = 'Admin Info';
		$this->ag_auth->view('users/details',$page);
	}
		
	public function delete($id)
	{
		$this->db->where('id', $id)->delete($this->ag_auth->config['auth_user_table']);
		$page['page_title'] = 'Delete User';
		$this->ag_auth->view('users/delete_success',$page);
	}

	public function get_user($id){
		$result = $this->db->where('id', $id)->get($this->ag_auth->config['auth_user_table']);
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
	
	public function get_admin_zone($id){
		$q = $this->db->where('user_id',$id)->get('admin_zones');
		if($q->num_rows() > 0){
			foreach($q->result_array() as $val){
				$result[] = $val['district'];
			}
			return $result;
		}else{
			return false;
		}
	}
	
	public function update_user($id,$data){
		$result = $this->db->where('id', $id)->update($this->ag_auth->config['auth_user_table'],$data);
		return $result;
	}
	
	
	public function add()
	{
		$this->form_validation->set_rules('username', 'Username', 'required|min_length[6]|callback_field_exists');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|matches[password_conf]');
		$this->form_validation->set_rules('password_conf', 'Password Confirmation', 'required|min_length[6]|matches[password]');
		$this->form_validation->set_rules('fullname', 'Full Name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('mobile', 'Mobile Number', 'required|trim|xss_clean');
		$this->form_validation->set_rules('district[]', 'Administration Area', 'xss_clean');
		$this->form_validation->set_rules('email', 'Email Address', 'required|min_length[6]|valid_email|callback_field_exists');
		$this->form_validation->set_rules('group_id', 'Group', 'trim');
				
		if($this->form_validation->run() == FALSE)
		{
			$data['groups'] = $this->get_group();
			$data['page_title'] = 'Add User';
			$this->ag_auth->view('users/add',$data);
		}
		else
		{
			$dataset['username'] = set_value('username');
			$dataset['password'] = $this->ag_auth->salt(set_value('password'));
			$dataset['email'] = set_value('email');
			$dataset['fullname'] = set_value('fullname');
			$dataset['mobile'] = set_value('mobile');
			$dataset['group_id'] = set_value('group_id');
			
			$districts = $this->input->post('district');
			

			if($this->db->insert($this->config->item('auth_user_table'),$dataset) === TRUE)
			//if($this->ag_auth->register($username, $password, $email, $group_id) === TRUE)
			{
				$new_id = $this->db->insert_id();
				
				if($districts != null && is_array($districts)){
					$idx = 0;
					foreach($districts as $d){
						$district[$idx]['user_id'] = $new_id;
						$district[$idx]['district'] = $d;
						$idx++;
					}
					$this->db->insert_batch('admin_zones',$district);
				}
				
				$data['message'] = "The user account has now been created.";
				$data['page_title'] = 'Add User';
				$this->ag_auth->view('message', $data);
				
			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$data['message'] = "The user account has not been created.";
				$data['page_title'] = 'Add User';
				$this->ag_auth->view('message', $data);
			}

		} // if($this->form_validation->run() == FALSE)
		
	} // public function register()

	public function edit($id)
	{
		$this->form_validation->set_rules('email', 'Email Address', 'required|min_length[6]|valid_email');
		$this->form_validation->set_rules('group_id', 'Group', 'trim');
		$this->form_validation->set_rules('fullname', 'Full Name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('district[]', 'Administration Area', 'xss_clean');
		$this->form_validation->set_rules('mobile', 'Mobile Number', 'required|trim|xss_clean');
		
		$user = $this->get_user($id);
		$data['user'] = $user;
				
		if($this->form_validation->run() == FALSE)
		{
			$data['groups'] = $this->get_group();
			$data['district'] = $this->get_admin_zone($id);
			$data['page_title'] = 'Edit User';
			$this->ag_auth->view('users/edit',$data);
		}
		else
		{
			$dataset['email'] = set_value('email');
			$dataset['group_id'] = set_value('group_id');
			$dataset['fullname'] = set_value('fullname');
			$dataset['mobile'] = set_value('mobile');

			$districts = $this->input->post('district');
			
			
			if($this->update_user($id,$dataset) === TRUE)
			{
				if($districts != null && is_array($districts)){
					$this->db->where('user_id',$id)->delete('admin_zones');
					$idx = 0;
					foreach($districts as $d){
						$district[$idx]['user_id'] = $id;
						$district[$idx]['district'] = $d;
						$idx++;
					}
					$this->db->insert_batch('admin_zones',$district);
				}
				
				$data['message'] = "The user account has now updated.";
				$data['page_title'] = 'Edit User';
				$this->ag_auth->view('message', $data);
				
			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$data['message'] = "The user account failed to update.";
				$data['page_title'] = 'Edit User';
				$this->ag_auth->view('message', $data);
			}

		} // if($this->form_validation->run() == FALSE)
		
	} // public function register()

	public function editprofile()
	{
		$id = $this->session->userdata('userid');

		//print_r($this->session->userdata);
		//print $id;		
		$this->form_validation->set_rules('username', 'Username', 'required|min_length[6]');
		$this->form_validation->set_rules('email', 'Email Address', 'required|min_length[6]|valid_email');
		$this->form_validation->set_rules('fullname', 'Full Name', 'required|trim|xss_clean');	
		$this->form_validation->set_rules('mobile', 'Mobile Number', 'required|trim|xss_clean');


		$user = $this->get_user($id);
		$data['user'] = $user;

		if($this->form_validation->run() == FALSE)
		{
			$data['groups'] = $this->get_group();
			$data['page_title'] = 'Edit Personal Profile';
			$this->ag_auth->view('users/editprofile',$data);
		}
		else
		{
			$dataset['username'] = set_value('username');
			$dataset['fullname'] = set_value('fullname');
			$dataset['mobile'] = set_value('mobile'); 
			$dataset['email'] = set_value('email');
			
			if($this->update_user($id,$dataset) === TRUE)
			{
				$this->oi->add_success('Personal profile updated successfully.');
				
			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$this->oi->add_error('Failed to update personal profile');
			}

			redirect('admin/dashboard');

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
			$data['groups'] = $this->get_group();
			$data['page_title'] = 'Change User Password';
			$this->ag_auth->view('users/editpass',$data);
		}
		else
		{
			$result = TRUE;
			
			$dataset['password'] = $this->ag_auth->salt(set_value('password'));

			if( $result = $this->update_user($id,$dataset))
			{
				$data['message'] = "The user password has now updated.";
				$data['page_title'] = 'Edit User Success';
				$data['back_url'] = anchor('admin/users/manage','Back to list');
				$this->ag_auth->view('message', $data);
				
			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$data['message'] = "The user account failed to update.";
				$data['page_title'] = 'Edit User Error';
				$data['back_url'] = anchor('admin/users/manage','Back to list');
				$this->ag_auth->view('message', $data);
			}

		} // if($this->form_validation->run() == FALSE)
		
	} // public function register()

	
	// WOKRING ON PROPER IMPLEMENTATION OF ADDING & EDITING USER ACCOUNTS
}

?>