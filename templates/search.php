<section class="section">
    <div class="container">
        <div class="section-header">
            <h1 style="font-family:var(--font-heading);font-size:var(--text-3xl);"><?= __('search_results') ?></h1>
            <?php if ($query): ?>
            <p><?= __($total > 0 ? 'search_showing' : 'search_no_results', ['count' => $total, 'query' => clean($query)]) ?></p>
            <?php endif; ?>
        </div>

        <!-- Search Bar -->
        <form class="search-bar" action="/search" method="GET" style="margin:0 auto var(--space-8);max-width:560px;box-shadow:var(--shadow-md);">
            <input type="text" name="q" placeholder="<?= __('search_placeholder') ?>" value="<?= clean($query) ?>" autofocus>
            <button type="submit"><?= __('search_button') ?></button>
        </form>

        <?php if (!empty($listings)): ?>
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
                    <div class="listing-info">
                        <span class="badge badge-category"><?= $item['category_icon'] ?? '' ?> <?= clean($item['category_name']) ?></span>
                    </div>
                    <h3 class="card-title"><?= clean($item['name']) ?></h3>
                    <p class="card-text"><?= truncate(clean($item['description'] ?? ''), 80) ?></p>
                    <?php if ($item['address']): ?><div class="card-meta">📍 <?= clean($item['address']) ?></div><?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
