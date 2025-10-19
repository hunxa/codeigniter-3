<form class="auth-form" action="<?php echo site_url('auth/process_forgot_password'); ?>" method="POST" novalidate id="forgotPasswordForm">
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
        <label for="email" class="form-label">Email Address</label>
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
                autocomplete="email">
        </div>
        <?php if (form_error('email')): ?>
            <div class="invalid-feedback d-block">
                <?php echo form_error('email'); ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="auth-btn auth-btn-primary" data-original-text="Send Reset Link">
        <i class="bi bi-send me-2"></i>
        Send Reset Link
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
                    Check Your Email
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-4">
                    We've sent a password reset link to your email address. Please check your inbox and follow the instructions to reset your password.
                </p>
                <div class="text-center">
                    <i class="bi bi-envelope-check text-primary" style="font-size: 3rem;"></i>
                </div>
                <div class="alert alert-info mt-3">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Note:</strong> The reset link will expire in 1 hour for security reasons.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    Got it
                </button>
                <button type="button" class="btn btn-outline-secondary" id="resendEmail">
                    Resend Email
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-focus email field
        const emailField = document.getElementById('email');
        if (emailField) {
            emailField.focus();
        }

        // Handle form submission
        const forgotPasswordForm = document.querySelector('.auth-form');
        if (forgotPasswordForm) {
            forgotPasswordForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validate form
                if (!validateForgotPasswordForm()) {
                    return;
                }

                // Show loading state
                const submitButton = this.querySelector('button[type="submit"]');
                const originalText = submitButton.innerHTML;
                submitButton.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Sending...';
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

        // Handle success modal
        const successModal = document.getElementById('successModal');
        if (successModal) {
            // Show modal if password reset email was sent
            if (window.location.search.includes('sent=1')) {
                const modal = new bootstrap.Modal(successModal);
                modal.show();
            }

            // Handle resend email
            const resendButton = document.getElementById('resendEmail');
            if (resendButton) {
                resendButton.addEventListener('click', function() {
                    this.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Sending...';
                    this.disabled = true;

                    // Make AJAX request to resend email
                    const email = document.getElementById('email').value;
                    fetch('<?php echo site_url('auth/resend-reset-email'); ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                                email: email
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showAuthMessage('Password reset email sent successfully!', 'success');
                            } else {
                                showAuthMessage(data.message || 'Failed to send password reset email', 'error');
                            }
                        })
                        .catch(error => {
                            showAuthMessage('An error occurred. Please try again.', 'error');
                        })
                        .finally(function() {
                            resendButton.innerHTML = 'Resend Email';
                            resendButton.disabled = false;
                        });
                });
            }
        }
    });

    function validateForgotPasswordForm() {
        let isValid = true;

        // Check email field
        const emailField = document.getElementById('email');
        if (!emailField.value) {
            emailField.classList.add('is-invalid');
            showFieldError(emailField, 'Email address is required');
            isValid = false;
        } else if (!isValidEmail(emailField.value)) {
            emailField.classList.add('is-invalid');
            showFieldError(emailField, 'Please enter a valid email address');
            isValid = false;
        } else {
            emailField.classList.remove('is-invalid');
            clearFieldError(emailField);
        }

        return isValid;
    }

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
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