<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * API Guard
 * 
 * This guard uses API tokens for authentication.
 * 
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Authentication
 * @author      Your Name
 */
class Auth_Api_guard extends Base_guard
{
    /**
     * API token length
     *
     * @var int
     */
    protected $token_length;

    /**
     * API token expiration time
     *
     * @var int
     */
    protected $expire;

    /**
     * API tokens table
     *
     * @var string
     */
    protected $table;

    /**
     * Current token
     *
     * @var string|null
     */
    protected $token;

    /**
     * Class constructor
     *
     * @param array $config
     * @param array $provider_config
     */
    public function __construct($config, $provider_config)
    {
        parent::__construct($config, $provider_config);

        $this->CI->load->config('auth');
        $api_config = $this->CI->config->item('auth_api');

        $this->token_length = $api_config['token_length'];
        $this->expire = $api_config['expire'];
        $this->table = $api_config['table'];
    }

    /**
     * Retrieve the user from storage.
     *
     * @return mixed|null
     */
    protected function retrieveUser()
    {
        $token = $this->getTokenFromRequest();

        if (!$token) {
            return null;
        }

        // Get token from database
        $api_token = $this->getApiToken($token);

        if (!$api_token) {
            return null;
        }

        // Check if token is expired
        if ($this->isTokenExpired($api_token)) {
            $this->deleteApiToken($token);
            return null;
        }

        // Get user from provider
        $user = $this->provider->retrieveById($api_token->user_id);

        if (!$user) {
            return null;
        }

        // Update last used
        $this->updateTokenLastUsed($api_token);

        // Store token for later use
        $this->token = $token;

        return $user;
    }

    /**
     * Get token from request
     *
     * @return string|null
     */
    protected function getTokenFromRequest()
    {
        // Check Authorization header
        $header = $this->CI->input->get_request_header('Authorization');

        if ($header && strpos($header, 'Bearer ') === 0) {
            return substr($header, 7);
        }

        // Check API-Key header
        $api_key = $this->CI->input->get_request_header('API-Key');

        if ($api_key) {
            return $api_key;
        }

        // Check query parameter
        $token = $this->CI->input->get('api_token');

        if ($token) {
            return $token;
        }

        return null;
    }

    /**
     * Get API token from database
     *
     * @param string $token
     * @return object|null
     */
    protected function getApiToken($token)
    {
        $this->CI->db->where('token', hash('sha256', $token));
        $query = $this->CI->db->get($this->table);
        return $query->row();
    }

    /**
     * Check if token is expired
     *
     * @param object $token
     * @return bool
     */
    protected function isTokenExpired($token)
    {
        $expire_time = strtotime($token->expires_at);
        return time() > $expire_time;
    }

    /**
     * Update token last used
     *
     * @param object $token
     * @return void
     */
    protected function updateTokenLastUsed($token)
    {
        $this->CI->db->where('id', $token->id);
        $this->CI->db->update($this->table, array(
            'last_used_at' => date('Y-m-d H:i:s'),
            'last_used_ip' => $this->CI->input->ip_address()
        ));
    }

    /**
     * Delete API token
     *
     * @param string $token
     * @return void
     */
    protected function deleteApiToken($token)
    {
        $this->CI->db->where('token', hash('sha256', $token));
        $this->CI->db->delete($this->table);
    }

    /**
     * Create a remember token for the user.
     * For API, this creates a new API token.
     *
     * @param mixed $user
     * @return void
     */
    protected function createRememberToken($user)
    {
        $token = $this->generateApiToken();

        // Store token in database
        $this->CI->db->insert($this->table, array(
            'user_id' => $user->id,
            'token' => hash('sha256', $token),
            'name' => 'Remember Token',
            'expires_at' => date('Y-m-d H:i:s', time() + $this->expire),
            'last_used_at' => date('Y-m-d H:i:s'),
            'last_used_ip' => $this->CI->input->ip_address(),
            'created_at' => date('Y-m-d H:i:s')
        ));
    }

    /**
     * Clear the remember token for the user.
     *
     * @param mixed $user
     * @return void
     */
    protected function clearRememberToken($user)
    {
        // Delete all API tokens for user
        $this->CI->db->where('user_id', $user->id);
        $this->CI->db->delete($this->table);
    }

    /**
     * Generate API token
     *
     * @return string
     */
    public function generateApiToken()
    {
        return bin2hex(random_bytes($this->token_length / 2));
    }

    /**
     * Create API token for user
     *
     * @param mixed $user
     * @param string $name
     * @param int|null $expire
     * @return string
     */
    public function createToken($user, $name = 'API Token', $expire = null)
    {
        $token = $this->generateApiToken();
        $expire_time = $expire ?: $this->expire;

        // Store token in database
        $this->CI->db->insert($this->table, array(
            'user_id' => $user->id,
            'token' => hash('sha256', $token),
            'name' => $name,
            'expires_at' => date('Y-m-d H:i:s', time() + $expire_time),
            'last_used_at' => date('Y-m-d H:i:s'),
            'last_used_ip' => $this->CI->input->ip_address(),
            'created_at' => date('Y-m-d H:i:s')
        ));

        return $token;
    }

    /**
     * Get user's API tokens
     *
     * @param mixed $user
     * @return array
     */
    public function getUserTokens($user)
    {
        $this->CI->db->where('user_id', $user->id);
        $this->CI->db->order_by('created_at', 'DESC');
        $query = $this->CI->db->get($this->table);
        return $query->result();
    }

    /**
     * Revoke API token
     *
     * @param string $token
     * @return bool
     */
    public function revokeToken($token)
    {
        $this->CI->db->where('token', hash('sha256', $token));
        return $this->CI->db->delete($this->table);
    }

    /**
     * Revoke all tokens for user
     *
     * @param mixed $user
     * @return bool
     */
    public function revokeAllTokens($user)
    {
        $this->CI->db->where('user_id', $user->id);
        return $this->CI->db->delete($this->table);
    }

    /**
     * Log a user into the application.
     *
     * @param mixed $user
     * @param bool $remember
     * @return string
     */
    public function login($user, $remember = false)
    {
        parent::login($user, $remember);

        $token = $this->createToken($user, 'Login Token');

        return $token;
    }

    /**
     * Get current token
     *
     * @return string|null
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return void
     */
    public function setToken($token)
    {
        $this->token = $token;
    }
}
