<?php

declare(strict_types=1);

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Authentication Configuration
 * 
 * This file contains configuration settings for the multi-guard authentication system.
 * 
 * @package     CodeIgniter
 * @subpackage  Config
 * @category    Authentication
 * @author      Your Name
 * @link        https://codeigniter.com/userguide3/libraries/authentication.html
 */

/*
|--------------------------------------------------------------------------
| Default Guard
|--------------------------------------------------------------------------
|
| This option controls the default authentication guard that will be used
| by the framework. You may set this to any of the guards defined in the
| "guards" array below.
|
*/
$config['auth_default_guard'] = 'session';

/*
|--------------------------------------------------------------------------
| Guards
|--------------------------------------------------------------------------
|
| Here you may define every authentication guard for your application.
| Of course, a great default configuration has been defined for you
| here which uses session storage and the Eloquent user provider.
|
| All authentication drivers have a user provider. This defines how the
| users are actually retrieved out of your database or other storage
| mechanisms used by this application to persist your user's data.
|
| Supported: "session", "jwt", "api", "sanctum"
|
*/
$config['auth_guards'] = array(
    'session' => array(
        'driver' => 'session',
        'provider' => 'users',
    ),
    'jwt' => array(
        'driver' => 'jwt',
        'provider' => 'users',
        'expire' => 3600, // 1 hour
        'refresh_expire' => 604800, // 7 days
    ),
    'api' => array(
        'driver' => 'api',
        'provider' => 'users',
        'expire' => 3600, // 1 hour
    ),
    'sanctum' => array(
        'driver' => 'sanctum',
        'provider' => 'users',
        'expire' => 3600, // 1 hour
    ),
);

/*
|--------------------------------------------------------------------------
| User Providers
|--------------------------------------------------------------------------
|
| All authentication drivers have a user provider. This defines how the
| users are actually retrieved out of your database or other storage
| mechanisms used by this application to persist your user's data.
|
| If you have multiple user tables or models you may configure multiple
| sources which represent each model / table. These sources may then
| be assigned to any extra authentication guards you have defined.
|
| Supported: "database", "eloquent"
|
*/
$config['auth_providers'] = array(
    'users' => array(
        'driver' => 'database',
        'table' => 'users',
        'model' => 'User_model',
    ),
    'admins' => array(
        'driver' => 'database',
        'table' => 'admins',
        'model' => 'Admin_model',
    ),
);

/*
|--------------------------------------------------------------------------
| Password Hashing
|--------------------------------------------------------------------------
|
| Here you may specify the configuration options for the password hashing
| service used by your application. By default, we use the bcrypt algorithm
| which is a great default choice for security and usability.
|
| Supported: "bcrypt", "argon2i", "argon2id"
|
*/
$config['auth_password_hash'] = 'bcrypt';
$config['auth_password_rounds'] = 12;

/*
|--------------------------------------------------------------------------
| Password Reset
|--------------------------------------------------------------------------
|
| Here you may configure the password reset settings for your application.
| You may specify the name of the table that holds the reset tokens as
| well as the expiration time for the tokens.
|
*/
$config['auth_password_reset_table'] = 'password_resets';
$config['auth_password_reset_expire'] = 3600; // 1 hour

/*
|--------------------------------------------------------------------------
| Remember Me
|--------------------------------------------------------------------------
|
| Here you may configure the "remember me" functionality for your application.
| You may specify the name of the table that holds the remember tokens as
| well as the expiration time for the tokens.
|
*/
$config['auth_remember_table'] = 'remember_tokens';
$config['auth_remember_expire'] = 2628000; // 1 month

/*
|--------------------------------------------------------------------------
| Two Factor Authentication
|--------------------------------------------------------------------------
|
| Here you may configure the two factor authentication settings for your
| application. You may specify whether 2FA is enabled and which methods
| are supported.
|
*/
$config['auth_2fa_enabled'] = TRUE;
$config['auth_2fa_methods'] = array('totp', 'sms', 'email');
$config['auth_2fa_table'] = 'two_factor_auth';

/*
|--------------------------------------------------------------------------
| Rate Limiting
|--------------------------------------------------------------------------
|
| Here you may configure the rate limiting settings for authentication
| attempts. This helps prevent brute force attacks on your application.
|
*/
$config['auth_rate_limit'] = array(
    'enabled' => TRUE,
    'max_attempts' => 5,
    'decay_minutes' => 15,
    'table' => 'auth_attempts',
);

/*
|--------------------------------------------------------------------------
| Session Configuration
|--------------------------------------------------------------------------
|
| Here you may configure the session settings for the authentication system.
| These settings are used when the session guard is active.
|
*/
$config['auth_session'] = array(
    'lifetime' => 120, // 2 hours
    'expire_on_close' => FALSE,
    'encrypt' => TRUE,
    'files' => APPPATH . 'cache/sessions/',
    'cookie' => 'ci_session',
    'path' => '/',
    'domain' => '',
    'secure' => FALSE,
    'httponly' => TRUE,
    'samesite' => 'Lax',
);

/*
|--------------------------------------------------------------------------
| JWT Configuration
|--------------------------------------------------------------------------
|
| Here you may configure the JWT settings for the authentication system.
| These settings are used when the JWT guard is active.
|
*/
$config['auth_jwt'] = array(
    'secret' => 'your-secret-key-change-this-in-production',
    'algorithm' => 'HS256',
    'issuer' => 'your-app-name',
    'audience' => 'your-app-users',
    'expire' => 3600, // 1 hour
    'refresh_expire' => 604800, // 7 days
    'leeway' => 60, // 1 minute
);

/*
|--------------------------------------------------------------------------
| API Configuration
|--------------------------------------------------------------------------
|
| Here you may configure the API authentication settings.
| These settings are used when the API guard is active.
|
*/
$config['auth_api'] = array(
    'token_length' => 64,
    'expire' => 3600, // 1 hour
    'table' => 'api_tokens',
);

/*
|--------------------------------------------------------------------------
| Sanctum Configuration
|--------------------------------------------------------------------------
|
| Here you may configure the Sanctum authentication settings.
| These settings are used when the Sanctum guard is active.
|
*/
$config['auth_sanctum'] = array(
    'token_length' => 64,
    'expire' => 3600, // 1 hour
    'table' => 'personal_access_tokens',
    'abilities' => array('*'), // All abilities by default
);

/*
|--------------------------------------------------------------------------
| Social Authentication
|--------------------------------------------------------------------------
|
| Here you may configure the social authentication providers.
| These settings are used for OAuth authentication.
|
*/
$config['auth_social'] = array(
    'enabled' => TRUE,
    'providers' => array(
        'google' => array(
            'client_id' => '',
            'client_secret' => '',
            'redirect' => 'auth/callback/google',
        ),
        'facebook' => array(
            'client_id' => '',
            'client_secret' => '',
            'redirect' => 'auth/callback/facebook',
        ),
        'github' => array(
            'client_id' => '',
            'client_secret' => '',
            'redirect' => 'auth/callback/github',
        ),
    ),
);

/*
|--------------------------------------------------------------------------
| Email Verification
|--------------------------------------------------------------------------
|
| Here you may configure the email verification settings.
|
*/
$config['auth_email_verification'] = array(
    'enabled' => TRUE,
    'expire' => 3600, // 1 hour
    'table' => 'email_verifications',
    'template' => 'auth/verify_email',
);

/*
|--------------------------------------------------------------------------
| Account Lockout
|--------------------------------------------------------------------------
|
| Here you may configure the account lockout settings.
|
*/
$config['auth_lockout'] = array(
    'enabled' => TRUE,
    'max_attempts' => 5,
    'lockout_time' => 900, // 15 minutes
    'table' => 'account_lockouts',
);

/*
|--------------------------------------------------------------------------
| Logging
|--------------------------------------------------------------------------
|
| Here you may configure the authentication logging settings.
|
*/
$config['auth_logging'] = array(
    'enabled' => TRUE,
    'log_attempts' => TRUE,
    'log_logins' => TRUE,
    'log_logouts' => TRUE,
    'log_failures' => TRUE,
    'log_password_resets' => TRUE,
    'log_email_verifications' => TRUE,
);

/*
|--------------------------------------------------------------------------
| Security Headers
|--------------------------------------------------------------------------
|
| Here you may configure the security headers for authentication.
|
*/
$config['auth_security_headers'] = array(
    'x_frame_options' => 'DENY',
    'x_content_type_options' => 'nosniff',
    'x_xss_protection' => '1; mode=block',
    'strict_transport_security' => 'max-age=31536000; includeSubDomains',
    'content_security_policy' => "default-src 'self'",
);

/*
|--------------------------------------------------------------------------
| Multi-Factor Authentication
|--------------------------------------------------------------------------
|
| Here you may configure the multi-factor authentication settings.
|
*/
$config['auth_mfa'] = array(
    'enabled' => TRUE,
    'required' => FALSE, // Set to TRUE to require MFA for all users
    'methods' => array('totp', 'sms', 'email', 'backup_codes'),
    'backup_codes_count' => 10,
    'totp_issuer' => 'Your App Name',
    'totp_algorithm' => 'sha1',
    'totp_digits' => 6,
    'totp_period' => 30,
);

/*
|--------------------------------------------------------------------------
| Device Management
|--------------------------------------------------------------------------
|
| Here you may configure the device management settings.
|
*/
$config['auth_devices'] = array(
    'enabled' => TRUE,
    'max_devices' => 5,
    'table' => 'user_devices',
    'trusted_devices' => TRUE,
    'device_fingerprinting' => TRUE,
);

/*
|--------------------------------------------------------------------------
| Audit Trail
|--------------------------------------------------------------------------
|
| Here you may configure the audit trail settings.
|
*/
$config['auth_audit'] = array(
    'enabled' => TRUE,
    'table' => 'auth_audit_log',
    'log_ip' => TRUE,
    'log_user_agent' => TRUE,
    'log_events' => array(
        'login',
        'logout',
        'password_change',
        'email_verification',
        'two_factor_enabled',
        'two_factor_disabled',
        'device_added',
        'device_removed',
    ),
);
