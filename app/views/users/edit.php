<h1>Edit User</h1>

<div class="card">
    <form method="POST" action="/users/<?php echo $editUser['id']; ?>/edit">
        <?php echo CSRF::field(); ?>

        <div class="form-group">
            <label for="first_name" class="form-label">First Name *</label>
            <input type="text" id="first_name" name="first_name" class="form-control" value="<?php echo htmlspecialchars($editUser['first_name'], ENT_QUOTES, 'UTF-8'); ?>" required>
        </div>

        <div class="form-group">
            <label for="last_name" class="form-label">Last Name *</label>
            <input type="text" id="last_name" name="last_name" class="form-control" value="<?php echo htmlspecialchars($editUser['last_name'], ENT_QUOTES, 'UTF-8'); ?>" required>
        </div>

        <div class="form-group">
            <label for="role" class="form-label">Role *</label>
            <select id="role" name="role" class="form-control" required>
                <option value="editor" <?php echo $editUser['role'] === 'editor' ? 'selected' : ''; ?>>Editor</option>
                <option value="viewer" <?php echo $editUser['role'] === 'viewer' ? 'selected' : ''; ?>>Viewer</option>
                <option value="tenant_admin" <?php echo $editUser['role'] === 'tenant_admin' ? 'selected' : ''; ?>>Admin</option>
            </select>
        </div>

        <div class="form-group">
            <label for="status" class="form-label">Status *</label>
            <select id="status" name="status" class="form-control" required>
                <option value="active" <?php echo $editUser['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo $editUser['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                <option value="suspended" <?php echo $editUser['status'] === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
            </select>
        </div>

        <div class="form-group">
            <label for="password" class="form-label">New Password (leave blank to keep current)</label>
            <input type="password" id="password" name="password" class="form-control" minlength="8">
        </div>

        <div class="flex gap-10">
            <button type="submit" class="btn btn-primary">Update User</button>
            <a href="/users" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
