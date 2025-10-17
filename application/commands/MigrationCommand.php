<?php

declare(strict_types=1);

/**
 * Migration Commands
 * 
 * @package CodeIgniter
 * @subpackage Commands
 * @category Migration
 * @author Your Name
 */
class MigrationCommand
{
    protected $migration;
    protected $db_available;
    protected $db_manager;

    public function __construct($migration, $db_available, $db_manager = null)
    {
        $this->migration = $migration;
        $this->db_available = $db_available;
        $this->db_manager = $db_manager;
    }

    public function migrate()
    {
        if (!$this->db_available) {
            // Try to get database connection with fallback
            if ($this->db_manager) {
                try {
                    $this->db_manager->getConnection();
                    echo "Database connection established!\n";
                    echo "Running migrations...\n";
                    $migrations = $this->migration->migrate();
                    if (empty($migrations)) {
                        echo "No migrations to run.\n";
                    } else {
                        echo "Migrations completed: " . implode(', ', $migrations) . "\n";
                    }
                    return;
                } catch (Exception $e) {
                    echo "Error: Database not available. Migration commands require a database connection.\n";
                    echo "Error details: " . $e->getMessage() . "\n";
                    exit(1);
                }
            } else {
                echo "Error: Database not available. Migration commands require a database connection.\n";
                exit(1);
            }
        }

        echo "Running migrations...\n";
        $migrations = $this->migration->migrate();
        if (empty($migrations)) {
            echo "No migrations to run.\n";
        } else {
            echo "Migrations completed: " . implode(', ', $migrations) . "\n";
        }
    }

    public function rollback($args)
    {
        if (!$this->db_available) {
            echo "Error: Database not available. Migration commands require a database connection.\n";
            exit(1);
        }
        $step = (int) ($args[0] ?? 1);
        echo "Rolling back {$step} migration(s)...\n";
        $migrations = $this->migration->rollback($step);
        if (empty($migrations)) {
            echo "No migrations to rollback.\n";
        } else {
            echo "Migrations rolled back: " . implode(', ', $migrations) . "\n";
        }
    }

    public function reset()
    {
        if (!$this->db_available) {
            echo "Error: Database not available. Migration commands require a database connection.\n";
            exit(1);
        }
        echo "Resetting all migrations...\n";
        $migrations = $this->migration->reset();
        if (empty($migrations)) {
            echo "No migrations to reset.\n";
        } else {
            echo "Migrations reset: " . implode(', ', $migrations) . "\n";
        }
    }

    public function refresh()
    {
        if (!$this->db_available) {
            echo "Error: Database not available. Migration commands require a database connection.\n";
            exit(1);
        }
        echo "Refreshing migrations...\n";
        $migrations = $this->migration->refresh();
        echo "Migrations refreshed: " . implode(', ', $migrations) . "\n";
    }

    public function status()
    {
        if (!$this->db_available) {
            echo "Error: Database not available. Migration commands require a database connection.\n";
            exit(1);
        }
        $this->migration->status();
    }

    public function make($args)
    {
        $name = $args[0] ?? null;
        if (!$name) {
            echo "Please provide a migration name\n";
            echo "Usage: php ci make:migration create_users_table\n";
            exit(1);
        }

        // Create migration file directly without database connection
        $timestamp = date('Y_m_d_His');
        $className = str_replace('_', '', ucwords($name, '_'));
        $filename = $timestamp . '_' . $name . '.php';
        $filepath = APPPATH . 'migrations/' . $filename;

        $content = "<?php

declare(strict_types=1);

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration: {$className}
 * 
 * @package CodeIgniter
 * @subpackage Migrations
 * @category Database
 * @author Your Name
 */
class {$className}
{
    /**
     * CI instance
     *
     * @var object
     */
    protected \$CI;

    /**
     * Database connection
     *
     * @var object
     */
    protected \$db;

    /**
     * Constructor
     */
    public function __construct()
    {
        \$this->CI =& get_instance();
        \$this->db = \$this->CI->db;
    }

    /**
     * Run the migrations
     *
     * @return void
     */
    public function up(): void
    {
        // Add your migration code here
        // Example:
        // \$this->db->query('CREATE TABLE products (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255))');
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down(): void
    {
        // Add your rollback code here
        // Example:
        // \$this->db->query('DROP TABLE products');
    }
}";

        if (file_put_contents($filepath, $content)) {
            echo "Migration created: {$filename}\n";
        } else {
            echo "Failed to create migration file\n";
            exit(1);
        }
    }

    public function fresh()
    {
        if (!$this->db_available) {
            // Try to get database connection with fallback
            if ($this->db_manager) {
                try {
                    $this->db_manager->getConnection();
                    echo "Database connection established!\n";
                } catch (Exception $e) {
                    echo "Error: Database not available. Migration commands require a database connection.\n";
                    echo "Error details: " . $e->getMessage() . "\n";
                    exit(1);
                }
            } else {
                echo "Error: Database not available. Migration commands require a database connection.\n";
                exit(1);
            }
        }

        echo "Dropping all tables...\n";

        // Get database connection from global CI instance
        $CI = get_instance();
        $db = $CI->db;

        // Get all tables in the database
        $tables = $db->list_tables();

        if (!empty($tables)) {
            // Disable foreign key checks
            $db->query('SET FOREIGN_KEY_CHECKS = 0');

            // Drop all tables
            foreach ($tables as $table) {
                echo "Dropping table: {$table}\n";
                $db->query("DROP TABLE IF EXISTS `{$table}`");
            }

            // Re-enable foreign key checks
            $db->query('SET FOREIGN_KEY_CHECKS = 1');
        }

        echo "All tables dropped successfully!\n";
        echo "Running all migrations...\n";

        // Create a new migration instance since the old one might have cached data
        $migration = new CI_Migration();

        // Run all migrations
        $migrations = $migration->migrate();
        if (empty($migrations)) {
            echo "No migrations to run.\n";
        } else {
            echo "Fresh migrations completed: " . implode(', ', $migrations) . "\n";
        }
    }
}
