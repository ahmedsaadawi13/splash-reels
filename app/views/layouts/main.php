<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . ' - ' : ''; ?>SplashReels</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <?php echo CSRF::meta(); ?>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="/dashboard" class="logo">SplashReels</a>
                <nav class="nav">
                    <ul>
                        <li><a href="/dashboard">Dashboard</a></li>
                        <li><a href="/projects">Projects</a></li>
                        <li><a href="/media">Media</a></li>
                        <li><a href="/clips">Clips</a></li>
                        <li><a href="/brand-kits">Brand Kits</a></li>
                        <?php if (Auth::hasRole(array('tenant_admin'))): ?>
                            <li><a href="/billing">Billing</a></li>
                            <li><a href="/users">Users</a></li>
                        <?php endif; ?>
                        <?php if (Auth::hasRole(array('platform_admin'))): ?>
                            <li><a href="/admin/tenants">Admin</a></li>
                        <?php endif; ?>
                        <li><a href="/auth/logout">Logout</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <?php if (Session::has('_flash_success')): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars(Session::getFlash('success'), ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <?php if (Session::has('_flash_error')): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars(Session::getFlash('error'), ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <?php echo $content; ?>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> SplashReels. All rights reserved.</p>
        </div>
    </footer>

    <script src="/assets/js/app.js"></script>
</body>
</html>
