<?php
$data['title'] = 'Register';
$data['page_title'] = 'Create Account';
$data['page_subtitle'] = 'Join us today and get started';
$data['content'] = $this->load->view('auth/forms/register_form', '', true);
$this->load->view('layouts/auth', $data);
