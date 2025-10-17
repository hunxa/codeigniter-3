<?php

declare(strict_types=1);

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Send Welcome Email Listener
 * 
 * Example listener that logs welcome email sending
 * 
 * @package CodeIgniter
 * @subpackage Listeners
 * @category Events
 * @author Your Name
 */
class SendWelcomeEmail extends BaseListener
{
    /**
     * Handle the user.registered event
     *
     * @param mixed $payload User data from the event
     * @return void
     */
    public function handle($payload = null): void
    {
        $userEmail = $payload['email'] ?? 'unknown@example.com';
        $userName = $payload['name'] ?? 'User';

        log_message('info', "SendWelcomeEmail: Would send welcome email to {$userEmail} for user {$userName}");

        // In a real application, you would send the actual email here
        // $this->CI->load->library('email');
        // ... email sending logic
    }
}
