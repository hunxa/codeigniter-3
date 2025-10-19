<footer class="bg-dark text-light py-5 mt-auto">
    <div class="container">
        <div class="row g-4">
            <!-- Company Info -->
            <div class="col-lg-4 col-md-6">
                <div class="footer-brand mb-3">
                    <h5 class="fw-bold">
                        <i class="bi bi-shield-check-fill me-2 text-primary"></i>
                        <?php echo $this->config->item('site_name'); ?>
                    </h5>
                </div>
                <p class="text-muted mb-3">
                    <?php echo $this->config->item('site_description'); ?>
                </p>
                <div class="social-links">
                    <a href="#" class="text-light me-3" title="Facebook">
                        <i class="bi bi-facebook fs-5"></i>
                    </a>
                    <a href="#" class="text-light me-3" title="Twitter">
                        <i class="bi bi-twitter fs-5"></i>
                    </a>
                    <a href="#" class="text-light me-3" title="LinkedIn">
                        <i class="bi bi-linkedin fs-5"></i>
                    </a>
                    <a href="#" class="text-light me-3" title="GitHub">
                        <i class="bi bi-github fs-5"></i>
                    </a>
                    <a href="#" class="text-light" title="Instagram">
                        <i class="bi bi-instagram fs-5"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6">
                <h6 class="fw-bold mb-3">Quick Links</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="<?php echo site_url(); ?>" class="text-muted text-decoration-none">
                            <i class="bi bi-chevron-right me-1"></i>Home
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?php echo site_url('about'); ?>" class="text-muted text-decoration-none">
                            <i class="bi bi-chevron-right me-1"></i>About
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?php echo site_url('services'); ?>" class="text-muted text-decoration-none">
                            <i class="bi bi-chevron-right me-1"></i>Services
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?php echo site_url('contact'); ?>" class="text-muted text-decoration-none">
                            <i class="bi bi-chevron-right me-1"></i>Contact
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Support -->
            <div class="col-lg-2 col-md-6">
                <h6 class="fw-bold mb-3">Support</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="<?php echo site_url('help'); ?>" class="text-muted text-decoration-none">
                            <i class="bi bi-chevron-right me-1"></i>Help Center
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?php echo site_url('faq'); ?>" class="text-muted text-decoration-none">
                            <i class="bi bi-chevron-right me-1"></i>FAQ
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?php echo site_url('support'); ?>" class="text-muted text-decoration-none">
                            <i class="bi bi-chevron-right me-1"></i>Support
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?php echo site_url('status'); ?>" class="text-muted text-decoration-none">
                            <i class="bi bi-chevron-right me-1"></i>Status
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Legal -->
            <div class="col-lg-2 col-md-6">
                <h6 class="fw-bold mb-3">Legal</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="<?php echo site_url('privacy'); ?>" class="text-muted text-decoration-none">
                            <i class="bi bi-chevron-right me-1"></i>Privacy Policy
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?php echo site_url('terms'); ?>" class="text-muted text-decoration-none">
                            <i class="bi bi-chevron-right me-1"></i>Terms of Service
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?php echo site_url('cookies'); ?>" class="text-muted text-decoration-none">
                            <i class="bi bi-chevron-right me-1"></i>Cookie Policy
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?php echo site_url('security'); ?>" class="text-muted text-decoration-none">
                            <i class="bi bi-chevron-right me-1"></i>Security
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Newsletter -->
            <div class="col-lg-2 col-md-6">
                <h6 class="fw-bold mb-3">Newsletter</h6>
                <p class="text-muted small mb-3">Subscribe to our newsletter for updates.</p>
                <form class="newsletter-form" id="newsletterForm">
                    <div class="input-group input-group-sm">
                        <input type="email" class="form-control" placeholder="Your email" required>
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-send"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <hr class="my-4">

        <!-- Bottom Footer -->
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="text-muted small mb-0">
                    &copy; <?php echo date('Y'); ?> <?php echo $this->config->item('site_name'); ?>. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="text-muted small mb-0">
                    Made with <i class="bi bi-heart-fill text-danger"></i> using
                    <a href="https://codeigniter.com" class="text-primary text-decoration-none" target="_blank">CodeIgniter</a>
                </p>
            </div>
        </div>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Newsletter form submission
        const newsletterForm = document.getElementById('newsletterForm');
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const email = this.querySelector('input[type="email"]').value;
                const button = this.querySelector('button');
                const originalText = button.innerHTML;

                // Show loading state
                button.innerHTML = '<i class="bi bi-hourglass-split"></i>';
                button.disabled = true;

                // Simulate API call
                setTimeout(function() {
                    // Reset button
                    button.innerHTML = originalText;
                    button.disabled = false;

                    // Show success message
                    showFlashMessage('Thank you for subscribing to our newsletter!', 'success');

                    // Reset form
                    newsletterForm.reset();
                }, 1000);
            });
        }

        // Add hover effects to social links
        const socialLinks = document.querySelectorAll('.social-links a');
        socialLinks.forEach(link => {
            link.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.transition = 'transform 0.3s ease';
            });

            link.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });
</script>

<style>
    .social-links a {
        display: inline-block;
        transition: all 0.3s ease;
    }

    .social-links a:hover {
        color: var(--bs-primary) !important;
        transform: translateY(-2px);
    }

    .newsletter-form .input-group {
        max-width: 250px;
    }

    .newsletter-form .form-control:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25);
    }

    @media (max-width: 768px) {
        .newsletter-form .input-group {
            max-width: 100%;
        }
    }
</style>