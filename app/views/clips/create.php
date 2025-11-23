<h1>Create New Clip</h1>

<div class="card">
    <form method="POST" action="/clips/create">
        <?php echo CSRF::field(); ?>

        <div class="form-group">
            <label for="project_id" class="form-label">Project *</label>
            <select id="project_id" name="project_id" class="form-control" required>
                <option value="">Select a project</option>
                <?php foreach ($projects as $project): ?>
                    <option value="<?php echo $project['id']; ?>"><?php echo htmlspecialchars($project['name'], ENT_QUOTES, 'UTF-8'); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="media_file_id" class="form-label">Media File *</label>
            <select id="media_file_id" name="media_file_id" class="form-control" required>
                <option value="">Select media file</option>
            </select>
        </div>

        <div class="form-group">
            <label for="title" class="form-label">Title *</label>
            <input type="text" id="title" name="title" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control"></textarea>
        </div>

        <div class="form-group">
            <label for="start_seconds" class="form-label">Start Time (seconds) *</label>
            <input type="number" id="start_seconds" name="start_seconds" class="form-control" value="0" required>
        </div>

        <div class="form-group">
            <label for="end_seconds" class="form-label">End Time (seconds) *</label>
            <input type="number" id="end_seconds" name="end_seconds" class="form-control" value="30" required>
        </div>

        <div class="form-group">
            <label for="clip_template_id" class="form-label">Template</label>
            <select id="clip_template_id" name="clip_template_id" class="form-control">
                <option value="">Default</option>
                <?php foreach ($templates as $template): ?>
                    <option value="<?php echo $template['id']; ?>"><?php echo htmlspecialchars($template['name'], ENT_QUOTES, 'UTF-8'); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="platform_hint" class="form-label">Platform</label>
            <select id="platform_hint" name="platform_hint" class="form-control">
                <option value="generic">Generic</option>
                <option value="tiktok">TikTok</option>
                <option value="reels">Instagram Reels</option>
                <option value="shorts">YouTube Shorts</option>
            </select>
        </div>

        <div class="flex gap-10">
            <button type="submit" class="btn btn-primary">Create Clip</button>
            <a href="/clips" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
