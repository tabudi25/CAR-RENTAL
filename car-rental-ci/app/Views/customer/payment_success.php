<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Car Rental System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .success-container {
            max-width: 600px;
            margin: 3rem auto;
            text-align: center;
        }
        .success-icon {
            font-size: 5rem;
            color: #10b981;
            margin-bottom: 1.5rem;
        }
        .success-card {
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        .payment-reference {
            background-color: #f3f4f6;
            padding: 1rem;
            border-radius: 8px;
            font-family: monospace;
            font-size: 1.1rem;
            font-weight: bold;
            color: #2563eb;
        }
    </style>
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
                        <a class="nav-link" href="/customer">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/customer/bookings">My Bookings</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="nav-link">Welcome, <?= $user['name'] ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/auth/logout">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container success-container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>

        <div class="success-card">
            <h2 class="text-success mb-3">Payment Successful!</h2>
            <p class="lead">Your payment has been processed successfully.</p>
            
            <div class="mt-4 mb-4">
                <h5>Payment Details</h5>
                <div class="payment-reference">
                    Reference: <?= $payment_reference ?? 'N/A' ?>
                </div>
                <p class="mt-3 mb-0">
                    <strong>Amount Paid:</strong> ₱<?= number_format($payment_amount ?? 0, 2) ?>
                </p>
                <p class="text-muted">
                    <?php if ($payment_type === 'down_payment'): ?>
                        50% Down Payment (Status: Pending)
                    <?php else: ?>
                        Full Payment (Status: Paid)
                    <?php endif; ?>
                </p>
            </div>

            <div class="alert alert-info">
                <h6>Booking Information</h6>
                <p class="mb-1"><strong>Booking ID:</strong> #<?= $booking['id'] ?></p>
                <p class="mb-1"><strong>Car:</strong> <?= $booking['car_name'] ?></p>
                <p class="mb-0"><strong>Status:</strong> 
                    <span class="badge bg-<?= $booking['payment_status'] === 'paid' ? 'success' : 'warning' ?>">
                        <?= ucfirst($booking['payment_status'] ?? 'pending') ?>
                    </span>
                </p>
            </div>

            <?php if ($booking['payment_status'] === 'pending' && ($booking['down_payment_amount'] ?? 0) > 0): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Note:</strong> You have paid the 50% down payment. 
                    Payment status remains <strong>Pending</strong> until full payment is completed.
                    The remaining balance will be due upon car pick-up.
                </div>
            <?php endif; ?>

            <div class="d-grid gap-2 mt-4">
                <a href="/customer/bookings" class="btn btn-primary btn-lg">
                    <i class="fas fa-list me-2"></i>View My Bookings
                </a>
                <a href="/customer" class="btn btn-outline-secondary">
                    <i class="fas fa-home me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>

        <div class="alert alert-warning">
            <i class="fas fa-flask me-2"></i>
            <strong>Demo Payment:</strong> This was a simulated payment. 
            In a real system, you would receive a confirmation email with your receipt.
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

