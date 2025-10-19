/**
 * Authentication JavaScript File
 * Handles authentication-specific functionality
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
        initializeAuth();
    });

    /**
     * Initialize authentication functionality
     */
    function initializeAuth() {
        initializeAuthForms();
        initializePasswordToggle();
        initializePasswordStrength();
        initializeSocialLogin();
        initializeTwoFactorAuth();
        initializeRememberMe();
        initializeFormValidation();
        initializeAuthModals();
        initializeAuthAnimations();
    }

    /**
     * Initialize authentication forms
     */
    function initializeAuthForms() {
        const authForms = document.querySelectorAll('.auth-form');
        authForms.forEach(function (form) {
            form.addEventListener('submit', function (e) {
                handleAuthFormSubmit(e, form);
            });
        });

        // Real-time validation
        const inputs = document.querySelectorAll('.auth-form .form-control');
        inputs.forEach(function (input) {
            input.addEventListener('blur', function () {
                validateAuthField(input);
            });

            input.addEventListener('input', function () {
                clearFieldError(input);
            });
        });
    }

    /**
     * Handle authentication form submission
     */
    function handleAuthFormSubmit(e, form) {
        e.preventDefault();

        const formData = new FormData(form);
        const action = form.action || window.location.href;
        const method = form.method || 'POST';

        // Show loading state
        showFormLoading(form);

        // Disable form elements
        const formElements = form.querySelectorAll('input, button, select, textarea');
        formElements.forEach(function (element) {
            element.disabled = true;
        });

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
                handleAuthResponse(data, form);
            })
            .catch(error => {
                handleAuthError(error, form);
            })
            .finally(function () {
                hideFormLoading(form);

                // Re-enable form elements
                formElements.forEach(function (element) {
                    element.disabled = false;
                });
            });
    }

    /**
     * Handle authentication response
     */
    function handleAuthResponse(data, form) {
        if (data.success) {
            // Show success message
            showAuthMessage(data.message || 'Operation completed successfully', 'success');

            // Redirect if specified
            if (data.redirect) {
                setTimeout(function () {
                    window.location.href = data.redirect;
                }, 1000);
            }

            // Reset form if specified
            if (data.resetForm) {
                form.reset();
            }
        } else {
            // Show error message
            showAuthMessage(data.message || 'An error occurred. Please try again.', 'error');

            // Show field errors
            if (data.errors) {
                showFieldErrors(data.errors);
            }
        }
    }

    /**
     * Handle authentication error
     */
    function handleAuthError(error, form) {
        console.error('Authentication error:', error);
        showAuthMessage('An error occurred. Please try again.', 'error');
    }

    /**
     * Initialize password toggle functionality
     */
    function initializePasswordToggle() {
        const passwordToggles = document.querySelectorAll('.password-toggle');
        passwordToggles.forEach(function (toggle) {
            toggle.addEventListener('click', function () {
                const input = this.parentNode.querySelector('input');
                if (input) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        this.innerHTML = '<i class="bi bi-eye-slash"></i>';
                    } else {
                        input.type = 'password';
                        this.innerHTML = '<i class="bi bi-eye"></i>';
                    }
                }
            });
        });
    }

    /**
     * Initialize password strength indicator
     */
    function initializePasswordStrength() {
        const passwordInputs = document.querySelectorAll('input[type="password"][data-strength]');
        passwordInputs.forEach(function (input) {
            input.addEventListener('input', function () {
                updatePasswordStrength(input);
            });
        });
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
     * Initialize social login functionality
     */
    function initializeSocialLogin() {
        const socialButtons = document.querySelectorAll('.social-btn');
        socialButtons.forEach(function (button) {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const provider = this.dataset.provider;
                if (provider) {
                    initiateSocialLogin(provider);
                }
            });
        });
    }

    /**
     * Initiate social login
     */
    function initiateSocialLogin(provider) {
        const url = `${siteUrl}auth/social/${provider}`;
        const popup = window.open(url, 'socialLogin', 'width=600,height=600,scrollbars=yes,resizable=yes');

        // Listen for popup close
        const checkClosed = setInterval(function () {
            if (popup.closed) {
                clearInterval(checkClosed);
                // Check if login was successful
                checkSocialLoginStatus();
            }
        }, 1000);
    }

    /**
     * Check social login status
     */
    function checkSocialLoginStatus() {
        fetch(`${siteUrl}auth/social/status`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAuthMessage('Login successful!', 'success');
                    setTimeout(function () {
                        window.location.href = data.redirect || '/dashboard';
                    }, 1000);
                }
            })
            .catch(error => {
                console.error('Social login check failed:', error);
            });
    }

    /**
     * Initialize two-factor authentication
     */
    function initializeTwoFactorAuth() {
        const twoFactorForm = document.querySelector('#two-factor-form');
        if (!twoFactorForm) return;

        const codeInput = twoFactorForm.querySelector('input[name="code"]');
        if (codeInput) {
            codeInput.addEventListener('input', function () {
                if (this.value.length === 6) {
                    // Auto-submit when 6 digits are entered
                    twoFactorForm.submit();
                }
            });
        }
    }

    /**
     * Initialize remember me functionality
     */
    function initializeRememberMe() {
        const rememberMeCheckbox = document.querySelector('input[name="remember"]');
        if (rememberMeCheckbox) {
            rememberMeCheckbox.addEventListener('change', function () {
                // Store preference in localStorage
                localStorage.setItem('rememberMe', this.checked);
            });

            // Restore preference
            const rememberMe = localStorage.getItem('rememberMe');
            if (rememberMe === 'true') {
                rememberMeCheckbox.checked = true;
            }
        }
    }

    /**
     * Initialize form validation
     */
    function initializeFormValidation() {
        const forms = document.querySelectorAll('.auth-form');
        forms.forEach(function (form) {
            form.addEventListener('submit', function (e) {
                if (!validateAuthForm(form)) {
                    e.preventDefault();
                }
            });
        });
    }

    /**
     * Validate authentication form
     */
    function validateAuthForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('.form-control[required]');

        inputs.forEach(function (input) {
            if (!validateAuthField(input)) {
                isValid = false;
            }
        });

        return isValid;
    }

    /**
     * Validate authentication field
     */
    function validateAuthField(field) {
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

        // Confirm password validation
        if (field.name === 'password_confirm' && value) {
            const passwordField = field.form.querySelector('input[name="password"]');
            if (passwordField && value !== passwordField.value) {
                isValid = false;
                message = 'Passwords do not match.';
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
     * Clear field error
     */
    function clearFieldError(field) {
        field.classList.remove('is-invalid');
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.remove();
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
     * Initialize authentication modals
     */
    function initializeAuthModals() {
        const authModals = document.querySelectorAll('.auth-modal');
        authModals.forEach(function (modal) {
            modal.addEventListener('shown.bs.modal', function () {
                const firstInput = modal.querySelector('input, textarea, select');
                if (firstInput) {
                    firstInput.focus();
                }
            });
        });
    }

    /**
     * Initialize authentication animations
     */
    function initializeAuthAnimations() {
        // Add entrance animations
        const authCard = document.querySelector('.auth-card');
        if (authCard) {
            authCard.classList.add('fade-in-up');
        }

        // Add hover effects
        const authButtons = document.querySelectorAll('.auth-btn');
        authButtons.forEach(function (button) {
            button.addEventListener('mouseenter', function () {
                this.style.transform = 'translateY(-2px)';
            });

            button.addEventListener('mouseleave', function () {
                this.style.transform = 'translateY(0)';
            });
        });
    }

    /**
     * Show form loading state
     */
    function showFormLoading(form) {
        form.classList.add('auth-loading');
        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Processing...';
        }
    }

    /**
     * Hide form loading state
     */
    function hideFormLoading(form) {
        form.classList.remove('auth-loading');
        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            const originalText = submitButton.dataset.originalText || 'Submit';
            submitButton.innerHTML = originalText;
        }
    }

    /**
     * Show authentication message
     */
    function showAuthMessage(message, type = 'info') {
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

        const container = document.querySelector('.auth-content') || document.body;
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
    window.Auth = {
        showAuthMessage,
        validateAuthField,
        validateAuthForm,
        updatePasswordStrength,
        initiateSocialLogin,
        checkSocialLoginStatus
    };

})();
