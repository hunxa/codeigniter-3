<?php
$data['title'] = 'Forgot Password';
$data['page_title'] = 'Reset Password';
$data['page_subtitle'] = 'Enter your email address and we\'ll send you a link to reset your password';
$data['content'] = $this->load->view('auth/forms/forgot_password_form', '', true);
$this->load->view('layouts/auth', $data);
