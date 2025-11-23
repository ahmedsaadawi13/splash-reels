<h1>Upload Media</h1>

<div class="card">
    <form method="POST" action="/media/upload" enctype="multipart/form-data">
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
            <label for="title" class="form-label">Title *</label>
            <input type="text" id="title" name="title" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control"></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Source Type *</label>
            <div>
                <label>
                    <input type="radio" name="source_type" value="upload" checked onchange="toggleUploadMethod()"> Upload File
                </label>
                <label style="margin-left: 20px;">
                    <input type="radio" name="source_type" value="youtube_url" onchange="toggleUploadMethod()"> YouTube URL
                </label>
            </div>
        </div>

        <div id="file-upload-section" class="form-group">
            <label for="video_file" class="form-label">Video File</label>
            <input type="file" id="video_file" name="video_file" class="form-control" accept="video/*">
        </div>

        <div id="url-upload-section" class="form-group" style="display: none;">
            <label for="source_url" class="form-label">YouTube URL</label>
            <input type="url" id="source_url" name="source_url" class="form-control" placeholder="https://youtube.com/watch?v=...">
        </div>

        <div class="flex gap-10">
            <button type="submit" class="btn btn-primary">Upload</button>
            <a href="/media" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
