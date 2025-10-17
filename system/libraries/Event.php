<?php

declare(strict_types=1);

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Event System for CodeIgniter 3
 * 
 * Provides Laravel-like event firing and listening capabilities
 * 
 * @package CodeIgniter
 * @subpackage Libraries
 * @category Events
 * @author Your Name
 * @link https://codeigniter.com/user_guide/libraries/event.html
 */
class CI_Event
{
    /**
     * CI instance
     *
     * @var object
     */
    protected $CI;

    /**
     * Event listeners configuration
     *
     * @var array
     */
    protected $events = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->CI = &get_instance();

        // Load events configuration
        $this->CI->config->load('events');
        $this->events = $this->CI->config->item('events') ?: array();

        log_message('info', "Event::__construct - Loaded events: " . print_r($this->events, true));
    }

    /**
     * Fire an event
     *
     * @param string $eventName The name of the event to fire
     * @param mixed $payload Optional data to pass to listeners
     * @return void
     */
    public function fire(string $eventName, $payload = null): void
    {
        $this->dispatch($eventName, $payload);
    }

    /**
     * Dispatch an event to its listeners
     *
     * @param string $eventName The name of the event to dispatch
     * @param mixed $payload Optional data to pass to listeners
     * @return void
     */
    public function dispatch(string $eventName, $payload = null): void
    {
        log_message('info', "Event::dispatch called for event: {$eventName}");

        if (!isset($this->events[$eventName])) {
            log_message('info', "Event::dispatch - No listeners found for event: {$eventName}");
            return;
        }

        $listeners = $this->events[$eventName];
        log_message('info', "Event::dispatch - Found listeners: " . print_r($listeners, true));

        if (!is_array($listeners)) {
            log_message('info', "Event::dispatch - Listeners is not an array");
            return;
        }

        foreach ($listeners as $listener) {
            log_message('info', "Event::dispatch - Calling listener: {$listener}");
            $this->callListener($listener, $payload);
        }
    }

    /**
     * Call a specific listener
     *
     * @param string $listenerName The name of the listener class
     * @param mixed $payload Optional data to pass to the listener
     * @return void
     */
    protected function callListener(string $listenerName, $payload = null): void
    {
        $listenerClass = $listenerName;

        // Check if listener class exists in application/listeners
        $listenerPath = APPPATH . 'listeners/' . $listenerClass . '.php';

        if (!file_exists($listenerPath)) {
            log_message('error', 'Event listener not found: ' . $listenerClass);
            return;
        }

        // Load BaseListener first
        if (!class_exists('BaseListener')) {
            $baseListenerPath = APPPATH . 'libraries/BaseListener.php';
            if (file_exists($baseListenerPath)) {
                require_once $baseListenerPath;
            }
        }

        // Load the listener class
        require_once $listenerPath;

        if (!class_exists($listenerClass)) {
            log_message('error', 'Event listener class not found: ' . $listenerClass);
            return;
        }

        try {
            $listener = new $listenerClass();

            if (method_exists($listener, 'handle')) {
                $listener->handle($payload);
            } else {
                log_message('error', 'Event listener does not have handle method: ' . $listenerClass);
            }
        } catch (Exception $e) {
            log_message('error', 'Event listener error: ' . $e->getMessage());
        }
    }

    /**
     * Register an event listener at runtime
     *
     * @param string $eventName The name of the event
     * @param string $listenerName The name of the listener class
     * @return void
     */
    public function listen(string $eventName, string $listenerName): void
    {
        if (!isset($this->events[$eventName])) {
            $this->events[$eventName] = array();
        }

        $this->events[$eventName][] = $listenerName;
    }

    /**
     * Get all registered events
     *
     * @return array
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * Get listeners for a specific event
     *
     * @param string $eventName The name of the event
     * @return array
     */
    public function getListeners(string $eventName): array
    {
        return isset($this->events[$eventName]) ? $this->events[$eventName] : array();
    }
}
