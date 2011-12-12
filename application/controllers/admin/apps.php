<?php

class Apps extends Application
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
			
		$data = $this->db->get($this->config->item('applications_table'));
		$result = $data->result_array();
		$this->table->set_heading(
			'Merchant',		 	 	
			'Application Name',		 	 	 	 	 	 	 
			'Domain',				 	 	 	 	 	 	 
			'Key',					 	 	 	 	 	 	 
			'Callback URL',		 	 	 	 	 	 	 
			'Description',		 	 	 	 	 	 	 
			'Actions'
			); // Setting headings for the table
		
		foreach($result as $value => $key)
		{
			$delete = anchor("admin/apps/delete/".$key['id']."/", "Delete"); // Build actions links
			$edit = anchor("admin/apps/edit/".$key['id']."/", "Edit"); // Build actions links
			$add = anchor("admin/apps/add/".$key['merchant_id']."/", "Add"); // Build actions links
			$this->table->add_row(
				$this->get_merchant($key['merchant_id']),		 	 	
				$key['application_name'],		 	 	 	 	 	 	 
				$key['domain'],				 	 	 	 	 	 	 
				$key['key'],					 	 	 	 	 	 	 
				$key['callback_url'],		 	 	 	 	 	 	 
				$key['application_description'],		 	 	 	 	 	 	 
				$add.' '.$edit.' '.$delete
			); // Adding row to table
		}
		$page['page_title'] = 'Application Keys';
		$this->ag_auth->view('apps/manage',$page); // Load the view
	}

	public function merchantmanage($id)
	{
			
		$data = $this->db->where('merchant_id',$id)->get($this->config->item('applications_table'));
		$result = $data->result_array();
		$this->table->set_heading(
			'Merchant',		 	 	
			'Application Name',		 	 	 	 	 	 	 
			'Domain',				 	 	 	 	 	 	 
			'Key',					 	 	 	 	 	 	 
			'Callback URL',		 	 	 	 	 	 	 
			'Description',		 	 	 	 	 	 	 
			'Actions'
			); // Setting headings for the table
		
		foreach($result as $value => $key)
		{
			$delete = anchor("admin/members/merchantdelete/".$key['id']."/", "Delete"); // Build actions links
			$edit = anchor("admin/members/merchantedit/".$key['id']."/", "Edit"); // Build actions links
			$this->table->add_row(
				$this->get_merchant($key['merchant_id']),		 	 	
				$key['application_name'],		 	 	 	 	 	 	 
				$key['domain'],				 	 	 	 	 	 	 
				$key['key'],					 	 	 	 	 	 	 
				$key['callback_url'],		 	 	 	 	 	 	 
				$key['application_description'],		 	 	 	 	 	 	 
				$edit.' '.$delete
			); // Adding row to table
		}
		$page['merchant_id'] = $id;
		$page['page_title'] = 'Application Keys - '.$id.' - '.$this->get_merchant($id);
		$this->ag_auth->view('apps/merchantmanage',$page); // Load the view
	}
	
	public function delete($id)
	{
		$this->db->where('id', $id)->delete($this->config->item('applications_table'));
		$page['page_title'] = 'Delete Application';
		$this->ag_auth->view('apps/delete_success');
	}
	
	public function get_merchant($id){
		$result = $this->db->select('merchantname')->where('id',$id)->get($this->config->item('jayon_members_table'));
		$row = $result->row();
		return ($row->merchantname === '')?'anonymous merchant':$row->merchantname;
	}

	public function get_app($id){
		$result = $this->db->where('id', $id)->get($this->config->item('applications_table'));
		if($result->num_rows() > 0){
			return $result->row_array();
		}else{
			return false;
		}
	}
	
	public function add($merchant_id)
	{
		$this->form_validation->set_rules('owner_id','Owner ID','trim');	 	 	
		$this->form_validation->set_rules('merchant_id','Merchant ID','trim');
		$this->form_validation->set_rules('domain','Application Domain','required|trim|xss_clean');				 	 	 	 	 	 	 
		$this->form_validation->set_rules('application_name','Application Name','requiredtrim|xss_clean');		 	 	 	 	 	 	 
		$this->form_validation->set_rules('callback_url','Callback URL','required|trim|xss_clean');		 	 	 	 	 	 	 
		$this->form_validation->set_rules('application_description','Application Description','required|trim|xss_clean');		 	 	 	 	 	 	 
		$this->form_validation->set_rules('logo_url','Logo URL','required|trim|xss_clean');						 	 	 	 	 	 	 
		$this->form_validation->set_rules('signature','Signature','required|trim|xss_clean');
						
		if($this->form_validation->run() == FALSE)
		{
			$data['merchant_id'] = $merchant_id;
			$data['merchant_name'] = $this->get_merchant($merchant_id);
			//$data['page_title'] = 'Application Keys';
			$data['page_title'] = 'Add Application Keys <br />'.$merchant_id.' - '.$this->get_merchant($merchant_id);
			if(in_array('members',$this->uri->segment_array())){
				$data['act_url'] = 'admin/members/merchantadd/'.$merchant_id;
				$data['back_url'] = anchor('admin/members/merchantmanage/'.$merchant_id,'Back to list');
			}else{
				$data['act_url'] = 'admin/apps/add/'.$merchant_id;
				$data['back_url'] = anchor('admin/apps/manage','Back to list');
			}
			$this->ag_auth->view('apps/add',$data);
		}
		else
		{
			//$dataset['owner_id'] = set_value('owner_id');	 	 	
			$dataset['merchant_id'] = set_value('merchant_id');
			$dataset['domain'] = set_value('domain');				 	 	 	 	 	 	 
			$dataset['application_name'] = set_value('application_name');		 	 	 	 	 	 	 
			$dataset['key'] = random_string('sha1',40);					 	 	 	 	 	 	 
			$dataset['callback_url'] = set_value('callback_url');		 	 	 	 	 	 	 
			$dataset['application_description'] = set_value('application_description');		 	 	 	 	 	 	 
			$dataset['logo_url'] = set_value('logo_url');						 	 	 	 	 	 	 
			$dataset['signature'] = set_value('signature');
			
			if($this->db->insert($this->config->item('applications_table'),$dataset) === TRUE)
			{
				$data['message'] = "The application has now been created.";
				$data['page_title'] = 'Add Application';
				if(in_array('members',$this->uri->segment_array())){
					$data['act_url'] = 'admin/members/merchantadd/'.$merchant_id;
					$data['back_url'] = anchor('admin/members/merchantmanage/'.$merchant_id,'Back to list');
				}else{
					$data['act_url'] = 'admin/apps/add/'.$merchant_id;
					$data['back_url'] = anchor('admin/apps/manage','Back to list');
				}
				$this->ag_auth->view('message', $data);
				
			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$data['message'] = "The application has not been created.";
				$data['page_title'] = 'Add Application Error';
				
				if(in_array('members',$this->uri->segment_array())){
					$data['act_url'] = 'admin/members/merchantadd/'.$merchant_id;
					$data['back_url'] = anchor('admin/members/merchantmanage/'.$merchant_id,'Back to list');
				}else{
					$data['act_url'] = 'admin/apps/add/'.$merchant_id;
					$data['back_url'] = anchor('admin/apps/manage','Back to list');
				}
				$this->ag_auth->view('message', $data);
			}

		} // if($this->form_validation->run() == FALSE)
		
	} // public function register()

	public function edit($id)
	{
		$this->form_validation->set_rules('domain','Application Domain','required|trim|xss_clean');				 	 	 	 	 	 	 
		$this->form_validation->set_rules('application_name','Application Name','requiredtrim|xss_clean');		 	 	 	 	 	 	 
		$this->form_validation->set_rules('callback_url','Callback URL','required|trim|xss_clean');		 	 	 	 	 	 	 
		$this->form_validation->set_rules('application_description','Application Description','required|trim|xss_clean');		 	 	 	 	 	 	 
		$this->form_validation->set_rules('logo_url','Logo URL','required|trim|xss_clean');						 	 	 	 	 	 	 
		$this->form_validation->set_rules('signature','Signature','required|trim|xss_clean');
		
		$user = $this->get_app($id);
		$data['user'] = $user;
		$merchant_id = $user['merchant_id'];
				
		if($this->form_validation->run() == FALSE)
		{
			$data['merchant_id'] = $merchant_id;
			$data['merchant_name'] = $this->get_merchant($merchant_id);
			//$data['page_title'] = 'Application Keys';
			$data['page_title'] = 'Edit Application Keys <br />'.$user['application_name'].' - '.$this->get_merchant($merchant_id);
			if(in_array('members',$this->uri->segment_array())){
				$data['act_url'] = 'admin/members/merchantedit/'.$id;
				$data['back_url'] = anchor('admin/members/merchantmanage/'.$merchant_id,'Back to list');
			}else{
				$data['act_url'] = 'admin/apps/edit/'.$id;
				$data['back_url'] = anchor('admin/apps/manage','Back to list');
			}
			$this->ag_auth->view('apps/edit',$data);
		}
		else
		{
			//$dataset['owner_id'] = set_value('owner_id');	 	 	
			//$dataset['merchant_id'] = set_value('merchant_id');
			$dataset['domain'] = set_value('domain');				 	 	 	 	 	 	 
			$dataset['application_name'] = set_value('application_name');
			$dataset['callback_url'] = set_value('callback_url');		 	 	 	 	 	 	 
			$dataset['application_description'] = set_value('application_description');		 	 	 	 	 	 	 
			$dataset['logo_url'] = set_value('logo_url');						 	 	 	 	 	 	 
			$dataset['signature'] = set_value('signature');

			
			if($this->db->where('id',$id)->update($this->config->item('applications_table'),$dataset) === TRUE)
			{
				$data['message'] = "The application has now been updated.";
				$data['page_title'] = 'Edit Application';
				if(in_array('members',$this->uri->segment_array())){
					$data['act_url'] = 'admin/members/merchantedit/'.$id;
					$data['back_url'] = anchor('admin/members/merchantmanage/'.$merchant_id,'Back to list');
				}else{
					$data['act_url'] = 'admin/apps/edit/'.$id;
					$data['back_url'] = anchor('admin/apps/manage','Back to list');
				}
				$this->ag_auth->view('message', $data);
				
			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$data['message'] = "The application has not been updated.";
				$data['page_title'] = 'Edit Application Error';
				if(in_array('members',$this->uri->segment_array())){
					$data['act_url'] = 'admin/members/merchantedit/'.$id;
					$data['back_url'] = anchor('admin/members/merchantmanage/'.$merchant_id,'Back to list');
				}else{
					$data['act_url'] = 'admin/apps/edit/'.$id;
					$data['back_url'] = anchor('admin/apps/manage','Back to list');
				}
				$this->ag_auth->view('message', $data);
			}

		} // if($this->form_validation->run() == FALSE)
		
	} // public function register()


	
	// WOKRING ON PROPER IMPLEMENTATION OF ADDING & EDITING USER ACCOUNTS
}

?>