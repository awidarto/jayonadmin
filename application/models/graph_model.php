<?php
/**
* Authentication Library
*
* @package Authentication
* @category Libraries
* @author Adam Griffiths
* @link http://adamgriffiths.co.uk
* @version 2.0.3
* @copyright Adam Griffiths 2011
*
* Auth provides a powerful, lightweight and simple interface for user authentication .
*/

class Users_model extends CI_Model
{
	var $user_table; // The user table (prefix + config)
	var $group_table; // The group table (prefix + config)
	
	public function __construct()
	{
		parent::__construct();

		log_message('debug', 'Graph Model Loaded');
		
		$this->config->load('ag_auth');
		$this->load->database();

		$this->user_table = $this->config->item('auth_user_table');
		$this->group_table = $this->config->item('auth_group_table');
	}

}

?>