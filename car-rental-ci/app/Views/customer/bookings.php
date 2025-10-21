<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Car Rental System</title>
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
                        <a class="nav-link" href="/customer">Available Cars</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/customer/bookings">My Bookings</a>
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
        <h1 class="mb-4">My Bookings</h1>
        
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
        
        <?php if (empty($bookings)): ?>
            <div class="alert alert-info">You have no bookings yet.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Booking ID</th>
                            <th>Car</th>
                            <th>From Date</th>
                            <th>To Date</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?= $booking['id'] ?></td>
                                <td><?= $booking['car_name'] ?></td>
                                <td><?= date('M d, Y', strtotime($booking['from_date'])) ?></td>
                                <td><?= date('M d, Y', strtotime($booking['to_date'])) ?></td>
                                <td>$<?= number_format($booking['total_price'], 2) ?></td>
                                <td>
                                    <span class="badge bg-<?= getStatusBadgeClass($booking['status']) ?>">
                                        <?= ucfirst($booking['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($booking['status'] === 'pending' || $booking['status'] === 'confirmed'): ?>
                                        <a href="/customer/cancel-booking/<?= $booking['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel</a>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-secondary" disabled>Cancel</button>
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