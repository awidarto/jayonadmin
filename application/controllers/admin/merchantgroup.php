<?php

class Merchantgroup extends Application
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
		$this->breadcrumb->add_crumb('Merchantgroup','admin/merchantgroup/manage');
		
		
	}

	public function manage()
	{
	    $this->load->library('table');		
			
		$this->breadcrumb->add_crumb('Administrators','admin/merchantgroup/manage');
			
		$data = $this->db->get($this->config->item('jayon_merchantgroup_table'));
		$result = $data->result_array();
		$this->table->set_heading('Username','Full Name','Actions'); // Setting headings for the table
		
		foreach($result as $value => $key)
		{
			$delete = anchor("admin/merchantgroup/delete/".$key['id']."/", "Delete"); // Build actions links
			// $editpass = anchor("admin/merchantgroup/editpass/".$key['id']."/", "Change Password"); // Build actions links
			$edit = anchor("admin/merchantgroup/edit/".$key['id']."/", "Edit"); // Build actions links
			$detail = anchor("admin/merchantgroup/details/".$key['id']."/", $key['groupname']); // Build detail links
			$this->table->add_row($detail,$key['description'],$edit.$delete); // Adding row to table
		}
		$page['add_button'] = array('link'=>'admin/merchantgroup/add','label'=>'Add New merchant group');
		$page['page_title'] = 'Manage merchant group';
		$this->ag_auth->view('listview',$page); // Load the view
	}
	
	public function __manage()
	{

		$this->breadcrumb->add_crumb('Manage Merchant groups','admin/merchantgroups/manage');

		$this->load->library('table');

		$this->table->set_heading(
			'Group Name',
			'Description',
			// 'Merchant Name',
			// 'Group',
			// 'Created',
			'Actions'); // Setting headings for the table

		$this->table->set_footing(
			'<input type="text" name="search_groupname" id="search_username" value="Search Group Name" class="search_init" />',
			'<input type="text" name="search_email" id="search_email" value="Search description" class="search_init" />',
			
            // '<input type="text" name="search_merchant_name" value="Search merchant name" class="search_init" />',
            
			// '<input type="text" name="search_created" id="search_timestamp" value="Search created" class="search_init" />',
			form_button('do_setgroup','Set Group','id="doSetGroup"')
			);

		$page['sortdisable'] = '';
		$page['ajaxurl'] = 'admin/merchantgroup/ajaxmerchantgroup';
		$page['add_button'] = array('link'=>'admin/merchantgroup/add','label'=>'Add New Group');
        $page['group_button'] = false;
		$page['page_title'] = 'Manage Merchants Group';
		$this->ag_auth->view('listview',$page); // Load the view
	}

	function details($id){
		$this->load->library('table');		
	
		$user = $this->get_user($id);
		
		foreach($user as $key=>$val){
			$this->table->add_row($key,$val); // Adding row to table
		}
		
		$page['page_title'] = 'Admin Info';
		$this->ag_auth->view('merchantgroup/details',$page);
	}
		
	public function delete($id)
	{
		$this->db->where('id', $id)->delete($this->config->item('jayon_merchantgroup_table'));
		$page['page_title'] = 'Delete User';
		$this->ag_auth->view('merchantgroup/delete_success',$page);
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
		$this->form_validation->set_rules('groupname', 'Groupname', 'required|min_length[6]|callback_field_exists');
		$this->form_validation->set_rules('description', 'Description', 'required|trim|xss_clean');
		
				
		if($this->form_validation->run() == FALSE)
		{
			$data['groups'] = $this->get_group();
			$data['page_title'] = 'Add Merchant Group';
			$this->ag_auth->view('merchantgroup/add',$data);
		}
		else
		{
			$dataset['groupname'] = set_value('groupname');
			$dataset['description'] = set_value('description');

			if($this->db->insert($this->config->item('jayon_merchantgroup_table'),$dataset) === TRUE)
			{
				$data['message'] = "The merchant group has now been created.";
				$data['page_title'] = 'Add Merchant Group';
				$data['back_url'] = anchor('admin/merchantgroup/manage','Back to list');
				$this->ag_auth->view('message', $data);

			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$data['message'] = "The merchant group has not been created.";
				$data['page_title'] = 'Add Merchant Group Error';
				$data['back_url'] = anchor('admin/merchantgroup/manage','Back to list');
				$this->ag_auth->view('message', $data);
			}

		} // if($this->form_validation->run() == FALSE)
		
	} // public function register()

	public function edit($id)
	{
		$this->form_validation->set_rules('groupname', 'Groupname', 'required|min_length[6]|callback_field_exists');
		$this->form_validation->set_rules('description', 'Description', 'required|trim|xss_clean');
		
		$success_url = 'admin/merchantgroup/manage';
		$error_url = 'admin/merchantgroup/edit/'.$id;

		$user = $this->get_user($id);
		$data['user'] = $user;
		$back_url = 'admin/merchantgroup/manager';
				
		if($this->form_validation->run() == FALSE)
		{
			$data['groups'] = array(
                group_id('pendingmerchant')=>group_desc('pendingmerchant'),
				group_id('merchant')=>group_desc('merchant'),
				group_id('buyer')=>group_desc('buyer')
			);
			$data['back_url'] = anchor($back_url,'Cancel');
			$this->ag_auth->view('merchantgroup/edit',$data);
		}
		else
		{

			$dataset['groupname'] = set_value('groupname');
			$dataset['description'] = set_value('description');

			if($this->db->where('id',$id)->update($this->config->item('jayon_merchantgroup_table'),$dataset) === TRUE)
			//if($this->update_user($id,$dataset) === TRUE)
			{
				$this->oi->add_success($utype.' updated & saved');
				redirect($success_url);

			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$this->oi->add_success('Failed to update '.$utype);
				redirect($error_url);
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
			$this->ag_auth->view('merchantgroup/editprofile',$data);
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
			$this->ag_auth->view('merchantgroup/editpass',$data);
		}
		else
		{
			$result = TRUE;
			
			$dataset['password'] = $this->ag_auth->salt(set_value('password'));

			if( $result = $this->update_user($id,$dataset))
			{
				$data['message'] = "The user password has now updated.";
				$data['page_title'] = 'Edit User Success';
				$data['back_url'] = anchor('admin/merchantgroup/manage','Back to list');
				$this->ag_auth->view('message', $data);
				
			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$data['message'] = "The user account failed to update.";
				$data['page_title'] = 'Edit User Error';
				$data['back_url'] = anchor('admin/merchantgroup/manage','Back to list');
				$this->ag_auth->view('message', $data);
			}

		} // if($this->form_validation->run() == FALSE)
		
	} // public function register()

	
	// WOKRING ON PROPER IMPLEMENTATION OF ADDING & EDITING USER ACCOUNTS
}

?>