<?php

declare(strict_types=1);

/**
 * Command Loader
 * 
 * @package CodeIgniter
 * @subpackage CLI
 * @category Loader
 * @author Your Name
 */
class CommandLoader
{
    protected $commands;
    protected $instances = [];
    protected $bootstrap;

    public function __construct($commands, $bootstrap)
    {
        $this->commands = $commands;
        $this->bootstrap = $bootstrap;
    }

    /**
     * Execute command
     */
    public function execute($command, $args)
    {
        if (!isset($this->commands[$command])) {
            throw new Exception("Unknown command: {$command}");
        }

        $config = $this->commands[$command];
        $instance = $this->getInstance($config['class']);
        $method = $config['method'];

        if (!method_exists($instance, $method)) {
            throw new Exception("Method '{$method}' not found in {$config['class']}");
        }

        $instance->$method($args);
    }

    /**
     * Get command instance
     */
    protected function getInstance($commandClass)
    {
        if (!isset($this->instances[$commandClass])) {
            $this->instances[$commandClass] = $this->createInstance($commandClass);
        }

        return $this->instances[$commandClass];
    }

    /**
     * Create command instance
     */
    protected function createInstance($commandClass)
    {
        // Create instance using Composer autoloading
        switch ($commandClass) {
            case 'QueueCommand':
                return new QueueCommand($this->bootstrap['queue'], $this->bootstrap['db_available']);
            case 'MigrationCommand':
                return new MigrationCommand($this->bootstrap['migration'], $this->bootstrap['db_available'], $this->bootstrap['db_manager']);
            case 'ExampleCommand':
                return new ExampleCommand();
            default:
                throw new Exception("Unknown command class: {$commandClass}");
        }
    }
}
