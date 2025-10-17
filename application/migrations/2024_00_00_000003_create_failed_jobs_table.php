<?php

declare(strict_types=1);

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration: CreateFailedJobsTable
 * 
 * @package CodeIgniter
 * @subpackage Migrations
 * @category Database
 * @author Your Name
 */
class CreateFailedJobsTable
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
            CREATE TABLE IF NOT EXISTS failed_jobs (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                uuid VARCHAR(255) NOT NULL UNIQUE,
                connection TEXT NOT NULL,
                queue TEXT NOT NULL,
                payload LONGTEXT NOT NULL,
                exception LONGTEXT NOT NULL,
                failed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
        $this->db->query('DROP TABLE IF EXISTS jobs');
    }
}
