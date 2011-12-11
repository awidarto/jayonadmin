<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Base Site URL
|--------------------------------------------------------------------------
|
| URL to your CodeIgniter root. Typically this will be your base URL,
| WITH a trailing slash:
|
|	http://example.com/
|
| If this is not set then CodeIgniter will guess the protocol, domain and
| path to your installation.
|
*/
$config['site_title']	= 'Jayon Express Admin';


/*table names*/
$config['incoming_delivery_table'] = 'delivery_order_incoming';
$config['assigned_delivery_table'] = 'delivery_order_assigned';
$config['delivered_delivery_table'] = 'delivery_order_delivered';

$config['delivery_details_table'] = 'delivery_order_details';

$config['applications_table'] = 'applications';
$config['delivery_log_table'] = 'delivery_log';
$config['location_log_table'] = 'location_log';
$config['sequence_table'] = 'applications';


$config['jayon_members_table'] = 'members';
$config['jayon_couriers_table'] = 'couriers';
$config['jayon_devices_table'] = 'devices';
?>