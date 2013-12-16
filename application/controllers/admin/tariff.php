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

		$app = $this->get_merchant_app($id);

		$merchant_name = $app['merchant_name'];
		$app_name = $app['app_name'];

		$this->breadcrumb->add_crumb($app_name,'admin/members/merchant/apps/manage/'.$merchant_id);
		$this->breadcrumb->add_crumb('COD Surcharge','admin/tariff/cod');

		//'seq','from_price','to_price','surcharge','app_id','period_from','period_to','is_on'

		$this->table->set_heading(
			'Sequence',
			'From Price',
			'Up To ',
			'Surcharge',
			'Period From',
			'Period End',
			'Actions'
			); // Setting headings for the table

		$page['merchant_id'] = $merchant_id;
		$page['sortdisable'] = '';
		$page['add_button'] = array('link'=>'admin/tariff/addcod/'.$id,'label'=>'Add COD Surcharge Range');
		$page['ajaxurl'] = 'admin/tariff/ajaxcod/'.$id;
		$page['page_title'] = 'COD Surcharges';
		$this->ag_auth->view('tariff/codajaxlistview',$page); // Load the view
	}

	public function delivery($id,$merchant_id)
	{
		$this->breadcrumb->add_crumb('Users','admin/users/manage');
		$this->breadcrumb->add_crumb('Manage Merchant','admin/members/merchant');

		$app = $this->get_merchant_app($id);

		$merchant_name = $app['merchant_name'];
		$app_name = $app['app_name'];

		$this->breadcrumb->add_crumb($app_name,'admin/members/merchant/apps/manage/'.$merchant_id);
		$this->breadcrumb->add_crumb('Delivery Charge','admin/tariff/delivery');
		//'seq','kg_from','kg_to','calculated_kg','tariff_kg','total','period_from','period_to'
		$this->table->set_heading(
			'Sequence',
			'From Kg',
			'Up To Kg',
			'Calculated Kg',
			'Tariff / Kg',
			'Total',
			'Period From',
			'Period End',
			'Actions'
			); // Setting headings for the table


		$page['merchant_name'] = $app['merchant_name'];
		$page['app_name'] = $app['app_name'];
		$page['merchant_id'] = $merchant_id;
		$page['sortdisable'] = '';
		$page['add_button'] = array('link'=>'admin/tariff/adddelivery/'.$id,'label'=>'Add Delivery Charge Range');
		$page['ajaxurl'] = 'admin/tariff/ajaxdelivery/'.$id;
		$page['page_title'] = 'Delivery Charges';
		$this->ag_auth->view('tariff/deliveryajaxlistview',$page); // Load the view
	}

	public function pickup($id,$merchant_id)
	{
		$this->breadcrumb->add_crumb('Users','admin/users/manage');
		$this->breadcrumb->add_crumb('Manage Merchant','admin/members/merchant');

		$app = $this->get_merchant_app($id);

		$merchant_name = $app['merchant_name'];
		$app_name = $app['app_name'];

		$this->breadcrumb->add_crumb($app_name,'admin/members/merchant/apps/manage/'.$merchant_id);
		$this->breadcrumb->add_crumb('Pickup Charge','admin/tariff/pickup');
		//'seq','kg_from','kg_to','calculated_kg','tariff_kg','total','period_from','period_to'
		$this->table->set_heading(
			'Sequence',
			'From Kg',
			'Up To Kg',
			'Calculated Kg',
			'Tariff / Kg',
			'Total',
			'Period From',
			'Period End',
            'Note',
			'Actions'
			); // Setting headings for the table


		$page['merchant_name'] = $app['merchant_name'];
		$page['app_name'] = $app['app_name'];
		$page['merchant_id'] = $merchant_id;
		$page['sortdisable'] = '';
		$page['add_button'] = array('link'=>'admin/tariff/addpickup/'.$id,'label'=>'Add Pickup Charge Range');
		$page['ajaxurl'] = 'admin/tariff/ajaxpickup/'.$id;
		$page['page_title'] = 'Pickup Charges';
		$this->ag_auth->view('tariff/pickupajaxlistview',$page); // Load the view
	}

	public function addcod($app_id)
	{

		$this->breadcrumb->add_crumb('Users','admin/users/manage');
		$this->breadcrumb->add_crumb('Manage Merchant','admin/members/merchant');

		$app = $this->get_merchant_app($app_id);

		$merchant_name = $app['merchant_name'];
		$merchant_id = $app['merchant_id'];
		$app_name = $app['app_name'];

		$this->breadcrumb->add_crumb($app_name,'admin/members/merchant/apps/manage/'.$merchant_id);
		$this->breadcrumb->add_crumb('COD Surcharge','admin/tariff/cod/'.$app_id.'/'.$merchant_id);
		$this->breadcrumb->add_crumb('Add','admin/tariff/addcod/'.$app_id);


		$this->form_validation->set_rules('seq', 'Sequence', 'required|trim|xss_clean');
		$this->form_validation->set_rules('from_price', 'Price From', 'required|trim|xss_clean');
		$this->form_validation->set_rules('to_price', 'Price Up To', 'required|trim|xss_clean');
		$this->form_validation->set_rules('surcharge', 'Surcharge', 'required|trim|xss_clean');
		$this->form_validation->set_rules('period_from'	, 'Valid From', 'required|trim|xss_clean');
		$this->form_validation->set_rules('period_to', 'Valid Until', 'required|trim|xss_clean');

		$data['app_id'] = $app_id;

		if($this->form_validation->run() == FALSE)
		{
			$data['page_title'] = 'Add COD Surcharge Entry';
			$this->ag_auth->view('tariff/codadd',$data);
		}
		else
		{
			$dataset['seq'] = set_value('seq');
			$dataset['from_price'] = set_value('from_price');
			$dataset['app_id'] = $app_id;
			$dataset['to_price'] = set_value('to_price');
			$dataset['surcharge'] = set_value('surcharge');
			$dataset['period_from'] = set_value('period_from');
			$dataset['period_to'] = set_value('period_to');


			if($this->db->insert($this->config->item('jayon_cod_fee_table'),$dataset) === TRUE)
			//if($this->update_user($id,$dataset) === TRUE)
			{
				$success_url = 'admin/tariff/cod/'.$app_id.'/'.$merchant_id;
				$this->oi->add_success('COD fee added');
				redirect($success_url);

			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$error_url = 'admin/tariff/addcod/'.$app_id;
				$this->oi->add_error('Failed to add COD fee');
				redirect($error_url);
			}

		} // if($this->form_validation->run() == FALSE)

	} // public function register()


	public function editcod($id,$app_id)
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

	public function adddelivery($app_id)
	{

		$this->breadcrumb->add_crumb('Users','admin/users/manage');
		$this->breadcrumb->add_crumb('Manage Merchant','admin/members/merchant');

		$app = $this->get_merchant_app($app_id);

		$merchant_name = $app['merchant_name'];
		$merchant_id = $app['merchant_id'];
		$app_name = $app['app_name'];

		$this->breadcrumb->add_crumb($app_name,'admin/members/merchant/apps/manage/'.$merchant_id);
		$this->breadcrumb->add_crumb('Delivery charges','admin/tariff/delivery/'.$app_id.'/'.$merchant_id);
		$this->breadcrumb->add_crumb('Add','admin/tariff/adddelivery/'.$app_id);


		$this->form_validation->set_rules('seq', 'Sequence', 'required|trim|xss_clean');
		$this->form_validation->set_rules('kg_from', 'Kg From', 'required|trim|xss_clean');
		$this->form_validation->set_rules('kg_to', 'Kg Up To', 'required|trim|xss_clean');
		$this->form_validation->set_rules('calculated_kg', 'Calculated kg', 'required|trim|xss_clean');
		$this->form_validation->set_rules('tariff_kg', 'tarif per Kg', 'required|trim|xss_clean');
		$this->form_validation->set_rules('total', 'Total', 'required|trim|xss_clean');
		$this->form_validation->set_rules('period_from'	, 'Valid From', 'required|trim|xss_clean');
		$this->form_validation->set_rules('period_to', 'Valid Until', 'required|trim|xss_clean');

		$data['app_id'] = $app_id;

		if($this->form_validation->run() == FALSE)
		{
			$data['page_title'] = 'Add Delivery Charge Entry';
			$this->ag_auth->view('tariff/deliveryadd',$data);
		}
		else
		{
			$dataset['seq'] = set_value('seq');
			$dataset['app_id'] = $app_id;
			$dataset['kg_from'] = set_value('kg_from');
			$dataset['kg_to'] = set_value('kg_to');
			$dataset['calculated_kg'] = set_value('calculated_kg');
			$dataset['tariff_kg'] = set_value('tariff_kg');
			$dataset['total'] = set_value('total');
			$dataset['period_from'] = set_value('period_from');
			$dataset['period_to'] = set_value('period_to');


			if($this->db->insert($this->config->item('jayon_delivery_fee_table'),$dataset) === TRUE)
			//if($this->update_user($id,$dataset) === TRUE)
			{
				$success_url = 'admin/tariff/delivery/'.$app_id.'/'.$merchant_id;
				$this->oi->add_success('Delivery fee added');
				redirect($success_url);

			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$error_url = 'admin/tariff/adddelivery/'.$id.'/'.$app_id;
				$this->oi->add_error('Failed to add Delivery fee');
				redirect($error_url);
			}

		} // if($this->form_validation->run() == FALSE)

	} // public function register()


	public function editdelivery($id,$app_id)
	{

		$this->breadcrumb->add_crumb('Users','admin/users/manage');
		$this->breadcrumb->add_crumb('Manage Merchant','admin/members/merchant');

		$app = $this->get_merchant_app($app_id);

		$merchant_name = $app['merchant_name'];
		$merchant_id = $app['merchant_id'];
		$app_name = $app['app_name'];

		$this->breadcrumb->add_crumb($app_name,'admin/members/merchant/apps/manage/'.$merchant_id);
		$this->breadcrumb->add_crumb('Delivery charges','admin/tariff/delivery/'.$app_id.'/'.$merchant_id);
		$this->breadcrumb->add_crumb('Edit','admin/tariff/editdelivery/'.$id.'/'.$app_id);


		$this->form_validation->set_rules('seq', 'Sequence', 'required|trim|xss_clean');
		$this->form_validation->set_rules('kg_from', 'Kg From', 'required|trim|xss_clean');
		$this->form_validation->set_rules('kg_to', 'Kg Up To', 'required|trim|xss_clean');
		$this->form_validation->set_rules('calculated_kg', 'Calculated kg', 'required|trim|xss_clean');
		$this->form_validation->set_rules('tariff_kg', 'tarif per Kg', 'required|trim|xss_clean');
		$this->form_validation->set_rules('total', 'Total', 'required|trim|xss_clean');
		$this->form_validation->set_rules('period_from'	, 'Valid From', 'required|trim|xss_clean');
		$this->form_validation->set_rules('period_to', 'Valid Until', 'required|trim|xss_clean');

		$user = $this->get_delivery_entry($id);
		$data['user'] = $user;

		$data['app_id'] = $app_id;

		if($this->form_validation->run() == FALSE)
		{
			$data['page_title'] = 'Edit Delivery Charge Entry';
			$this->ag_auth->view('tariff/deliveryedit',$data);
		}
		else
		{
			$dataset['seq'] = set_value('seq');
			$dataset['kg_from'] = set_value('kg_from');
			$dataset['kg_to'] = set_value('kg_to');
			$dataset['calculated_kg'] = set_value('calculated_kg');
			$dataset['tariff_kg'] = set_value('tariff_kg');
			$dataset['total'] = set_value('total');
			$dataset['period_from'] = set_value('period_from');
			$dataset['period_to'] = set_value('period_to');


			if($this->db->where('id',$id)->update($this->config->item('jayon_delivery_fee_table'),$dataset) === TRUE)
			//if($this->update_user($id,$dataset) === TRUE)
			{
				$success_url = 'admin/tariff/delivery/'.$app_id.'/'.$merchant_id;
				$this->oi->add_success('Delivery fee updated');
				redirect($success_url);

			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$error_url = 'admin/tariff/editdelivery/'.$id.'/'.$app_id;
				$this->oi->add_error('Failed to update Delivery fee');
				redirect($error_url);
			}

		} // if($this->form_validation->run() == FALSE)

	} // public function register()


	public function addpickup($app_id)
	{

		$this->breadcrumb->add_crumb('Users','admin/users/manage');
		$this->breadcrumb->add_crumb('Manage Merchant','admin/members/merchant');

		$app = $this->get_merchant_app($app_id);

		$merchant_name = $app['merchant_name'];
		$merchant_id = $app['merchant_id'];
		$app_name = $app['app_name'];

		$this->breadcrumb->add_crumb($app_name,'admin/members/merchant/apps/manage/'.$merchant_id);
		$this->breadcrumb->add_crumb('Pickup charges','admin/tariff/pickup/'.$app_id.'/'.$merchant_id);
		$this->breadcrumb->add_crumb('Add','admin/tariff/addpickup/'.$app_id);


		$this->form_validation->set_rules('seq', 'Sequence', 'required|trim|xss_clean');
		$this->form_validation->set_rules('kg_from', 'Kg From', 'required|trim|xss_clean');
		$this->form_validation->set_rules('kg_to', 'Kg Up To', 'required|trim|xss_clean');
		$this->form_validation->set_rules('calculated_kg', 'Calculated kg', 'required|trim|xss_clean');
		$this->form_validation->set_rules('tariff_kg', 'tarif per Kg', 'required|trim|xss_clean');
		$this->form_validation->set_rules('total', 'Total', 'required|trim|xss_clean');
		$this->form_validation->set_rules('period_from'	, 'Valid From', 'required|trim|xss_clean');
		$this->form_validation->set_rules('period_to', 'Valid Until', 'required|trim|xss_clean');
        $this->form_validation->set_rules('note', 'Note', 'trim|xss_clean');


		$data['app_id'] = $app_id;

		if($this->form_validation->run() == FALSE)
		{
			$data['page_title'] = 'Add Pickup Charge Entry';
			$this->ag_auth->view('tariff/pickupadd',$data);
		}
		else
		{
			$dataset['seq'] = set_value('seq');
			$dataset['app_id'] = $app_id;
			$dataset['kg_from'] = set_value('kg_from');
			$dataset['kg_to'] = set_value('kg_to');
			$dataset['calculated_kg'] = set_value('calculated_kg');
			$dataset['tariff_kg'] = set_value('tariff_kg');
			$dataset['total'] = set_value('total');
			$dataset['period_from'] = set_value('period_from');
			$dataset['period_to'] = set_value('period_to');
            $dataset['note'] = set_value('note');


			if($this->db->insert($this->config->item('jayon_pickup_fee_table'),$dataset) === TRUE)
			//if($this->update_user($id,$dataset) === TRUE)
			{
				$success_url = 'admin/tariff/pickup/'.$app_id.'/'.$merchant_id;
				$this->oi->add_success('Pickup fee added');
				redirect($success_url);

			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$error_url = 'admin/tariff/addpickup/'.$id.'/'.$app_id;
				$this->oi->add_error('Failed to add Pickup fee');
				redirect($error_url);
			}

		} // if($this->form_validation->run() == FALSE)

	} // public function register()


	public function editpickup($id,$app_id)
	{

		$this->breadcrumb->add_crumb('Users','admin/users/manage');
		$this->breadcrumb->add_crumb('Manage Merchant','admin/members/merchant');

		$app = $this->get_merchant_app($app_id);

		$merchant_name = $app['merchant_name'];
		$merchant_id = $app['merchant_id'];
		$app_name = $app['app_name'];

		$this->breadcrumb->add_crumb($app_name,'admin/members/merchant/apps/manage/'.$merchant_id);
		$this->breadcrumb->add_crumb('Pickup charges','admin/tariff/pickup/'.$app_id.'/'.$merchant_id);
		$this->breadcrumb->add_crumb('Edit','admin/tariff/editpickup/'.$id.'/'.$app_id);


		$this->form_validation->set_rules('seq', 'Sequence', 'required|trim|xss_clean');
		$this->form_validation->set_rules('kg_from', 'Kg From', 'required|trim|xss_clean');
		$this->form_validation->set_rules('kg_to', 'Kg Up To', 'required|trim|xss_clean');
		$this->form_validation->set_rules('calculated_kg', 'Calculated kg', 'required|trim|xss_clean');
		$this->form_validation->set_rules('tariff_kg', 'tarif per Kg', 'required|trim|xss_clean');
		$this->form_validation->set_rules('total', 'Total', 'required|trim|xss_clean');
		$this->form_validation->set_rules('period_from'	, 'Valid From', 'required|trim|xss_clean');
		$this->form_validation->set_rules('period_to', 'Valid Until', 'required|trim|xss_clean');
        $this->form_validation->set_rules('note', 'Note', 'trim|xss_clean');

		$user = $this->get_pickup_entry($id);
		$data['user'] = $user;

		$data['app_id'] = $app_id;

		if($this->form_validation->run() == FALSE)
		{
			$data['page_title'] = 'Edit Delivery Charge Entry';
			$this->ag_auth->view('tariff/pickupedit',$data);
		}
		else
		{
			$dataset['seq'] = set_value('seq');
			$dataset['kg_from'] = set_value('kg_from');
			$dataset['kg_to'] = set_value('kg_to');
			$dataset['calculated_kg'] = set_value('calculated_kg');
			$dataset['tariff_kg'] = set_value('tariff_kg');
			$dataset['total'] = set_value('total');
			$dataset['period_from'] = set_value('period_from');
			$dataset['period_to'] = set_value('period_to');
            $dataset['note'] = set_value('note');


			if($this->db->where('id',$id)->update($this->config->item('jayon_pickup_fee_table'),$dataset) === TRUE)
			//if($this->update_user($id,$dataset) === TRUE)
			{
				$success_url = 'admin/tariff/pickup/'.$app_id.'/'.$merchant_id;
				$this->oi->add_success('Delivery fee updated');
				redirect($success_url);

			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$error_url = 'admin/tariff/editpickup/'.$id.'/'.$app_id;
				$this->oi->add_error('Failed to update Delivery fee');
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

	public function get_pickup_entry($id){
		$result = $this->db->where('id', $id)->get($this->config->item('jayon_pickup_fee_table'));
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

		$this->db->where('app_id', $app_id);

		// get total count result
		$count_all = $this->db->count_all($this->config->item('jayon_cod_fee_table'));

		$count_display_all = $this->db->count_all_results($this->config->item('jayon_cod_fee_table'));

		$this->db->where('app_id', $app_id);

		$this->db->order_by('seq','asc');

		$data = $this->db->limit($limit_count, $limit_offset)->order_by($columns[$sort_col],$sort_dir)->get($this->config->item('jayon_cod_fee_table'));

		//print $this->db->last_query();

		$result = $data->result_array();

		$aadata = array();


		foreach($result as $value => $key)
		{
			$delete = '<span id="'.$key['id'].'" class="delete_link" style="cursor:pointer;text-decoration:underline;">Delete</span>'; // Build actions links
			$edit = anchor("admin/tariff/editcod/".$key['id']."/".$key['app_id'], "Edit"); // Build actions links
			$detail = anchor("admin/tariff/details/".$key['id']."/", $key['id']); // Build detail links
			$aadata[] = array(
				$key['seq'],
				$key['from_price'],
				$key['to_price'],
				$key['surcharge'],
				$key['period_from'],
				$key['period_to'],
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

	public function ajaxdelivery($app_id){

		$limit_count = $this->input->post('iDisplayLength');
		$limit_offset = $this->input->post('iDisplayStart');

		$sort_col = $this->input->post('iSortCol_0');
		$sort_dir = $this->input->post('sSortDir_0');

		$columns = array(
			'seq','kg_from','kg_to','calculated_kg','tariff_kg','total','period_from','period_to','is_on'
			);

		$this->db->where('app_id', $app_id);

		// get total count result
		$count_all = $this->db->count_all($this->config->item('jayon_delivery_fee_table'));

		$count_display_all = $this->db->count_all_results($this->config->item('jayon_delivery_fee_table'));

		$this->db->where('app_id', $app_id);

		$this->db->order_by('seq','asc');

		$data = $this->db->limit($limit_count, $limit_offset)->order_by($columns[$sort_col],$sort_dir)->get($this->config->item('jayon_delivery_fee_table'));

		//print $this->db->last_query();

		$result = $data->result_array();

		$aadata = array();


		foreach($result as $value => $key)
		{
			$delete = '<span id="'.$key['id'].'" class="delete_link" style="cursor:pointer;text-decoration:underline;">Delete</span>'; // Build actions links
			$edit = anchor("admin/tariff/editdelivery/".$key['id']."/".$key['app_id'], "Edit"); // Build actions links
			$aadata[] = array(
				$key['seq'],
				$key['kg_from'],
				$key['kg_to'],
				$key['calculated_kg'],
				$key['tariff_kg'],
				$key['total'],
				$key['period_from'],
				$key['period_to']
				,$edit.' '.$delete); // Adding row to table
		}

		$result = array(
			'sEcho'=> $this->input->post('sEcho'),
			'iTotalRecords'=>$count_all,
			'iTotalDisplayRecords'=> $count_display_all,
			'aaData'=>$aadata
		);

		print json_encode($result);
	}

	public function ajaxpickup($app_id){

		$limit_count = $this->input->post('iDisplayLength');
		$limit_offset = $this->input->post('iDisplayStart');

		$sort_col = $this->input->post('iSortCol_0');
		$sort_dir = $this->input->post('sSortDir_0');

		$columns = array(
			'seq','kg_from','kg_to','calculated_kg','tariff_kg','total','period_from','period_to','is_on','note'
			);

		$this->db->where('app_id', $app_id);

		// get total count result
		$count_all = $this->db->count_all($this->config->item('jayon_pickup_fee_table'));

		$count_display_all = $this->db->count_all_results($this->config->item('jayon_pickup_fee_table'));

		$this->db->where('app_id', $app_id);

		$this->db->order_by('seq','asc');

		$data = $this->db->limit($limit_count, $limit_offset)->order_by($columns[$sort_col],$sort_dir)->get($this->config->item('jayon_pickup_fee_table'));

		//print $this->db->last_query();

		$result = $data->result_array();

		$aadata = array();


		foreach($result as $value => $key)
		{
			$delete = '<span id="'.$key['id'].'" class="delete_link" style="cursor:pointer;text-decoration:underline;">Delete</span>'; // Build actions links
			$edit = anchor("admin/tariff/editpickup/".$key['id']."/".$key['app_id'], "Edit"); // Build actions links
			$aadata[] = array(
				$key['seq'],
				$key['kg_from'],
				$key['kg_to'],
				$key['calculated_kg'],
				$key['tariff_kg'],
				$key['total'],
				$key['period_from'],
				$key['period_to'],
                $key['note']
				,$edit.' '.$delete); // Adding row to table
		}

		$result = array(
			'sEcho'=> $this->input->post('sEcho'),
			'iTotalRecords'=>$count_all,
			'iTotalDisplayRecords'=> $count_display_all,
			'aaData'=>$aadata
		);

		print json_encode($result);
	}


	public function ajaxdeletecod()
	{
		$id = $this->input->post('id');

		if($this->db->where('id', $id)->delete($this->config->item('jayon_cod_fee_table'))){
			print json_encode(array('result'=>'ok'));
		}else{
			print json_encode(array('result'=>'failed'));
		}
	}

	public function ajaxdeletedelivery()
	{
		$id = $this->input->post('id');

		if($this->db->where('id', $id)->delete($this->config->item('jayon_delivery_fee_table'))){
			print json_encode(array('result'=>'ok'));
		}else{
			print json_encode(array('result'=>'failed'));
		}
	}

	public function ajaxdeletepickup()
	{
		$id = $this->input->post('id');

		if($this->db->where('id', $id)->delete($this->config->item('jayon_pickup_fee_table'))){
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