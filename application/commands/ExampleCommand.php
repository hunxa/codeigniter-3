<?php

declare(strict_types=1);

/**
 * Example Commands
 * 
 * @package CodeIgniter
 * @subpackage Commands
 * @category Example
 * @author Your Name
 */
class ExampleCommand
{
    public function hello($args)
    {
        $name = $args[0] ?? 'World';
        echo "Hello, {$name}!\n";
    }

    public function info()
    {
        echo "CodeIgniter CLI Example Command\n";
        echo "This demonstrates how easy it is to add new commands.\n";
    }

    public function version()
    {
        echo "CodeIgniter CLI v1.0.0\n";
        echo "Built with love for CodeIgniter 3\n";
    }
}
