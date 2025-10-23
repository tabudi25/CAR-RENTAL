<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AJIS Car Rental - Premium Car Rental Service</title>
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
            padding: 4rem 0;
            margin-bottom: 2rem;
        }

        .feature-card {
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            overflow: hidden;
            border: none;
            height: 100%;
            padding: 2rem;
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--card-shadow-hover);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color) 0%, #1d4ed8 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 2rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1d4ed8 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(37, 99, 235, 0.3);
        }

        .section-title {
            font-size: 1.875rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1.5rem;
        }

        .stats-card {
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--card-shadow-hover);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .footer {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: white;
            padding: 3rem 0 2rem;
            margin-top: 4rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-car me-2"></i>
                AJIS Car Rental
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/auth/login">
                            <i class="fas fa-sign-in-alt me-1"></i>Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/auth/register">
                            <i class="fas fa-user-plus me-1"></i>Register
                        </a>
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
                    <h1 class="display-4 fw-bold mb-3">Premium Car Rental Service</h1>
                    <p class="lead mb-4">Discover our premium collection of vehicles and book your next adventure with ease. Experience luxury, comfort, and reliability with AJIS Car Rental.</p>
                    <div class="d-flex gap-3">
                        <a href="/auth/register" class="btn btn-light btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Get Started
                        </a>
                        <a href="/auth/login" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="bg-white bg-opacity-20 rounded-3 p-3 mb-3">
                                <div class="h2 fw-bold">50+</div>
                                <div class="small">Premium Cars</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-white bg-opacity-20 rounded-3 p-3 mb-3">
                                <div class="h2 fw-bold">24/7</div>
                                <div class="small">Support</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-white bg-opacity-20 rounded-3 p-3 mb-3">
                                <div class="h2 fw-bold">1000+</div>
                                <div class="small">Happy Customers</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-white bg-opacity-20 rounded-3 p-3 mb-3">
                                <div class="h2 fw-bold">5★</div>
                                <div class="small">Rating</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Features Section -->
        <section id="features" class="py-5">
            <h2 class="section-title text-center">
                <i class="fas fa-star me-2"></i>Why Choose AJIS Car Rental?
            </h2>
            
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-car"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Premium Fleet</h4>
                        <p class="text-muted">Choose from our extensive collection of luxury and economy vehicles, all well-maintained and ready for your journey.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h4 class="fw-bold mb-3">24/7 Service</h4>
                        <p class="text-muted">Round-the-clock customer support and emergency assistance to ensure your peace of mind throughout your rental period.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Fully Insured</h4>
                        <p class="text-muted">Comprehensive insurance coverage for all our vehicles, giving you complete protection and confidence on the road.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Best Prices</h4>
                        <p class="text-muted">Competitive pricing with no hidden fees. Transparent pricing structure that gives you the best value for your money.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Easy Booking</h4>
                        <p class="text-muted">Simple and intuitive online booking system. Reserve your vehicle in just a few clicks from anywhere, anytime.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-tools"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Maintenance</h4>
                        <p class="text-muted">Regular maintenance and safety checks ensure all our vehicles are in perfect condition for your safety and comfort.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-5 bg-light rounded-3">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stats-card">
                        <div class="stats-number">500+</div>
                        <h5 class="fw-bold">Cars Available</h5>
                        <p class="text-muted mb-0">Wide selection of vehicles</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stats-card">
                        <div class="stats-number">10K+</div>
                        <h5 class="fw-bold">Happy Customers</h5>
                        <p class="text-muted mb-0">Satisfied clients worldwide</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stats-card">
                        <div class="stats-number">99%</div>
                        <h5 class="fw-bold">Satisfaction Rate</h5>
                        <p class="text-muted mb-0">Customer satisfaction guaranteed</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stats-card">
                        <div class="stats-number">24/7</div>
                        <h5 class="fw-bold">Support</h5>
                        <p class="text-muted mb-0">Round-the-clock assistance</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="py-5">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="section-title">About AJIS Car Rental</h2>
                    <p class="lead">We are a leading car rental service provider committed to delivering exceptional customer experiences through our premium fleet and outstanding service.</p>
                    <p>With years of experience in the automotive industry, we understand the importance of reliability, comfort, and value. Our mission is to make your travel experience seamless and memorable.</p>
                    <div class="d-flex gap-3">
                        <a href="/auth/register" class="btn btn-primary">
                            <i class="fas fa-user-plus me-2"></i>Join Us Today
                        </a>
                        <a href="/auth/login" class="btn btn-outline-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <i class="fas fa-car-side fa-10x text-primary opacity-25"></i>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-car me-2"></i>AJIS Car Rental
                    </h5>
                    <p class="text-light">Your trusted partner for premium car rental services. Experience the difference with our exceptional fleet and outstanding customer service.</p>
                </div>
                <div class="col-lg-4 mb-4">
                    <h6 class="fw-bold mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="/" class="text-light text-decoration-none">Home</a></li>
                        <li><a href="#features" class="text-light text-decoration-none">Features</a></li>
                        <li><a href="#about" class="text-light text-decoration-none">About</a></li>
                        <li><a href="/auth/login" class="text-light text-decoration-none">Login</a></li>
                        <li><a href="/auth/register" class="text-light text-decoration-none">Register</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h6 class="fw-bold mb-3">Contact Info</h6>
                    <ul class="list-unstyled text-light">
                        <li><i class="fas fa-phone me-2"></i>+1 (555) 123-4567</li>
                        <li><i class="fas fa-envelope me-2"></i>info@ajiscarrental.com</li>
                        <li><i class="fas fa-map-marker-alt me-2"></i>123 Main Street, City, State</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?= date('Y') ?> AJIS Car Rental. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">Page rendered in {elapsed_time} seconds | Environment: <?= ENVIRONMENT ?></p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>