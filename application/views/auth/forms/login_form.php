<form class="auth-form" action="<?php echo site_url('auth/process_login'); ?>" method="POST" novalidate id="loginForm">
    <!-- CSRF Token -->
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token">

    <!-- CSRF Error Display -->
    <div class="alert alert-danger d-none" id="csrf-error" role="alert">
        <i class="bi bi-shield-exclamation me-2"></i>
        <strong>Security Error:</strong> Your session has expired. Please refresh the page and try again.
        <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="refreshCSRFToken()">
            <i class="bi bi-arrow-clockwise me-1"></i>
            Refresh
        </button>
    </div>

    <!-- Email Field -->
    <div class="form-group">
        <label for="email" class="form-label">
            <i class="bi bi-envelope me-1"></i>
            Email Address
        </label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-envelope"></i>
            </span>
            <input type="email"
                class="form-control"
                id="email"
                name="email"
                placeholder="Enter your email address"
                value="<?php echo set_value('email'); ?>"
                required
                autocomplete="email"
                data-validation="email">
            <div class="input-group-append">
                <span class="input-group-text validation-icon">
                    <i class="bi bi-check-circle-fill text-success d-none" id="email-valid"></i>
                    <i class="bi bi-exclamation-circle-fill text-danger d-none" id="email-invalid"></i>
                </span>
            </div>
        </div>
        <div class="form-text" id="email-help">We'll never share your email with anyone else.</div>
        <?php if (form_error('email')): ?>
            <div class="invalid-feedback d-block">
                <i class="bi bi-exclamation-triangle me-1"></i>
                <?php echo form_error('email'); ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Password Field -->
    <div class="form-group">
        <label for="password" class="form-label">
            <i class="bi bi-lock me-1"></i>
            Password
        </label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-lock"></i>
            </span>
            <input type="password"
                class="form-control"
                id="password"
                name="password"
                placeholder="Enter your password"
                required
                autocomplete="current-password"
                data-validation="password">
            <button type="button" class="password-toggle" tabindex="-1" aria-label="Toggle password visibility">
                <i class="bi bi-eye" id="password-toggle-icon"></i>
            </button>
        </div>
        <div class="password-strength d-none" id="password-strength">
            <div class="strength-bar">
                <div class="strength-fill" id="strength-fill"></div>
            </div>
            <small class="strength-text" id="strength-text">Password strength</small>
        </div>
        <?php if (form_error('password')): ?>
            <div class="invalid-feedback d-block">
                <i class="bi bi-exclamation-triangle me-1"></i>
                <?php echo form_error('password'); ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Remember Me & Forgot Password -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
            <label class="form-check-label" for="remember">
                <i class="bi bi-check-square me-1"></i>
                Remember me for 30 days
            </label>
        </div>
        <a href="<?php echo site_url('auth/forgot-password'); ?>" class="auth-link" title="Reset your password">
            <i class="bi bi-key me-1"></i>
            Forgot password?
        </a>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="auth-btn auth-btn-primary" data-original-text="Sign In" id="loginSubmit">
        <span class="btn-content">
            <i class="bi bi-box-arrow-in-right me-2"></i>
            Sign In
        </span>
        <span class="btn-loading d-none">
            <i class="bi bi-hourglass-split me-2"></i>
            Signing in...
        </span>
    </button>

    <!-- Quick Login Options -->
    <div class="quick-login mt-3">
        <div class="text-center">
            <small class="text-muted">Quick access:</small>
        </div>
        <div class="d-flex justify-content-center gap-2 mt-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="fillDemoCredentials()" title="Fill demo credentials">
                <i class="bi bi-person-check me-1"></i>
                Demo Account
            </button>
            <button type="button" class="btn btn-sm btn-outline-info" onclick="clearForm()" title="Clear form">
                <i class="bi bi-arrow-clockwise me-1"></i>
                Clear
            </button>
        </div>
    </div>

    <!-- Social Login -->
    <div class="social-login">
        <div class="social-login-title">
            <span>Or continue with</span>
        </div>
        <div class="social-buttons">
            <a href="<?php echo site_url('auth/social/google'); ?>" class="social-btn social-btn-google" data-provider="google">
                <i class="bi bi-google me-2"></i>
                Google
            </a>
            <a href="<?php echo site_url('auth/social/facebook'); ?>" class="social-btn social-btn-facebook" data-provider="facebook">
                <i class="bi bi-facebook me-2"></i>
                Facebook
            </a>
            <a href="<?php echo site_url('auth/social/github'); ?>" class="social-btn social-btn-github" data-provider="github">
                <i class="bi bi-github me-2"></i>
                GitHub
            </a>
        </div>
    </div>

    <!-- Register Link -->
    <div class="auth-links">
        <p class="mb-0">
            Don't have an account?
            <a href="<?php echo site_url('auth/register'); ?>" class="auth-link">
                Create one here
            </a>
        </p>
    </div>
</form>

<!-- Two-Factor Authentication Modal -->
<div class="modal fade" id="twoFactorModal" tabindex="-1" aria-labelledby="twoFactorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="twoFactorModalLabel">
                    <i class="bi bi-shield-check me-2"></i>
                    Two-Factor Authentication
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-4">
                    Please enter the 6-digit code from your authenticator app.
                </p>
                <form id="two-factor-form" action="<?php echo site_url('auth/verify-2fa'); ?>" method="POST">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="form-group">
                        <label for="code" class="form-label">Verification Code</label>
                        <input type="text"
                            class="form-control text-center"
                            id="code"
                            name="code"
                            placeholder="000000"
                            maxlength="6"
                            pattern="[0-9]{6}"
                            required
                            autocomplete="one-time-code">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>
                            Verify Code
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
                <a href="<?php echo site_url('auth/2fa/backup'); ?>" class="btn btn-link">
                    Use backup code
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-focus email field
        const emailField = document.getElementById('email');
        if (emailField && !emailField.value) {
            emailField.focus();
        }

        // Real-time validation
        setupRealTimeValidation();

        // Password toggle functionality
        setupPasswordToggle();

        // Handle form submission
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                // Validate form before submission
                if (!validateLoginForm()) {
                    e.preventDefault();
                    return false;
                }

                // Show loading state
                showLoadingState();

                // Let the form submit normally - don't prevent default
                // This ensures CSRF token is properly handled by CodeIgniter
            });
        }

        // Setup demo credentials
        window.fillDemoCredentials = function() {
            document.getElementById('email').value = 'demo@example.com';
            document.getElementById('password').value = 'demo123';
            validateField(document.getElementById('email'));
            validateField(document.getElementById('password'));
        };

        // Setup clear form
        window.clearForm = function() {
            loginForm.reset();
            clearValidationStates();
        };

        // Setup CSRF token refresh
        window.refreshCSRFToken = function() {
            fetch('<?php echo site_url('auth/refresh-csrf'); ?>', {
                    method: 'GET',
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('csrf_token').value = data.token;
                        document.getElementById('csrf-error').classList.add('d-none');
                        showAuthMessage('CSRF token refreshed successfully!', 'success');
                    } else {
                        showAuthMessage('Failed to refresh CSRF token. Please reload the page.', 'error');
                    }
                })
                .catch(error => {
                    console.error('CSRF refresh error:', error);
                    showAuthMessage('Failed to refresh CSRF token. Please reload the page.', 'error');
                });
        };

        // Check for CSRF errors on page load
        checkCSRFError();

        // Handle two-factor authentication
        const twoFactorModal = document.getElementById('twoFactorModal');
        if (twoFactorModal) {
            // Show modal if 2FA is required
            if (window.location.search.includes('2fa=required')) {
                const modal = new bootstrap.Modal(twoFactorModal);
                modal.show();
            }

            // Auto-submit when 6 digits are entered
            const codeInput = document.getElementById('code');
            if (codeInput) {
                codeInput.addEventListener('input', function() {
                    if (this.value.length === 6) {
                        this.form.submit();
                    }
                });
            }
        }

        // Handle social login
        const socialButtons = document.querySelectorAll('.social-btn');
        socialButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const provider = this.dataset.provider;
                if (provider) {
                    // Show loading state
                    this.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Connecting...';
                    this.disabled = true;

                    // Redirect to social login
                    window.location.href = this.href;
                }
            });
        });
    });

    // Real-time validation setup
    function setupRealTimeValidation() {
        const emailField = document.getElementById('email');
        const passwordField = document.getElementById('password');

        if (emailField) {
            emailField.addEventListener('blur', function() {
                validateField(this);
            });
            emailField.addEventListener('input', function() {
                clearFieldError(this);
            });
        }

        if (passwordField) {
            passwordField.addEventListener('blur', function() {
                validateField(this);
            });
            passwordField.addEventListener('input', function() {
                clearFieldError(this);
            });
        }
    }

    // Password toggle setup
    function setupPasswordToggle() {
        const passwordToggle = document.querySelector('.password-toggle');
        const passwordField = document.getElementById('password');
        const toggleIcon = document.getElementById('password-toggle-icon');

        if (passwordToggle && passwordField && toggleIcon) {
            passwordToggle.addEventListener('click', function() {
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);

                toggleIcon.classList.toggle('bi-eye');
                toggleIcon.classList.toggle('bi-eye-slash');
            });
        }
    }

    // Field validation
    function validateField(field) {
        const value = field.value.trim();
        const fieldType = field.getAttribute('data-validation');
        let isValid = false;

        switch (fieldType) {
            case 'email':
                isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
                break;
            case 'password':
                isValid = value.length >= 6;
                break;
            default:
                isValid = value.length > 0;
        }

        updateFieldValidation(field, isValid);
        return isValid;
    }

    // Update field validation UI
    function updateFieldValidation(field, isValid) {
        const validIcon = field.parentNode.querySelector('#email-valid, #password-valid');
        const invalidIcon = field.parentNode.querySelector('#email-invalid, #password-invalid');

        if (isValid) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            if (validIcon) validIcon.classList.remove('d-none');
            if (invalidIcon) invalidIcon.classList.add('d-none');
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            if (validIcon) validIcon.classList.add('d-none');
            if (invalidIcon) invalidIcon.classList.remove('d-none');
        }
    }

    // Clear field error
    function clearFieldError(field) {
        field.classList.remove('is-invalid', 'is-valid');
        const validIcon = field.parentNode.querySelector('#email-valid, #password-valid');
        const invalidIcon = field.parentNode.querySelector('#email-invalid, #password-invalid');
        if (validIcon) validIcon.classList.add('d-none');
        if (invalidIcon) invalidIcon.classList.add('d-none');
    }

    // Clear all validation states
    function clearValidationStates() {
        const fields = document.querySelectorAll('.form-control');
        fields.forEach(field => {
            field.classList.remove('is-valid', 'is-invalid');
        });

        const icons = document.querySelectorAll('.validation-icon i');
        icons.forEach(icon => {
            icon.classList.add('d-none');
        });
    }

    // Validate entire form
    function validateLoginForm() {
        const emailField = document.getElementById('email');
        const passwordField = document.getElementById('password');

        const emailValid = validateField(emailField);
        const passwordValid = validateField(passwordField);

        return emailValid && passwordValid;
    }

    // Show loading state
    function showLoadingState() {
        const submitButton = document.getElementById('loginSubmit');
        const btnContent = submitButton.querySelector('.btn-content');
        const btnLoading = submitButton.querySelector('.btn-loading');

        btnContent.classList.add('d-none');
        btnLoading.classList.remove('d-none');
        submitButton.disabled = true;

        // Disable form elements
        const formElements = document.querySelectorAll('#loginForm input, #loginForm button, #loginForm select, #loginForm textarea');
        formElements.forEach(function(element) {
            element.disabled = true;
        });
    }

    // Check for CSRF errors
    function checkCSRFError() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('csrf_error') === '1') {
            document.getElementById('csrf-error').classList.remove('d-none');
            showAuthMessage('CSRF token validation failed. Please try again.', 'error');
        }
    }

    // Show auth messages
    function showAuthMessage(message, type = 'info') {
        // Remove existing messages
        const existingMessages = document.querySelectorAll('.auth-message');
        existingMessages.forEach(msg => msg.remove());

        // Create new message
        const messageDiv = document.createElement('div');
        messageDiv.className = `alert alert-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'} auth-message`;
        messageDiv.innerHTML = `
            <i class="bi bi-${type === 'error' ? 'exclamation-triangle' : type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
        `;

        // Insert after CSRF error div
        const csrfError = document.getElementById('csrf-error');
        csrfError.parentNode.insertBefore(messageDiv, csrfError.nextSibling);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.remove();
            }
        }, 5000);
    }

    // Enhanced form submission with CSRF error handling
    function submitFormWithCSRFHandling(form) {
        const formData = new FormData(form);

        fetch(form.action, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => {
                if (response.ok) {
                    return response.text();
                } else if (response.status === 403) {
                    // CSRF error
                    document.getElementById('csrf-error').classList.remove('d-none');
                    showAuthMessage('CSRF token validation failed. Please refresh and try again.', 'error');
                    throw new Error('CSRF validation failed');
                } else {
                    throw new Error('Server error');
                }
            })
            .then(html => {
                // Check if response contains CSRF error
                if (html.includes('action you have requested is not allowed')) {
                    document.getElementById('csrf-error').classList.remove('d-none');
                    showAuthMessage('CSRF token validation failed. Please refresh and try again.', 'error');
                } else {
                    // Success - redirect or show success message
                    window.location.href = '<?php echo site_url('dashboard'); ?>';
                }
            })
            .catch(error => {
                console.error('Form submission error:', error);
                showAuthMessage('Login failed. Please check your credentials and try again.', 'error');
            })
            .finally(() => {
                // Reset loading state
                const submitButton = document.getElementById('loginSubmit');
                const btnContent = submitButton.querySelector('.btn-content');
                const btnLoading = submitButton.querySelector('.btn-loading');

                btnContent.classList.remove('d-none');
                btnLoading.classList.add('d-none');
                submitButton.disabled = false;

                // Re-enable form elements
                const formElements = document.querySelectorAll('#loginForm input, #loginForm button, #loginForm select, #loginForm textarea');
                formElements.forEach(function(element) {
                    element.disabled = false;
                });
            });
    }
</script>