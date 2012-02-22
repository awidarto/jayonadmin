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
		
		$result = send_notification($subject,$to,$template,$data);
		if($result){
			print "notification sent";
		}else{
			print "failed to send notification";
		}
	}

	public function testmail(){
		
		$subject = "test notification";
		$to = "andy.awidarto@kickstartlab.com";

		print "test notify<br />";
		$result = send_notification($subject,$to);

		if($result){
			print "notification sent<br />";
		}else{
			print "failed to send notification<br />";
		}

		$subject = "test report";
		$template = 'default';
		
		print "test admin<br />";
		$result = send_admin($subject,$to);

		if($result){
			print "admin message sent<br />";
		}else{
			print "failed to send admin message<br />";
		}

	}	

}