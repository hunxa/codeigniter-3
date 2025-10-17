<?php

declare(strict_types=1);

/**
 * Commands Registry
 * 
 * Maps command names to their handler files
 * 
 * @package CodeIgniter
 * @subpackage Commands
 * @category Registry
 * @author Your Name
 */

return [
    // Queue commands
    'queue:work' => ['class' => 'QueueCommand', 'method' => 'work'],
    'queue:status' => ['class' => 'QueueCommand', 'method' => 'status'],
    'queue:failed' => ['class' => 'QueueCommand', 'method' => 'failed'],
    'queue:retry' => ['class' => 'QueueCommand', 'method' => 'retry'],
    'queue:flush' => ['class' => 'QueueCommand', 'method' => 'flush'],

    // Migration commands
    'migrate' => ['class' => 'MigrationCommand', 'method' => 'migrate'],
    'migrate:rollback' => ['class' => 'MigrationCommand', 'method' => 'rollback'],
    'migrate:reset' => ['class' => 'MigrationCommand', 'method' => 'reset'],
    'migrate:refresh' => ['class' => 'MigrationCommand', 'method' => 'refresh'],
    'migrate:fresh' => ['class' => 'MigrationCommand', 'method' => 'fresh'],
    'migrate:status' => ['class' => 'MigrationCommand', 'method' => 'status'],
    'make:migration' => ['class' => 'MigrationCommand', 'method' => 'make'],

    // Example commands
    'hello' => ['class' => 'ExampleCommand', 'method' => 'hello'],
    'info' => ['class' => 'ExampleCommand', 'method' => 'info'],
    'version' => ['class' => 'ExampleCommand', 'method' => 'version'],
];
