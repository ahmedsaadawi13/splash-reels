<div class="flex justify-between items-center mb-20">
    <h1>Projects</h1>
    <?php if (Auth::hasRole(array('tenant_admin', 'editor'))): ?>
        <a href="/projects/create" class="btn btn-primary">New Project</a>
    <?php endif; ?>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($projects)): ?>
                <tr>
                    <td colspan="5" class="text-center">No projects found</td>
                </tr>
            <?php else: ?>
                <?php foreach ($projects as $project): ?>
                <tr>
                    <td><?php echo htmlspecialchars($project['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars(substr($project['description'], 0, 100), ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><span class="badge badge-success"><?php echo htmlspecialchars($project['status'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                    <td><?php echo date('M j, Y', strtotime($project['created_at'])); ?></td>
                    <td>
                        <a href="/projects/<?php echo $project['id']; ?>" class="btn btn-small btn-secondary">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
