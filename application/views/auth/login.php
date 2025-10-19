<?php
$data['title'] = 'Login';
$data['page_title'] = 'Welcome Back';
$data['page_subtitle'] = 'Please sign in to your account';
$data['content'] = $this->load->view('auth/forms/login_form', '', true);
$this->load->view('layouts/auth', $data);
