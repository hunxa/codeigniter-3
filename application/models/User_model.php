<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * User Model
 * 
 * This model handles user-related database operations.
 * 
 * @package     CodeIgniter
 * @subpackage  Models
 * @category    Authentication
 * @author      Your Name
 */
class User_model extends CI_Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Primary key
     *
     * @var string
     */
    protected $primary_key = 'id';

    /**
     * Fillable fields
     *
     * @var array
     */
    protected $fillable = array(
        'name',
        'email',
        'username',
        'phone',
        'password',
        'role',
        'active',
        'email_verified_at',
        'last_login_at',
        'last_login_ip',
        'remember_token'
    );

    /**
     * Hidden fields
     *
     * @var array
     */
    protected $hidden = array(
        'password',
        'remember_token'
    );

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get user by ID
     *
     * @param int $id
     * @return object|null
     */
    public function find($id)
    {
        $this->db->where($this->primary_key, $id);
        $query = $this->db->get($this->table);
        return $query->row();
    }

    /**
     * Get user by email
     *
     * @param string $email
     * @return object|null
     */
    public function findByEmail($email)
    {
        $this->db->where('email', $email);
        $query = $this->db->get($this->table);
        return $query->row();
    }

    /**
     * Get user by username
     *
     * @param string $username
     * @return object|null
     */
    public function findByUsername($username)
    {
        $this->db->where('username', $username);
        $query = $this->db->get($this->table);
        return $query->row();
    }

    /**
     * Get user by phone
     *
     * @param string $phone
     * @return object|null
     */
    public function findByPhone($phone)
    {
        $this->db->where('phone', $phone);
        $query = $this->db->get($this->table);
        return $query->row();
    }

    /**
     * Get user by remember token
     *
     * @param string $token
     * @return object|null
     */
    public function findByRememberToken($token)
    {
        $this->db->where('remember_token', $token);
        $query = $this->db->get($this->table);
        return $query->row();
    }

    /**
     * Create a new user
     *
     * @param array $data
     * @return int|false
     */
    public function create($data)
    {
        // Filter fillable fields
        $data = $this->filterFillable($data);

        // Add timestamps
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Hash password if present
        if (isset($data['password'])) {
            $data['password'] = $this->hashPassword($data['password']);
        }

        // Set default values
        $data['active'] = $data['active'] ?? 1;
        $data['role'] = $data['role'] ?? 'user';

        $this->db->insert($this->table, $data);

        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Update a user
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        // Filter fillable fields
        $data = $this->filterFillable($data);

        // Add updated timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Hash password if present
        if (isset($data['password'])) {
            $data['password'] = $this->hashPassword($data['password']);
        }

        $this->db->where($this->primary_key, $id);
        $this->db->update($this->table, $data);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Delete a user
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $this->db->where($this->primary_key, $id);
        $this->db->delete($this->table);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Soft delete a user
     *
     * @param int $id
     * @return bool
     */
    public function softDelete($id)
    {
        return $this->update($id, array('deleted_at' => date('Y-m-d H:i:s')));
    }

    /**
     * Restore a soft deleted user
     *
     * @param int $id
     * @return bool
     */
    public function restore($id)
    {
        return $this->update($id, array('deleted_at' => null));
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
            $this->db->limit($limit, $offset);
        }

        $this->db->where('deleted_at', null);
        $query = $this->db->get($this->table);
        return $query->result();
    }

    /**
     * Count users
     *
     * @return int
     */
    public function count()
    {
        $this->db->where('deleted_at', null);
        return $this->db->count_all_results($this->table);
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

        $this->db->where('deleted_at', null);
        $this->db->group_start();

        foreach ($fields as $index => $field) {
            if ($index > 0) {
                $this->db->or_like($field, $term);
            } else {
                $this->db->like($field, $term);
            }
        }

        $this->db->group_end();

        $query = $this->db->get($this->table);
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
        $this->db->where('role', $role);
        $this->db->where('deleted_at', null);
        $query = $this->db->get($this->table);
        return $query->result();
    }

    /**
     * Get active users
     *
     * @return array
     */
    public function getActive()
    {
        $this->db->where('active', 1);
        $this->db->where('deleted_at', null);
        $query = $this->db->get($this->table);
        return $query->result();
    }

    /**
     * Get inactive users
     *
     * @return array
     */
    public function getInactive()
    {
        $this->db->where('active', 0);
        $this->db->where('deleted_at', null);
        $query = $this->db->get($this->table);
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
        $this->db->where('created_at >=', $start_date);
        $this->db->where('created_at <=', $end_date);
        $this->db->where('deleted_at', null);
        $query = $this->db->get($this->table);
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
        $this->db->where('last_login_at >=', $start_date);
        $this->db->where('last_login_at <=', $end_date);
        $this->db->where('deleted_at', null);
        $query = $this->db->get($this->table);
        return $query->result();
    }

    /**
     * Update last login
     *
     * @param int $id
     * @param string $ip
     * @return bool
     */
    public function updateLastLogin($id, $ip = null)
    {
        $data = array(
            'last_login_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        if ($ip) {
            $data['last_login_ip'] = $ip;
        }

        return $this->update($id, $data);
    }

    /**
     * Update remember token
     *
     * @param int $id
     * @param string $token
     * @return bool
     */
    public function updateRememberToken($id, $token)
    {
        return $this->update($id, array('remember_token' => $token));
    }

    /**
     * Verify email
     *
     * @param int $id
     * @return bool
     */
    public function verifyEmail($id)
    {
        return $this->update($id, array('email_verified_at' => date('Y-m-d H:i:s')));
    }

    /**
     * Activate user
     *
     * @param int $id
     * @return bool
     */
    public function activate($id)
    {
        return $this->update($id, array('active' => 1));
    }

    /**
     * Deactivate user
     *
     * @param int $id
     * @return bool
     * @return bool
     */
    public function deactivate($id)
    {
        return $this->update($id, array('active' => 0));
    }

    /**
     * Change password
     *
     * @param int $id
     * @param string $password
     * @return bool
     */
    public function changePassword($id, $password)
    {
        return $this->update($id, array('password' => $password));
    }

    /**
     * Check if email exists
     *
     * @param string $email
     * @param int $exclude_id
     * @return bool
     */
    public function emailExists($email, $exclude_id = null)
    {
        $this->db->where('email', $email);

        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }

        $this->db->where('deleted_at', null);
        $query = $this->db->get($this->table);

        return $query->num_rows() > 0;
    }

    /**
     * Check if username exists
     *
     * @param string $username
     * @param int $exclude_id
     * @return bool
     */
    public function usernameExists($username, $exclude_id = null)
    {
        $this->db->where('username', $username);

        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }

        $this->db->where('deleted_at', null);
        $query = $this->db->get($this->table);

        return $query->num_rows() > 0;
    }

    /**
     * Check if phone exists
     *
     * @param string $phone
     * @param int $exclude_id
     * @return bool
     */
    public function phoneExists($phone, $exclude_id = null)
    {
        $this->db->where('phone', $phone);

        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }

        $this->db->where('deleted_at', null);
        $query = $this->db->get($this->table);

        return $query->num_rows() > 0;
    }

    /**
     * Hash password
     *
     * @param string $password
     * @return string
     */
    protected function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Filter fillable fields
     *
     * @param array $data
     * @return array
     */
    protected function filterFillable($data)
    {
        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * Get user statistics
     *
     * @return array
     */
    public function getStatistics()
    {
        $stats = array();

        // Total users
        $stats['total'] = $this->count();

        // Active users
        $this->db->where('active', 1);
        $this->db->where('deleted_at', null);
        $stats['active'] = $this->db->count_all_results($this->table);

        // Inactive users
        $this->db->where('active', 0);
        $this->db->where('deleted_at', null);
        $stats['inactive'] = $this->db->count_all_results($this->table);

        // Verified users
        $this->db->where('email_verified_at IS NOT NULL');
        $this->db->where('deleted_at', null);
        $stats['verified'] = $this->db->count_all_results($this->table);

        // Users by role
        $this->db->select('role, COUNT(*) as count');
        $this->db->where('deleted_at', null);
        $this->db->group_by('role');
        $query = $this->db->get($this->table);
        $stats['by_role'] = $query->result();

        // Recent registrations (last 30 days)
        $this->db->where('created_at >=', date('Y-m-d H:i:s', strtotime('-30 days')));
        $this->db->where('deleted_at', null);
        $stats['recent_registrations'] = $this->db->count_all_results($this->table);

        return $stats;
    }
}
