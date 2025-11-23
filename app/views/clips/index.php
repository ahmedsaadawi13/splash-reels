<div class="flex justify-between items-center mb-20">
    <h1>Clips</h1>
    <a href="/clips/create" class="btn btn-primary">Create Clip</a>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Duration</th>
                <th>Platform</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($clips)): ?>
                <tr>
                    <td colspan="6" class="text-center">No clips found</td>
                </tr>
            <?php else: ?>
                <?php foreach ($clips as $clip): ?>
                <tr>
                    <td><?php echo htmlspecialchars($clip['title'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo $clip['duration_seconds']; ?>s</td>
                    <td><?php echo htmlspecialchars($clip['platform_hint'], ENT_QUOTES, 'UTF-8'); ?></td>
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
                    <td class="flex gap-10">
                        <a href="/clips/edit/<?php echo $clip['id']; ?>" class="btn btn-small btn-secondary">Edit</a>
                        <?php if ($clip['status'] === 'ready'): ?>
                            <button onclick="exportClip(<?php echo $clip['id']; ?>)" class="btn btn-small btn-success">Export</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
