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
					
					$order['ordertime'] = date('Y-m-d h:i:s',time());
					$order['application_id'] = $app->id;
					$order['application_key'] = $app->key;
					$order['buyer_id'] = 1; // change this to current buyer after login
					$order['merchant_id'] = $app->merchant_id;
					$order['merchant_trans_id'] = trim($transaction_id);
					
					$order['shipping_address'] = $in->shipping_address;
					$order['phone'] = $in->phone;
					$order['status'] = 'incoming';
					
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

	public function edit($username)
	{
		$this->form_validation->set_rules('username', 'Username', 'required|min_length[6]|callback_field_exists');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|matches[password_conf]');
		$this->form_validation->set_rules('password_conf', 'Password Confirmation', 'required|min_length[6]|matches[password]');
		$this->form_validation->set_rules('email', 'Email Address', 'required|min_length[6]|valid_email|callback_field_exists');
		$this->form_validation->set_rules('group_id', 'Group', 'trim');
				
		if($this->form_validation->run() == FALSE)
		{
			$data['groups'] = $this->get_group();
			$this->ag_auth->view('users/add',$data);
		}
		else
		{
			$username = set_value('username');
			$password = $this->ag_auth->salt(set_value('password'));
			$email = set_value('email');
			$group_id = set_value('group_id');
			
			if($this->ag_auth->register($username, $password, $email, $group_id) === TRUE)
			{
				$data['message'] = "The user account has now been created.";
				$this->ag_auth->view('message', $data);
				
			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$data['message'] = "The user account has not been created.";
				$this->ag_auth->view('message', $data);
			}

		} // if($this->form_validation->run() == FALSE)
		
	} // public function register()

	
	// WOKRING ON PROPER IMPLEMENTATION OF ADDING & EDITING USER ACCOUNTS
}

?>