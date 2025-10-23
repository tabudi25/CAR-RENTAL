<?= $this->extend('staff/layout') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-book me-2 text-primary"></i>
        Booking Details
    </h2>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-calendar-check fa-2x text-primary"></i>
                </div>
                <div>
                    <h3 class="mb-0"><?= $totalBookings ?></h3>
                    <p class="text-muted mb-0">Total Bookings</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-clock fa-2x text-warning"></i>
                </div>
                <div>
                    <h3 class="mb-0"><?= $pendingBookings ?></h3>
                    <p class="text-muted mb-0">Pending</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-check-circle fa-2x text-success"></i>
                </div>
                <div>
                    <h3 class="mb-0"><?= $confirmedBookings ?></h3>
                    <p class="text-muted mb-0">Confirmed</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-flag-checkered fa-2x text-info"></i>
                </div>
                <div>
                    <h3 class="mb-0"><?= $completedBookings ?></h3>
                    <p class="text-muted mb-0">Completed</p>
                </div>
            </div>
        </div>
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
                    <th>Customer Details</th>
                    <th>Car Details</th>
                    <th>Booking Period</th>
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
                                <br>
                                <small class="text-muted"><?= $booking['customer_phone'] ?? '' ?></small>
                            </div>
                        </td>
                        <td>
                            <div>
                                <div class="fw-bold"><?= $booking['car_name'] ?? 'N/A' ?></div>
                                <small class="text-muted"><?= $booking['brand'] ?? '' ?> <?= $booking['model'] ?? '' ?> (<?= $booking['year'] ?? '' ?>)</small>
                                <br>
                                <small class="text-muted">Plate: <?= $booking['plate'] ?? '' ?></small>
                            </div>
                        </td>
                        <td>
                            <div>
                                <div class="fw-bold"><?= date('M d, Y', strtotime($booking['start_date'])) ?></div>
                                <small class="text-muted">to</small>
                                <div class="fw-bold"><?= date('M d, Y', strtotime($booking['end_date'])) ?></div>
                                <?php 
                                $start = new \DateTime($booking['start_date']);
                                $end = new \DateTime($booking['end_date']);
                                $days = $end->diff($start)->days + 1;
                                ?>
                                <small class="text-info"><?= $days ?> day(s)</small>
                            </div>
                        </td>
                        <td>
                            <span class="fw-bold text-primary">₱<?= number_format($booking['price_per_day'], 2) ?></span>
                            <div class="small text-muted">per day</div>
                        </td>
                        <td>
                            <span class="fw-bold">₱<?= number_format($booking['total_price'] ?? 0, 2) ?></span>
                        </td>
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
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#">
                                        <i class="fas fa-eye text-info me-2"></i>View Details
                                    </a></li>
                                    <li><a class="dropdown-item" href="#">
                                        <i class="fas fa-print text-secondary me-2"></i>Print Receipt
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

<?= $this->endSection() ?>
