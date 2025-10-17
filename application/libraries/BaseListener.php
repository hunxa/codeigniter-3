<?php

declare(strict_types=1);

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Base Listener Class
 * 
 * All event listeners should extend this class
 * 
 * @package CodeIgniter
 * @subpackage Libraries
 * @category Events
 * @author Your Name
 */
abstract class BaseListener
{
    /**
     * CI instance
     *
     * @var object
     */
    protected $CI;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->CI = &get_instance();
    }

    /**
     * Handle the event
     * 
     * This method must be implemented by all listeners
     *
     * @param mixed $payload The event payload data
     * @return void
     */
    abstract public function handle($payload = null): void;
}
