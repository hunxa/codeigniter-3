<?php if ($this->session->flashdata('success') || $this->session->flashdata('error') || $this->session->flashdata('warning') || $this->session->flashdata('info')): ?>
    <div class="flash-messages-container position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 1050;">
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show flash-message" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?php echo $this->session->flashdata('success'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show flash-message" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?php echo $this->session->flashdata('error'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('warning')): ?>
            <div class="alert alert-warning alert-dismissible fade show flash-message" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?php echo $this->session->flashdata('warning'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('info')): ?>
            <div class="alert alert-info alert-dismissible fade show flash-message" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i>
                <?php echo $this->session->flashdata('info'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- AJAX Flash Messages Container -->
<div id="ajax-flash-messages" class="position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 1050; display: none;"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide flash messages after 5 seconds
        const flashMessages = document.querySelectorAll('.flash-message');
        flashMessages.forEach(function(message) {
            setTimeout(function() {
                const alert = new bootstrap.Alert(message);
                alert.close();
            }, 5000);
        });

        // Add click to dismiss functionality
        flashMessages.forEach(function(message) {
            message.addEventListener('click', function() {
                const alert = new bootstrap.Alert(message);
                alert.close();
            });
        });
    });

    // Function to show AJAX flash messages
    function showFlashMessage(message, type = 'info') {
        const container = document.getElementById('ajax-flash-messages');
        const alertClass = 'alert-' + type;
        const iconClass = getIconClass(type);

        const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show flash-message" role="alert">
            <i class="${iconClass} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

        container.innerHTML = alertHtml;
        container.style.display = 'block';

        // Auto-hide after 5 seconds
        setTimeout(function() {
            const alert = container.querySelector('.alert');
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);

        // Hide container when all alerts are closed
        container.addEventListener('closed.bs.alert', function() {
            if (container.querySelectorAll('.alert').length === 0) {
                container.style.display = 'none';
            }
        });
    }

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

    // Global error handler for AJAX requests
    $(document).ajaxError(function(event, xhr, settings, thrownError) {
        let message = 'An error occurred. Please try again.';

        if (xhr.responseJSON && xhr.responseJSON.message) {
            message = xhr.responseJSON.message;
        } else if (xhr.status === 401) {
            message = 'You are not authorized to perform this action.';
        } else if (xhr.status === 403) {
            message = 'Access denied. You do not have permission to perform this action.';
        } else if (xhr.status === 404) {
            message = 'The requested resource was not found.';
        } else if (xhr.status === 422) {
            message = 'Please check your input and try again.';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = Object.values(xhr.responseJSON.errors).flat();
                message = errors.join('<br>');
            }
        } else if (xhr.status === 500) {
            message = 'A server error occurred. Please try again later.';
        }

        showFlashMessage(message, 'error');
    });

    // Global success handler for AJAX requests
    $(document).ajaxSuccess(function(event, xhr, settings) {
        if (xhr.responseJSON && xhr.responseJSON.message) {
            showFlashMessage(xhr.responseJSON.message, 'success');
        }
    });
</script>

<style>
    .flash-messages-container {
        max-width: 500px;
        width: 100%;
    }

    .flash-message {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: none;
        margin-bottom: 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .flash-message:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .flash-message .btn-close {
        padding: 0.5rem;
    }

    @media (max-width: 576px) {
        .flash-messages-container {
            margin: 0 1rem;
            max-width: none;
        }
    }
</style>