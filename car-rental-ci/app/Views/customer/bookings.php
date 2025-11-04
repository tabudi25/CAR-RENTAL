<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Car Rental System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/customer">Car Rental System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/customer">Available Cars</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/customer/bookings">My Bookings</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="nav-link">Welcome, <?= session()->get('name') ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/auth/logout">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h1 class="mb-4">My Bookings</h1>
        
        <?php if (session()->has('success')): ?>
            <div class="alert alert-success">
                <?= session('success') ?>
            </div>
        <?php endif; ?>
        
        <?php if (session()->has('error')): ?>
            <div class="alert alert-danger">
                <?= session('error') ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($bookings)): ?>
            <div class="alert alert-info">You have no bookings yet.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Booking ID</th>
                            <th>Car</th>
                            <th>From Date</th>
                            <th>Pick-up Time</th>
                            <th>To Date</th>
                            <th>Car Price/Day</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?= $booking['id'] ?></td>
                                <td><?= $booking['car_name'] ?></td>
                                <td><?= date('M d, Y', strtotime($booking['start_date'])) ?></td>
                                <td>
                                    <?php if (!empty($booking['pick_up_time'])): ?>
                                        <?= date('g:i A', strtotime($booking['pick_up_time'])) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Not set</span>
                                    <?php endif; ?>
                                    <div class="small text-info mt-1">
                                        <i class="fas fa-clock"></i> Pick-up hours: 8:00 AM - 5:00 PM
                                    </div>
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
                                    $badgeClass = '';
                                    switch ($booking['status']) {
                                        case 'pending':
                                            $badgeClass = 'warning';
                                            break;
                                        case 'confirmed':
                                            $badgeClass = 'success';
                                            break;
                                        case 'cancelled':
                                            $badgeClass = 'danger';
                                            break;
                                        case 'completed':
                                            $badgeClass = 'info';
                                            break;
                                        case 'return_requested':
                                            $badgeClass = 'info';
                                            break;
                                        case 'returned':
                                            $badgeClass = 'success';
                                            break;
                                        default:
                                            $badgeClass = 'secondary';
                                    }
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?>">
                                        <?= ucfirst($booking['status']) ?>
                                    </span>
                                    <div class="mt-1">
                                        <?php
                                        $paymentStatus = $booking['payment_status'] ?? 'pending';
                                        $totalAmount = $booking['total_price'] ?? 0;
                                        $downPaymentAmount = $booking['down_payment_amount'] ?? 0;
                                        
                                        // Determine payment display status
                                        if ($paymentStatus === 'paid') {
                                            $paymentDisplay = 'Paid';
                                            $paymentBadgeClass = 'success';
                                        } elseif ($downPaymentAmount > 0 && $downPaymentAmount < $totalAmount) {
                                            $paymentDisplay = 'Pending (50% Paid)';
                                            $paymentBadgeClass = 'warning';
                                        } else {
                                            $paymentDisplay = 'Pending';
                                            $paymentBadgeClass = 'secondary';
                                        }
                                        ?>
                                        <span class="badge bg-<?= $paymentBadgeClass ?>">
                                            Payment: <?= $paymentDisplay ?>
                                        </span>
                                    </div>
                                    <?php if (!empty($booking['payment_method'])): ?>
                                        <div class="small text-muted mt-1">
                                            <i class="fas fa-credit-card"></i> <?= ucfirst(str_replace('_', ' ', $booking['payment_method'])) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <?php
                                        $paymentStatus = $booking['payment_status'] ?? 'pending';
                                        $totalAmount = $booking['total_price'] ?? 0;
                                        $downPaymentAmount = $booking['down_payment_amount'] ?? 0;
                                        ?>
                                        
                                        <?php if ($paymentStatus === 'paid'): ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Paid in Full
                                            </span>
                                        <?php elseif ($downPaymentAmount > 0 && $downPaymentAmount < $totalAmount): ?>
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock me-1"></i>Down Payment Only
                                            </span>
                                            <?php 
                                            // Enable Pay Full Amount button if booking is confirmed or ready for pick-up
                                            $canPayFull = ($booking['status'] === 'confirmed') || !empty($booking['ready_for_pickup_notification']);
                                            ?>
                                            <?php if ($canPayFull): ?>
                                                <a href="/payment/<?= $booking['id'] ?>?type=full" class="btn btn-sm btn-success">
                                                    <i class="fas fa-credit-card me-1"></i>Pay Full Amount
                                                </a>
                                            <?php else: ?>
                                                <small class="text-muted d-block">Waiting for confirmation</small>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        
                                        <?php if ($booking['status'] === 'pending' || $booking['status'] === 'confirmed'): ?>
                                            <a href="/customer/cancel-booking/<?= $booking['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to cancel this booking?')">
                                                <i class="fas fa-times me-1"></i>Cancel
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-secondary" disabled>Cancel</button>
                                        <?php endif; ?>
                                        
                                        <?php 
                                        // Show return button only when payment is fully paid, booking is confirmed, and due date has passed
                                        $isFullyPaid = ($paymentStatus === 'paid');
                                        // Fallback: also check if down payment amount equals or exceeds total amount
                                        $isFullyPaidFallback = ($downPaymentAmount > 0 && $totalAmount > 0 && $downPaymentAmount >= $totalAmount);
                                        $isFullyPaid = $isFullyPaid || $isFullyPaidFallback;
                                        
                                        // Check if booking status allows return (must be confirmed and not already returned)
                                        $statusAllowsReturn = ($booking['status'] === 'confirmed');
                                        
                                        // Check if due date has passed (end_date is today or in the past)
                                        $endDate = !empty($booking['end_date']) ? strtotime($booking['end_date']) : 0;
                                        $today = strtotime(date('Y-m-d'));
                                        $dueDatePassed = ($endDate > 0 && $today >= $endDate);
                                        
                                        $canReturn = ($isFullyPaid && $statusAllowsReturn && $dueDatePassed);
                                        ?>
                                        
                                        <?php if ($canReturn): ?>
                                            <a href="/customer/request-return/<?= $booking['id'] ?>" class="btn btn-sm btn-primary" onclick="return confirm('Are you sure you want to return this car? This will notify staff and admin.')">
                                                <i class="fas fa-undo me-1"></i>Return
                                            </a>
                                        <?php elseif ($booking['status'] === 'return_requested'): ?>
                                            <span class="badge bg-info">
                                                <i class="fas fa-clock me-1"></i>Return Requested
                                            </span>
                                            <small class="text-muted d-block mt-1">
                                                Waiting for car arrival at office
                                            </small>
                                        <?php elseif ($booking['status'] === 'returned'): ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Returned
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; <?= date('Y') ?> Car Rental System. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
