<?= $this->extend('staff/layout') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-sign-in-alt me-2 text-success"></i>
        Customer Check-in
    </h2>
    <a href="/staff" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>
        Back to Dashboard
    </a>
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

<div class="row">
    <div class="col-md-8">
        <div class="stat-card">
            <h5 class="mb-3">
                <i class="fas fa-search me-2"></i>
                Find Booking
            </h5>
            
            <form action="/staff/checkin" method="post" class="mb-4">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="booking_id" class="form-label">Booking ID</label>
                        <input type="text" class="form-control" id="booking_id" name="booking_id" placeholder="Enter booking ID">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="customer_phone" class="form-label">Customer Phone</label>
                        <input type="text" class="form-control" id="customer_phone" name="customer_phone" placeholder="Enter customer phone">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>
                    Find Booking
                </button>
            </form>
            
            <?php if (isset($booking) && $booking): ?>
            <div class="border rounded p-3 bg-light">
                <h6 class="mb-3">Booking Details</h6>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Booking ID:</strong> #<?= $booking['id'] ?></p>
                        <p><strong>Customer:</strong> <?= $booking['customer_name'] ?></p>
                        <p><strong>Car:</strong> <?= $booking['car_name'] ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Start Date:</strong> <?= date('M d, Y', strtotime($booking['start_date'])) ?></p>
                        <p><strong>End Date:</strong> <?= date('M d, Y', strtotime($booking['end_date'])) ?></p>
                        <p><strong>Total:</strong> ₱<?= number_format($booking['total_price'], 2) ?></p>
                    </div>
                </div>
                
                <div class="mt-3">
                    <form action="/staff/process-checkin" method="post">
                        <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="mileage_start" class="form-label">Starting Mileage</label>
                                <input type="number" class="form-control" id="mileage_start" name="mileage_start" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fuel_level" class="form-label">Fuel Level</label>
                                <select class="form-select" id="fuel_level" name="fuel_level" required>
                                    <option value="">Select fuel level</option>
                                    <option value="full">Full</option>
                                    <option value="3/4">3/4</option>
                                    <option value="1/2">1/2</option>
                                    <option value="1/4">1/4</option>
                                    <option value="empty">Empty</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Check-in Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Any damage, issues, or notes..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-2"></i>
                            Complete Check-in
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="stat-card">
            <h5 class="mb-3">
                <i class="fas fa-info-circle me-2"></i>
                Check-in Process
            </h5>
            <div class="mb-3">
                <strong>1. Find Booking</strong>
                <p class="text-muted small">Search by booking ID or customer phone number</p>
            </div>
            <div class="mb-3">
                <strong>2. Verify Details</strong>
                <p class="text-muted small">Confirm customer and car information</p>
            </div>
            <div class="mb-3">
                <strong>3. Record Condition</strong>
                <p class="text-muted small">Note starting mileage and fuel level</p>
            </div>
            <div class="mb-3">
                <strong>4. Complete Check-in</strong>
                <p class="text-muted small">Update booking status and car availability</p>
            </div>
        </div>
        
        <div class="stat-card mt-3">
            <h6 class="mb-3">
                <i class="fas fa-clock me-2"></i>
                Today's Check-ins
            </h6>
            <div class="text-center">
                <div class="h3 text-success"><?= $todayCheckins ?? 0 ?></div>
                <small class="text-muted">Completed today</small>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
