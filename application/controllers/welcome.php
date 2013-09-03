<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('welcome_message');
	}

	public function donothing(){
		return true;
	}

    public function testphone($num){

        print normalphone('+6208675656464/02178078675/+44 ( 78 ) 77656465');

        print "\r\n";

        print '<br />';

        print normalphone($num);
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */