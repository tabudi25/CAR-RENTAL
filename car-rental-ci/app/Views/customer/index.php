<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - Car Rental System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
                        <a class="nav-link active" href="/customer">Available Cars</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/customer/bookings">My Bookings</a>
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
        <h1 class="mb-4">Available Cars</h1>
        
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
        
        <div class="row">
            <?php if (empty($cars)): ?>
                <div class="col-12">
                    <div class="alert alert-info">No cars available for rent at the moment.</div>
                </div>
            <?php else: ?>
                <?php foreach ($cars as $car): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <?php if (!empty($car['image'])): ?>
                                <img src="<?= base_url($car['image']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?= $car['name'] ?>">
                            <?php else: ?>
                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <span>No Image</span>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= $car['name'] ?></h5>
                                <p class="card-text">
                                    <strong>Brand:</strong> <?= $car['brand'] ?><br>
                                    <strong>Model:</strong> <?= $car['model'] ?><br>
                                    <strong>Year:</strong> <?= $car['year'] ?><br>
                                    <strong>Type:</strong> <?= $car['type'] ?><br>
                                    <strong>Category:</strong> <?= ucfirst($car['category']) ?><br>
                                    <strong>Seats:</strong> <?= $car['seats'] ?><br>
                                    <strong>Price:</strong> $<?= number_format($car['price_per_day'], 2) ?> per day
                                </p>
                            </div>
                            <div class="card-footer">
                                <a href="/customer/book-car/<?= $car['id'] ?>" class="btn btn-primary w-100">Book Now</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; <?= date('Y') ?> Car Rental System. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>