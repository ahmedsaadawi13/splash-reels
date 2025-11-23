<h1>Billing & Usage</h1>

<?php if ($plan): ?>
<div class="card">
    <h2 class="card-title">Current Plan: <?php echo htmlspecialchars($plan['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
    <p><strong>Price:</strong> $<?php echo number_format($plan['price_monthly'], 2); ?>/month</p>
    <p><strong>Status:</strong> <span class="badge badge-success"><?php echo htmlspecialchars($subscription['status'], ENT_QUOTES, 'UTF-8'); ?></span></p>
    <?php if ($subscription['renewal_date']): ?>
        <p><strong>Next Renewal:</strong> <?php echo date('M j, Y', strtotime($subscription['renewal_date'])); ?></p>
    <?php endif; ?>

    <a href="/billing/plans" class="btn btn-primary">View All Plans</a>
</div>
<?php endif; ?>

<div class="card">
    <h2 class="card-title">Usage This Month</h2>

    <div class="form-group">
        <label class="form-label">
            Processing Minutes: <?php echo $usage['total_input_minutes']; ?> / <?php echo $limits['max_minutes_input_per_month']; ?>
        </label>
        <div class="progress">
            <div class="progress-bar <?php echo $minutesPercentage > 80 ? 'warning' : ''; ?>"
                 style="width: <?php echo $minutesPercentage; ?>%"></div>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">
            Exports: <?php echo $usage['total_exported_clips']; ?> / <?php echo $limits['max_exports_per_month']; ?>
        </label>
        <div class="progress">
            <div class="progress-bar <?php echo $exportsPercentage > 80 ? 'warning' : ''; ?>"
                 style="width: <?php echo $exportsPercentage; ?>%"></div>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">
            Storage: <?php echo $usage['total_storage_mb']; ?> MB / <?php echo $limits['storage_limit_mb']; ?> MB
        </label>
        <div class="progress">
            <div class="progress-bar <?php echo $storagePercentage > 80 ? 'warning' : ''; ?>"
                 style="width: <?php echo $storagePercentage; ?>%"></div>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">API Calls: <?php echo $usage['api_calls_count']; ?></label>
    </div>
</div>

<div class="card">
    <div class="card-header flex justify-between items-center">
        <h2 class="card-title">API Keys</h2>
        <form method="POST" action="/billing/api-key/create" style="display: inline;">
            <?php echo CSRF::field(); ?>
            <button type="submit" class="btn btn-primary btn-small">Create New Key</button>
        </form>
    </div>

    <?php if (empty($apiKeys)): ?>
        <p>No API keys yet.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Label</th>
                    <th>API Key</th>
                    <th>Status</th>
                    <th>Last Used</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($apiKeys as $key): ?>
                <tr>
                    <td><?php echo htmlspecialchars($key['label'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><code><?php echo htmlspecialchars(substr($key['api_key'], 0, 20), ENT_QUOTES, 'UTF-8'); ?>...</code></td>
                    <td>
                        <?php if ($key['is_active']): ?>
                            <span class="badge badge-success">Active</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Revoked</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $key['last_used_at'] ? date('M j, Y', strtotime($key['last_used_at'])) : 'Never'; ?></td>
                    <td>
                        <?php if ($key['is_active']): ?>
                            <form method="POST" action="/billing/api-key/<?php echo $key['id']; ?>/revoke" style="display: inline;">
                                <?php echo CSRF::field(); ?>
                                <button type="submit" class="btn btn-small btn-danger" onclick="return confirm('Revoke this API key?')">Revoke</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
