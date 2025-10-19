<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * JWT Library
 * 
 * This library provides JWT (JSON Web Token) functionality.
 * 
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Authentication
 * @author      Your Name
 */
class Jwt
{
    /**
     * CI instance
     *
     * @var object
     */
    protected $CI;

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
     * Class constructor
     */
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->config('auth');

        $jwt_config = $this->CI->config->item('auth_jwt');
        $this->secret = $jwt_config['secret'];
        $this->algorithm = $jwt_config['algorithm'];
    }

    /**
     * Encode a JWT token
     *
     * @param array $payload
     * @param string $secret
     * @param string $algorithm
     * @return string
     */
    public function encode($payload, $secret = null, $algorithm = null)
    {
        $secret = $secret ?: $this->secret;
        $algorithm = $algorithm ?: $this->algorithm;

        $header = array(
            'typ' => 'JWT',
            'alg' => $algorithm
        );

        $header_encoded = $this->base64UrlEncode(json_encode($header));
        $payload_encoded = $this->base64UrlEncode(json_encode($payload));

        $signature = $this->sign($header_encoded . '.' . $payload_encoded, $secret, $algorithm);
        $signature_encoded = $this->base64UrlEncode($signature);

        return $header_encoded . '.' . $payload_encoded . '.' . $signature_encoded;
    }

    /**
     * Decode a JWT token
     *
     * @param string $token
     * @param string $secret
     * @param array $algorithms
     * @return object
     * @throws Exception
     */
    public function decode($token, $secret = null, $algorithms = null)
    {
        $secret = $secret ?: $this->secret;
        $algorithms = $algorithms ?: array($this->algorithm);

        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            throw new Exception('Invalid JWT token format');
        }

        list($header_encoded, $payload_encoded, $signature_encoded) = $parts;

        $header = json_decode($this->base64UrlDecode($header_encoded), true);
        $payload = json_decode($this->base64UrlDecode($payload_encoded), true);

        if (!$header || !$payload) {
            throw new Exception('Invalid JWT token data');
        }

        if (!in_array($header['alg'], $algorithms)) {
            throw new Exception('Invalid JWT algorithm');
        }

        $signature = $this->base64UrlDecode($signature_encoded);
        $expected_signature = $this->sign($header_encoded . '.' . $payload_encoded, $secret, $header['alg']);

        if (!$this->verify($signature, $expected_signature, $header['alg'])) {
            throw new Exception('Invalid JWT signature');
        }

        return (object) $payload;
    }

    /**
     * Sign a JWT token
     *
     * @param string $data
     * @param string $secret
     * @param string $algorithm
     * @return string
     */
    protected function sign($data, $secret, $algorithm)
    {
        switch ($algorithm) {
            case 'HS256':
                return hash_hmac('sha256', $data, $secret, true);
            case 'HS384':
                return hash_hmac('sha384', $data, $secret, true);
            case 'HS512':
                return hash_hmac('sha512', $data, $secret, true);
            case 'RS256':
                return $this->rsaSign($data, $secret, 'sha256');
            case 'RS384':
                return $this->rsaSign($data, $secret, 'sha384');
            case 'RS512':
                return $this->rsaSign($data, $secret, 'sha512');
            default:
                throw new Exception('Unsupported JWT algorithm: ' . $algorithm);
        }
    }

    /**
     * Verify a JWT signature
     *
     * @param string $signature
     * @param string $expected_signature
     * @param string $algorithm
     * @return bool
     */
    protected function verify($signature, $expected_signature, $algorithm)
    {
        switch ($algorithm) {
            case 'HS256':
            case 'HS384':
            case 'HS512':
                return hash_equals($expected_signature, $signature);
            case 'RS256':
            case 'RS384':
            case 'RS512':
                return $this->rsaVerify($signature, $expected_signature, $algorithm);
            default:
                return false;
        }
    }

    /**
     * RSA sign
     *
     * @param string $data
     * @param string $private_key
     * @param string $algorithm
     * @return string
     */
    protected function rsaSign($data, $private_key, $algorithm)
    {
        $key = openssl_pkey_get_private($private_key);

        if (!$key) {
            throw new Exception('Invalid private key');
        }

        $signature = '';
        $success = openssl_sign($data, $signature, $key, $algorithm);

        openssl_free_key($key);

        if (!$success) {
            throw new Exception('RSA signing failed');
        }

        return $signature;
    }

    /**
     * RSA verify
     *
     * @param string $signature
     * @param string $data
     * @param string $public_key
     * @param string $algorithm
     * @return bool
     */
    protected function rsaVerify($signature, $data, $public_key, $algorithm)
    {
        $key = openssl_pkey_get_public($public_key);

        if (!$key) {
            return false;
        }

        $result = openssl_verify($data, $signature, $key, $algorithm);

        openssl_free_key($key);

        return $result === 1;
    }

    /**
     * Base64 URL encode
     *
     * @param string $data
     * @return string
     */
    protected function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64 URL decode
     *
     * @param string $data
     * @return string
     */
    protected function base64UrlDecode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    /**
     * Generate a random secret key
     *
     * @param int $length
     * @return string
     */
    public function generateSecret($length = 64)
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Validate JWT token structure
     *
     * @param string $token
     * @return bool
     */
    public function isValidStructure($token)
    {
        $parts = explode('.', $token);
        return count($parts) === 3;
    }

    /**
     * Get JWT header
     *
     * @param string $token
     * @return array|null
     */
    public function getHeader($token)
    {
        if (!$this->isValidStructure($token)) {
            return null;
        }

        $parts = explode('.', $token);
        $header_encoded = $parts[0];

        return json_decode($this->base64UrlDecode($header_encoded), true);
    }

    /**
     * Get JWT payload without verification
     *
     * @param string $token
     * @return array|null
     */
    public function getPayload($token)
    {
        if (!$this->isValidStructure($token)) {
            return null;
        }

        $parts = explode('.', $token);
        $payload_encoded = $parts[1];

        return json_decode($this->base64UrlDecode($payload_encoded), true);
    }

    /**
     * Check if JWT token is expired
     *
     * @param string $token
     * @return bool
     */
    public function isExpired($token)
    {
        $payload = $this->getPayload($token);

        if (!$payload || !isset($payload['exp'])) {
            return true;
        }

        return time() >= $payload['exp'];
    }

    /**
     * Check if JWT token is valid (not expired and has valid structure)
     *
     * @param string $token
     * @return bool
     */
    public function isValid($token)
    {
        return $this->isValidStructure($token) && !$this->isExpired($token);
    }

    /**
     * Get JWT token expiration time
     *
     * @param string $token
     * @return int|null
     */
    public function getExpiration($token)
    {
        $payload = $this->getPayload($token);

        if (!$payload || !isset($payload['exp'])) {
            return null;
        }

        return $payload['exp'];
    }

    /**
     * Get JWT token issued at time
     *
     * @param string $token
     * @return int|null
     */
    public function getIssuedAt($token)
    {
        $payload = $this->getPayload($token);

        if (!$payload || !isset($payload['iat'])) {
            return null;
        }

        return $payload['iat'];
    }

    /**
     * Get JWT token subject
     *
     * @param string $token
     * @return string|null
     */
    public function getSubject($token)
    {
        $payload = $this->getPayload($token);

        if (!$payload || !isset($payload['sub'])) {
            return null;
        }

        return $payload['sub'];
    }

    /**
     * Get JWT token issuer
     *
     * @param string $token
     * @return string|null
     */
    public function getIssuer($token)
    {
        $payload = $this->getPayload($token);

        if (!$payload || !isset($payload['iss'])) {
            return null;
        }

        return $payload['iss'];
    }

    /**
     * Get JWT token audience
     *
     * @param string $token
     * @return string|array|null
     */
    public function getAudience($token)
    {
        $payload = $this->getPayload($token);

        if (!$payload || !isset($payload['aud'])) {
            return null;
        }

        return $payload['aud'];
    }
}
