<section class="section">
    <div class="container" style="max-width:900px;">
        <div class="section-header" style="text-align:center;">
            <h1 style="font-family:var(--font-heading);font-size:var(--text-4xl);color:var(--danger);"><i data-lucide="shield-alert"></i> Essential Services</h1>
            <p>Important contacts and emergency numbers for <?= clean(config('city')) ?> residents and visitors.</p>
        </div>

        <div class="grid grid-2" style="margin-top:var(--space-12);gap:var(--space-6);">
            <?php foreach ($emergency as $item): ?>
            <div class="card" style="display:flex;align-items:center;gap:var(--space-6);padding:var(--space-6);border-left:4px solid var(--danger);">
                <div style="background:var(--danger-50);padding:var(--space-4);border-radius:var(--radius-lg);color:var(--danger);">
                    <i data-lucide="<?= $item['category_icon'] ?: 'phone' ?>" style="width:32px;height:32px;"></i>
                </div>
                <div style="flex:1;">
                    <h3 style="font-family:var(--font-heading);margin-bottom:2px;"><?= clean($item['name']) ?></h3>
                    <p class="text-muted text-sm" style="margin-bottom:var(--space-2);"><?= clean($item['address'] ?? 'Tampakan') ?></p>
                    <a href="tel:<?= $item['phone'] ?>" class="btn btn-primary btn-sm" style="background:var(--danger);border-color:var(--danger);">
                        <i data-lucide="phone"></i> <?= clean($item['phone']) ?>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Utility Services -->
        <div style="margin-top:var(--space-16);">
            <h2 style="font-family:var(--font-heading);margin-bottom:var(--space-6);text-align:center;">Utility & Other Services</h2>
            <div class="grid grid-3">
                <div class="card p-6 text-center">
                    <i data-lucide="zap" style="width:24px;height:24px;margin-bottom:var(--space-3);color:var(--primary);"></i>
                    <h3>SOCOTECO</h3>
                    <p class="text-sm text-muted">Electric Cooperative</p>
                </div>
                <div class="card p-6 text-center">
                    <i data-lucide="droplet" style="width:24px;height:24px;margin-bottom:var(--space-3);color:var(--primary);"></i>
                    <h3>Water District</h3>
                    <p class="text-sm text-muted">Municipal Water Supply</p>
                </div>
                <div class="card p-6 text-center">
                    <i data-lucide="mail" style="width:24px;height:24px;margin-bottom:var(--space-3);color:var(--primary);"></i>
                    <h3>Post Office</h3>
                    <p class="text-sm text-muted">PHLPost <?= clean(config('city')) ?></p>
                </div>
            </div>
        </div>

        <div class="alert alert-info" style="margin-top:var(--space-12);text-align:center;">
            <p><strong>Missing something?</strong> If you manage an essential service in <?= clean(config('city')) ?>, please <a href="/submit">submit it here</a> for inclusion.</p>
        </div>
    </div>
</section>
