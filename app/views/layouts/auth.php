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
    <div class="auth-container">
        <div class="auth-card">
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
    </div>
    <script src="/assets/js/app.js"></script>
</body>
</html>
