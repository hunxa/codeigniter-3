<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * JWT Guard
 * 
 * This guard uses JSON Web Tokens for authentication.
 * 
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Authentication
 * @author      Your Name
 */
class Auth_Jwt_guard extends Base_guard
{
    /**
     * JWT secret key
     *
     * @var string
     */
    protected $secret;

    /**
     * JWT algorithm
     *
     * @var string
     */
    protected $algorithm;

    /**
     * JWT issuer
     *
     * @var string
     */
    protected $issuer;

    /**
     * JWT audience
     *
     * @var string
     */
    protected $audience;

    /**
     * JWT expiration time
     *
     * @var int
     */
    protected $expire;

    /**
     * JWT refresh expiration time
     *
     * @var int
     */
    protected $refresh_expire;

    /**
     * JWT leeway
     *
     * @var int
     */
    protected $leeway;

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
        $jwt_config = $this->CI->config->item('auth_jwt');

        $this->secret = $jwt_config['secret'];
        $this->algorithm = $jwt_config['algorithm'];
        $this->issuer = $jwt_config['issuer'];
        $this->audience = $jwt_config['audience'];
        $this->expire = $jwt_config['expire'];
        $this->refresh_expire = $jwt_config['refresh_expire'];
        $this->leeway = $jwt_config['leeway'];

        // Load JWT library
        $this->CI->load->library('jwt');
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

        try {
            $payload = $this->CI->jwt->decode($token, $this->secret, array($this->algorithm));

            // Validate token
            if (!$this->validateToken($payload)) {
                return null;
            }

            // Check if token is expired
            if ($this->isTokenExpired($payload)) {
                return null;
            }

            // Get user from provider
            $user = $this->provider->retrieveById($payload->sub);

            if (!$user) {
                return null;
            }

            // Store token for later use
            $this->token = $token;

            return $user;
        } catch (Exception $e) {
            log_message('error', 'JWT decode error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get token from request
     *
     * @return string|null
     */
    protected function getTokenFromRequest()
    {
        $header = $this->CI->input->get_request_header('Authorization');

        if (!$header) {
            return null;
        }

        if (strpos($header, 'Bearer ') !== 0) {
            return null;
        }

        return substr($header, 7);
    }

    /**
     * Validate token payload
     *
     * @param object $payload
     * @return bool
     */
    protected function validateToken($payload)
    {
        // Check issuer
        if (isset($payload->iss) && $payload->iss !== $this->issuer) {
            return false;
        }

        // Check audience
        if (isset($payload->aud) && $payload->aud !== $this->audience) {
            return false;
        }

        // Check subject
        if (!isset($payload->sub)) {
            return false;
        }

        return true;
    }

    /**
     * Check if token is expired
     *
     * @param object $payload
     * @return bool
     */
    protected function isTokenExpired($payload)
    {
        if (!isset($payload->exp)) {
            return true;
        }

        $now = time();
        $exp = $payload->exp;

        // Add leeway to account for clock skew
        return ($now - $this->leeway) >= $exp;
    }

    /**
     * Create a remember token for the user.
     * For JWT, this creates a refresh token.
     *
     * @param mixed $user
     * @return void
     */
    protected function createRememberToken($user)
    {
        $refresh_token = $this->generateRefreshToken($user);

        // Store refresh token in database
        $this->CI->db->insert('refresh_tokens', array(
            'user_id' => $user->id,
            'token' => hash('sha256', $refresh_token),
            'expires_at' => date('Y-m-d H:i:s', time() + $this->refresh_expire),
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
        // Delete refresh tokens from database
        $this->CI->db->where('user_id', $user->id);
        $this->CI->db->delete('refresh_tokens');
    }

    /**
     * Generate access token
     *
     * @param mixed $user
     * @return string
     */
    public function generateAccessToken($user)
    {
        $now = time();

        $payload = array(
            'iss' => $this->issuer,
            'aud' => $this->audience,
            'sub' => $user->id,
            'iat' => $now,
            'exp' => $now + $this->expire,
            'type' => 'access'
        );

        return $this->CI->jwt->encode($payload, $this->secret, $this->algorithm);
    }

    /**
     * Generate refresh token
     *
     * @param mixed $user
     * @return string
     */
    public function generateRefreshToken($user)
    {
        $now = time();

        $payload = array(
            'iss' => $this->issuer,
            'aud' => $this->audience,
            'sub' => $user->id,
            'iat' => $now,
            'exp' => $now + $this->refresh_expire,
            'type' => 'refresh'
        );

        return $this->CI->jwt->encode($payload, $this->secret, $this->algorithm);
    }

    /**
     * Refresh access token
     *
     * @param string $refresh_token
     * @return array|false
     */
    public function refreshToken($refresh_token)
    {
        try {
            $payload = $this->CI->jwt->decode($refresh_token, $this->secret, array($this->algorithm));

            // Validate token
            if (!$this->validateToken($payload) || $payload->type !== 'refresh') {
                return false;
            }

            // Check if token is expired
            if ($this->isTokenExpired($payload)) {
                return false;
            }

            // Check if refresh token exists in database
            $stored_token = $this->getStoredRefreshToken($refresh_token);
            if (!$stored_token) {
                return false;
            }

            // Get user
            $user = $this->provider->retrieveById($payload->sub);
            if (!$user) {
                return false;
            }

            // Generate new access token
            $access_token = $this->generateAccessToken($user);

            return array(
                'access_token' => $access_token,
                'token_type' => 'Bearer',
                'expires_in' => $this->expire
            );
        } catch (Exception $e) {
            log_message('error', 'JWT refresh error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get stored refresh token
     *
     * @param string $token
     * @return object|null
     */
    protected function getStoredRefreshToken($token)
    {
        $this->CI->db->where('token', hash('sha256', $token));
        $this->CI->db->where('expires_at >', date('Y-m-d H:i:s'));
        $query = $this->CI->db->get('refresh_tokens');
        return $query->row();
    }

    /**
     * Log a user into the application.
     *
     * @param mixed $user
     * @param bool $remember
     * @return array
     */
    public function login($user, $remember = false)
    {
        parent::login($user, $remember);

        $access_token = $this->generateAccessToken($user);
        $refresh_token = null;

        if ($remember) {
            $refresh_token = $this->generateRefreshToken($user);
            $this->createRememberToken($user);
        }

        return array(
            'access_token' => $access_token,
            'refresh_token' => $refresh_token,
            'token_type' => 'Bearer',
            'expires_in' => $this->expire
        );
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
