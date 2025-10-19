/**
 * Dashboard JavaScript File
 * Handles dashboard-specific functionality
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
        initializeDashboard();
    });

    /**
     * Initialize dashboard functionality
     */
    function initializeDashboard() {
        initializeSidebar();
        initializeTheme();
        initializeNotifications();
        initializeCharts();
        initializeTables();
        initializeForms();
        initializeModals();
        initializeTooltips();
        initializeAnimations();
        initializeKeyboardShortcuts();
    }

    /**
     * Initialize sidebar functionality
     */
    function initializeSidebar() {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const mainContent = document.getElementById('mainContent');

        if (!sidebar) return;

        // Desktop sidebar toggle
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function () {
                sidebar.classList.toggle('collapsed');
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            });
        }

        // Mobile sidebar toggle
        if (mobileSidebarToggle) {
            mobileSidebarToggle.addEventListener('click', function () {
                sidebar.classList.add('show');
                sidebarOverlay.style.display = 'block';
            });
        }

        // Close mobile sidebar when clicking overlay
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function () {
                sidebar.classList.remove('show');
                this.style.display = 'none';
            });
        }

        // Close mobile sidebar when clicking on a link
        const sidebarLinks = sidebar.querySelectorAll('.nav-link');
        sidebarLinks.forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth < 992) {
                    sidebar.classList.remove('show');
                    sidebarOverlay.style.display = 'none';
                }
            });
        });

        // Restore sidebar state
        const sidebarCollapsed = localStorage.getItem('sidebarCollapsed');
        if (sidebarCollapsed === 'true') {
            sidebar.classList.add('collapsed');
        }

        // Handle window resize
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 992) {
                sidebar.classList.remove('show');
                if (sidebarOverlay) {
                    sidebarOverlay.style.display = 'none';
                }
            }
        });
    }

    /**
     * Initialize theme functionality
     */
    function initializeTheme() {
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
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
     * Initialize notifications
     */
    function initializeNotifications() {
        loadNotifications();
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
     * Load notifications
     */
    function loadNotifications() {
        fetch(`${siteUrl}ajax/dashboard/notifications`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateNotificationUI(data.notifications);
                }
            })
            .catch(error => {
                console.error('Failed to load notifications:', error);
            });
    }

    /**
     * Update notification UI
     */
    function updateNotificationUI(notifications) {
        const notificationCount = document.getElementById('notificationCount');
        const notificationMenu = document.querySelector('.notification-menu');
        const noNotifications = document.getElementById('noNotifications');

        if (notificationCount) {
            if (notifications.length > 0) {
                notificationCount.textContent = notifications.length;
                notificationCount.style.display = 'inline-block';
            } else {
                notificationCount.style.display = 'none';
            }
        }

        if (notificationMenu) {
            // Clear existing notifications
            const existingNotifications = notificationMenu.querySelectorAll('.notification-item');
            existingNotifications.forEach(function (item) {
                item.remove();
            });

            if (notifications.length > 0) {
                // Hide "no notifications" message
                if (noNotifications) {
                    noNotifications.style.display = 'none';
                }

                // Add new notifications
                notifications.forEach(function (notification) {
                    const notificationItem = createNotificationItem(notification);
                    notificationMenu.appendChild(notificationItem);
                });
            } else {
                // Show "no notifications" message
                if (noNotifications) {
                    noNotifications.style.display = 'block';
                }
            }
        }
    }

    /**
     * Create notification item
     */
    function createNotificationItem(notification) {
        const li = document.createElement('li');
        li.className = 'notification-item';
        li.dataset.notificationId = notification.id;

        const timeAgo = getTimeAgo(notification.created_at);
        const iconClass = getNotificationIcon(notification.type);

        li.innerHTML = `
            <a class="dropdown-item" href="${notification.url || '#'}">
                <div class="d-flex align-items-start">
                    <div class="notification-icon me-3">
                        <i class="${iconClass}"></i>
                    </div>
                    <div class="notification-content flex-grow-1">
                        <div class="notification-title">${notification.title}</div>
                        <div class="notification-message">${notification.message}</div>
                        <div class="notification-time text-muted small">${timeAgo}</div>
                    </div>
                    ${!notification.read ? '<div class="notification-dot"></div>' : ''}
                </div>
            </a>
        `;

        return li;
    }

    /**
     * Get notification icon based on type
     */
    function getNotificationIcon(type) {
        const icons = {
            'info': 'bi bi-info-circle-fill text-info',
            'success': 'bi bi-check-circle-fill text-success',
            'warning': 'bi bi-exclamation-triangle-fill text-warning',
            'error': 'bi bi-x-circle-fill text-danger',
            'default': 'bi bi-bell-fill text-primary'
        };
        return icons[type] || icons.default;
    }

    /**
     * Get time ago string
     */
    function getTimeAgo(dateString) {
        const now = new Date();
        const date = new Date(dateString);
        const diffInSeconds = Math.floor((now - date) / 1000);

        if (diffInSeconds < 60) {
            return 'Just now';
        } else if (diffInSeconds < 3600) {
            const minutes = Math.floor(diffInSeconds / 60);
            return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
        } else if (diffInSeconds < 86400) {
            const hours = Math.floor(diffInSeconds / 3600);
            return `${hours} hour${hours > 1 ? 's' : ''} ago`;
        } else {
            const days = Math.floor(diffInSeconds / 86400);
            return `${days} day${days > 1 ? 's' : ''} ago`;
        }
    }

    /**
     * Mark notification as read
     */
    function markNotificationAsRead(notificationId) {
        fetch(`${siteUrl}ajax/dashboard/notifications/mark-read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                [csrfName]: csrfToken,
                notification_id: notificationId
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the notification dot
                    const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
                    if (notificationItem) {
                        const dot = notificationItem.querySelector('.notification-dot');
                        if (dot) {
                            dot.remove();
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Failed to mark notification as read:', error);
            });
    }

    /**
     * Initialize charts
     */
    function initializeCharts() {
        // This would typically initialize Chart.js or similar
        const chartElements = document.querySelectorAll('.dashboard-chart');
        chartElements.forEach(function (chartElement) {
            // Initialize chart based on data attributes
            const chartType = chartElement.dataset.chartType;
            const chartData = chartElement.dataset.chartData;

            if (chartType && chartData) {
                // Initialize chart here
                console.log('Initializing chart:', chartType, chartData);
            }
        });
    }

    /**
     * Initialize tables
     */
    function initializeTables() {
        const tables = document.querySelectorAll('.dashboard-table');
        tables.forEach(function (table) {
            // Add sorting functionality
            const headers = table.querySelectorAll('th[data-sort]');
            headers.forEach(function (header) {
                header.style.cursor = 'pointer';
                header.addEventListener('click', function () {
                    sortTable(table, this.dataset.sort);
                });
            });
        });
    }

    /**
     * Sort table
     */
    function sortTable(table, column) {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const isAscending = table.dataset.sortDirection !== 'asc';

        rows.sort(function (a, b) {
            const aValue = a.querySelector(`td[data-sort="${column}"]`).textContent.trim();
            const bValue = b.querySelector(`td[data-sort="${column}"]`).textContent.trim();

            if (isAscending) {
                return aValue.localeCompare(bValue);
            } else {
                return bValue.localeCompare(aValue);
            }
        });

        rows.forEach(function (row) {
            tbody.appendChild(row);
        });

        table.dataset.sortDirection = isAscending ? 'asc' : 'desc';
    }

    /**
     * Initialize forms
     */
    function initializeForms() {
        const forms = document.querySelectorAll('.dashboard-form');
        forms.forEach(function (form) {
            form.addEventListener('submit', function (e) {
                handleFormSubmit(e, form);
            });
        });
    }

    /**
     * Handle form submission
     */
    function handleFormSubmit(e, form) {
        e.preventDefault();

        const formData = new FormData(form);
        const action = form.action || window.location.href;
        const method = form.method || 'POST';

        // Show loading state
        showFormLoading(form);

        // Add CSRF token
        if (csrfToken && csrfName) {
            formData.append(csrfName, csrfToken);
        }

        // Make AJAX request
        fetch(action, {
            method: method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                handleFormResponse(data, form);
            })
            .catch(error => {
                handleFormError(error, form);
            })
            .finally(function () {
                hideFormLoading(form);
            });
    }

    /**
     * Handle form response
     */
    function handleFormResponse(data, form) {
        if (data.success) {
            showFlashMessage(data.message || 'Operation completed successfully', 'success');

            if (data.redirect) {
                setTimeout(function () {
                    window.location.href = data.redirect;
                }, 1000);
            }

            if (data.resetForm) {
                form.reset();
            }
        } else {
            showFlashMessage(data.message || 'An error occurred. Please try again.', 'error');

            if (data.errors) {
                showFieldErrors(data.errors);
            }
        }
    }

    /**
     * Handle form error
     */
    function handleFormError(error, form) {
        console.error('Form submission error:', error);
        showFlashMessage('An error occurred. Please try again.', 'error');
    }

    /**
     * Show form loading state
     */
    function showFormLoading(form) {
        form.classList.add('loading');
        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Processing...';
        }
    }

    /**
     * Hide form loading state
     */
    function hideFormLoading(form) {
        form.classList.remove('loading');
        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = false;
            const originalText = submitButton.dataset.originalText || 'Submit';
            submitButton.innerHTML = originalText;
        }
    }

    /**
     * Show field errors
     */
    function showFieldErrors(errors) {
        Object.keys(errors).forEach(function (fieldName) {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.classList.add('is-invalid');
                let feedback = field.parentNode.querySelector('.invalid-feedback');
                if (!feedback) {
                    feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    field.parentNode.appendChild(feedback);
                }
                feedback.textContent = errors[fieldName];
            }
        });
    }

    /**
     * Initialize modals
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
     * Initialize tooltips
     */
    function initializeTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    /**
     * Initialize animations
     */
    function initializeAnimations() {
        // Add entrance animations to dashboard cards
        const cards = document.querySelectorAll('.dashboard-card');
        cards.forEach(function (card, index) {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('fade-in-up');
        });

        // Add hover effects to interactive elements
        const interactiveElements = document.querySelectorAll('.dashboard-card, .btn, .nav-link');
        interactiveElements.forEach(function (element) {
            element.addEventListener('mouseenter', function () {
                this.style.transform = 'translateY(-2px)';
            });

            element.addEventListener('mouseleave', function () {
                this.style.transform = 'translateY(0)';
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

            // Ctrl/Cmd + B to toggle sidebar
            if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
                e.preventDefault();
                const sidebar = document.getElementById('sidebar');
                if (sidebar) {
                    sidebar.classList.toggle('collapsed');
                    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
                }
            }
        });
    }

    /**
     * Show flash message
     */
    function showFlashMessage(message, type = 'info') {
        // Use the global showFlashMessage function if available
        if (window.showFlashMessage) {
            window.showFlashMessage(message, type);
            return;
        }

        // Fallback to creating alert element
        const alertClass = 'alert-' + type;
        const iconClass = getIconClass(type);

        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="${iconClass} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        const container = document.querySelector('.page-content') || document.body;
        const alertElement = document.createElement('div');
        alertElement.innerHTML = alertHtml;
        container.insertBefore(alertElement.firstElementChild, container.firstChild);

        // Auto-hide after 5 seconds
        setTimeout(function () {
            const alert = container.querySelector('.alert');
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    }

    /**
     * Get icon class for message type
     */
    function getIconClass(type) {
        switch (type) {
            case 'success':
                return 'bi bi-check-circle-fill';
            case 'error':
            case 'danger':
                return 'bi bi-exclamation-triangle-fill';
            case 'warning':
                return 'bi bi-exclamation-triangle-fill';
            case 'info':
                return 'bi bi-info-circle-fill';
            default:
                return 'bi bi-info-circle-fill';
        }
    }

    // Expose functions globally
    window.Dashboard = {
        showFlashMessage,
        loadNotifications,
        markNotificationAsRead,
        showFormLoading,
        hideFormLoading,
        showFieldErrors
    };

})();
