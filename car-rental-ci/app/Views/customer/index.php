<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - Car Rental System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --light-bg: #f8fafc;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --card-shadow-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light-bg);
        }

        .navbar {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%) !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .hero-section {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }

        .car-card {
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            overflow: hidden;
            border: none;
            height: 100%;
        }

        .car-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--card-shadow-hover);
        }

        .car-image {
            height: 220px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .car-card:hover .car-image {
            transform: scale(1.05);
        }

        .car-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: var(--success-color);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .car-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .car-features {
            display: flex;
            gap: 1rem;
            margin: 1rem 0;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--secondary-color);
            font-size: 0.875rem;
        }

        .btn-book {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1d4ed8 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-book:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(37, 99, 235, 0.3);
        }

        .section-title {
            font-size: 1.875rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1.5rem;
        }

        .no-cars {
            text-align: center;
            padding: 3rem;
            color: var(--secondary-color);
        }

        .no-cars i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
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

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">Find Your Perfect Ride</h1>
                    <p class="lead mb-4">Discover our premium collection of vehicles and book your next adventure with ease.</p>
                </div>
                <div class="col-lg-4">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="bg-white bg-opacity-20 rounded-3 p-3 mb-3">
                                <div class="h2 fw-bold"><?= count($cars) ?></div>
                                <div class="small">Available Cars</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-white bg-opacity-20 rounded-3 p-3 mb-3">
                                <div class="h2 fw-bold">24/7</div>
                                <div class="small">Support</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <?php if (session()->has('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= session('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (session()->has('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= session('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Available Cars Section -->
        <h2 class="section-title">
            <i class="fas fa-car me-2"></i>Available Cars
        </h2>
        
        <div class="row">
            <?php if (empty($cars)): ?>
                <div class="col-12">
                    <div class="no-cars">
                        <i class="fas fa-car-crash"></i>
                        <h3>No cars available at the moment</h3>
                        <p>Please check back later or contact our support team.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($cars as $car): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="car-card">
                            <div class="position-relative">
                                <?php if (!empty($car['image_url'])): ?>
                                    <img src="<?= base_url($car['image_url']) ?>" class="car-image w-100" alt="<?= $car['name'] ?>">
                                <?php else: ?>
                                    <img src="https://images.unsplash.com/photo-1549317336-206569e8475c?w=800&h=600&fit=crop" class="car-image w-100" alt="Default Car Image">
                                <?php endif; ?>
                                <div class="car-badge">
                                    <i class="fas fa-check-circle me-1"></i>Available
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="fw-bold mb-1"><?= $car['name'] ?></h5>
                                        <p class="text-muted mb-0"><?= $car['brand'] ?> • <?= $car['year'] ?></p>
                                    </div>
                                    <div class="text-end">
                                        <div class="car-price">₱<?= number_format($car['price_per_day'], 0) ?></div>
                                        <small class="text-muted">per day</small>
                                    </div>
                                </div>
                                
                                <div class="car-features">
                                    <div class="feature-item">
                                        <i class="fas fa-users"></i>
                                        <span><?= $car['seats'] ?> seats</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-tag"></i>
                                        <span><?= ucfirst($car['category']) ?></span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-cog"></i>
                                        <span><?= $car['type'] ?></span>
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <a href="/customer/book-car/<?= $car['id'] ?>" class="btn btn-book">
                                        <i class="fas fa-calendar-plus me-2"></i>Book Now
                                    </a>
                                </div>
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
    <script>
        // Add smooth animations and interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Add fade-in animation to car cards
            const carCards = document.querySelectorAll('.car-card');
            carCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
            
            // Add hover effects
            carCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px) scale(1.02)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        });
    </script>
</body>
</html>