<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-calendar-check me-2 text-primary"></i>
        Manage Bookings
    </h2>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" onclick="exportBookings()">
            <i class="fas fa-download me-2"></i>
            Export
        </button>
        <button class="btn btn-primary" onclick="refreshBookings()">
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
                    <th>Booking ID</th>
                    <th>Customer</th>
                    <th>Car</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($bookings) && !empty($bookings)): ?>
                    <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td>#<?= $booking['id'] ?></td>
                        <td>
                            <div>
                                <div class="fw-bold"><?= $booking['customer_name'] ?? 'N/A' ?></div>
                                <small class="text-muted"><?= $booking['customer_email'] ?? '' ?></small>
                            </div>
                        </td>
                        <td>
                            <div>
                                <div class="fw-bold"><?= $booking['car_name'] ?? 'N/A' ?></div>
                                <small class="text-muted"><?= $booking['car_plate'] ?? '' ?></small>
                            </div>
                        </td>
                        <td><?= date('M d, Y', strtotime($booking['start_date'])) ?></td>
                        <td><?= date('M d, Y', strtotime($booking['end_date'])) ?></td>
                        <td class="fw-bold">₱<?= number_format($booking['total_price'], 2) ?></td>
                        <td>
                            <span class="badge bg-<?= $booking['status'] == 'pending' ? 'warning' : ($booking['status'] == 'confirmed' ? 'success' : ($booking['status'] == 'cancelled' ? 'danger' : 'info')) ?>">
                                <?= ucfirst($booking['status']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?= $booking['payment_status'] == 'paid' ? 'success' : 'warning' ?>">
                                <?= ucfirst($booking['payment_status']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/admin/view-booking/<?= $booking['id'] ?>">
                                        <i class="fas fa-eye me-2"></i>View Details
                                    </a></li>
                                    <li><a class="dropdown-item" href="/admin/edit-booking/<?= $booking['id'] ?>">
                                        <i class="fas fa-edit me-2"></i>Edit
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="/admin/delete-booking/<?= $booking['id'] ?>" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash me-2"></i>Delete
                                    </a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Bookings Found</h5>
                            <p class="text-muted">Bookings will appear here when customers make reservations.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function exportBookings() {
    // Implement export functionality
    alert('Export functionality will be implemented');
}

function refreshBookings() {
    location.reload();
}
</script>
<?= $this->endSection() ?>
