<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $this->security->get_csrf_hash(); ?>">
    <title><?php echo isset($title) ? $title . ' - Dashboard' : 'Dashboard - ' . $this->config->item('site_name'); ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php echo base_url('assets/css/main.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/dashboard.css'); ?>" rel="stylesheet">

    <!-- Additional CSS -->
    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css): ?>
            <link href="<?php echo $css; ?>" rel="stylesheet">
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo base_url('assets/images/favicon.ico'); ?>">
</head>

<body class="dashboard-body">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="<?php echo site_url('dashboard'); ?>" class="sidebar-brand">
                <i class="bi bi-shield-check-fill me-2"></i>
                <span class="brand-text"><?php echo $this->config->item('site_name'); ?></span>
            </a>
            <button class="sidebar-toggle d-lg-none" id="sidebarToggle">
                <i class="bi bi-x"></i>
            </button>
        </div>

        <div class="sidebar-content">
            <nav class="sidebar-nav">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?php echo (uri_string() == 'dashboard') ? 'active' : ''; ?>" href="<?php echo site_url('dashboard'); ?>">
                            <i class="bi bi-house-fill me-2"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos(uri_string(), 'profile') !== false) ? 'active' : ''; ?>" href="<?php echo site_url('profile'); ?>">
                            <i class="bi bi-person-fill me-2"></i>
                            <span>Profile</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos(uri_string(), 'settings') !== false) ? 'active' : ''; ?>" href="<?php echo site_url('settings'); ?>">
                            <i class="bi bi-gear-fill me-2"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                    <?php if (has_role('admin')): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos(uri_string(), 'admin') !== false) ? 'active' : ''; ?>" href="<?php echo site_url('admin'); ?>">
                                <i class="bi bi-shield-fill me-2"></i>
                                <span>Admin Panel</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>

        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div class="user-details">
                    <div class="user-name"><?php echo auth_user()->name; ?></div>
                    <div class="user-email"><?php echo auth_user()->email; ?></div>
                </div>
            </div>
            <a href="<?php echo site_url('auth/logout'); ?>" class="logout-btn" onclick="return confirm('Are you sure you want to logout?')">
                <i class="bi bi-box-arrow-right me-2"></i>
                Logout
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Navigation -->
        <nav class="top-navbar">
            <div class="navbar-left">
                <button class="sidebar-toggle d-lg-none" id="mobileSidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <h1 class="page-title"><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h1>
            </div>
            <div class="navbar-right">
                <!-- Notifications -->
                <div class="notification-dropdown">
                    <button class="notification-btn" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell-fill"></i>
                        <span class="notification-badge" id="notificationCount" style="display: none;">0</span>
                    </button>
                    <ul class="dropdown-menu notification-menu">
                        <li class="dropdown-header">
                            <i class="bi bi-bell-fill me-2"></i>Notifications
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <div class="dropdown-item-text text-center text-muted" id="noNotifications">
                                No new notifications
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Theme Toggle -->
                <button class="theme-toggle" id="themeToggle" title="Toggle theme">
                    <i class="bi bi-sun-fill" id="themeIcon"></i>
                </button>

                <!-- User Menu -->
                <div class="user-dropdown">
                    <button class="user-btn" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar-small">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <span class="user-name-small d-none d-md-inline"><?php echo auth_user()->name; ?></span>
                        <i class="bi bi-chevron-down ms-2"></i>
                    </button>
                    <ul class="dropdown-menu user-menu">
                        <li class="dropdown-header">
                            <i class="bi bi-person-fill me-2"></i>
                            <?php echo auth_user()->name; ?>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php echo site_url('profile'); ?>">
                                <i class="bi bi-person-fill me-2"></i>Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php echo site_url('settings'); ?>">
                                <i class="bi bi-gear-fill me-2"></i>Settings
                            </a>
                        </li>
                        <?php if (has_role('admin')): ?>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo site_url('admin'); ?>">
                                    <i class="bi bi-shield-fill me-2"></i>Admin Panel
                                </a>
                            </li>
                        <?php endif; ?>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item text-danger" href="<?php echo site_url('auth/logout'); ?>" onclick="return confirm('Are you sure you want to logout?')">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="page-content">
            <!-- Flash Messages -->
            <?php $this->load->view('layouts/partials/flash_messages'); ?>

            <!-- Content -->
            <div class="container-fluid">
                <?php echo $content; ?>
            </div>
        </main>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay d-lg-none" id="sidebarOverlay" style="display: none;"></div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.5); z-index: 9999; display: none !important;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Custom JavaScript -->
    <script src="<?php echo base_url('assets/js/main.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/dashboard.js'); ?>"></script>

    <!-- CSRF Token for AJAX -->
    <script>
        window.csrfToken = '<?php echo $this->security->get_csrf_hash(); ?>';
        window.csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
        window.baseUrl = '<?php echo base_url(); ?>';
        window.siteUrl = '<?php echo site_url(); ?>';
    </script>

    <!-- Additional JavaScript -->
    <?php if (isset($additional_js)): ?>
        <?php foreach ($additional_js as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Inline JavaScript -->
    <?php if (isset($inline_js)): ?>
        <script>
            <?php echo $inline_js; ?>
        </script>
    <?php endif; ?>
</body>

</html>