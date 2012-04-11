<?php

class Order extends Application
{
	
	public function __construct()
	{
		parent::__construct();
		//$this->ag_auth->restrict('admin'); // restrict this controller to admins only
		$this->table_tpl = array(
			'table_open' => '<table border="0" cellpadding="4" cellspacing="0" class="dataTable">',
			'row_start' => '<tr class="detail_row">',
			'tbody_open' => '<tbody id="detail_body">'
		);
		$this->table->set_template($this->table_tpl);
	    
	}

	public function neworder()
	{
		$data['page_title'] = 'New Delivery Orders';
		$this->load->view('auth/pages/neworderform',$data); // Load the view
	}

}

?>