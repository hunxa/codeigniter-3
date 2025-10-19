<?php

declare(strict_types=1);

defined('BASEPATH') or exit('No direct script access allowed');

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
|	https://codeigniter.com/userguide3/general/routing.html
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
| When you set this option to TRUE, it will replace ALL dashes with
| underscores in the controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = false;

/*
|--------------------------------------------------------------------------
| AUTHENTICATION ROUTES
|--------------------------------------------------------------------------
| Routes for user authentication, registration, password reset, etc.
*/

// Authentication Routes
$route['auth'] = 'AuthController/login';
$route['auth/login'] = 'AuthController/login';
$route['auth/register'] = 'AuthController/register';
$route['auth/logout'] = 'AuthController/logout';

// CSRF Token Routes
$route['auth/refresh-csrf'] = 'AuthController/refresh_csrf';

// Password Reset Routes
$route['auth/forgot-password'] = 'AuthController/forgot_password';
$route['auth/reset-password'] = 'AuthController/reset_password';
$route['auth/reset-password/(:any)'] = 'AuthController/reset_password/$1';

// Two-Factor Authentication Routes
$route['auth/2fa'] = 'AuthController/two_factor';
$route['auth/2fa/verify'] = 'AuthController/verify_2fa';
$route['auth/2fa/backup'] = 'AuthController/backup_codes';
$route['auth/2fa/setup'] = 'AuthController/setup_2fa';
$route['auth/2fa/disable'] = 'AuthController/disable_2fa';

// Email Verification Routes
$route['auth/verify-email'] = 'AuthController/verify_email';
$route['auth/verify-email/(:any)'] = 'AuthController/verify_email/$1';
$route['auth/resend-verification'] = 'AuthController/resend_verification';

// Social Authentication Routes
$route['auth/social/(:any)'] = 'AuthController/social_login/$1';
$route['auth/social/(:any)/callback'] = 'AuthController/social_callback/$1';
$route['auth/social/status'] = 'AuthController/social_status';

// API Authentication Routes
$route['auth/api/login'] = 'AuthController/api_login';
$route['auth/api/register'] = 'AuthController/api_register';
$route['auth/api/refresh'] = 'AuthController/api_refresh';
$route['auth/api/logout'] = 'AuthController/api_logout';

// Process Routes (for form submissions)
$route['auth/process_login'] = 'AuthController/process_login';
$route['auth/process_register'] = 'AuthController/process_register';
$route['auth/process_forgot_password'] = 'AuthController/process_forgot_password';
$route['auth/process_reset_password'] = 'AuthController/process_reset_password';
$route['auth/process_2fa'] = 'AuthController/process_2fa';
$route['auth/resend-reset-email'] = 'AuthController/resend_reset_email';

/*
|--------------------------------------------------------------------------
| DASHBOARD ROUTES
|--------------------------------------------------------------------------
| Routes for the main dashboard and user area
*/

// Dashboard Routes
$route['dashboard'] = 'dashboard/index';
$route['dashboard/(:any)'] = 'dashboard/$1';

// Profile Routes
$route['profile'] = 'profile/index';
$route['profile/edit'] = 'profile/edit';
$route['profile/update'] = 'profile/update';
$route['profile/change-password'] = 'profile/change_password';
$route['profile/update-password'] = 'profile/update_password';
$route['profile/delete-account'] = 'profile/delete_account';

// Settings Routes
$route['settings'] = 'settings/index';
$route['settings/account'] = 'settings/account';
$route['settings/security'] = 'settings/security';
$route['settings/notifications'] = 'settings/notifications';
$route['settings/privacy'] = 'settings/privacy';
$route['settings/update'] = 'settings/update';

// Admin Routes
$route['admin'] = 'admin/index';
$route['admin/dashboard'] = 'admin/dashboard';
$route['admin/users'] = 'admin/users';
$route['admin/users/(:any)'] = 'admin/users/$1';
$route['admin/roles'] = 'admin/roles';
$route['admin/roles/(:any)'] = 'admin/roles/$1';
$route['admin/permissions'] = 'admin/permissions';
$route['admin/permissions/(:any)'] = 'admin/permissions/$1';
$route['admin/settings'] = 'admin/settings';
$route['admin/settings/(:any)'] = 'admin/settings/$1';
$route['admin/logs'] = 'admin/logs';
$route['admin/logs/(:any)'] = 'admin/logs/$1';

/*
|--------------------------------------------------------------------------
| API ROUTES
|--------------------------------------------------------------------------
| Routes for API endpoints
*/

// API Routes
$route['api'] = 'api/index';
$route['api/v1'] = 'api/v1/index';
$route['api/v1/auth'] = 'api/v1/auth/index';
$route['api/v1/auth/login'] = 'api/v1/auth/login';
$route['api/v1/auth/register'] = 'api/v1/auth/register';
$route['api/v1/auth/logout'] = 'api/v1/auth/logout';
$route['api/v1/auth/refresh'] = 'api/v1/auth/refresh';
$route['api/v1/auth/me'] = 'api/v1/auth/me';

// API User Routes
$route['api/v1/users'] = 'api/v1/users/index';
$route['api/v1/users/(:num)'] = 'api/v1/users/show/$1';
$route['api/v1/users/(:num)/update'] = 'api/v1/users/update/$1';
$route['api/v1/users/(:num)/delete'] = 'api/v1/users/delete/$1';

// API Profile Routes
$route['api/v1/profile'] = 'api/v1/profile/index';
$route['api/v1/profile/update'] = 'api/v1/profile/update';
$route['api/v1/profile/change-password'] = 'api/v1/profile/change_password';

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
| Routes for public pages
*/

// Public Pages
$route['about'] = 'pages/about';
$route['contact'] = 'pages/contact';
$route['privacy'] = 'pages/privacy';
$route['terms'] = 'pages/terms';
$route['help'] = 'pages/help';
$route['faq'] = 'pages/faq';
$route['support'] = 'pages/support';
$route['status'] = 'pages/status';
$route['cookies'] = 'pages/cookies';
$route['security'] = 'pages/security';

// Services Pages
$route['services'] = 'pages/services';
$route['services/(:any)'] = 'pages/services/$1';

// Blog Routes (if you have a blog)
$route['blog'] = 'blog/index';
$route['blog/(:any)'] = 'blog/show/$1';
$route['blog/category/(:any)'] = 'blog/category/$1';
$route['blog/tag/(:any)'] = 'blog/tag/$1';

/*
|--------------------------------------------------------------------------
| WEBHOOK ROUTES
|--------------------------------------------------------------------------
| Routes for webhooks and external integrations
*/

// Webhook Routes
$route['webhooks'] = 'webhooks/index';
$route['webhooks/(:any)'] = 'webhooks/$1';

// Payment Webhooks
$route['webhooks/stripe'] = 'webhooks/stripe';
$route['webhooks/paypal'] = 'webhooks/paypal';

// Social Media Webhooks
$route['webhooks/facebook'] = 'webhooks/facebook';
$route['webhooks/twitter'] = 'webhooks/twitter';
$route['webhooks/linkedin'] = 'webhooks/linkedin';

/*
|--------------------------------------------------------------------------
| AJAX ROUTES
|--------------------------------------------------------------------------
| Routes for AJAX requests
*/

// AJAX Authentication Routes
$route['ajax/auth/check'] = 'ajax/auth/check';
$route['ajax/auth/validate'] = 'ajax/auth/validate';
$route['ajax/auth/social'] = 'ajax/auth/social';

// AJAX User Routes
$route['ajax/users/search'] = 'ajax/users/search';
$route['ajax/users/update'] = 'ajax/users/update';
$route['ajax/users/delete'] = 'ajax/users/delete';

// AJAX Profile Routes
$route['ajax/profile/update'] = 'ajax/profile/update';
$route['ajax/profile/upload-avatar'] = 'ajax/profile/upload_avatar';
$route['ajax/profile/remove-avatar'] = 'ajax/profile/remove_avatar';

// AJAX Settings Routes
$route['ajax/settings/update'] = 'ajax/settings/update';
$route['ajax/settings/notifications'] = 'ajax/settings/notifications';

// AJAX Dashboard Routes
$route['ajax/dashboard/stats'] = 'ajax/dashboard/stats';
$route['ajax/dashboard/notifications'] = 'ajax/dashboard/notifications';
$route['ajax/dashboard/activities'] = 'ajax/dashboard/activities';

/*
|--------------------------------------------------------------------------
| COMMAND ROUTES
|--------------------------------------------------------------------------
| Routes for CLI commands and background jobs
*/

// Command Routes
$route['commands'] = 'commands/index';
$route['commands/(:any)'] = 'commands/$1';

// Job Routes
$route['jobs'] = 'jobs/index';
$route['jobs/(:any)'] = 'jobs/$1';
$route['jobs/queue/(:any)'] = 'jobs/queue/$1';

/*
|--------------------------------------------------------------------------
| MAINTENANCE ROUTES
|--------------------------------------------------------------------------
| Routes for maintenance and system management
*/

// Maintenance Routes
$route['maintenance'] = 'maintenance/index';
$route['maintenance/upgrade'] = 'maintenance/upgrade';
$route['maintenance/backup'] = 'maintenance/backup';
$route['maintenance/restore'] = 'maintenance/restore';

// System Routes
$route['system'] = 'system/index';
$route['system/health'] = 'system/health';
$route['system/logs'] = 'system/logs';
$route['system/clear-cache'] = 'system/clear_cache';
$route['system/optimize'] = 'system/optimize';

/*
|--------------------------------------------------------------------------
| REDIRECT ROUTES
|--------------------------------------------------------------------------
| Routes for common redirects
*/

// Common Redirects
$route['login'] = 'auth/login';
$route['register'] = 'auth/register';
$route['signup'] = 'auth/register';
$route['signin'] = 'auth/login';
$route['logout'] = 'auth/logout';
$route['signout'] = 'auth/logout';

// Dashboard Redirects
$route['home'] = 'dashboard';
$route['account'] = 'profile';
$route['user'] = 'profile';
$route['admin-panel'] = 'admin';
$route['admin-panel/(:any)'] = 'admin/$1';

/*
|--------------------------------------------------------------------------
| LEGACY ROUTES
|--------------------------------------------------------------------------
| Routes for backward compatibility
*/

// Legacy Authentication Routes
$route['user/login'] = 'auth/login';
$route['user/register'] = 'auth/register';
$route['user/logout'] = 'auth/logout';
$route['user/profile'] = 'profile';

// Legacy Admin Routes
$route['admin-panel'] = 'admin';
$route['admin-panel/(:any)'] = 'admin/$1';
