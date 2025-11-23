<h1>Clip Suggestions for: <?php echo htmlspecialchars($mediaFile['title'], ENT_QUOTES, 'UTF-8'); ?></h1>

<div class="card">
    <?php if (empty($suggestions)): ?>
        <p>No suggestions found. <a href="/media/view/<?php echo $mediaFile['id']; ?>">Generate AI highlights first</a></p>
    <?php else: ?>
        <?php foreach ($suggestions as $suggestion): ?>
            <div style="border-bottom: 1px solid #e5e5e5; padding: 20px 0;">
                <h3><?php echo htmlspecialchars($suggestion['suggested_title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <p><?php echo htmlspecialchars($suggestion['suggested_caption_text'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p>
                    <strong>Time:</strong> <?php echo TimecodeHelper::secondsToTimecode($suggestion['start_seconds']); ?> - <?php echo TimecodeHelper::secondsToTimecode($suggestion['end_seconds']); ?>
                    (<?php echo $suggestion['end_seconds'] - $suggestion['start_seconds']; ?>s)
                </p>
                <p><strong>Confidence:</strong> <?php echo ($suggestion['confidence_score'] * 100); ?>%</p>
                <p><strong>Status:</strong> <span class="badge badge-info"><?php echo htmlspecialchars($suggestion['status'], ENT_QUOTES, 'UTF-8'); ?></span></p>

                <?php if ($suggestion['status'] === 'suggested'): ?>
                    <div class="form-group">
                        <label>Template:</label>
                        <select id="template_<?php echo $suggestion['id']; ?>" class="form-control" style="max-width: 300px;">
                            <option value="">Default</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Platform:</label>
                        <select id="platform_<?php echo $suggestion['id']; ?>" class="form-control" style="max-width: 200px;">
                            <option value="generic">Generic</option>
                            <option value="tiktok">TikTok</option>
                            <option value="reels">Instagram Reels</option>
                            <option value="shorts">YouTube Shorts</option>
                        </select>
                    </div>

                    <button onclick="acceptSuggestion(<?php echo $suggestion['id']; ?>)" class="btn btn-primary">Accept & Create Clip</button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
