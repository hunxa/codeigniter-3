<?php

declare(strict_types=1);

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Queue System for CodeIgniter 3
 * 
 * Laravel-like queue system for background job processing
 * 
 * @package CodeIgniter
 * @subpackage Libraries
 * @category Queue
 * @author Your Name
 */
class CI_Queue
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
     * Constructor
     */
    public function __construct()
    {
        $this->CI = get_instance();
        $this->db = $this->CI->db;

        // Create jobs table if it doesn't exist
        $this->createJobsTable();
    }

    /**
     * Push a job to the queue
     *
     * @param string $jobClass Job class name
     * @param mixed $payload Job payload
     * @param string $queue Queue name
     * @param int $delay Delay in seconds
     * @return int Job ID
     */
    public function push(string $jobClass, $payload = null, string $queue = 'default', int $delay = 0): int
    {
        $data = [
            'queue' => $queue,
            'payload' => json_encode([
                'job' => $jobClass,
                'data' => $payload
            ]),
            'attempts' => 0,
            'reserved_at' => null,
            'available_at' => date('Y-m-d H:i:s', time() + $delay),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('jobs', $data);
        return $this->db->insert_id();
    }

    /**
     * Push a job to a specific queue
     *
     * @param string $queue Queue name
     * @param string $jobClass Job class name
     * @param mixed $payload Job payload
     * @param int $delay Delay in seconds
     * @return int Job ID
     */
    public function pushOn(string $queue, string $jobClass, $payload = null, int $delay = 0): int
    {
        return $this->push($jobClass, $payload, $queue, $delay);
    }

    /**
     * Push a job with delay
     *
     * @param int $delay Delay in seconds
     * @param string $jobClass Job class name
     * @param mixed $payload Job payload
     * @param string $queue Queue name
     * @return int Job ID
     */
    public function later(int $delay, string $jobClass, $payload = null, string $queue = 'default'): int
    {
        return $this->push($jobClass, $payload, $queue, $delay);
    }

    /**
     * Start the queue worker
     *
     * @param string $queue Queue name
     * @param int $timeout Timeout in seconds
     * @param int $sleep Sleep time in seconds
     * @param int $tries Max retry attempts
     * @param int $maxJobs Max jobs to process
     * @param int $maxTime Max time to run
     * @return void
     */
    public function work(string $queue = 'default', int $timeout = 60, int $sleep = 3, int $tries = 3, int $maxJobs = 0, int $maxTime = 0): void
    {
        $startTime = time();
        $jobsProcessed = 0;

        echo "Queue worker started at " . date('Y-m-d H:i:s') . "\n";

        while (true) {
            // Check if we should stop
            if ($maxTime > 0 && (time() - $startTime) >= $maxTime) {
                echo "Max time reached, stopping worker\n";
                break;
            }

            if ($maxJobs > 0 && $jobsProcessed >= $maxJobs) {
                echo "Max jobs reached, stopping worker\n";
                break;
            }

            // Get next job
            $job = $this->getNextJob($queue);

            if ($job) {
                echo "Processing job {$job->id}...\n";
                $this->processJob($job, $tries);
                $jobsProcessed++;
            } else {
                echo "No jobs available, sleeping for {$sleep} seconds...\n";
                sleep($sleep);
            }
        }

        echo "Queue worker stopped at " . date('Y-m-d H:i:s') . "\n";
    }

    /**
     * Get the next job from the queue
     *
     * @param string $queue Queue name
     * @return object|null
     */
    protected function getNextJob(string $queue)
    {
        $this->db->where('queue', $queue);
        $this->db->where('reserved_at', null);
        $this->db->where('available_at <=', date('Y-m-d H:i:s'));
        $this->db->order_by('id', 'ASC');
        $this->db->limit(1);

        $query = $this->db->get('jobs');
        return $query->row();
    }

    /**
     * Process a job
     *
     * @param object $job Job object
     * @param int $tries Max retry attempts
     * @return void
     */
    protected function processJob($job, int $tries): void
    {
        // Mark job as reserved
        $this->db->where('id', $job->id);
        $this->db->update('jobs', [
            'reserved_at' => date('Y-m-d H:i:s'),
            'attempts' => $job->attempts + 1
        ]);

        try {
            // Decode payload
            $payload = json_decode($job->payload, true);
            $jobClass = $payload['job'];
            $data = $payload['data'] ?? null;

            // Load job class
            $jobPath = APPPATH . 'jobs/' . $jobClass . '.php';
            if (!file_exists($jobPath)) {
                throw new Exception("Job class not found: {$jobClass}");
            }

            require_once $jobPath;

            if (!class_exists($jobClass)) {
                throw new Exception("Job class not found: {$jobClass}");
            }

            // Create and execute job
            $jobInstance = new $jobClass();
            if (method_exists($jobInstance, 'handle')) {
                $jobInstance->handle($data);
            } else {
                throw new Exception("Job class does not have handle method: {$jobClass}");
            }

            // Job completed successfully, delete it
            $this->db->where('id', $job->id);
            $this->db->delete('jobs');

            echo "Job {$job->id} completed successfully\n";
        } catch (Exception $e) {
            echo "Job {$job->id} failed: " . $e->getMessage() . "\n";

            // Check if we should retry
            if ($job->attempts < $tries) {
                // Retry the job
                $this->db->where('id', $job->id);
                $this->db->update('jobs', [
                    'reserved_at' => null,
                    'available_at' => date('Y-m-d H:i:s', time() + 60) // Retry in 1 minute
                ]);
                echo "Job {$job->id} will be retried\n";
            } else {
                // Move to failed jobs
                $this->moveToFailed($job, $e->getMessage());
            }
        }
    }

    /**
     * Move job to failed jobs table
     *
     * @param object $job Job object
     * @param string $exception Exception message
     * @return void
     */
    protected function moveToFailed($job, string $exception): void
    {
        $this->createFailedJobsTable();

        $this->db->insert('failed_jobs', [
            'queue' => $job->queue,
            'payload' => $job->payload,
            'exception' => $exception,
            'failed_at' => date('Y-m-d H:i:s')
        ]);

        $this->db->where('id', $job->id);
        $this->db->delete('jobs');
    }

    /**
     * Create jobs table
     *
     * @return void
     */
    protected function createJobsTable(): void
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `jobs` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `queue` varchar(255) NOT NULL,
                `payload` longtext NOT NULL,
                `attempts` tinyint(3) unsigned NOT NULL,
                `reserved_at` int(10) unsigned DEFAULT NULL,
                `available_at` int(10) unsigned NOT NULL,
                `created_at` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id`),
                KEY `jobs_queue_index` (`queue`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    /**
     * Create failed jobs table
     *
     * @return void
     */
    protected function createFailedJobsTable(): void
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `failed_jobs` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `queue` varchar(255) NOT NULL,
                `payload` longtext NOT NULL,
                `exception` longtext NOT NULL,
                `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    /**
     * Restart queue workers
     *
     * @return void
     */
    public function restart(): void
    {
        // This would typically send a signal to restart workers
        // For now, just clear reserved jobs
        $this->db->where('reserved_at IS NOT NULL', null, false);
        $this->db->update('jobs', [
            'reserved_at' => null,
            'available_at' => date('Y-m-d H:i:s')
        ]);

        echo "Queue workers restarted\n";
    }

    /**
     * Show queue status
     *
     * @return void
     */
    public function status(): void
    {
        $this->db->select('queue, COUNT(*) as pending');
        $this->db->where('reserved_at', null);
        $this->db->group_by('queue');
        $query = $this->db->get('jobs');

        echo "Queue Status:\n";
        echo "=============\n";

        foreach ($query->result() as $row) {
            echo "{$row->queue}: {$row->pending} pending jobs\n";
        }

        // Show failed jobs count
        $this->db->select('COUNT(*) as failed');
        $failed = $this->db->get('failed_jobs')->row();
        echo "Failed: {$failed->failed} jobs\n";
    }

    /**
     * Show failed jobs
     *
     * @return void
     */
    public function showFailed(): void
    {
        $query = $this->db->get('failed_jobs');

        echo "Failed Jobs:\n";
        echo "============\n";

        foreach ($query->result() as $row) {
            $payload = json_decode($row->payload, true);
            echo "ID: {$row->id}\n";
            echo "Queue: {$row->queue}\n";
            echo "Job: {$payload['job']}\n";
            echo "Failed: {$row->failed_at}\n";
            echo "Exception: {$row->exception}\n";
            echo "---\n";
        }
    }

    /**
     * Retry a failed job
     *
     * @param int $jobId Failed job ID
     * @return void
     */
    public function retry(int $jobId): void
    {
        $this->db->where('id', $jobId);
        $failedJob = $this->db->get('failed_jobs')->row();

        if (!$failedJob) {
            echo "Failed job not found\n";
            return;
        }

        // Move back to jobs table
        $payload = json_decode($failedJob->payload, true);
        $this->db->insert('jobs', [
            'queue' => $failedJob->queue,
            'payload' => $failedJob->payload,
            'attempts' => 0,
            'reserved_at' => null,
            'available_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Remove from failed jobs
        $this->db->where('id', $jobId);
        $this->db->delete('failed_jobs');

        echo "Job {$jobId} moved back to queue\n";
    }

    /**
     * Flush all failed jobs
     *
     * @return void
     */
    public function flush(): void
    {
        $this->db->truncate('failed_jobs');
        echo "All failed jobs flushed\n";
    }
}
