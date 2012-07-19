<?php

class Tariff extends Application
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

	public function cod($id,$merchant_id)
	{
		$this->breadcrumb->add_crumb('Users','admin/users/manage');
		$this->breadcrumb->add_crumb('Manage Merchant','admin/members/merchant');
		$this->breadcrumb->add_crumb('Applications','admin/members/merchant/apps/manage/'.$merchant_id);
		$this->breadcrumb->add_crumb('COD Surcharge','admin/tariff/cod');

		$this->table->set_heading(
			'Merchant',
			'Application Name',
			'Domain',
			'Key',
			'Callback URL',
			'Description',
			'Actions'
			); // Setting headings for the table

		$page['merchant_id'] = $id;
		$page['sortdisable'] = '6';
		$page['add_button'] = array('link'=>'admin/members/merchant/apps/add/'.$id,'label'=>'Add COD Surcharge Range');
		$page['ajaxurl'] = 'admin/tariff/ajaxcod/'.$id;
		$page['page_title'] = 'COD Surcharges';
		$this->ag_auth->view('ajaxlistview',$page); // Load the view
	}

	public function delivery($id,$merchant_id)
	{
		$this->breadcrumb->add_crumb('Users','admin/users/manage');
		$this->breadcrumb->add_crumb('Manage Merchant','admin/members/merchant');
		$this->breadcrumb->add_crumb('Applications','admin/members/merchant/apps/manage/'.$merchant_id);
		$this->breadcrumb->add_crumb('Delivery Charge','admin/tariff/delivery');

		$this->table->set_heading(
			'Merchant',
			'Application Name',
			'Domain',
			'Key',
			'Callback URL',
			'Description',
			'Actions'
			); // Setting headings for the table

		$page['merchant_id'] = $id;
		$page['sortdisable'] = '6';
		$page['add_button'] = array('link'=>'admin/members/merchant/apps/add/'.$id,'label'=>'Add Delivery Charge Range');
		$page['ajaxurl'] = 'admin/apps/ajaxmerchantmanage/'.$id;
		$page['page_title'] = 'Delivery Charges';
		$this->ag_auth->view('ajaxlistview',$page); // Load the view
	}

	public function addcod()
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

	public function editcod($id)
	{
		$this->form_validation->set_rules('seq', 'Sequence', 'required|trim|xss_clean');
		$this->form_validation->set_rules('from_price', 'Price From', 'required|trim|xss_clean');
		$this->form_validation->set_rules('to_price', 'Price Up To', 'required|trim|xss_clean');
		$this->form_validation->set_rules('surcharge', 'Surcharge', 'required|trim|xss_clean');
		$this->form_validation->set_rules('app_id', 'Application Id', 'required|trim|xss_clean');
		$this->form_validation->set_rules('period_from'	, 'Valid From', 'required|trim|xss_clean');
		$this->form_validation->set_rules('period_to', 'Valid Until', 'required|trim|xss_clean');
		$this->form_validation->set_rules('is_on', 'Active', 'required|trim|xss_clean');
		
		$user = $this->get_cod_entry($id);
		$data['user'] = $user;
				
		if($this->form_validation->run() == FALSE)
		{
			$data['page_title'] = 'Edit COD Surcharge Entry';
			$this->ag_auth->view('tariff/codedit',$data);
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

	public function get_cod_entry($id){
		$result = $this->db->where('id', $id)->get($this->config->item('jayon_cod_fee_table'));
		if($result->num_rows() > 0){
			return $result->row_array();
		}else{
			return false;
		}
	}

	public function ajaxcod($app_id){

		$limit_count = $this->input->post('iDisplayLength');
		$limit_offset = $this->input->post('iDisplayStart');
		
		$sort_col = $this->input->post('iSortCol_0');
		$sort_dir = $this->input->post('sSortDir_0');

		$columns = array(
			'seq','from_price','to_price','surcharge','app_id','period_from','period_to','is_on'
			);

		$this->db->where('app_id', $id)->get($this->config->item('jayon_cod_fee_table'));

		// get total count result
		$count_all = $this->db->count_all($this->config->item('jayon_cod_fee_table'));

		$count_display_all = $this->db->count_all_results($this->config->item('jayon_cod_fee_table'));
		
		$data = $this->db->limit($limit_count, $limit_offset)->order_by($columns[$sort_col],$sort_dir)->get($this->config->item('jayon_couriers_table'));
		
		//print $this->db->last_query();
		
		$result = $data->result_array();
			
		$aadata = array();
		
		
		foreach($result as $value => $key)
		{
			$delete = anchor("admin/tariff/delete/".$key['id']."/", "Delete"); // Build actions links
			$edit = anchor("admin/tariff/editcod/".$key['id']."/", "Edit"); // Build actions links
			$detail = anchor("admin/tariff/details/".$key['id']."/", $key['username']); // Build detail links
			$aadata[] = array($detail, $key['seq'],$key['from_price'],$key['to_price'],$key['surcharge'],$key['period_from'],$key['period_to'],$edit.' '.$editpass.' '.$delete); // Adding row to table
		}
		
		$result = array(
			'sEcho'=> $this->input->post('sEcho'),
			'iTotalRecords'=>$count_all,
			'iTotalDisplayRecords'=> $count_display_all,
			'aaData'=>$aadata
		);
		
		print json_encode($result);
	}



}

?>