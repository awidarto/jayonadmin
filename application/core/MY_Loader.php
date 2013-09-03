<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

//Load Active Record manually
require_once(BASEPATH.'database/DB_driver.php');
require_once(BASEPATH.'database/DB_active_rec.php');

//Load our version version of Active Record 
require_once(APPPATH. 'core/Active_Record.php');

//Finally initialize the DB class
class CI_DB extends custom_active_record{} 

//In order to not break the loader class I will create my dummy loader class
class MY_Loader extends CI_Loader {}

//NOTE that if you are using HMVC you would probably do something like this
//require APPPATH."third_party/MX/Loader.php";
//class MY_Loader extends MX_Loader {}