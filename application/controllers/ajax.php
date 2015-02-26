<?php

class Ajax extends Application
{
	public function __construct()
	{
		parent::__construct();

        date_default_timezone_set('Asia/Jakarta');

        $this->accessor_ip = $_SERVER['REMOTE_ADDR'];

	}

    public function printsession(){
        $ids = $this->input->post('ids');
        $sess = mt_rand( 1000, 9999 );
        $sess = 'PRINT_'.$sess.time();

        session_start();
        $_SESSION[ $sess ] = $ids;

        print json_encode(array(
                'result'=>'OK',
                'session'=>$sess
            ));
    }

    public function printdefault(){
        $def = $this->input->post();
        //print_r($this->session->userdata());

        $def['user_id'] = $this->session->userdata('userid');
        $def['user_group'] = user_group_id('admin');

        $df = $this->db->where('user_id',$def['user_id'])
                ->where('user_group',$def['user_group'])
                ->get('print_defaults');

        $result = 'NOK';

        if($df->num_rows() > 0){
            $this->db->where('user_id',$def['user_id'])
                ->where('user_group',$def['user_group'])
                ->update('print_defaults',$def);
        }else{
            $res = $this->db->insert('print_defaults', $def);
        }

        if($this->db->affected_rows() > 0){
            $result = 'OK';
        }

        print json_encode(array('result'=>$result, 'rows'=>$this->db->affected_rows()));

    }

    public function ocr(){
        $filename = $this->input->post('filename');
        $file = FCPATH.'public/pickup/'.$filename;

        $ocrfile = str_replace('pickup', 'ocr', $file);
        $ocrfile = str_replace('.jpg', '.txt', $ocrfile);

        if(file_exists($ocrfile)){
            $result = file_get_contents($ocrfile);
        }else{
            $this->load->library('ocr',array('file'=>$file));
            $result = $this->ocr->execute();
        }

        if($result == ''){
            print json_encode(array('status'=>'OK:EMPTY','result'=>$result));
        }else{
            print json_encode(array('status'=>'OK','result'=>$result));
        }
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
		$q = $this->input->post('term');
		$merchant_id = $this->input->post('merchant_id');
		$merchant_id = ($merchant_id == '')?null:$merchant_id;
		$merchants = ajax_find_buyer($q,'buyer_name','id',$merchant_id);
		print json_encode($merchants);
	}

	public function getbuyeremail(){
		$q = $this->input->get('term');
		$merchants = ajax_find_buyer_email($q,'fullname','id');
		print json_encode($merchants);
	}

    public function getbuyerphone(){
        $q = $this->input->get('term');
        $merchant_id = $this->input->post('merchant_id');
        $merchant_id = ($merchant_id == '')?null:$merchant_id;
        $merchants = ajax_find_phone($q,'phone','id',$merchant_id);
        print json_encode($merchants);
    }

    public function getbuyermobile1(){
        $q = $this->input->get('term');
        $merchant_id = $this->input->post('merchant_id');
        $merchant_id = ($merchant_id == '')?null:$merchant_id;
        $merchants = ajax_find_phone($q,'mobile1','id',$merchant_id);
        print json_encode($merchants);
    }

    public function getbuyermobile2(){
        $q = $this->input->get('term');
        $merchant_id = $this->input->post('merchant_id');
        $merchant_id = ($merchant_id == '')?null:$merchant_id;
        $merchants = ajax_find_phone($q,'mobile2','id',$merchant_id);
        print json_encode($merchants);
    }

	public function getdevice(){
		$q = $this->input->get('term');
		$zones = ajax_find_device($q,'identifier');
		print json_encode($zones);
	}

	public function rotatephoto(){
		$delivery_id = $this->input->post('delivery_id');
		$is_thumb = $this->input->post('is_thumb');

		$delivery_id = trim(str_replace('r_', '', $delivery_id));

		$this->load->library('image_lib');

		$config['image_library'] = 'imagemagick';
		$config['library_path'] = '/usr/bin/';
		$config['rotation_angle'] = '90';

		if($is_thumb == 1){
			$config['source_image']	= $this->config->item('thumbnail_path').'th_'.$delivery_id.'.jpg';
		}else{
			$config['source_image']	= $this->config->item('picture_path').$delivery_id.'.jpg';
		}

		chmod($config['source_image'],0777);

		$this->image_lib->initialize($config);

		if ( $this->image_lib->rotate())
		{
			$result = 'ok';
		}else{
			$result = 'err';
		}

		print json_encode(array('result'=>$result,'delivery_id'=>$delivery_id,'data'=>$config['source_image'].' '.$this->image_lib->display_errors()));
	}

    public function rotateaddressphoto(){
        $trx_id = $this->input->post('trx_id');
        $is_thumb = $this->input->post('is_thumb');

        $trx_id = trim(str_replace('r_', '', $trx_id));

        $this->load->library('image_lib');

        $config['image_library'] = 'imagemagick';
        $config['library_path'] = '/usr/bin/';
        $config['rotation_angle'] = '90';

        if($is_thumb == 1){
            $config['source_image'] = $this->config->item('thumbnail_path').'th_'.$trx_id.'.jpg';
        }else{
            $config['source_image'] = $this->config->item('pickuppic_path').$trx_id.'_address.jpg';
        }

        chmod($config['source_image'],0777);

        $this->image_lib->initialize($config);

        if ( $this->image_lib->rotate())
        {
            $result = 'ok';
        }else{
            $result = 'err';
        }

        $url = base_url().'public/pickup/'.$trx_id.'_address.jpg?'.time();

        print json_encode(array('result'=>$result,'trx_id'=>$trx_id,'url'=>$url,'data'=>$config['source_image'].' '.$this->image_lib->display_errors()));
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

	public function getmapmarker(){

		$device_name = $this->input->post('device_identifier');
		$timestamp = $this->input->post('timestamp');

		$device_name = ($device_name == 'Search device')?'':$device_name;
		$timestamp = ($timestamp == 'Search timestamp')?'':$timestamp;

		$this->db->distinct();
		$this->db->select('identifier');

		if($device_name != ''){
			$this->db->like('identifier',$device_name);
		}

		$devices = $this->db->get($this->config->item('location_log_table'))
			->result();

		$locations = array();

		$paths = array();

		foreach($devices as $d){

			$mapcolor = get_device_color($d->identifier);

			$this->db
				->select('identifier,timestamp,latitude as lat,longitude as lng')
				->where('identifier',$d->identifier);

			if($timestamp == ''){
				$this->db->like('timestamp',date('Y-m-d',time()),'after');
			}else{
				$this->db->like('timestamp',$timestamp,'after');
			}
				//->like('timestamp','2012-09-03','after')
				//->limit(10,0)
			$loc = $this->db
				->order_by('timestamp','desc')
				->get($this->config->item('location_log_table'));

			if($loc->num_rows() > 0){
				$path = array();
				$loc = $loc->result();
				foreach($loc as $l){
					$locations[] = array(
						'data'=>array(
								'lat'=>(double)$l->lat,
								'lng'=>(double)$l->lng,
								'timestamp'=>$l->timestamp,
								'identifier'=>$l->identifier
							)
						);
					$path[] = array(
							(double)$l->lat,
							(double)$l->lng
						);
				}
				$paths[]=array('color'=>$mapcolor,'poly'=>$path);
			}
		}

		print json_encode(array('result'=>'ok','locations'=>$locations,'paths'=>$paths));

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

	public function getpickupdata(){
		$app_key = $this->input->post('app_key');
		if($app_key == '0'){
			$dctable = false;
			$app_id = 0;
		}else{
			$app_id = get_app_id_from_key($app_key);
			$dctable = get_pickup_charge_table($app_id);
		}

		if($dctable == true){
			$weight[0] = 'Select weight range';
			foreach ($dctable as $r) {
				$weight[$r->total] = $r->kg_from.' kg - '.$r->kg_to.' kg';
				$this->table->add_row($r->kg_from.' kg - '.$r->kg_to.' kg', 'IDR '.number_format($r->total,2,',','.'));
			}
		}else{
			$dctable = get_pickup_charge_table(0);
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

    public function handover(){
        $prev_courier = $this->input->post('prev_courier');
        $new_courier = $this->input->post('new_courier');

        $prev = explode('_',$prev_courier);
        $date = $prev[0];
        $device_id = $prev[1];
        $prev_courier = $prev[2];

        $this->db->where('courier_id',$prev_courier);
        $this->db->where('assignment_date',$date);
        $this->db->where('device_id',$device_id);

        $dataset = array('courier_id'=>$new_courier);

        if($this->db->update($this->config->item('incoming_delivery_table'),$dataset) == TRUE){
            $result = json_encode(array('status'=>'OK:REASSIGNED','timestamp'=>now(),'new_courier_id'=>$new_courier,'courier_id'=>$prev_courier));
        }else{
            $result = json_encode(array('status'=>'ERR:NOREASSIGN','timestamp'=>now() ));
        }

        print $result;

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

    function reassignmulti(){
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

        $success = 0;
        foreach($delivery_id as $did){
            if($this->db->where('delivery_id',$did)->update($this->config->item('incoming_delivery_table'),$dataset) == TRUE){
                $success++;
            }
        }

        if($success > 0){
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
            'merchant_trans_id'=>$this->input->post('merchant_trans_id'),
			'directions'=>$this->input->post('direction'),
			'auto_confirm'=>$this->input->post('auto_confirm'),
			'email'=>$this->input->post('email'),
			'zip' => $this->input->post('zip'),
			'phone' => $this->input->post('phone'),
			'mobile1' => $this->input->post('mobile1'),
			'mobile2' => $this->input->post('mobile2'),
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
			'delivery_type' => $this->input->post('delivery_type'),
			'show_merchant' => $this->input->post('show_merchant'),
			'show_shop' => $this->input->post('show_shop'),
			'cod_bearer' => $this->input->post('bearer_cod'),
			'delivery_bearer' => $this->input->post('bearer_delivery'),
			'cod_method' => $this->input->post('cod_method'),
			'ccod_method' => $this->input->post('ccod_method')
		);

        if($trx['merchant_trans_id'] == '' || $trx['merchant_trans_id'] == '-'){
            $trx['transaction_id'] = 'TRX_'.$merchant_id.'_'.substr(str_replace(array(' ','.'), '', microtime()), 10 );
        }else{
            $trx['transaction_id'] = $trx['merchant_trans_id'];
        }

		$api_key = $this->input->post('api_key');
		$trx_id = $trx['transaction_id'];

        /*
        $result = $this->jexclient
                    ->base($this->config->item('api_url'))
                    ->endpoint('order')
                    ->addparam('key',$api_key)
                    ->addparam('trx',$trx_id)
                    ->data($trx)
                    ->setmethod('POST')
                    ->format('json')
                    ->send();
        */

        $trx = json_encode($trx);
        $result = $this->order_save($trx,$api_key,$trx_id);

        print $result;

	}

	public function toggle()
	{
		$field = $this->input->post('field');
		$id = $this->input->post('id');
		$setsw = $this->input->post('switchto');
		$toggle = ($setsw == 'On')?1:0;

		$dataset[$field] = $toggle;

		if($this->db->where('delivery_id',$id)->update($this->config->item('incoming_delivery_table'),$dataset) == TRUE){
			print json_encode(array('result'=>'ok','state'=>$setsw));
		}else{
			print json_encode(array('result'=>'failed'));
		}
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

    public function savedeliverybearer(){
        $delivery_id = $this->input->post('delivery_id');
        $delivery_bearer_type = $this->input->post('delivery_bearer_type');

        $newdata = array('delivery_bearer'=>$delivery_bearer_type);

        $this->db->where('delivery_id',$delivery_id)->update($this->config->item('incoming_delivery_table'),$newdata);

        if($this->db->affected_rows() > 0){
            print json_encode(array('status'=>'OK','delivery_bearer_type'=>$delivery_bearer_type));
        }else{
            print json_encode(array('status'=>'ERR','delivery_bearer_type'=>0));
        }

    }

    public function savecodbearer(){
        $delivery_id = $this->input->post('delivery_id');
        $cod_bearer_type = $this->input->post('cod_bearer_type');

        $newdata = array('cod_bearer'=>$cod_bearer_type);

        $this->db->where('delivery_id',$delivery_id)->update($this->config->item('incoming_delivery_table'),$newdata);

        if($this->db->affected_rows() > 0){
            print json_encode(array('status'=>'OK','cod_bearer_type'=>$cod_bearer_type));
        }else{
            print json_encode(array('status'=>'ERR','cod_bearer_type'=>0));
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

    public function setbuyergroup(){
        $parent = $this->input->post('parent');
        $children = $this->input->post('children');

        $parent = (int)$parent;

        $chdata = array('is_parent'=>0, 'is_child_of'=>$parent);
        $this->db->where_in('id',$children)->update($this->config->item('jayon_buyers_table'),$chdata);

        if(in_array($parent,$children)){
            $num_children = count($children) - 1;
        }else{
            $num_children = count($children);
        }

        $group_count = $this->db->from($this->config->item('jayon_buyers_table'))->where('is_child_of',$parent)->count_all_results();

        $group_count += 1;

        $pardata = array('is_parent'=>1, 'is_child_of'=>0, 'group_count'=>$group_count);
        $this->db->where('id',$parent)->update($this->config->item('jayon_buyers_table'),$pardata);

        if($num_children > 0){
            print json_encode(array('result'=>'OK','children'=>$num_children, 'group'=>$group_count ));
        }else{
            print json_encode(array('result'=>'ERR','delivery_type'=>0));
        }
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

    public function setbuyerloc(){
        $id = $this->input->post('id');
        $latitude = $this->input->post('latitude');
        $longitude = $this->input->post('longitude');
        $type = $this->input->post('type');

        $data = array(
                    'latitude'=>$latitude,
                    'longitude'=>$longitude,
                    'dir_lat'=>$latitude,
                    'dir_lon'=>$longitude
                );

        if($type == 'buyer'){
            $table = $this->config->item('jayon_buyers_table');
        }else{
            $table = $this->config->item('incoming_delivery_table');
        }

        $up = $this->db->where('id',$id)
            ->update($table,$data);

        if($up){
            print json_encode(array('result'=>'OK' ));
        }else{
            print json_encode(array('result'=>'ERR'));
        }
    }


    // worker functions

    public function order_save($indata,$api_key,$transaction_id)
    {
        $args = '';

        //$api_key = $this->get('key');
        //$transaction_id = $this->get('trx');

        if(is_null($api_key)){
            $result = json_encode(array('status'=>'ERR:NOKEY','timestamp'=>now()));
            return $result;
        }else{
            $app = $this->get_key_info(trim($api_key));

            if($app == false){
                $result = json_encode(array('status'=>'ERR:INVALIDKEY','timestamp'=>now()));
                return $result;
            }else{
                //$in = $this->input->post('transaction_detail');
                //$in = file_get_contents('php://input');
                $in = $indata;

                $buyer_id = 1;

                $args = 'p='.$in;

                $in = json_decode($in);

                $is_new = false;

                $in->phone = ( isset( $in->phone ) && $in->phone != '')?normalphone( $in->phone ):'';
                $in->mobile1 = ( isset( $in->mobile1 ) && $in->mobile1 != '' )?normalphone( $in->mobile1 ):'';
                $in->mobile2 = ( isset( $in->mobile2 ) && $in->mobile2 != '' )?normalphone( $in->mobile2 ):'';


                if(isset($in->buyer_id) && $in->buyer_id != '' && $in->buyer_id > 1){

                    $buyer_id = $in->buyer_id;
                    $is_new = false;

                }else{

                    if($in->email == '' || $in->email == '-' || !isset($in->email) || $in->email == 'noemail'){

                        $in->email = 'noemail';
                        $is_new = true;
                        if( trim($in->phone.$in->mobile1.$in->mobile2) != ''){
                            if($buyer = $this->check_phone($in->phone,$in->mobile1,$in->mobile2)){
                                $buyer_id = $buyer['id'];
                                $is_new = false;
                            }
                        }

                    }else if($buyer = $this->check_email($in->email)){

                        $buyer_id = $buyer['id'];
                        $is_new = false;

                    }else if($buyer = $this->check_phone($in->phone,$in->mobile1,$in->mobile2)){

                        $buyer_id = $buyer['id'];
                        $is_new = false;

                    }

                }

                if(isset($in->transaction_id) && $in->transaction_id != ""){
                    $transaction_id = $in->transaction_id;
                }


                if($is_new){
                    $buyer_username = substr(strtolower(str_replace(' ','',$in->buyer_name)),0,6).random_string('numeric', 4);
                    $dataset['username'] = $buyer_username;
                    $dataset['email'] = $in->email;
                    $dataset['phone'] = $in->phone;
                    $dataset['mobile1'] = $in->mobile1;
                    $dataset['mobile2'] = $in->mobile2;
                    $dataset['fullname'] = $in->buyer_name;
                    $password = random_string('alnum', 8);
                    $dataset['password'] = $this->ag_auth->salt($password);
                    $dataset['created'] = date('Y-m-d H:i:s',time());

                    /*
                    $dataset['province'] =
                    $dataset['mobile']
                    */

                    $dataset['street'] = $in->shipping_address;
                    $dataset['district'] = $in->buyerdeliveryzone;
                    $dataset['city'] = $in->buyerdeliverycity;
                    $dataset['country'] = 'Indonesia';
                    $dataset['zip'] = $in->zip;

                    //$buyer_id = $this->register_buyer($dataset);
                    $is_new = true;
                }

                $order['created'] = date('Y-m-d H:i:s',time());
                $order['ordertime'] = date('Y-m-d H:i:s',time());
                $order['pickuptime'] = date('Y-m-d H:i:s',time());
                $order['application_id'] = $app->id;
                $order['application_key'] = $app->key;
                $order['buyer_id'] = $buyer_id;
                $order['merchant_id'] = $app->merchant_id;
                $order['merchant_trans_id'] = trim($transaction_id);

                $order['buyer_name'] = $in->buyer_name;
                $order['recipient_name'] = $in->recipient_name;
                $order['email'] = $in->email;
                $order['directions'] = $in->directions;
                //$order['dir_lat'] = $in->dir_lat;
                //$order['dir_lon'] = $in->dir_lon;
                $order['buyerdeliverytime'] = $in->buyerdeliverytime;
                $order['buyerdeliveryslot'] = $in->buyerdeliveryslot;
                $order['buyerdeliveryzone'] = $in->buyerdeliveryzone;
                $order['buyerdeliverycity'] = (is_null($in->buyerdeliverycity) || $in->buyerdeliverycity == '')?'Jakarta':$in->buyerdeliverycity;

                $order['currency'] = $in->currency;
                $order['total_price'] = (isset($in->total_price))?$in->total_price:0;
                $order['total_discount'] = (isset($in->total_discount))?$in->total_discount:0;
                $order['total_tax'] = (isset($in->total_tax))?$in->total_tax:0;
                $order['cod_cost'] = $in->cod_cost;
                $order['chargeable_amount'] = (isset($in->chargeable_amount))?$in->chargeable_amount:0;

                $order['shipping_address'] = $in->shipping_address;
                $order['shipping_zip'] = $in->zip;
                $order['phone'] = $in->phone;
                $order['mobile1'] = $in->mobile1;
                $order['mobile2'] = $in->mobile2;
                $order['status'] = $in->status;

                $order['width'] = $in->width;
                $order['height'] = $in->height;
                $order['length'] = $in->length;
                $order['weight'] = (isset($in->weight))?$in->weight:0;
                $order['delivery_type'] = $in->delivery_type;
                $order['delivery_cost'] = (isset($in->delivery_cost))?$in->delivery_cost:0;

                $order['cod_bearer'] = (isset($in->cod_bearer))?$in->cod_bearer:'merchant';
                $order['delivery_bearer'] = (isset($in->delivery_bearer))?$in->delivery_bearer:'merchant';

                $order['cod_method'] = (isset($in->cod_method))?$in->cod_method:'cash';
                $order['ccod_method'] = (isset($in->ccod_method))?$in->ccod_method:'full';

                if(isset($in->show_shop)){
                    $order['show_shop'] = $in->show_shop;
                }

                if(isset($in->show_merchant)){
                    $order['show_merchant'] = $in->show_merchant;
                }

                $inres = $this->db->insert($this->config->item('incoming_delivery_table'),$order);
                $sequence = $this->db->insert_id();

                $delivery_id = get_delivery_id($sequence,$app->merchant_id);

                $nedata['fullname'] = $in->buyer_name;
                $nedata['merchant_trx_id'] = trim($transaction_id);
                $nedata['delivery_id'] = $delivery_id;
                $nedata['merchantname'] = $app->application_name;
                $nedata['app'] = $app;

                $order['delivery_id'] = $delivery_id;

                $this->save_buyer($order);

                $this->db->where('id',$sequence)->update($this->config->item('incoming_delivery_table'),array('delivery_id'=>$delivery_id));

                    $this->table_tpl = array(
                        'table_open' => '<table border="0" cellpadding="4" cellspacing="0" class="dataTable">'
                    );
                    $this->table->set_template($this->table_tpl);


                    $this->table->set_heading(
                        'No.',
                        'Description',
                        'Quantity',
                        'Total'
                        ); // Setting headings for the table

                    $d = 0;
                    $gt = 0;


                if($in->trx_detail){
                    $seq = 0;

                    foreach($in->trx_detail as $it){
                        $item['ordertime'] = $order['ordertime'];
                        $item['delivery_id'] = $delivery_id;
                        $item['unit_sequence'] = $seq++;
                        $item['unit_description'] = $it->unit_description;
                        $item['unit_price'] = $it->unit_price;
                        $item['unit_quantity'] = $it->unit_quantity;
                        $item['unit_total'] = $it->unit_total;
                        $item['unit_discount'] = $it->unit_discount;

                        $rs = $this->db->insert($this->config->item('delivery_details_table'),$item);

                        $this->table->add_row(
                            (int)$item['unit_sequence'] + 1,
                            $item['unit_description'],
                            $item['unit_quantity'],
                            $item['unit_total']
                        );

                        $u_total = str_replace(array(',','.'), '', $item['unit_total']);
                        $u_discount = str_replace(array(',','.'), '', $item['unit_discount']);
                        $gt += (int)$u_total;
                        $d += (int)$u_discount;

                    }

                    $total = (isset($in->total_price) && $in->total_price > 0)?$in->total_price:0;
                    $total = str_replace(array(',','.'), '', $total);
                    $total = (int)$total;
                    $gt = ($total < $gt)?$gt:$total;

                    $disc = (isset($in->total_discount))?$in->total_discount:0;
                    $tax = (isset($in->total_tax))?$in->total_tax:0;
                    $cod = (isset($in->cod_cost))?$in->cod_cost:'Paid by merchant';

                    $disc = str_replace(array(',','.'), '', $disc);
                    $tax = str_replace(array(',','.'), '',$tax);
                    $cod = str_replace(array(',','.'), '',$cod);

                    $disc = (int)$disc;
                    $tax = (int)$tax;
                    $cod = (int)$cod;

                    $chg = ($gt - $disc) + $tax + $cod;

                    $this->table->add_row(
                        '',
                        '',
                        'Total Price',
                        number_format($gt,2,',','.')
                    );

                    $this->table->add_row(
                        '',
                        '',
                        'Total Discount',
                        number_format($disc,2,',','.')
                    );

                    $this->table->add_row(
                        '',
                        '',
                        'Total Tax',
                        number_format($tax,2,',','.')
                    );


                    if($cod == 0){
                        $this->table->add_row(
                            '',
                            '',
                            'COD Charges',
                            'Paid by Merchant'
                        );
                    }else{
                        $this->table->add_row(
                            '',
                            '',
                            'COD Charges',
                            number_format($cod,2,',','.')
                        );
                    }


                    $this->table->add_row(
                        '',
                        '',
                        'Total Charges',
                        number_format($chg,2,',','.')
                    );

                    $nedata['detail'] = $this->table;

                    $result = json_encode(array('status'=>'OK:ORDERPOSTED','timestamp'=>now(),'delivery_id'=>$delivery_id,'buyer_id'=>$buyer_id));

                    try{

                        if($app->notify_on_new_order == 1){
                            if(valid_email($in->email)){
                                send_notification('New Delivery Order - Jayon Express COD Service',$in->email,$app->cc_to,$app->reply_to,'order_processed',$nedata,null);
                            }
                        }

                        if($is_new == true){
                            $edata['fullname'] = $dataset['fullname'];
                            $edata['username'] = $buyer_username;
                            $edata['password'] = $password;
                            if($app->notify_on_new_member == 1 && $in->email != 'noemail'){
                                send_notification('New Member Registration - Jayon Express COD Service',$in->email,null,null,'new_member',$edata,null);
                            }

                        }

                    }catch(Exception $e){

                    }

                    $this->log_access($api_key, __METHOD__ ,$result,$args);

                    return $result;
                }else{
                    $nedata['detail'] = false;

                    $result = json_encode(array('status'=>'OK:ORDERPOSTEDNODETAIL','timestamp'=>now(),'delivery_id'=>$delivery_id));

                    return $result;
                }

                //print_r($app);

                if($app->notify_on_new_order == 1){
                    //send_notification('New Delivery Order - Jayon Express COD Service',$in->email,$app->cc_to,$app->reply_to,'order_submit',$nedata,null);
                }

                if($is_new == true){
                    $edata['fullname'] = $dataset['fullname'];
                    $edata['username'] = $buyer_username;
                    $edata['password'] = $password;
                    if($app->notify_on_new_member == 1 && $in->email != 'noemail'){
                        send_notification('New Member Registration - Jayon Express COD Service',$in->email,null,null,'new_member',$edata,null);
                    }

                }

            }
        }

        $this->log_access($api_key, __METHOD__ ,$result,$args);
    }

    //private supporting functions

    private function get_key_info($key){
        if(!is_null($key)){
            $this->db->where('key',$key);
            $result = $this->db->get($this->config->item('applications_table'));
            if($result->num_rows() > 0){
                $row = $result->row();
                return $row;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    private function get_dev_info($key){
        if(!is_null($key)){
            $this->db->where('key',$key);
            $result = $this->db->get($this->config->item('jayon_devices_table'));
            if($result->num_rows() > 0){
                $row = $result->row();
                return $row;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    private function get_dev_info_by_id($identifier){
        if(!is_null($identifier)){
            $this->db->where('identifier',$identifier);
            $result = $this->db->get($this->config->item('jayon_devices_table'));
            if($result->num_rows() > 0){
                $row = $result->row();
                return $row;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }


    private function check_email($email){
        $em = $this->db->where('email',$email)->get($this->config->item('jayon_members_table'));
        if($em->num_rows() > 0){
            return $em->row_array();
        }else{
            return false;
        }
    }

    private function check_phone($phone, $mobile1, $mobile2){
        $em = $this->db->like('phone',$phone)
                ->or_like('mobile1',$mobile1)
                ->or_like('mobile2',$mobile2)
                ->get($this->config->item('jayon_members_table'));
        if($em->num_rows() > 0){
            return $em->row_array();
        }else{
            return false;
        }
    }


    private function register_buyer($dataset){
        $dataset['group_id'] = 5;

        if($this->db->insert($this->config->item('jayon_members_table'),$dataset)){
            return $this->db->insert_id();
        }else{
            return 0;
        }
    }


    private function save_buyer($ds){

        if(isset($ds['buyer_id']) && $ds['buyer_id'] != '' && $ds['buyer_id'] > 1){
            if($pid = $this->get_parent_buyer($ds['buyer_id'])){
                $bd['is_child_of'] = $pid;
                $this->update_group_count($pid);
            }
        }

        $bd['buyer_name']  =  $ds['buyer_name'];
        $bd['buyerdeliveryzone']  =  $ds['buyerdeliveryzone'];
        $bd['buyerdeliverycity']  =  $ds['buyerdeliverycity'];
        $bd['shipping_address']  =  $ds['shipping_address'];
        $bd['phone']  =  $ds['phone'];
        $bd['mobile1']  =  $ds['mobile1'];
        $bd['mobile2']  =  $ds['mobile2'];
        $bd['recipient_name']  =  $ds['recipient_name'];
        $bd['shipping_zip']  =  $ds['shipping_zip'];
        $bd['email']  =  $ds['email'];
        $bd['delivery_id']  =  $ds['delivery_id'];
        $bd['delivery_cost']  =  $ds['delivery_cost'];
        $bd['cod_cost']  =  $ds['cod_cost'];
        $bd['delivery_type']  =  $ds['delivery_type'];
        $bd['currency']  =  $ds['currency'];
        $bd['total_price']  =  $ds['total_price'];
        $bd['chargeable_amount']  =  $ds['chargeable_amount'];
        $bd['delivery_bearer']  =  $ds['delivery_bearer'];
        $bd['cod_bearer']  =  $ds['cod_bearer'];
        $bd['cod_method']  =  $ds['cod_method'];
        $bd['ccod_method']  =  $ds['ccod_method'];
        $bd['application_id']  =  $ds['application_id'];
        //$bd['buyer_id']  =  $ds['buyer_id'];
        $bd['merchant_id']  =  $ds['merchant_id'];
        $bd['merchant_trans_id']  =  $ds['merchant_trans_id'];
        //$bd['courier_id']  =  $ds['courier_id'];
        //$bd['device_id']  =  $ds['device_id'];
        $bd['directions']  =  $ds['directions'];
        //$bd['dir_lat']  =  $ds['dir_lat'];
        //$bd['dir_lon']  =  $ds['dir_lon'];
        //$bd['delivery_note']  =  $ds['delivery_note'];
        //$bd['latitude']  =  $ds['latitude'];
        //$bd['longitude']  =  $ds['longitude'];
        $bd['created']  =  $ds['created'];

        $bd['cluster_id'] = substr(md5(uniqid(rand(), true)), 0, 20 );

        if($this->db->insert($this->config->item('jayon_buyers_table'),$bd)){
            return $this->db->insert_id();
        }else{
            return 0;
        }
    }

    private function get_parent_buyer($id){
        $this->db->where('id',$id);
        $by = $this->db->get($this->config->item('jayon_buyers_table'));

        if($by->num_rows() > 0){

            $buyer = $by->row_array();
            if($buyer['is_parent'] == 1){
                $pid = $buyer['id'];
            }elseif($buyer['is_child_of'] > 0 && $buyer['is_parent'] == 0){
                $pid = $buyer['is_child_of'];
            }else{
                $pid = false;
            }

            return $pid;

        }else{
            return false;
        }

    }

    private function update_group_count($id){

        $this->db->where('is_child_of',$id);
        $groupcount = $this->db->count_all_results($this->config->item('jayon_buyers_table'));

        $dataup = array('group_count'=>($groupcount + 1) );

        $this->db->where('id',$id);

        if($res = $this->db->update($this->config->item('jayon_buyers_table'),$dataup) ){
            return $res;
        }else{
            return false;
        }

    }

    private function get_device($key){
        $dev = $this->db->where('key',$key)->get($this->config->item('jayon_mobile_table'));
        print_r($dev);
        print $this->db->last_query();
        return $dev->row_array();
    }

    private function get_group(){
        $this->db->select('id,description');
        $result = $this->db->get($this->ag_auth->config['auth_group_table']);
        foreach($result->result_array() as $row){
            $res[$row['id']] = $row['description'];
        }
        return $res;
    }

    private function log_access($api_key,$query,$result,$args = null){
        $data['timestamp'] = date('Y-m-d H:i:s',time());
        $data['accessor_ip'] = $this->accessor_ip;
        $data['api_key'] = (is_null($api_key))?'':$api_key;
        $data['query'] = $query;
        $data['result'] = $result;
        $data['args'] = (is_null($args))?'':$args;

        access_log($data);
    }

    private function admin_auth($username = null,$password = null){
        if(is_null($username) || is_null($password)){
            return false;
        }

        $password = $this->ag_auth->salt($password);
        $result = $this->db->where('username',$username)->where('password',$password)->get($this->ag_auth->config['auth_user_table']);

        if($result->num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }



}

/* End of file: buy.php */
/* Location: application/controllers/admin/dashboard.php */