<?php

declare(strict_types=1);

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration: CreateJobsTable
 * 
 * @package CodeIgniter
 * @subpackage Migrations
 * @category Database
 * @author Your Name
 */
class CreateJobsTable
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
            CREATE TABLE IF NOT EXISTS jobs (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                queue VARCHAR(255) NOT NULL,
                payload LONGTEXT NOT NULL,
                attempts TINYINT UNSIGNED NOT NULL,
                reserved_at INT UNSIGNED NULL,
                available_at INT UNSIGNED NOT NULL,
                created_at INT UNSIGNED NOT NULL,
                INDEX queue (queue),
                INDEX reserved_at (reserved_at),
                INDEX available_at (available_at)
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
