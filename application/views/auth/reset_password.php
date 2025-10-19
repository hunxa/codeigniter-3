<?php
$data['title'] = 'Reset Password';
$data['page_title'] = 'Set New Password';
$data['page_subtitle'] = 'Enter your new password below';
$data['content'] = $this->load->view('auth/forms/reset_password_form', '', true);
$this->load->view('layouts/auth', $data);
