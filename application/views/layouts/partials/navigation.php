<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand fw-bold" href="<?php echo site_url(); ?>">
            <i class="bi bi-shield-check-fill me-2"></i>
            <?php echo $this->config->item('site_name'); ?>
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Menu -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo site_url(); ?>">
                        <i class="bi bi-house-fill me-1"></i>Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo site_url('about'); ?>">
                        <i class="bi bi-info-circle-fill me-1"></i>About
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo site_url('contact'); ?>">
                        <i class="bi bi-envelope-fill me-1"></i>Contact
                    </a>
                </li>
            </ul>

            <!-- Right Side -->
            <ul class="navbar-nav">
                <?php if (auth_check()): ?>
                    <!-- User Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar me-2">
                                <i class="bi bi-person-circle"></i>
                            </div>
                            <span class="d-none d-md-inline"><?php echo auth_user()->name; ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <h6 class="dropdown-header">
                                    <i class="bi bi-person-fill me-2"></i>
                                    <?php echo auth_user()->name; ?>
                                </h6>
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
                    </li>

                    <!-- Notifications -->
                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell-fill"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notification-count" style="display: none;">
                                0
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown">
                            <li>
                                <h6 class="dropdown-header">
                                    <i class="bi bi-bell-fill me-2"></i>Notifications
                                </h6>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <div class="dropdown-item-text text-center text-muted" id="no-notifications">
                                    No new notifications
                                </div>
                            </li>
                        </ul>
                    </li>

                    <!-- Theme Toggle -->
                    <li class="nav-item">
                        <button class="btn btn-link nav-link" id="theme-toggle" title="Toggle theme">
                            <i class="bi bi-sun-fill" id="theme-icon"></i>
                        </button>
                    </li>

                <?php else: ?>
                    <!-- Guest Menu -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo site_url('auth/login'); ?>">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light ms-2" href="<?php echo site_url('auth/register'); ?>">
                            <i class="bi bi-person-plus-fill me-1"></i>Register
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu-overlay d-lg-none" id="mobileMenuOverlay" style="display: none;"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Theme toggle functionality
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const html = document.documentElement;

        // Check for saved theme preference or default to 'light'
        const currentTheme = localStorage.getItem('theme') || 'light';
        html.setAttribute('data-bs-theme', currentTheme);
        updateThemeIcon(currentTheme);

        themeToggle.addEventListener('click', function() {
            const currentTheme = html.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            html.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcon(newTheme);
        });

        function updateThemeIcon(theme) {
            if (theme === 'dark') {
                themeIcon.className = 'bi bi-moon-fill';
            } else {
                themeIcon.className = 'bi bi-sun-fill';
            }
        }

        // Mobile menu overlay
        const navbarToggler = document.querySelector('.navbar-toggler');
        const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
        const navbarCollapse = document.getElementById('navbarNav');

        navbarToggler.addEventListener('click', function() {
            if (navbarCollapse.classList.contains('show')) {
                mobileMenuOverlay.style.display = 'block';
            } else {
                mobileMenuOverlay.style.display = 'none';
            }
        });

        mobileMenuOverlay.addEventListener('click', function() {
            navbarToggler.click();
            mobileMenuOverlay.style.display = 'none';
        });

        // Close mobile menu when clicking on a link
        const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    navbarToggler.click();
                    mobileMenuOverlay.style.display = 'none';
                }
            });
        });

        // Load notifications
        loadNotifications();

        // Set up notification polling
        setInterval(loadNotifications, 30000); // Check every 30 seconds
    });

    function loadNotifications() {
        // This would typically make an AJAX call to load notifications
        // For now, we'll just simulate it
        const notificationCount = 0; // This would come from the server
        const notificationCountElement = document.getElementById('notification-count');

        if (notificationCount > 0) {
            notificationCountElement.textContent = notificationCount;
            notificationCountElement.style.display = 'inline-block';
        } else {
            notificationCountElement.style.display = 'none';
        }
    }
</script>