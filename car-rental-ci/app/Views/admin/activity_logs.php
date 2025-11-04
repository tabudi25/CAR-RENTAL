<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-history me-2"></i>Customer Activity Logs
    </h2>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="/admin/activity-logs" class="row g-3">
            <div class="col-md-3">
                <label for="action" class="form-label">Action</label>
                <select name="action" id="action" class="form-select">
                    <option value="">All Actions</option>
                    <?php foreach ($actions as $act): ?>
                        <option value="<?= $act ?>" <?= ($filters['action'] ?? '') === $act ? 'selected' : '' ?>>
                            <?= ucwords(str_replace('_', ' ', $act)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="<?= $filters['start_date'] ?? '' ?>">
            </div>
            <div class="col-md-2">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="<?= $filters['end_date'] ?? '' ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <a href="/admin/activity-logs" class="btn btn-secondary">
                        <i class="fas fa-redo me-2"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Activity Logs Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($logs)): ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle me-2"></i>No activity logs found.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>Related</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td>
                                    <small><?= date('M d, Y', strtotime($log['created_at'])) ?></small><br>
                                    <small class="text-muted"><?= date('h:i A', strtotime($log['created_at'])) ?></small>
                                </td>
                                <td>
                                    <strong><?= $log['user_name'] ?? 'N/A' ?></strong><br>
                                    <small class="text-muted"><?= $log['user_email'] ?? 'N/A' ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= ucwords(str_replace('_', ' ', $log['action'])) ?>
                                    </span>
                                </td>
                                <td><?= $log['description'] ?? 'N/A' ?></td>
                                <td>
                                    <?php if ($log['related_id']): ?>
                                        <small>
                                            <?= ucfirst($log['related_type'] ?? 'item') ?> #<?= $log['related_id'] ?>
                                        </small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small><?= $log['ip_address'] ?? 'N/A' ?></small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

