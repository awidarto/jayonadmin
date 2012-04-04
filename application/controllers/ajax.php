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