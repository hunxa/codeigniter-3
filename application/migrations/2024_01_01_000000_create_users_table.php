<?php

declare(strict_types=1);

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration: CreateUsersTable
 * 
 * @package CodeIgniter
 * @subpackage Migrations
 * @category Database
 * @author Your Name
 */
class CreateUsersTable
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
            CREATE TABLE `users` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL,
                `email_verified_at` timestamp NULL DEFAULT NULL,
                `password` varchar(255) NOT NULL,
                `remember_token` varchar(100) DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `users_email_unique` (`email`)
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
        $this->db->query("DROP TABLE IF EXISTS `users`");
    }
}
