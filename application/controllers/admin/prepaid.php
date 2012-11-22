<?php

class Prepaid extends Application
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

	public function credit($id,$merchant_id)
	{
		$this->breadcrumb->add_crumb('Users','admin/users/manage');
		$this->breadcrumb->add_crumb('Manage Merchant','admin/members/merchant');

		$app = $this->get_merchant_app($id);

		$merchant_name = $app['merchant_name'];
		$app_name = $app['app_name'];

		$this->breadcrumb->add_crumb($app_name,'admin/members/merchant/apps/manage/'.$merchant_id);
		$this->breadcrumb->add_crumb('Prepaid Credit','admin/prepaid/credit');

		//'seq','from_price','to_price','surcharge','app_id','period_from','period_to','is_on'
		
		$this->table->set_heading(
			'Created',
			'Period',
			'Credit',
			'Usage',
			'Action'
			); // Setting headings for the table

		$page['merchant_id'] = $merchant_id;
		$page['sortdisable'] = '';
		$page['add_button'] = array('link'=>'admin/prepaid/addcredit/'.$id,'label'=>'Add Prepaid Credit ');
		$page['ajaxurl'] = 'admin/prepaid/ajaxcredit/'.$id;
		$page['page_title'] = 'Prepaid Credit';
		$this->ag_auth->view('prepaid/creditajaxlistview',$page); // Load the view
	}

	public function ajaxcredit($app_id){

		$limit_count = $this->input->post('iDisplayLength');
		$limit_offset = $this->input->post('iDisplayStart');
		
		$sort_col = $this->input->post('iSortCol_0');
		$sort_dir = $this->input->post('sSortDir_0');

		$columns = array(
			'created',	
			'app_key',				 	 	 	 	 	 	 
			'period',				 	 	 	 	 	 	 
			'credit',	
			'usage'
		);

		$this->db->where('app_id', $app_id);

		// get total count result
		$count_all = $this->db->count_all($this->config->item('jayon_prepaid_table'));

		$count_display_all = $this->db->count_all_results($this->config->item('jayon_prepaid_table'));
		
		$this->db->where('app_id', $app_id);

		$this->db->order_by('created','desc');

		$data = $this->db->limit($limit_count, $limit_offset)->order_by($columns[$sort_col],$sort_dir)->get($this->config->item('jayon_prepaid_table'));
		
		//print $this->db->last_query();
		
		$result = $data->result_array();
			
		$aadata = array();
		
		
		foreach($result as $value => $key)
		{
			$delete = '<span id="'.$key['id'].'" class="delete_link" style="cursor:pointer;text-decoration:underline;">Delete</span>'; // Build actions links
			$edit = anchor("admin/tariff/editcod/".$key['id']."/".$key['app_id'], "Edit"); // Build actions links
			$detail = anchor("admin/tariff/details/".$key['id']."/", $key['id']); // Build detail links
			$aadata[] = array(
				$key['created'],
				$key['period'],
				$key['credit'],
				$key['usage'],
				$edit.' '.$delete); // Adding row to table
		}

		$result = array(
			'sEcho'=> $this->input->post('sEcho'),
			'iTotalRecords'=>$count_all,
			'iTotalDisplayRecords'=> $count_display_all,
			'aaData'=>$aadata
		);
		
		print json_encode($result);
	}


	public function addcredit($app_id)
	{

		$this->breadcrumb->add_crumb('Users','admin/users/manage');
		$this->breadcrumb->add_crumb('Manage Merchant','admin/members/merchant');

		$app = $this->get_merchant_app($app_id);

		$merchant_name = $app['merchant_name'];
		$merchant_id = $app['merchant_id'];
		$app_name = $app['app_name'];

		$this->breadcrumb->add_crumb($app_name,'admin/members/merchant/apps/manage/'.$merchant_id);
		$this->breadcrumb->add_crumb('Prepaid Credit','admin/prepaid/credit/'.$app_id.'/'.$merchant_id);
		$this->breadcrumb->add_crumb('Add','admin/prepaid/addcredit/'.$app_id);


		$this->form_validation->set_rules('period', 'Period', 'required|trim|xss_clean');
		$this->form_validation->set_rules('credit', 'Credit', 'required|trim|xss_clean');
				
		$data['app_id'] = $app_id;

		if($this->form_validation->run() == FALSE)
		{
			$data['page_title'] = 'Add Prepaid Credit Entry';
			$this->ag_auth->view('prepaid/creditadd',$data);
		}
		else
		{
			$dataset['app_id'] = $app_id;
			$dataset['credit'] = set_value('credit'); 
			$dataset['period'] = set_value('period');
			
			
			if($this->db->insert($this->config->item('jayon_prepaid_table'),$dataset) === TRUE)
			//if($this->update_user($id,$dataset) === TRUE)
			{
				$success_url = 'admin/prepaid/credit/'.$app_id.'/'.$merchant_id;
				$this->oi->add_success('Prepaid Credit added');
				redirect($success_url);

			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$error_url = 'admin/prepaid/addcredit/'.$app_id;				
				$this->oi->add_error('Failed to add Prepaid Credit');
				redirect($error_url);
			}

		} // if($this->form_validation->run() == FALSE)
		
	} // public function register()


	public function editcredit($id,$app_id)
	{

		$this->breadcrumb->add_crumb('Users','admin/users/manage');
		$this->breadcrumb->add_crumb('Manage Merchant','admin/members/merchant');

		$app = $this->get_merchant_app($app_id);

		$merchant_name = $app['merchant_name'];
		$merchant_id = $app['merchant_id'];
		$app_name = $app['app_name'];

		$this->breadcrumb->add_crumb($app_name,'admin/members/merchant/apps/manage/'.$merchant_id);
		$this->breadcrumb->add_crumb('COD Surcharge','admin/tariff/cod/'.$app_id.'/'.$merchant_id);
		$this->breadcrumb->add_crumb('Edit','admin/tariff/editcod/'.$id);


		$this->form_validation->set_rules('seq', 'Sequence', 'required|trim|xss_clean');
		$this->form_validation->set_rules('from_price', 'Price From', 'required|trim|xss_clean');
		$this->form_validation->set_rules('to_price', 'Price Up To', 'required|trim|xss_clean');
		$this->form_validation->set_rules('surcharge', 'Surcharge', 'required|trim|xss_clean');
		$this->form_validation->set_rules('period_from'	, 'Valid From', 'required|trim|xss_clean');
		$this->form_validation->set_rules('period_to', 'Valid Until', 'required|trim|xss_clean');
		
		$user = $this->get_cod_entry($id);
		$data['user'] = $user;
		
		$data['app_id'] = $app_id;

		if($this->form_validation->run() == FALSE)
		{
			$data['page_title'] = 'Edit COD Surcharge Entry';
			$this->ag_auth->view('tariff/codedit',$data);
		}
		else
		{
			$dataset['seq'] = set_value('seq');
			$dataset['from_price'] = set_value('from_price');

			$dataset['to_price'] = set_value('to_price');
			$dataset['surcharge'] = set_value('surcharge'); 
			$dataset['period_from'] = set_value('period_from');
			$dataset['period_to'] = set_value('period_to'); 
			
			
			if($this->db->where('id',$id)->update($this->config->item('jayon_cod_fee_table'),$dataset) === TRUE)
			//if($this->update_user($id,$dataset) === TRUE)
			{
				$success_url = 'admin/tariff/cod/'.$app_id.'/'.$merchant_id;
				$this->oi->add_success('COD fee updated');
				redirect($success_url);

			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$error_url = 'admin/tariff/editcod/'.$id.'/'.$app_id;				
				$this->oi->add_error('Failed to update COD fee');
				redirect($error_url);
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

	public function get_delivery_entry($id){
		$result = $this->db->where('id', $id)->get($this->config->item('jayon_delivery_fee_table'));
		if($result->num_rows() > 0){
			return $result->row_array();
		}else{
			return false;
		}
	}


	public function ajaxdeletecredit()
	{
		$id = $this->input->post('id');
		
		if($this->db->where('id', $id)->delete($this->config->item('jayon_cod_fee_table'))){
			print json_encode(array('result'=>'ok'));
		}else{
			print json_encode(array('result'=>'failed'));
		}
	}
	
	function get_merchant_app($app_id){
		
		$this->db->select('applications.application_name as app_name, m.fullname as merchant_name, m.id as merchant_id');
		$this->db->join('members as m','applications.merchant_id=m.id','left');
		$this->db->where('applications.id',$app_id);

		$res = $this->db->get('applications');

		return $res->row_array();
	}


}

?>