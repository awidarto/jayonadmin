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

	public function getcity(){
		$q = $this->input->get('term');
		$zones = ajax_find_cities($q,'city');
		print json_encode($zones);
	}

	public function getcourier(){
		$q = $this->input->get('term');
		$zones = ajax_find_courier($q,'fullname','id');
		print json_encode($zones);
	}

	public function getdevice(){
		$q = $this->input->get('term');
		$zones = ajax_find_device($q,'identifier');
		print json_encode($zones);
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