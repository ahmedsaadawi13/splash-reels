<h1>Add New User</h1>

<div class="card">
    <form method="POST" action="/users/create">
        <?php echo CSRF::field(); ?>

        <div class="form-group">
            <label for="first_name" class="form-label">First Name *</label>
            <input type="text" id="first_name" name="first_name" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="last_name" class="form-label">Last Name *</label>
            <input type="text" id="last_name" name="last_name" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email *</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password *</label>
            <input type="password" id="password" name="password" class="form-control" minlength="8" required>
        </div>

        <div class="form-group">
            <label for="role" class="form-label">Role *</label>
            <select id="role" name="role" class="form-control" required>
                <option value="editor">Editor</option>
                <option value="viewer">Viewer</option>
                <option value="tenant_admin">Admin</option>
            </select>
        </div>

        <div class="flex gap-10">
            <button type="submit" class="btn btn-primary">Create User</button>
            <a href="/users" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
