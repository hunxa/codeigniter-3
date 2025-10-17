<?php

/**
 * CodeIgniter Bootstrap
 *
 * This file contains the bootstrap logic for CodeIgniter applications.
 * It handles environment setup, path resolution, and framework initialization.
 *
 * @package	CodeIgniter
 * @author	CodeIgniter Dev Team
 * @copyright	Copyright (c) 2008 - 2019, EllisLab, Inc. & British Columbia Institute of Technology
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */

class Bootstrap
{
    public function __construct()
    {
        $this->loadComposerAutoloader();
        $this->loadEnvironment();
    }

    public function loadComposerAutoloader()
    {
        if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
            require_once __DIR__ . '/../vendor/autoload.php';
        }
    }

    public function loadEnvironment()
    {
        if (class_exists('Dotenv\Dotenv')) {
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
            $dotenv->load();
        }
    }

    public function configure()
    {
        require_once __DIR__ . '/helpers/env_helper.php';

        define('ENVIRONMENT', env('APP_ENV', 'development'));

        switch (ENVIRONMENT) {
            case 'development':
                error_reporting(-1);
                ini_set('display_errors', 1);
                break;

            case 'testing':
            case 'production':
                ini_set('display_errors', 0);
                error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
                break;

            default:
                header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
                echo 'The application environment is not set correctly.';
                exit(1); // EXIT_ERROR
        }
    }
}
