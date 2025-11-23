<h1>Edit Clip</h1>

<div class="card">
    <form method="POST" action="/clips/edit/<?php echo $clip['id']; ?>">
        <?php echo CSRF::field(); ?>

        <div class="form-group">
            <label for="title" class="form-label">Title *</label>
            <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($clip['title'], ENT_QUOTES, 'UTF-8'); ?>" required>
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control"><?php echo htmlspecialchars($clip['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>

        <div class="form-group">
            <label for="start_seconds" class="form-label">Start Time (seconds) *</label>
            <input type="number" id="start_seconds" name="start_seconds" class="form-control" value="<?php echo $clip['start_seconds']; ?>" required>
        </div>

        <div class="form-group">
            <label for="end_seconds" class="form-label">End Time (seconds) *</label>
            <input type="number" id="end_seconds" name="end_seconds" class="form-control" value="<?php echo $clip['end_seconds']; ?>" required>
        </div>

        <div class="form-group">
            <label for="clip_template_id" class="form-label">Template</label>
            <select id="clip_template_id" name="clip_template_id" class="form-control">
                <option value="">Default</option>
                <?php foreach ($templates as $template): ?>
                    <option value="<?php echo $template['id']; ?>" <?php echo $clip['clip_template_id'] == $template['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($template['name'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="platform_hint" class="form-label">Platform</label>
            <select id="platform_hint" name="platform_hint" class="form-control">
                <option value="generic" <?php echo $clip['platform_hint'] === 'generic' ? 'selected' : ''; ?>>Generic</option>
                <option value="tiktok" <?php echo $clip['platform_hint'] === 'tiktok' ? 'selected' : ''; ?>>TikTok</option>
                <option value="reels" <?php echo $clip['platform_hint'] === 'reels' ? 'selected' : ''; ?>>Instagram Reels</option>
                <option value="shorts" <?php echo $clip['platform_hint'] === 'shorts' ? 'selected' : ''; ?>>YouTube Shorts</option>
            </select>
        </div>

        <div class="flex gap-10">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <?php if ($clip['status'] === 'draft'): ?>
                <button type="button" onclick="renderClip(<?php echo $clip['id']; ?>)" class="btn btn-success">Render Clip</button>
            <?php endif; ?>
            <?php if ($clip['status'] === 'ready'): ?>
                <button type="button" onclick="exportClip(<?php echo $clip['id']; ?>)" class="btn btn-success">Export</button>
            <?php endif; ?>
            <a href="/clips" class="btn btn-secondary">Back to Clips</a>
        </div>
    </form>
</div>

<?php if ($clip['status'] === 'ready'): ?>
<div class="card">
    <h3>Preview</h3>
    <p><strong>Output File:</strong> <?php echo htmlspecialchars($clip['output_file_path'], ENT_QUOTES, 'UTF-8'); ?></p>
    <p><strong>Thumbnail:</strong> <?php echo htmlspecialchars($clip['preview_thumbnail_path'], ENT_QUOTES, 'UTF-8'); ?></p>
</div>
<?php endif; ?>
