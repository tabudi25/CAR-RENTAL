<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-chart-bar me-2 text-primary"></i>
        Reports & Analytics
    </h2>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" onclick="window.print()">
            <i class="fas fa-print me-2"></i>
            Print
        </button>
        <button class="btn btn-primary" onclick="exportReport()">
            <i class="fas fa-download me-2"></i>
            Export
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

<!-- Filters -->
<div class="stat-card mb-4">
    <h5 class="mb-3">Filter Reports</h5>
    <form method="get" action="/admin/reports" class="row g-3">
        <div class="col-md-3">
            <label for="start_date" class="form-label">Start Date</label>
            <input type="date" class="form-control" id="start_date" name="start_date" value="<?= $filters['start_date'] ?>">
        </div>
        <div class="col-md-3">
            <label for="end_date" class="form-label">End Date</label>
            <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $filters['end_date'] ?>">
        </div>
        <div class="col-md-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
                <option value="">All Statuses</option>
                <option value="pending" <?= $filters['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="confirmed" <?= $filters['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                <option value="completed" <?= $filters['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                <option value="returned" <?= $filters['status'] === 'returned' ? 'selected' : '' ?>>Returned</option>
                <option value="cancelled" <?= $filters['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="car_id" class="form-label">Car</label>
            <select class="form-select" id="car_id" name="car_id">
                <option value="">All Cars</option>
                <?php foreach ($allCars as $car): ?>
                    <option value="<?= $car['id'] ?>" <?= $filters['car_id'] == $car['id'] ? 'selected' : '' ?>>
                        <?= $car['name'] ?> (<?= $car['plate'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter me-2"></i>Apply Filters
            </button>
            <a href="/admin/reports" class="btn btn-outline-secondary">
                <i class="fas fa-times me-2"></i>Clear
            </a>
        </div>
    </form>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card text-center">
            <h6 class="text-muted mb-2">Total Bookings</h6>
            <h3 class="mb-0"><?= number_format($totalBookings) ?></h3>
            <small class="text-muted">All time</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <h6 class="text-muted mb-2">Total Revenue</h6>
            <h3 class="mb-0 text-success">₱<?= number_format($totalRevenue, 2) ?></h3>
            <small class="text-muted">In selected period</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <h6 class="text-muted mb-2">Paid Revenue</h6>
            <h3 class="mb-0 text-primary">₱<?= number_format($paidRevenue, 2) ?></h3>
            <small class="text-muted">Received</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <h6 class="text-muted mb-2">Pending Revenue</h6>
            <h3 class="mb-0 text-warning">₱<?= number_format($pendingRevenue, 2) ?></h3>
            <small class="text-muted">Outstanding</small>
        </div>
    </div>
</div>

<!-- Status Statistics -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="stat-card">
            <h5 class="mb-3">Booking Status Statistics</h5>
            <div class="row">
                <?php foreach ($statusStats as $stat => $count): ?>
                    <div class="col-md-2 mb-3">
                        <div class="text-center p-3 border rounded">
                            <div class="h4 mb-1"><?= $count ?></div>
                            <small class="text-muted text-capitalize"><?= str_replace('_', ' ', $stat) ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Revenue Chart -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="stat-card">
            <h5 class="mb-3">Monthly Revenue (Last 6 Months)</h5>
            <canvas id="revenueChart" height="80"></canvas>
        </div>
    </div>
</div>

<!-- Top Cars -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="stat-card">
            <h5 class="mb-3">Top Cars by Bookings</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Car</th>
                            <th>Plate</th>
                            <th>Bookings</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($topCars)): ?>
                            <?php foreach ($topCars as $index => $car): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= $car['name'] ?></td>
                                    <td><?= $car['plate'] ?></td>
                                    <td><?= $car['booking_count'] ?></td>
                                    <td>₱<?= number_format($car['total_revenue'] ?? 0, 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">No data available</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Bookings List -->
<div class="stat-card">
    <h5 class="mb-3">Bookings Report</h5>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Customer</th>
                    <th>Car</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($bookings)): ?>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td>#<?= $booking['id'] ?></td>
                            <td>
                                <div><?= $booking['customer_name'] ?></div>
                                <small class="text-muted"><?= $booking['customer_email'] ?></small>
                            </td>
                            <td>
                                <div><?= $booking['car_name'] ?></div>
                                <small class="text-muted"><?= $booking['car_plate'] ?></small>
                            </td>
                            <td><?= date('M d, Y', strtotime($booking['start_date'])) ?></td>
                            <td><?= date('M d, Y', strtotime($booking['end_date'])) ?></td>
                            <td>₱<?= number_format($booking['total_price'] ?? 0, 2) ?></td>
                            <td>
                                <span class="badge bg-<?= $booking['status'] == 'pending' ? 'warning' : ($booking['status'] == 'confirmed' ? 'success' : ($booking['status'] == 'cancelled' ? 'danger' : 'info')) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $booking['status'])) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= ($booking['payment_status'] ?? 'pending') === 'paid' ? 'success' : 'warning' ?>">
                                    <?= ucfirst($booking['payment_status'] ?? 'pending') ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y', strtotime($booking['created_at'] ?? 'now')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-chart-line fa-3x mb-3"></i>
                            <h5>No bookings found</h5>
                            <p>Try adjusting your filters</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Chart
const ctx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($monthlyLabels) ?>,
        datasets: [{
            label: 'Revenue (₱)',
            data: <?= json_encode($monthlyRevenue) ?>,
            borderColor: 'rgb(37, 99, 235)',
            backgroundColor: 'rgba(37, 99, 235, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '₱' + value.toLocaleString();
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Revenue: ₱' + context.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    }
                }
            }
        }
    }
});

function exportReport() {
    alert('Export functionality will be implemented');
}
</script>
<?= $this->endSection() ?>

