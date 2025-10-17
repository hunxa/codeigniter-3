<?php

declare(strict_types=1);

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration System for CodeIgniter 3
 * 
 * Laravel-like migration system for database schema management
 * 
 * @package CodeIgniter
 * @subpackage Libraries
 * @category Migration
 * @author Your Name
 */
class CI_Migration
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
	 * Migration path
	 *
	 * @var string
	 */
	protected $migrationPath;

	/**
	 * Migration table
	 *
	 * @var string
	 */
	protected $migrationTable = 'migrations';

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->CI = get_instance();
		$this->db = $this->CI->db;
		$this->migrationPath = APPPATH . 'migrations/';

		// Create migrations table if it doesn't exist
		$this->createMigrationsTable();
	}

	/**
	 * Run all pending migrations
	 *
	 * @return array
	 */
	public function migrate(): array
	{
		$migrations = $this->getPendingMigrations();
		$run = [];

		foreach ($migrations as $migration) {
			echo "Running migration: {$migration}\n";

			$migrationClass = $this->loadMigration($migration);
			if ($migrationClass) {
				try {
					$migrationClass->up();
					$this->recordMigration($migration);
					$run[] = $migration;
					echo "✓ Migration {$migration} completed\n";
				} catch (Exception $e) {
					echo "✗ Migration {$migration} failed: " . $e->getMessage() . "\n";
					throw $e;
				}
			}
		}

		return $run;
	}

	/**
	 * Rollback the last batch of migrations
	 *
	 * @param int $steps Number of steps to rollback
	 * @return array
	 */
	public function rollback(int $steps = 1): array
	{
		$migrations = $this->getLastBatchMigrations($steps);
		$rolledBack = [];

		foreach (array_reverse($migrations) as $migration) {
			echo "Rolling back migration: {$migration}\n";

			$migrationClass = $this->loadMigration($migration);
			if ($migrationClass) {
				try {
					$migrationClass->down();
					$this->removeMigration($migration);
					$rolledBack[] = $migration;
					echo "✓ Migration {$migration} rolled back\n";
				} catch (Exception $e) {
					echo "✗ Migration {$migration} rollback failed: " . $e->getMessage() . "\n";
					throw $e;
				}
			}
		}

		return $rolledBack;
	}

	/**
	 * Reset all migrations
	 *
	 * @return array
	 */
	public function reset(): array
	{
		$migrations = $this->getAllMigrations();
		$reset = [];

		foreach (array_reverse($migrations) as $migration) {
			echo "Resetting migration: {$migration}\n";

			$migrationClass = $this->loadMigration($migration);
			if ($migrationClass) {
				try {
					$migrationClass->down();
					$this->removeMigration($migration);
					$reset[] = $migration;
					echo "✓ Migration {$migration} reset\n";
				} catch (Exception $e) {
					echo "✗ Migration {$migration} reset failed: " . $e->getMessage() . "\n";
					throw $e;
				}
			}
		}

		return $reset;
	}

	/**
	 * Refresh migrations (reset and migrate)
	 *
	 * @return array
	 */
	public function refresh(): array
	{
		echo "Resetting all migrations...\n";
		$this->reset();

		echo "Running all migrations...\n";
		return $this->migrate();
	}

	/**
	 * Show migration status
	 *
	 * @return void
	 */
	public function status(): void
	{
		$pending = $this->getPendingMigrations();
		$ran = $this->getRanMigrations();

		echo "Migration Status:\n";
		echo "================\n";
		echo "Pending: " . count($pending) . " migrations\n";
		echo "Ran: " . count($ran) . " migrations\n\n";

		if (!empty($pending)) {
			echo "Pending migrations:\n";
			foreach ($pending as $migration) {
				echo "  - {$migration}\n";
			}
		}

		if (!empty($ran)) {
			echo "\nRan migrations:\n";
			foreach ($ran as $migration) {
				echo "  ✓ {$migration}\n";
			}
		}
	}

	/**
	 * Create a new migration file
	 *
	 * @param string $name Migration name
	 * @return string
	 */
	public function make(string $name): string
	{
		$timestamp = date('Y_m_d_His');
		$className = $this->getMigrationClassName($name);
		$filename = $timestamp . '_' . $name . '.php';
		$filepath = $this->migrationPath . $filename;

		$content = $this->getMigrationTemplate($className);

		if (file_put_contents($filepath, $content)) {
			echo "Created migration: {$filename}\n";
			return $filename;
		} else {
			throw new Exception("Failed to create migration file: {$filename}");
		}
	}

	/**
	 * Get pending migrations
	 *
	 * @return array
	 */
	protected function getPendingMigrations(): array
	{
		$files = $this->getMigrationFiles();
		$ran = $this->getRanMigrations();

		return array_diff($files, $ran);
	}

	/**
	 * Get all migration files
	 *
	 * @return array
	 */
	protected function getMigrationFiles(): array
	{
		$files = [];
		$path = $this->migrationPath;

		if (is_dir($path)) {
			$files = array_diff(scandir($path), ['.', '..']);
			$files = array_filter($files, function ($file) {
				return pathinfo($file, PATHINFO_EXTENSION) === 'php';
			});
			sort($files);
		}

		return $files;
	}

	/**
	 * Get ran migrations
	 *
	 * @return array
	 */
	protected function getRanMigrations(): array
	{
		$this->db->select('migration');
		$this->db->order_by('id', 'ASC');
		$query = $this->db->get($this->migrationTable);

		return array_column($query->result_array(), 'migration');
	}

	/**
	 * Get last batch migrations
	 *
	 * @param int $steps Number of steps
	 * @return array
	 */
	protected function getLastBatchMigrations(int $steps): array
	{
		$this->db->select('migration');
		$this->db->order_by('id', 'DESC');
		$this->db->limit($steps);
		$query = $this->db->get($this->migrationTable);

		return array_column($query->result_array(), 'migration');
	}

	/**
	 * Get all migrations
	 *
	 * @return array
	 */
	protected function getAllMigrations(): array
	{
		return $this->getRanMigrations();
	}

	/**
	 * Load migration class
	 *
	 * @param string $migration Migration filename
	 * @return object|null
	 */
	protected function loadMigration(string $migration)
	{
		$filepath = $this->migrationPath . $migration;

		if (!file_exists($filepath)) {
			return null;
		}

		require_once $filepath;

		$className = $this->getMigrationClassName($migration);

		if (class_exists($className)) {
			return new $className();
		}

		return null;
	}

	/**
	 * Get migration class name from filename
	 *
	 * @param string $filename Migration filename
	 * @return string
	 */
	protected function getMigrationClassName(string $filename): string
	{
		$name = pathinfo($filename, PATHINFO_FILENAME);
		$name = preg_replace('/^\d+_\d+_\d+_\d+_/', '', $name);
		$name = str_replace('_', ' ', $name);
		$name = ucwords($name);
		$name = str_replace(' ', '', $name);

		return $name;
	}

	/**
	 * Record migration as run
	 *
	 * @param string $migration Migration filename
	 * @return void
	 */
	protected function recordMigration(string $migration): void
	{
		$this->db->insert($this->migrationTable, [
			'migration' => $migration,
			'batch' => $this->getNextBatchNumber()
		]);
	}

	/**
	 * Remove migration record
	 *
	 * @param string $migration Migration filename
	 * @return void
	 */
	protected function removeMigration(string $migration): void
	{
		$this->db->where('migration', $migration);
		$this->db->delete($this->migrationTable);
	}

	/**
	 * Get next batch number
	 *
	 * @return int
	 */
	protected function getNextBatchNumber(): int
	{
		$this->db->select_max('batch');
		$query = $this->db->get($this->migrationTable);
		$result = $query->row();

		return ($result->batch ?? 0) + 1;
	}

	/**
	 * Create migrations table
	 *
	 * @return void
	 */
	protected function createMigrationsTable(): void
	{
		$this->db->query("
            CREATE TABLE IF NOT EXISTS `{$this->migrationTable}` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `migration` varchar(255) NOT NULL,
                `batch` int(11) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
	}

	/**
	 * Get migration template
	 *
	 * @param string $className Class name
	 * @return string
	 */
	protected function getMigrationTemplate(string $className): string
	{
		return "<?php

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
        // \$this->db->query('CREATE TABLE users (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255))');
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
        // \$this->db->query('DROP TABLE users');
    }
}";
	}
}
