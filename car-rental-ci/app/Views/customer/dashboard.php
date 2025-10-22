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

        .search-filter-section {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
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

        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            text-align: center;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .filter-btn {
            border: 2px solid #e2e8f0;
            background: white;
            color: var(--secondary-color);
            border-radius: 8px;
            padding: 8px 16px;
            margin: 4px;
            transition: all 0.3s ease;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .search-input {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 16px;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
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
                        <div class="col-4">
                            <div class="stats-card">
                                <div class="stats-number"><?= count($cars) ?></div>
                                <div class="text-muted">Available Cars</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stats-card">
                                <div class="stats-number"><?= count($bookings) ?></div>
                                <div class="text-muted">Your Bookings</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stats-card">
                                <div class="stats-number">24/7</div>
                                <div class="text-muted">Support</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Search and Filter Section -->
        <div class="search-filter-section">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control search-input" id="searchInput" placeholder="Search cars by name, brand, or type...">
                    </div>
                </div>
                <div class="col-lg-6 mt-3 mt-lg-0">
                    <div class="d-flex flex-wrap">
                        <button class="btn filter-btn active" data-filter="all">All Cars</button>
                        <button class="btn filter-btn" data-filter="economy">Economy</button>
                        <button class="btn filter-btn" data-filter="luxury">Luxury</button>
                        <button class="btn filter-btn" data-filter="suv">SUV</button>
                        <button class="btn filter-btn" data-filter="sports">Sports</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Cars Section -->
        <h2 class="section-title">
            <i class="fas fa-car me-2"></i>Available Cars
        </h2>
        
        <div class="row" id="carsContainer">
            <?php if (empty($cars)) : ?>
                <div class="col-12">
                    <div class="no-cars">
                        <i class="fas fa-car-crash"></i>
                        <h3>No cars available at the moment</h3>
                        <p>Please check back later or contact our support team.</p>
                    </div>
                </div>
            <?php else : ?>
                <?php foreach ($cars as $car) : ?>
                    <div class="col-lg-4 col-md-6 mb-4" data-category="<?= strtolower($car['category'] ?? '') ?>" data-search="<?= strtolower($car['name'] . ' ' . ($car['brand'] ?? '') . ' ' . ($car['type'] ?? '')) ?>">
                        <div class="car-card">
                            <div class="position-relative">
                                <?php if (!empty($car['image_url'])) : ?>
                                    <img src="<?= base_url($car['image_url']) ?>" class="car-image w-100" alt="<?= $car['name'] ?>">
                                <?php else : ?>
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
                                        <p class="text-muted mb-0"><?= $car['brand'] ?? 'N/A' ?> • <?= $car['year'] ?? 'N/A' ?></p>
                                    </div>
                                    <div class="text-end">
                                        <div class="car-price">₱<?= number_format($car['price_per_day'] ?? 0, 0) ?></div>
                                        <small class="text-muted">per day</small>
                                    </div>
                                </div>
                                
                                <div class="car-features">
                                    <div class="feature-item">
                                        <i class="fas fa-users"></i>
                                        <span><?= $car['seats'] ?? 'N/A' ?> seats</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-tag"></i>
                                        <span><?= ucfirst($car['category'] ?? 'N/A') ?></span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-cog"></i>
                                        <span><?= $car['type'] ?? 'N/A' ?></span>
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
                                <td>₱<?= number_format($booking['total_price'], 2) ?></td>
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
    <script>
        // Search and Filter Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const filterButtons = document.querySelectorAll('.filter-btn');
            const carCards = document.querySelectorAll('[data-category]');
            
            // Search functionality
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                filterCars(searchTerm, getActiveFilter());
            });
            
            // Filter functionality
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Update active button
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Filter cars
                    const searchTerm = searchInput.value.toLowerCase();
                    filterCars(searchTerm, this.dataset.filter);
                });
            });
            
            function getActiveFilter() {
                const activeButton = document.querySelector('.filter-btn.active');
                return activeButton ? activeButton.dataset.filter : 'all';
            }
            
            function filterCars(searchTerm, category) {
                carCards.forEach(card => {
                    const cardCategory = card.dataset.category;
                    const cardSearch = card.dataset.search;
                    
                    const matchesSearch = searchTerm === '' || cardSearch.includes(searchTerm);
                    const matchesCategory = category === 'all' || cardCategory === category;
                    
                    if (matchesSearch && matchesCategory) {
                        card.style.display = 'block';
                        card.style.animation = 'fadeIn 0.3s ease-in';
                    } else {
                        card.style.display = 'none';
                    }
                });
                
                // Show no results message if needed
                const visibleCards = Array.from(carCards).filter(card => card.style.display !== 'none');
                const noResultsDiv = document.getElementById('noResults');
                
                if (visibleCards.length === 0 && carCards.length > 0) {
                    if (!noResultsDiv) {
                        const container = document.getElementById('carsContainer');
                        const noResults = document.createElement('div');
                        noResults.id = 'noResults';
                        noResults.className = 'col-12';
                        noResults.innerHTML = `
                            <div class="no-cars">
                                <i class="fas fa-search"></i>
                                <h3>No cars found</h3>
                                <p>Try adjusting your search or filter criteria.</p>
                            </div>
                        `;
                        container.appendChild(noResults);
                    }
                } else if (noResultsDiv) {
                    noResultsDiv.remove();
                }
            }
            
            // Add fade-in animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(20px); }
                    to { opacity: 1; transform: translateY(0); }
                }
            `;
            document.head.appendChild(style);
        });
    </script>
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