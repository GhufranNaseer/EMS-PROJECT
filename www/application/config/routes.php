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
$route['login']['get'] = "welcome/login";
$route['login-validate']['post'] = "welcome/login_validate/doError";
$route['login-submit']['post'] = "welcome/login_submit";
# E Log in

# S Forget password
$route['forget-password']['get'] = "welcome/forget_password";
$route['forget-password-validate']['post'] = "welcome/forget_password_validate/doError";
$route['forget-password-submit']['post'] = "welcome/forget_password_submit";

$route['recover-account/(:any)']['get'] = "welcome/recover_account/$1";
$route['recover-account-validate/(:any)']['post'] = "welcome/recover_account_validate/$1/doError";
$route['recover-account-submit/(:any)']['post'] = "welcome/recover_account_submit/$1";
# E Forget password

$route['log-out']['get'] = "welcome/logout";


$route['dashboard']['get'] = "portal";
$route['dashboard-salesperson-bookings.html']['post'] = "portal/crd_list_datatable";

# S Profile
$route['my-profile']['get'] = "portal/my_profile";
$route['my-profile-validate']['post'] = "portal/my_profile_validate/doError";
$route['my-profile-submit']['post'] = "portal/my_profile_submit";
$route['my-password-validate']['post'] = "welcome/my_password_validate/doError";
$route['my-password-submit']['post'] = "welcome/my_password_submit/my-profile";
# E Profile


#S user_group
$route['user-groups.html']['get'] = "user_group/crd_list";
$route['user-groups-datatable.html']['post'] = "user_group/crd_list_datatable";

$route['user-groups-add.html']['get'] = "user_group/crd_add";
$route['user-groups-validate.html']['post'] = "user_group/crd_add_validate/doError";
$route['user-groups-submit.html']['post'] = "user_group/crd_add_submit";

$route['user-groups-edit.html']['get'] = "user_group/crd_edit";
$route['user-groups-edit-validate.html']['post'] = "user_group/crd_edit_validate/doError";
$route['user-groups-edit-submit.html']['post'] = "user_group/crd_edit_submit";

$route['user-groups-delete.html']['get'] = "user_group/crd_delete";
#E user_group


#S user
$route['users.html']['get'] = "users/crd_list";
$route['users-datatable.html']['post'] = "users/crd_list_datatable";

$route['user-add.html']['get'] = "users/crd_add";
$route['user-validate.html']['post'] = "users/crd_add_validate/doError";
$route['user-submit.html']['post'] = "users/crd_add_submit";

$route['user-edit.html']['get'] = "users/crd_edit";
$route['user-edit-validate.html']['post'] = "users/crd_edit_validate/doError";
$route['user-edit-submit.html']['post'] = "users/crd_edit_submit";

$route['user-delete.html']['get'] = "users/crd_delete";
#E user


#S inventory category
$route['inventory-category.html']['get'] = "inventory/inventory_category/crd_list";
$route['inventory-category-datatable.html']['post'] = "inventory/inventory_category/crd_list_datatable";

$route['inventory-category-add.html']['get'] = "inventory/inventory_category/crd_add";
$route['inventory-category-validate.html']['post'] = "inventory/inventory_category/crd_add_validate/doError";
$route['inventory-category-submit.html']['post'] = "inventory/inventory_category/crd_add_submit";

$route['inventory-category-edit.html']['get'] = "inventory/inventory_category/crd_edit";
$route['inventory-category-edit-validate.html']['post'] = "inventory/inventory_category/crd_edit_validate/doError";
$route['inventory-category-edit-submit.html']['post'] = "inventory/inventory_category/crd_edit_submit";

$route['inventory-category-delete.html']['get'] = "inventory/inventory_category/crd_delete";
#E inventory category


#S inventory item
$route['inventory-item.html']['get'] = "inventory/inventory_item/crd_list";
$route['inventory-item-datatable.html']['post'] = "inventory/inventory_item/crd_list_datatable";

$route['inventory-item-add.html']['get'] = "inventory/inventory_item/crd_add";
$route['inventory-item-validate.html']['post'] = "inventory/inventory_item/crd_add_validate/doError";
$route['inventory-item-submit.html']['post'] = "inventory/inventory_item/crd_add_submit";

$route['inventory-item-edit.html']['get'] = "inventory/inventory_item/crd_edit";
$route['inventory-item-edit-validate.html']['post'] = "inventory/inventory_item/crd_edit_validate/doError";
$route['inventory-item-edit-submit.html']['post'] = "inventory/inventory_item/crd_edit_submit";

$route['inventory-item-delete.html']['get'] = "inventory/inventory_item/crd_delete";

$route['inventory-item-file-upload.html']['post'] = "inventory/inventory_item/item_file_upload";
#E inventory item


#S demonstration venue
$route['demonstration-venue.html']['get'] = "inventory/demonstration_venue/crd_list";
$route['demonstration-venue-datatable.html']['post'] = "inventory/demonstration_venue/crd_list_datatable";

$route['demonstration-venue-add.html']['get'] = "inventory/demonstration_venue/crd_add";
$route['demonstration-venue-validate.html']['post'] = "inventory/demonstration_venue/crd_add_validate/doError";
$route['demonstration-venue-submit.html']['post'] = "inventory/demonstration_venue/crd_add_submit";

$route['demonstration-venue-edit.html']['get'] = "inventory/demonstration_venue/crd_edit";
$route['demonstration-venue-edit-validate.html']['post'] = "inventory/demonstration_venue/crd_edit_validate/doError";
$route['demonstration-venue-edit-submit.html']['post'] = "inventory/demonstration_venue/crd_edit_submit";

$route['demonstration-venue-delete.html']['get'] = "inventory/demonstration_venue/crd_delete";
#E demonstration venue



#S locations
$route['locations.html']['get'] = "locations/crd_list";
$route['locations-datatable.html']['post'] = "locations/crd_list_datatable";

$route['locations-add.html']['get'] = "locations/crd_add";
$route['locations-validate.html']['post'] = "locations/crd_add_validate/doError";
$route['locations-submit.html']['post'] = "locations/crd_add_submit";

$route['locations-edit.html']['get'] = "locations/crd_edit";
$route['locations-edit-validate.html']['post'] = "locations/crd_edit_validate/doError";
$route['locations-edit-submit.html']['post'] = "locations/crd_edit_submit";

$route['locations-delete.html']['get'] = "locations/crd_delete";
#E locations

#S Email template
$route['email_template.html']['get'] = "email_template/crd_list";
$route['email_template-datatable.html']['post'] = "email_template/crd_list_datatable";

$route['email_template-add.html']['get'] = "email_template/crd_add";
$route['email_template-validate.html']['post'] = "email_template/crd_add_validate/doError";
$route['email_template-submit.html']['post'] = "email_template/crd_add_submit";

$route['email_template-edit.html']['get'] = "email_template/crd_edit";
$route['email_template-edit-validate.html']['post'] = "email_template/crd_edit_validate/doError";
$route['email_template-edit-submit.html']['post'] = "email_template/crd_edit_submit";

$route['email_template-delete.html']['get'] = "email_template/crd_delete";
#E Email template


#S packages
$route['packages-event.html']['get'] = "inventory/packages/crd_event_list";
$route['packages-event-datatable.html']['post'] = "inventory/packages/crd_event_list_datatable";

$route['packages.html']['get'] = "inventory/packages/crd_list";
$route['packages-datatable.html']['post'] = "inventory/packages/crd_list_datatable";

$route['packages-add.html']['get'] = "inventory/packages/crd_add";
$route['packages-validate.html']['post'] = "inventory/packages/crd_add_validate/doError";
$route['packages-submit.html']['post'] = "inventory/packages/crd_add_submit";

$route['packages-edit.html']['get'] = "inventory/packages/crd_edit";
$route['packages-edit-validate.html']['post'] = "inventory/packages/crd_edit_validate/doError";
$route['packages-edit-submit.html']['post'] = "inventory/packages/crd_edit_submit";

$route['packages-delete.html']['get'] = "inventory/packages/crd_delete";
#E packages



#S update order badges
$route['update-order-badges.html']['get'] = "update_order_badges/crd_event_list";
$route['update-order-badges-datatable.html']['post'] = "update_order_badges/crd_event_list_datatable";

$route['update-order-badges-packages.html']['get'] = "update_order_badges/crd_list";
$route['update-order-badges-packages-datatable.html']['post'] = "update_order_badges/crd_list_datatable";

$route['update-order-badges-edit.html']['get'] = "update_order_badges/crd_edit";
$route['update-order-badges-edit-validate.html']['post'] = "update_order_badges/crd_edit_validate/doError";
$route['update-order-badges-edit-submit.html']['post'] = "update_order_badges/crd_edit_submit";

#E update order badges


#S exhibitions
$route['exhibitions.html']['get'] = "exhibitions/crd_list";
$route['exhibitions-datatable.html']['post'] = "exhibitions/crd_list_datatable";

$route['exhibitions-add.html']['get'] = "exhibitions/crd_add";
$route['exhibitions-validate.html']['post'] = "exhibitions/crd_add_validate/doError";
$route['exhibitions-submit.html']['post'] = "exhibitions/crd_add_submit";

$route['exhibitions-edit.html']['get'] = "exhibitions/crd_edit";
$route['exhibitions-edit-validate.html']['post'] = "exhibitions/crd_edit_validate/doError";
$route['exhibitions-edit-submit.html']['post'] = "exhibitions/crd_edit_submit";

$route['exhibitions-delete.html']['get'] = "exhibitions/crd_delete";

$route['exhibitions-inventory-category.html']['post'] = "inventory/packages/get_inventory_category";
$route['exhibitions-inventory-items.html']['post'] = "exhibitions/get_inventory_items";
#E exhibitions


#S notification
$route['notification.html']['get'] = "notification/crd_list";
$route['notification-datatable.html']['post'] = "notification/crd_list_datatable";

$route['notification2.html']['get'] = "notification/notification_list";
$route['notification2-datatable.html']['post'] = "notification/notification_list_datatable";

$route['notification-add.html']['get'] = "notification/crd_add";
$route['notification-validate.html']['post'] = "notification/crd_add_validate/doError";
$route['notification-submit.html']['post'] = "notification/crd_add_submit";

$route['notification-edit.html']['get'] = "notification/crd_edit";
$route['notification-edit-validate.html']['post'] = "notification/crd_edit_validate/doError";
$route['notification-edit-submit.html']['post'] = "notification/crd_edit_submit";

$route['notification-delete.html']['get'] = "notification/crd_delete";
#E notification

#S advertisment
$route['advertisment.html']['get'] = "advertisment/crd_list";
$route['advertisment-datatable.html']['post'] = "advertisment/crd_list_datatable";

$route['advertisment-event.html']['get'] = "advertisment/advertisment_list";
$route['advertisment-event-datatable.html']['post'] = "advertisment/advertisment_list_datatable";

$route['advertisment-add.html']['get'] = "advertisment/crd_add";
$route['advertisment-validate.html']['post'] = "advertisment/crd_add_validate/doError";
$route['advertisment-submit.html']['post'] = "advertisment/crd_add_submit";

$route['advertisment-edit.html']['get'] = "advertisment/crd_edit";
$route['advertisment-edit-validate.html']['post'] = "advertisment/crd_edit_validate/doError";
$route['advertisment-edit-submit.html']['post'] = "advertisment/crd_edit_submit";

$route['advertisment-delete.html']['get'] = "advertisment/crd_delete";
#E advertisment


#S print jobs
$route['print-job.html']['get'] = "print_job/crd_list";
$route['print-job-datatable.html']['post'] = "print_job/crd_list_datatable";

$route['print-job-exhibitors.html']['get'] = "print_job/booking_list";
$route['print-job-exhibitors-datatable.html'] = "print_job/booking_list_datatable";

$route['print-multiple-badges.html']['get'] = "print_job/print_badges";
#E print jobs


#S customers
$route['customers.html']['get'] = "customers/crd_list";
$route['customers-datatable.html']['post'] = "customers/crd_list_datatable";

$route['customer-add.html']['get'] = "customers/crd_add";
$route['customer-validate.html']['post'] = "customers/crd_add_validate/doError";
$route['customer-submit.html']['post'] = "customers/crd_add_submit";

$route['customer-edit.html']['get'] = "customers/crd_edit";
$route['customer-edit-validate.html']['post'] = "customers/crd_edit_validate/doError";
$route['customer-edit-submit.html']['post'] = "customers/crd_edit_submit";

$route['customer-delete.html']['get'] = "customers/crd_delete";
#E customers

#S contact person
$route['contact_person.html']['get'] = "contact_person/crd_list";
$route['contact_person-datatable.html']['post'] = "contact_person/crd_list_datatable";

$route['contact_person-add.html']['get'] = "contact_person/crd_add";
$route['contact_person-validate.html']['post'] = "contact_person/crd_add_validate/doError";
$route['contact_person-submit.html']['post'] = "contact_person/crd_add_submit";

$route['contact_person-edit.html']['get'] = "contact_person/crd_edit";
$route['contact_person-edit-validate.html']['post'] = "contact_person/crd_edit_validate/doError";
$route['contact_person-edit-submit.html']['post'] = "contact_person/crd_edit_submit";

$route['contact_person-delete.html']['get'] = "contact_person/crd_delete";
#E contact person

#S agent
$route['agent.html']['get'] = "agent/crd_list";
$route['agent-datatable.html']['post'] = "agent/crd_list_datatable";

$route['agent-add.html']['get'] = "agent/crd_add";
$route['agent-validate.html']['post'] = "agent/crd_add_validate/doError";
$route['agent-submit.html']['post'] = "agent/crd_add_submit";

$route['agent-edit.html']['get'] = "agent/crd_edit";
$route['agent-edit-validate.html']['post'] = "agent/crd_edit_validate/doError";
$route['agent-edit-submit.html']['post'] = "agent/crd_edit_submit";

$route['agent-delete.html']['get'] = "agent/crd_delete";
#E agent

#S officer

$route['officer_exhibitor.html']['get'] = "officer/exhibitor_crd_list";
$route['officer_exhibitor-datatable.html']['post'] = "officer/exhibitor_crd_list_datatable";

$route['officer.html']['get'] = "officer/crd_list";
$route['officer-datatable.html']['post'] = "officer/crd_list_datatable";

$route['officer-add.html']['get'] = "officer/crd_add";
$route['officer-validate.html']['post'] = "officer/crd_add_validate/doError";
$route['officer-submit.html']['post'] = "officer/crd_add_submit";

$route['officer-edit.html']['get'] = "officer/crd_edit";
$route['officer-edit-validate.html']['post'] = "officer/crd_edit_validate/doError";
$route['officer-edit-submit.html']['post'] = "officer/crd_edit_submit";

$route['officer-print.html']['get'] = "officer/crd_print";

$route['officer-edit-foreign-delegations.html']['get'] = "officer/foreign_delegations_edit";
$route['officer-edit-foreign-delegations-validate.html']['post'] = "officer/foreign_delegations_edit_validate/doError";
$route['officer-edit-foreign-delegations-submit.html']['post'] = "officer/foreign_delegations_edit_submit";

$route['officer-edit-local-delegates.html']['get'] = "officer/local_delegates_edit";
$route['officer-edit-local-delegates-validate.html']['post'] = "officer/local_delegates_edit_validate/doError";
$route['officer-edit-local-delegates-submit.html']['post'] = "officer/local_delegates_edit_submit";


$route['officer-edit-chief-of-servicing.html']['get'] = "officer/chief_of_servicing_edit";
$route['officer-edit-chief-of-servicing-validate.html']['post'] = "officer/chief_of_servicings_edit_validate/doError";
$route['officer-edit-chief-of-servicing-submit.html']['post'] = "officer/chief_of_servicing_edit_submit";

$route['officer-delete.html']['get'] = "officer/crd_delete";

$route['officer-add-foreign-delegations.html']['get'] = "officer/foreign_delegations";
$route['officer-foreign-delegations-validate.html']['post'] = "officer/foreign_delegations_add_validate/doError";
$route['officer-foreign-delegations-submit.html']['post'] = "officer/foreign_delegations_add_submit";

$route['officer-add-local-delegates.html']['get'] = "officer/local_delegates";
$route['officer-local-delegates-validate.html']['post'] = "officer/local_delegates_add_validate/doError";
$route['officer-local-delegates-submit.html']['post'] = "officer/local_delegates_add_submit";

$route['officer-add-chief-of-servicing.html']['get'] = "officer/chief_of_servicing";
$route['officer-chief-of-servicing-validate.html']['post'] = "officer/chief_of_servicing_add_validate/doError";
$route['officer-chief-of-servicing-submit.html']['post'] = "officer/chief_of_servicing_add_submit";


$route['officer-add-armed-force.html']['get'] = "officer/armed_force";
$route['officer-armed-force-validate.html']['post'] = "officer/armed_force_add_validate/doError";
$route['officer-armed-force-submit.html']['post'] = "officer/armed_force_add_submit";
$route['officer-edit-armed-force.html']['get'] = "officer/armed_force_edit";
$route['officer-edit-armed-force-validate.html']['post'] = "officer/armed_force_edit_validate/doError";
$route['officer-edit-armed-force-submit.html']['post'] = "officer/armed_force_edit_submit";

$route['officer-add-government-officials.html']['get'] = "officer/government_officials";
$route['officer-government-officials-validate.html']['post'] = "officer/government_officials_add_validate/doError";
$route['officer-government-officials-submit.html']['post'] = "officer/government_officials_add_submit";
$route['officer-edit-government-officials.html']['get'] = "officer/government_officials_edit";
$route['officer-edit-government-officials-validate.html']['post'] = "officer/government_officials_edit_validate/doError";
$route['officer-edit-government-officials-submit.html']['post'] = "officer/government_officials_edit_submit";

$route['officer-add-organizer.html']['get'] = "officer/organizer";
$route['officer-organizer-validate.html']['post'] = "officer/organizer_add_validate/doError";
$route['officer-organizer-submit.html']['post'] = "officer/organizer_add_submit";
$route['officer-edit-organizer.html']['get'] = "officer/organizer_edit";
$route['officer-edit-organizer-validate.html']['post'] = "officer/organizer_edit_validate/doError";
$route['officer-edit-organizer-submit.html']['post'] = "officer/organizer_edit_submit";
#E officer

#S Sms Notification
$route['sms-send-to-companies']['get'] = "sms_notification/crd_add";
$route['sms-send-to-companies-add-validate']['post'] = "sms_notification/crd_add_validate/doError";
$route['sms-send-to-companies-add-submit']['post'] = "sms_notification/crd_add_submit";

$route['sms-individual-send']['get'] = "sms_notification/crd_add_individual";
$route['sms-individual-send-add-validate']['post'] = "sms_notification/crd_add_validate_individual/doError";
$route['sms-individual-send-add-submit']['post'] = "sms_notification/crd_add_submit_individual";
#E Sms Notification


#S user
$route['organizer.html']['get'] = "organizer/crd_list";
$route['organizer-datatable.html']['post'] = "organizer/crd_list_datatable";

$route['organizer-add.html']['get'] = "organizer/crd_add";
$route['organizer-validate.html']['post'] = "organizer/crd_add_validate/doError";
$route['organizer-submit.html']['post'] = "organizer/crd_add_submit";

$route['organizer-edit.html']['get'] = "organizer/crd_edit";
$route['organizer-edit-validate.html']['post'] = "organizer/crd_edit_validate/doError";
$route['organizer-edit-submit.html']['post'] = "organizer/crd_edit_submit";

$route['organizer-delete.html']['get'] = "organizer/crd_delete";
#E user


#S stall builder
$route['stall-builder.html']['get'] = "stall_builder/crd_list";
$route['stall-builder-datatable.html']['post'] = "stall_builder/crd_list_datatable";

$route['stall-builder-add.html']['get'] = "stall_builder/crd_add";
$route['stall-builder-validate.html']['post'] = "stall_builder/crd_add_validate/doError";
$route['stall-builder-submit.html']['post'] = "stall_builder/crd_add_submit";

$route['stall-builder-edit.html']['get'] = "stall_builder/crd_edit";
$route['stall-builder-edit-validate.html']['post'] = "stall_builder/crd_edit_validate/doError";
$route['stall-builder-edit-submit.html']['post'] = "stall_builder/crd_edit_submit";

$route['stall-builder-delete.html']['get'] = "stall_builder/crd_delete";
#E stall builder

#S stalls
$route['stalls.html']['get'] = "stalls/crd_list";
$route['stalls-datatable.html']['post'] = "stalls/crd_list_datatable";

$route['stalls-list.html']['get'] = "stalls/stall_list";
$route['stalls-list-datatable.html']['post'] = "stalls/stall_list_datatable";

$route['stalls-add.html']['get'] = "stalls/crd_add";
$route['stalls-validate.html']['post'] = "stalls/crd_add_validate/doError";
$route['stalls-submit.html']['post'] = "stalls/crd_add_submit";

$route['stalls-edit.html']['get'] = "stalls/crd_edit";
$route['stalls-edit-validate.html']['post'] = "stalls/crd_edit_validate/doError";
$route['stalls-edit-submit.html']['post'] = "stalls/crd_edit_submit";

$route['stalls-delete.html']['get'] = "stalls/crd_delete";
#E stalls

#S event forms
$route['event_form.html']['get'] = "event_form/crd_list";
$route['event_form-datatable.html']['post'] = "event_form/crd_list_datatable";

$route['event_form-add.html']['get'] = "event_form/crd_add";
$route['event_form-validate.html']['post'] = "event_form/crd_add_validate/doError";
$route['event_form-submit.html']['post'] = "event_form/crd_add_submit";
#E event forms


#S Form Status And Reports
$route['form_status.html']['get'] = "reports/exhibitors_report/crd_list";
$route['form_status-datatable.html']['post'] = "reports/exhibitors_report/crd_list_datatable";

$route['exhibitions_form_status-list.html']['get'] = "reports/exhibitors_report/crd_exhibition_list";
$route['exhibitions_form_status-datatable.html'] = "reports/exhibitors_report/crd_exhibition_list_datatable";

$route['form_status_2.html']['get'] = "reports/status_report/crd_list";
$route['form_status_2-datatable.html']['post'] = "reports/status_report/crd_list_datatable";

$route['exhibitions_form_status_2-list.html']['get'] = "reports/status_report/crd_exhibition_list";
$route['exhibitions_form_status_2-datatable.html']['post'] = "reports/status_report/crd_exhibition_list_datatable";

$route['stall_builders.html']['get'] = "reports/stall_builders/crd_list";
$route['stall_builders-datatable.html']['post'] = "reports/stall_builders/crd_list_datatable";

$route['stall_builders_report-list.html']['get'] = "reports/stall_builders/crd_exhibition_list";
$route['stall_builders_report-datatable.html'] = "reports/stall_builders/crd_exhibition_list_datatable";

$route['fascia.html']['get'] = "reports/fascia_report/crd_list";
$route['fascia-datatable.html']['post'] = "reports/fascia_report/crd_list_datatable";

$route['fascia_report-list.html']['get'] = "reports/fascia_report/crd_exhibition_list";
$route['fascia_report-datatable.html'] = "reports/fascia_report/crd_exhibition_list_datatable";


$route['end_user_certificate.html']['get'] = "reports/end_user_certificate_report/crd_list";
$route['end_user_certificate-datatable.html']['post'] = "reports/end_user_certificate_report/crd_list_datatable";

$route['end_user_certificate_report-list.html']['get'] = "reports/end_user_certificate_report/crd_exhibition_list";
$route['end_user_certificate_report-datatable.html'] = "reports/end_user_certificate_report/crd_exhibition_list_datatable";

$route['visa_to_pakistan.html']['get'] = "reports/visa_to_pakistan_report/crd_list";
$route['visa_to_pakistan-datatable.html']['post'] = "reports/visa_to_pakistan_report/crd_list_datatable";

$route['visa_to_pakistan_report-list.html']['get'] = "reports/visa_to_pakistan_report/crd_exhibition_list";

$route['branding.html']['get'] = "reports/branding_report/crd_list";
$route['branding-datatable.html']['post'] = "reports/branding_report/crd_list_datatable";

$route['branding_report-list.html']['get'] = "reports/branding_report/crd_exhibition_list";

$route['hotel_reservation.html']['get'] = "reports/hotel_reservation_report/crd_list";
$route['hotel_reservation-datatable.html']['post'] = "reports/hotel_reservation_report/crd_list_datatable";

$route['hotel_reservation_report-list.html']['get'] = "reports/hotel_reservation_report/crd_exhibition_list";
$route['hotel_reservation_report-datatable.html']['post'] = "reports/hotel_reservation_report/crd_exhibition_list_datatable";


$route['vehicle_rent.html']['get'] = "reports/vehicle_rent_report/crd_list";
$route['vehicle_rent-datatable.html']['post'] = "reports/vehicle_rent_report/crd_list_datatable";

$route['vehicle_rent_report-list.html']['get'] = "reports/vehicle_rent_report/crd_exhibition_list";
$route['vehicle_rent_report-datatable.html']['post'] = "reports/vehicle_rent_report/crd_exhibition_list_datatable";


$route['display_mobility.html']['get'] = "reports/display_mobility_report/crd_list";
$route['display_mobility-datatable.html']['post'] = "reports/display_mobility_report/crd_list_datatable";

$route['display_mobility_report-list.html']['get'] = "reports/display_mobility_report/crd_exhibition_list";
$route['display_mobility_report-datatable.html']['post'] = "reports/display_mobility_report/crd_exhibition_list_datatable";

$route['ecommerce.html']['get'] = "reports/ecommerce_report/crd_list";
$route['ecommerce-datatable.html']['post'] = "reports/ecommerce_report/crd_list_datatable";

$route['ecommerce_report-list.html']['get'] = "reports/ecommerce_report/crd_exhibition_list";
$route['ecommerce_report-datatable.html']['post'] = "reports/ecommerce_report/crd_exhibition_list_datatable";



#E Form Status And Reports

#S order list
$route['order_list.html']['get'] = "order_list/crd_list";
$route['order_list-datatable.html']['post'] = "order_list/crd_list_datatable";

$route['event-order-list.html']['get'] = "order_list/event_order_list";
$route['order-list-datatable.html']['post'] = "order_list/order_list_datatable";

$route['order_detail.html']['get'] = "order_list/order_detail";
$route['print_invoice.html']['get'] = "order_list/print_order_invoice";

$route['edit-order-submit.html']['post'] = "order_list/edit_order_submit";
$route['order-extended-form-submit.html']['post'] = "order_list/order_extended_form_submit";

$route['send-order-invitation-view.html']['get'] = "order_list/send_order_invitation_view";
$route['send-order-invitation-validate.html']['post'] = "order_list/send_order_invitation_validate/doError";
$route['send-order-invitation-submit.html']['post'] = "order_list/send_order_invitation_submit";
#E order list

#S event inventory
$route['event-inventory.html']['get'] = "event_inventory/crd_list";
$route['event-inventory-datatable.html']['post'] = "event_inventory/crd_list_datatable";

$route['event-inventory-view.html']['get'] = "event_inventory/crd_view";
$route['event-inventory-validate.html']['post'] = "event_inventory/crd_view_validate/doError";
$route['event-inventory-submit.html']['post'] = "event_inventory/crd_view_submit";
#E event inventory


#S book stall
$route['book-stall-exhibitions.html']['get'] = "book_stall/crd_list";
$route['book-stall-exhibitions-datatable.html']['post'] = "book_stall/crd_list_datatable";

$route['book-stall.html']['get'] = "book_stall/book";
$route['book-stall-submit.html']['post'] = "book_stall/book_submit";
#E book stall


#S Forms
$route['forms.html']['get'] = "forms/crd_list";
$route['forms-datatable.html']['post'] = "forms/crd_list_datatable";

$route['forms-edit.html']['get'] = "forms/crd_edit";
$route['forms-edit-validate.html']['post'] = "forms/crd_edit_validate/doError";
$route['forms-edit-submit.html']['post'] = "forms/crd_edit_submit";

$route['forms-view.html']['get'] = "forms/crd_view";
#E Forms

#S discounts
$route['discount.html']['get'] = "discount/crd_list";
$route['discount-datatable.html']['post'] = "discount/crd_list_datatable";

$route['discount-add.html']['get'] = "discount/crd_add";
$route['discount-validate.html']['post'] = "discount/crd_add_validate/doError";
$route['discount-submit.html']['post'] = "discount/crd_add_submit";

$route['discount-edit.html']['get'] = "discount/crd_edit";
$route['discount-edit-validate.html']['post'] = "discount/crd_edit_validate/doError";
$route['discount-edit-submit.html']['post'] = "discount/crd_edit_submit";

$route['discount-delete.html']['get'] = "discount/crd_delete";
#E discounts


#ENHANCED LIST
$route['enhanced.html']['get'] = "Enhanced_list/crd_list";
$route['enhanced-datatable.html']['post'] = "Enhanced_list/crd_list_datatable";

$route['enhanced-order-list.html']['get'] = "Enhanced_list/enhanced_order_list";
$route['enhanced-order-list-datatable.html']['post'] = "Enhanced_list/enhanced_order_list_datatable";
$route['enhanced-order-list-view.html']['get'] = "Enhanced_list/enhanced_order_list_view";

$route['enhanced-order-approve.html']['post'] = "Enhanced_list/enhanced_order_approve";
$route['enhanced-order-cancel.html']['post'] = "Enhanced_list/enhanced_order_cancel";
#ENHANCED LIST

$route['other-settings.html']['get'] = "settings/view_settings";
$route['tax-update.html']['get'] = "settings/tax_update";
$route['tex-update-validate.html']['post'] = "settings/crd_edit_validate/doError";
$route['tex-update-submit.html']['post'] = "settings/crd_edit_submit";


#S badges report
$route['badges-report-events.html']['get'] = "reports/badges_report/crd_list";
$route['badges-report-events-datatable.html']['post'] = "reports/badges_report/crd_list_datatable";
$route['badges-report.html']['get'] = "reports/badges_report/crd_badge_list";
$route['badges-report-datatable.html'] = "reports/badges_report/crd_badge_list_datatable";
$route['badges-edit.html']['get'] = "reports/badges_report/badges_edit";
$route['badges-edit-validation.html']['post'] = "reports/badges_report/badge_edit_validate/doError";
$route['badges-edit-submit.html']['post'] = "reports/badges_report/badge_edit_submit";
$route['view-badges.html']['get'] = "reports/badges_report/print_badge";
$route['hold-badges.html']['get'] = "reports/badges_report/hold_badge";
$route['reset-badges-print.html']['get'] = "reports/badges_report/reset_badge_print";
$route['invitation-report.html']['get'] = "reports/badges_report/crd_invitation_list";
$route['invitation-report-datatable.html'] = "reports/badges_report/crd_invitation_list_datatable";
#E badges report

$route['meeting-report.html']['get'] = "reports/meeting_report/crd_list";
$route['meeting-report-datatable.html']['post'] = "reports/meeting_report/crd_list_datatable";

$route['meeting-report_status-list.html']['get'] = "reports/meeting_report/crd_exhibition_list";
$route['meeting-report_status-datatable.html'] = "reports/meeting_report/crd_exhibition_list_datatable";
$route['meeting-report_status-details.html'] = "reports/meeting_report/get_meeting_details_ajax";


$route['show_catalogue-report.html']['get'] = "reports/show_catalogue_report/crd_list";
$route['show_catalogue-report-datatable.html']['post'] = "reports/show_catalogue_report/crd_list_datatable";

$route['show_catalogue_status-report-list.html']['get'] = "reports/show_catalogue_report/crd_exhibition_list";
$route['show_catalogue_status-report-datatable.html'] = "reports/show_catalogue_report/crd_exhibition_list_datatable";

$route['show_business_sector-report-list.html']['get'] = "reports/show_catalogue_report/business_sector_list";
$route['show_product-report-list.html']['get'] = "reports/show_catalogue_report/product_list";

$route['show_product-report-export']['get'] = "reports/show_catalogue_report/product_report_export";
$route['show_business_sector-report-export']['get'] = "reports/show_catalogue_report/business_sector_report_export";
$route['show_catalogue-report-export']['get'] = "reports/show_catalogue_report/catalogue_report_export";

#S Badge scan
$route['badge-scan.html']['get'] = "badges_scan/scan";
$route['badge-scan_id.html']['post'] = "badges_scan/scan_id";
#E Badge scan


#S Trade Visitor Badges
$route['trade-visitor.html']['get'] = "trade_visitor/exhibitor_crd_list";
$route['trade-visitor-exhibitor-datatable.html']['post'] = "trade_visitor/exhibitor_crd_list_datatable";

$route['trade-visitor-badges.html']['get'] = "trade_visitor/crd_list";
$route['trade-visitor-badges-datatable.html'] = "trade_visitor/crd_list_datatable";

$route['trade-visitor-add.html']['get'] = "trade_visitor/crd_add";
$route['trade-visitor-validate.html']['post'] = "trade_visitor/crd_add_validate/doError";
$route['trade-visitor-submit.html']['post'] = "trade_visitor/crd_add_submit";

$route['trade-visitor-edit.html']['get'] = "trade_visitor/crd_edit";
$route['trade-visitor-edit-validate.html']['post'] = "trade_visitor/crd_edit_validate/doError";
$route['trade-visitor-edit-submit.html']['post'] = "trade_visitor/crd_edit_submit";

$route['trade-visitor-delete.html']['get'] = "trade_visitor/crd_delete";
#E Trade Visitor Badges

#Mou area

$route['mou_sign.html']['get'] = "mou_report/crd_list";
$route['mou_sign-datatable.html'] = "mou_report/crd_list_datatable";
$route['mou_sign-cancel.html']['get'] = "mou_report/cancel";
$route['mou_sign-approved.html']['get'] = "mou_report/approved";
$route['mou_sign_re_schedule.html']['get'] = "mou_report/schedule";
$route['mou_re_schedule_validate.html']['post'] = "mou_report/mou_re_schedule_validate/doError";
$route['mou_re_schedule_submit.html']['post'] = "mou_report/mou_re_schedule_submit";

