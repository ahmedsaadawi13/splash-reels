<h1>Dashboard</h1>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Active Projects</div>
        <div class="stat-value"><?php echo $projectsCount; ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Clips</div>
        <div class="stat-value"><?php echo $clipsCount; ?></div>
        <small><?php echo $clipsReadyCount; ?> ready</small>
    </div>
    <div class="stat-card">
        <div class="stat-label">Media Files</div>
        <div class="stat-value"><?php echo $mediaCount; ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">API Calls</div>
        <div class="stat-value"><?php echo $usage['api_calls_count']; ?></div>
        <small>this month</small>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Usage & Quotas</h2>
    </div>

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
</div>

<div class="card">
    <div class="card-header flex justify-between items-center">
        <h2 class="card-title">Recent Projects</h2>
        <a href="/projects/create" class="btn btn-primary btn-small">New Project</a>
    </div>

    <?php if (empty($recentProjects)): ?>
        <p>No projects yet. <a href="/projects/create">Create your first project</a></p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentProjects as $project): ?>
                <tr>
                    <td><?php echo htmlspecialchars($project['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><span class="badge badge-success"><?php echo htmlspecialchars($project['status'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                    <td><?php echo date('M j, Y', strtotime($project['created_at'])); ?></td>
                    <td><a href="/projects/<?php echo $project['id']; ?>" class="btn btn-small btn-secondary">View</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Recent Clips</h2>
    </div>

    <?php if (empty($recentClips)): ?>
        <p>No clips yet.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentClips as $clip): ?>
                <tr>
                    <td><?php echo htmlspecialchars($clip['title'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo $clip['duration_seconds']; ?>s</td>
                    <td>
                        <?php if ($clip['status'] === 'ready'): ?>
                            <span class="badge badge-success">Ready</span>
                        <?php elseif ($clip['status'] === 'processing'): ?>
                            <span class="badge badge-warning">Processing</span>
                        <?php else: ?>
                            <span class="badge badge-info"><?php echo htmlspecialchars($clip['status'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('M j, Y', strtotime($clip['created_at'])); ?></td>
                    <td><a href="/clips/edit/<?php echo $clip['id']; ?>" class="btn btn-small btn-secondary">Edit</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
