<?= $this->extend('staff/layout') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-car me-2 text-primary"></i>
        Manage Cars
    </h2>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" onclick="refreshCars()">
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

<div class="row">
    <?php if (isset($cars) && !empty($cars)): ?>
        <?php foreach ($cars as $car): ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="stat-card">
                <div class="row">
                    <div class="col-md-4">
                        <?php if (!empty($car['image_url'])): ?>
                            <img src="<?= base_url($car['image_url']) ?>" class="img-fluid rounded" alt="<?= $car['name'] ?>" style="height: 120px; object-fit: cover; width: 100%;">
                        <?php else: ?>
                            <img src="https://images.unsplash.com/photo-1549317336-206569e8475c?w=800&h=600&fit=crop" class="img-fluid rounded" alt="Default Car Image" style="height: 120px; object-fit: cover; width: 100%;">
                        <?php endif; ?>
                    </div>
                    <div class="col-md-8">
                        <h5 class="mb-1"><?= $car['name'] ?></h5>
                        <p class="text-muted mb-2"><?= $car['brand'] ?> <?= $car['model'] ?> (<?= $car['year'] ?>)</p>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-<?= $car['status'] == 'available' ? 'success' : ($car['status'] == 'rented' ? 'danger' : ($car['status'] == 'maintenance' ? 'warning' : 'info')) ?> dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <?= ucfirst($car['status']) ?>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <form action="/staff/update-car/<?= $car['id'] ?>" method="post" class="d-inline">
                                            <input type="hidden" name="status" value="available">
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-check-circle text-success me-2"></i>Available
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="/staff/update-car/<?= $car['id'] ?>" method="post" class="d-inline">
                                            <input type="hidden" name="status" value="rented">
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-car text-danger me-2"></i>Rented
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="/staff/update-car/<?= $car['id'] ?>" method="post" class="d-inline">
                                            <input type="hidden" name="status" value="maintenance">
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-wrench text-warning me-2"></i>Maintenance
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="/staff/update-car/<?= $car['id'] ?>" method="post" class="d-inline">
                                            <input type="hidden" name="status" value="reserved">
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-clock text-info me-2"></i>Reserved
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold text-primary">₱<?= number_format($car['price_per_day'], 2) ?>/day</span>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge bg-<?= $car['status'] == 'available' ? 'success' : ($car['status'] == 'rented' ? 'danger' : ($car['status'] == 'maintenance' ? 'warning' : 'info')) ?>">
                                <?= ucfirst($car['status']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="stat-card text-center py-5">
                <i class="fas fa-car fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No Cars Found</h4>
                <p class="text-muted">No cars are available in the system.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function refreshCars() {
    location.reload();
}
</script>
<?= $this->endSection() ?>

