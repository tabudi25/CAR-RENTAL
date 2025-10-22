<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="stat-card p-4">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon me-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <i class="fas fa-car"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0"><?= $totalCars ?? 0 ?></h3>
                                        <p class="text-muted mb-0">Total Cars</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="stat-card p-4">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon me-3" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0"><?= $totalBookings ?? 0 ?></h3>
                                        <p class="text-muted mb-0">Total Bookings</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="stat-card p-4">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon me-3" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0"><?= $totalCustomers ?? 0 ?></h3>
                                        <p class="text-muted mb-0">Total Customers</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="stat-card p-4">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon me-3" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0">₱<?= number_format($totalRevenue ?? 0, 2) ?></h3>
                                        <p class="text-muted mb-0">Total Revenue</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Charts Row -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="chart-container">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">
                                        <i class="fas fa-chart-line me-2"></i>
                                        Booking Trends
                                    </h6>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary active" id="monthlyBtn">Monthly</button>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="weeklyBtn">Weekly</button>
                                    </div>
                                </div>
                                <canvas id="bookingChart" height="60"></canvas>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="chart-container">
                                <h6 class="mb-2">
                                    <i class="fas fa-chart-pie me-2"></i>
                                    Car Status
                                </h6>
                                <canvas id="carStatusChart" height="120"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Activity -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="chart-container">
                                <h5 class="mb-3">
                                    <i class="fas fa-clock me-2"></i>
                                    Recent Bookings
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Customer</th>
                                                <th>Car</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(isset($recentBookings) && !empty($recentBookings)): ?>
                                                <?php foreach($recentBookings as $booking): ?>
                                                <tr>
                                                    <td>#<?= $booking['id'] ?></td>
                                                    <td><?= $booking['customer_name'] ?? 'N/A' ?></td>
                                                    <td><?= $booking['car_name'] ?? 'N/A' ?></td>
                                                    <td><?= date('M d, Y', strtotime($booking['created_at'])) ?></td>
                                                    <td>
                                                        <span class="badge bg-<?= $booking['status'] == 'pending' ? 'warning' : ($booking['status'] == 'confirmed' ? 'success' : 'secondary') ?>">
                                                            <?= ucfirst($booking['status']) ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">No recent bookings</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="chart-container">
                                <h5 class="mb-3">
                                    <i class="fas fa-car me-2"></i>
                                    Car Inventory
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Car</th>
                                                <th>Type</th>
                                                <th>Status</th>
                                                <th>Price/Day</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(isset($recentCars) && !empty($recentCars)): ?>
                                                <?php foreach($recentCars as $car): ?>
                                                <tr>
                                                    <td><?= $car['name'] ?></td>
                                                    <td><?= $car['type'] ?></td>
                                                    <td>
                                                        <span class="badge bg-<?= $car['status'] == 'available' ? 'success' : ($car['status'] == 'rented' ? 'danger' : 'warning') ?>">
                                                            <?= ucfirst($car['status']) ?>
                                                        </span>
                                                    </td>
                                                    <td>$<?= number_format($car['price_per_day'], 2) ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">No cars available</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Booking Trends Chart
        const bookingCtx = document.getElementById('bookingChart').getContext('2d');
        
        // Data from server
        const monthlyData = {
            labels: <?= json_encode($months ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']) ?>,
            data: <?= json_encode($bookingTrends ?? [0, 0, 0, 0, 0, 0]) ?>
        };
        
        const weeklyData = {
            labels: <?= json_encode($days ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']) ?>,
            data: <?= json_encode($weeklyTrends ?? [0, 0, 0, 0, 0, 0, 0]) ?>
        };
        
        let currentChart = new Chart(bookingCtx, {
            type: 'line',
            data: {
                labels: monthlyData.labels,
                datasets: [{
                    label: 'Bookings',
                    data: monthlyData.data,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                aspectRatio: 3,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        
        // Toggle functionality
        document.getElementById('monthlyBtn').addEventListener('click', function() {
            currentChart.data.labels = monthlyData.labels;
            currentChart.data.datasets[0].data = monthlyData.data;
            currentChart.update();
            
            document.getElementById('monthlyBtn').classList.add('active');
            document.getElementById('weeklyBtn').classList.remove('active');
        });
        
        document.getElementById('weeklyBtn').addEventListener('click', function() {
            currentChart.data.labels = weeklyData.labels;
            currentChart.data.datasets[0].data = weeklyData.data;
            currentChart.update();
            
            document.getElementById('weeklyBtn').classList.add('active');
            document.getElementById('monthlyBtn').classList.remove('active');
        });

        // Car Status Chart
        const carStatusCtx = document.getElementById('carStatusChart').getContext('2d');
        new Chart(carStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Available', 'Rented', 'Maintenance'],
                datasets: [{
                    data: [<?= $availableCars ?? 0 ?>, <?= $rentedCars ?? 0 ?>, <?= $maintenanceCars ?? 0 ?>],
                    backgroundColor: [
                        '#28a745',
                        '#dc3545',
                        '#ffc107'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>

<?= $this->endSection() ?>
