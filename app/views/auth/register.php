<h2 class="auth-title">Create Your Account</h2>

<form method="POST" action="/auth/register">
    <?php echo CSRF::field(); ?>

    <div class="form-group">
        <label for="company_name" class="form-label">Company Name</label>
        <input type="text" id="company_name" name="company_name" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="first_name" class="form-label">First Name</label>
        <input type="text" id="first_name" name="first_name" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="last_name" class="form-label">Last Name</label>
        <input type="text" id="last_name" name="last_name" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="password" class="form-label">Password (min 8 characters)</label>
        <input type="password" id="password" name="password" class="form-control" required minlength="8">
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary" style="width: 100%;">Create Account</button>
    </div>

    <div class="text-center">
        <p>Already have an account? <a href="/auth/login">Login here</a></p>
    </div>
</form>
