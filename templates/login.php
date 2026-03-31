<section class="section" style="min-height:60vh;display:flex;align-items:center;">
    <div class="container" style="max-width:400px;">
        <div class="text-center" style="margin-bottom:var(--space-8);">
            <h1 style="font-family:var(--font-heading);font-size:var(--text-3xl);"><?= __('login_title') ?></h1>
            <p class="text-muted"><?= clean(config('site_name')) ?></p>
        </div>

        <form method="POST" action="/login<?= !empty($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : '' ?>" class="card" style="padding:var(--space-8);">
            <?= csrf_field() ?>

            <!-- Quick Test Login (Dev Only) -->
            <div style="background:var(--primary-50); padding:var(--space-3); border-radius:var(--radius-md); margin-bottom:var(--space-4); border:1px solid var(--primary-200); color:var(--text-primary);">
                <p style="font-size:var(--text-sm); font-weight:600; margin-bottom:var(--space-2); color:var(--primary-light);">🧪 Quick Test Login</p>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <label style="font-size:var(--text-sm); display:flex; align-items:center; gap:8px; cursor:pointer; color:var(--text-primary);">
                        <input type="radio" name="test_login" onclick="document.getElementById('email').value='admin@tampakan.com';document.getElementById('password').value='admin123';">
                        <span><strong>Admin</strong> </span>
                    </label>
                    <label style="font-size:var(--text-sm); display:flex; align-items:center; gap:8px; cursor:pointer; color:var(--text-primary);">
                        <input type="radio" name="test_login" onclick="document.getElementById('email').value='gerame.paquera@tampakan.com';document.getElementById('password').value='gerame2024';">
                        <span><strong>Business / Creator</strong> (Gerame)</span>
                    </label>
                    <label style="font-size:var(--text-sm); display:flex; align-items:center; gap:8px; cursor:pointer; color:var(--text-primary);">
                        <input type="radio" name="test_login" onclick="document.getElementById('email').value='juan@example.com';document.getElementById('password').value='user123';">
                        <span><strong>Standard User</strong> (Juan)</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label"><?= __('email') ?></label>
                <input type="email" name="email" id="email" class="form-input" required autofocus>
            </div>
            <div class="form-group">
                <label class="form-label"><?= __('password') ?></label>
                <input type="password" name="password" id="password" class="form-input" required>
            </div>
            <button type="submit" class="btn btn-primary btn-lg" style="width:100%;"><?= __('login_button') ?></button>
        </form>
    </div>
</section>
