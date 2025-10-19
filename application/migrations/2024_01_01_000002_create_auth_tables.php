<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration: Create Auth Tables
 * 
 * This migration creates all the authentication-related tables.
 * 
 * @package     CodeIgniter
 * @subpackage  Migrations
 * @category    Database
 * @author      Your Name
 */
class Migration_Create_auth_tables extends CI_Migration
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

        // Password resets table
        $this->createPasswordResetsTable();

        // Remember tokens table
        $this->createRememberTokensTable();

        // Two factor authentication table
        $this->createTwoFactorTable();

        // API tokens table
        $this->createApiTokensTable();

        // Refresh tokens table
        $this->createRefreshTokensTable();

        // Email verifications table
        $this->createEmailVerificationsTable();

        // Account lockouts table
        $this->createAccountLockoutsTable();

        // Auth attempts table
        $this->createAuthAttemptsTable();

        // Auth audit log table
        $this->createAuthAuditLogTable();

        // User devices table
        $this->createUserDevicesTable();
    }

    /**
     * Migration down
     *
     * @return void
     */
    public function down()
    {
        $this->dbforge->drop_table('user_devices', TRUE);
        $this->dbforge->drop_table('auth_audit_log', TRUE);
        $this->dbforge->drop_table('auth_attempts', TRUE);
        $this->dbforge->drop_table('account_lockouts', TRUE);
        $this->dbforge->drop_table('email_verifications', TRUE);
        $this->dbforge->drop_table('refresh_tokens', TRUE);
        $this->dbforge->drop_table('api_tokens', TRUE);
        $this->dbforge->drop_table('two_factor_auth', TRUE);
        $this->dbforge->drop_table('remember_tokens', TRUE);
        $this->dbforge->drop_table('password_resets', TRUE);
    }

    /**
     * Create password resets table
     *
     * @return void
     */
    private function createPasswordResetsTable()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'email' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ),
            'token' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            )
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('email');
        $this->dbforge->add_key('token');

        $this->dbforge->create_table('password_resets', TRUE);
    }

    /**
     * Create remember tokens table
     *
     * @return void
     */
    private function createRememberTokensTable()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => FALSE
            ),
            'token' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            )
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('user_id');
        $this->dbforge->add_key('token');

        $this->dbforge->create_table('remember_tokens', TRUE);
    }

    /**
     * Create two factor authentication table
     *
     * @return void
     */
    private function createTwoFactorTable()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => FALSE
            ),
            'method' => array(
                'type' => 'ENUM',
                'constraint' => array('totp', 'sms', 'email'),
                'null' => FALSE
            ),
            'secret' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ),
            'backup_codes' => array(
                'type' => 'TEXT',
                'null' => TRUE
            ),
            'enabled' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            )
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('user_id');

        $this->dbforge->create_table('two_factor_auth', TRUE);
    }

    /**
     * Create API tokens table
     *
     * @return void
     */
    private function createApiTokensTable()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => FALSE
            ),
            'token' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ),
            'abilities' => array(
                'type' => 'TEXT',
                'null' => TRUE
            ),
            'expires_at' => array(
                'type' => 'DATETIME',
                'null' => TRUE
            ),
            'last_used_at' => array(
                'type' => 'DATETIME',
                'null' => TRUE
            ),
            'last_used_ip' => array(
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => TRUE
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            )
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('user_id');
        $this->dbforge->add_key('token');

        $this->dbforge->create_table('api_tokens', TRUE);
    }

    /**
     * Create refresh tokens table
     *
     * @return void
     */
    private function createRefreshTokensTable()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => FALSE
            ),
            'token' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ),
            'expires_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            )
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('user_id');
        $this->dbforge->add_key('token');

        $this->dbforge->create_table('refresh_tokens', TRUE);
    }

    /**
     * Create email verifications table
     *
     * @return void
     */
    private function createEmailVerificationsTable()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => FALSE
            ),
            'token' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ),
            'expires_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            )
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('user_id');
        $this->dbforge->add_key('token');

        $this->dbforge->create_table('email_verifications', TRUE);
    }

    /**
     * Create account lockouts table
     *
     * @return void
     */
    private function createAccountLockoutsTable()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => FALSE
            ),
            'ip_address' => array(
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => FALSE
            ),
            'attempts' => array(
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1
            ),
            'locked_until' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            )
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('user_id');
        $this->dbforge->add_key('ip_address');

        $this->dbforge->create_table('account_lockouts', TRUE);
    }

    /**
     * Create auth attempts table
     *
     * @return void
     */
    private function createAuthAttemptsTable()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'ip_address' => array(
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => FALSE
            ),
            'email' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE
            ),
            'user_agent' => array(
                'type' => 'TEXT',
                'null' => TRUE
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            )
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('ip_address');
        $this->dbforge->add_key('email');
        $this->dbforge->add_key('created_at');

        $this->dbforge->create_table('auth_attempts', TRUE);
    }

    /**
     * Create auth audit log table
     *
     * @return void
     */
    private function createAuthAuditLogTable()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE
            ),
            'event' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => FALSE
            ),
            'ip_address' => array(
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => TRUE
            ),
            'user_agent' => array(
                'type' => 'TEXT',
                'null' => TRUE
            ),
            'metadata' => array(
                'type' => 'TEXT',
                'null' => TRUE
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            )
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('user_id');
        $this->dbforge->add_key('event');
        $this->dbforge->add_key('created_at');

        $this->dbforge->create_table('auth_audit_log', TRUE);
    }

    /**
     * Create user devices table
     *
     * @return void
     */
    private function createUserDevicesTable()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => FALSE
            ),
            'device_id' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ),
            'device_name' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ),
            'device_type' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => FALSE
            ),
            'user_agent' => array(
                'type' => 'TEXT',
                'null' => TRUE
            ),
            'ip_address' => array(
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => TRUE
            ),
            'trusted' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ),
            'last_used_at' => array(
                'type' => 'DATETIME',
                'null' => TRUE
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            )
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('user_id');
        $this->dbforge->add_key('device_id');

        $this->dbforge->create_table('user_devices', TRUE);
    }
}
