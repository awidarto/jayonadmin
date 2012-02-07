<?php

class Admin extends Application
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		if(logged_in())
		{
			$this->breadcrumb->add_crumb('Home','admin/dashboard');
			
			$page['page_title'] = 'Dashboard';
			$this->ag_auth->view('dashboard',$page);
		}
		else
		{
			$this->login();
		}
	}
	

	public function testmail(){
		$subject = 'Processed order';
		$to = 'andy.awidarto@gmail.com';
		$template = '';
		$data = '';
		if(send_notification($subject,$to,$template,$data)){
			print "notification sent";
		}else{
			print "failed to send notification";
		}
	}
	
}

/* End of file: dashboard.php */
/* Location: application/controllers/admin/dashboard.php */