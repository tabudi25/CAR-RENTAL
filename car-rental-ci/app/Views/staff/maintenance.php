<?= $this->extend('staff/layout') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-wrench me-2 text-info"></i>
        Vehicle Maintenance
    </h2>
    <a href="/staff" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>
        Back to Dashboard
    </a>
</div>

<?php if (session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= session('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->has('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= session('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <div class="stat-card">
            <h5 class="mb-3">
                <i class="fas fa-plus me-2"></i>
                Report Maintenance Issue
            </h5>
            
            <form action="/staff/report-maintenance" method="post">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="car_id" class="form-label">Select Vehicle</label>
                        <select class="form-select" id="car_id" name="car_id" required>
                            <option value="">Choose a vehicle</option>
                            <?php if (isset($cars) && !empty($cars)): ?>
                                <?php foreach ($cars as $car): ?>
                                    <option value="<?= $car['id'] ?>"><?= $car['name'] ?> (<?= $car['plate'] ?>)</option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="issue_type" class="form-label">Issue Type</label>
                        <select class="form-select" id="issue_type" name="issue_type" required>
                            <option value="">Select issue type</option>
                            <option value="mechanical">Mechanical</option>
                            <option value="electrical">Electrical</option>
                            <option value="body_damage">Body Damage</option>
                            <option value="interior">Interior</option>
                            <option value="tire">Tire Issue</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="priority" class="form-label">Priority</label>
                        <select class="form-select" id="priority" name="priority" required>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="estimated_cost" class="form-label">Estimated Cost (₱)</label>
                        <input type="number" class="form-control" id="estimated_cost" name="estimated_cost" step="0.01" min="0">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Issue Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4" required placeholder="Describe the maintenance issue in detail..."></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="reported_by" class="form-label">Reported By</label>
                    <input type="text" class="form-control" id="reported_by" name="reported_by" value="<?= session()->get('name') ?>" readonly>
                </div>
                
                <button type="submit" class="btn btn-info">
                    <i class="fas fa-wrench me-2"></i>
                    Report Maintenance Issue
                </button>
            </form>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="stat-card">
            <h5 class="mb-3">
                <i class="fas fa-list me-2"></i>
                Current Maintenance
            </h5>
            
            <?php if (isset($maintenanceCars) && !empty($maintenanceCars)): ?>
                <?php foreach ($maintenanceCars as $car): ?>
                <div class="border rounded p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1"><?= $car['name'] ?></h6>
                            <small class="text-muted"><?= $car['plate'] ?></small>
                        </div>
                        <span class="badge bg-warning">In Maintenance</span>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">Status: <?= ucfirst($car['status']) ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-3">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <p class="text-muted mb-0">No vehicles in maintenance</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="stat-card mt-3">
            <h6 class="mb-3">
                <i class="fas fa-chart-pie me-2"></i>
                Maintenance Stats
            </h6>
            <div class="row text-center">
                <div class="col-6">
                    <div class="h4 text-warning"><?= $maintenanceCount ?? 0 ?></div>
                    <small class="text-muted">In Maintenance</small>
                </div>
                <div class="col-6">
                    <div class="h4 text-success"><?= $availableCount ?? 0 ?></div>
                    <small class="text-muted">Available</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Maintenance History -->
<div class="row mt-4">
    <div class="col-12">
        <div class="stat-card">
            <h5 class="mb-3">
                <i class="fas fa-history me-2"></i>
                Recent Maintenance Reports
            </h5>
            
            <?php if (isset($maintenanceHistory) && !empty($maintenanceHistory)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Vehicle</th>
                            <th>Issue Type</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Reported By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($maintenanceHistory as $report): ?>
                        <tr>
                            <td><?= date('M d, Y', strtotime($report['created_at'])) ?></td>
                            <td><?= $report['car_name'] ?></td>
                            <td><?= ucfirst(str_replace('_', ' ', $report['issue_type'])) ?></td>
                            <td>
                                <span class="badge bg-<?= $report['priority'] == 'urgent' ? 'danger' : ($report['priority'] == 'high' ? 'warning' : 'info') ?>">
                                    <?= ucfirst($report['priority']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $report['status'] == 'completed' ? 'success' : ($report['status'] == 'in_progress' ? 'warning' : 'secondary') ?>">
                                    <?= ucfirst($report['status']) ?>
                                </span>
                            </td>
                            <td><?= $report['reported_by'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-wrench fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Maintenance Reports</h5>
                <p class="text-muted">Maintenance reports will appear here when submitted.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
