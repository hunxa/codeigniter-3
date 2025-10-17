<?php

declare(strict_types=1);

/**
 * CLI Bootstrap for CodeIgniter 3
 * 
 * @package CodeIgniter
 * @subpackage CLI
 * @category Bootstrap
 * @author Your Name
 */

// Set the path to CodeIgniter
$system_path = __DIR__ . '/..';
$application_folder = __DIR__ . '/../../application';

// Define constants
define('BASEPATH', $system_path . '/');
define('APPPATH', $application_folder . '/');
define('ENVIRONMENT', 'development');
define('VIEWPATH', $application_folder . '/views/');
define('FCPATH', __DIR__ . '/../../public/');

// Load .env file if it exists
if (file_exists(__DIR__ . '/../../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();
}

// Load CodeIgniter core files
require_once BASEPATH . 'core/Common.php';
require_once BASEPATH . 'core/Config.php';

// Load application config
require_once APPPATH . 'config/config.php';
require_once APPPATH . 'config/database.php';

// Load database
require_once BASEPATH . 'database/DB.php';

// Create database connection
$config = new CI_Config();

// Load database configuration from config
$db_config = [
    'dsn' => env('DB_DSN', ''),
    'hostname' => env('DB_HOSTNAME', 'localhost'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'database' => env('DB_DATABASE', 'codeigniter'),
    'dbdriver' => env('DB_DRIVER', 'mysqli'),
    'dbprefix' => env('DB_PREFIX', ''),
    'pconnect' => env('DB_PCONNECT', false),
    'db_debug' => env('DB_DEBUG', true),
    'cache_on' => env('DB_CACHE_ON', false),
    'cachedir' => env('DB_CACHE_DIR', ''),
    'char_set' => env('DB_CHAR_SET', 'utf8'),
    'dbcollat' => env('DB_COLLAT', 'utf8_general_ci'),
    'swap_pre' => env('DB_SWAP_PRE', ''),
    'encrypt' => env('DB_ENCRYPT', false),
    'compress' => env('DB_COMPRESS', false),
    'stricton' => env('DB_STRICTON', false),
    'failover' => env('DB_FAILOVER', []),
    'save_queries' => env('DB_SAVE_QUERIES', true)
];


// Load our custom libraries
require_once BASEPATH . 'libraries/Queue.php';
require_once BASEPATH . 'libraries/Migration.php';
require_once __DIR__ . '/DatabaseManager.php';

// Create database manager
$db_manager = new DatabaseManager($db_config);

// Try to get database connection with fallback
try {
    $db = $db_manager->getConnection();
    $db_available = true;
} catch (Exception $e) {
    // Check if it's a database-specific error and ask to create it
    $message = $e->getMessage();
    $mysql_error = $e->getPrevious() ? $e->getPrevious()->getMessage() : '';

    if (
        strpos($message, "Unknown database") !== false ||
        strpos($message, "doesn't exist") !== false ||
        strpos($mysql_error, "Unknown database") !== false ||
        strpos($mysql_error, "doesn't exist") !== false
    ) {

        echo "Database '{$db_config['database']}' does not exist.\n";
        echo "Would you like to create it? (y/n): ";

        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);

        $response = trim(strtolower($line));

        if ($response === 'y' || $response === 'yes') {
            if ($db_manager->createDatabase()) {
                // Try connection again after creating database
                try {
                    $db = $db_manager->getConnection();
                    $db_available = true;
                } catch (Exception $e2) {
                    echo "Error: Failed to connect even after creating database: " . $e2->getMessage() . "\n";
                    $db_available = false;
                }
            } else {
                $db_available = false;
            }
        } else {
            echo "Database creation cancelled.\n";
            $db_available = false;
        }
    } else {
        echo "Warning: Database not available - some commands may not work\n";
        echo "Error: " . $e->getMessage() . "\n\n";
        $db_available = false;
    }
}

// Create instances only if database is available
if ($db_available) {
    // Create a mock CI instance for CLI
    $CI = new stdClass();
    $CI->db = $db;
    $CI->config = $config;

    // Create a mock Loader class
    class MockLoader
    {
        public function get_package_paths($include_base = FALSE)
        {
            return [];
        }
    }

    // Create a mock Lang class
    class MockLang
    {
        public function load($file)
        {
            return true;
        }
    }

    $CI->load = new MockLoader();
    $CI->lang = new MockLang();

    // Set global CI instance
    $GLOBALS['CI'] = $CI;

    // Create mock get_instance function for CLI
    if (!function_exists('get_instance')) {
        function get_instance()
        {
            return $GLOBALS['CI'] ?? new stdClass();
        }
    }

    $queue = new CI_Queue();
    $migration = new CI_Migration();
}

return [
    'db_available' => $db_available,
    'queue' => $queue ?? null,
    'migration' => $migration ?? null,
    'config' => $config,
    'db_manager' => $db_manager
];
