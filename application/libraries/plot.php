<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Curl Class
 *
 * Work with remote servers via cURL much easier than using the native PHP bindings.
 *
 * @package        	CodeIgniter
 * @subpackage    	Libraries
 * @category    	Libraries
 * @author        	Philip Sturgeon
 * @license         http://philsturgeon.co.uk/code/dbad-license
 * @link			http://philsturgeon.co.uk/code/codeigniter-curl
 */

require_once('phplot/phplot.php');
class Plot {

	private $CI;				// CodeIgniter instance

	function __construct($url = '')
	{
		$this->CI = &get_instance();
		log_message('debug', 'PHPlot Class Initialized');
	}

	function plot($width=500,$height=400){
		return new PHPlot($width,$height);
	}
}

?>