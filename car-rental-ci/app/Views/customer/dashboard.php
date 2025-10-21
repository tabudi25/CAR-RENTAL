<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - Car Rental System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                        <a class="nav-link active" href="/customer">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/customer/bookings">My Bookings</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <span class="navbar-text me-3">
                        Welcome, <?= $user['name'] ?>
                    </span>
                    <a href="/auth/logout" class="btn btn-outline-light">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <h2 class="mb-4">Available Cars</h2>
        
        <div class="row">
            <?php if (empty($cars)) : ?>
                <div class="col-12">
                    <div class="alert alert-info">No cars available at the moment.</div>
                </div>
            <?php else : ?>
                <?php foreach ($cars as $car) : ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <?php if (!empty($car['image_url'])) : ?>
                                <img src="<?= base_url($car['image_url']) ?>" class="card-img-top" alt="<?= $car['name'] ?>" style="height: 200px; object-fit: cover;">
                            <?php else : ?>
                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <span>No Image</span>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= $car['name'] ?></h5>
                                <p class="card-text">
                                    <strong>Brand:</strong> <?= $car['brand'] ?? 'N/A' ?><br>
                                    <strong>Year:</strong> <?= $car['year'] ?? 'N/A' ?><br>
                                    <strong>Type:</strong> <?= $car['type'] ?? 'N/A' ?><br>
                                    <strong>Category:</strong> <?= ucfirst($car['category'] ?? 'N/A') ?><br>
                                    <strong>Seats:</strong> <?= $car['seats'] ?? 'N/A' ?><br>
                                    <strong>Price:</strong> $<?= number_format($car['price_per_day'] ?? 0, 2) ?> per day
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

        <h2 class="mt-5 mb-4">My Recent Bookings</h2>
        
        <?php if (empty($bookings)) : ?>
            <div class="alert alert-info">You don't have any bookings yet.</div>
        <?php else : ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Car</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking) : ?>
                            <tr>
                                <td><?= $booking['id'] ?></td>
                                <td><?= $booking['car_model'] ?? 'N/A' ?></td>
                                <td><?= date('M d, Y', strtotime($booking['start_date'])) ?></td>
                                <td><?= date('M d, Y', strtotime($booking['end_date'])) ?></td>
                                <td>$<?= number_format($booking['total_price'], 2) ?></td>
                                <td>
                                    <span class="badge bg-<?= getStatusBadgeClass($booking['status']) ?>">
                                        <?= ucfirst($booking['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($booking['status'] === 'pending') : ?>
                                        <form action="/customer/cancel-booking/<?= $booking['id'] ?>" method="post" class="d-inline">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; <?= date('Y') ?> Car Rental System. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Helper function to get appropriate badge class based on status
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'pending':
            return 'warning';
        case 'confirmed':
            return 'success';
        case 'cancelled':
            return 'danger';
        case 'completed':
            return 'info';
        default:
            return 'secondary';
    }
}
?>