<h2 class="auth-title">Reset Password</h2>

<form method="POST" action="/auth/forgot-password">
    <?php echo CSRF::field(); ?>

    <div class="form-group">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" class="form-control" required>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary" style="width: 100%;">Send Reset Link</button>
    </div>

    <div class="text-center">
        <p><a href="/auth/login">Back to login</a></p>
    </div>
</form>
