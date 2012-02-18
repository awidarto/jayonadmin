<?php

class Members extends Application
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

		$this->breadcrumb->add_crumb('Members','admin/members/manage');
		
	    $this->load->library('table');		
			
		$data = $this->db->get($this->config->item('jayon_members_table'));
		$result = $data->result_array();
		$this->table->set_heading('Username', 'Email','Full Name','Merchant Name','Bank Account','Mobile','Phone','Group','Actions'); // Setting headings for the table
		
		foreach($result as $value => $key)
		{
			$delete = anchor("admin/members/delete/".$key['id']."/", "Delete"); // Build actions links
			$editpass = anchor("admin/members/editpass/".$key['id']."/", "Change Password"); // Build actions links
			if($key['group_id'] === group_id('merchant')){
				$addapp = anchor("admin/members/merchantmanage/".$key['id']."/", "Applications"); // Build actions links
			}else{
				$addapp = '&nbsp'; // Build actions links
			}
			$edit = anchor("admin/members/edit/".$key['id']."/", "Edit"); // Build actions links
			$detail = anchor("admin/members/details/".$key['id']."/", $key['username']); // Build detail links
			$this->table->add_row($detail, $key['email'],$key['fullname'],$key['merchantname'],$key['bank'].'<br/>'.$key['account_number'].'<br/>'.$key['account_name'],$key['mobile'],$key['phone'],$this->get_group_description($key['group_id']),$edit.' '.$editpass.' '.$addapp.' '.$delete); // Adding row to table
		}
		
		$page['sortdisable'] = '8';
		$page['ajaxurl'] = 'admin/members/ajaxmanage';
		$page['add_button'] = array('link'=>'admin/members/add','label'=>'Add New Member');
		$page['page_title'] = 'Manage Members';
		$this->ag_auth->view('ajaxlistview',$page); // Load the view
	}
	
	public function ajaxmanage(){

		$limit_count = $this->input->post('iDisplayLength');
		$limit_offset = $this->input->post('iDisplayStart');
		
		$sort_col = $this->input->post('iSortCol_0');
		$sort_dir = $this->input->post('sSortDir_0');

		$columns = array('username', 'email','fullname','merchantname','account_number','mobile','phone','group_id');

		// get total count result
		$count_all = $this->db->count_all($this->config->item('jayon_members_table'));

		$count_display_all = $this->db->count_all_results($this->config->item('jayon_members_table'));
		
		$data = $this->db->limit($limit_count, $limit_offset)->order_by($columns[$sort_col],$sort_dir)->get($this->config->item('jayon_members_table'));
		
		//print $this->db->last_query();
		
		$result = $data->result_array();
			
		$aadata = array();
		
		
		foreach($result as $value => $key)
		{
			$delete = anchor("admin/members/delete/".$key['id']."/", "Delete"); // Build actions links
			$editpass = anchor("admin/members/editpass/".$key['id']."/", "Change Password"); // Build actions links
			if($key['group_id'] === group_id('merchant')){
				$addapp = anchor("admin/members/merchantmanage/".$key['id']."/", "Applications"); // Build actions links
			}else{
				$addapp = '&nbsp'; // Build actions links
			}
			$edit = anchor("admin/members/edit/".$key['id']."/", "Edit"); // Build actions links
			$detail = anchor("admin/members/details/".$key['id']."/", $key['username']); // Build detail links
			$aadata[] = array($detail, $key['email'],$key['fullname'],$key['merchantname'],$key['bank'].'<br/>'.$key['account_number'].'<br/>'.$key['account_name'],$key['mobile'],$key['phone'],$this->get_group_description($key['group_id']),$edit.' '.$editpass.' '.$addapp.' '.$delete); // Adding row to table
			
		}
		
		$result = array(
			'sEcho'=> $this->input->post('sEcho'),
			'iTotalRecords'=>$count_all,
			'iTotalDisplayRecords'=> $count_display_all,
			'aaData'=>$aadata
		);
		
		print json_encode($result);
	}
	
	
	function details($id){
		$this->load->library('table');		
	
		$user = $this->get_user($id);
		
		foreach($user as $key=>$val){
			$this->table->add_row($key,$val); // Adding row to table
		}
		
		$page['page_title'] = 'Member Info';
		$this->ag_auth->view('members/details',$page);
	}
	
	public function delete($id)
	{
		$this->db->where('id', $id)->delete($this->config->item('jayon_members_table'));
		$page['page_title'] = 'Delete Member';
		$this->ag_auth->view('members/delete_success',$page);
	}

	public function get_user($id){
		$result = $this->db->where('id', $id)->get($this->config->item('jayon_members_table'));
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
		$result = $this->db->where('id', $id)->update($this->config->item('jayon_members_table'),$data);
		return $this->db->affected_rows();
	}
	
	
	public function add()
	{
		$this->form_validation->set_rules('username', 'Username', 'required|min_length[6]|callback_field_exists');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|matches[password_conf]');
		$this->form_validation->set_rules('password_conf', 'Password Confirmation', 'required|min_length[6]|matches[password]');
		$this->form_validation->set_rules('email', 'Email Address', 'required|min_length[6]|valid_email|callback_field_exists');
		$this->form_validation->set_rules('fullname', 'Full Name', 'required|trim|xss_clean');	
		$this->form_validation->set_rules('merchantname', 'Merchant Name', 'trim|xss_clean');	
		$this->form_validation->set_rules('bank', 'Bank', 'trim|xss_clean');	
		$this->form_validation->set_rules('account_name', 'Account Name', 'trim|xss_clean');	
		$this->form_validation->set_rules('account_number', 'Account Number', 'trim|xss_clean');	
		$this->form_validation->set_rules('street', 'Street', 'required|trim|xss_clean');	
		$this->form_validation->set_rules('district', 'District', 'required|trim|xss_clean');  
		$this->form_validation->set_rules('city', 'City', 'required|trim|xss_clean');	
		$this->form_validation->set_rules('country', 'Country', 'required|trim|xss_clean');	
		$this->form_validation->set_rules('zip', 'ZIP', 'required|trim|xss_clean');		
		$this->form_validation->set_rules('phone', 'Phone Number', 'required|trim|xss_clean');   
		$this->form_validation->set_rules('mobile', 'Mobile Number', 'required|trim|xss_clean');
		$this->form_validation->set_rules('group_id', 'Group', 'trim');
				
		if($this->form_validation->run() == FALSE)
		{
			$data['groups'] = array(
				group_id('merchant')=>group_desc('merchant'),
				group_id('buyer')=>group_desc('buyer')
			);
			$data['page_title'] = 'Add User';
			$this->ag_auth->view('members/add',$data);
		}
		else
		{
			$username = set_value('username');
			$password = $this->ag_auth->salt(set_value('password'));
			$fullname = set_value('fullname');
			$merchantname = set_value('merchantname');
			$bank = set_value('bank');
			$account_number = set_value('account_number');
			$account_name = set_value('account_name');
			$street = set_value('street'); 
			$district = set_value('district');
			$province = set_value('province');
			$city = set_value('city');
			$country = set_value('country');
			$zip = set_value('zip');
			$phone= set_value('phone');
			$mobile= set_value('mobile'); 
			$email = set_value('email');
			$group_id = set_value('group_id');
			
			$dataset = array(
				'username'=>$username,
				'password'=>$password,
				'fullname'=>$fullname,
				'merchantname'=>$merchantname,
				'bank'=>$bank,
				'account_number'=>$account_number,
				'account_name'=>$account_name,
				'street'=>$street,
				'district'=>$district,
				'province'=>$province,
				'city'=>$city,
				'country'=>$country, 
				'zip'=>$zip,
				'phone'=>$phone,
				'mobile'=>$mobile, 
				'email'=>$email,
				'group_id'=>$group_id
			);
			
			if($this->db->insert($this->config->item('jayon_members_table'),$dataset) === TRUE)
			{
				$data['message'] = "The user account has now been created.";
				$data['page_title'] = 'Add Member';
				$data['back_url'] = anchor('admin/members/manage','Back to list');
				$this->ag_auth->view('message', $data);
				
			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$data['message'] = "The user account has not been created.";
				$data['page_title'] = 'Add Member Error';
				$data['back_url'] = anchor('admin/members/manage','Back to list');
				$this->ag_auth->view('message', $data);
			}

		} // if($this->form_validation->run() == FALSE)
		
	} // public function register()

	public function edit($id)
	{
		$this->form_validation->set_rules('email', 'Email Address', 'required|min_length[6]|valid_email');
		$this->form_validation->set_rules('fullname', 'Full Name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('merchantname', 'Merchant Name', 'trim|xss_clean');	
		$this->form_validation->set_rules('bank', 'Bank', 'trim|xss_clean');	
		$this->form_validation->set_rules('account_name', 'Account Name', 'trim|xss_clean');	
		$this->form_validation->set_rules('account_number', 'Account Number', 'trim|xss_clean');	
		$this->form_validation->set_rules('street', 'Street', 'required|trim|xss_clean');	
		$this->form_validation->set_rules('district', 'District', 'required|trim|xss_clean');  
		$this->form_validation->set_rules('city', 'City', 'required|trim|xss_clean');	
		$this->form_validation->set_rules('country', 'Country', 'required|trim|xss_clean');	
		$this->form_validation->set_rules('zip', 'ZIP', 'required|trim|xss_clean');		
		$this->form_validation->set_rules('phone', 'Phone Number', 'required|trim|xss_clean');   
		$this->form_validation->set_rules('mobile', 'Mobile Number', 'required|trim|xss_clean');
		$this->form_validation->set_rules('group_id', 'Group', 'trim');
		
		$user = $this->get_user($id);
		$data['user'] = $user;
				
		if($this->form_validation->run() == FALSE)
		{
			$data['groups'] = array(
				group_id('merchant')=>group_desc('merchant'),
				group_id('buyer')=>group_desc('buyer')
			);
			$data['page_title'] = 'Edit Member';
			$this->ag_auth->view('members/edit',$data);
		}
		else
		{

			$dataset['fullname'] = set_value('fullname');
			$dataset['merchantname'] = set_value('merchantname');
			$dataset['bank'] = set_value('bank');
			$dataset['account_name'] = set_value('account_name');
			$dataset['account_number'] = set_value('account_number');
			$dataset['street'] = set_value('street'); 
			$dataset['district'] = set_value('district');
			$dataset['province'] = set_value('province');
			$dataset['city'] = set_value('city');
			$dataset['country'] = set_value('country');
			$dataset['zip'] = set_value('zip');
			$dataset['phone'] = set_value('phone');
			$dataset['mobile'] = set_value('mobile'); 
			$dataset['email'] = set_value('email');
			$dataset['group_id'] = set_value('group_id');
			
			if($this->db->where('id',$id)->update($this->config->item('jayon_members_table'),$dataset) === TRUE)
			//if($this->update_user($id,$dataset) === TRUE)
			{
				$data['message'] = "The member account has now updated.";
				$data['page_title'] = 'Edit Member';
				$data['back_url'] = anchor('admin/members/manage','Back to list');
				$this->ag_auth->view('message', $data);
				
			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$data['message'] = "The member account has not been created.";
				$data['page_title'] = 'Edit Member';
				$data['back_url'] = anchor('admin/members/manage','Back to list');
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
			$data['groups'] = $this->get_group();
			$data['page_title'] = 'Change Member Password';
			$this->ag_auth->view('members/editpass',$data);
		}
		else
		{
			$result = TRUE;
			$dataset['password'] = $this->ag_auth->salt(set_value('password'));

			//if( $result = $this->update_user($id,$dataset))
			if($this->db->where('id',$id)->update($this->config->item('jayon_members_table'),$dataset) === TRUE)
			{
				$data['message'] = "The user password has now updated.";
				$data['page_title'] = 'Edit Member Password Success';
				$data['back_url'] = anchor('admin/members/manage','Back to list');
				$this->ag_auth->view('message', $data);
				
			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$data['message'] = "The user account failed to update.";
				$data['page_title'] = 'Edit Member Password Error';
				$data['back_url'] = anchor('admin/members/manage','Back to list');
				$this->ag_auth->view('message', $data);
			}

		} // if($this->form_validation->run() == FALSE)
		
	} // public function register()
	// WOKRING ON PROPER IMPLEMENTATION OF ADDING & EDITING USER ACCOUNTS
}

?>