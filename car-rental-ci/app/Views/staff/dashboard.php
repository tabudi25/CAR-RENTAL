<?= $this->extend('staff/layout') ?>

<?= $this->section('content') ?>
<!-- Quick Stats -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card p-4">
            <div class="d-flex align-items-center">
                <div class="stat-icon me-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <h3 class="mb-0"><?= $pendingBookings ?? 0 ?></h3>
                    <p class="text-muted mb-0">Pending Bookings</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stat-card p-4">
            <div class="d-flex align-items-center">
                <div class="stat-icon me-3" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <i class="fas fa-car"></i>
                </div>
                <div>
                    <h3 class="mb-0"><?= $availableCars ?? 0 ?></h3>
                    <p class="text-muted mb-0">Available Cars</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stat-card p-4">
            <div class="d-flex align-items-center">
                <div class="stat-icon me-3" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <i class="fas fa-wrench"></i>
                </div>
                <div>
                    <h3 class="mb-0"><?= $maintenanceCars ?? 0 ?></h3>
                    <p class="text-muted mb-0">In Maintenance</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stat-card p-4">
            <div class="d-flex align-items-center">
                <div class="stat-icon me-3" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h3 class="mb-0"><?= $activeCustomers ?? 0 ?></h3>
                    <p class="text-muted mb-0">Active Customers</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Today's Operations -->
    <div class="col-md-8 mb-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-day me-2 text-primary"></i>
                    Today's Operations
                </h5>
                <span class="badge bg-primary"><?= date('M d, Y') ?></span>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <i class="fas fa-sign-in-alt fa-2x text-success mb-2"></i>
                            <h4 class="text-success"><?= $todayCheckins ?? 0 ?></h4>
                            <p class="mb-0">Check-ins Today</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <i class="fas fa-sign-out-alt fa-2x text-warning mb-2"></i>
                            <h4 class="text-warning"><?= $todayCheckouts ?? 0 ?></h4>
                            <p class="mb-0">Check-outs Today</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="col-md-4 mb-4">
        <div class="stat-card">
            <h5 class="mb-3">
                <i class="fas fa-bolt me-2 text-warning"></i>
                Quick Actions
            </h5>
            <div class="d-grid gap-2">
                <a href="/staff/checkin" class="btn btn-success">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    New Check-in
                </a>
                <a href="/staff/checkout" class="btn btn-warning">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    Process Check-out
                </a>
                <a href="/staff/bookings" class="btn btn-primary">
                    <i class="fas fa-calendar-check me-2"></i>
                    Manage Bookings
                </a>
                <a href="/staff/maintenance" class="btn btn-info">
                    <i class="fas fa-wrench me-2"></i>
                    Report Maintenance
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Bookings -->
    <div class="col-md-6 mb-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2 text-info"></i>
                    Recent Bookings
                </h5>
                <a href="/staff/bookings" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            
            <?php if (isset($recentBookings) && !empty($recentBookings)): ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Car</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentBookings as $booking): ?>
                            <tr>
                                <td>#<?= $booking['id'] ?></td>
                                <td><?= $booking['customer_name'] ?? 'N/A' ?></td>
                                <td><?= $booking['car_name'] ?? 'N/A' ?></td>
                                <td>
                                    <span class="badge bg-<?= $booking['status'] == 'pending' ? 'warning' : ($booking['status'] == 'confirmed' ? 'success' : 'secondary') ?>">
                                        <?= ucfirst($booking['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="/staff/bookings" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No recent bookings</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Car Status Overview -->
    <div class="col-md-6 mb-4">
        <div class="stat-card">
            <h5 class="mb-3">
                <i class="fas fa-car me-2 text-primary"></i>
                Car Status Overview
            </h5>
            
            <div class="row">
                <div class="col-6 mb-3">
                    <div class="text-center">
                        <div class="h3 text-success"><?= $availableCars ?? 0 ?></div>
                        <small class="text-muted">Available</small>
                    </div>
                </div>
                <div class="col-6 mb-3">
                    <div class="text-center">
                        <div class="h3 text-danger"><?= $rentedCars ?? 0 ?></div>
                        <small class="text-muted">Rented</small>
                    </div>
                </div>
                <div class="col-6 mb-3">
                    <div class="text-center">
                        <div class="h3 text-warning"><?= $maintenanceCars ?? 0 ?></div>
                        <small class="text-muted">Maintenance</small>
                    </div>
                </div>
                <div class="col-6 mb-3">
                    <div class="text-center">
                        <div class="h3 text-info"><?= $reservedCars ?? 0 ?></div>
                        <small class="text-muted">Reserved</small>
                    </div>
                </div>
            </div>
            
            <div class="mt-3">
                <a href="/staff/cars" class="btn btn-outline-primary btn-sm w-100">
                    <i class="fas fa-car me-2"></i>
                    Manage All Cars
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Alerts & Notifications -->
<?php if (isset($alerts) && !empty($alerts)): ?>
<div class="row">
    <div class="col-12">
        <div class="stat-card">
            <h5 class="mb-3">
                <i class="fas fa-bell me-2 text-warning"></i>
                Alerts & Notifications
            </h5>
            <?php foreach ($alerts as $alert): ?>
            <div class="alert alert-<?= $alert['type'] ?> alert-dismissible fade show">
                <i class="fas fa-<?= $alert['icon'] ?> me-2"></i>
                <?= $alert['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>
<?= $this->endSection() ?>
