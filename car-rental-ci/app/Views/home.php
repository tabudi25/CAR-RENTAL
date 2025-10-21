<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Rental System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .car-card {
            transition: transform 0.3s;
            margin-bottom: 20px;
        }
        .car-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .car-img {
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Car Rental System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/auth/login">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/auth/register">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="text-center">Welcome to Car Rental System</h1>
                <p class="text-center lead">Find and book your perfect car for your next journey</p>
            </div>
        </div>

        <div class="row">
            <?php if (empty($cars)): ?>
                <div class="col-12 text-center">
                    <p>No cars available at the moment. Please check back later.</p>
                </div>
            <?php else: ?>
                <?php foreach ($cars as $car): ?>
                    <div class="col-md-4">
                        <div class="card car-card">
                            <?php if (!empty($car['image'])): ?>
                                <img src="<?= base_url($car['image']) ?>" class="card-img-top car-img" alt="<?= $car['name'] ?>">
                            <?php else: ?>
                                <div class="card-img-top car-img bg-secondary d-flex align-items-center justify-content-center">
                                    <span class="text-white">No Image</span>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= $car['name'] ?></h5>
                                <p class="card-text">
                                    <strong>Type:</strong> <?= $car['type'] ?><br>
                                    <strong>Category:</strong> <?= ucfirst($car['category']) ?><br>
                                    <strong>Seats:</strong> <?= $car['seats'] ?><br>
                                    <strong>Price:</strong> $<?= number_format($car['price'], 2) ?> per day
                                </p>
                                <a href="/auth/login" class="btn btn-primary">Login to Book</a>
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