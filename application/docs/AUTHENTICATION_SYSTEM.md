# Robust Multi-Guard Authentication System

A comprehensive authentication system for CodeIgniter 3 with multi-guard support, featuring session-based, JWT, API token, and Sanctum authentication methods.

## Table of Contents

1. [Features](#features)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Guards](#guards)
5. [Usage](#usage)
6. [API Documentation](#api-documentation)
7. [Security Features](#security-features)
8. [Examples](#examples)
9. [Troubleshooting](#troubleshooting)

## Features

### 🔐 Multi-Guard Support
- **Session Guard**: Traditional session-based authentication
- **JWT Guard**: JSON Web Token authentication for APIs
- **API Guard**: API token-based authentication
- **Sanctum Guard**: Personal access token authentication

### 🛡️ Security Features
- Password hashing with bcrypt, argon2i, and argon2id
- Rate limiting for login attempts
- Account lockout protection
- Two-factor authentication (TOTP, SMS, Email)
- Email verification
- Password reset functionality
- Remember me tokens
- Device management
- Audit logging

### 🔧 Advanced Features
- Role-based access control (RBAC)
- Permission-based authorization
- Social authentication (Google, Facebook, GitHub)
- Multi-factor authentication
- Device fingerprinting
- Session management
- Token refresh
- API rate limiting

## Installation

### 1. Database Setup

Run the migrations to create the required tables:

```bash
php ci migrate
```

### 2. Configuration

Copy the authentication configuration:

```php
// application/config/auth.php
$config['auth_default_guard'] = 'session';
$config['auth_guards'] = array(
    'session' => array(
        'driver' => 'session',
        'provider' => 'users',
    ),
    'jwt' => array(
        'driver' => 'jwt',
        'provider' => 'users',
        'expire' => 3600,
    ),
    // ... more guards
);
```

### 3. Autoload

Add the authentication library to your autoload:

```php
// application/config/autoload.php
$autoload['libraries'] = array('auth');
$autoload['helpers'] = array('auth');
```

## Configuration

### Basic Configuration

```php
// application/config/auth.php

// Default guard
$config['auth_default_guard'] = 'session';

// Password hashing
$config['auth_password_hash'] = 'bcrypt';
$config['auth_password_rounds'] = 12;

// Rate limiting
$config['auth_rate_limit'] = array(
    'enabled' => TRUE,
    'max_attempts' => 5,
    'decay_minutes' => 15,
);

// Two-factor authentication
$config['auth_2fa_enabled'] = TRUE;
$config['auth_2fa_methods'] = array('totp', 'sms', 'email');
```

### JWT Configuration

```php
$config['auth_jwt'] = array(
    'secret' => 'your-secret-key-change-this-in-production',
    'algorithm' => 'HS256',
    'issuer' => 'your-app-name',
    'audience' => 'your-app-users',
    'expire' => 3600,
    'refresh_expire' => 604800,
);
```

## Guards

### Session Guard

Traditional session-based authentication:

```php
// Login
$credentials = array(
    'email' => 'user@example.com',
    'password' => 'password123'
);

if (auth()->attempt($credentials, true)) { // true for remember me
    // User logged in
}

// Check authentication
if (auth()->check()) {
    $user = auth()->user();
}

// Logout
auth()->logout();
```

### JWT Guard

JSON Web Token authentication:

```php
// Login with JWT
$guard = auth()->guard('jwt');
if ($guard->attempt($credentials)) {
    $tokens = $guard->login(auth()->user(), true);
    // Returns: array('access_token' => '...', 'refresh_token' => '...')
}

// Refresh token
$new_tokens = $guard->refreshToken($refresh_token);
```

### API Guard

API token authentication:

```php
// Create API token
$guard = auth()->guard('api');
$token = $guard->createToken($user, 'My API Token', 3600);

// Use token in requests
// Header: Authorization: Bearer {token}
// Header: API-Key: {token}
// Query: ?api_token={token}
```

## Usage

### Basic Authentication

```php
class Dashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('auth');
        $this->load->helper('auth');
        
        // Require authentication
        require_auth();
    }
    
    public function index()
    {
        $user = auth_user();
        $data['user'] = $user;
        $this->load->view('dashboard', $data);
    }
}
```

### Role-Based Access Control

```php
class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('auth');
        $this->load->helper('auth');
        
        // Require admin role
        require_role('admin');
    }
    
    public function users()
    {
        // Only admins can access this
    }
}
```

### Permission-Based Authorization

```php
class Posts extends CI_Controller
{
    public function create()
    {
        // Check permission
        if (!has_permission('posts.create')) {
            show_error('Insufficient permissions', 403);
        }
        
        // Create post
    }
    
    public function delete($id)
    {
        // Check permission
        if (!has_permission('posts.delete')) {
            show_error('Insufficient permissions', 403);
        }
        
        // Delete post
    }
}
```

### Two-Factor Authentication

```php
// Enable 2FA
$result = auth()->enableTwoFactor($user, 'totp');
// Returns: array('secret' => '...', 'qr_code' => '...', 'method' => 'totp')

// Verify 2FA
if (auth()->verifyTwoFactor($user, $code)) {
    // 2FA verified
}
```

### API Authentication

```php
class Api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('auth');
        $this->load->helper('auth');
        
        // Require API authentication
        require_auth('jwt');
    }
    
    public function users()
    {
        $this->output->set_content_type('application/json');
        
        $users = $this->User_model->getAll();
        
        $this->output->set_output(json_encode(array(
            'success' => true,
            'data' => $users
        )));
    }
}
```

## API Documentation

### Authentication Endpoints

#### POST /auth/login
Login with email and password.

**Request:**
```json
{
    "email": "user@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "token_type": "Bearer",
        "expires_in": 3600
    }
}
```

#### POST /auth/register
Register a new user.

**Request:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirm": "password123"
}
```

#### POST /auth/logout
Logout the current user.

#### POST /auth/refresh
Refresh JWT token.

**Request:**
```json
{
    "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
}
```

#### POST /auth/forgot-password
Request password reset.

**Request:**
```json
{
    "email": "user@example.com"
}
```

#### POST /auth/reset-password
Reset password with token.

**Request:**
```json
{
    "token": "reset_token_here",
    "password": "new_password123",
    "password_confirm": "new_password123"
}
```

### Helper Functions

#### Authentication Helpers

```php
// Check authentication
auth_check(); // Returns boolean
auth_guest(); // Returns boolean

// Get user
$user = auth_user(); // Returns user object or null
$user_id = auth_id(); // Returns user ID or null

// Authentication
auth_attempt($credentials, $remember, $guard);
auth_login($user, $remember, $guard);
auth_logout($guard);

// Registration
auth_register($data, $auto_login, $guard);

// Guards
$guard = auth_guard('jwt');
```

#### Authorization Helpers

```php
// Role checks
has_role('admin');
has_any_role(['admin', 'moderator']);

// Permission checks
has_permission('users.create');
has_permission(['users.create', 'users.update']);

// Redirects
require_auth();
require_guest();
require_role('admin');
require_permission('users.create');
```

#### Utility Helpers

```php
// Password hashing
$hash = auth_hash_password('password123');
$valid = auth_verify_password('password123', $hash);

// Token generation
$token = auth_generate_token(32);
$remember_token = auth_generate_remember_token();
$reset_token = auth_generate_reset_token();
$api_token = auth_generate_api_token(64);

// JWT tokens
$jwt = auth_generate_jwt_token($payload, $secret, 'HS256');
$payload = auth_verify_jwt_token($jwt, $secret, 'HS256');
```

## Security Features

### Rate Limiting

```php
// Check rate limit
if (!auth_rate_limit('login', 5, 15)) {
    show_error('Too many login attempts. Please try again later.', 429);
}
```

### Account Lockout

```php
// Check if account is locked
if (auth_middleware()->isAccountLocked($user)) {
    show_error('Account is locked due to too many failed attempts.', 423);
}
```

### Two-Factor Authentication

```php
// Enable 2FA
$result = auth()->enableTwoFactor($user, 'totp');

// Verify 2FA
if (auth()->verifyTwoFactor($user, $code)) {
    // Proceed
}
```

### Device Management

```php
// Trust device
auth_middleware()->trustDevice($user, $device_info);

// Check trusted device
if (auth_middleware()->isTrustedDevice($user, $device_info)) {
    // Skip 2FA
}
```

## Examples

### Complete Login Controller

```php
class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('auth');
        $this->load->library('form_validation');
        $this->load->helper('auth');
    }
    
    public function login()
    {
        // Redirect if already authenticated
        auth_redirect_if_authenticated();
        
        $data['title'] = 'Login';
        $this->load->view('auth/login', $data);
    }
    
    public function process_login()
    {
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');
        
        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('auth/login');
        }
        
        $credentials = array(
            'email' => $this->input->post('email'),
            'password' => $this->input->post('password')
        );
        
        $remember = $this->input->post('remember') ? true : false;
        
        if (auth_attempt($credentials, $remember)) {
            $this->session->set_flashdata('success', 'Login successful!');
            redirect('dashboard');
        } else {
            $this->session->set_flashdata('error', 'Invalid credentials.');
            redirect('auth/login');
        }
    }
}
```

### API Controller with JWT

```php
class Api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('auth');
        $this->load->helper('auth');
        
        // Require JWT authentication
        require_auth('jwt');
    }
    
    public function users()
    {
        $this->output->set_content_type('application/json');
        
        try {
            $users = $this->User_model->getAll();
            
            $this->output->set_output(json_encode(array(
                'success' => true,
                'data' => $users
            )));
        } catch (Exception $e) {
            $this->output->set_status_header(500);
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => $e->getMessage()
            )));
        }
    }
}
```

### Middleware Usage

```php
class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('auth_middleware');
        
        // Require admin role
        $this->auth_middleware->requireRole('admin');
    }
    
    public function dashboard()
    {
        $data['title'] = 'Admin Dashboard';
        $this->load->view('admin/dashboard', $data);
    }
}
```

## Troubleshooting

### Common Issues

#### 1. "Authentication guard not found"
Make sure the guard is properly configured in `application/config/auth.php`.

#### 2. "User provider not found"
Ensure the user provider is configured and the corresponding class exists.

#### 3. "JWT token invalid"
Check that the JWT secret is properly set and the token is not expired.

#### 4. "Rate limit exceeded"
The user has exceeded the maximum number of login attempts. Wait for the decay period to expire.

#### 5. "Two-factor authentication required"
The user has 2FA enabled but hasn't completed the verification process.

### Debug Mode

Enable debug mode in your configuration:

```php
$config['auth_debug'] = TRUE;
```

This will log additional information to help troubleshoot issues.

### Logging

The authentication system logs various events. Check your logs for:

- Failed login attempts
- Successful logins
- Password resets
- Two-factor authentication events
- Token generation and validation

### Performance

For better performance:

1. Use database indexes on frequently queried columns
2. Implement caching for user data
3. Use connection pooling for database connections
4. Consider using Redis for session storage

## License

This authentication system is part of the CodeIgniter 3 framework and follows the same MIT license.

## Support

For support and questions:

1. Check the documentation
2. Review the code examples
3. Check the troubleshooting section
4. Create an issue on the project repository

## Contributing

Contributions are welcome! Please:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## Changelog

### Version 1.0.0
- Initial release
- Multi-guard support
- JWT authentication
- API token authentication
- Two-factor authentication
- Role-based access control
- Permission system
- Rate limiting
- Audit logging
- Device management
