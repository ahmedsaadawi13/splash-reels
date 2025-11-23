<div class="flex justify-between items-center mb-20">
    <h1><?php echo htmlspecialchars($project['name'], ENT_QUOTES, 'UTF-8'); ?></h1>
    <div class="flex gap-10">
        <a href="/media/upload?project_id=<?php echo $project['id']; ?>" class="btn btn-primary">Upload Media</a>
        <a href="/projects/<?php echo $project['id']; ?>/edit" class="btn btn-secondary">Edit Project</a>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Media Files</div>
        <div class="stat-value"><?php echo $stats['media_count']; ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Clips</div>
        <div class="stat-value"><?php echo $stats['clips_count']; ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Duration</div>
        <div class="stat-value"><?php echo TimecodeHelper::formatDuration($stats['total_duration']); ?></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Media Files</h2>
    </div>

    <?php if (empty($mediaFiles)): ?>
        <p>No media files yet. <a href="/media/upload?project_id=<?php echo $project['id']; ?>">Upload your first video</a></p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mediaFiles as $media): ?>
                <tr>
                    <td><?php echo htmlspecialchars($media['title'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo TimecodeHelper::formatDuration($media['duration_seconds']); ?></td>
                    <td><span class="badge badge-<?php echo $media['status'] === 'ready' ? 'success' : 'info'; ?>"><?php echo htmlspecialchars($media['status'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                    <td>
                        <a href="/media/view/<?php echo $media['id']; ?>" class="btn btn-small btn-secondary">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Clips</h2>
    </div>

    <?php if (empty($clips)): ?>
        <p>No clips yet.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Platform</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clips as $clip): ?>
                <tr>
                    <td><?php echo htmlspecialchars($clip['title'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo $clip['duration_seconds']; ?>s</td>
                    <td><span class="badge badge-<?php echo $clip['status'] === 'ready' ? 'success' : 'info'; ?>"><?php echo htmlspecialchars($clip['status'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                    <td><?php echo htmlspecialchars($clip['platform_hint'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <a href="/clips/edit/<?php echo $clip['id']; ?>" class="btn btn-small btn-secondary">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
