<h1><?php echo isset($brandKit) ? 'Edit' : 'Create'; ?> Brand Kit</h1>

<div class="card">
    <form method="POST" action="<?php echo isset($brandKit) ? '/brand-kits/' . $brandKit['id'] . '/edit' : '/brand-kits/create'; ?>" enctype="multipart/form-data">
        <?php echo CSRF::field(); ?>

        <div class="form-group">
            <label for="name" class="form-label">Name *</label>
            <input type="text" id="name" name="name" class="form-control" value="<?php echo isset($brandKit) ? htmlspecialchars($brandKit['name'], ENT_QUOTES, 'UTF-8') : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="primary_color" class="form-label">Primary Color</label>
            <input type="color" id="primary_color" name="primary_color" class="form-control" value="<?php echo isset($brandKit) ? htmlspecialchars($brandKit['primary_color'], ENT_QUOTES, 'UTF-8') : '#000000'; ?>">
        </div>

        <div class="form-group">
            <label for="secondary_color" class="form-label">Secondary Color</label>
            <input type="color" id="secondary_color" name="secondary_color" class="form-control" value="<?php echo isset($brandKit) ? htmlspecialchars($brandKit['secondary_color'], ENT_QUOTES, 'UTF-8') : '#FFFFFF'; ?>">
        </div>

        <div class="form-group">
            <label for="accent_color" class="form-label">Accent Color</label>
            <input type="color" id="accent_color" name="accent_color" class="form-control" value="<?php echo isset($brandKit) ? htmlspecialchars($brandKit['accent_color'], ENT_QUOTES, 'UTF-8') : '#FF0000'; ?>">
        </div>

        <div class="form-group">
            <label for="font_family" class="form-label">Font Family</label>
            <select id="font_family" name="font_family" class="form-control">
                <option value="Arial" <?php echo (isset($brandKit) && $brandKit['font_family'] === 'Arial') ? 'selected' : ''; ?>>Arial</option>
                <option value="Helvetica" <?php echo (isset($brandKit) && $brandKit['font_family'] === 'Helvetica') ? 'selected' : ''; ?>>Helvetica</option>
                <option value="Inter" <?php echo (isset($brandKit) && $brandKit['font_family'] === 'Inter') ? 'selected' : ''; ?>>Inter</option>
                <option value="Roboto" <?php echo (isset($brandKit) && $brandKit['font_family'] === 'Roboto') ? 'selected' : ''; ?>>Roboto</option>
                <option value="Poppins" <?php echo (isset($brandKit) && $brandKit['font_family'] === 'Poppins') ? 'selected' : ''; ?>>Poppins</option>
            </select>
        </div>

        <div class="form-group">
            <label for="logo" class="form-label">Logo</label>
            <input type="file" id="logo" name="logo" class="form-control" accept="image/*">
            <?php if (isset($brandKit) && $brandKit['logo_path']): ?>
                <p>Current logo: <?php echo htmlspecialchars($brandKit['logo_path'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
        </div>

        <div class="flex gap-10">
            <button type="submit" class="btn btn-primary"><?php echo isset($brandKit) ? 'Update' : 'Create'; ?></button>
            <a href="/brand-kits" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
