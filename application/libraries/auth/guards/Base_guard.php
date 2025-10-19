<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Base Guard Class
 * 
 * This class provides a base implementation for authentication guards.
 * 
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Authentication
 * @author      Your Name
 */
abstract class Base_guard implements Guard_interface
{
    /**
     * CI instance
     *
     * @var object
     */
    protected $CI;

    /**
     * Guard configuration
     *
     * @var array
     */
    protected $config;

    /**
     * User provider configuration
     *
     * @var array
     */
    protected $provider_config;

    /**
     * User provider instance
     *
     * @var object
     */
    protected $provider;

    /**
     * Current user
     *
     * @var mixed|null
     */
    protected $user;

    /**
     * Guard name
     *
     * @var string
     */
    protected $name;

    /**
     * Provider name
     *
     * @var string
     */
    protected $provider_name;

    /**
     * Class constructor
     *
     * @param array $config
     * @param array $provider_config
     */
    public function __construct($config, $provider_config)
    {
        $this->CI = &get_instance();
        $this->config = $config;
        $this->provider_config = $provider_config;
        $this->name = $config['driver'];
        $this->provider_name = $config['provider'];

        // Initialize provider
        $this->initializeProvider();
    }

    /**
     * Initialize the user provider
     *
     * @return void
     */
    protected function initializeProvider()
    {
        $driver = $this->provider_config['driver'];
        $provider_class = 'Auth_' . ucfirst($driver) . '_provider';

        if (!class_exists($provider_class)) {
            $this->CI->load->library('auth/providers/' . $driver . '_provider');
        }

        $this->provider = new $provider_class($this->provider_config);
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check()
    {
        return !is_null($this->user());
    }

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest()
    {
        return !$this->check();
    }

    /**
     * Get the currently authenticated user.
     *
     * @return mixed|null
     */
    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        $this->user = $this->retrieveUser();

        return $this->user;
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return mixed|null
     */
    public function id()
    {
        $user = $this->user();
        return $user ? $user->id : null;
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
        $this->setUser($user);

        if ($remember) {
            $this->createRememberToken($user);
        }
    }

    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout()
    {
        $user = $this->user();

        if ($user) {
            $this->clearRememberToken($user);
        }

        $this->setUser(null);
    }

    /**
     * Get the user provider for this guard.
     *
     * @return object
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Get the user provider name
     *
     * @return string
     */
    public function getProviderName()
    {
        return $this->provider_name;
    }

    /**
     * Set the user provider for this guard.
     *
     * @param object $provider
     * @return void
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
    }

    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     * @return bool
     */
    public function validate($credentials)
    {
        $user = $this->provider->retrieveByCredentials($credentials);

        if (!$user) {
            return false;
        }

        return $this->provider->validateCredentials($user, $credentials);
    }

    /**
     * Attempt to authenticate a user with the given credentials.
     *
     * @param array $credentials
     * @param bool $remember
     * @return bool
     */
    public function attempt($credentials, $remember = false)
    {
        if (!$this->validate($credentials)) {
            return false;
        }

        $user = $this->provider->retrieveByCredentials($credentials);
        $this->login($user, $remember);

        return true;
    }

    /**
     * Log a user into the application without sessions or cookies.
     *
     * @param mixed $user
     * @return mixed
     */
    public function once($user)
    {
        $this->setUser($user);
        return $user;
    }

    /**
     * Log the given user ID into the application.
     *
     * @param mixed $id
     * @param bool $remember
     * @return mixed
     */
    public function loginUsingId($id, $remember = false)
    {
        $user = $this->provider->retrieveById($id);

        if (!$user) {
            return false;
        }

        $this->login($user, $remember);
        return $user;
    }

    /**
     * Determine if the guard has a user instance.
     *
     * @return bool
     */
    public function hasUser()
    {
        return !is_null($this->user);
    }

    /**
     * Set the current user.
     *
     * @param mixed $user
     * @return void
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Get the name of the guard.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name of the guard.
     *
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Retrieve the user from storage.
     * This method must be implemented by concrete guard classes.
     *
     * @return mixed|null
     */
    abstract protected function retrieveUser();

    /**
     * Create a remember token for the user.
     * This method must be implemented by concrete guard classes.
     *
     * @param mixed $user
     * @return void
     */
    abstract protected function createRememberToken($user);

    /**
     * Clear the remember token for the user.
     * This method must be implemented by concrete guard classes.
     *
     * @param mixed $user
     * @return void
     */
    abstract protected function clearRememberToken($user);
}
