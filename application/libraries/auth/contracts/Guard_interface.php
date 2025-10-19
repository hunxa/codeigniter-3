<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Guard Interface
 * 
 * This interface defines the contract that all authentication guards must implement.
 * 
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Authentication
 * @author      Your Name
 */
interface Guard_interface
{
    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check();

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest();

    /**
     * Get the currently authenticated user.
     *
     * @return mixed|null
     */
    public function user();

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return mixed|null
     */
    public function id();

    /**
     * Log a user into the application.
     *
     * @param mixed $user
     * @param bool $remember
     * @return void
     */
    public function login($user, $remember = false);

    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout();

    /**
     * Get the user provider for this guard.
     *
     * @return string
     */
    public function getProvider();

    /**
     * Set the user provider for this guard.
     *
     * @param string $provider
     * @return void
     */
    public function setProvider($provider);

    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     * @return bool
     */
    public function validate($credentials);

    /**
     * Attempt to authenticate a user with the given credentials.
     *
     * @param array $credentials
     * @param bool $remember
     * @return bool
     */
    public function attempt($credentials, $remember = false);

    /**
     * Log a user into the application without sessions or cookies.
     *
     * @param mixed $user
     * @return mixed
     */
    public function once($user);

    /**
     * Log the given user ID into the application.
     *
     * @param mixed $id
     * @param bool $remember
     * @return mixed
     */
    public function loginUsingId($id, $remember = false);

    /**
     * Determine if the guard has a user instance.
     *
     * @return bool
     */
    public function hasUser();

    /**
     * Set the current user.
     *
     * @param mixed $user
     * @return void
     */
    public function setUser($user);

    /**
     * Get the name of the guard.
     *
     * @return string
     */
    public function getName();

    /**
     * Set the name of the guard.
     *
     * @param string $name
     * @return void
     */
    public function setName($name);
}
