<?= $this->extend('staff/layout') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-calendar-check me-2 text-primary"></i>
        Manage Bookings
    </h2>
    <div class="d-flex gap-2">
        <a href="/staff/process-notifications" class="btn btn-success" onclick="return confirm('Process automatic notifications (due date near and overdue)?')">
            <i class="fas fa-bell me-2"></i>
            Process Notifications
        </a>
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
                    <th>Pick-up Time</th>
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
                        <td>
                            <?php if (!empty($booking['pick_up_time'])): ?>
                                <?= date('g:i A', strtotime($booking['pick_up_time'])) ?>
                            <?php else: ?>
                                <span class="text-muted">Not set</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('M d, Y', strtotime($booking['end_date'])) ?></td>
                        <td>
                            <span class="fw-bold text-primary">₱<?= number_format($booking['price_per_day'], 2) ?></span>
                            <div class="small text-muted">per day</div>
                        </td>
                        <td>
                            <span class="fw-bold">₱<?= number_format($booking['total_price'] ?? 0, 2) ?></span>
                            <?php if (!empty($booking['down_payment_amount'])): ?>
                                <div class="small text-muted">Down: ₱<?= number_format($booking['down_payment_amount'], 2) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($booking['overdue_charge']) && $booking['overdue_charge'] > 0): ?>
                                <div class="small text-danger">Overdue: ₱<?= number_format($booking['overdue_charge'], 2) ?></div>
                            <?php endif; ?>
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
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-cog"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <form action="/staff/update-booking/<?= $booking['id'] ?>" method="post" class="d-inline">
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit" class="dropdown-item" onclick="return confirm('Confirm this booking?')">
                                                <i class="fas fa-check text-success me-2"></i>Confirm
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <?php if ($booking['status'] == 'confirmed' && empty($booking['ready_for_pickup_notification'])): ?>
                                            <a href="/staff/send-ready-notification/<?= $booking['id'] ?>" class="dropdown-item" onclick="return confirm('Send car ready for pick-up notification to customer?')">
                                                <i class="fas fa-bell text-primary me-2"></i>Notify: Ready for Pick-up
                                            </a>
                                        <?php elseif (!empty($booking['ready_for_pickup_notification'])): ?>
                                            <span class="dropdown-item text-muted">
                                                <i class="fas fa-check-circle text-success me-2"></i>Notified: <?= date('M d, Y g:i A', strtotime($booking['ready_for_pickup_notification'])) ?>
                                            </span>
                                        <?php endif; ?>
                                    </li>
                                    <?php if ($booking['status'] === 'return_requested'): ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a href="/staff/mark-returned/<?= $booking['id'] ?>" class="dropdown-item text-success" onclick="return confirm('Mark as returned? This will update the status to returned and make the car available.')">
                                                <i class="fas fa-check-circle me-2"></i><strong>Mark as Returned</strong>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="/staff/update-booking/<?= $booking['id'] ?>" method="post" class="d-inline">
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="dropdown-item" onclick="return confirm('Mark as completed?')">
                                                <i class="fas fa-flag-checkered text-info me-2"></i>Complete
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="/staff/update-booking/<?= $booking['id'] ?>" method="post" class="d-inline">
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="dropdown-item" onclick="return confirm('Cancel this booking?')">
                                                <i class="fas fa-times text-danger me-2"></i>Cancel
                                            </button>
                                        </form>
                                    </li>
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
    // Export functionality
    alert('Export functionality will be implemented');
}

function refreshBookings() {
    location.reload();
}
</script>
<?= $this->endSection() ?>

