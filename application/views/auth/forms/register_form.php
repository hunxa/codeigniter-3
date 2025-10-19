<form class="auth-form" action="<?php echo site_url('auth/process_register'); ?>" method="POST" novalidate id="registerForm">
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

    <!-- Name Field -->
    <div class="form-group">
        <label for="name" class="form-label">Full Name</label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-person"></i>
            </span>
            <input type="text"
                class="form-control"
                id="name"
                name="name"
                placeholder="Enter your full name"
                value="<?php echo set_value('name'); ?>"
                required
                autocomplete="name">
        </div>
        <?php if (form_error('name')): ?>
            <div class="invalid-feedback d-block">
                <?php echo form_error('name'); ?>
            </div>
        <?php endif; ?>
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

    <!-- Password Field -->
    <div class="form-group">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-lock"></i>
            </span>
            <input type="password"
                class="form-control"
                id="password"
                name="password"
                placeholder="Create a strong password"
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
        <label for="password_confirm" class="form-label">Confirm Password</label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-lock-fill"></i>
            </span>
            <input type="password"
                class="form-control"
                id="password_confirm"
                name="password_confirm"
                placeholder="Confirm your password"
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

    <!-- Terms and Conditions -->
    <div class="form-group">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="terms" name="terms" value="1" required>
            <label class="form-check-label" for="terms">
                I agree to the <a href="<?php echo site_url('terms'); ?>" target="_blank" class="auth-link">Terms of Service</a>
                and <a href="<?php echo site_url('privacy'); ?>" target="_blank" class="auth-link">Privacy Policy</a>
            </label>
        </div>
        <?php if (form_error('terms')): ?>
            <div class="invalid-feedback d-block">
                <?php echo form_error('terms'); ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Newsletter Subscription -->
    <div class="form-group">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter" value="1">
            <label class="form-check-label" for="newsletter">
                Subscribe to our newsletter for updates and news
            </label>
        </div>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="auth-btn auth-btn-primary" data-original-text="Create Account">
        <i class="bi bi-person-plus me-2"></i>
        Create Account
    </button>

    <!-- Social Login -->
    <div class="social-login">
        <div class="social-login-title">
            <span>Or sign up with</span>
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

    <!-- Login Link -->
    <div class="auth-links">
        <p class="mb-0">
            Already have an account?
            <a href="<?php echo site_url('auth/login'); ?>" class="auth-link">
                Sign in here
            </a>
        </p>
    </div>
</form>

<!-- Email Verification Modal -->
<div class="modal fade" id="emailVerificationModal" tabindex="-1" aria-labelledby="emailVerificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailVerificationModalLabel">
                    <i class="bi bi-envelope-check me-2"></i>
                    Verify Your Email
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-4">
                    We've sent a verification link to your email address. Please check your inbox and click the link to verify your account.
                </p>
                <div class="text-center">
                    <i class="bi bi-envelope-check text-primary" style="font-size: 3rem;"></i>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    Got it
                </button>
                <button type="button" class="btn btn-outline-secondary" id="resendVerification">
                    Resend Email
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-focus name field
        const nameField = document.getElementById('name');
        if (nameField) {
            nameField.focus();
        }

        // Handle form submission
        const registerForm = document.querySelector('.auth-form');
        if (registerForm) {
            registerForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validate form
                if (!validateRegisterForm()) {
                    return;
                }

                // Show loading state
                const submitButton = this.querySelector('button[type="submit"]');
                const originalText = submitButton.innerHTML;
                submitButton.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Creating Account...';
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

        // Password confirmation validation
        const passwordField = document.getElementById('password');
        const confirmPasswordField = document.getElementById('password_confirm');

        if (passwordField && confirmPasswordField) {
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

        // Handle email verification modal
        const emailVerificationModal = document.getElementById('emailVerificationModal');
        if (emailVerificationModal) {
            // Show modal if email verification is required
            if (window.location.search.includes('verify=email')) {
                const modal = new bootstrap.Modal(emailVerificationModal);
                modal.show();
            }

            // Handle resend verification
            const resendButton = document.getElementById('resendVerification');
            if (resendButton) {
                resendButton.addEventListener('click', function() {
                    this.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Sending...';
                    this.disabled = true;

                    // Make AJAX request to resend verification
                    fetch('<?php echo site_url('auth/resend-verification'); ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showAuthMessage('Verification email sent successfully!', 'success');
                            } else {
                                showAuthMessage(data.message || 'Failed to send verification email', 'error');
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

    function validateRegisterForm() {
        let isValid = true;

        // Check required fields
        const requiredFields = ['name', 'email', 'password', 'password_confirm', 'terms'];
        requiredFields.forEach(function(fieldName) {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field && !field.value) {
                field.classList.add('is-invalid');
                showFieldError(field, 'This field is required');
                isValid = false;
            }
        });

        // Check password match
        const passwordField = document.getElementById('password');
        const confirmPasswordField = document.getElementById('password_confirm');
        if (passwordField && confirmPasswordField && passwordField.value !== confirmPasswordField.value) {
            confirmPasswordField.classList.add('is-invalid');
            showFieldError(confirmPasswordField, 'Passwords do not match');
            isValid = false;
        }

        return isValid;
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