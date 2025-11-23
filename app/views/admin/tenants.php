<h1>Platform Admin - Tenants</h1>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Slug</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($tenants)): ?>
                <tr>
                    <td colspan="5" class="text-center">No tenants found</td>
                </tr>
            <?php else: ?>
                <?php foreach ($tenants as $tenant): ?>
                <tr>
                    <td><?php echo htmlspecialchars($tenant['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($tenant['slug'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <?php if ($tenant['status'] === 'active'): ?>
                            <span class="badge badge-success">Active</span>
                        <?php elseif ($tenant['status'] === 'suspended'): ?>
                            <span class="badge badge-warning">Suspended</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Canceled</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('M j, Y', strtotime($tenant['created_at'])); ?></td>
                    <td>
                        <a href="/admin/tenants/<?php echo $tenant['id']; ?>" class="btn btn-small btn-secondary">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
