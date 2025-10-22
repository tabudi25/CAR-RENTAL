<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-users me-2 text-primary"></i>
        Manage Users
    </h2>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" onclick="exportUsers()">
            <i class="fas fa-download me-2"></i>
            Export
        </button>
        <button class="btn btn-primary" onclick="refreshUsers()">
            <i class="fas fa-sync me-2"></i>
            Refresh
        </button>
    </div>
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

<!-- User Statistics -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card p-4">
            <div class="d-flex align-items-center">
                <div class="stat-icon me-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
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
                <div class="stat-icon me-3" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div>
                    <h3 class="mb-0"><?= $totalStaff ?? 0 ?></h3>
                    <p class="text-muted mb-0">Staff Members</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stat-card p-4">
            <div class="d-flex align-items-center">
                <div class="stat-icon me-3" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div>
                    <h3 class="mb-0"><?= $totalAdmins ?? 0 ?></h3>
                    <p class="text-muted mb-0">Administrators</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stat-card p-4">
            <div class="d-flex align-items-center">
                <div class="stat-icon me-3" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <i class="fas fa-user-check"></i>
                </div>
                <div>
                    <h3 class="mb-0"><?= $activeUsers ?? 0 ?></h3>
                    <p class="text-muted mb-0">Active Users</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="stat-card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($users) && !empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td>#<?= $user['id'] ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-user-circle fa-2x text-muted"></i>
                                </div>
                                <div>
                                    <div class="fw-bold"><?= $user['name'] ?></div>
                                    <small class="text-muted">ID: <?= $user['id'] ?></small>
                                </div>
                            </div>
                        </td>
                        <td><?= $user['email'] ?></td>
                        <td>
                            <span class="badge bg-<?= $user['user_type'] == 'admin' ? 'danger' : ($user['user_type'] == 'staff' ? 'warning' : 'info') ?>">
                                <?= ucfirst($user['user_type']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-success">Active</span>
                        </td>
                        <td><?= date('M d, Y', strtotime($user['created_at'] ?? 'now')) ?></td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/admin/view-user/<?= $user['id'] ?>">
                                        <i class="fas fa-eye me-2"></i>View Details
                                    </a></li>
                                    <li><a class="dropdown-item" href="/admin/edit-user/<?= $user['id'] ?>">
                                        <i class="fas fa-edit me-2"></i>Edit User
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="/admin/delete-user/<?= $user['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?')">
                                        <i class="fas fa-trash me-2"></i>Delete User
                                    </a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Users Found</h5>
                            <p class="text-muted">Users will appear here when they register.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function exportUsers() {
    // Implement export functionality
    alert('Export functionality will be implemented');
}

function refreshUsers() {
    location.reload();
}
</script>
<?= $this->endSection() ?>
