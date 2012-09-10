<?php

class Util extends CI_Controller{

	public function __construct()
	{
		parent::__construct();		
	}

	public function mergeaddress($mode = 'dry'){

		set_time_limit(0);

		$this->db->distinct();
		$this->db->select('buyer_id,phone,buyer_name as fullname,shipping_address as street,buyerdeliveryzone as district,buyerdeliverycity as city,shipping_zip as zip');
		$this->db->from($this->config->item('incoming_delivery_table'));

		$dx = $this->db->get()->result();

		//street	district	province	city	country	zip

		foreach ($dx as $d) {
			//$data['buyer_id']	=

            //$data['fullname'] 	= $d->fullname;

            print "\r\n______________from_trx_______________\r\n";
            print_r($d);

			$byr = $this->db
					->select('phone,street,district,city,zip')
	            	->from($this->config->item('jayon_members_table'))
	            	->where('id',$d->buyer_id)
	            	->get();


            if($byr->num_rows() > 0){

		        $by = $byr->row();
	            print "\r\n______________current_in_member______________\r\n";
	        	print_r($by);

	            $data['phone']		= (trim($by->phone) == "" || is_null($by->phone))?$d->phone:$by->phone;
	            $data['street'] 	= (trim($by->street) == "" || is_null($by->street))?$d->street:$by->street;
	            $data['district'] 	= (trim($by->district) == "" || is_null($by->district))?$d->district:$by->district;
	            $data['city'] 		= (trim($by->city)== "" || is_null($by->city))?$d->city:$by->city;
	            $data['zip'] 		= (trim($by->zip) == "" || is_null($by->zip))?$d->zip:$by->zip;

	            print "\r\n______________merged______________\r\n";
	            print_r($data);

	            if($mode == 'run'){
		            $this->db
		            	->where('id',$d->buyer_id)
		            	->update($this->config->item('jayon_members_table'),$data);
		            print "\r\n______________merging______________\r\n";
		            $this->db->affected_rows();
	            }

	            print "\r\n==============================\r\n";

            }

            /*

            else{
	            $data['phone']		= (isset($by->phone) && !($by->phone == '' || is_null($by->phone)))?$by->phone:$d->phone;
	            $data['street'] 	= (isset($by->street) && !($by->street == '' || is_null($by->street)))?$by->street:$d->street;
	            $data['district'] 	= (isset($by->district) && !($by->district == '' || is_null($by->district)))?$by->district:$d->district;
	            $data['city'] 		= (isset($by->city) && !($by->city == '' || is_null($by->city)))?$by->city:$d->city;
	            $data['zip'] 		= (isset($by->zip) && !($by->zip == '' || is_null($by->zip)))?$by->zip:$d->zip;            	
            }

			
	            $data['phone']		= (isset($by->phone) && !($by->phone == '' || is_null($by->phone)))?$by->phone:$d->phone;
	            $data['street'] 	= (isset($by->street) && !($by->street == '' || is_null($by->street)))?$by->street:$d->street;
	            $data['district'] 	= (isset($by->district) && !($by->district == '' || is_null($by->district)))?$by->district:$d->district;
	            $data['city'] 		= (isset($by->city) && !($by->city == '' || is_null($by->city)))?$by->city:$d->city;
	            $data['zip'] 		= (isset($by->zip) && !($by->zip == '' || is_null($by->zip)))?$by->zip:$d->zip;
			*/


		}



	}	

}

?>