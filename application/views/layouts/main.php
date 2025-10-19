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
    <link href="<?php echo base_url('assets/css/main.css'); ?>" rel="stylesheet">

    <!-- Additional CSS -->
    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css): ?>
            <link href="<?php echo $css; ?>" rel="stylesheet">
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Meta Tags -->
    <meta name="description" content="<?php echo isset($description) ? $description : $this->config->item('site_description'); ?>">
    <meta name="keywords" content="<?php echo isset($keywords) ? $keywords : $this->config->item('site_keywords'); ?>">
    <meta name="author" content="<?php echo $this->config->item('site_author'); ?>">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo isset($title) ? $title : $this->config->item('site_name'); ?>">
    <meta property="og:description" content="<?php echo isset($description) ? $description : $this->config->item('site_description'); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo current_url(); ?>">
    <meta property="og:image" content="<?php echo base_url('assets/images/og-image.jpg'); ?>">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo isset($title) ? $title : $this->config->item('site_name'); ?>">
    <meta name="twitter:description" content="<?php echo isset($description) ? $description : $this->config->item('site_description'); ?>">
    <meta name="twitter:image" content="<?php echo base_url('assets/images/og-image.jpg'); ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo base_url('assets/images/favicon.ico'); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo base_url('assets/images/apple-touch-icon.png'); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url('assets/images/favicon-32x32.png'); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url('assets/images/favicon-16x16.png'); ?>">

    <!-- Theme Color -->
    <meta name="theme-color" content="#0d6efd">
    <meta name="msapplication-TileColor" content="#0d6efd">
</head>

<body class="d-flex flex-column min-vh-100">
    <!-- Skip to main content -->
    <a href="#main-content" class="visually-hidden-focusable btn btn-primary position-absolute top-0 start-0 m-3">Skip to main content</a>

    <!-- Navigation -->
    <?php $this->load->view('layouts/partials/navigation'); ?>

    <!-- Main Content -->
    <main id="main-content" class="flex-grow-1">
        <!-- Flash Messages -->
        <?php $this->load->view('layouts/partials/flash_messages'); ?>

        <!-- Page Header -->
        <?php if (isset($show_page_header) && $show_page_header): ?>
            <?php $this->load->view('layouts/partials/page_header'); ?>
        <?php endif; ?>

        <!-- Content -->
        <div class="container-fluid">
            <?php echo $content; ?>
        </div>
    </main>

    <!-- Footer -->
    <?php $this->load->view('layouts/partials/footer'); ?>

    <!-- Back to Top Button -->
    <button type="button" class="btn btn-primary btn-floating btn-lg position-fixed bottom-0 end-0 m-4" id="back-to-top" style="display: none;">
        <i class="bi bi-arrow-up"></i>
    </button>

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

    <!-- CSRF Token for AJAX -->
    <script>
        window.csrfToken = '<?php echo $this->security->get_csrf_hash(); ?>';
        window.csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
        window.baseUrl = '<?php echo base_url(); ?>';
        window.siteUrl = '<?php echo site_url(); ?>';
    </script>
</body>

</html>