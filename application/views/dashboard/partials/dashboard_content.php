<!-- Dashboard Stats -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <div class="dashboard-card-icon primary">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="dashboard-stat">
                    <div class="dashboard-stat-value">1,234</div>
                    <div class="dashboard-stat-label">Total Users</div>
                    <div class="dashboard-stat-change positive">
                        <i class="bi bi-arrow-up"></i> 12% from last month
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <div class="dashboard-card-icon success">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div class="dashboard-stat">
                    <div class="dashboard-stat-value">$45,678</div>
                    <div class="dashboard-stat-label">Revenue</div>
                    <div class="dashboard-stat-change positive">
                        <i class="bi bi-arrow-up"></i> 8% from last month
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <div class="dashboard-card-icon warning">
                    <i class="bi bi-cart-fill"></i>
                </div>
                <div class="dashboard-stat">
                    <div class="dashboard-stat-value">567</div>
                    <div class="dashboard-stat-label">Orders</div>
                    <div class="dashboard-stat-change negative">
                        <i class="bi bi-arrow-down"></i> 3% from last month
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <div class="dashboard-card-icon info">
                    <i class="bi bi-eye-fill"></i>
                </div>
                <div class="dashboard-stat">
                    <div class="dashboard-stat-value">89,012</div>
                    <div class="dashboard-stat-label">Page Views</div>
                    <div class="dashboard-stat-change positive">
                        <i class="bi bi-arrow-up"></i> 15% from last month
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-xl-8 mb-4">
        <div class="dashboard-chart">
            <div class="dashboard-chart-header">
                <h5 class="dashboard-chart-title">Revenue Overview</h5>
                <div class="dashboard-chart-actions">
                    <button class="btn btn-sm btn-outline-primary">Export</button>
                    <button class="btn btn-sm btn-outline-secondary">Settings</button>
                </div>
            </div>
            <div class="dashboard-chart-body">
                <canvas id="revenueChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-4 mb-4">
        <div class="dashboard-chart">
            <div class="dashboard-chart-header">
                <h5 class="dashboard-chart-title">Traffic Sources</h5>
            </div>
            <div class="dashboard-chart-body">
                <canvas id="trafficChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tables Row -->
<div class="row mb-4">
    <div class="col-xl-8 mb-4">
        <div class="dashboard-table">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th data-sort="name">Name</th>
                            <th data-sort="email">Email</th>
                            <th data-sort="role">Role</th>
                            <th data-sort="status">Status</th>
                            <th data-sort="created">Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar-small me-3">
                                        <i class="bi bi-person-circle"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">John Doe</div>
                                        <div class="text-muted small">ID: #1234</div>
                                    </div>
                                </div>
                            </td>
                            <td>john.doe@example.com</td>
                            <td><span class="badge bg-primary">Admin</span></td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>2024-01-15</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar-small me-3">
                                        <i class="bi bi-person-circle"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">Jane Smith</div>
                                        <div class="text-muted small">ID: #1235</div>
                                    </div>
                                </div>
                            </td>
                            <td>jane.smith@example.com</td>
                            <td><span class="badge bg-secondary">User</span></td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>2024-01-16</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar-small me-3">
                                        <i class="bi bi-person-circle"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">Bob Johnson</div>
                                        <div class="text-muted small">ID: #1236</div>
                                    </div>
                                </div>
                            </td>
                            <td>bob.johnson@example.com</td>
                            <td><span class="badge bg-warning">Moderator</span></td>
                            <td><span class="badge bg-danger">Inactive</span></td>
                            <td>2024-01-17</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-4 mb-4">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5 class="dashboard-card-title">Recent Activity</h5>
                <button class="btn btn-sm btn-outline-primary">View All</button>
            </div>
            <div class="dashboard-card-body">
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="bi bi-person-plus text-success"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">New user registered</div>
                            <div class="activity-message">John Doe joined the platform</div>
                            <div class="activity-time">2 minutes ago</div>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="bi bi-file-earmark-text text-info"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Document uploaded</div>
                            <div class="activity-message">project-proposal.pdf</div>
                            <div class="activity-time">1 hour ago</div>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="bi bi-gear text-warning"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Settings updated</div>
                            <div class="activity-message">Email preferences changed</div>
                            <div class="activity-time">3 hours ago</div>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="bi bi-shield-check text-primary"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Security alert</div>
                            <div class="activity-message">Login from new device</div>
                            <div class="activity-time">1 day ago</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5 class="dashboard-card-title">Quick Actions</h5>
            </div>
            <div class="dashboard-card-body">
                <div class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <a href="<?php echo site_url('profile'); ?>" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                            <i class="bi bi-person-fill fs-2 mb-2"></i>
                            <span>Update Profile</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="<?php echo site_url('settings'); ?>" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                            <i class="bi bi-gear-fill fs-2 mb-2"></i>
                            <span>Settings</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="<?php echo site_url('admin'); ?>" class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                            <i class="bi bi-shield-fill fs-2 mb-2"></i>
                            <span>Admin Panel</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="<?php echo site_url('help'); ?>" class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                            <i class="bi bi-question-circle-fill fs-2 mb-2"></i>
                            <span>Help & Support</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .activity-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .activity-item {
        display: flex;
        align-items: flex-start;
        padding: 1rem 0;
        border-bottom: 1px solid #e9ecef;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .activity-content {
        flex: 1;
        min-width: 0;
    }

    .activity-title {
        font-weight: 600;
        color: #212529;
        margin-bottom: 0.25rem;
    }

    .activity-message {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }

    .activity-time {
        color: #adb5bd;
        font-size: 0.8rem;
    }

    .user-avatar-small {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #0d6efd;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: white;
    }

    @media (max-width: 768px) {
        .activity-item {
            padding: 0.75rem 0;
        }

        .activity-icon {
            width: 32px;
            height: 32px;
            margin-right: 0.75rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize charts (placeholder - you would use Chart.js or similar)
        initializeCharts();

        // Initialize table sorting
        initializeTableSorting();

        // Initialize quick actions
        initializeQuickActions();
    });

    function initializeCharts() {
        // This is a placeholder - you would initialize actual charts here
        console.log('Initializing charts...');

        // Example with Chart.js:
        // const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        // new Chart(revenueCtx, {
        //     type: 'line',
        //     data: {
        //         labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        //         datasets: [{
        //             label: 'Revenue',
        //             data: [12000, 19000, 15000, 25000, 22000, 30000],
        //             borderColor: '#0d6efd',
        //             backgroundColor: 'rgba(13, 110, 253, 0.1)',
        //             tension: 0.4
        //         }]
        //     },
        //     options: {
        //         responsive: true,
        //         maintainAspectRatio: false
        //     }
        // });
    }

    function initializeTableSorting() {
        const table = document.querySelector('.dashboard-table table');
        if (!table) return;

        const headers = table.querySelectorAll('th[data-sort]');
        headers.forEach(function(header) {
            header.style.cursor = 'pointer';
            header.addEventListener('click', function() {
                sortTable(table, this.dataset.sort);
            });
        });
    }

    function sortTable(table, column) {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const isAscending = table.dataset.sortDirection !== 'asc';

        rows.sort(function(a, b) {
            const aValue = a.querySelector(`td[data-sort="${column}"]`).textContent.trim();
            const bValue = b.querySelector(`td[data-sort="${column}"]`).textContent.trim();

            if (isAscending) {
                return aValue.localeCompare(bValue);
            } else {
                return bValue.localeCompare(aValue);
            }
        });

        rows.forEach(function(row) {
            tbody.appendChild(row);
        });

        table.dataset.sortDirection = isAscending ? 'asc' : 'desc';
    }

    function initializeQuickActions() {
        const quickActionButtons = document.querySelectorAll('.quick-actions .btn');
        quickActionButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                // Add loading state
                const originalContent = this.innerHTML;
                this.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Loading...';
                this.disabled = true;

                // Simulate loading
                setTimeout(function() {
                    button.innerHTML = originalContent;
                    button.disabled = false;
                }, 1000);
            });
        });
    }
</script>