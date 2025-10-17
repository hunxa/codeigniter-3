<?php

declare(strict_types=1);

/**
 * Queue Commands
 * 
 * @package CodeIgniter
 * @subpackage Commands
 * @category Queue
 * @author Your Name
 */
class QueueCommand
{
    protected $queue;
    protected $db_available;

    public function __construct($queue, $db_available)
    {
        $this->queue = $queue;
        $this->db_available = $db_available;
    }

    public function work($args)
    {
        if (!$this->db_available) {
            echo "Error: Database not available. Queue commands require a database connection.\n";
            exit(1);
        }

        $queue = $args[0] ?? 'default';
        $timeout = (int) ($args[1] ?? 60);
        $sleep = (int) ($args[2] ?? 3);
        $tries = (int) ($args[3] ?? 3);

        echo "Starting queue worker...\n";
        echo "Queue: {$queue}\n";
        echo "Timeout: {$timeout}s\n";
        echo "Sleep: {$sleep}s\n";
        echo "Tries: {$tries}\n";
        echo "Press Ctrl+C to stop\n\n";

        $this->queue->work($queue, $timeout, $sleep, $tries);
    }

    public function status()
    {
        if (!$this->db_available) {
            echo "Error: Database not available. Queue commands require a database connection.\n";
            exit(1);
        }
        $this->queue->status();
    }

    public function failed()
    {
        if (!$this->db_available) {
            echo "Error: Database not available. Queue commands require a database connection.\n";
            exit(1);
        }
        $this->queue->showFailed();
    }

    public function retry($args)
    {
        if (!$this->db_available) {
            echo "Error: Database not available. Queue commands require a database connection.\n";
            exit(1);
        }
        $jobId = $args[0] ?? null;
        if ($jobId) {
            $this->queue->retry((int)$jobId);
        } else {
            echo "Please provide a job ID to retry\n";
        }
    }

    public function flush()
    {
        if (!$this->db_available) {
            echo "Error: Database not available. Queue commands require a database connection.\n";
            exit(1);
        }
        $this->queue->flush();
    }
}
