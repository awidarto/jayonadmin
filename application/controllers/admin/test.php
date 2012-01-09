<?php

class Test extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index(){
		print 'test controller';
	}
	
	public function notify(){
		$subject = 'Processed order';
		$to = 'andy.awidarto@gmail.com';
		$template = '';
		$data = '';
		
		$result = $this->send_notification($subject,$to,$template,$data);
		if($result){
			print "notification sent";
		}else{
			print "failed to send notification";
		}
	}
	
	private function send_notification($subject,$to,$template,$data){

		$config = array(
		    'protocol' => 'smtp',
		    'smtp_host' => $this->config->item('smtp_host'),
		    'smtp_port' => $this->config->item('smtp_port'),
		    'smtp_user' => $this->config->item('notify_username'),
		    'smtp_pass' => $this->config->item('notify_password'),
		    'charset'   => 'iso-8859-1'
		);
		
		print_r($config);

		$this->load->library('email');
		
		$this->email->initialize($config);

		$this->email->from($this->config->item('notify_username'), 'Jayon Express Notification');
		$this->email->to($to); 
		$this->email->cc('admin@jayonexpress.com'); 
		$this->email->subject($subject);
		$this->email->message('Testing notification email.');	

		$result = $this->email->send();

		return $result;
	}
}