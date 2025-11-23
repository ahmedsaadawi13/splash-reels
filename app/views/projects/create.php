<h1>Create New Project</h1>

<div class="card">
    <form method="POST" action="/projects/create">
        <?php echo CSRF::field(); ?>

        <div class="form-group">
            <label for="name" class="form-label">Project Name *</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control"></textarea>
        </div>

        <div class="flex gap-10">
            <button type="submit" class="btn btn-primary">Create Project</button>
            <a href="/projects" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
