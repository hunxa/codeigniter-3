<form class="auth-form" action="<?php echo site_url('auth/process_reset_password'); ?>" method="POST" novalidate id="resetPasswordForm">
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

    <!-- Reset Token -->
    <input type="hidden" name="token" value="<?php echo $token; ?>">

    <!-- New Password Field -->
    <div class="form-group">
        <label for="password" class="form-label">New Password</label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-lock"></i>
            </span>
            <input type="password"
                class="form-control"
                id="password"
                name="password"
                placeholder="Enter your new password"
                required
                autocomplete="new-password"
                data-strength="#password-strength">
            <button type="button" class="password-toggle" tabindex="-1">
                <i class="bi bi-eye"></i>
            </button>
        </div>
        <!-- Password Strength Indicator -->
        <div id="password-strength" class="password-strength mt-2"></div>
        <?php if (form_error('password')): ?>
            <div class="invalid-feedback d-block">
                <?php echo form_error('password'); ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Confirm Password Field -->
    <div class="form-group">
        <label for="password_confirm" class="form-label">Confirm New Password</label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-lock-fill"></i>
            </span>
            <input type="password"
                class="form-control"
                id="password_confirm"
                name="password_confirm"
                placeholder="Confirm your new password"
                required
                autocomplete="new-password">
            <button type="button" class="password-toggle" tabindex="-1">
                <i class="bi bi-eye"></i>
            </button>
        </div>
        <?php if (form_error('password_confirm')): ?>
            <div class="invalid-feedback d-block">
                <?php echo form_error('password_confirm'); ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Password Requirements -->
    <div class="password-requirements">
        <h6 class="mb-2">Password Requirements:</h6>
        <ul class="list-unstyled small text-muted">
            <li id="req-length" class="requirement-item">
                <i class="bi bi-circle me-2"></i>
                At least 8 characters long
            </li>
            <li id="req-uppercase" class="requirement-item">
                <i class="bi bi-circle me-2"></i>
                Contains uppercase letter
            </li>
            <li id="req-lowercase" class="requirement-item">
                <i class="bi bi-circle me-2"></i>
                Contains lowercase letter
            </li>
            <li id="req-number" class="requirement-item">
                <i class="bi bi-circle me-2"></i>
                Contains number
            </li>
            <li id="req-special" class="requirement-item">
                <i class="bi bi-circle me-2"></i>
                Contains special character
            </li>
        </ul>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="auth-btn auth-btn-primary" data-original-text="Reset Password">
        <i class="bi bi-key me-2"></i>
        Reset Password
    </button>

    <!-- Back to Login -->
    <div class="auth-links">
        <p class="mb-0">
            Remember your password?
            <a href="<?php echo site_url('auth/login'); ?>" class="auth-link">
                Sign in here
            </a>
        </p>
    </div>
</form>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">
                    <i class="bi bi-check-circle text-success me-2"></i>
                    Password Reset Successfully
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-4">
                    Your password has been successfully reset. You can now sign in with your new password.
                </p>
                <div class="text-center">
                    <i class="bi bi-shield-check text-success" style="font-size: 3rem;"></i>
                </div>
            </div>
            <div class="modal-footer">
                <a href="<?php echo site_url('auth/login'); ?>" class="btn btn-primary">
                    <i class="bi bi-box-arrow-in-right me-2"></i>
                    Sign In
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-focus password field
        const passwordField = document.getElementById('password');
        if (passwordField) {
            passwordField.focus();
        }

        // Handle form submission
        const resetPasswordForm = document.querySelector('.auth-form');
        if (resetPasswordForm) {
            resetPasswordForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validate form
                if (!validateResetPasswordForm()) {
                    return;
                }

                // Show loading state
                const submitButton = this.querySelector('button[type="submit"]');
                const originalText = submitButton.innerHTML;
                submitButton.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Resetting...';
                submitButton.disabled = true;

                // Disable form elements
                const formElements = this.querySelectorAll('input, button, select, textarea');
                formElements.forEach(function(element) {
                    element.disabled = true;
                });

                // Submit form
                this.submit();
            });
        }

        // Password validation
        if (passwordField) {
            passwordField.addEventListener('input', function() {
                validatePasswordRequirements(this.value);
            });
        }

        // Password confirmation validation
        const confirmPasswordField = document.getElementById('password_confirm');
        if (confirmPasswordField) {
            confirmPasswordField.addEventListener('input', function() {
                if (this.value && passwordField.value !== this.value) {
                    this.classList.add('is-invalid');
                    showFieldError(this, 'Passwords do not match');
                } else {
                    this.classList.remove('is-invalid');
                    clearFieldError(this);
                }
            });
        }

        // Handle success modal
        const successModal = document.getElementById('successModal');
        if (successModal) {
            // Show modal if password was reset successfully
            if (window.location.search.includes('success=1')) {
                const modal = new bootstrap.Modal(successModal);
                modal.show();
            }
        }
    });

    function validateResetPasswordForm() {
        let isValid = true;

        // Check password field
        const passwordField = document.getElementById('password');
        if (!passwordField.value) {
            passwordField.classList.add('is-invalid');
            showFieldError(passwordField, 'Password is required');
            isValid = false;
        } else if (!validatePasswordStrength(passwordField.value)) {
            passwordField.classList.add('is-invalid');
            showFieldError(passwordField, 'Password does not meet requirements');
            isValid = false;
        } else {
            passwordField.classList.remove('is-invalid');
            clearFieldError(passwordField);
        }

        // Check password confirmation
        const confirmPasswordField = document.getElementById('password_confirm');
        if (!confirmPasswordField.value) {
            confirmPasswordField.classList.add('is-invalid');
            showFieldError(confirmPasswordField, 'Password confirmation is required');
            isValid = false;
        } else if (passwordField.value !== confirmPasswordField.value) {
            confirmPasswordField.classList.add('is-invalid');
            showFieldError(confirmPasswordField, 'Passwords do not match');
            isValid = false;
        } else {
            confirmPasswordField.classList.remove('is-invalid');
            clearFieldError(confirmPasswordField);
        }

        return isValid;
    }

    function validatePasswordRequirements(password) {
        const requirements = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[^A-Za-z0-9]/.test(password)
        };

        // Update requirement indicators
        Object.keys(requirements).forEach(function(req) {
            const element = document.getElementById('req-' + req);
            if (element) {
                const icon = element.querySelector('i');
                if (requirements[req]) {
                    icon.className = 'bi bi-check-circle-fill text-success me-2';
                    element.classList.add('text-success');
                } else {
                    icon.className = 'bi bi-circle me-2';
                    element.classList.remove('text-success');
                }
            }
        });
    }

    function validatePasswordStrength(password) {
        const requirements = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[^A-Za-z0-9]/.test(password)
        };

        return Object.values(requirements).every(req => req);
    }

    function showFieldError(field, message) {
        let feedback = field.parentNode.querySelector('.invalid-feedback');
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            field.parentNode.appendChild(feedback);
        }
        feedback.textContent = message;
    }

    function clearFieldError(field) {
        field.classList.remove('is-invalid');
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.remove();
        }
    }
</script>

<style>
    .password-requirements {
        background: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .requirement-item {
        margin-bottom: 0.5rem;
        transition: color 0.3s ease;
    }

    .requirement-item.text-success {
        color: #198754 !important;
    }

    .password-strength {
        display: flex;
        gap: 0.25rem;
        margin-top: 0.5rem;
    }

    .strength-bar {
        height: 4px;
        background: #e9ecef;
        border-radius: 2px;
        flex: 1;
        transition: background-color 0.3s ease;
    }

    .strength-bar.active {
        background: #198754;
    }

    .password-strength.weak .strength-bar.active {
        background: #dc3545;
    }

    .password-strength.medium .strength-bar.active {
        background: #ffc107;
    }

    .password-strength.strong .strength-bar.active {
        background: #198754;
    }

    .strength-text {
        font-size: 0.875rem;
        font-weight: 500;
        margin-left: 0.5rem;
    }
</style>