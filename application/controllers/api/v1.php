<?php

class V1 extends Application
{

	public function __construct()
	{
		parent::__construct();
		//$this->ag_auth->restrict('admin'); // restrict this controller to admins only

		$this->accessor_ip = $_SERVER['REMOTE_ADDR'];
	}

	/**
	*	transaction posting function
	*	required field is buyer email, since this function will try to extract it to check and authenticate
	*	whether buyer already registered at JEX or not
	*	if not yet registered, buyer will be automatically registered as new member and notified about new membership via email
	*/

	public function post($api_key = null,$transaction_id = null)
	{
		$args = '';

		if(is_null($api_key)){
			$result = json_encode(array('status'=>'ERR:NOKEY','timestamp'=>now()));
			print $result;
		}else{
			$app = $this->get_key_info(trim($api_key));

			if($app == false){
				$result = json_encode(array('status'=>'ERR:INVALIDKEY','timestamp'=>now()));
				print $result;
			}else{
				$in = $this->input->post('transaction_detail');

				$args = 'p='.$in;

				$in = json_decode($in);

				$buyer_id = 1;

				$is_new = false;
				if($buyer = $this->check_email($in->email)){
					$buyer_id = $buyer['id'];
					$is_new = false;
				}else{
					$buyer_username = substr(strtolower(str_replace(' ','',$in->buyer_name)),0,6).random_string('numeric', 4);
					$dataset['username'] = $buyer_username;
					$dataset['email'] = $in->email;
					$dataset['fullname'] = $in->buyer_name;
					$password = random_string('alnum', 8);
					$dataset['password'] = $this->ag_auth->salt($password);
					$dataset['created'] = date('Y-m-d h:i:s',time());
					$buyer_id = $this->register_buyer($dataset);
					$is_new = true;
				}
				$order['created'] = date('Y-m-d h:i:s',time());
				$order['ordertime'] = date('Y-m-d h:i:s',time());
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
				$order['buyerdeliveryzone'] = $in->buyerdeliveryzone;
				$order['buyerdeliverycity'] = $in->buyerdeliverycity;

				$order['currency'] = $in->currency;
				$order['total_price'] = (isset($in->total_price))?$in->total_price:0;
				$order['total_discount'] = (isset($in->total_discount))?$in->total_discount:0;
				$order['total_tax'] = (isset($in->total_tax))?$in->total_tax:0;
				$order['cod_cost'] = $in->cod_cost;
				$order['chargeable_amount'] = (isset($in->chargeable_amount))?$in->chargeable_amount:0;

				$order['shipping_address'] = $in->shipping_address;
				$order['shipping_zip'] = $in->zip;
				$order['phone'] = $in->phone;
				$order['status'] = $in->status;
				
				$inres = $this->db->insert($this->config->item('incoming_delivery_table'),$order);
				$sequence = $this->db->insert_id();

				$delivery_id = get_delivery_id($sequence,$app->merchant_id);

				$nedata['fullname'] = $in->buyer_name;
				$nedata['merchant_trx_id'] = trim($transaction_id);
				$nedata['delivery_id'] = $delivery_id;
				$nedata['merchantname'] = $app->application_name;
				$nedata['app'] = $app;

				$this->db->where('id',$sequence)->update($this->config->item('incoming_delivery_table'),array('delivery_id'=>$delivery_id));

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
						$item['unit_total']	= $it->unit_total;
						$item['unit_discount'] = $it->unit_discount;

						$rs = $this->db->insert($this->config->item('delivery_details_table'),$item);

						$this->table->add_row(
							(int)$item['unit_sequence'] + 1,		 	 	
							$item['unit_description'],	 	 	 	 	 	 	 
							$item['unit_quantity'],		
							$item['unit_total']			
						);

						$gt += $item['unit_total'];
						$d += $item['unit_discount'];

					}

					$this->table->add_row(
						'',		
						'',		
						'Total',		
						$gt
					);

					$nedata['detail'] = $this->table;

					$result = json_encode(array('status'=>'OK:ORDERPOSTED','timestamp'=>now(),'delivery_id'=>$delivery_id,'buyer_id'=>$buyer_id));

					print $result;
				}else{
					$nedata['detail'] = false;

					$result = json_encode(array('status'=>'OK:ORDERPOSTEDNODETAIL','timestamp'=>now(),'delivery_id'=>$delivery_id));

					print $result;
				}

				if($app->notify_on_new_order == 1){
					send_notification('New Delivery Order - Jayon Express COD Service',$in->email,null,null,'order_submit',$nedata,null);
				}
				
				if($is_new == true){
					$edata['fullname'] = $dataset['fullname'];
					$edata['username'] = $buyer_username;
					$edata['password'] = $password;
					if($app->notify_on_new_member == 1){
						send_notification('New Member Registration - Jayon Express COD Service',$in->email,null,null,'new_member',$edata,null);
					}

				}

			}
		}

		$this->log_access($api_key, __METHOD__ ,$result,$args);
	}

	/* Check & get particular timeslot for current date  */

	public function tsget($api_key = null,$month = null,$city = null){
		if(is_null($api_key)){
			$result = json_encode(array('status'=>'ERR:NOKEY','timestamp'=>now()));
			print $result;
		}else{
			if(is_null($month)){
				//get slot for specified date
				$month = date('m',time());
				$dateblock = getdateblock($month);
				$result = json_encode(array('status'=>'OK:CURRENTDATE','timestamp'=>now(),'timeslot'=>$dateblock));
				print $result;

			}else{
				//full calendar time series for current month
				$dateblock = getdateblock($month);
				$result = json_encode(array('status'=>'OK:CURRENTMONTH','timestamp'=>now(),'timeslot'=>$dateblock));
				print $result;
			}
		}
		$args = 'q='.$month;
		$this->log_access($api_key, __METHOD__ ,$result);
	}

	/* Check & get particular timeslot for current date  */

	public function tscheck($api_key = null,$date = null,$city = null){
		if(is_null($api_key)){
			$result = json_encode(array('status'=>'ERR:NOKEY','timestamp'=>now()));
			print $result;
		}else{
			if(is_null($date)){
				//get slot for specified date
				$month = date('m',time());
				$dateblock = getdateblock($month);
				$result = json_encode(array('status'=>'OK:CURRENTMONTH','timestamp'=>now(),'timeslot'=>$dateblock));
				print $result;
			}else{
				//full calendar time series for current month
				$dateblock = checkdateblock($date, $city);
				$result = json_encode(array('status'=>'OK:CURRENTDATE','timestamp'=>now(),'timeslot'=>$dateblock));
				print $result;
			}
		}
		$args = 'q='.$date;
		$this->log_access($api_key, __METHOD__ ,$result,$args);
	}

	/* Update current location */

	public function locpost($api_key = null){
		if(is_null($api_key)){
			$result = json_encode(array('status'=>'ERR:NOKEY','timestamp'=>now()));
			print $result;
		}else{

			if(isset($_POST['loc'])){
				$in = json_decode($_POST['loc']);
				//file_put_contents('/Applications/XAMPP/htdocs/jayonadmin/public/locreportval.txt', $in->key);

				if($dev = $this->get_dev_info($in->key)){

					$dataset['timestamp'] = date('Y-m-d h:i:s',time());
					$dataset['device_id'] = $dev->id;
					$dataset['identifier'] = $dev->identifier;
					$dataset['courier_id'] = '';
					$dataset['latitude'] = $in->lat;
					$dataset['longitude'] = $in->lon;
					$dataset['status'] = $this->config->item('trans_status_mobile_location');
					$dataset['notes'] = $in->note;


					$this->db->insert($this->config->item('location_log_table'),$dataset);

					//get slot for specified date
					$result = json_encode(array('status'=>'OK:LOCPOSTED','timestamp'=>now()));
					print $result;
				}else{
					$result = json_encode(array('status'=>'NOK:DEVICENOTFOUND','timestamp'=>now()));
					print $result;

				}

			}else{
				//full calendar time series for current month
				$result = json_encode(array('status'=>'NOK:LOCFAILED','timestamp'=>now()));
				print $result;
			}
		}

		$this->log_access($api_key, __METHOD__ ,$result);

	}

	/* Update delivery status from mobile */

	public function statpost($api_key = null){
		if(is_null($api_key)){
			$result = json_encode(array('status'=>'ERR:NOKEY','timestamp'=>now()));
			print $result;
		}else{

			if(isset($_POST['trx'])){

				//file_put_contents('posted_status.txt', $_POST['trx'] );

				$in = json_decode($_POST['trx']);

				if($dev = $this->get_dev_info($in->key)){

					$dataset['status'] = $in->status;

					//other action for different status migh be needed

					$this->db->where('delivery_id',$in->delivery_id)->update($this->config->item('assigned_delivery_table'),$dataset);

					$data = array(
						'timestamp'=>date('Y-m-d h:i:s',time()),
						'report_timestamp'=>date('Y-m-d h:i:s',time()),
						'delivery_id'=>$in->delivery_id,
						'device_id'=>$dev->id,
						'courier_id'=>'',
						'actor_type'=>'MB',
						'actor_id'=>'',
						'latitude'=>$in->lat,
						'longitude'=>$in->lon,
						'status'=>$in->status,
						'notes'=>$in->note
					);

					delivery_log($data);
					$result = json_encode(array('status'=>'OK:STATPOSTED','timestamp'=>now()));
					print $result;
				}else{
					$result = json_encode(array('status'=>'NOK:DEVICENOTFOUND','timestamp'=>now()));
					print $result;
				}

			}else{
				//full calendar time series for current month
				$result = json_encode(array('status'=>'NOK:STATFAILED','timestamp'=>now()));
				print $result;
			}
		}

		$this->log_access($api_key, __METHOD__ ,$result);
	}

	/* Synchronize mobile device */
	public function syncreport($api_key = null){
		if(is_null($api_key)){
			$result = json_encode(array('status'=>'ERR:NOKEY','timestamp'=>now()));
			print $result;
		}else{

			//sync steps :
			//post stored data from device local db
			//retrieve relevant data for next delivery assignment

			//sync in

			//sync out
			if($dev = $this->get_dev_info($api_key)){

				if(isset($_POST['trx'])){
					$in = json_decode($_POST['trx']);

					//set status based on reported

					$out = $orders->result_array();

					$data = array(
						'timestamp'=>date('Y-m-d h:i:s',time()),
						'report_timestamp'=>date('Y-m-d h:i:s',time()),
						'delivery_id'=>'',
						'device_id'=>$dev->id,
						'courier_id'=>'',
						'actor_type'=>'MB',
						'actor_id'=>$dev->id,
						'latitude'=>'',
						'longitude'=>'',
						'status'=>'sync_report',
						'notes'=>''
					);

					delivery_log($data);

					//get slot for specified date
					$result = json_encode(array('status'=>'OK:DEVSYNC','data'=>$out ,'timestamp'=>now()));
					print $result;
				}else{
					$result = json_encode(array('status'=>'ERR:NODATA','timestamp'=>now()));
					print $result;
				}
			}else{
				$result = json_encode(array('status'=>'NOK:DEVICENOTFOUND','timestamp'=>now()));
				print $result;
			}
		}

		$this->log_access($api_key, __METHOD__ ,$result);
	}

	public function syncdata($api_key = null,$indate = null){
		if(is_null($api_key)){
			$result = json_encode(array('status'=>'ERR:NOKEY','timestamp'=>now()));
			print $result;
		}else{

			if($dev = $this->get_dev_info($api_key)){

				$indate = (is_null($indate))?date('Y-m-d',time()):$indate;

				$orders = $this->db
					->select('d.delivery_id as delivery_id,
							d.assignment_date as as_date,
							d.assignment_timeslot as as_timeslot,
							d.assignment_zone as as_zone,
							d.assignment_city as as_city,
							m.merchantname as mc_name,
							m.street as mc_street,
							m.district as mc_district,
							m.province as mc_province,
							m.city as mc_city,
							d.merchant_trans_id as mc_trans_id,
							d.buyerdeliverytime as by_time,
							d.buyerdeliveryzone as by_zone,
							d.buyerdeliverycity as by_city,
							d.buyer_name as by_name,
							d.email as by_email,
							d.phone as by_phone,
							d.recipient_name as rec_name,
							d.undersign as rec_sign,
							d.cod_cost as cod_cost,
							d.currency as cod_curr,
							d.shipping_address as ship_addr,
							d.directions as ship_dir,
							d.dir_lat as ship_lat,
							d.dir_lon as ship_lon,
							d.deliverytime as dl_time,
							d.status as dl_status,
							d.delivery_note as dl_note,
							d.latitude as dl_lat,
							d.longitude as dl_lon,
							d.reschedule_ref as res_ref,
							d.revoke_ref as rev_ref')
					->from($this->config->item('assigned_delivery_table').' as d')
					->join('members as m','d.merchant_id=m.id','left')
					->where('assignment_date',$indate)
					->where('device_id',$dev->id)
					->get();

				$out = $orders->result_array();

				$data = array(
					'timestamp'=>date('Y-m-d h:i:s',time()),
					'report_timestamp'=>date('Y-m-d h:i:s',time()),
					'delivery_id'=>'',
					'device_id'=>$dev->id,
					'actor_type'=>'MB',
					'actor_id'=>$dev->id,
					'status'=>'sync_data'
				);

				delivery_log($data);

				//get slot for specified date
				$result = json_encode(array('status'=>'OK:DEVSYNC','data'=>$out ,'timestamp'=>now()));
				print $result;
			}else{
				$result = json_encode(array('status'=>'NOK:DEVICENOTFOUND','timestamp'=>now()));
				print $result;
			}
		}

		$this->log_access($api_key, __METHOD__ ,$result);
	}

	/* Lists JEX zones of coverage */

	public function zonelist($api_key = null){
		if(is_null($api_key)){
			$result = json_encode(array('status'=>'ERR:NOKEY','timestamp'=>now()));
			print $result;
		}else{
			$z = $this->db->get($this->config->item('jayon_zones_table'));
			$zones = $z->result_array();
			$result = json_encode(array('status'=>'OK:ZONEOUT','data'=>$zones,'timestamp'=>now()));
			print $result;
		}

		$this->log_access($api_key, __METHOD__ ,$result);

	}

	/* Lists JEX zones of coverage, as results of <query string> matches */

	public function zoneget($api_key = null, $query = null){
		if(is_null($api_key)){
			$result = json_encode(array('status'=>'ERR:NOKEY','timestamp'=>now()));
			print $result;
		}else{
			$z = $this->db
				->like('district',$query)
				->or_like('city',$query)
				->get($this->config->item('jayon_zones_table'));
			$zones = $z->result_array();
			$result = json_encode(array('status'=>'OK:ZONEOUT','data'=>$zones,'timestamp'=>now()));
			print $result;
		}
		$args = 'q='.$query;
		$this->log_access($api_key, __METHOD__ ,$result,$args);

	}

	public function add($api_key = null,$transaction_id = null)
	{

		if(is_null($api_key)){
			$result = json_encode(array('status'=>'ERR:NOKEY','timestamp'=>now()));
			print $result;
		}else{
			$app = $this->get_key_info(trim($api_key));

			if($app){
				if($in = $this->input->post('transaction_detail')){
					$in = json_decode($in);

					//print_r($in);

					//check if email already registered
					if($buyer = $this->check_email($order['email'])){
						$buyer_id = $buyer['id'];
						print 'existing buyer';
					}else{
						$buyer_username = substr(strtolower(str_replace(' ','',$order['buyer_name'])),0,6).random_string('num', 4);
						$dataset['username'] = $buyer_username;
						$dataset['email'] = $order['email'];
						$dataset['fullname'] = $order['buyer_name'];
						$dataset['password'] = $this->ag_auth->salt(random_string('alnum', 8));
						$buyer_id = $this->register_buyer($dataset);
						print 'new buyer';
					}

					//print $buyer_id;


					$order['ordertime'] = date('Y-m-d h:i:s',time());
					$order['application_id'] = $app->id;
					$order['application_key'] = $app->key;
					$order['buyer_id'] = $buyer_id; // change this to current buyer after login
					$order['merchant_id'] = $app->merchant_id;
					$order['merchant_trans_id'] = trim($transaction_id);

					$order['buyer_name'] = $in->buyer_name;
					$order['recipient_name'] = $in->recipient_name;
					$order['email'] = $in->email;
					$order['buyerdeliverytime'] = $in->buyerdeliverytime;
					$order['buyerdeliveryzone'] = $in->buyerdeliveryzone;
					$order['currency'] = $in->currency;
					$order['cod_cost'] = $in->cod_cost;

					$order['shipping_address'] = $in->shipping_address;
					$order['phone'] = $in->phone;
					$order['status'] = $in->status;

					$this->db->insert($this->config->item('incoming_delivery_table'),$order);
					$sequence = $this->db->insert_id();

					$result = $this->db->affected_rows();

					$year_count = str_pad($sequence, 10, '0', STR_PAD_LEFT);
					$merchant_id = str_pad($app->merchant_id, 8, '0', STR_PAD_LEFT);
					$delivery_id = $merchant_id.'-'.date('d-mY',time()).'-'.$year_count;

					$this->db->where('id',$sequence)->update($this->config->item('incoming_delivery_table'),array('delivery_id'=>$delivery_id));

					if($in->trx_detail){
						$seq = 0;
						foreach($in->trx_detail as $it){
							$item['ordertime'] = $order['ordertime'];
							$item['delivery_id'] = $delivery_id;
							$item['unit_sequence'] = $seq++;
							$item['unit_description'] = $it->unit_description;
							$item['unit_price'] = $it->unit_price;
							$item['unit_quantity'] = $it->unit_quantity;
							$item['unit_total']	= $it->unit_total;
							$item['unit_discount'] = $it->unit_discount;

							$rs = $this->db->insert($this->config->item('delivery_details_table'),$item);
						}

					}
					$result = json_encode(array('status'=>'OK:ORDERPOSTED','timestamp'=>now(),'delivery_id'=>$delivery_id));
					print $result;
				}else{
					$result = json_encode(array('status'=>'ERR:NODETAIL','timestamp'=>now()));
					print $result;
				}
			}else{
				$result = json_encode(array('status'=>'ERR:NOKEYFOUND','timestamp'=>now()));
				print $result;
			}

		}

		$this->log_access($api_key, __METHOD__ ,$result);
	} // public function add() transaction


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


	private function check_email($email){
		$em = $this->db->where('email',$email)->get($this->config->item('jayon_members_table'));
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
		$data['timestamp'] = date('Y-m-d h:i:s',time());
		$data['accessor_ip'] = $this->accessor_ip;
		$data['api_key'] = (is_null($api_key))?'':$api_key;
		$data['query'] = $query;
		$data['result'] = $result;
		$data['args'] = (is_null($args))?'':$args;

		access_log($data);
	}
}

?>