<div class="flex justify-between items-center mb-20">
    <h1>Brand Kits</h1>
    <a href="/brand-kits/create" class="btn btn-primary">Create Brand Kit</a>
</div>

<div class="card">
    <?php if (empty($brandKits)): ?>
        <p>No brand kits yet. <a href="/brand-kits/create">Create your first brand kit</a></p>
    <?php else: ?>
        <?php foreach ($brandKits as $kit): ?>
            <div style="border-bottom: 1px solid #e5e5e5; padding: 20px 0;">
                <h3><?php echo htmlspecialchars($kit['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <div class="flex gap-10" style="margin: 10px 0;">
                    <div>
                        <strong>Primary:</strong>
                        <span style="display: inline-block; width: 30px; height: 30px; background-color: <?php echo htmlspecialchars($kit['primary_color'], ENT_QUOTES, 'UTF-8'); ?>; border: 1px solid #ccc;"></span>
                        <?php echo htmlspecialchars($kit['primary_color'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                    <div>
                        <strong>Secondary:</strong>
                        <span style="display: inline-block; width: 30px; height: 30px; background-color: <?php echo htmlspecialchars($kit['secondary_color'], ENT_QUOTES, 'UTF-8'); ?>; border: 1px solid #ccc;"></span>
                        <?php echo htmlspecialchars($kit['secondary_color'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                    <div>
                        <strong>Accent:</strong>
                        <span style="display: inline-block; width: 30px; height: 30px; background-color: <?php echo htmlspecialchars($kit['accent_color'], ENT_QUOTES, 'UTF-8'); ?>; border: 1px solid #ccc;"></span>
                        <?php echo htmlspecialchars($kit['accent_color'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                </div>
                <p><strong>Font:</strong> <?php echo htmlspecialchars($kit['font_family'], ENT_QUOTES, 'UTF-8'); ?></p>
                <a href="/brand-kits/<?php echo $kit['id']; ?>/edit" class="btn btn-small btn-secondary">Edit</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
