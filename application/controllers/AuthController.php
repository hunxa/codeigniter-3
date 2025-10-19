<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Authentication Controller
 * 
 * This controller handles all authentication-related requests.
 * 
 * @package     CodeIgniter
 * @subpackage  Controllers
 * @category    Authentication
 * @author      Your Name
 */
class AuthController extends CI_Controller
{

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();

        try {
            $this->load->library('auth');
            $this->load->library('form_validation');
            $this->load->library('session');
            $this->load->helper('url');
            $this->load->helper('form');
        } catch (Exception $e) {
            show_error('Failed to load authentication system: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Show login form
     *
     * @return void
     */
    public function login()
    {
        // Redirect if already authenticated
        if ($this->auth->check()) {
            redirect('dashboard');
        }

        $data['title'] = 'Login';
        $data['error'] = $this->session->flashdata('error');
        $data['success'] = $this->session->flashdata('success');

        $this->load->view('auth/login', $data);
    }

    /**
     * Process login
     *
     * @return void
     */
    public function process_login()
    {
        // Set validation rules
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

        if ($this->auth->attempt($credentials, $remember)) {
            $this->session->set_flashdata('success', 'Login successful!');
            redirect('dashboard');
        } else {
            $this->session->set_flashdata('error', 'Invalid credentials.');
            redirect('auth/login');
        }
    }

    /**
     * Show registration form
     *
     * @return void
     */
    public function register()
    {
        // Redirect if already authenticated
        if ($this->auth->check()) {
            redirect('dashboard');
        }

        $data['title'] = 'Register';
        $data['error'] = $this->session->flashdata('error');
        $data['success'] = $this->session->flashdata('success');

        $this->load->view('auth/register', $data);
    }

    /**
     * Process registration
     *
     * @return void
     */
    public function process_register()
    {
        // Set validation rules
        $this->form_validation->set_rules('name', 'Name', 'required|min_length[2]|max_length[255]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
        $this->form_validation->set_rules('password_confirm', 'Password Confirmation', 'required|matches[password]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('auth/register');
        }

        $data = array(
            'name' => $this->input->post('name'),
            'email' => $this->input->post('email'),
            'password' => $this->input->post('password'),
            'role' => 'user'
        );

        $user = $this->auth->register($data, false); // Don't auto login for now

        if ($user) {
            $this->session->set_flashdata('success', 'Registration successful!');
            redirect('dashboard');
        } else {
            $this->session->set_flashdata('error', 'Registration failed. Please try again.');
            redirect('auth/register');
        }
    }

    /**
     * Logout user
     *
     * @return void
     */
    public function logout()
    {
        $this->auth->logout();
        $this->session->set_flashdata('success', 'You have been logged out.');
        redirect('auth/login');
    }

    /**
     * Show password reset form
     *
     * @return void
     */
    public function forgot_password()
    {
        $data['title'] = 'Forgot Password';
        $data['error'] = $this->session->flashdata('error');
        $data['success'] = $this->session->flashdata('success');

        $this->load->view('auth/forgot_password', $data);
    }

    /**
     * Process password reset request
     *
     * @return void
     */
    public function process_forgot_password()
    {
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('auth/forgot_password');
        }

        $email = $this->input->post('email');

        if ($this->auth->resetPassword(array('email' => $email))) {
            $this->session->set_flashdata('success', 'Password reset link sent to your email.');
        } else {
            $this->session->set_flashdata('error', 'Email not found or reset failed.');
        }

        redirect('auth/forgot_password');
    }

    /**
     * Show password reset form with token
     *
     * @param string $token
     * @return void
     */
    public function reset_password($token = null)
    {
        if (!$token) {
            show_404();
        }

        $data['title'] = 'Reset Password';
        $data['token'] = $token;
        $data['error'] = $this->session->flashdata('error');
        $data['success'] = $this->session->flashdata('success');

        $this->load->view('auth/reset_password', $data);
    }

    /**
     * Process password reset
     *
     * @return void
     */
    public function process_reset_password()
    {
        $this->form_validation->set_rules('token', 'Token', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
        $this->form_validation->set_rules('password_confirm', 'Password Confirmation', 'required|matches[password]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('auth/reset_password/' . $this->input->post('token'));
        }

        $token = $this->input->post('token');
        $password = $this->input->post('password');

        if ($this->auth->completePasswordReset($token, $password)) {
            $this->session->set_flashdata('success', 'Password reset successful. You can now login.');
            redirect('auth/login');
        } else {
            $this->session->set_flashdata('error', 'Invalid or expired token.');
            redirect('auth/reset_password/' . $token);
        }
    }

    /**
     * Verify email
     *
     * @param string $token
     * @return void
     */
    public function verify_email($token = null)
    {
        if (!$token) {
            show_404();
        }

        if ($this->auth->verifyEmail($token)) {
            $this->session->set_flashdata('success', 'Email verified successfully!');
        } else {
            $this->session->set_flashdata('error', 'Invalid or expired verification token.');
        }

        redirect('auth/login');
    }

    /**
     * Show two-factor authentication setup
     *
     * @return void
     */
    public function two_factor_setup()
    {
        if (!$this->auth->check()) {
            redirect('auth/login');
        }

        $user = $this->auth->user();

        if ($this->auth->hasTwoFactorEnabled($user)) {
            redirect('dashboard');
        }

        $data['title'] = 'Two-Factor Authentication Setup';
        $data['user'] = $user;

        $this->load->view('auth/two_factor_setup', $data);
    }

    /**
     * Enable two-factor authentication
     *
     * @return void
     */
    public function enable_two_factor()
    {
        if (!$this->auth->check()) {
            redirect('auth/login');
        }

        $user = $this->auth->user();
        $method = $this->input->post('method') ?: 'totp';

        try {
            $result = $this->auth->enableTwoFactor($user, $method);

            $this->session->set_flashdata('two_factor_data', $result);
            $this->session->set_flashdata('success', 'Two-factor authentication enabled!');

            redirect('auth/two_factor_verify');
        } catch (Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());
            redirect('auth/two_factor_setup');
        }
    }

    /**
     * Show two-factor verification form
     *
     * @return void
     */
    public function two_factor_verify()
    {
        if (!$this->auth->check()) {
            redirect('auth/login');
        }

        $data['title'] = 'Verify Two-Factor Authentication';
        $data['two_factor_data'] = $this->session->flashdata('two_factor_data');

        $this->load->view('auth/two_factor_verify', $data);
    }

    /**
     * Process two-factor verification
     *
     * @return void
     */
    public function process_two_factor_verify()
    {
        if (!$this->auth->check()) {
            redirect('auth/login');
        }

        $this->form_validation->set_rules('code', 'Verification Code', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('auth/two_factor_verify');
        }

        $user = $this->auth->user();
        $code = $this->input->post('code');

        if ($this->auth->verifyTwoFactor($user, $code)) {
            $this->session->set_flashdata('success', 'Two-factor authentication verified!');
            redirect('dashboard');
        } else {
            $this->session->set_flashdata('error', 'Invalid verification code.');
            redirect('auth/two_factor_verify');
        }
    }

    /**
     * API login endpoint
     *
     * @return void
     */
    public function api_login()
    {
        $this->output->set_content_type('application/json');

        $credentials = array(
            'email' => $this->input->post('email'),
            'password' => $this->input->post('password')
        );

        if ($this->auth->guard('jwt')->attempt($credentials)) {
            $token_data = $this->auth->guard('jwt')->login($this->auth->user(), true);

            $this->output->set_output(json_encode(array(
                'success' => true,
                'message' => 'Login successful',
                'data' => $token_data
            )));
        } else {
            $this->output->set_status_header(401);
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Invalid credentials'
            )));
        }
    }

    /**
     * API logout endpoint
     *
     * @return void
     */
    public function api_logout()
    {
        $this->output->set_content_type('application/json');

        $this->auth->guard('jwt')->logout();

        $this->output->set_output(json_encode(array(
            'success' => true,
            'message' => 'Logout successful'
        )));
    }

    /**
     * API refresh token endpoint
     *
     * @return void
     */
    public function api_refresh()
    {
        $this->output->set_content_type('application/json');

        $refresh_token = $this->input->post('refresh_token');

        if (!$refresh_token) {
            $this->output->set_status_header(400);
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Refresh token required'
            )));
            return;
        }

        $result = $this->auth->guard('jwt')->refreshToken($refresh_token);

        if ($result) {
            $this->output->set_output(json_encode(array(
                'success' => true,
                'message' => 'Token refreshed successfully',
                'data' => $result
            )));
        } else {
            $this->output->set_status_header(401);
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Invalid refresh token'
            )));
        }
    }

    /**
     * Refresh CSRF token endpoint
     *
     * @return void
     */
    public function refresh_csrf()
    {
        $this->output->set_content_type('application/json');

        // Generate new CSRF token
        $csrf_token_name = $this->security->get_csrf_token_name();
        $csrf_token_value = $this->security->get_csrf_hash();

        $this->output->set_output(json_encode(array(
            'success' => true,
            'message' => 'CSRF token refreshed successfully',
            'token_name' => $csrf_token_name,
            'token' => $csrf_token_value
        )));
    }
}
