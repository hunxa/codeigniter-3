<?php

declare(strict_types=1);

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Send Email Job
 * 
 * Example job for sending emails
 * 
 * @package CodeIgniter
 * @subpackage Jobs
 * @category Queue
 * @author Your Name
 */
class SendEmail
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
     * Handle the job
     *
     * @param mixed $data Job data
     * @return void
     */
    public function handle($data = null): void
    {
        if (!$data || !isset($data['to'])) {
            throw new Exception('Email data is required');
        }

        $to = $data['to'];
        $subject = $data['subject'] ?? 'No Subject';
        $message = $data['message'] ?? 'No Message';

        // Load email library
        $this->CI->load->library('email');

        // Configure email
        $this->CI->email->from('noreply@example.com', 'Your App');
        $this->CI->email->to($to);
        $this->CI->email->subject($subject);
        $this->CI->email->message($message);

        // Send email
        if ($this->CI->email->send()) {
            log_message('info', "Email sent to: {$to}");
        } else {
            throw new Exception("Failed to send email to: {$to}");
        }
    }
}
