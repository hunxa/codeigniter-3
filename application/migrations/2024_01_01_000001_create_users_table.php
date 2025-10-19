<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration: Create Users Table
 * 
 * This migration creates the users table for the authentication system.
 * 
 * @package     CodeIgniter
 * @subpackage  Migrations
 * @category    Database
 * @author      Your Name
 */
class Migration_Create_users_table extends CI_Migration
{
    /**
     * Database connection
     *
     * @var object
     */
    protected $db;

    /**
     * Migration up
     *
     * @return void
     */
    public function up()
    {
        $this->db = $this->load->database('default', TRUE);

        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ),
            'email' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE,
                'unique' => TRUE
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE,
                'unique' => TRUE
            ),
            'phone' => array(
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => TRUE,
                'unique' => TRUE
            ),
            'password' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ),
            'role' => array(
                'type' => 'ENUM',
                'constraint' => array('admin', 'user', 'moderator'),
                'default' => 'user'
            ),
            'active' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1
            ),
            'email_verified_at' => array(
                'type' => 'DATETIME',
                'null' => TRUE
            ),
            'last_login_at' => array(
                'type' => 'DATETIME',
                'null' => TRUE
            ),
            'last_login_ip' => array(
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => TRUE
            ),
            'remember_token' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            ),
            'deleted_at' => array(
                'type' => 'DATETIME',
                'null' => TRUE
            )
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('email');
        $this->dbforge->add_key('username');
        $this->dbforge->add_key('phone');
        $this->dbforge->add_key('remember_token');
        $this->dbforge->add_key('deleted_at');

        $this->dbforge->create_table('users', TRUE);
    }

    /**
     * Migration down
     *
     * @return void
     */
    public function down()
    {
        $this->dbforge->drop_table('users', TRUE);
    }
}
