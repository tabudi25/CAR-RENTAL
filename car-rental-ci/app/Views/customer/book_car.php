<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Car - Car Rental System</title>
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

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Book a Car</h4>
                    </div>
                    <div class="card-body">
                        <?php if(isset($car)): ?>
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <?php if(!empty($car['image_url'])): ?>
                                        <img src="<?= base_url($car['image_url']) ?>" class="img-fluid rounded" alt="<?= $car['name'] ?>">
                                    <?php else: ?>
                                        <div class="bg-secondary text-white p-5 text-center rounded">No Image</div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-8">
                                    <h3><?= $car['name'] ?></h3>
                                    <p><strong>Plate:</strong> <?= $car['plate'] ?></p>
                                    <p><strong>Type:</strong> <?= $car['type'] ?></p>
                                    <p><strong>Category:</strong> <?= ucfirst($car['category']) ?></p>
                                    <p><strong>Seats:</strong> <?= $car['seats'] ?></p>
                                    <p><strong>Price:</strong> ₱<?= number_format($car['price_per_day'], 2) ?> per day</p>
                                </div>
                            </div>

                            <form action="/customer/create-booking" method="post">
                                <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" required min="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" required min="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes (Optional)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">Book Now</button>
                                    <a href="/customer" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                Car not found or not available for booking.
                                <a href="/customer" class="btn btn-primary mt-3">Back to Available Cars</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Booking Information</h5>
                    </div>
                    <div class="card-body">
                        <p>Please note the following:</p>
                        <ul>
                            <li>Bookings are subject to availability</li>
                            <li>A valid ID is required upon pickup</li>
                            <li>Full payment is required before pickup</li>
                            <li>Cancellations must be made 24 hours before pickup</li>
                        </ul>
                        <div class="alert alert-info">
                            For assistance, please contact our customer service at (123) 456-7890.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Ensure end date is not before start date
        document.getElementById('start_date').addEventListener('change', function() {
            document.getElementById('end_date').min = this.value;
            
            // If end date is before start date, update it
            const endDate = document.getElementById('end_date');
            if (endDate.value && endDate.value < this.value) {
                endDate.value = this.value;
            }
        });
    </script>
</body>
</html>