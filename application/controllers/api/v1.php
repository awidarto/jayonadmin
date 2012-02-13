<?php

class V1 extends Application
{

	public function __construct()
	{
		parent::__construct();
		//$this->ag_auth->restrict('admin'); // restrict this controller to admins only
	}

	public function items()
	{
		$this->load->library('table');
		$data = $this->db->get($this->config->item('incoming_delivery_table'));
		$result = $data->result_array();

		$this->table->set_heading(
			'Delivery ID',
			'Application ID',
			'Buyer',
			'Merchant',
			'Merchant Trans ID',
			'Courier',
			'Shipping Address',
			'Phone',
			'Status',
			'Reschedule Ref',
			'Revoke Ref',
			'Actions'
			); // Setting headings for the table

		foreach($result as $value => $key)
		{
			$delete = anchor("admin/delivery/delete/".$key['id']."/", "Delete"); // Build actions links
			$edit = anchor("admin/delivery/edit/".$key['id']."/", "Edit"); // Build actions links
			$this->table->add_row(
				$key['delivery_id'],
				$key['application_id'],
				$key['buyer_id'],
				$key['merchant_id'],
				$key['merchant_trans_id'],
				$key['courier_id'],
				$key['shipping_address'],
				$key['phone'],
				$key['status'],
				$key['reschedule_ref'],
				$key['revoke_ref'],
				$edit.' '.$delete
			);
		}

		$this->ag_auth->view('delivery/incoming'); // Load the view
	}

	public function delete($id)
	{
		$this->db->where('id', $id)->delete($this->ag_auth->config['auth_user_table']);
		$this->ag_auth->view('users/delete_success');
	}

	private function get_group(){
		$this->db->select('id,description');
		$result = $this->db->get($this->ag_auth->config['auth_group_table']);
		foreach($result->result_array() as $row){
			$res[$row['id']] = $row['description'];
		}
		return $res;
	}

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

	/**
	*	transaction posting function
	*	required field is buyer email, since this function will try to extract it to check and authenticate
	*	whether buyer already registered at JEX or not
	*	if not yet registered, buyer will be automatically registered as new member and notified about new membership via email
	*/

	public function post($api_key = null,$transaction_id = null)
	{
		if(is_null($api_key)){
			print json_encode(array('status'=>'ERR:NOKEY','timestamp'=>now()));
		}else{
			$app = $this->get_key_info(trim($api_key));
			if($app == false){
				print json_encode(array('status'=>'ERR:INVALIDKEY','timestamp'=>now()));
			}else{
				//print_r($app);
				//$in = $this->input->post('transaction_detail');
				$in = $_POST['transaction_detail'];
				//print $in;
				$in = json_decode($in);

				//print_r($in);

				//check if email already registered

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
					$buyer_id = $this->register_buyer($dataset);
					$is_new = true;
				}

				$order['ordertime'] = date('Y-m-d h:i:s',time());
				$order['application_id'] = $app->id;
				$order['application_key'] = $app->key;
				$order['buyer_id'] = $buyer_id; // change this to current buyer after login
				$order['merchant_id'] = $app->merchant_id;
				$order['merchant_trans_id'] = trim($transaction_id);

				$order['buyer_name'] = $in->buyer_name;
				$order['recipient_name'] = $in->recipient_name;
				$order['email'] = $in->email;
				$order['directions'] = $in->directions;
				$order['buyerdeliverytime'] = $in->buyerdeliverytime;
				$order['buyerdeliveryzone'] = $in->buyerdeliveryzone;
				$order['buyerdeliverycity'] = $in->buyerdeliverycity;
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
					print json_encode(array('status'=>'OK:ORDERPOSTED','timestamp'=>now(),'delivery_id'=>$delivery_id,'buyer_id'=>$buyer_id));
				}else{
					print json_encode(array('status'=>'OK:ORDERPOSTEDNODETAIL','timestamp'=>now(),'delivery_id'=>$delivery_id));
				}

				if($is_new == true){
					$edata['fullname'] = $dataset['fullname'];
					$edata['username'] = $buyer_username;
					$edata['password'] = $password;

					send_notification('New Member Registration - Jayon Express COD Service',$in->email,null,'new_member',$edata,null);
				}

			}
		}
	}

	/*

	public function posts($api_key = null,$transaction_id = null)
	{

		if(is_null($api_key)){
			print json_encode(array('status'=>'ERR:NOKEY','timestamp'=>now()));
		}else{
			//print $api_key;
			//echo json_encode(array('status'=>'ERR:KEYEXISTS','timestamp'=>now()));
			$app = $this->get_key_info(trim($api_key));
			//print json_encode($app);

//			if($app){
//				if($in = $this->input->post('transaction_detail')){

					//$in = $this->input->post('transaction_detail');
					$in = $_POST['transaction_detail'];
					//print $in;

					$in = json_decode($in);

					$order['ordertime'] = date('Y-m-d h:i:s',time());
					$order['application_id'] = $app->id;
					$order['application_key'] = $app->key;
					$order['buyer_id'] = 1; // change this to current buyer after login
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
					//print json_encode(array('status'=>'OK:ORDERPOSTED','timestamp'=>now(),'delivery_id'=>$delivery_id));
//				}else{
//					print json_encode(array('status'=>'ERR:NODETAIL','timestamp'=>now()));
//				}
//			}else{
//				print json_encode(array('status'=>'ERR:NOAPPFOUND','timestamp'=>now()));
//			}

		}

	} // public function add() transaction

	*/

	/* Check & get particular timeslot for current date  */


	public function tsget($api_key = null,$month = null){
		if(is_null($api_key)){
			print json_encode(array('status'=>'ERR:NOKEY','timestamp'=>now()));
		}else{
			if(is_null($date)){
				//get slot for specified date
				print json_encode(array('status'=>'OK:CURRENTDATE','timestamp'=>now(),'timeslot'=>$delivery_id));
			}else{
				//full calendar time series for current month
				print json_encode(array('status'=>'OK:CURRENTMONTH','timestamp'=>now(),'timeslot'=>$delivery_id));
			}
		}
	}

	/* Check & get particular timeslot for current date  */

	public function tscheck($api_key = null,$date = null){
		if(is_null($api_key)){
			print json_encode(array('status'=>'ERR:NOKEY','timestamp'=>now()));
		}else{
			if(is_null($date)){
				//get slot for specified date
				print json_encode(array('status'=>'OK:CURRENTDATE','timestamp'=>now(),'timeslot'=>$delivery_id));
			}else{
				//full calendar time series for current month
				print json_encode(array('status'=>'OK:CURRENTMONTH','timestamp'=>now(),'timeslot'=>$delivery_id));
			}
		}
	}

	/* Update current location */

	public function locpost($api_key = null){
		if(is_null($api_key)){
			print json_encode(array('status'=>'ERR:NOKEY','timestamp'=>now()));
		}else{
			if(is_null($date)){
				//get slot for specified date
				print json_encode(array('status'=>'OK:CURRENTDATE','timestamp'=>now(),'timeslot'=>$delivery_id));
			}else{
				//full calendar time series for current month
				print json_encode(array('status'=>'OK:CURRENTMONTH','timestamp'=>now(),'timeslot'=>$delivery_id));
			}
		}
	}

	/* Update delivery status from mobile */

	public function statpost($api_key = null){
		if(is_null($api_key)){
			print json_encode(array('status'=>'ERR:NOKEY','timestamp'=>now()));
		}else{
			if(is_null($date)){
				//get slot for specified date
				print json_encode(array('status'=>'OK:CURRENTDATE','timestamp'=>now(),'timeslot'=>$delivery_id));
			}else{
				//full calendar time series for current month
				print json_encode(array('status'=>'OK:CURRENTMONTH','timestamp'=>now(),'timeslot'=>$delivery_id));
			}
		}
	}

	/* Synchronize mobile device */
	public function sync($api_key = null){
		if(is_null($api_key)){
			print json_encode(array('status'=>'ERR:NOKEY','timestamp'=>now()));
		}else{
			if(is_null($date)){
				//get slot for specified date
				print json_encode(array('status'=>'OK:CURRENTDATE','timestamp'=>now(),'timeslot'=>$delivery_id));
			}else{
				//full calendar time series for current month
				print json_encode(array('status'=>'OK:CURRENTMONTH','timestamp'=>now(),'timeslot'=>$delivery_id));
			}
		}
	}

	/* Lists JEX zones of coverage */

	public function zonelist($api_key = null){
		if(is_null($api_key)){
			print json_encode(array('status'=>'ERR:NOKEY','timestamp'=>now()));
		}else{
			if(is_null($date)){
				//get slot for specified date
				print json_encode(array('status'=>'OK:CURRENTDATE','timestamp'=>now(),'timeslot'=>$delivery_id));
			}else{
				//full calendar time series for current month
				print json_encode(array('status'=>'OK:CURRENTMONTH','timestamp'=>now(),'timeslot'=>$delivery_id));
			}
		}
	}

	/* Lists JEX zones of coverage, as results of <query string> matches */

	public function zoneget($api_key = null, $query = null){
		if(is_null($api_key)){
			print json_encode(array('status'=>'ERR:NOKEY','timestamp'=>now()));
		}else{
			if(is_null($date)){
				//get slot for specified date
				print json_encode(array('status'=>'OK:CURRENTDATE','timestamp'=>now(),'timeslot'=>$delivery_id));
			}else{
				//full calendar time series for current month
				print json_encode(array('status'=>'OK:CURRENTMONTH','timestamp'=>now(),'timeslot'=>$delivery_id));
			}
		}
	}

	public function add($api_key = null,$transaction_id = null)
	{

		if(is_null($api_key)){
			print json_encode(array('status'=>'ERR:NOKEY','timestamp'=>now()));
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

					print $buyer_id;


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
					print json_encode(array('status'=>'OK:ORDERPOSTED','timestamp'=>now(),'delivery_id'=>$delivery_id));
				}else{
					print json_encode(array('status'=>'ERR:NODETAIL','timestamp'=>now()));
				}
			}else{
				print json_encode(array('status'=>'ERR:NOKEYFOUND','timestamp'=>now()));
			}

		}

	} // public function add() transaction

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

	// WOKRING ON PROPER IMPLEMENTATION OF ADDING & EDITING USER ACCOUNTS
}

?>