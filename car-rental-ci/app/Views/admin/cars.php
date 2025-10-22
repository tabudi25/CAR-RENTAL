<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-car me-2 text-primary"></i>
        Manage Cars
    </h2>
    <a href="/admin/add-car" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>
        Add New Car
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
                                    <li><a class="dropdown-item" href="/admin/change-status/<?= $car['id'] ?>/available">
                                        <i class="fas fa-check-circle text-success me-2"></i>Available
                                    </a></li>
                                    <li><a class="dropdown-item" href="/admin/change-status/<?= $car['id'] ?>/rented">
                                        <i class="fas fa-car text-danger me-2"></i>Rented
                                    </a></li>
                                    <li><a class="dropdown-item" href="/admin/change-status/<?= $car['id'] ?>/maintenance">
                                        <i class="fas fa-wrench text-warning me-2"></i>Maintenance
                                    </a></li>
                                    <li><a class="dropdown-item" href="/admin/change-status/<?= $car['id'] ?>/reserved">
                                        <i class="fas fa-clock text-info me-2"></i>Reserved
                                    </a></li>
                                </ul>
                            </div>
                            <span class="fw-bold text-primary">₱<?= number_format($car['price_per_day'], 2) ?>/day</span>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="/admin/edit-car/<?= $car['id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit me-1"></i>
                                Edit
                            </a>
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
                <p class="text-muted">Start by adding your first car to the fleet.</p>
                <a href="/admin/add-car" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Add Your First Car
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
