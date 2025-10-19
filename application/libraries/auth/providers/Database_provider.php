<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Database User Provider
 * 
 * This provider retrieves users from the database.
 * 
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Authentication
 * @author      Your Name
 */
class Auth_Database_provider implements User_provider_interface
{
    /**
     * CI instance
     *
     * @var object
     */
    protected $CI;

    /**
     * Provider configuration
     *
     * @var array
     */
    protected $config;

    /**
     * Table name
     *
     * @var string
     */
    protected $table;

    /**
     * Model name
     *
     * @var string
     */
    protected $model;

    /**
     * Class constructor
     *
     * @param array $config
     */
    public function __construct($config)
    {
        $this->CI = &get_instance();
        $this->CI->load->database();

        $this->config = $config;
        $this->table = $config['table'];
        $this->model = $config['model'];
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param mixed $identifier
     * @return mixed|null
     */
    public function retrieveById($identifier)
    {
        $this->CI->db->where('id', $identifier);
        $query = $this->CI->db->get($this->table);

        if ($query->num_rows() === 0) {
            return null;
        }

        return $query->row();
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param mixed $identifier
     * @param string $token
     * @return mixed|null
     */
    public function retrieveByToken($identifier, $token)
    {
        $this->CI->db->where('id', $identifier);
        $this->CI->db->where('remember_token', $token);
        $query = $this->CI->db->get($this->table);

        if ($query->num_rows() === 0) {
            return null;
        }

        return $query->row();
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param mixed $user
     * @param string $token
     * @return void
     */
    public function updateRememberToken($user, $token)
    {
        $this->CI->db->where('id', $user->id);
        $this->CI->db->update($this->table, array(
            'remember_token' => $token,
            'updated_at' => date('Y-m-d H:i:s')
        ));
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param array $credentials
     * @return mixed|null
     */
    public function retrieveByCredentials($credentials)
    {
        if (empty($credentials)) {
            return null;
        }

        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // generic "user" object that will be utilized by the Guard instances.
        $query = $this->CI->db->get_where($this->table, $credentials);

        if ($query->num_rows() === 0) {
            return null;
        }

        return $query->row();
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param mixed $user
     * @param array $credentials
     * @return bool
     */
    public function validateCredentials($user, $credentials)
    {
        if (!isset($credentials['password'])) {
            return false;
        }

        return password_verify($credentials['password'], $user->password);
    }

    /**
     * Create a new user instance.
     *
     * @param array $data
     * @return mixed
     */
    public function create($data)
    {
        // Add timestamps
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Insert user
        $this->CI->db->insert($this->table, $data);

        if ($this->CI->db->affected_rows() === 0) {
            return false;
        }

        // Get the created user
        $user_id = $this->CI->db->insert_id();
        return $this->retrieveById($user_id);
    }

    /**
     * Update a user instance.
     *
     * @param mixed $user
     * @param array $data
     * @return bool
     */
    public function update($user, $data = null)
    {
        if ($data === null) {
            // Update the user object directly
            $data = (array) $user;
            unset($data['id']); // Don't update the ID
        }

        // Add updated timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->CI->db->where('id', $user->id);
        $this->CI->db->update($this->table, $data);

        return $this->CI->db->affected_rows() > 0;
    }

    /**
     * Delete a user instance.
     *
     * @param mixed $user
     * @return bool
     */
    public function delete($user)
    {
        $this->CI->db->where('id', $user->id);
        $this->CI->db->delete($this->table);

        return $this->CI->db->affected_rows() > 0;
    }

    /**
     * Get the name of the provider.
     *
     * @return string
     */
    public function getName()
    {
        return 'database';
    }

    /**
     * Set the name of the provider.
     *
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        // Not used in database provider
    }

    /**
     * Find user by email
     *
     * @param string $email
     * @return mixed|null
     */
    public function findByEmail($email)
    {
        return $this->retrieveByCredentials(array('email' => $email));
    }

    /**
     * Find user by username
     *
     * @param string $username
     * @return mixed|null
     */
    public function findByUsername($username)
    {
        return $this->retrieveByCredentials(array('username' => $username));
    }

    /**
     * Find user by phone
     *
     * @param string $phone
     * @return mixed|null
     */
    public function findByPhone($phone)
    {
        return $this->retrieveByCredentials(array('phone' => $phone));
    }

    /**
     * Get all users
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAll($limit = null, $offset = 0)
    {
        if ($limit) {
            $this->CI->db->limit($limit, $offset);
        }

        $query = $this->CI->db->get($this->table);
        return $query->result();
    }

    /**
     * Count users
     *
     * @return int
     */
    public function count()
    {
        return $this->CI->db->count_all($this->table);
    }

    /**
     * Search users
     *
     * @param string $term
     * @param array $fields
     * @return array
     */
    public function search($term, $fields = array('name', 'email'))
    {
        if (empty($fields)) {
            return array();
        }

        $this->CI->db->group_start();

        foreach ($fields as $index => $field) {
            if ($index > 0) {
                $this->CI->db->or_like($field, $term);
            } else {
                $this->CI->db->like($field, $term);
            }
        }

        $this->CI->db->group_end();

        $query = $this->CI->db->get($this->table);
        return $query->result();
    }

    /**
     * Get users by role
     *
     * @param string $role
     * @return array
     */
    public function getByRole($role)
    {
        $this->CI->db->where('role', $role);
        $query = $this->CI->db->get($this->table);
        return $query->result();
    }

    /**
     * Get active users
     *
     * @return array
     */
    public function getActive()
    {
        $this->CI->db->where('active', 1);
        $query = $this->CI->db->get($this->table);
        return $query->result();
    }

    /**
     * Get inactive users
     *
     * @return array
     */
    public function getInactive()
    {
        $this->CI->db->where('active', 0);
        $query = $this->CI->db->get($this->table);
        return $query->result();
    }

    /**
     * Get users created between dates
     *
     * @param string $start_date
     * @param string $end_date
     * @return array
     */
    public function getCreatedBetween($start_date, $end_date)
    {
        $this->CI->db->where('created_at >=', $start_date);
        $this->CI->db->where('created_at <=', $end_date);
        $query = $this->CI->db->get($this->table);
        return $query->result();
    }

    /**
     * Get users with last login between dates
     *
     * @param string $start_date
     * @param string $end_date
     * @return array
     */
    public function getLastLoginBetween($start_date, $end_date)
    {
        $this->CI->db->where('last_login_at >=', $start_date);
        $this->CI->db->where('last_login_at <=', $end_date);
        $query = $this->CI->db->get($this->table);
        return $query->result();
    }
}
