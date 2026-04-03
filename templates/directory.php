<section class="section">
    <div class="container">
        <div class="section-header">
            <h1 style="font-family:var(--font-heading);font-size:var(--text-3xl);"><?= __('business_directory') ?></h1>
            <p>Browse all businesses and places in <?= clean(config('city')) ?></p>
        </div>

        <!-- Type Filter -->
        <div style="display:flex;justify-content:center;gap:var(--space-3);margin-bottom:var(--space-8);flex-wrap:wrap;">
            <a href="/directory" class="btn <?= !$type_filter ? 'btn-primary' : 'btn-ghost' ?>">All</a>
            <a href="/directory?type=business" class="btn <?= $type_filter === 'business' ? 'btn-primary' : 'btn-ghost' ?>" style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="store" style="width:16px;height:16px;"></i> <?= __('submit_type_business') ?></a>
            <a href="/directory?type=tourism" class="btn <?= $type_filter === 'tourism' ? 'btn-primary' : 'btn-ghost' ?>" style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="mountain" style="width:16px;height:16px;"></i> <?= __('submit_type_tourism') ?></a>
            <a href="/directory?type=creator" class="btn <?= $type_filter === 'creator' ? 'btn-primary' : 'btn-ghost' ?>" style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="video" style="width:16px;height:16px;"></i> Creators</a>
        </div>

        <!-- Category Sidebar + Listings -->
        <div class="directory-layout">
            <!-- Categories Sidebar -->
            <aside>
                <h3 style="font-family:var(--font-heading);margin-bottom:var(--space-4);font-size:var(--text-lg);"><?= __('all_categories') ?></h3>
                <?php foreach ($categories as $cat): ?>
                <a href="<?= category_url($cat) ?>" style="display:flex;align-items:center;justify-content:space-between;padding:var(--space-2) var(--space-3);border-radius:var(--radius-md);color:var(--gray-600);font-size:var(--text-sm);transition:all 150ms;">
                    <span style="display:flex;align-items:center;gap:6px;"><i data-lucide="<?= $cat['icon'] ?? 'folder' ?>" style="width:16px;height:16px;"></i> <?= clean($cat['name']) ?></span>
                    <span class="text-xs text-muted"><?= $cat['listing_count'] ?></span>
                </a>
                <?php endforeach; ?>
            </aside>

            <!-- Listings Grid -->
            <div>
                <?php if (empty($listings)): ?>
                <div class="text-center" style="padding:var(--space-16) 0;">
                    <p style="margin-bottom:var(--space-4);color:var(--gray-400);display:flex;justify-content:center;"><i data-lucide="search" style="width:48px;height:48px;"></i></p>
                    <p class="text-muted"><?= __('no_listings') ?></p>
                    <a href="/submit" class="btn btn-primary mt-4"><?= __('nav_submit') ?></a>
                </div>
                <?php else: ?>
                <div class="grid grid-3">
                    <?php foreach ($listings as $item): ?>
                    <a href="<?= listing_url($item) ?>" class="card listing-card">
                        <?php if ($item['is_featured']): ?>
                        <span class="badge badge-featured"><i data-lucide="star" style="width:14px;height:14px;margin-right:2px;"></i> <?= __('featured') ?></span>
                        <?php endif; ?>
                        <div class="card-img">
                            <?php if ($item['primary_image']): ?>
                            <img src="<?= $item['primary_image'] ?>" alt="<?= clean($item['name']) ?>" loading="lazy">
                            <?php else: ?>
                            <?php
                                $placeholder_map = [
                                    'business' => '/assets/img/placeholders/business.png',
                                    'tourism'  => '/assets/img/placeholders/tourism.png',
                                    'creator'  => '/assets/img/placeholders/creator.png',
                                ];
                                $ph = $placeholder_map[$item['type']] ?? $placeholder_map['business'];
                            ?>
                            <img src="<?= $ph ?>" alt="<?= clean($item['name']) ?>" loading="lazy" style="object-fit:cover;">
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <div class="listing-info">
                                <span class="badge badge-category" style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="<?= $item['category_icon'] ?? 'tag' ?>" style="width:14px;height:14px;"></i> <?= clean($item['category_name']) ?></span>
                                <?php $open = is_open_now($item['hours']); if ($open !== null): ?>
                                <span class="badge <?= $open ? 'badge-open' : 'badge-closed' ?> text-xs">
                                    <?= $open ? __('open_now') : __('closed') ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            <h3 class="card-title"><?= clean($item['name']) ?></h3>
                            <?php if (($item['rating_count'] ?? 0) > 0): ?>
                            <div style="display:flex;align-items:center;gap:4px;margin-bottom:var(--space-1);font-size:var(--text-xs);">
                                <?= render_stars($item['rating_avg'] ?? 0) ?>
                                <span style="font-weight:600;color:var(--gray-700);"><?= number_format($item['rating_avg'] ?? 0, 1) ?></span>
                                <span style="color:var(--gray-400);">(<?= $item['rating_count'] ?? 0 ?>)</span>
                            </div>
                            <?php endif; ?>
                            <p class="card-text"><?= truncate(clean($item['description'] ?? ''), 80) ?></p>
                            <?php if ($item['address']): ?>
                            <div class="card-meta" style="display:flex;align-items:center;gap:4px;"><i data-lucide="map-pin" style="width:14px;height:14px;"></i> <?= clean($item['address']) ?></div>
                            <?php endif; ?>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Pagination -->
                <?php if ($pagination['pages'] > 1): ?>
                <div class="pagination">
                    <?php if ($pagination['has_prev']): ?>
                    <a href="?page=<?= $pagination['current'] - 1 ?><?= $type_filter ? '&type='.$type_filter : '' ?>">← Prev</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $pagination['pages']; $i++): ?>
                    <?php if ($i === $pagination['current']): ?>
                    <span class="active"><?= $i ?></span>
                    <?php else: ?>
                    <a href="?page=<?= $i ?><?= $type_filter ? '&type='.$type_filter : '' ?>"><?= $i ?></a>
                    <?php endif; ?>
                    <?php endfor; ?>
                    <?php if ($pagination['has_next']): ?>
                    <a href="?page=<?= $pagination['current'] + 1 ?><?= $type_filter ? '&type='.$type_filter : '' ?>">Next →</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
