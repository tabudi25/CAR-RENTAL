<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-edit me-2 text-primary"></i>
        Edit Car
    </h2>
    <a href="/admin/cars" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>
        Back to Cars
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
            <form action="/admin/edit-car/<?= $car['id'] ?>" method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Car Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= $car['name'] ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="plate" class="form-label">License Plate</label>
                        <input type="text" class="form-control" id="plate" name="plate" value="<?= $car['plate'] ?>" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label">Car Type</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="">Select Type</option>
                            <option value="sedan" <?= $car['type'] == 'sedan' ? 'selected' : '' ?>>Sedan</option>
                            <option value="suv" <?= $car['type'] == 'suv' ? 'selected' : '' ?>>SUV</option>
                            <option value="hatchback" <?= $car['type'] == 'hatchback' ? 'selected' : '' ?>>Hatchback</option>
                            <option value="coupe" <?= $car['type'] == 'coupe' ? 'selected' : '' ?>>Coupe</option>
                            <option value="convertible" <?= $car['type'] == 'convertible' ? 'selected' : '' ?>>Convertible</option>
                            <option value="truck" <?= $car['type'] == 'truck' ? 'selected' : '' ?>>Truck</option>
                            <option value="van" <?= $car['type'] == 'van' ? 'selected' : '' ?>>Van</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="economy" <?= $car['category'] == 'economy' ? 'selected' : '' ?>>Economy</option>
                            <option value="compact" <?= $car['category'] == 'compact' ? 'selected' : '' ?>>Compact</option>
                            <option value="intermediate" <?= $car['category'] == 'intermediate' ? 'selected' : '' ?>>Intermediate</option>
                            <option value="standard" <?= $car['category'] == 'standard' ? 'selected' : '' ?>>Standard</option>
                            <option value="fullsize" <?= $car['category'] == 'fullsize' ? 'selected' : '' ?>>Full Size</option>
                            <option value="premium" <?= $car['category'] == 'premium' ? 'selected' : '' ?>>Premium</option>
                            <option value="luxury" <?= $car['category'] == 'luxury' ? 'selected' : '' ?>>Luxury</option>
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="seats" class="form-label">Number of Seats</label>
                        <input type="number" class="form-control" id="seats" name="seats" value="<?= $car['seats'] ?>" min="1" max="8" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label">Price per Day (₱)</label>
                        <input type="number" class="form-control" id="price" name="price" value="<?= $car['price_per_day'] ?>" step="0.01" min="0" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Car Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="available" <?= $car['status'] == 'available' ? 'selected' : '' ?>>Available</option>
                            <option value="rented" <?= $car['status'] == 'rented' ? 'selected' : '' ?>>Rented</option>
                            <option value="maintenance" <?= $car['status'] == 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                            <option value="reserved" <?= $car['status'] == 'reserved' ? 'selected' : '' ?>>Reserved</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="year" class="form-label">Year</label>
                        <input type="number" class="form-control" id="year" name="year" value="<?= $car['year'] ?? date('Y') ?>" min="1900" max="<?= date('Y') + 1 ?>" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="image" class="form-label">Car Image</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    <div class="form-text">Upload a new image to replace the current one (JPG, PNG, GIF)</div>
                    <?php if (!empty($car['image_url'])): ?>
                        <div class="mt-2">
                            <strong>Current Image:</strong><br>
                            <img src="<?= base_url($car['image_url']) ?>" alt="<?= $car['name'] ?>" class="img-thumbnail mt-2" style="max-width: 200px;">
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        Update Car
                    </button>
                    <a href="/admin/cars" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="stat-card">
            <h5 class="mb-3">
                <i class="fas fa-info-circle me-2"></i>
                Car Information
            </h5>
            <div class="mb-3">
                <strong>Car ID:</strong>
                <p class="text-muted">#<?= $car['id'] ?></p>
            </div>
            <div class="mb-3">
                <strong>Current Status:</strong>
                <p class="text-muted">
                    <span class="badge bg-<?= $car['status'] == 'available' ? 'success' : ($car['status'] == 'rented' ? 'danger' : 'warning') ?>">
                        <?= ucfirst($car['status']) ?>
                    </span>
                </p>
            </div>
            <div class="mb-3">
                <strong>Brand:</strong>
                <p class="text-muted"><?= $car['brand'] ?? 'N/A' ?></p>
            </div>
            <div class="mb-3">
                <strong>Model:</strong>
                <p class="text-muted"><?= $car['model'] ?? 'N/A' ?></p>
            </div>
            <div class="mb-3">
                <strong>Year:</strong>
                <p class="text-muted"><?= $car['year'] ?? 'N/A' ?></p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
