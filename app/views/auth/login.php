<h2 class="auth-title">Login to SplashReels</h2>

<form method="POST" action="/auth/login">
    <?php echo CSRF::field(); ?>

    <div class="form-group">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="password" class="form-label">Password</label>
        <input type="password" id="password" name="password" class="form-control" required>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
    </div>

    <div class="text-center">
        <p><a href="/auth/forgot-password">Forgot password?</a></p>
        <p>Don't have an account? <a href="/auth/register">Register here</a></p>
    </div>
</form>
