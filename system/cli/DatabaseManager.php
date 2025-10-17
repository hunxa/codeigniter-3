<?php

declare(strict_types=1);

/**
 * Database Manager
 * 
 * Handles database creation and connection management
 * 
 * @package CodeIgniter
 * @subpackage CLI
 * @category Database
 * @author Your Name
 */
class DatabaseManager
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Check if database exists
     */
    public function databaseExists()
    {
        try {
            // Try to connect without specifying database
            $temp_config = $this->config;
            $temp_config['database'] = '';

            $db = DB($temp_config);
            $result = $db->query("SHOW DATABASES LIKE '" . $this->config['database'] . "'");
            return $result && $result->num_rows() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Create database
     */
    public function createDatabase()
    {
        try {
            // Connect without database
            $temp_config = $this->config;
            $temp_config['database'] = '';

            $db = DB($temp_config);

            // Create database
            $sql = "CREATE DATABASE IF NOT EXISTS `" . $this->config['database'] . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
            $db->query($sql);

            echo "Database '{$this->config['database']}' created successfully!\n";
            return true;
        } catch (Exception $e) {
            echo "Error creating database: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Ask user if they want to create database
     */
    public function askToCreateDatabase()
    {
        echo "Database '{$this->config['database']}' does not exist.\n";
        echo "Would you like to create it? (y/n): ";

        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);

        $response = trim(strtolower($line));

        if ($response === 'y' || $response === 'yes') {
            return $this->createDatabase();
        }

        echo "Database creation cancelled.\n";
        return false;
    }

    /**
     * Get database connection with fallback
     */
    public function getConnection()
    {
        // First try normal connection
        try {
            return DB($this->config);
        } catch (Exception $e) {
            // Check if it's a database-specific error
            $message = $e->getMessage();

            // If database doesn't exist, ask to create it
            if (
                strpos($message, "Unknown database") !== false ||
                strpos($message, "doesn't exist") !== false ||
                strpos($message, "Unknown database") !== false
            ) {

                if (!$this->databaseExists()) {
                    if ($this->askToCreateDatabase()) {
                        // Try connection again after creating database
                        try {
                            return DB($this->config);
                        } catch (Exception $e2) {
                            throw new Exception("Failed to connect even after creating database: " . $e2->getMessage());
                        }
                    }
                }
            }

            // For other errors (like access denied), just throw the original error
            throw $e;
        }
    }
}
