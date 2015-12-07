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
|	example.com/class/method/id/
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
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller']				= "client";

$route['(:any)/p(:num)']					= 'client/product/$1/$2';
$route['(:any)/c(:num)']					= 'client/category/$1/$2';

$route['(:any)/a(:num)']					= 'client/page/$1/$2';

$route['oplata-i-dostavka']					= 'client/oplata';
$route['biznes-predlojenie']				= 'client/business';

$route['partners']							= 'client/partners';
$route['partners/(:any)/s(:num)']			= 'client/partners/$1/$2';

$route['contacts']							= 'client/contacts';
$route['about-us']							= 'client/about';
$route['search']							= 'client/search';
$route['pricelist']							= 'client/pricelist';
$route['getprice']							= 'client/getprice';
$route['novaposhta']						= 'client/novaposhta';


$route['reviews']							= 'client/reviews';
$route['reviews/(add-review)']				= 'client/reviews/$1';

$route['404_override'] 						= '';


/* End of file routes.php */
/* Location: ./application/config/routes.php */