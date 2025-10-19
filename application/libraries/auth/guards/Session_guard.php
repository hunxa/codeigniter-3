<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Session Guard
 * 
 * This guard uses CodeIgniter's session library for authentication.
 * 
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Authentication
 * @author      Your Name
 */
class Auth_Session_guard extends Base_guard
{
    /**
     * Session key for user ID
     *
     * @var string
     */
    protected $session_key = 'auth_user_id';

    /**
     * Remember token key
     *
     * @var string
     */
    protected $remember_key = 'auth_remember_token';

    /**
     * Class constructor
     *
     * @param array $config
     * @param array $provider_config
     */
    public function __construct($config, $provider_config)
    {
        parent::__construct($config, $provider_config);

        $this->CI->load->library('session');
    }

    /**
     * Retrieve the user from storage.
     *
     * @return mixed|null
     */
    protected function retrieveUser()
    {
        $user_id = $this->CI->session->userdata($this->session_key);

        if (!$user_id) {
            // Try to retrieve from remember token
            $remember_token = $this->CI->input->cookie($this->remember_key);

            if ($remember_token) {
                $user = $this->retrieveUserByRememberToken($remember_token);

                if ($user) {
                    $this->CI->session->set_userdata($this->session_key, $user->id);
                    return $user;
                }
            }

            return null;
        }

        return $this->provider->retrieveById($user_id);
    }

    /**
     * Retrieve user by remember token
     *
     * @param string $token
     * @return mixed|null
     */
    protected function retrieveUserByRememberToken($token)
    {
        $remember_token = $this->getRememberToken($token);

        if (!$remember_token) {
            return null;
        }

        $user = $this->provider->retrieveByToken($remember_token->user_id, $token);

        if ($user && $this->isRememberTokenValid($remember_token)) {
            return $user;
        }

        return null;
    }

    /**
     * Get remember token from database
     *
     * @param string $token
     * @return object|null
     */
    protected function getRememberToken($token)
    {
        $this->CI->db->where('token', hash('sha256', $token));
        $query = $this->CI->db->get('remember_tokens');
        return $query->row();
    }

    /**
     * Check if remember token is valid
     *
     * @param object $token
     * @return bool
     */
    protected function isRememberTokenValid($token)
    {
        $expire_time = strtotime($token->created_at) + (30 * 24 * 60 * 60); // 30 days
        return time() < $expire_time;
    }

    /**
     * Create a remember token for the user.
     *
     * @param mixed $user
     * @return void
     */
    protected function createRememberToken($user)
    {
        $token = bin2hex(random_bytes(32));

        // Store in database
        $this->CI->db->insert('remember_tokens', array(
            'user_id' => $user->id,
            'token' => hash('sha256', $token),
            'created_at' => date('Y-m-d H:i:s')
        ));

        // Set cookie
        $this->CI->input->set_cookie(array(
            'name' => $this->remember_key,
            'value' => $token,
            'expire' => 30 * 24 * 60 * 60, // 30 days
            'path' => '/',
            'secure' => $this->CI->config->item('cookie_secure'),
            'httponly' => true
        ));

        // Update user's remember token
        $this->provider->updateRememberToken($user, $token);
    }

    /**
     * Clear the remember token for the user.
     *
     * @param mixed $user
     * @return void
     */
    protected function clearRememberToken($user)
    {
        // Delete from database
        $this->CI->db->where('user_id', $user->id);
        $this->CI->db->delete('remember_tokens');

        // Clear cookie
        $this->CI->input->set_cookie(array(
            'name' => $this->remember_key,
            'value' => '',
            'expire' => time() - 3600,
            'path' => '/'
        ));

        // Clear session
        $this->CI->session->unset_userdata($this->session_key);
    }

    /**
     * Log a user into the application.
     *
     * @param mixed $user
     * @param bool $remember
     * @return void
     */
    public function login($user, $remember = false)
    {
        parent::login($user, $remember);

        // Set session data
        $this->CI->session->set_userdata($this->session_key, $user->id);
    }

    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout()
    {
        parent::logout();

        // Clear session
        $this->CI->session->unset_userdata($this->session_key);
    }
}
