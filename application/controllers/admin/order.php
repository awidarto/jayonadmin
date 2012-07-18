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

		$this->load->library('table');

		$this->db->distinct('city');
		$this->db->order_by('city');
		$cities = $this->db->get($this->config->item('jayon_zones_table'));

		if($cities->num_rows() > 0){
			$city[0] = 'Select delivery city';
			foreach ($cities->result() as $r) {
				$city[$r->city] = $r->city;
			}
		}else{
			$city[0] = 'Select delivery city';
		}

		$cityselect = form_dropdown('buyerdeliverycity',$city,null,'id="buyerdeliverycity"');

		$delivery_type = array(
			'0'=>'Select delivery type',
			'COD'=>'COD',
			'Delivery Only'=>'Delivery Only'
		);

		$typeselect = form_dropdown('delivery_type',$delivery_type,null,'id="delivery_type"');

		$tmpl = array ( 'table_open'=> '<table class="tariff" border="0" cellpadding="3" cellspacing="0">');

		$this->table->set_template($tmpl);

		$this->table->set_heading('Weight', 'Tariff');


		$this->db->select('seq,kg_from,kg_to,calculated_kg,tariff_kg,total');
		$this->db->order_by('seq','asc');
		$weights = $this->db->get($this->config->item('jayon_delivery_fee_table'));

		if($weights->num_rows() > 0){
			$weight[0] = 'Select weight range';
			foreach ($weights->result() as $r) {
				$weight[$r->total] = $r->kg_from.' kg - '.$r->kg_to.' kg';
				$this->table->add_row($r->kg_from.' kg - '.$r->kg_to.' kg', 'IDR '.number_format($r->total,2,',','.'));
			}
		}else{
			$weight[0] = 'Select weight range';
		}

		$weightselect = form_dropdown('package_weight',$weight,null,'id="package_weight"');
		$weighttable = $this->table->generate();

		$this->table->clear();

		$tmpl = array ( 'table_open'=> '<table class="tariff" id="cod_table" border="0" cellpadding="3" cellspacing="0">');
		$this->table->set_template($tmpl);		

		$this->table->set_heading('Total Price', 'Tariff');

		$this->db->select('seq,from_price,to_price,surcharge');	
		$this->db->order_by('seq','asc');
		$cods = $this->db->get($this->config->item('jayon_cod_fee_table'));

		foreach($cods->result() as $r){
			$this->table->add_row('IDR '.number_format($r->from_price,2,',','.').' - IDR '.number_format($r->to_price,2,',','.'), 'IDR '.number_format($r->surcharge,2,',','.'));
		}

		$codtable = $this->table->generate();

		$codhash = json_encode($cods->result_array());

	    $data['merchantemail'] = $this->session->userdata('email');
	    $data['merchantname'] = $this->session->userdata('merchantname');
	    $data['merchantfullname'] = $this->session->userdata('fullname');
		$data['cityselect'] = $cityselect;
		$data['typeselect'] = $typeselect;
		$data['weightselect'] = $weightselect;
		$data['weighttable'] = $weighttable;
		$data['codtable'] = $codtable;
		$data['codhash'] = $codhash;

		$data['page_title'] = 'New Delivery Orders';
		$this->load->view('auth/pages/neworderform',$data); // Load the view
	}

}

?>