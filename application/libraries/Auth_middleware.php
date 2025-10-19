<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Authentication Middleware
 * 
 * This middleware handles authentication checks for routes and controllers.
 * 
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Authentication
 * @author      Your Name
 */
class Auth_middleware
{
    /**
     * CI instance
     *
     * @var object
     */
    protected $CI;

    /**
     * Auth library instance
     *
     * @var object
     */
    protected $auth;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('auth');
        $this->CI->load->library('session');
        $this->CI->load->helper('url');

        $this->auth = $this->CI->auth;
    }

    /**
     * Check if user is authenticated
     *
     * @param string|null $guard
     * @return bool
     */
    public function check($guard = null)
    {
        return $this->auth->check($guard);
    }

    /**
     * Check if user is a guest
     *
     * @param string|null $guard
     * @return bool
     */
    public function guest($guard = null)
    {
        return $this->auth->guest($guard);
    }

    /**
     * Require authentication
     *
     * @param string|null $guard
     * @param string $redirect_to
     * @return void
     */
    public function requireAuth($guard = null, $redirect_to = 'auth/login')
    {
        if (!$this->check($guard)) {
            if ($this->CI->input->is_ajax_request()) {
                $this->CI->output->set_status_header(401);
                $this->CI->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Authentication required'
                )));
                return;
            }

            $this->CI->session->set_flashdata('error', 'Please login to access this page.');
            redirect($redirect_to);
        }
    }

    /**
     * Require guest (not authenticated)
     *
     * @param string|null $guard
     * @param string $redirect_to
     * @return void
     */
    public function requireGuest($guard = null, $redirect_to = 'dashboard')
    {
        if ($this->check($guard)) {
            redirect($redirect_to);
        }
    }

    /**
     * Require specific role
     *
     * @param string|array $roles
     * @param string|null $guard
     * @param string $redirect_to
     * @return void
     */
    public function requireRole($roles, $guard = null, $redirect_to = 'dashboard')
    {
        $this->requireAuth($guard);

        $user = $this->auth->user($guard);

        if (!$user) {
            $this->CI->session->set_flashdata('error', 'User not found.');
            redirect('auth/login');
        }

        $user_role = $user->role ?? 'user';
        $required_roles = is_array($roles) ? $roles : array($roles);

        if (!in_array($user_role, $required_roles)) {
            if ($this->CI->input->is_ajax_request()) {
                $this->CI->output->set_status_header(403);
                $this->CI->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Insufficient permissions'
                )));
                return;
            }

            $this->CI->session->set_flashdata('error', 'You do not have permission to access this page.');
            redirect($redirect_to);
        }
    }

    /**
     * Require specific permission
     *
     * @param string|array $permissions
     * @param string|null $guard
     * @param string $redirect_to
     * @return void
     */
    public function requirePermission($permissions, $guard = null, $redirect_to = 'dashboard')
    {
        $this->requireAuth($guard);

        $user = $this->auth->user($guard);

        if (!$user) {
            $this->CI->session->set_flashdata('error', 'User not found.');
            redirect('auth/login');
        }

        $user_permissions = $this->getUserPermissions($user);
        $required_permissions = is_array($permissions) ? $permissions : array($permissions);

        $has_permission = false;
        foreach ($required_permissions as $permission) {
            if (in_array($permission, $user_permissions)) {
                $has_permission = true;
                break;
            }
        }

        if (!$has_permission) {
            if ($this->CI->input->is_ajax_request()) {
                $this->CI->output->set_status_header(403);
                $this->CI->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Insufficient permissions'
                )));
                return;
            }

            $this->CI->session->set_flashdata('error', 'You do not have permission to access this page.');
            redirect($redirect_to);
        }
    }

    /**
     * Require email verification
     *
     * @param string|null $guard
     * @param string $redirect_to
     * @return void
     */
    public function requireEmailVerification($guard = null, $redirect_to = 'auth/verify_email')
    {
        $this->requireAuth($guard);

        $user = $this->auth->user($guard);

        if (!$user) {
            $this->CI->session->set_flashdata('error', 'User not found.');
            redirect('auth/login');
        }

        if (!$user->email_verified_at) {
            if ($this->CI->input->is_ajax_request()) {
                $this->CI->output->set_status_header(403);
                $this->CI->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Email verification required'
                )));
                return;
            }

            $this->CI->session->set_flashdata('error', 'Please verify your email address.');
            redirect($redirect_to);
        }
    }

    /**
     * Require two-factor authentication
     *
     * @param string|null $guard
     * @param string $redirect_to
     * @return void
     */
    public function requireTwoFactor($guard = null, $redirect_to = 'auth/two_factor_setup')
    {
        $this->requireAuth($guard);

        $user = $this->auth->user($guard);

        if (!$user) {
            $this->CI->session->set_flashdata('error', 'User not found.');
            redirect('auth/login');
        }

        if (!$this->auth->hasTwoFactorEnabled($user)) {
            if ($this->CI->input->is_ajax_request()) {
                $this->CI->output->set_status_header(403);
                $this->CI->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Two-factor authentication required'
                )));
                return;
            }

            $this->CI->session->set_flashdata('error', 'Please enable two-factor authentication.');
            redirect($redirect_to);
        }
    }

    /**
     * Check rate limiting
     *
     * @param string $key
     * @param int $max_attempts
     * @param int $decay_minutes
     * @return bool
     */
    public function checkRateLimit($key, $max_attempts = 5, $decay_minutes = 15)
    {
        $this->CI->load->database();

        $ip = $this->CI->input->ip_address();
        $rate_key = $key . ':' . $ip;

        $this->CI->db->where('key', $rate_key);
        $this->CI->db->where('created_at >', date('Y-m-d H:i:s', time() - $decay_minutes * 60));

        $attempts = $this->CI->db->count_all_results('rate_limits');

        if ($attempts >= $max_attempts) {
            return false;
        }

        // Record this attempt
        $this->CI->db->insert('rate_limits', array(
            'key' => $rate_key,
            'ip_address' => $ip,
            'created_at' => date('Y-m-d H:i:s')
        ));

        return true;
    }

    /**
     * Get user permissions
     *
     * @param object $user
     * @return array
     */
    protected function getUserPermissions($user)
    {
        // This would typically come from a permissions table
        // For now, we'll return role-based permissions
        $permissions = array();

        switch ($user->role) {
            case 'admin':
                $permissions = array(
                    'users.create',
                    'users.read',
                    'users.update',
                    'users.delete',
                    'roles.create',
                    'roles.read',
                    'roles.update',
                    'roles.delete',
                    'settings.read',
                    'settings.update'
                );
                break;
            case 'moderator':
                $permissions = array(
                    'users.read',
                    'users.update',
                    'roles.read'
                );
                break;
            case 'user':
                $permissions = array(
                    'profile.read',
                    'profile.update'
                );
                break;
        }

        return $permissions;
    }

    /**
     * Check if user has permission
     *
     * @param string $permission
     * @param string|null $guard
     * @return bool
     */
    public function hasPermission($permission, $guard = null)
    {
        if (!$this->check($guard)) {
            return false;
        }

        $user = $this->auth->user($guard);

        if (!$user) {
            return false;
        }

        $permissions = $this->getUserPermissions($user);

        return in_array($permission, $permissions);
    }

    /**
     * Check if user has role
     *
     * @param string $role
     * @param string|null $guard
     * @return bool
     */
    public function hasRole($role, $guard = null)
    {
        if (!$this->check($guard)) {
            return false;
        }

        $user = $this->auth->user($guard);

        if (!$user) {
            return false;
        }

        return $user->role === $role;
    }

    /**
     * Check if user has any of the roles
     *
     * @param array $roles
     * @param string|null $guard
     * @return bool
     */
    public function hasAnyRole($roles, $guard = null)
    {
        if (!$this->check($guard)) {
            return false;
        }

        $user = $this->auth->user($guard);

        if (!$user) {
            return false;
        }

        return in_array($user->role, $roles);
    }

    /**
     * Get current user
     *
     * @param string|null $guard
     * @return object|null
     */
    public function user($guard = null)
    {
        return $this->auth->user($guard);
    }

    /**
     * Get current user ID
     *
     * @param string|null $guard
     * @return mixed|null
     */
    public function id($guard = null)
    {
        return $this->auth->id($guard);
    }

    /**
     * Logout user
     *
     * @param string|null $guard
     * @return void
     */
    public function logout($guard = null)
    {
        $this->auth->logout($guard);
    }
}
