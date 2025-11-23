<h1><?php echo htmlspecialchars($mediaFile['title'], ENT_QUOTES, 'UTF-8'); ?></h1>

<div class="card">
    <h3>Media Details</h3>
    <p><strong>Duration:</strong> <?php echo TimecodeHelper::formatDuration($mediaFile['duration_seconds']); ?></p>
    <p><strong>Resolution:</strong> <?php echo htmlspecialchars($mediaFile['resolution'], ENT_QUOTES, 'UTF-8'); ?></p>
    <p><strong>Aspect Ratio:</strong> <?php echo htmlspecialchars($mediaFile['aspect_ratio'], ENT_QUOTES, 'UTF-8'); ?></p>
    <p><strong>Status:</strong> <span class="badge badge-<?php echo $mediaFile['status'] === 'ready' ? 'success' : 'info'; ?>"><?php echo htmlspecialchars($mediaFile['status'], ENT_QUOTES, 'UTF-8'); ?></span></p>

    <?php if (Auth::hasRole(array('tenant_admin', 'editor'))): ?>
        <div class="mt-20">
            <button onclick="generateHighlights(<?php echo $mediaFile['id']; ?>)" class="btn btn-primary">Generate AI Highlights</button>
        </div>
    <?php endif; ?>
</div>

<?php if (!empty($suggestions)): ?>
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Clip Suggestions (<?php echo count($suggestions); ?>)</h2>
    </div>

    <?php foreach ($suggestions as $suggestion): ?>
        <?php if ($suggestion['status'] === 'suggested'): ?>
        <div style="border-bottom: 1px solid #e5e5e5; padding: 15px 0;">
            <h4><?php echo htmlspecialchars($suggestion['suggested_title'], ENT_QUOTES, 'UTF-8'); ?></h4>
            <p><?php echo htmlspecialchars($suggestion['suggested_caption_text'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p>
                <strong>Time:</strong> <?php echo TimecodeHelper::secondsToTimecode($suggestion['start_seconds']); ?> - <?php echo TimecodeHelper::secondsToTimecode($suggestion['end_seconds']); ?>
                (<?php echo $suggestion['end_seconds'] - $suggestion['start_seconds']; ?>s)
            </p>
            <p><strong>Confidence:</strong> <?php echo ($suggestion['confidence_score'] * 100); ?>%</p>

            <div class="flex gap-10">
                <a href="/clips/suggestions/<?php echo $mediaFile['id']; ?>" class="btn btn-primary btn-small">Accept & Edit</a>
                <button class="btn btn-secondary btn-small">Reject</button>
            </div>
        </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>
