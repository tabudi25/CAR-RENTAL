<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-plus me-2 text-primary"></i>
        Add New Car
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
            <form action="/admin/add-car" method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Car Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="plate" class="form-label">License Plate</label>
                        <input type="text" class="form-control" id="plate" name="plate" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label">Car Type</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="">Select Type</option>
                            <option value="sedan">Sedan</option>
                            <option value="suv">SUV</option>
                            <option value="hatchback">Hatchback</option>
                            <option value="coupe">Coupe</option>
                            <option value="convertible">Convertible</option>
                            <option value="truck">Truck</option>
                            <option value="van">Van</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="economy">Economy</option>
                            <option value="compact">Compact</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="standard">Standard</option>
                            <option value="fullsize">Full Size</option>
                            <option value="premium">Premium</option>
                            <option value="luxury">Luxury</option>
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="seats" class="form-label">Number of Seats</label>
                        <input type="number" class="form-control" id="seats" name="seats" min="1" max="8" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label">Price per Day (₱)</label>
                        <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="image" class="form-label">Car Image</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    <div class="form-text">Upload a clear image of the car (JPG, PNG, GIF)</div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        Add Car
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
                <strong>Car Name:</strong>
                <p class="text-muted">Enter the full name of the car (e.g., "Toyota Camry 2023")</p>
            </div>
            <div class="mb-3">
                <strong>License Plate:</strong>
                <p class="text-muted">Enter the vehicle's license plate number</p>
            </div>
            <div class="mb-3">
                <strong>Car Type:</strong>
                <p class="text-muted">Select the body type of the vehicle</p>
            </div>
            <div class="mb-3">
                <strong>Category:</strong>
                <p class="text-muted">Choose the rental category for pricing</p>
            </div>
            <div class="mb-3">
                <strong>Seats:</strong>
                <p class="text-muted">Number of passenger seats available</p>
            </div>
            <div class="mb-3">
                <strong>Price:</strong>
                <p class="text-muted">Daily rental rate in USD</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
