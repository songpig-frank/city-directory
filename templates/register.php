<section class="section" style="min-height:80vh;display:flex;align-items:center;">
    <div class="container" style="max-width:450px;">
        <div class="card p-8" style="width:100%;">
            <div style="text-align:center;margin-bottom:var(--space-8);">
                <h1 style="font-family:var(--font-heading);font-size:var(--text-3xl);">Create Account</h1>
                <p class="text-muted">Join the <?= clean(config('city')) ?> community today.</p>
            </div>

            <form action="/register" method="POST">
                <?= csrf_field() ?>
                
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-input" required placeholder="Juana Dela Cruz">
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-input" required placeholder="juana@example.com">
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input" required placeholder="••••••••" minlength="8">
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirm" class="form-input" required placeholder="••••••••">
                </div>

                <div style="margin-bottom:var(--space-6);">
                    <label style="display:flex;align-items:flex-start;gap:var(--space-2);font-size:var(--text-sm);cursor:pointer;">
                        <input type="checkbox" required style="margin-top:3px;">
                        <span>I agree to the <a href="/terms" target="_blank">Terms of Service</a> and <a href="/privacy" target="_blank">Privacy Policy</a> of <?= clean(config('site_name')) ?>.</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-lg" style="width:100%;">Create Account</button>
            </form>

            <div style="text-align:center;margin-top:var(--space-8);font-size:var(--text-sm);">
                Already have an account? <a href="/login" style="font-weight:600;color:var(--primary);">Sign In</a>
            </div>
        </div>
    </div>
</section>
