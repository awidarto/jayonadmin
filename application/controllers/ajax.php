<?php

class Ajax extends Application
{
	public function __construct()
	{
		parent::__construct();
	}

	public function getzone(){
		$q = $this->input->get('term');
		$zones = ajax_find_zones($q,'district');
		print json_encode($zones);
	}

	public function getcities(){
		$q = $this->input->get('term');
		$zones = ajax_find_cities($q,'city');
		print json_encode($zones);
	}                       
	
	public function getprovinces(){
		$q = $this->input->get('term');
		$zones = ajax_find_provinces($q,'province');
		print json_encode($zones);
	}

	public function getcountries(){
		$q = $this->input->get('term');
		$zones = ajax_find_countries($q,'country');
		print json_encode($zones);
	}

	public function getcourier(){
		$q = $this->input->get('term');
		$zones = ajax_find_courier($q,'fullname','id');
		print json_encode($zones);
	}

	public function getmerchant(){
		$q = $this->input->get('term');
		$merchants = ajax_find_merchants($q,'fullname','id');
		print json_encode($merchants);
	}

	public function getbuyer(){
		$q = $this->input->get('term');
		$merchants = ajax_find_buyer($q,'fullname','id');
		print json_encode($merchants);
	}

	public function getbuyeremail(){
		$q = $this->input->get('term');
		$merchants = ajax_find_buyer_email($q,'fullname','id');
		print json_encode($merchants);
	}

	public function getdevice(){
		$q = $this->input->get('term');
		$zones = ajax_find_device($q,'identifier');
		print json_encode($zones);
	}

	public function getappselect(){
		$merchant_id = $this->input->post('merchant_id');

		$this->db->select('key,application_name');
		$this->db->where('merchant_id',$merchant_id);
		$apps = $this->db->get($this->config->item('applications_table'));

		if($apps->num_rows() > 0){
			foreach ($apps->result() as $r) {
				$app[$r->key] = $r->application_name;
			}			
		}else{
			$app[0] = 'Select application domain';
		}

		$select = form_dropdown('app_id',$app,null,'id="app_id"');

		print json_encode(array('result'=>'ok','data'=>$select));
	}

	public function neworder(){

		$url = $this->config->item('api_url').'post/'.$api_key.'/'.$trx_id;
		
		$trx = array(
			'api_key'=>$api_key,
			'transaction_id'=>$trx_id,
			'buyer_name'=>$buyer_name,
			'recipient_name'=>$recipient_name,
			'shipping_address'=>$shipping_address,
			'buyerdeliveryzone'=>$buyerdeliveryzone,
			'buyerdeliverycity'=>$buyerdeliverycity,
			'buyerdeliverytime'=>$buyerdeliverytime,
			'directions'=>$directions,
			'auto_confirm'=>false,
			'email'=>$email,
			'zip' => $zip,
			'phone' => $phone,
			'total_price'=>500000,
			'total_discount'=>20000,
			'total_tax'=>'117.500',
			'chargeable_amount'=>500000,
			'cod_cost' => '0', 		/* cod_cost 0 if absorbed in price of goods sold, otherwise specify the amount here*/
			'currency' => 'IDR', 	/* currency in 3 digit codes*/
			'status'=>$status, 	/* status can be : pending or confirm, depending on merchant's workflow */

			/*
				trx_detail should contain merchants transaction details for perticular session, below are just example
			*/

			'trx_detail'=>array( // 
				array(
					'unit_description'=>'kaos oblong swan',
					'unit_price'=>3000,
					'unit_quantity'=>100,
					'unit_total'=>280000,
					'unit_discount'=>20000
				),
				array(
					'unit_description'=>'kaos turtle neck',
					'unit_price'=>35000,
					'unit_quantity'=>2,
					'unit_total'=>70000,
					'unit_discount'=>0,
				),
				array(
					'unit_description'=>'kaos polo biru',
					'unit_price'=>135000,
					'unit_quantity'=>5,
					'unit_total'=>675000,
					'unit_discount'=>0,
				),
				array(
					'unit_description'=>'kaos kutung',
					'unit_price'=>15000,
					'unit_quantity'=>10,
					'unit_total'=>150000,
					'unit_discount'=>0
				)
			)
		);
		
		$result = $this->curl->simple_post($url,array('transaction_detail'=>json_encode($trx)));

		
		print $result;

	}

	public function subcalc(){
		
	}

	public function getdateblock($month = null){
		print getdateblock($month);
	}	
	
	public function incomingmonthly(){
		
	}

	public function deliveredmonthly(){
		
	}

	public function rescheduledmonthly(){
		
	}

	public function revokedmonthly(){
		
	}



}

/* End of file: buy.php */
/* Location: application/controllers/admin/dashboard.php */