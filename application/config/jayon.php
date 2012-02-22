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

$config['infinite_scroll'] = false;

/*table names*/
$config['incoming_delivery_table'] = 'delivery_order_active';
$config['assigned_delivery_table'] = 'delivery_order_active';
$config['delivered_delivery_table'] = 'delivery_order_active';
$config['archived_delivery_table'] = 'delivery_order_archive';

$config['delivery_details_table'] = 'delivery_order_details';

$config['applications_table'] = 'applications';
$config['delivery_log_table'] = 'delivery_log';
$config['location_log_table'] = 'location_log';
$config['access_log_table'] = 'api_access_log';
$config['sequence_table'] = 'applications';
$config['device_assignment_table'] = 'device_assignment';


$config['jayon_members_table'] = 'members';
$config['jayon_couriers_table'] = 'couriers';
$config['jayon_holidays_table'] = 'holidays';
$config['jayon_devices_table'] = 'devices';
$config['jayon_options_table'] = 'options';
$config['jayon_zones_table'] = 'districts';
$config['jayon_timeslots_table'] = 'slots';
$config['jayon_email_outbox_table'] = 'email_outbox';

//test only
$config['jayon_mobile_table'] = 'mobile_orders';


/* Delivery status strings */

$config['trans_status_new'] = 'pending';
$config['trans_status_purged'] = 'purged';
$config['trans_status_archived'] = 'archived';
$config['trans_status_confirmed'] = 'confirmed';
$config['trans_status_canceled'] = 'canceled';
$config['trans_status_rescheduled'] = 'rescheduled';

$config['trans_status_mobile_dispatched'] = 'dispatched';
$config['trans_status_mobile_departure'] = 'departed';
$config['trans_status_mobile_return'] = 'returned';
$config['trans_status_mobile_pickedup'] = 'pickedup';
$config['trans_status_mobile_enroute'] = 'enroute';
$config['trans_status_mobile_location'] = 'loc_update';
$config['trans_status_mobile_rescheduled'] = 'rescheduled';
$config['trans_status_mobile_delivered'] = 'delivered';
$config['trans_status_mobile_revoked'] = 'revoked';
$config['trans_status_mobile_noshow'] = 'noshow';

$config['trans_status_admin_zoned'] = 'zone_assigned';
$config['trans_status_admin_dated'] = 'date_assigned';
$config['trans_status_admin_devassigned'] = 'dev_assigned';
$config['trans_status_admin_courierassigned'] = 'cr_assigned';
$config['trans_status_admin_dispatched'] = 'dispatched';

/* status colors */

$config['status_colors'] = array(
	$config['trans_status_new'] => 'orange',
	$config['trans_status_purged'] => 'red',
	$config['trans_status_archived'] => 'brown',
	$config['trans_status_confirmed'] => 'green',
	$config['trans_status_canceled'] => 'red',
	$config['trans_status_rescheduled'] => 'green',

	$config['trans_status_mobile_pickedup'] => 'green',
	$config['trans_status_mobile_enroute'] => 'orange',
	$config['trans_status_mobile_location'] => 'black',
	$config['trans_status_mobile_rescheduled'] => 'brown',
	$config['trans_status_mobile_delivered'] => 'green',
	$config['trans_status_mobile_revoked'] => 'red',
	$config['trans_status_mobile_noshow'] => 'orange',

	$config['trans_status_admin_zoned'] => 'brown',
	$config['trans_status_admin_dated'] => 'blue',
	$config['trans_status_admin_devassigned'] => 'black',
	$config['trans_status_admin_courierassigned'] => 'black',
	$config['trans_status_admin_dispatched'] => 'green'
);

$config['actors_code'] = array(
	'mobile'=>'MB',
	'admin'=>'AD',
	'buyer'=>'BY',
	'merchant'=>'MC'
);

$config['actors_title'] = array(
	'MB'=>'mobile',
	'AD'=>'admin',
	'BY'=>'buyer',
	'MC'=>'merchant'
);


$config['fetch_method'] = array(
	'GET'=>'GET',
	'URL'=>'URL Segment'
);

$config['smtp_host'] = 'ssl://smtp.googlemail.com';
$config['smtp_port'] = '465';

$config['notify_username'] = 'notification@jayonexpress.com';
$config['notify_password'] = 'NotiFier987';
//$config['notify_username'] = 'bolongsox';
//$config['notify_password'] = 'masukajadeh';


$config['admin_username'] = 'admin@jayonexpress.com';
$config['admin_password'] = 'JayonAdmin234';

//for test only 

$config['api_url'] = 'http://localhost/jayonadmin/api/v1/';


?>