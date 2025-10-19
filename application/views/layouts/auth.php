<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $this->security->get_csrf_hash(); ?>">
    <title><?php echo isset($title) ? $title . ' - ' . $this->config->item('site_name') : $this->config->item('site_name'); ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php echo base_url('assets/css/auth.css'); ?>" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo base_url('assets/images/favicon.ico'); ?>">

    <!-- Theme Color -->
    <meta name="theme-color" content="#0d6efd">
</head>

<body class="auth-body">
    <!-- Background -->
    <div class="auth-background">
        <div class="auth-background-overlay"></div>
        <div class="auth-background-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
            <div class="shape shape-4"></div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="container-fluid h-100">
        <div class="row h-100">
            <!-- Left Side - Branding -->
            <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center">
                <div class="auth-branding text-center text-white">
                    <div class="auth-logo mb-4">
                        <i class="bi bi-shield-check-fill" style="font-size: 4rem;"></i>
                    </div>
                    <h1 class="display-4 fw-bold mb-3"><?php echo $this->config->item('site_name'); ?></h1>
                    <p class="lead mb-4"><?php echo $this->config->item('site_description'); ?></p>
                    <div class="auth-features">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="feature-item">
                                    <i class="bi bi-shield-lock-fill mb-2"></i>
                                    <h6>Secure</h6>
                                    <small>Enterprise-grade security</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="feature-item">
                                    <i class="bi bi-lightning-fill mb-2"></i>
                                    <h6>Fast</h6>
                                    <small>Lightning-fast performance</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="feature-item">
                                    <i class="bi bi-people-fill mb-2"></i>
                                    <h6>Reliable</h6>
                                    <small>Trusted by millions</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="feature-item">
                                    <i class="bi bi-gear-fill mb-2"></i>
                                    <h6>Flexible</h6>
                                    <small>Highly customizable</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Auth Form -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center">
                <div class="auth-container">
                    <!-- Auth Card -->
                    <div class="auth-card">
                        <!-- Header -->
                        <div class="auth-header text-center mb-4">
                            <div class="auth-logo-mobile d-lg-none mb-3">
                                <i class="bi bi-shield-check-fill text-primary" style="font-size: 3rem;"></i>
                            </div>
                            <h2 class="auth-title"><?php echo isset($page_title) ? $page_title : 'Welcome'; ?></h2>
                            <p class="auth-subtitle text-muted"><?php echo isset($page_subtitle) ? $page_subtitle : 'Please sign in to your account'; ?></p>
                        </div>

                        <!-- Flash Messages -->
                        <?php $this->load->view('layouts/partials/flash_messages'); ?>

                        <!-- Content -->
                        <div class="auth-content">
                            <?php echo $content; ?>
                        </div>

                        <!-- Footer -->
                        <div class="auth-footer text-center mt-4">
                            <p class="text-muted small">
                                <?php if (isset($footer_text)): ?>
                                    <?php echo $footer_text; ?>
                                <?php else: ?>
                                    &copy; <?php echo date('Y'); ?> <?php echo $this->config->item('site_name'); ?>. All rights reserved.
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
    <script src="<?php echo base_url('assets/js/auth.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/csrf-handler.js'); ?>"></script>

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