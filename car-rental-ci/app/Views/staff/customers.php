<?= $this->extend('staff/layout') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-users me-2 text-primary"></i>
        Manage Customers
    </h2>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" onclick="exportCustomers()">
            <i class="fas fa-download me-2"></i>
            Export
        </button>
        <button class="btn btn-primary" onclick="refreshCustomers()">
            <i class="fas fa-sync me-2"></i>
            Refresh
        </button>
    </div>
</div>

<?php if (session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= session('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->has('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= session('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="stat-card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Customer ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Registration Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($customers) && !empty($customers)): ?>
                    <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td>#<?= $customer['id'] ?></td>
                        <td>
                            <div class="fw-bold"><?= $customer['name'] ?? 'N/A' ?></div>
                        </td>
                        <td><?= $customer['email'] ?? 'N/A' ?></td>
                        <td><?= $customer['phone'] ?? 'N/A' ?></td>
                        <td><?= date('M d, Y', strtotime($customer['created_at'])) ?></td>
                        <td>
                            <span class="badge bg-success">Active</span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-cog"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">
                                        <i class="fas fa-eye text-info me-2"></i>View Details
                                    </a></li>
                                    <li><a class="dropdown-item" href="#">
                                        <i class="fas fa-calendar text-primary me-2"></i>View Bookings
                                    </a></li>
                                    <li><a class="dropdown-item" href="#">
                                        <i class="fas fa-envelope text-success me-2"></i>Send Email
                                    </a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Customers Found</h5>
                            <p class="text-muted">Customers will appear here when they register.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function exportCustomers() {
    // Export functionality
    alert('Export functionality will be implemented');
}

function refreshCustomers() {
    location.reload();
}
</script>
<?= $this->endSection() ?>

