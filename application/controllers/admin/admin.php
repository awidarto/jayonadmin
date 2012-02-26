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
			$this->breadcrumb->add_crumb('Dashboard','admin/dashboard');
			
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

	public function resetpass(){

		$this->form_validation->set_rules('email', 'Email Address', 'required|min_length[6]|valid_email');

		if($this->form_validation->run() == FALSE)
		{
			$this->ag_auth->view('resetpass');
		}
		else
		{
			$email = set_value('email');
			if($buyer = $this->check_email($email)){
				$password = random_string('alnum', 8);
				$dataset['password'] = $this->ag_auth->salt($password);
				$this->oi->add_success('New password has been sent to your email.');
			}else{
				$this->oi->add_error('Your email can not be found, please consider registering as new member.');
			}

			redirect('resetpass');
		}

	}

	private function check_email($email){
		$em = $this->db->where('email',$email)->get($this->config->item('auth_user_table'));
		if($em->num_rows() > 0){
			return $em->row_array();
		}else{
			return false;
		}
	}
	
}

/* End of file: dashboard.php */
/* Location: application/controllers/admin/dashboard.php */