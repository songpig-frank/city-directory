<section class="section" style="min-height:60vh;display:flex;align-items:center;">
    <div class="container" style="max-width:400px;">
        <div class="text-center" style="margin-bottom:var(--space-8);">
            <h1 style="font-family:var(--font-heading);font-size:var(--text-3xl);"><?= __('login_title') ?></h1>
            <p class="text-muted"><?= clean(config('site_name')) ?></p>
        </div>

        <form method="POST" action="/login<?= !empty($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : '' ?>" class="card" style="padding:var(--space-8);">
            <?= csrf_field() ?>

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

        <div style="text-align:center;margin-top:var(--space-6);font-size:var(--text-sm);">
            Don't have an account? <a href="/register" style="font-weight:600;color:var(--primary);">Create one</a>
        </div>
    </div>
</section>
