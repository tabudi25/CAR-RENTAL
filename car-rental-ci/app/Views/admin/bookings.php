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
                    <th>Car Price/Day</th>
                    <th>Total Price</th>
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
                        <td>
                            <span class="fw-bold text-primary">₱<?= number_format($booking['price_per_day'], 2) ?></span>
                            <div class="small text-muted">per day</div>
                        </td>
                        <td>
                            <span class="fw-bold">₱<?= number_format($booking['total_price'] ?? 0, 2) ?></span>
                        </td>
                        <td>
                            <?php
                            $statusBadgeClass = 'secondary';
                            switch($booking['status']) {
                                case 'pending':
                                    $statusBadgeClass = 'warning';
                                    break;
                                case 'confirmed':
                                    $statusBadgeClass = 'success';
                                    break;
                                case 'cancelled':
                                    $statusBadgeClass = 'danger';
                                    break;
                                case 'completed':
                                    $statusBadgeClass = 'info';
                                    break;
                                case 'return_requested':
                                    $statusBadgeClass = 'info';
                                    break;
                                case 'returned':
                                    $statusBadgeClass = 'success';
                                    break;
                            }
                            ?>
                            <span class="badge bg-<?= $statusBadgeClass ?>">
                                <?= ucfirst(str_replace('_', ' ', $booking['status'])) ?>
                            </span>
                            <?php if (!empty($booking['return_requested_at'])): ?>
                                <div class="mt-1">
                                    <small class="text-muted d-block">
                                        <?= date('M d, Y g:i A', strtotime($booking['return_requested_at'])) ?>
                                    </small>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $totalAmount = $booking['total_price'] ?? $booking['total_amount'] ?? 0;
                            $downPaymentAmount = isset($booking['down_payment_amount']) && $booking['down_payment_amount'] !== null ? (float)$booking['down_payment_amount'] : 0;
                            $paymentStatus = $booking['payment_status'] ?? 'pending';
                            $hasPaymentReference = !empty($booking['payment_reference']) && !empty($booking['payment_method']);
                            
                            // Calculate amount paid: if fully paid, use total; otherwise use down payment amount
                            if ($paymentStatus === 'paid') {
                                $amountPaid = $totalAmount;
                            } else {
                                // If down_payment_amount exists and is > 0, use it
                                if ($downPaymentAmount > 0) {
                                    $amountPaid = $downPaymentAmount;
                                } 
                                // If payment reference exists but no down_payment_amount recorded, assume 50% was paid
                                elseif ($hasPaymentReference && $totalAmount > 0) {
                                    $amountPaid = $totalAmount * 0.5;
                                } else {
                                    $amountPaid = 0;
                                }
                            }
                            
                            $balance = $totalAmount - $amountPaid;
                            $paymentPercentage = $totalAmount > 0 ? ($amountPaid / $totalAmount) * 100 : 0;
                            ?>
                            <div class="payment-summary">
                                <div class="mb-1">
                                    <strong>Amount Paid:</strong> 
                                    <span class="text-primary">₱<?= number_format($amountPaid, 2) ?></span>
                                    <small class="text-muted">(<?= number_format($paymentPercentage, 0) ?>%)</small>
                                </div>
                                <?php if ($balance > 0): ?>
                                    <div class="mb-1">
                                        <strong>Balance:</strong> 
                                        <span class="text-danger">₱<?= number_format($balance, 2) ?></span>
                                        <small class="text-muted">(<?= number_format(100 - $paymentPercentage, 0) ?>%)</small>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <span class="badge bg-<?= $paymentStatus == 'paid' ? 'success' : ($downPaymentAmount > 0 ? 'warning' : 'secondary') ?>">
                                        <?php if ($paymentStatus == 'paid'): ?>
                                            Paid in Full
                                        <?php elseif ($downPaymentAmount > 0): ?>
                                            Down Payment Only
                                        <?php else: ?>
                                            Pending Payment
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <?php if ($booking['status'] === 'return_requested'): ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a href="/admin/mark-returned/<?= $booking['id'] ?>" class="dropdown-item text-success" onclick="return confirm('Mark as returned? This will update the status to returned and make the car available.')">
                                                <i class="fas fa-check-circle me-2"></i><strong>Mark as Returned</strong>
                                            </a>
                                        </li>
                                    <?php endif; ?>
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
                        <td colspan="10" class="text-center py-4">
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
