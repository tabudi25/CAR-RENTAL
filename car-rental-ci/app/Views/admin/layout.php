<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Dashboard' ?> - AJIS Car Rental</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --info-color: #17a2b8;
        }
        
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
        }
        
        .sidebar.collapsed {
            width: 70px;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 10px;
            margin: 5px 15px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
            transform: translateX(5px);
        }
        
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        .sidebar.collapsed .nav-link i {
            margin-right: 0;
        }
        
        .sidebar.collapsed .nav-link span {
            display: none;
        }
        
        .main-content {
            margin-left: 250px;
            transition: all 0.3s ease;
            min-height: 100vh;
            background: #f8f9fa;
            position: relative;
            z-index: 1;
            flex: 1;
            width: calc(100% - 250px);
        }
        
        .main-content.expanded {
            margin-left: 70px;
            width: calc(100% - 70px);
        }
        
        .top-navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 30px;
            margin-bottom: 30px;
        }
        
        .content-area {
            padding: 0 30px 30px;
            overflow-x: hidden;
            max-width: 100%;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            padding: 20px;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        
        .chart-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 15px;
        }
        
        .chart-container canvas {
            max-height: 200px !important;
        }
        
        .sidebar-toggle {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            padding: 10px;
            cursor: pointer;
        }
        
        .brand-logo {
            padding: 20px 15px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        
        .brand-logo h4 {
            color: white;
            margin: 0;
            font-weight: bold;
        }
        
        .sidebar.collapsed .brand-logo h4 {
            display: none;
        }
        
        .user-info {
            position: absolute;
            bottom: 20px;
            left: 15px;
            right: 15px;
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 10px;
            color: white;
        }
        
        .sidebar.collapsed .user-info {
            display: none;
        }
        
        .notification-badge {
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 10px;
            position: absolute;
            top: 5px;
            right: 5px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 1050;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .main-content.expanded {
                margin-left: 0;
                width: 100%;
            }
            
            .content-area {
                padding: 0 15px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
                <div class="brand-logo">
                    <button class="sidebar-toggle" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h4 class="mt-2">
                        <i class="fas fa-car me-2"></i>
                        <span>AJIS Car Rental</span>
                    </h4>
                </div>
                
                <nav class="nav flex-column">
                    <a class="nav-link <?= (uri_string() == 'admin' || uri_string() == 'admin/') ? 'active' : '' ?>" href="/admin">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                    <a class="nav-link <?= (strpos(uri_string(), 'admin/cars') !== false) ? 'active' : '' ?>" href="/admin/cars">
                        <i class="fas fa-car"></i>
                        <span>Manage Cars</span>
                        <?php if(isset($totalCars) && $totalCars > 0): ?>
                            <span class="notification-badge"><?= $totalCars ?></span>
                        <?php endif; ?>
                    </a>
                    <a class="nav-link <?= (strpos(uri_string(), 'admin/bookings') !== false) ? 'active' : '' ?>" href="/admin/bookings">
                        <i class="fas fa-calendar-check"></i>
                        <span>Bookings</span>
                        <?php if(isset($totalBookings) && $totalBookings > 0): ?>
                            <span class="notification-badge"><?= $totalBookings ?></span>
                        <?php endif; ?>
                    </a>
                    <a class="nav-link <?= (strpos(uri_string(), 'admin/users') !== false) ? 'active' : '' ?>" href="/admin/users">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                        <?php if(isset($totalCustomers) && $totalCustomers > 0): ?>
                            <span class="notification-badge"><?= $totalCustomers ?></span>
                        <?php endif; ?>
                    </a>
                    <a class="nav-link <?= (strpos(uri_string(), 'admin/add-car') !== false) ? 'active' : '' ?>" href="/admin/add-car">
                        <i class="fas fa-plus"></i>
                        <span>Add Car</span>
                    </a>
                    <a class="nav-link" href="/admin/reports">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reports</span>
                    </a>
                </nav>
                
                <div class="user-info">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-user-circle fa-2x"></i>
                        </div>
                        <div>
                            <div class="fw-bold"><?= session()->get('name') ?? 'Admin' ?></div>
                            <small class="text-light">Administrator</small>
                        </div>
                    </div>
                </div>
            </div>
            
        <!-- Main Content -->
        <div class="main-content" id="mainContent">
                <!-- Top Navbar -->
                <div class="top-navbar">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <button class="btn btn-outline-secondary me-3 d-md-none" onclick="toggleMobileMenu()">
                                <i class="fas fa-bars"></i>
                            </button>
                            <div>
                                <h4 class="mb-0"><?= $pageTitle ?? 'Admin Dashboard' ?></h4>
                                <small class="text-muted"><?= $pageSubtitle ?? 'Manage your car rental business' ?></small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="text-muted">Welcome back, <?= session()->get('name') ?>!</span>
                            </div>
                            <a href="/auth/logout" class="btn btn-outline-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Content Area -->
                <div class="content-area">
                    <?= $this->renderSection('content') ?>
                </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        }
        
        // Auto-collapse on mobile
        if (window.innerWidth < 768) {
            document.getElementById('sidebar').classList.add('collapsed');
            document.getElementById('mainContent').classList.add('expanded');
        }
        
        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth < 768) {
                document.getElementById('sidebar').classList.add('collapsed');
                document.getElementById('mainContent').classList.add('expanded');
            } else {
                document.getElementById('sidebar').classList.remove('collapsed');
                document.getElementById('mainContent').classList.remove('expanded');
            }
        });
        
        // Add mobile menu toggle
        function toggleMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        }
    </script>
</body>
</html>
