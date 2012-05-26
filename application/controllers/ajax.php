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
			$app[0] = 'Select application domain';
			foreach ($apps->result() as $r) {
				$app[$r->key] = $r->application_name;
			}
		}else{
			$app[0] = 'Select application domain';
		}

		$select = form_dropdown('app_id',$app,null,'id="app_id"');

		print json_encode(array('result'=>'ok','data'=>$select));
	}

	public function editdetail(){
		$delivery_id = $this->input->post('delivery_id');

		$dataset['recipient_name'] = $this->input->post('recipient_name');
		$dataset['shipping_address'] = $this->input->post('shipping_address');
		$dataset['buyerdeliveryzone'] = $this->input->post('buyerdeliveryzone');
		$dataset['buyerdeliverycity'] = $this->input->post('buyerdeliverycity');
		$dataset['directions'] = $this->input->post('directions');
		$dataset['assignment_date'] = $this->input->post('assignment_date');
		//$dataset['auto_confirm'] = $this->input->post('auto_confirm');
		$dataset['phone'] = $this->input->post('phone');
		//$dataset['total_price'] = $this->input->post('total_price');
		//$dataset['total_discount'] = $this->input->post('total_discount');
		//$dataset['total_tax'] = $this->input->post('total_tax');
		//$dataset['chargeable_amount'] = $this->input->post('chargeable_amount');
		//$dataset['status'] = $this->input->post('status');

		if($this->db->where('delivery_id',$delivery_id)->update($this->config->item('incoming_delivery_table'),$dataset) == TRUE){
			$result = json_encode(array('status'=>'OK:ORDERUPDATED','timestamp'=>now(),'delivery_id'=>'','buyer_id'=>''));
		}else{
			$result = json_encode(array('status'=>'ERR:NOORDERUPDATED','timestamp'=>now(),'delivery_id'=>'','buyer_id'=>''));
		}

		print $result;
	}

	public function neworder(){

		$this->load->library('curl');

		$udescs = $this->input->post('udescs');
        $uqtys = $this->input->post('uqtys');
        $uprices = $this->input->post('uprices');
        $upctdisc = $this->input->post('upctdisc');
        $unomdisc = $this->input->post('unomdisc');
        $utotals = $this->input->post('utotals');

        $trx_detail = array();

        for($i=0;$i < sizeof($uprices);$i++){
        	$line = array(
        			'unit_description'=>$udescs[$i],
					'unit_price'=>$uprices[$i],
					'unit_quantity'=>$uqtys[$i],
					'unit_total'=>$utotals[$i],
					'unit_pct_discount'=>$upctdisc[$i],
					'unit_discount'=>$unomdisc[$i]
        		);
        	$trx_detail[] = $line;
        }


		$merchant_id = $this->input->post('merchant_id');
		$buyer_id = $this->input->post('buyer_id');		

		$trx = array(
			'api_key'=>$this->input->post('api_key'),
			'buyer_name'=>$this->input->post('buyer_name'),
			'recipient_name'=>$this->input->post('recipient_name'),
			'shipping_address'=>$this->input->post('shipping_address'),
			'buyerdeliveryzone'=>$this->input->post('buyerdeliveryzone'),
			'buyerdeliverycity'=>$this->input->post('buyerdeliverycity'),
			'buyerdeliverytime'=>$this->input->post('buyerdeliverytime'),
			'directions'=>$this->input->post('direction'),
			'auto_confirm'=>$this->input->post('auto_confirm'),
			'email'=>$this->input->post('email'),
			'zip' => $this->input->post('zip'),
			'phone' => $this->input->post('phone'),
			'total_price'=>$this->input->post('total_price'),
			'total_discount'=>$this->input->post('total_discount'),
			'total_tax'=>$this->input->post('total_tax'),
			'chargeable_amount'=>$this->input->post('chargeable_amount'),
			'cod_cost' => $this->input->post('cod_cost'), 		
			'currency' => $this->input->post('currency'), 	
			'status'=>$this->input->post('status'),
			'merchant_id'=>$this->input->post('merchant_id'),
			'buyer_id'=>$this->input->post('buyer_id'),
			'trx_detail'=>$trx_detail
		);

		$trx['transaction_id'] = 'TRX_'.$merchant_id.'_'.str_replace(array(' ','.'), '', microtime());

		$api_key = $this->input->post('api_key');
		$trx_id = $trx['transaction_id'];

		$url = base_url().'api/v1/post/'.$api_key.'/'.$trx_id;

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

	public function getlastfiveloc(){
		
		$locs = $this->db->select('timestamp,identifier,latitude as lat,longitude as lng')
			->limit(5,0)
			->order_by('timestamp','desc')
			->get($this->config->item('location_log_table'));

		$locations = array();

		foreach ($locs->result() as $l) {
			$locations[] = array(
				'lat'=>(double)$l->lat,
				'lng'=>(double)$l->lng,
				'data'=>array(
						'timestamp'=>$l->timestamp,
						'identifier'=>$l->identifier
					)
				);
		}

		$locjson = json_encode($locations);

		print str_replace('"', '', $locjson);
	}


}

/* End of file: buy.php */
/* Location: application/controllers/admin/dashboard.php */