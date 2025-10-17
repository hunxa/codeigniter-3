<?php

declare(strict_types=1);

/**
 * Command Discovery
 * 
 * @package CodeIgniter
 * @subpackage CLI
 * @category Discovery
 * @author Your Name
 */
class CommandDiscovery
{
    protected $commands;
    protected $descriptions = [
        'queue:work' => 'Start queue worker',
        'queue:status' => 'Show queue status',
        'queue:failed' => 'Show failed jobs',
        'queue:retry' => 'Retry failed job',
        'queue:flush' => 'Flush failed jobs',
        'migrate' => 'Run migrations',
        'migrate:rollback' => 'Rollback migrations',
        'migrate:reset' => 'Reset all migrations',
        'migrate:refresh' => 'Refresh migrations',
        'migrate:fresh' => 'Drop all tables and re-run migrations',
        'migrate:status' => 'Show migration status',
        'make:migration' => 'Create migration',
        'hello' => 'Say hello',
        'info' => 'Show CLI info',
        'version' => 'Show version information',
    ];

    public function __construct($commands, $bootstrap)
    {
        $this->commands = $commands;
    }

    /**
     * Generate help text
     */
    public function generateHelp()
    {
        $output = "CodeIgniter CLI\n\nUsage: php ci <command> [options]\n\nAvailable commands:\n";

        $categories = [
            'Queue Commands' => [],
            'Migration Commands' => [],
            'Other Commands' => []
        ];

        foreach ($this->commands as $command => $config) {
            $description = $this->descriptions[$command] ?? 'No description';

            if (strpos($command, 'queue:') === 0) {
                $categories['Queue Commands'][] = "  {$command}" . str_repeat(' ', max(1, 25 - strlen($command))) . $description;
            } elseif (strpos($command, 'migrate') === 0 || strpos($command, 'make:') === 0) {
                $categories['Migration Commands'][] = "  {$command}" . str_repeat(' ', max(1, 25 - strlen($command))) . $description;
            } else {
                $categories['Other Commands'][] = "  {$command}" . str_repeat(' ', max(1, 25 - strlen($command))) . $description;
            }
        }

        foreach ($categories as $category => $commands) {
            if (!empty($commands)) {
                $output .= "{$category}:\n";
                foreach ($commands as $command) {
                    $output .= "{$command}\n";
                }
                $output .= "\n";
            }
        }

        $output .= "  list                                          Show this help\n\n";
        $output .= "Examples:\n";
        $output .= "  php ci queue:work\n";
        $output .= "  php ci migrate\n";
        $output .= "  php ci make:migration create_users_table\n";
        $output .= "  php ci hello\n";

        return $output;
    }
}
