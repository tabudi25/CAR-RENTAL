<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Car Rental System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .payment-container {
            max-width: 800px;
            margin: 2rem auto;
        }
        .payment-card {
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        .payment-method-card {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .payment-method-card:hover {
            border-color: #2563eb;
            background-color: #f8fafc;
        }
        .payment-method-card.selected {
            border-color: #2563eb;
            background-color: #eff6ff;
        }
        .payment-icon {
            font-size: 2rem;
            margin-right: 1rem;
        }
        .demo-badge {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 1rem;
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

    <div class="container payment-container">
        <div class="demo-badge">
            <i class="fas fa-flask me-2"></i>DEMO PAYMENT - This is a simulation only
        </div>

        <?php if (session()->has('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= session('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="payment-card">
            <h2 class="mb-4">
                <i class="fas fa-credit-card me-2 text-primary"></i>Payment Details
            </h2>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Booking Information</h5>
                    <p><strong>Booking ID:</strong> #<?= $booking['id'] ?></p>
                    <p><strong>Car:</strong> <?= $booking['car_name'] ?></p>
                    <p><strong>Start Date:</strong> <?= date('M d, Y', strtotime($booking['start_date'])) ?></p>
                    <p><strong>End Date:</strong> <?= date('M d, Y', strtotime($booking['end_date'])) ?></p>
                    <?php if (!empty($booking['pick_up_time'])): ?>
                        <p><strong>Pick-up Time:</strong> <?= date('g:i A', strtotime($booking['pick_up_time'])) ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <h5>Payment Summary</h5>
                    <?php
                    $totalAmount = $booking['total_price'] ?? $booking['total_amount'] ?? 0;
                    $downPaymentAmount = $booking['down_payment_amount'] ?? 0;
                    $paymentStatus = $booking['payment_status'] ?? 'pending';
                    $paymentType = $payment_type ?? 'down_payment';
                    $alreadyPaid = $downPaymentAmount; // Always check down_payment_amount, not status
                    $remainingBalance = $totalAmount - $alreadyPaid;
                    $isFullPayment = ($paymentType === 'full');
                    ?>
                    <p><strong>Total Amount:</strong> ₱<?= number_format($totalAmount, 2) ?></p>
                    <?php if ($alreadyPaid > 0): ?>
                        <p><strong>Down Payment Paid:</strong> ₱<?= number_format($alreadyPaid, 2) ?> (50%)</p>
                        <p class="text-primary"><strong>Remaining Balance:</strong> ₱<?= number_format($remainingBalance, 2) ?></p>
                    <?php else: ?>
                        <p><strong>Down Payment Required:</strong> ₱<?= number_format($totalAmount * 0.5, 2) ?> (50%)</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <form action="/payment/process" method="post" id="paymentForm">
            <?= csrf_field() ?>
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
            <input type="hidden" name="payment_type" id="payment_type" value="<?= $isFullPayment ? 'full' : 'down_payment' ?>">
            <input type="hidden" name="payment_method" id="payment_method" required>

            <div class="payment-card">
                <h4 class="mb-4">Select Payment Method</h4>
                
                <div class="payment-method-card" data-method="gcash" onclick="selectPaymentMethod('gcash')">
                    <div class="d-flex align-items-center">
                        <div class="payment-icon text-success">
                            <i class="fab fa-google-pay"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1">GCash</h5>
                            <p class="text-muted mb-0">Pay using GCash mobile wallet</p>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method_radio" value="gcash" id="gcash" required onchange="selectPaymentMethod('gcash')">
                        </div>
                    </div>
                </div>

                <div class="payment-method-card" data-method="bank_transfer" onclick="selectPaymentMethod('bank_transfer')">
                    <div class="d-flex align-items-center">
                        <div class="payment-icon text-primary">
                            <i class="fas fa-university"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1">Bank Transfer</h5>
                            <p class="text-muted mb-0">Pay via bank transfer</p>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method_radio" value="bank_transfer" id="bank_transfer" required onchange="selectPaymentMethod('bank_transfer')">
                        </div>
                    </div>
                </div>
            </div>

            <div class="payment-card bg-light">
                <h4 class="mb-3">Payment Amount</h4>
                <div class="alert alert-info">
                    <h3 class="mb-0">
                        <strong id="payment_amount_display">
                            ₱<?= number_format($isFullPayment ? $remainingBalance : ($totalAmount * 0.5), 2) ?>
                        </strong>
                    </h3>
                    <p class="mb-0 mt-2">
                        <?php if ($isFullPayment): ?>
                            Remaining balance to complete full payment
                        <?php else: ?>
                            50% down payment required
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary btn-lg" id="payButton" disabled>
                    <i class="fas fa-lock me-2"></i>
                                    <span id="payButtonText">
                        <?php if ($isFullPayment): ?>
                            Pay Remaining Balance (₱<?= number_format($remainingBalance, 2) ?>)
                        <?php else: ?>
                            Pay Down Payment (50% - ₱<?= number_format($totalAmount * 0.5, 2) ?>)
                        <?php endif; ?>
                    </span>
                </button>
                <a href="/customer/bookings" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Cancel
                </a>
            </div>
        </form>

        <div class="alert alert-warning mt-4">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Demo Payment:</strong> This is a simulated payment system for demonstration purposes only. 
            No actual money will be charged. The payment will be processed instantly for demo purposes.
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectPaymentMethod(method) {
            // Update radio button
            document.getElementById(method).checked = true;
            
            // Update hidden input
            document.getElementById('payment_method').value = method;
            
            // Update visual selection
            document.querySelectorAll('.payment-method-card').forEach(card => {
                card.classList.remove('selected');
            });
            document.querySelector(`[data-method="${method}"]`).classList.add('selected');
            
            // Enable pay button
            document.getElementById('payButton').disabled = false;
        }

        // Form submission
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            const paymentMethod = document.getElementById('payment_method').value;
            const paymentMethodRadio = document.querySelector('input[name="payment_method_radio"]:checked');
            
            // Validate payment method is selected
            if (!paymentMethod && !paymentMethodRadio) {
                e.preventDefault();
                alert('Please select a payment method');
                return false;
            }
            
            // Ensure payment_method hidden field is set from radio button
            if (!paymentMethod && paymentMethodRadio) {
                document.getElementById('payment_method').value = paymentMethodRadio.value;
            }

            // Show loading state
            const payButton = document.getElementById('payButton');
            payButton.disabled = true;
            payButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing Payment...';
            
            // Form will submit normally
        });
    </script>
</body>
</html>

