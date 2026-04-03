<section class="section" style="min-height:80vh;display:flex;align-items:center;">
    <div class="container" style="max-width:450px;">
        <div class="card p-8" style="width:100%;">
            <div style="text-align:center;margin-bottom:var(--space-8);">
                <h1 style="font-family:var(--font-heading);font-size:var(--text-3xl);">Create Account</h1>
                <p class="text-muted">Join the <?= clean(config('city')) ?> community today.</p>
            </div>

            <form action="/register" method="POST" autocomplete="on">
                <?= csrf_field() ?>
                
                <div class="form-group">
                    <label class="form-label" for="reg-name">Full Name</label>
                    <input type="text" name="name" id="reg-name" class="form-input" required placeholder="Juana Dela Cruz" autocomplete="name">
                </div>

                <div class="form-group">
                    <label class="form-label" for="reg-email">Email Address</label>
                    <input type="email" name="email" id="reg-email" class="form-input" required placeholder="juana@example.com" autocomplete="email">
                </div>

                <div class="form-group">
                    <label class="form-label" for="reg-password">Password</label>
                    <div style="position:relative;">
                        <input type="password" name="password" id="reg-password" class="form-input" required placeholder="••••••••" minlength="8" autocomplete="new-password" style="padding-right:48px;">
                        <button type="button" onclick="togglePassword('reg-password', this)" aria-label="Show password" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:6px;color:var(--gray-400);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="reg-confirm">Confirm Password</label>
                    <div style="position:relative;">
                        <input type="password" name="password_confirm" id="reg-confirm" class="form-input" required placeholder="••••••••" autocomplete="new-password" style="padding-right:48px;">
                        <button type="button" onclick="togglePassword('reg-confirm', this)" aria-label="Show password" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:6px;color:var(--gray-400);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
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

<script>
function togglePassword(id, btn) {
    const input = document.getElementById(id);
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    btn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
    btn.innerHTML = isHidden
        ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>'
        : '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
}
</script>
