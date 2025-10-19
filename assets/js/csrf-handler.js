/**
 * CSRF Handler JavaScript
 * 
 * This file provides CSRF token management functionality for all authentication forms.
 */

// Global CSRF handler object
window.CSRFHandler = {
    /**
     * Refresh CSRF token
     */
    refreshToken: function () {
        return fetch(window.siteUrl + 'auth/refresh-csrf', {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update CSRF token in all forms
                    const csrfInputs = document.querySelectorAll('input[name="' + data.token_name + '"]');
                    csrfInputs.forEach(input => {
                        input.value = data.token;
                    });

                    // Hide CSRF error
                    const csrfErrors = document.querySelectorAll('#csrf-error');
                    csrfErrors.forEach(error => {
                        error.classList.add('d-none');
                    });

                    this.showMessage('CSRF token refreshed successfully!', 'success');
                    return true;
                } else {
                    throw new Error(data.message || 'Failed to refresh CSRF token');
                }
            })
            .catch(error => {
                console.error('CSRF refresh error:', error);
                this.showMessage('Failed to refresh CSRF token. Please reload the page.', 'error');
                return false;
            });
    },

    /**
     * Show message to user
     */
    showMessage: function (message, type = 'info') {
        // Remove existing messages
        const existingMessages = document.querySelectorAll('.csrf-message');
        existingMessages.forEach(msg => msg.remove());

        // Create new message
        const messageDiv = document.createElement('div');
        messageDiv.className = `alert alert-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'} csrf-message alert-dismissible fade show`;
        messageDiv.innerHTML = `
            <i class="bi bi-${type === 'error' ? 'exclamation-triangle' : type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

        // Insert after CSRF error div or at the top of the form
        const csrfError = document.querySelector('#csrf-error');
        if (csrfError) {
            csrfError.parentNode.insertBefore(messageDiv, csrfError.nextSibling);
        } else {
            const form = document.querySelector('form[id$="Form"]');
            if (form) {
                form.insertBefore(messageDiv, form.firstChild);
            }
        }

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.remove();
            }
        }, 5000);
    },

    /**
     * Check for CSRF errors in URL parameters
     */
    checkForErrors: function () {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('csrf_error') === '1') {
            const csrfErrors = document.querySelectorAll('#csrf-error');
            csrfErrors.forEach(error => {
                error.classList.remove('d-none');
            });
            this.showMessage('CSRF token validation failed. Please try again.', 'error');
        }
    },

    /**
     * Setup form submission with CSRF handling
     */
    setupFormSubmission: function (formId) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('submit', function (e) {
            // Check if form has validation errors
            const invalidFields = form.querySelectorAll('.is-invalid');
            if (invalidFields.length > 0) {
                e.preventDefault();
                return false;
            }

            // Show loading state if submit button exists
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                const originalText = submitButton.innerHTML;
                submitButton.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Processing...';
                submitButton.disabled = true;

                // Re-enable after 10 seconds as fallback
                setTimeout(() => {
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                }, 10000);
            }
        });
    },

    /**
     * Initialize CSRF handling for all forms
     */
    init: function () {
        // Check for CSRF errors on page load
        this.checkForErrors();

        // Setup form submissions
        this.setupFormSubmission('loginForm');
        this.setupFormSubmission('registerForm');
        this.setupFormSubmission('forgotPasswordForm');
        this.setupFormSubmission('resetPasswordForm');

        // Make refreshCSRFToken globally available
        window.refreshCSRFToken = () => this.refreshToken();
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    CSRFHandler.init();
});

// Also make it available immediately for inline scripts
window.refreshCSRFToken = function () {
    return CSRFHandler.refreshToken();
};
