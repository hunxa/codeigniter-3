<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Authentication Helper
 * 
 * This helper provides convenient functions for authentication.
 * 
 * @package     CodeIgniter
 * @subpackage  Helpers
 * @category    Authentication
 * @author      Your Name
 */

if (!function_exists('auth')) {
    /**
     * Get the auth instance
     *
     * @return object
     */
    function auth()
    {
        $CI = &get_instance();
        $CI->load->library('auth');
        return $CI->auth;
    }
}

if (!function_exists('auth_check')) {
    /**
     * Check if user is authenticated
     *
     * @param string|null $guard
     * @return bool
     */
    function auth_check($guard = null)
    {
        return auth()->check($guard);
    }
}

if (!function_exists('auth_guest')) {
    /**
     * Check if user is a guest
     *
     * @param string|null $guard
     * @return bool
     */
    function auth_guest($guard = null)
    {
        return auth()->guest($guard);
    }
}

if (!function_exists('auth_user')) {
    /**
     * Get the current user
     *
     * @param string|null $guard
     * @return object|null
     */
    function auth_user($guard = null)
    {
        return auth()->user($guard);
    }
}

if (!function_exists('auth_id')) {
    /**
     * Get the current user ID
     *
     * @param string|null $guard
     * @return mixed|null
     */
    function auth_id($guard = null)
    {
        return auth()->id($guard);
    }
}

if (!function_exists('auth_attempt')) {
    /**
     * Attempt to authenticate a user
     *
     * @param array $credentials
     * @param bool $remember
     * @param string|null $guard
     * @return bool
     */
    function auth_attempt($credentials, $remember = false, $guard = null)
    {
        return auth()->attempt($credentials, $remember, $guard);
    }
}

if (!function_exists('auth_login')) {
    /**
     * Login a user
     *
     * @param mixed $user
     * @param bool $remember
     * @param string|null $guard
     * @return void
     */
    function auth_login($user, $remember = false, $guard = null)
    {
        auth()->login($user, $remember, $guard);
    }
}

if (!function_exists('auth_logout')) {
    /**
     * Logout the current user
     *
     * @param string|null $guard
     * @return void
     */
    function auth_logout($guard = null)
    {
        auth()->logout($guard);
    }
}

if (!function_exists('auth_register')) {
    /**
     * Register a new user
     *
     * @param array $data
     * @param bool $auto_login
     * @param string|null $guard
     * @return mixed|false
     */
    function auth_register($data, $auto_login = false, $guard = null)
    {
        return auth()->register($data, $auto_login, $guard);
    }
}

if (!function_exists('auth_guard')) {
    /**
     * Get a guard instance
     *
     * @param string|null $guard
     * @return object
     */
    function auth_guard($guard = null)
    {
        return auth()->guard($guard);
    }
}

if (!function_exists('auth_middleware')) {
    /**
     * Get the auth middleware instance
     *
     * @return object
     */
    function auth_middleware()
    {
        $CI = &get_instance();
        $CI->load->library('auth_middleware');
        return $CI->auth_middleware;
    }
}

if (!function_exists('require_auth')) {
    /**
     * Require authentication
     *
     * @param string|null $guard
     * @param string $redirect_to
     * @return void
     */
    function require_auth($guard = null, $redirect_to = 'auth/login')
    {
        auth_middleware()->requireAuth($guard, $redirect_to);
    }
}

if (!function_exists('require_guest')) {
    /**
     * Require guest (not authenticated)
     *
     * @param string|null $guard
     * @param string $redirect_to
     * @return void
     */
    function require_guest($guard = null, $redirect_to = 'dashboard')
    {
        auth_middleware()->requireGuest($guard, $redirect_to);
    }
}

if (!function_exists('require_role')) {
    /**
     * Require specific role
     *
     * @param string|array $roles
     * @param string|null $guard
     * @param string $redirect_to
     * @return void
     */
    function require_role($roles, $guard = null, $redirect_to = 'dashboard')
    {
        auth_middleware()->requireRole($roles, $guard, $redirect_to);
    }
}

if (!function_exists('require_permission')) {
    /**
     * Require specific permission
     *
     * @param string|array $permissions
     * @param string|null $guard
     * @param string $redirect_to
     * @return void
     */
    function require_permission($permissions, $guard = null, $redirect_to = 'dashboard')
    {
        auth_middleware()->requirePermission($permissions, $guard, $redirect_to);
    }
}

if (!function_exists('has_permission')) {
    /**
     * Check if user has permission
     *
     * @param string $permission
     * @param string|null $guard
     * @return bool
     */
    function has_permission($permission, $guard = null)
    {
        return auth_middleware()->hasPermission($permission, $guard);
    }
}

if (!function_exists('has_role')) {
    /**
     * Check if user has role
     *
     * @param string $role
     * @param string|null $guard
     * @return bool
     */
    function has_role($role, $guard = null)
    {
        return auth_middleware()->hasRole($role, $guard);
    }
}

if (!function_exists('has_any_role')) {
    /**
     * Check if user has any of the roles
     *
     * @param array $roles
     * @param string|null $guard
     * @return bool
     */
    function has_any_role($roles, $guard = null)
    {
        return auth_middleware()->hasAnyRole($roles, $guard);
    }
}

if (!function_exists('auth_hash_password')) {
    /**
     * Hash a password
     *
     * @param string $password
     * @return string
     */
    function auth_hash_password($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}

if (!function_exists('auth_verify_password')) {
    /**
     * Verify a password
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    function auth_verify_password($password, $hash)
    {
        return password_verify($password, $hash);
    }
}

if (!function_exists('auth_generate_token')) {
    /**
     * Generate a random token
     *
     * @param int $length
     * @return string
     */
    function auth_generate_token($length = 32)
    {
        return bin2hex(random_bytes($length));
    }
}

if (!function_exists('auth_generate_remember_token')) {
    /**
     * Generate a remember token
     *
     * @return string
     */
    function auth_generate_remember_token()
    {
        return auth_generate_token(32);
    }
}

if (!function_exists('auth_generate_reset_token')) {
    /**
     * Generate a password reset token
     *
     * @return string
     */
    function auth_generate_reset_token()
    {
        return auth_generate_token(32);
    }
}

if (!function_exists('auth_generate_verification_token')) {
    /**
     * Generate an email verification token
     *
     * @return string
     */
    function auth_generate_verification_token()
    {
        return auth_generate_token(32);
    }
}

if (!function_exists('auth_generate_api_token')) {
    /**
     * Generate an API token
     *
     * @param int $length
     * @return string
     */
    function auth_generate_api_token($length = 64)
    {
        return auth_generate_token($length);
    }
}

if (!function_exists('auth_generate_jwt_token')) {
    /**
     * Generate a JWT token
     *
     * @param array $payload
     * @param string $secret
     * @param string $algorithm
     * @return string
     */
    function auth_generate_jwt_token($payload, $secret, $algorithm = 'HS256')
    {
        $CI = &get_instance();
        $CI->load->library('jwt');

        return $CI->jwt->encode($payload, $secret, $algorithm);
    }
}

if (!function_exists('auth_verify_jwt_token')) {
    /**
     * Verify a JWT token
     *
     * @param string $token
     * @param string $secret
     * @param string $algorithm
     * @return object|false
     */
    function auth_verify_jwt_token($token, $secret, $algorithm = 'HS256')
    {
        $CI = &get_instance();
        $CI->load->library('jwt');

        try {
            return $CI->jwt->decode($token, $secret, array($algorithm));
        } catch (Exception $e) {
            return false;
        }
    }
}

if (!function_exists('auth_redirect_if_authenticated')) {
    /**
     * Redirect if user is authenticated
     *
     * @param string $redirect_to
     * @param string|null $guard
     * @return void
     */
    function auth_redirect_if_authenticated($redirect_to = 'dashboard', $guard = null)
    {
        if (auth_check($guard)) {
            redirect($redirect_to);
        }
    }
}

if (!function_exists('auth_redirect_if_guest')) {
    /**
     * Redirect if user is a guest
     *
     * @param string $redirect_to
     * @param string|null $guard
     * @return void
     */
    function auth_redirect_if_guest($redirect_to = 'auth/login', $guard = null)
    {
        if (auth_guest($guard)) {
            redirect($redirect_to);
        }
    }
}

if (!function_exists('auth_redirect_if_not_role')) {
    /**
     * Redirect if user doesn't have role
     *
     * @param string|array $roles
     * @param string $redirect_to
     * @param string|null $guard
     * @return void
     */
    function auth_redirect_if_not_role($roles, $redirect_to = 'dashboard', $guard = null)
    {
        if (!has_any_role($roles, $guard)) {
            redirect($redirect_to);
        }
    }
}

if (!function_exists('auth_redirect_if_not_permission')) {
    /**
     * Redirect if user doesn't have permission
     *
     * @param string|array $permissions
     * @param string $redirect_to
     * @param string|null $guard
     * @return void
     */
    function auth_redirect_if_not_permission($permissions, $redirect_to = 'dashboard', $guard = null)
    {
        $has_permission = false;
        $permissions = is_array($permissions) ? $permissions : array($permissions);

        foreach ($permissions as $permission) {
            if (has_permission($permission, $guard)) {
                $has_permission = true;
                break;
            }
        }

        if (!$has_permission) {
            redirect($redirect_to);
        }
    }
}

if (!function_exists('auth_can')) {
    /**
     * Check if user can perform action
     *
     * @param string $permission
     * @param string|null $guard
     * @return bool
     */
    function auth_can($permission, $guard = null)
    {
        return has_permission($permission, $guard);
    }
}

if (!function_exists('auth_cannot')) {
    /**
     * Check if user cannot perform action
     *
     * @param string $permission
     * @param string|null $guard
     * @return bool
     */
    function auth_cannot($permission, $guard = null)
    {
        return !has_permission($permission, $guard);
    }
}

if (!function_exists('auth_is_admin')) {
    /**
     * Check if user is admin
     *
     * @param string|null $guard
     * @return bool
     */
    function auth_is_admin($guard = null)
    {
        return has_role('admin', $guard);
    }
}

if (!function_exists('auth_is_moderator')) {
    /**
     * Check if user is moderator
     *
     * @param string|null $guard
     * @return bool
     */
    function auth_is_moderator($guard = null)
    {
        return has_role('moderator', $guard);
    }
}

if (!function_exists('auth_is_user')) {
    /**
     * Check if user is regular user
     *
     * @param string|null $guard
     * @return bool
     */
    function auth_is_user($guard = null)
    {
        return has_role('user', $guard);
    }
}

if (!function_exists('auth_get_user_role')) {
    /**
     * Get user role
     *
     * @param string|null $guard
     * @return string|null
     */
    function auth_get_user_role($guard = null)
    {
        $user = auth_user($guard);
        return $user ? $user->role : null;
    }
}

if (!function_exists('auth_get_user_permissions')) {
    /**
     * Get user permissions
     *
     * @param string|null $guard
     * @return array
     */
    function auth_get_user_permissions($guard = null)
    {
        $user = auth_user($guard);

        if (!$user) {
            return array();
        }

        return auth_middleware()->getUserPermissions($user);
    }
}

if (!function_exists('auth_log_event')) {
    /**
     * Log authentication event
     *
     * @param string $event
     * @param mixed $user
     * @return void
     */
    function auth_log_event($event, $user = null)
    {
        $CI = &get_instance();
        $CI->load->database();

        $CI->db->insert('auth_audit_log', array(
            'user_id' => $user ? $user->id : null,
            'event' => $event,
            'ip_address' => $CI->input->ip_address(),
            'user_agent' => $CI->input->user_agent(),
            'created_at' => date('Y-m-d H:i:s')
        ));
    }
}

if (!function_exists('auth_rate_limit')) {
    /**
     * Check rate limiting
     *
     * @param string $key
     * @param int $max_attempts
     * @param int $decay_minutes
     * @return bool
     */
    function auth_rate_limit($key, $max_attempts = 5, $decay_minutes = 15)
    {
        return auth_middleware()->checkRateLimit($key, $max_attempts, $decay_minutes);
    }
}
