<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Car - Car Rental System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .payment-method-card {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        .payment-method-card:hover {
            border-color: #2563eb;
            background-color: #f8fafc;
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
        
        <?php if (session()->has('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>
                <?= session('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-calendar-plus me-2"></i>Book a Car</h4>
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
                                    <label for="pick_up_time" class="form-label">Pick-up Time <span class="text-danger">*</span></label>
                                    <select class="form-control" id="pick_up_time" name="pick_up_time" required>
                                        <option value="">Select pick-up time</option>
                                        <option value="08:00:00">8:00 AM</option>
                                        <option value="09:00:00">9:00 AM</option>
                                        <option value="10:00:00">10:00 AM</option>
                                        <option value="11:00:00">11:00 AM</option>
                                        <option value="12:00:00">12:00 PM</option>
                                        <option value="13:00:00">1:00 PM</option>
                                        <option value="14:00:00">2:00 PM</option>
                                        <option value="15:00:00">3:00 PM</option>
                                        <option value="16:00:00">4:00 PM</option>
                                        <option value="17:00:00">5:00 PM</option>
                                    </select>
                                    <small class="form-text text-muted">Pick-up hours: 8:00 AM - 5:00 PM</small>
                                </div>
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" required min="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">
                                        Payment Method <span class="text-danger">*</span>
                                        <span class="badge bg-info ms-2"><i class="fas fa-flask me-1"></i>Demo Mode</span>
                                    </label>
                                    <select class="form-control" id="payment_method" name="payment_method" required onchange="showPaymentDetails()">
                                        <option value="">Select payment method</option>
                                        <option value="gcash">GCash</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                    </select>
                                    <small class="form-text text-muted">A 50% down payment is required to confirm your booking. <strong>(Demo: Enter your personal payment details - no actual charges)</strong></small>
                                </div>
                                
                                <!-- Payment Details Section (shown when payment method is selected) -->
                                <div id="payment_details_section" class="mb-3" style="display: none;">
                                    <div class="alert alert-info mb-3">
                                        <i class="fas fa-flask me-2"></i><strong>Demo Payment System:</strong> Enter your personal GCash number or bank account number below. This is a simulation - no actual payment will be processed. Click "Pay Down Payment" to simulate the payment process.
                                    </div>
                                    <label class="form-label">Payment Details</label>
                                    
                                    <!-- GCash Payment Details -->
                                    <div id="gcash_details" class="card border-success mb-3" style="display: none;">
                                        <div class="card-header bg-success text-white">
                                            <h5 class="mb-0"><i class="fab fa-google-pay me-2"></i>GCash Payment Details</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="gcash_number" class="form-label">
                                                    <strong>Your GCash Number <span class="text-danger">*</span></strong>
                                                </label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="gcash_number" 
                                                       name="gcash_number" 
                                                       placeholder="09XX XXX XXXX" 
                                                       pattern="[0-9]{11}" 
                                                       maxlength="11"
                                                       required>
                                                <small class="form-text text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>Enter your 11-digit GCash mobile number (e.g., 09171234567)
                                                </small>
                                            </div>
                                            <div class="alert alert-info">
                                                <p class="mb-2"><strong>Send Payment To:</strong></p>
                                                <p class="mb-1"><strong>GCash Number:</strong> 0917 123 4567</p>
                                                <p class="mb-1"><strong>Account Name:</strong> AJIS Car Rental</p>
                                                <p class="mb-0"><strong>Amount to Send:</strong> <span id="gcash_amount" class="fw-bold text-primary">₱0.00</span></p>
                                            </div>
                                            <div class="alert alert-warning mb-2">
                                                <small><i class="fas fa-info-circle me-1"></i>Enter your personal GCash number above. After "sending" payment, you will enter your transaction reference number below.</small>
                                            </div>
                                            <div class="alert alert-info mb-0">
                                                <small><i class="fas fa-flask me-1"></i><strong>Demo Mode:</strong> Enter any GCash number and reference number. Click "Pay Down Payment" to simulate payment. No actual money will be charged.</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Bank Transfer Payment Details -->
                                    <div id="bank_transfer_details" class="card border-primary mb-3" style="display: none;">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="mb-0"><i class="fas fa-university me-2"></i>Bank Transfer Payment Details</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="bank_account_number" class="form-label">
                                                    <strong>Your Bank Account Number <span class="text-danger">*</span></strong>
                                                </label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="bank_account_number" 
                                                       name="bank_account_number" 
                                                       placeholder="Enter your account number" 
                                                       required>
                                                <small class="form-text text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>Enter the bank account number you will use to send the payment
                                                </small>
                                            </div>
                                            <div class="alert alert-info">
                                                <p class="mb-2"><strong>Send Payment To:</strong></p>
                                                <p class="mb-1"><strong>Bank Name:</strong> BDO (Banco de Oro)</p>
                                                <p class="mb-1"><strong>Account Number:</strong> 1234 5678 9012 3456</p>
                                                <p class="mb-1"><strong>Account Name:</strong> AJIS Car Rental</p>
                                                <p class="mb-1"><strong>Swift Code:</strong> BNORPHMM</p>
                                                <p class="mb-0"><strong>Amount to Send:</strong> <span id="bank_amount" class="fw-bold text-primary">₱0.00</span></p>
                                            </div>
                                            <div class="alert alert-warning mb-2">
                                                <small><i class="fas fa-info-circle me-1"></i>Enter your personal bank account number above. After "making" the transfer, you will enter your transaction reference number below.</small>
                                            </div>
                                            <div class="alert alert-info mb-0">
                                                <small><i class="fas fa-flask me-1"></i><strong>Demo Mode:</strong> Enter any bank account number and reference number. Click "Pay Down Payment" to simulate payment. No actual money will be charged.</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <strong>Down Payment Required:</strong> <span id="down_payment_amount">₱0.00</span><br>
                                        <small>This is 50% of the total rental amount. Remaining balance due upon pick-up.</small>
                                    </div>
                                    
                                    <!-- Payment Reference Input -->
                                    <div class="mb-3">
                                        <label for="payment_reference" class="form-label">
                                            <strong>Payment Reference Number <span class="text-danger">*</span></strong>
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="payment_reference" 
                                               name="payment_reference" 
                                               placeholder="Enter your transaction/reference number" 
                                               required>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            <span id="reference_help_text">Enter the reference number you received after sending the payment.</span>
                                        </small>
                                    </div>
                                    
                                    <!-- Pay Down Payment Button -->
                                    <div class="d-grid mb-3">
                                        <button type="button" class="btn btn-success btn-lg" id="pay_down_payment_btn" onclick="processDownPayment()">
                                            <i class="fas fa-credit-card me-2"></i>Pay Down Payment (50%)
                                        </button>
                                        <small class="text-muted text-center mt-2">
                                            <i class="fas fa-info-circle me-1"></i>Step 1: Enter your personal number above → Step 2: Enter reference number → Step 3: Click here to pay
                                        </small>
                                    </div>
                                    
                                    <!-- Payment Status -->
                                    <div id="payment_status" style="display: none;">
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle me-2"></i>
                                            <strong>Down Payment Received!</strong>
                                            <p class="mb-0 mt-2">
                                                <strong id="payment_ref_display"></strong>
                                            </p>
                                            <p class="mb-0 mt-2">You can now proceed to complete your booking by clicking "Book Now" below.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes (Optional)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary" id="book_now_btn" disabled>
                                        <i class="fas fa-calendar-check me-2"></i>Book Now
                                    </button>
                                    <small class="text-muted text-center" id="book_now_note">Please select a payment method and complete the down payment first.</small>
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
                            <li>50% down payment is required to confirm booking</li>
                            <li>Remaining balance due upon pickup</li>
                            <li>Pick-up hours: 8:00 AM - 5:00 PM</li>
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
        const dailyRate = <?= $car['price_per_day'] ?? 0 ?>;
        let downPaymentPaid = false;
        let downPaymentAmount = 0;
        
        // Calculate down payment (50% of total)
        function calculateDownPayment() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            
            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
                const totalAmount = dailyRate * days;
                downPaymentAmount = totalAmount * 0.5;
                
                document.getElementById('down_payment_amount').textContent = '₱' + downPaymentAmount.toFixed(2);
                document.getElementById('gcash_amount').textContent = '₱' + downPaymentAmount.toFixed(2);
                document.getElementById('bank_amount').textContent = '₱' + downPaymentAmount.toFixed(2);
            }
        }
        
        // Show payment details when payment method is selected
        function showPaymentDetails() {
            const paymentMethod = document.getElementById('payment_method').value;
            const paymentSection = document.getElementById('payment_details_section');
            const gcashDetails = document.getElementById('gcash_details');
            const bankDetails = document.getElementById('bank_transfer_details');
            const referenceInput = document.getElementById('payment_reference');
            const referenceHelp = document.getElementById('reference_help_text');
            const gcashNumberInput = document.getElementById('gcash_number');
            const bankAccountInput = document.getElementById('bank_account_number');
            
            if (paymentMethod) {
                paymentSection.style.display = 'block';
                
                // Recalculate down payment amount if dates are already selected
                calculateDownPayment();
                
                if (paymentMethod === 'gcash') {
                    gcashDetails.style.display = 'block';
                    bankDetails.style.display = 'none';
                    referenceInput.placeholder = 'Enter your GCash transaction reference number';
                    referenceHelp.textContent = 'Enter the transaction reference number you received from GCash after sending the payment.';
                    // Focus on GCash number input
                    if (gcashNumberInput) {
                        gcashNumberInput.value = '';
                        gcashNumberInput.focus();
                    }
                } else if (paymentMethod === 'bank_transfer') {
                    gcashDetails.style.display = 'none';
                    bankDetails.style.display = 'block';
                    referenceInput.placeholder = 'Enter your bank transfer reference number';
                    referenceHelp.textContent = 'Enter the transaction reference number you received from your bank after making the transfer.';
                    // Focus on bank account input
                    if (bankAccountInput) {
                        bankAccountInput.value = '';
                        bankAccountInput.focus();
                    }
                }
                
                // Clear reference input
                referenceInput.value = '';
            } else {
                paymentSection.style.display = 'none';
                gcashDetails.style.display = 'none';
                bankDetails.style.display = 'none';
            }
            
            // Reset payment status if method changed
            if (!downPaymentPaid) {
                document.getElementById('payment_status').style.display = 'none';
                document.getElementById('book_now_btn').disabled = true;
                document.getElementById('book_now_note').style.display = 'block';
                referenceInput.value = '';
                referenceInput.disabled = false;
                if (gcashNumberInput) gcashNumberInput.value = '';
                if (bankAccountInput) bankAccountInput.value = '';
            }
        }
        
        // Process down payment (demo)
        function processDownPayment() {
            const paymentMethod = document.getElementById('payment_method').value;
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const pickUpTime = document.getElementById('pick_up_time').value;
            const paymentReference = document.getElementById('payment_reference').value.trim();
            const gcashNumber = document.getElementById('gcash_number') ? document.getElementById('gcash_number').value.trim() : '';
            const bankAccountNumber = document.getElementById('bank_account_number') ? document.getElementById('bank_account_number').value.trim() : '';
            
            // Validate required fields
            if (!paymentMethod) {
                alert('Please select a payment method');
                return;
            }
            
            if (!startDate || !endDate) {
                alert('Please select start and end dates');
                return;
            }
            
            if (!pickUpTime) {
                alert('Please select a pick-up time');
                return;
            }
            
            // Validate payment account number
            if (paymentMethod === 'gcash') {
                if (!gcashNumber) {
                    alert('Please enter your GCash number');
                    document.getElementById('gcash_number').focus();
                    return;
                }
                if (gcashNumber.length !== 11 || !/^09\d{9}$/.test(gcashNumber)) {
                    alert('Please enter a valid 11-digit GCash number starting with 09');
                    document.getElementById('gcash_number').focus();
                    return;
                }
            } else if (paymentMethod === 'bank_transfer') {
                if (!bankAccountNumber) {
                    alert('Please enter your bank account number');
                    document.getElementById('bank_account_number').focus();
                    return;
                }
                if (bankAccountNumber.length < 5) {
                    alert('Please enter a valid bank account number');
                    document.getElementById('bank_account_number').focus();
                    return;
                }
            }
            
            if (!paymentReference) {
                alert('Please enter your payment reference/transaction number');
                document.getElementById('payment_reference').focus();
                return;
            }
            
            if (paymentReference.length < 5) {
                alert('Please enter a valid payment reference number (at least 5 characters)');
                document.getElementById('payment_reference').focus();
                return;
            }
            
            // Show processing
            const payBtn = document.getElementById('pay_down_payment_btn');
            payBtn.disabled = true;
            payBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing Payment...';
            
            // Simulate payment processing (demo)
            setTimeout(function() {
                // Payment successful
                downPaymentPaid = true;
                
                // Show success message
                document.getElementById('payment_status').style.display = 'block';
                let paymentInfo = 'Reference: ' + paymentReference;
                if (paymentMethod === 'gcash' && gcashNumber) {
                    paymentInfo += ' | GCash: ' + gcashNumber;
                } else if (paymentMethod === 'bank_transfer' && bankAccountNumber) {
                    paymentInfo += ' | Account: ' + bankAccountNumber;
                }
                document.getElementById('payment_ref_display').textContent = paymentInfo;
                
                // Enable Book Now button
                document.getElementById('book_now_btn').disabled = false;
                document.getElementById('book_now_note').style.display = 'none';
                
                // Update button
                payBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Down Payment Paid';
                payBtn.classList.remove('btn-success');
                payBtn.classList.add('btn-secondary');
                
                // Store payment info in hidden field for form submission
                const paymentPaidField = document.createElement('input');
                paymentPaidField.type = 'hidden';
                paymentPaidField.name = 'down_payment_paid';
                paymentPaidField.value = '1';
                paymentPaidField.id = 'down_payment_paid';
                if (!document.getElementById('down_payment_paid')) {
                    document.querySelector('form').appendChild(paymentPaidField);
                }
                
                // Store payment amount
                const paymentAmount = document.createElement('input');
                paymentAmount.type = 'hidden';
                paymentAmount.name = 'down_payment_amount_value';
                paymentAmount.value = downPaymentAmount.toFixed(2);
                paymentAmount.id = 'down_payment_amount_value';
                if (!document.getElementById('down_payment_amount_value')) {
                    document.querySelector('form').appendChild(paymentAmount);
                }
                
                // Store payment reference
                const paymentRef = document.createElement('input');
                paymentRef.type = 'hidden';
                paymentRef.name = 'payment_reference_number';
                paymentRef.value = paymentReference;
                paymentRef.id = 'payment_reference_number';
                if (!document.getElementById('payment_reference_number')) {
                    document.querySelector('form').appendChild(paymentRef);
                }
                
                // Store customer's payment account number
                if (paymentMethod === 'gcash' && gcashNumber) {
                    const customerGcash = document.createElement('input');
                    customerGcash.type = 'hidden';
                    customerGcash.name = 'customer_payment_account';
                    customerGcash.value = gcashNumber;
                    customerGcash.id = 'customer_payment_account';
                    if (!document.getElementById('customer_payment_account')) {
                        document.querySelector('form').appendChild(customerGcash);
                    }
                } else if (paymentMethod === 'bank_transfer' && bankAccountNumber) {
                    const customerBank = document.createElement('input');
                    customerBank.type = 'hidden';
                    customerBank.name = 'customer_payment_account';
                    customerBank.value = bankAccountNumber;
                    customerBank.id = 'customer_payment_account';
                    if (!document.getElementById('customer_payment_account')) {
                        document.querySelector('form').appendChild(customerBank);
                    }
                }
                
                // Disable inputs after payment
                document.getElementById('payment_reference').disabled = true;
                if (document.getElementById('gcash_number')) {
                    document.getElementById('gcash_number').disabled = true;
                }
                if (document.getElementById('bank_account_number')) {
                    document.getElementById('bank_account_number').disabled = true;
                }
            }, 1500); // 1.5 second delay to simulate processing
        }
        
        // Ensure end date is not before start date
        document.getElementById('start_date').addEventListener('change', function() {
            document.getElementById('end_date').min = this.value;
            
            // If end date is before start date, update it
            const endDate = document.getElementById('end_date');
            if (endDate.value && endDate.value < this.value) {
                endDate.value = this.value;
            }
            
            calculateDownPayment();
            // Reset payment if dates change
            if (downPaymentPaid) {
                downPaymentPaid = false;
                document.getElementById('payment_status').style.display = 'none';
                document.getElementById('book_now_btn').disabled = true;
                document.getElementById('book_now_note').style.display = 'block';
                const payBtn = document.getElementById('pay_down_payment_btn');
                payBtn.disabled = false;
                payBtn.innerHTML = '<i class="fas fa-credit-card me-2"></i>Pay Down Payment (50%)';
                payBtn.classList.remove('btn-secondary');
                payBtn.classList.add('btn-success');
            }
        });
        
        document.getElementById('end_date').addEventListener('change', function() {
            calculateDownPayment();
            // Reset payment if dates change
            if (downPaymentPaid) {
                downPaymentPaid = false;
                document.getElementById('payment_status').style.display = 'none';
                document.getElementById('book_now_btn').disabled = true;
                document.getElementById('book_now_note').style.display = 'block';
                const payBtn = document.getElementById('pay_down_payment_btn');
                payBtn.disabled = false;
                payBtn.innerHTML = '<i class="fas fa-credit-card me-2"></i>Pay Down Payment (50%)';
                payBtn.classList.remove('btn-secondary');
                payBtn.classList.add('btn-success');
                document.getElementById('payment_reference').disabled = false;
                document.getElementById('payment_reference').value = '';
                if (document.getElementById('gcash_number')) {
                    document.getElementById('gcash_number').disabled = false;
                    document.getElementById('gcash_number').value = '';
                }
                if (document.getElementById('bank_account_number')) {
                    document.getElementById('bank_account_number').disabled = false;
                    document.getElementById('bank_account_number').value = '';
                }
            }
        });
        
        // Reset payment if pick-up time changes
        document.getElementById('pick_up_time').addEventListener('change', function() {
            if (downPaymentPaid) {
                downPaymentPaid = false;
                document.getElementById('payment_status').style.display = 'none';
                document.getElementById('book_now_btn').disabled = true;
                document.getElementById('book_now_note').style.display = 'block';
                const payBtn = document.getElementById('pay_down_payment_btn');
                payBtn.disabled = false;
                payBtn.innerHTML = '<i class="fas fa-credit-card me-2"></i>Pay Down Payment (50%)';
                payBtn.classList.remove('btn-secondary');
                payBtn.classList.add('btn-success');
                document.getElementById('payment_reference').disabled = false;
                document.getElementById('payment_reference').value = '';
                if (document.getElementById('gcash_number')) {
                    document.getElementById('gcash_number').disabled = false;
                    document.getElementById('gcash_number').value = '';
                }
                if (document.getElementById('bank_account_number')) {
                    document.getElementById('bank_account_number').disabled = false;
                    document.getElementById('bank_account_number').value = '';
                }
            }
        });
        
        // Format GCash number input (auto-format)
        const gcashInput = document.getElementById('gcash_number');
        if (gcashInput) {
            gcashInput.addEventListener('input', function(e) {
                // Remove non-numeric characters
                let value = e.target.value.replace(/\D/g, '');
                // Limit to 11 digits
                if (value.length > 11) {
                    value = value.substring(0, 11);
                }
                e.target.value = value;
            });
        }
        
        // Form submission validation
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!downPaymentPaid) {
                e.preventDefault();
                alert('Please complete the down payment before booking.');
                return false;
            }
        });
        
        // Initial calculation
        calculateDownPayment();
    </script>
</body>
</html>