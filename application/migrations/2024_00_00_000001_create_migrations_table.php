<?php

declare(strict_types=1);

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration: CreateMigrationsTable
 * 
 * @package CodeIgniter
 * @subpackage Migrations
 * @category Database
 * @author Your Name
 */
class CreateMigrationsTable
{
    /**
     * CI instance
     *
     * @var object
     */
    protected $CI;

    /**
     * Database connection
     *
     * @var object
     */
    protected $db;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->CI = get_instance();
        $this->db = $this->CI->db;
    }

    /**
     * Run the migrations
     *
     * @return void
     */
    public function up(): void
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                batch INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down(): void
    {
        $this->db->query('DROP TABLE IF EXISTS migrations');
    }
}
