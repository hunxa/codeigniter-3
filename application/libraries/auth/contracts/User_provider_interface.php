<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * User Provider Interface
 * 
 * This interface defines the contract that all user providers must implement.
 * 
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Authentication
 * @author      Your Name
 */
interface User_provider_interface
{
    /**
     * Retrieve a user by their unique identifier.
     *
     * @param mixed $identifier
     * @return mixed|null
     */
    public function retrieveById($identifier);

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param mixed $identifier
     * @param string $token
     * @return mixed|null
     */
    public function retrieveByToken($identifier, $token);

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param mixed $user
     * @param string $token
     * @return void
     */
    public function updateRememberToken($user, $token);

    /**
     * Retrieve a user by the given credentials.
     *
     * @param array $credentials
     * @return mixed|null
     */
    public function retrieveByCredentials($credentials);

    /**
     * Validate a user against the given credentials.
     *
     * @param mixed $user
     * @param array $credentials
     * @return bool
     */
    public function validateCredentials($user, $credentials);

    /**
     * Create a new user instance.
     *
     * @param array $data
     * @return mixed
     */
    public function create($data);

    /**
     * Update a user instance.
     *
     * @param mixed $user
     * @param array $data
     * @return bool
     */
    public function update($user, $data = null);

    /**
     * Delete a user instance.
     *
     * @param mixed $user
     * @return bool
     */
    public function delete($user);

    /**
     * Get the name of the provider.
     *
     * @return string
     */
    public function getName();

    /**
     * Set the name of the provider.
     *
     * @param string $name
     * @return void
     */
    public function setName($name);
}
