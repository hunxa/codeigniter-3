<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Authentication Library
 * 
 * A robust multi-guard authentication system for CodeIgniter 3.
 * Supports multiple authentication methods including session, JWT, API, and Sanctum.
 * 
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Authentication
 * @author      Your Name
 * @link        https://codeigniter.com/userguide3/libraries/authentication.html
 */
class Auth
{
    /**
     * CI instance
     *
     * @var object
     */
    protected $CI;

    /**
     * Current guard instance
     *
     * @var object
     */
    protected $guard;

    /**
     * Default guard name
     *
     * @var string
     */
    protected $default_guard;

    /**
     * Available guards
     *
     * @var array
     */
    protected $guards = array();

    /**
     * User providers
     *
     * @var array
     */
    protected $providers = array();

    /**
     * Current guard name
     *
     * @var string|null
     */
    protected $current_guard;

    /**
     * Configuration
     *
     * @var array
     */
    protected $config = array();

    /**
     * Current user
     *
     * @var object|null
     */
    protected $user = null;

    /**
     * Class constructor
     *
     * @param array $config
     */
    public function __construct($config = array())
    {
        $this->CI = &get_instance();
        $this->CI->load->config('auth');
        $this->CI->load->library('session');
        $this->CI->load->database();

        // Load configuration items directly (they're prefixed with 'auth_')
        $this->default_guard = $this->CI->config->item('auth_default_guard');
        $this->guards = $this->CI->config->item('auth_guards');
        $this->providers = $this->CI->config->item('auth_providers');

        // Check if config loaded properly
        if (empty($this->default_guard) || empty($this->guards) || empty($this->providers)) {
            throw new Exception('Authentication configuration not found. Please check application/config/auth.php');
        }

        // Store full config for reference
        $this->config = array(
            'auth_default_guard' => $this->default_guard,
            'auth_guards' => $this->guards,
            'auth_providers' => $this->providers,
            'auth_password_hash' => $this->CI->config->item('auth_password_hash'),
            'auth_password_rounds' => $this->CI->config->item('auth_password_rounds'),
            'auth_email_verification' => $this->CI->config->item('auth_email_verification'),
            'auth_password_reset' => $this->CI->config->item('auth_password_reset'),
            'auth_session' => $this->CI->config->item('auth_session'),
            'auth_remember' => $this->CI->config->item('auth_remember'),
            'auth_two_factor' => $this->CI->config->item('auth_two_factor'),
            'auth_audit' => $this->CI->config->item('auth_audit'),
            'auth_security' => $this->CI->config->item('auth_security'),
            'auth_social' => $this->CI->config->item('auth_social'),
            'auth_notifications' => $this->CI->config->item('auth_notifications'),
            'auth_rate_limiting' => $this->CI->config->item('auth_rate_limiting'),
            'auth_captcha' => $this->CI->config->item('auth_captcha'),
            'auth_terms' => $this->CI->config->item('auth_terms'),
            'auth_privacy' => $this->CI->config->item('auth_privacy'),
            'auth_cookie' => $this->CI->config->item('auth_cookie'),
            'auth_csrf' => $this->CI->config->item('auth_csrf'),
            'auth_headers' => $this->CI->config->item('auth_headers'),
            'auth_middleware' => $this->CI->config->item('auth_middleware'),
            'auth_events' => $this->CI->config->item('auth_events'),
            'auth_guards' => $this->guards,
            'auth_providers' => $this->providers,
        );

        // Load all required interfaces and base classes
        $this->loadAuthDependencies();

        // Don't initialize guard immediately - let it be lazy loaded
        $this->current_guard = null;
    }

    /**
     * Load all authentication dependencies
     *
     * @return void
     */
    private function loadAuthDependencies()
    {
        // Load interfaces
        $interfaces = array(
            'Guard_interface' => 'libraries/auth/contracts/Guard_interface.php',
            'User_provider_interface' => 'libraries/auth/contracts/User_provider_interface.php',
        );

        foreach ($interfaces as $interface_name => $file_path) {
            if (!interface_exists($interface_name)) {
                $full_path = APPPATH . $file_path;
                if (file_exists($full_path)) {
                    require_once($full_path);
                } else {
                    throw new Exception("Interface file not found: {$full_path}");
                }
            }
        }

        // Load base classes
        $base_classes = array(
            'Base_guard' => 'libraries/auth/guards/Base_guard.php',
            'Database_provider' => 'libraries/auth/providers/Database_provider.php',
        );

        foreach ($base_classes as $class_name => $file_path) {
            if (!class_exists($class_name)) {
                $full_path = APPPATH . $file_path;
                if (file_exists($full_path)) {
                    require_once($full_path);
                } else {
                    throw new Exception("Base class file not found: {$full_path}");
                }
            }
        }
    }

    /**
     * Get a guard instance
     *
     * @param string|null $guard
     * @return object
     */
    public function guard($guard = null)
    {
        $guard = $guard ?: $this->default_guard;

        if (empty($this->guards) || !isset($this->guards[$guard])) {
            throw new Exception('Authentication guard [' . $guard . '] is not defined.');
        }

        // If guard is already instantiated, return it
        if (is_object($this->guards[$guard])) {
            return $this->guards[$guard];
        }

        $guard_config = $this->guards[$guard];
        $driver = $guard_config['driver'];

        // Load the guard driver (dependencies already loaded)
        $guard_class = 'Auth_' . ucfirst($driver) . '_guard';

        if (!class_exists($guard_class)) {
            $guard_file = APPPATH . 'libraries/auth/guards/' . $driver . '_guard.php';
            if (file_exists($guard_file)) {
                require_once($guard_file);
            } else {
                throw new Exception("Guard driver file not found: {$guard_file}");
            }
        }

        $this->guard = new $guard_class($guard_config, $this->providers[$guard_config['provider']]);

        return $this->guard;
    }

    /**
     * Attempt to authenticate a user
     *
     * @param array $credentials
     * @param bool $remember
     * @param string|null $guard
     * @return bool
     */
    public function attempt($credentials, $remember = false, $guard = null)
    {
        $guard = $this->guard($guard);

        // Check rate limiting
        if ($this->isRateLimited($credentials)) {
            return false;
        }

        // Validate credentials
        if (!$this->validateCredentials($credentials)) {
            $this->logFailedAttempt($credentials);
            return false;
        }

        // Get user from provider
        $user = $this->getUserProvider($guard->getProvider())->retrieveByCredentials($credentials);

        if (!$user) {
            $this->logFailedAttempt($credentials);
            return false;
        }

        // Validate password
        if (!$this->validatePassword($user, $credentials)) {
            $this->logFailedAttempt($credentials);
            return false;
        }

        // Check if user is active
        if (!$this->isUserActive($user)) {
            $this->logFailedAttempt($credentials);
            return false;
        }

        // Check two-factor authentication
        if ($this->requiresTwoFactor($user)) {
            $this->initiateTwoFactor($user);
            return false;
        }

        // Login the user
        $this->login($user, $remember, $guard);

        // Log successful attempt
        $this->logSuccessfulAttempt($user);

        return true;
    }

    /**
     * Login a user
     *
     * @param mixed $user
     * @param bool $remember
     * @param string|null $guard
     * @return void
     */
    public function login($user, $remember = false, $guard = null)
    {
        $guard = $this->guard($guard);

        // Set the user
        $this->user = $user;

        // Login via guard
        $guard->login($user, $remember);

        // Update last login
        $this->updateLastLogin($user);

        // Log the login
        $this->logEvent('login', $user);

        // Fire login event
        $this->fireEvent('user.login', $user);
    }

    /**
     * Logout the current user
     *
     * @param string|null $guard
     * @return void
     */
    public function logout($guard = null)
    {
        $guard = $this->guard($guard);

        if ($this->check($guard)) {
            $user = $this->user($guard);

            // Logout via guard
            $guard->logout();

            // Log the logout
            $this->logEvent('logout', $user);

            // Fire logout event
            $this->fireEvent('user.logout', $user);
        }

        $this->user = null;
    }

    /**
     * Check if a user is authenticated
     *
     * @param string|null $guard
     * @return bool
     */
    public function check($guard = null)
    {
        $guard = $this->guard($guard);
        return $guard->check();
    }

    /**
     * Get the current user
     *
     * @param string|null $guard
     * @return mixed|null
     */
    public function user($guard = null)
    {
        $guard = $this->guard($guard);

        if ($this->user) {
            return $this->user;
        }

        if ($guard->check()) {
            $this->user = $guard->user();
        }

        return $this->user;
    }

    /**
     * Get the current user's ID
     *
     * @param string|null $guard
     * @return mixed|null
     */
    public function id($guard = null)
    {
        $user = $this->user($guard);
        return $user ? $user->id : null;
    }

    /**
     * Check if a user is a guest
     *
     * @param string|null $guard
     * @return bool
     */
    public function guest($guard = null)
    {
        return !$this->check($guard);
    }

    /**
     * Register a new user
     *
     * @param array $data
     * @param bool $auto_login
     * @param string|null $guard
     * @return mixed|false
     */
    public function register($data, $auto_login = false, $guard = null)
    {
        $guard = $this->guard($guard);

        // Validate registration data
        if (!$this->validateRegistration($data)) {
            return false;
        }

        // Hash password
        $data['password'] = $this->hashPassword($data['password']);

        // Create user
        $provider_name = $guard->getProviderName();
        $user = $this->getUserProvider($provider_name)->create($data);

        if (!$user) {
            return false;
        }

        // Send email verification if enabled
        if ($this->config['auth_email_verification']['enabled']) {
            $this->sendEmailVerification($user);
        }

        // Auto login if requested
        if ($auto_login) {
            $this->login($user, false, $guard);
        }

        // Log registration
        $this->logEvent('register', $user);

        // Fire registration event
        $this->fireEvent('user.register', $user);

        return $user;
    }

    /**
     * Reset password
     *
     * @param array $credentials
     * @return bool
     */
    public function resetPassword($credentials)
    {
        $user = $this->getUserProvider('users')->retrieveByCredentials($credentials);

        if (!$user) {
            return false;
        }

        // Generate reset token
        $token = $this->generateResetToken();

        // Store reset token
        $this->storeResetToken($user, $token);

        // Send reset email
        $this->sendPasswordResetEmail($user, $token);

        // Log password reset request
        $this->logEvent('password_reset_requested', $user);

        return true;
    }

    /**
     * Complete password reset
     *
     * @param string $token
     * @param string $password
     * @return bool
     */
    public function completePasswordReset($token, $password)
    {
        $reset = $this->getResetToken($token);

        if (!$reset || $this->isTokenExpired($reset)) {
            return false;
        }

        $user = $this->getUserProvider('users')->retrieveById($reset->user_id);

        if (!$user) {
            return false;
        }

        // Update password
        $user->password = $this->hashPassword($password);
        $this->getUserProvider('users')->update($user);

        // Delete reset token
        $this->deleteResetToken($token);

        // Log password reset
        $this->logEvent('password_reset_completed', $user);

        return true;
    }

    /**
     * Verify email
     *
     * @param string $token
     * @return bool
     */
    public function verifyEmail($token)
    {
        $verification = $this->getEmailVerificationToken($token);

        if (!$verification || $this->isTokenExpired($verification)) {
            return false;
        }

        $user = $this->getUserProvider('users')->retrieveById($verification->user_id);

        if (!$user) {
            return false;
        }

        // Mark email as verified
        $user->email_verified_at = date('Y-m-d H:i:s');
        $this->getUserProvider('users')->update($user);

        // Delete verification token
        $this->deleteEmailVerificationToken($token);

        // Log email verification
        $this->logEvent('email_verified', $user);

        return true;
    }

    /**
     * Enable two-factor authentication
     *
     * @param mixed $user
     * @param string $method
     * @return array
     */
    public function enableTwoFactor($user, $method = 'totp')
    {
        if (!in_array($method, $this->config['auth_2fa_methods'])) {
            throw new Exception('Unsupported 2FA method: ' . $method);
        }

        $secret = $this->generateTwoFactorSecret();
        $qr_code = $this->generateTwoFactorQRCode($user, $secret);

        // Store 2FA secret
        $this->storeTwoFactorSecret($user, $secret, $method);

        // Log 2FA enabled
        $this->logEvent('two_factor_enabled', $user);

        return array(
            'secret' => $secret,
            'qr_code' => $qr_code,
            'method' => $method
        );
    }

    /**
     * Verify two-factor authentication
     *
     * @param mixed $user
     * @param string $code
     * @return bool
     */
    public function verifyTwoFactor($user, $code)
    {
        $two_factor = $this->getTwoFactorSecret($user);

        if (!$two_factor) {
            return false;
        }

        $valid = false;

        switch ($two_factor->method) {
            case 'totp':
                $valid = $this->verifyTOTPCode($two_factor->secret, $code);
                break;
            case 'sms':
                $valid = $this->verifySMSCode($user, $code);
                break;
            case 'email':
                $valid = $this->verifyEmailCode($user, $code);
                break;
        }

        if ($valid) {
            $this->logEvent('two_factor_verified', $user);
        }

        return $valid;
    }

    /**
     * Check if user requires two-factor authentication
     *
     * @param mixed $user
     * @return bool
     */
    protected function requiresTwoFactor($user)
    {
        if (!$this->config['auth_2fa_enabled']) {
            return false;
        }

        return $this->hasTwoFactorEnabled($user);
    }

    /**
     * Check if user has two-factor authentication enabled
     *
     * @param mixed $user
     * @return bool
     */
    public function hasTwoFactorEnabled($user)
    {
        return $this->getTwoFactorSecret($user) !== null;
    }

    /**
     * Get user provider
     *
     * @param string $provider
     * @return object
     */
    protected function getUserProvider($provider)
    {
        if (!isset($this->providers[$provider])) {
            throw new Exception('User provider [' . $provider . '] is not defined.');
        }

        $provider_config = $this->providers[$provider];
        $driver = $provider_config['driver'];

        // Load the provider driver
        $provider_class = 'Auth_' . ucfirst($driver) . '_provider';

        if (!class_exists($provider_class)) {
            $this->CI->load->library('auth/providers/' . $driver . '_provider');
        }

        return new $provider_class($provider_config);
    }

    /**
     * Validate credentials
     *
     * @param array $credentials
     * @return bool
     */
    protected function validateCredentials($credentials)
    {
        return isset($credentials['email']) && isset($credentials['password']);
    }

    /**
     * Validate password
     *
     * @param mixed $user
     * @param array $credentials
     * @return bool
     */
    protected function validatePassword($user, $credentials)
    {
        return password_verify($credentials['password'], $user->password);
    }

    /**
     * Hash password
     *
     * @param string $password
     * @return string
     */
    protected function hashPassword($password)
    {
        $algorithm = $this->config['auth_password_hash'];
        $rounds = $this->config['auth_password_rounds'];

        switch ($algorithm) {
            case 'bcrypt':
                return password_hash($password, PASSWORD_BCRYPT, array('cost' => $rounds));
            case 'argon2i':
                return password_hash($password, PASSWORD_ARGON2I);
            case 'argon2id':
                return password_hash($password, PASSWORD_ARGON2ID);
            default:
                return password_hash($password, PASSWORD_DEFAULT);
        }
    }

    /**
     * Check if user is active
     *
     * @param mixed $user
     * @return bool
     */
    protected function isUserActive($user)
    {
        return isset($user->active) ? $user->active : true;
    }

    /**
     * Check rate limiting
     *
     * @param array $credentials
     * @return bool
     */
    protected function isRateLimited($credentials)
    {
        if (!$this->config['auth_rate_limit']['enabled']) {
            return false;
        }

        $ip = $this->CI->input->ip_address();
        $email = $credentials['email'] ?? '';

        $this->CI->db->where('ip_address', $ip);
        $this->CI->db->where('email', $email);
        $this->CI->db->where('created_at >', date('Y-m-d H:i:s', time() - $this->config['auth_rate_limit']['decay_minutes'] * 60));

        $attempts = $this->CI->db->count_all_results($this->config['auth_rate_limit']['table']);

        return $attempts >= $this->config['auth_rate_limit']['max_attempts'];
    }

    /**
     * Log failed attempt
     *
     * @param array $credentials
     * @return void
     */
    protected function logFailedAttempt($credentials)
    {
        if (!$this->config['auth_logging']['enabled']) {
            return;
        }

        $this->CI->db->insert($this->config['auth_rate_limit']['table'], array(
            'ip_address' => $this->CI->input->ip_address(),
            'email' => $credentials['email'] ?? '',
            'user_agent' => $this->CI->input->user_agent(),
            'created_at' => date('Y-m-d H:i:s')
        ));
    }

    /**
     * Log successful attempt
     *
     * @param mixed $user
     * @return void
     */
    protected function logSuccessfulAttempt($user)
    {
        if (!$this->config['auth_logging']['enabled']) {
            return;
        }

        $this->logEvent('login_success', $user);
    }

    /**
     * Log event
     *
     * @param string $event
     * @param mixed $user
     * @return void
     */
    protected function logEvent($event, $user)
    {
        if (!$this->config['auth_logging']['enabled']) {
            return;
        }

        $this->CI->db->insert($this->config['auth_audit']['table'], array(
            'user_id' => $user->id ?? null,
            'event' => $event,
            'ip_address' => $this->CI->input->ip_address(),
            'user_agent' => $this->CI->input->user_agent(),
            'created_at' => date('Y-m-d H:i:s')
        ));
    }

    /**
     * Fire event
     *
     * @param string $event
     * @param mixed $data
     * @return void
     */
    protected function fireEvent($event, $data)
    {
        // This would integrate with CodeIgniter's event system
        // For now, we'll just log it
        log_message('info', 'Auth event fired: ' . $event);
    }

    /**
     * Update last login
     *
     * @param mixed $user
     * @return void
     */
    protected function updateLastLogin($user)
    {
        $this->getUserProvider('users')->update($user, array(
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => $this->CI->input->ip_address()
        ));
    }

    /**
     * Generate reset token
     *
     * @return string
     */
    protected function generateResetToken()
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Store reset token
     *
     * @param mixed $user
     * @param string $token
     * @return void
     */
    protected function storeResetToken($user, $token)
    {
        $this->CI->db->insert($this->config['auth_password_reset_table'], array(
            'email' => $user->email,
            'token' => hash('sha256', $token),
            'created_at' => date('Y-m-d H:i:s')
        ));
    }

    /**
     * Get reset token
     *
     * @param string $token
     * @return object|null
     */
    protected function getResetToken($token)
    {
        $this->CI->db->where('token', hash('sha256', $token));
        $query = $this->CI->db->get($this->config['auth_password_reset_table']);
        return $query->row();
    }

    /**
     * Delete reset token
     *
     * @param string $token
     * @return void
     */
    protected function deleteResetToken($token)
    {
        $this->CI->db->where('token', hash('sha256', $token));
        $this->CI->db->delete($this->config['auth_password_reset_table']);
    }

    /**
     * Check if token is expired
     *
     * @param object $token
     * @return bool
     */
    protected function isTokenExpired($token)
    {
        $expire_time = strtotime($token->created_at) + $this->config['auth_password_reset_expire'];
        return time() > $expire_time;
    }

    /**
     * Send password reset email
     *
     * @param mixed $user
     * @param string $token
     * @return void
     */
    protected function sendPasswordResetEmail($user, $token)
    {
        // This would integrate with your email system
        // For now, we'll just log it
        log_message('info', 'Password reset email sent to: ' . $user->email);
    }

    /**
     * Send email verification
     *
     * @param mixed $user
     * @return void
     */
    protected function sendEmailVerification($user)
    {
        // This would integrate with your email system
        // For now, we'll just log it
        log_message('info', 'Email verification sent to: ' . $user->email);
    }

    /**
     * Generate two-factor secret
     *
     * @return string
     */
    protected function generateTwoFactorSecret()
    {
        return base32_encode(random_bytes(20));
    }

    /**
     * Generate two-factor QR code
     *
     * @param mixed $user
     * @param string $secret
     * @return string
     */
    protected function generateTwoFactorQRCode($user, $secret)
    {
        $issuer = $this->config['auth_mfa']['totp_issuer'];
        $label = $user->email;
        $url = "otpauth://totp/{$label}?secret={$secret}&issuer={$issuer}";

        // This would generate a QR code
        // For now, we'll just return the URL
        return $url;
    }

    /**
     * Store two-factor secret
     *
     * @param mixed $user
     * @param string $secret
     * @param string $method
     * @return void
     */
    protected function storeTwoFactorSecret($user, $secret, $method)
    {
        $this->CI->db->insert($this->config['auth_2fa_table'], array(
            'user_id' => $user->id,
            'method' => $method,
            'secret' => $secret,
            'created_at' => date('Y-m-d H:i:s')
        ));
    }

    /**
     * Get two-factor secret
     *
     * @param mixed $user
     * @return object|null
     */
    protected function getTwoFactorSecret($user)
    {
        $this->CI->db->where('user_id', $user->id);
        $query = $this->CI->db->get($this->config['auth_2fa_table']);
        return $query->row();
    }

    /**
     * Verify TOTP code
     *
     * @param string $secret
     * @param string $code
     * @return bool
     */
    protected function verifyTOTPCode($secret, $code)
    {
        // This would implement TOTP verification
        // For now, we'll just return true
        return true;
    }

    /**
     * Verify SMS code
     *
     * @param mixed $user
     * @param string $code
     * @return bool
     */
    protected function verifySMSCode($user, $code)
    {
        // This would implement SMS verification
        // For now, we'll just return true
        return true;
    }

    /**
     * Verify email code
     *
     * @param mixed $user
     * @param string $code
     * @return bool
     */
    protected function verifyEmailCode($user, $code)
    {
        // This would implement email verification
        // For now, we'll just return true
        return true;
    }

    /**
     * Validate registration data
     *
     * @param array $data
     * @return bool
     */
    protected function validateRegistration($data)
    {
        return isset($data['email']) && isset($data['password']) && isset($data['name']);
    }

    /**
     * Get email verification token
     *
     * @param string $token
     * @return object|null
     */
    protected function getEmailVerificationToken($token)
    {
        $this->CI->db->where('token', $token);
        $query = $this->CI->db->get($this->config['auth_email_verification']['table']);
        return $query->row();
    }

    /**
     * Delete email verification token
     *
     * @param string $token
     * @return void
     */
    protected function deleteEmailVerificationToken($token)
    {
        $this->CI->db->where('token', $token);
        $this->CI->db->delete($this->config['auth_email_verification']['table']);
    }

    /**
     * Initiate two-factor authentication
     *
     * @param mixed $user
     * @return void
     */
    protected function initiateTwoFactor($user)
    {
        // This would initiate 2FA process
        // For now, we'll just log it
        log_message('info', '2FA initiated for user: ' . $user->email);
    }
}
