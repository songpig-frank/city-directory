<section class="section">
    <div class="container">
        <div class="section-header">
            <h1 style="font-family:var(--font-heading);font-size:var(--text-3xl);">
                <?= $category['icon'] ?? '📁' ?> <?= clean($category['name']) ?>
            </h1>
            <?php if ($category['description']): ?>
            <p><?= clean($category['description']) ?></p>
            <?php endif; ?>
            <p class="text-muted text-sm"><?= $pagination['total'] ?> listings</p>
        </div>

        <div style="display:flex;gap:var(--space-3);margin-bottom:var(--space-6);">
            <a href="/directory" class="btn btn-ghost btn-sm">← All Categories</a>
            <a href="/submit" class="btn btn-primary btn-sm"><?= __('nav_submit') ?></a>
        </div>

        <?php if (empty($listings)): ?>
        <div class="text-center" style="padding:var(--space-16) 0;">
            <p style="font-size:var(--text-4xl);margin-bottom:var(--space-4);">🔍</p>
            <p class="text-muted"><?= __('no_listings') ?></p>
            <a href="/submit" class="btn btn-primary mt-4"><?= __('nav_submit') ?></a>
        </div>
        <?php else: ?>
        <div class="grid grid-3">
            <?php foreach ($listings as $item): ?>
            <a href="<?= listing_url($item) ?>" class="card listing-card">
                <?php if ($item['is_featured']): ?><span class="badge badge-featured">⭐ <?= __('featured') ?></span><?php endif; ?>
                <div class="card-img">
                    <?php if ($item['primary_image']): ?>
                    <img src="<?= $item['primary_image'] ?>" alt="<?= clean($item['name']) ?>" loading="lazy">
                    <?php else: ?>
                    <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--primary-100),var(--primary-200));display:flex;align-items:center;justify-content:center;font-size:3rem;"><?= $item['category_icon'] ?? '📍' ?></div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <h3 class="card-title"><?= clean($item['name']) ?></h3>
                    <p class="card-text"><?= truncate(clean($item['description'] ?? ''), 80) ?></p>
                    <?php if ($item['address']): ?><div class="card-meta">📍 <?= clean($item['address']) ?></div><?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <?php if ($pagination['pages'] > 1): ?>
        <div class="pagination">
            <?php if ($pagination['has_prev']): ?><a href="?page=<?= $pagination['current'] - 1 ?>">← Prev</a><?php endif; ?>
            <?php for ($i = 1; $i <= $pagination['pages']; $i++): ?>
            <?php if ($i === $pagination['current']): ?><span class="active"><?= $i ?></span>
            <?php else: ?><a href="?page=<?= $i ?>"><?= $i ?></a><?php endif; ?>
            <?php endfor; ?>
            <?php if ($pagination['has_next']): ?><a href="?page=<?= $pagination['current'] + 1 ?>">Next →</a><?php endif; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
