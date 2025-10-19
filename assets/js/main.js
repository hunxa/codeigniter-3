/**
 * Main JavaScript File
 * Handles common functionality across the application
 */

(function () {
    'use strict';

    // Global variables
    let csrfToken = window.csrfToken || '';
    let csrfName = window.csrfName || '';
    let baseUrl = window.baseUrl || '';
    let siteUrl = window.siteUrl || '';

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function () {
        initializeApp();
    });

    /**
     * Initialize the application
     */
    function initializeApp() {
        initializeTheme();
        initializeBackToTop();
        initializeTooltips();
        initializePopovers();
        initializeAlerts();
        initializeForms();
        initializeModals();
        initializeDropdowns();
        initializeNavbar();
        initializeScrollEffects();
        initializeLazyLoading();
        initializeNotifications();
        initializeKeyboardShortcuts();
    }

    /**
     * Initialize theme functionality
     */
    function initializeTheme() {
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const html = document.documentElement;

        if (!themeToggle || !themeIcon) return;

        // Check for saved theme preference or default to 'light'
        const currentTheme = localStorage.getItem('theme') || 'light';
        html.setAttribute('data-bs-theme', currentTheme);
        updateThemeIcon(currentTheme);

        themeToggle.addEventListener('click', function () {
            const currentTheme = html.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            html.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcon(newTheme);

            // Trigger custom event
            document.dispatchEvent(new CustomEvent('themeChanged', {
                detail: { theme: newTheme }
            }));
        });

        function updateThemeIcon(theme) {
            if (theme === 'dark') {
                themeIcon.className = 'bi bi-moon-fill';
            } else {
                themeIcon.className = 'bi bi-sun-fill';
            }
        }
    }

    /**
     * Initialize back to top button
     */
    function initializeBackToTop() {
        const backToTopButton = document.getElementById('back-to-top');
        if (!backToTopButton) return;

        // Show/hide button based on scroll position
        window.addEventListener('scroll', function () {
            if (window.pageYOffset > 300) {
                backToTopButton.style.display = 'block';
            } else {
                backToTopButton.style.display = 'none';
            }
        });

        // Smooth scroll to top
        backToTopButton.addEventListener('click', function (e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    /**
     * Initialize Bootstrap tooltips
     */
    function initializeTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    /**
     * Initialize Bootstrap popovers
     */
    function initializePopovers() {
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    }

    /**
     * Initialize alert functionality
     */
    function initializeAlerts() {
        // Auto-hide alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(function (alert) {
            setTimeout(function () {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });

        // Add click to dismiss functionality
        alerts.forEach(function (alert) {
            alert.addEventListener('click', function () {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        });
    }

    /**
     * Initialize form functionality
     */
    function initializeForms() {
        // Form validation
        const forms = document.querySelectorAll('.needs-validation');
        forms.forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });

        // Real-time validation
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(function (input) {
            input.addEventListener('blur', function () {
                validateField(input);
            });
        });

        // Password strength indicator
        const passwordInputs = document.querySelectorAll('input[type="password"]');
        passwordInputs.forEach(function (input) {
            if (input.dataset.strength) {
                input.addEventListener('input', function () {
                    updatePasswordStrength(input);
                });
            }
        });
    }

    /**
     * Initialize modal functionality
     */
    function initializeModals() {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(function (modal) {
            modal.addEventListener('shown.bs.modal', function () {
                const firstInput = modal.querySelector('input, textarea, select');
                if (firstInput) {
                    firstInput.focus();
                }
            });
        });
    }

    /**
     * Initialize dropdown functionality
     */
    function initializeDropdowns() {
        const dropdowns = document.querySelectorAll('.dropdown-toggle');
        dropdowns.forEach(function (dropdown) {
            dropdown.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function () {
            const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
            openDropdowns.forEach(function (dropdown) {
                const bsDropdown = bootstrap.Dropdown.getInstance(dropdown.previousElementSibling);
                if (bsDropdown) {
                    bsDropdown.hide();
                }
            });
        });
    }

    /**
     * Initialize navbar functionality
     */
    function initializeNavbar() {
        const navbar = document.querySelector('.navbar');
        if (!navbar) return;

        // Add shadow on scroll
        window.addEventListener('scroll', function () {
            if (window.pageYOffset > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });

        // Mobile menu overlay
        const navbarToggler = document.querySelector('.navbar-toggler');
        const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
        const navbarCollapse = document.querySelector('.navbar-collapse');

        if (navbarToggler && mobileMenuOverlay && navbarCollapse) {
            navbarToggler.addEventListener('click', function () {
                if (navbarCollapse.classList.contains('show')) {
                    mobileMenuOverlay.style.display = 'block';
                } else {
                    mobileMenuOverlay.style.display = 'none';
                }
            });

            mobileMenuOverlay.addEventListener('click', function () {
                navbarToggler.click();
                mobileMenuOverlay.style.display = 'none';
            });

            // Close mobile menu when clicking on a link
            const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
            navLinks.forEach(function (link) {
                link.addEventListener('click', function () {
                    if (window.innerWidth < 992) {
                        navbarToggler.click();
                        mobileMenuOverlay.style.display = 'none';
                    }
                });
            });
        }
    }

    /**
     * Initialize scroll effects
     */
    function initializeScrollEffects() {
        // Fade in elements on scroll
        const fadeElements = document.querySelectorAll('.fade-on-scroll');
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in-up');
                }
            });
        }, { threshold: 0.1 });

        fadeElements.forEach(function (element) {
            observer.observe(element);
        });
    }

    /**
     * Initialize lazy loading
     */
    function initializeLazyLoading() {
        const lazyImages = document.querySelectorAll('img[data-src]');
        const imageObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        lazyImages.forEach(function (img) {
            imageObserver.observe(img);
        });
    }

    /**
     * Initialize notifications
     */
    function initializeNotifications() {
        // Load notifications on page load
        loadNotifications();

        // Set up notification polling
        setInterval(loadNotifications, 30000); // Check every 30 seconds

        // Mark notifications as read
        const notificationLinks = document.querySelectorAll('.notification-item');
        notificationLinks.forEach(function (link) {
            link.addEventListener('click', function () {
                const notificationId = this.dataset.notificationId;
                if (notificationId) {
                    markNotificationAsRead(notificationId);
                }
            });
        });
    }

    /**
     * Initialize keyboard shortcuts
     */
    function initializeKeyboardShortcuts() {
        document.addEventListener('keydown', function (e) {
            // Ctrl/Cmd + K for search
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.querySelector('#search-input');
                if (searchInput) {
                    searchInput.focus();
                }
            }

            // Escape to close modals
            if (e.key === 'Escape') {
                const openModals = document.querySelectorAll('.modal.show');
                openModals.forEach(function (modal) {
                    const bsModal = bootstrap.Modal.getInstance(modal);
                    if (bsModal) {
                        bsModal.hide();
                    }
                });
            }
        });
    }

    /**
     * Validate a form field
     */
    function validateField(field) {
        const value = field.value.trim();
        const type = field.type;
        const required = field.hasAttribute('required');
        let isValid = true;
        let message = '';

        // Required field validation
        if (required && !value) {
            isValid = false;
            message = 'This field is required.';
        }

        // Email validation
        if (type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                message = 'Please enter a valid email address.';
            }
        }

        // Password validation
        if (type === 'password' && value) {
            if (value.length < 8) {
                isValid = false;
                message = 'Password must be at least 8 characters long.';
            }
        }

        // Update field appearance
        field.classList.remove('is-valid', 'is-invalid');
        if (value) {
            field.classList.add(isValid ? 'is-valid' : 'is-invalid');
        }

        // Update feedback message
        let feedback = field.parentNode.querySelector('.invalid-feedback, .valid-feedback');
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = isValid ? 'valid-feedback' : 'invalid-feedback';
            field.parentNode.appendChild(feedback);
        }
        feedback.textContent = message;
        feedback.className = isValid ? 'valid-feedback' : 'invalid-feedback';

        return isValid;
    }

    /**
     * Update password strength indicator
     */
    function updatePasswordStrength(input) {
        const strengthIndicator = document.querySelector(input.dataset.strength);
        if (!strengthIndicator) return;

        const password = input.value;
        let strength = 0;
        let strengthText = '';

        // Length check
        if (password.length >= 8) strength++;
        if (password.length >= 12) strength++;

        // Character variety checks
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;

        // Update strength indicator
        strengthIndicator.className = 'password-strength';
        strengthIndicator.innerHTML = '';

        for (let i = 0; i < 5; i++) {
            const bar = document.createElement('div');
            bar.className = 'strength-bar';
            if (i < strength) {
                bar.classList.add('active');
            }
            strengthIndicator.appendChild(bar);
        }

        // Update strength text
        const strengthTextElement = strengthIndicator.querySelector('.strength-text');
        if (strengthTextElement) {
            strengthTextElement.remove();
        }

        if (strength < 2) {
            strengthText = 'Weak';
            strengthIndicator.classList.add('weak');
        } else if (strength < 4) {
            strengthText = 'Medium';
            strengthIndicator.classList.add('medium');
        } else {
            strengthText = 'Strong';
            strengthIndicator.classList.add('strong');
        }

        const textElement = document.createElement('span');
        textElement.className = 'strength-text';
        textElement.textContent = strengthText;
        strengthIndicator.appendChild(textElement);
    }

    /**
     * Load notifications
     */
    function loadNotifications() {
        // This would typically make an AJAX call to load notifications
        // For now, we'll just simulate it
        const notificationCount = 0; // This would come from the server
        const notificationCountElement = document.getElementById('notification-count');

        if (notificationCountElement) {
            if (notificationCount > 0) {
                notificationCountElement.textContent = notificationCount;
                notificationCountElement.style.display = 'inline-block';
            } else {
                notificationCountElement.style.display = 'none';
            }
        }
    }

    /**
     * Mark notification as read
     */
    function markNotificationAsRead(notificationId) {
        // This would typically make an AJAX call to mark notification as read
        console.log('Marking notification as read:', notificationId);
    }

    /**
     * Show loading overlay
     */
    function showLoading() {
        const overlay = document.getElementById('loading-overlay');
        if (overlay) {
            overlay.style.display = 'flex';
        }
    }

    /**
     * Hide loading overlay
     */
    function hideLoading() {
        const overlay = document.getElementById('loading-overlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
    }

    /**
     * Make AJAX request with CSRF protection
     */
    function makeAjaxRequest(url, options = {}) {
        const defaultOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        // Add CSRF token if available
        if (csrfToken && csrfName) {
            if (defaultOptions.method === 'POST' || defaultOptions.method === 'PUT' || defaultOptions.method === 'DELETE') {
                if (defaultOptions.body && typeof defaultOptions.body === 'string') {
                    const body = JSON.parse(defaultOptions.body);
                    body[csrfName] = csrfToken;
                    defaultOptions.body = JSON.stringify(body);
                } else {
                    defaultOptions.body = JSON.stringify({ [csrfName]: csrfToken });
                }
            }
        }

        const finalOptions = { ...defaultOptions, ...options };

        return fetch(url, finalOptions)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .catch(error => {
                console.error('AJAX request failed:', error);
                throw error;
            });
    }

    // Expose functions globally
    window.App = {
        showLoading,
        hideLoading,
        makeAjaxRequest,
        validateField,
        updatePasswordStrength,
        loadNotifications,
        markNotificationAsRead
    };

})();
