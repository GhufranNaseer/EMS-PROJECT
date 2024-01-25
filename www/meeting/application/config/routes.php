<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = 'welcome/show404';
$route['translate_uri_dashes'] = FALSE;

$route['alert.html']['get'] = 'welcome/showmsg';

# S Log in
$route['login/(:num)-(:any)']['get'] = "welcome/login/$1/$2";
$route['login-validate']['post'] = "welcome/login_validate/doError";
$route['login-submit']['post'] = "welcome/login_submit";
# E Log in


$route['log-out']['get'] = "welcome/logout";


# S Forget password
$route['forget-password/(:num)-(:any)']['get'] = "welcome/forget_password/$1/$2";
$route['forget-password-validate']['post'] = "welcome/forget_password_validate/doError";
$route['forget-password-submit']['post'] = "welcome/forget_password_submit";

$route['recover-account/(:any)']['get'] = "welcome/recover_account/$1";
$route['recover-account-validate/(:any)']['post'] = "welcome/recover_account_validate/$1/doError";
$route['recover-account-submit/(:any)']['post'] = "welcome/recover_account_submit/$1";
# E Forget password


$route['event_updates']['post'] = "welcome/event_updates";


$route['dashboard']['get'] = "portal";


# S Profile
$route['my-profile']['get'] = "portal/my_profile";
$route['my-profile-validate']['post'] = "portal/my_profile_validate/doError";
$route['my-profile-submit']['post'] = "portal/my_profile_submit";
# E Profile

# S password update
$route['my-profile-password-validate']['post'] = "portal/my_profile_password_validate/doError";
$route['my-profile-password']['post'] = "portal/my_profile_password";
# E password update


# S meeting
$route['exhibitors-list.html']['get'] = "meeting/crd_exhibitors_list";
$route['exhibitors-datatable.html'] = "meeting/crd_exhibitors_list_datatable";

$route['officer-list.html']['get'] = "meeting/crd_officer_list";
$route['officer-datatable.html'] = "meeting/crd_officer_list_datatable";

$route['meeting_schedule.html']['get'] = "meeting/meeting_form";
$route['meeting_schedule_validate.html']['post'] = "meeting/meeting_form_validate/doError";
$route['meeting_schedule_submit.html']['post'] = "meeting/meeting_form_submit";



$route['my_meeting.html']['get'] = "meeting_report/crd_list";
$route['my_meeting-datatable.html'] = "meeting_report/crd_list_datatable";

$route['meeting-cancel.html']['get'] = "meeting_report/cancel";
$route['meeting-approved.html']['get'] = "meeting_report/approved";
$route['meeting-delete.html']['get'] = "meeting_report/delete_meeting";

$route['meeting_re_schedule.html']['get'] = "meeting_report/schedule";
$route['meeting_re_schedule_validate.html']['post'] = "meeting_report/meeting_re_schedule_validate/doError";
$route['meeting_re_schedule_submit.html']['post'] = "meeting_report/meeting_re_schedule_submit";

