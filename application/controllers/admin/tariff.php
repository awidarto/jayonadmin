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
		$page['ajaxurl'] = 'admin/apps/ajaxmerchantmanage/'.$id;
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


}

?>