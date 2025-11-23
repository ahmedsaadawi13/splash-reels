<h1>Edit Project</h1>

<div class="card">
    <form method="POST" action="/projects/<?php echo $project['id']; ?>/edit">
        <?php echo CSRF::field(); ?>

        <div class="form-group">
            <label for="name" class="form-label">Project Name *</label>
            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($project['name'], ENT_QUOTES, 'UTF-8'); ?>" required>
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control"><?php echo htmlspecialchars($project['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>

        <div class="form-group">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-control">
                <option value="active" <?php echo $project['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                <option value="archived" <?php echo $project['status'] === 'archived' ? 'selected' : ''; ?>>Archived</option>
            </select>
        </div>

        <div class="flex gap-10">
            <button type="submit" class="btn btn-primary">Update Project</button>
            <a href="/projects/<?php echo $project['id']; ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
