<div class="flex justify-between items-center mb-20">
    <h1>Media Library</h1>
    <a href="/media/upload" class="btn btn-primary">Upload Media</a>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Source</th>
                <th>Duration</th>
                <th>Resolution</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($mediaFiles)): ?>
                <tr>
                    <td colspan="7" class="text-center">No media files found</td>
                </tr>
            <?php else: ?>
                <?php foreach ($mediaFiles as $media): ?>
                <tr>
                    <td><?php echo htmlspecialchars($media['title'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($media['source_type'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo TimecodeHelper::formatDuration($media['duration_seconds']); ?></td>
                    <td><?php echo htmlspecialchars($media['resolution'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><span class="badge badge-<?php echo $media['status'] === 'ready' ? 'success' : 'info'; ?>"><?php echo htmlspecialchars($media['status'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                    <td><?php echo date('M j, Y', strtotime($media['created_at'])); ?></td>
                    <td>
                        <a href="/media/view/<?php echo $media['id']; ?>" class="btn btn-small btn-secondary">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
