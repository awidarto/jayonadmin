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

	public function rotatephoto(){
		$delivery_id = $this->input->post('delivery_id');

		$delivery_id = trim(str_replace('r_', '', $delivery_id));

		$this->load->library('image_lib');

		$config['image_library'] = 'imagemagick';
		$config['library_path'] = '/usr/bin/';
		$config['rotation_angle'] = '90';
		$config['source_image']	= $this->config->item('thumbnail_path').'th_'.$delivery_id.'.jpg';

		chmod($config['source_image'],0777);

		$this->image_lib->initialize($config); 

		if ( $this->image_lib->rotate())
		{
			$thresult = 'thumbnail rotated '.$config['source_image'];
		}else{
			$thresult = 'thumbnail rotation failed';
		}

		$this->image_lib->clear();
		
		//$config['image_library'] = 'imagemagick';
		//$config['library_path'] = '/usr/bin/';
		//$config['rotation_angle'] = '90';
		$config['source_image']	= $this->config->item('picture_path').$delivery_id.'.jpg';
		//$config['source_image']	= $this->config->item('thumbnail_path').'th_'.$delivery_id.'.jpg';

		chmod($config['source_image'],0777);

		$this->image_lib->initialize($config); 

		if ( $this->image_lib->rotate())
		{
			$result = 'ok';
		}else{
			$result = 'err';
		}

		print json_encode(array('result'=>$result,'data'=>$thresult.' '.$this->image_lib->display_errors()));
	}

	public function getorder(){
		$delivery_id = $this->input->post('delivery_id');

		$this->db->select($this->config->item('incoming_delivery_table').'.*,d.identifier as device,c.fullname as courier');
		$this->db->join('devices as d',$this->config->item('assigned_delivery_table').'.device_id=d.id','left');
		$this->db->join('couriers as c',$this->config->item('assigned_delivery_table').'.courier_id=c.id','left');


		$order = $this->db->where($this->config->item('incoming_delivery_table').'.delivery_id',$delivery_id)
					->get($this->config->item('incoming_delivery_table'));

		if($order->num_rows() > 0){
			print json_encode(array('result'=>'ok','data'=>$order->row_array()));
		}else{
			print json_encode(array('result'=>'err','data'=>'No data found'));
		}

	}

	public function getappselect(){
		$merchant_id = $this->input->post('merchant_id');
		$delivery_type = $this->input->post('delivery_type');

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

	public function getweightdata(){
		$app_key = $this->input->post('app_key');
		if($app_key == '0'){
			$dctable = false;
			$app_id = 0;
		}else{
			$app_id = get_app_id_from_key($app_key);
			$dctable = get_delivery_charge_table($app_id);
		}

		if($dctable == true){
			$weight[0] = 'Select weight range';
			foreach ($dctable as $r) {
				$weight[$r->total] = $r->kg_from.' kg - '.$r->kg_to.' kg';
				$this->table->add_row($r->kg_from.' kg - '.$r->kg_to.' kg', 'IDR '.number_format($r->total,2,',','.'));
			}
		}else{
			$dctable = get_delivery_charge_table(0);
			$weight[0] = 'Select weight range';
			foreach ($dctable as $r) {
				$weight[$r->total] = $r->kg_from.' kg - '.$r->kg_to.' kg';
				$this->table->add_row($r->kg_from.' kg - '.$r->kg_to.' kg', 'IDR '.number_format($r->total,2,',','.'));
			}
		}

		$weightselect = form_dropdown('package_weight',$weight,null,'id="package_weight"');
		$weighttable = $this->table->generate();

		print json_encode(array('result'=>'ok','data'=>array('app_id'=>$app_id,'selector'=>$weightselect,'table'=>$weighttable)));
	}

	public function getcoddata(){
		$app_key = $this->input->post('app_key');
		if($app_key == '0'){
			$dctable = false;
			$app_id = 0;
		}else{
			$app_id = get_app_id_from_key($app_key);
			$dctable = get_cod_table($app_id);
		}

		if($dctable == true){
			foreach ($dctable as $r) {
				$this->table->add_row('IDR '.number_format($r->from_price,2,',','.').' - IDR '.number_format($r->to_price,2,',','.'), 'IDR '.number_format($r->surcharge,2,',','.'));
			}
		}else{
			$dctable = get_cod_table(0);
			foreach ($dctable as $r) {
				$this->table->add_row('IDR '.number_format($r->from_price,2,',','.').' - IDR '.number_format($r->to_price,2,',','.'), 'IDR '.number_format($r->surcharge,2,',','.'));
			}
		}

		$codhash = json_encode($dctable);			
		$codselect = $dctable;
		$codtable = $this->table->generate();

		print json_encode(array('result'=>'ok','data'=>array('selector'=>$codselect,'codhash'=>$codhash,'table'=>$codtable)));		
	}

	public function getweighttable(){
		$app_id = $this->input->post('app_id');
		$dctable = get_delivery_charge_table($app_id);


	}

	public function getcodtable(){
		
	}

	function reassign(){
		$delivery_id = $this->input->post('delivery_id');
		$courier_id = $this->input->post('courier_id');
		$assignment_device_id = $this->input->post('assignment_device_id');
		$assignment_timeslot = $this->input->post('assignment_timeslot');
		$assignment_date = $this->input->post('assignment_date');

		if($courier_id == 'current'){

			/*
				$courier = $this->db
					->distinct()
					->select('courier_id')
					->where('device_id',$assignment_device_id)
					->where('assignment_date',$assignment_date)
					->get($this->config->item('assigned_delivery_table'));
			*/

			$courier = $this->db
				->select('courier_id')
				->where('device_id',$assignment_device_id)
				->where('delivery_date',$assignment_date)
				->get($this->config->item('device_assignment_table'));

			//print $this->db->last_query();

			if($courier->num_rows() > 0){
				$courier_id = $courier->row()->courier_id;
				$dataset['courier_id'] = $courier_id;
			}else{
				$dataset['courier_id'] = '';
				$dataset['status'] = $this->config->item('trans_status_admin_devassigned');
			}
		}

		$dataset['device_id'] = $assignment_device_id;
		$dataset['assignment_timeslot'] = $assignment_timeslot;

		if($this->db->where('delivery_id',$delivery_id)->update($this->config->item('incoming_delivery_table'),$dataset) == TRUE){
			$result = json_encode(array('status'=>'OK:REASSIGNED','timestamp'=>now(),'delivery_id'=>$delivery_id,'courier_id'=>$courier_id));
		}else{
			$result = json_encode(array('status'=>'ERR:NOREASSIGN','timestamp'=>now(),'delivery_id'=>'','buyer_id'=>''));
		}

		print $result;

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

	public function getzoneselect(){
		$city = $this->input->post('city');

		$this->db->where(array('city'=>$city));
		$this->db->where(array('is_on'=>1));
		$zones = $this->db->get($this->config->item('jayon_zones_table'));

		if($zones->num_rows() > 0){
			$zone[0] = 'Select delivery zone';
			foreach ($zones->result() as $r) {
				$zone[trim($r->district)] = trim($r->district);
			}
		}else{
			$zone[0] = 'Select delivery zone';
		}

		$select = form_dropdown('buyerdeliveryzone',$zone,null,'id="buyerdeliveryzone"');

		print json_encode(array('result'=>'ok','data'=>$select));
	}	

	public function getslotselect(){

		$this->db->where(array('is_on'=>1));
		$slots = $this->db->get($this->config->item('jayon_timeslot_table'));

		if($slots->num_rows() > 0){
			$slot[0] = 'Select delivery slot';
			foreach ($slots->result() as $r) {
				$slot[$r->slot_no] = $r->time_from.':00 - '.$r->time_to.':00';
			}
		}else{
			$slot[0] = 'Select delivery slot';
		}

		$select = form_dropdown('buyerdeliverytime',$slot,null,'id="buyerdeliverytime"');

		print json_encode(array('result'=>'ok','data'=>$select));
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
			'buyerdeliveryslot'=>$this->input->post('buyerdeliveryslot'),
			'directions'=>$this->input->post('direction'),
			'auto_confirm'=>$this->input->post('auto_confirm'),
			'email'=>$this->input->post('email'),
			'zip' => $this->input->post('zip'),
			'phone' => $this->input->post('phone'),
			'total_price'=>$this->input->post('total_price'),
			'total_discount'=>$this->input->post('total_discount'),
			'total_tax'=>$this->input->post('total_tax'),
			'chargeable_amount'=>$this->input->post('chargeable_amount'),
			'delivery_cost' => $this->input->post('delivery_cost'), 		
			'cod_cost' => $this->input->post('cod_cost'), 		
			'currency' => $this->input->post('currency'), 	
			'status'=>$this->input->post('status'),
			'merchant_id'=>$this->input->post('merchant_id'),
			'buyer_id'=>$this->input->post('buyer_id'),
			'trx_detail'=>$trx_detail,
			'width' => $this->input->post('width'),
			'height' => $this->input->post('height'),
			'length' => $this->input->post('length'),
			'weight' => $this->input->post('weight'),
			'delivery_type' => $this->input->post('delivery_type')
		);

		$trx['transaction_id'] = 'TRX_'.$merchant_id.'_'.str_replace(array(' ','.'), '', microtime());

		$api_key = $this->input->post('api_key');
		$trx_id = $trx['transaction_id'];

		$url = base_url().'api/v1/post/'.$api_key.'/'.$trx_id;

		$result = $this->curl->simple_post($url,array('transaction_detail'=>json_encode($trx)));
		
		print $result;

	}


	public function saveweight(){
		$delivery_id = $this->input->post('delivery_id');
        $delivery_cost = $this->input->post('weight_tariff');

			$order = $this->db->where('delivery_id',$delivery_id)->get($this->config->item('incoming_delivery_table'));
			$order = $order->row_array();

			$total = str_replace(array(',','.'), '', $order['total_price']);
			$dsc = str_replace(array(',','.'), '', $order['total_discount']);
			$tax = str_replace(array(',','.'), '',$order['total_tax']);

			$dc = str_replace(array(',','.'), '',$delivery_cost);
			$cod = str_replace(array(',','.'), '',$order['cod_cost']);

			$total = (int)$total;
			$dsc = (int)$dsc;
			$tax = (int)$tax;
			$dc = (int)$dc;
			$cod = (int)$cod;

			$chg = ($total - $dsc) + $tax + $dc + $cod;

			$newdata = array(
				'delivery_cost'=>$delivery_cost,
				'weight'=>$delivery_cost
			);

		$this->db->where('delivery_id',$delivery_id)->update($this->config->item('incoming_delivery_table'),array('delivery_cost'=>$delivery_cost,'weight'=>$delivery_cost));

		if($this->db->affected_rows() > 0){

			print json_encode(array('status'=>'OK','delivery_cost'=>number_format($delivery_cost,2,',','.'),'weight_range'=>get_weight_range($delivery_cost),'total_charges'=>number_format($chg,2,',','.')));
		}else{
			print json_encode(array('status'=>'ERR','delivery_cost'=>0));
		}

	}

	public function savedeliverytype(){
		$delivery_id = $this->input->post('delivery_id');
        $delivery_type = $this->input->post('delivery_type');

			$order = $this->db->where('delivery_id',$delivery_id)->get($this->config->item('incoming_delivery_table'));
			$order = $order->row_array();

			$total = str_replace(array(',','.'), '', $order['total_price']);
			$dsc = str_replace(array(',','.'), '', $order['total_discount']);
			$tax = str_replace(array(',','.'), '',$order['total_tax']);

			$dc = str_replace(array(',','.'), '',$order['delivery_cost']);
			$cod = str_replace(array(',','.'), '',$order['cod_cost']);

			$total = (int)$total;
			$dsc = (int)$dsc;
			$tax = (int)$tax;
			$dc = (int)$dc;

			if($delivery_type == 'COD'){
				$cod = get_cod_tariff(($total - $dsc) + $tax);
			}else{
				$cod = 0;
			}

			$chg = ($total - $dsc) + $tax + $dc + $cod;

			$newdata = array(
				'cod_cost'=>$cod,
				'delivery_type'=>$delivery_type
			);


		$this->db->where('delivery_id',$delivery_id)->update($this->config->item('incoming_delivery_table'),$newdata);

		if($this->db->affected_rows() > 0){
			print json_encode(array('status'=>'OK','delivery_type'=>$delivery_type,'cod_cost'=>number_format($cod,2,',','.'),'total_charges'=>number_format($chg,2,',','.')));
		}else{
			print json_encode(array('status'=>'ERR','delivery_type'=>0));
		}

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