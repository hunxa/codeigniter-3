<?php
$data['title'] = 'Dashboard';
$data['page_title'] = 'Welcome back, ' . auth_user()->name . '!';
$data['content'] = $this->load->view('dashboard/partials/dashboard_content', '', true);
$this->load->view('layouts/dashboard', $data);
