<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
| 	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['scaffolding_trigger'] = 'scaffolding';
|
| This route lets you set a "secret" word that will trigger the
| scaffolding feature for added security. Note: Scaffolding must be
| enabled in the controller in which you intend to use it.   The reserved 
| routes must come before any wildcard or regular expression routes.
|
*/

$route['default_controller'] = "admin/admin/login";
$route['scaffolding_trigger'] = "";


// BEGIN AUTHENTICATION LIBRARY ROUTES
$route['login'] = "admin/admin/login";
$route['logout'] = "admin/admin/logout";
$route['register'] = "admin/admin/register";
$route['resetpass'] = "admin/admin/resetpass";
$route['admin/dashboard'] = "admin/admin/index";
// END AUTHENTICATION LIBRARY ROUTES

//JAYON SPECIFIC ROUTES
$route['admin/members/merchant/apps/manage/(:num)'] = "admin/apps/merchantmanage/$1";
$route['admin/members/merchant/apps/add/(:num)'] = "admin/apps/add/$1";
$route['admin/members/merchant/apps/edit/(:num)'] = "admin/apps/edit/$1";
$route['admin/members/merchantdelete/(:num)'] = "admin/apps/delete/$1";

$route['admin/members/merchant/add'] = "admin/members/add";
$route['admin/members/merchant/edit/(:num)'] = "admin/members/edit/$1";

$route['admin/members/merchantgroup/addgroup'] = "admin/members/addgroup";
$route['admin/members/merchantgroup/edit/(:num)'] = "admin/members/edit/$1";

$route['admin/members/buyer/add'] = "admin/members/add";
$route['admin/members/buyer/edit/(:num)'] = "admin/members/edit/$1";

$route['admin/uichanges'] = "admin/admin/uichanges";


/* End of file routes.php */
/* Location: ./system/application/config/routes.php */