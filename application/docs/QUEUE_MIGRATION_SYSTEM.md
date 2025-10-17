# Queue and Migration System for CodeIgniter 3

This system provides Laravel-like queue and migration functionality for CodeIgniter 3.

## Features

### Queue System
- Background job processing
- Multiple queue support
- Job retry mechanism
- Failed job handling
- Delayed jobs
- Queue worker management

### Migration System
- Database schema versioning
- Migrate, rollback, reset, refresh
- Batch management
- Migration status tracking
- Auto-generated migration files

## Installation

The system is already installed with the following components:

### Files Created:
- `bin/queue.php` - Queue worker command
- `bin/migrate.php` - Migration command
- `system/libraries/Queue.php` - Queue system library
- `system/libraries/Migration.php` - Migration system library
- `application/jobs/` - Directory for job classes
- `application/migrations/` - Directory for migration files

## Queue System Usage

### 1. Creating Jobs

Create job classes in `application/jobs/` directory:

```php
<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SendEmail
{
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    public function handle($data = null): void
    {
        // Your job logic here
        $this->CI->load->library('email');
        // ... send email
    }
}
```

### 2. Pushing Jobs to Queue

```php
// In your controller
$this->load->library('queue');

// Push job to default queue
$jobId = $this->queue->push('SendEmail', [
    'to' => 'user@example.com',
    'subject' => 'Welcome',
    'message' => 'Welcome to our app!'
]);

// Push job to specific queue
$jobId = $this->queue->pushOn('high', 'ProcessPayment', $paymentData);

// Push delayed job
$jobId = $this->queue->later(60, 'SendEmail', $emailData); // 60 seconds delay
```

### 3. Running Queue Workers

```bash
# Start queue worker
php bin/queue.php work

# Start worker for specific queue
php bin/queue.php work --queue=high,default

# Start worker with custom options
php bin/queue.php work --timeout=120 --sleep=5 --tries=5

# Check queue status
php bin/queue.php status

# Show failed jobs
php bin/queue.php failed

# Retry failed job
php bin/queue.php retry 123

# Flush failed jobs
php bin/queue.php flush
```

## Migration System Usage

### 1. Creating Migrations

```bash
# Create new migration
php bin/migrate.php make create_users_table

# This creates: application/migrations/YYYY_MM_DD_HHMMSS_create_users_table.php
```

### 2. Migration File Structure

```php
<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CreateUsersTable
{
    protected $CI;
    protected $db;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->db = $this->CI->db;
    }

    public function up(): void
    {
        // Migration code here
        $this->db->query("CREATE TABLE users (...)");
    }

    public function down(): void
    {
        // Rollback code here
        $this->db->query("DROP TABLE users");
    }
}
```

### 3. Running Migrations

```bash
# Run all pending migrations
php bin/migrate.php migrate

# Check migration status
php bin/migrate.php status

# Rollback last migration
php bin/migrate.php rollback

# Rollback multiple migrations
php bin/migrate.php rollback --step=3

# Reset all migrations
php bin/migrate.php reset

# Refresh (reset + migrate)
php bin/migrate.php refresh
```

## Database Tables

The system automatically creates these tables:

### Jobs Table
```sql
CREATE TABLE `jobs` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `queue` varchar(255) NOT NULL,
    `payload` longtext NOT NULL,
    `attempts` tinyint(3) unsigned NOT NULL,
    `reserved_at` int(10) unsigned DEFAULT NULL,
    `available_at` int(10) unsigned NOT NULL,
    `created_at` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `jobs_queue_index` (`queue`)
);
```

### Failed Jobs Table
```sql
CREATE TABLE `failed_jobs` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `queue` varchar(255) NOT NULL,
    `payload` longtext NOT NULL,
    `exception` longtext NOT NULL,
    `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
);
```

### Migrations Table
```sql
CREATE TABLE `migrations` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `migration` varchar(255) NOT NULL,
    `batch` int(11) NOT NULL,
    PRIMARY KEY (`id`)
);
```

## Examples

### Example Job: Send Welcome Email

```php
// application/jobs/SendWelcomeEmail.php
class SendWelcomeEmail
{
    public function handle($data = null): void
    {
        $this->CI->load->library('email');
        
        $this->CI->email->from('noreply@example.com', 'Your App');
        $this->CI->email->to($data['email']);
        $this->CI->email->subject('Welcome!');
        $this->CI->email->message('Welcome to our application!');
        
        if (!$this->CI->email->send()) {
            throw new Exception('Failed to send email');
        }
    }
}
```

### Example Migration: Create Users Table

```php
// application/migrations/YYYY_MM_DD_HHMMSS_create_users_table.php
class CreateUsersTable
{
    public function up(): void
    {
        $this->db->query("
            CREATE TABLE `users` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL,
                `password` varchar(255) NOT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `users_email_unique` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    public function down(): void
    {
        $this->db->query("DROP TABLE IF EXISTS `users`");
    }
}
```

## Testing

Visit `/queuetest` to see the demo controller that shows how to use both systems.

## Best Practices

1. **Jobs**: Keep jobs simple and focused on one task
2. **Migrations**: Always provide both `up()` and `down()` methods
3. **Queue Workers**: Run workers in production with process managers like Supervisor
4. **Error Handling**: Implement proper error handling in jobs
5. **Database**: Use transactions in migrations when possible

## Production Deployment

For production, consider:

1. **Process Manager**: Use Supervisor to manage queue workers
2. **Monitoring**: Monitor queue status and failed jobs
3. **Logging**: Enable proper logging for debugging
4. **Backup**: Regular database backups including migration state
