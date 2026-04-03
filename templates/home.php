<!-- Hero Section -->
<section class="hero">
    <div class="container hero-content fade-in">
        <h1><?= __('hero_title', ['city' => clean($city)]) ?></h1>
        <p><?= __('hero_subtitle', ['city' => clean($city), 'province' => clean($province)]) ?></p>

        <form class="search-bar" action="/search" method="GET">
            <input type="text" name="q" placeholder="<?= __('search_placeholder') ?>" autocomplete="off">
            <button type="submit"><?= __('search_button') ?></button>
        </form>

        <div class="hero-ctas">
            <a href="/directory" class="btn btn-primary btn-lg"><?= __('hero_cta') ?></a>
            <a href="/submit" class="btn btn-outline btn-lg"><?= __('hero_cta2') ?></a>
        </div>
    </div>
</section>

<!-- Quick Stats -->
<section class="section-alt" style="padding: var(--space-8) 0;">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card text-center">
                <div class="stat-value"><?= number_format($total_listings) ?></div>
                <div class="stat-label"><?= __('total_listings', ['count' => $total_listings]) ?: 'Total Listings' ?></div>
            </div>
            <div class="stat-card text-center">
                <div class="stat-value"><?= number_format($total_businesses) ?></div>
                <div class="stat-label"><?= __('business_directory') ?></div>
            </div>
            <div class="stat-card text-center">
                <div class="stat-value"><?= number_format($total_tourism) ?></div>
                <div class="stat-label"><?= __('tourism_directory') ?></div>
            </div>
            <div class="stat-card text-center">
                <div class="stat-value"><?= count($categories) ?></div>
                <div class="stat-label"><?= __('all_categories') ?></div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Listings -->
<?php if (!empty($featured)): ?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2><i data-lucide="star"></i> <?= __('featured') ?></h2>
            <p><?= __('featured_subtitle', ['city' => clean($city)]) ?: 'Top-rated businesses and places in ' . clean($city) ?></p>
        </div>
        <div class="grid grid-3">
            <?php foreach ($featured as $item): ?>
            <a href="<?= listing_url($item) ?>" class="card listing-card fade-in">
                <span class="badge badge-featured"><i data-lucide="star" style="width:14px;height:14px;margin-right:2px;"></i> <?= __('featured') ?></span>
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
                        <span class="badge <?= $open ? 'badge-open' : 'badge-closed' ?>">
                            <?= $open ? __('open_now') : __('closed') ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <h3 class="card-title"><?= clean($item['name']) ?></h3>
                    <p class="card-text"><?= truncate(clean($item['description'] ?? ''), 100) ?></p>
                    <?php if ($item['address']): ?>
                    <div class="card-meta" style="display:flex;align-items:center;gap:4px;"><i data-lucide="map-pin" style="width:14px;height:14px;"></i> <?= clean($item['address']) ?></div>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Browse Categories -->
<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <h2><?= __('all_categories') ?></h2>
            <p>Find exactly what you're looking for in <?= clean($city) ?></p>
        </div>
        <div class="category-grid">
            <?php foreach ($categories as $cat): ?>
            <a href="<?= category_url($cat) ?>" class="category-card">
                <div class="category-icon" style="display:flex;align-items:center;justify-content:center;"><i data-lucide="<?= $cat['icon'] ?? 'folder' ?>" style="width:24px;height:24px;"></i></div>
                <div>
                    <div class="category-name"><?= clean($cat['name']) ?></div>
                    <div class="category-count"><?= $cat['listing_count'] ?> <?= $cat['listing_count'] == 1 ? 'listing' : 'listings' ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Latest Listings -->
<?php if (!empty($latest)): ?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2><i data-lucide="sparkles"></i> Recently Added</h2>
            <p>The newest businesses and places in <?= clean($city) ?></p>
        </div>
        <div class="grid grid-3">
            <?php foreach ($latest as $item): ?>
            <a href="<?= listing_url($item) ?>" class="card listing-card">
                <div class="card-img">
                    <?php if ($item['primary_image']): ?>
                    <img src="<?= $item['primary_image'] ?>" alt="<?= clean($item['name']) ?>" loading="lazy">
                    <?php else: ?>
                    <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--gray-100),var(--gray-200));display:flex;align-items:center;justify-content:center;font-size:3rem;">
                        <i data-lucide="<?= $item['category_icon'] ?? 'map-pin' ?>"></i>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="listing-info">
                        <span class="badge badge-category" style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="<?= $item['category_icon'] ?? 'tag' ?>" style="width:14px;height:14px;"></i> <?= clean($item['category_name']) ?></span>
                    </div>
                    <h3 class="card-title"><?= clean($item['name']) ?></h3>
                    <p class="card-text"><?= truncate(clean($item['description'] ?? ''), 100) ?></p>
                    <div class="card-meta">
                        <span style="display:flex;align-items:center;gap:4px;"><i data-lucide="map-pin" style="width:14px;height:14px;"></i> <?= clean($item['address'] ?? $item['barangay'] ?? '') ?></span>
                        <span>• <?= time_ago($item['created_at']) ?></span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-8">
            <a href="/directory" class="btn btn-ghost"><?= __('view_all') ?> →</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Emergency Numbers -->
<?php $emergency = config('emergency_numbers'); if (!empty($emergency)): ?>
<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <h2><i data-lucide="alert-triangle"></i> <?= __('emergency_numbers') ?></h2>
            <p><?= __('essential_subtitle', ['city' => clean($city)]) ?></p>
        </div>
        <div class="emergency-grid">
            <?php foreach ($emergency as $svc): ?>
            <div class="emergency-card">
                <div class="emergency-icon" style="display:flex;align-items:center;justify-content:center;"><i data-lucide="phone"></i></div>
                <div>
                    <div class="emergency-label"><?= clean($svc['label']) ?></div>
                    <div class="emergency-number">
                        <a href="tel:<?= preg_replace('/[^0-9+]/', '', $svc['number']) ?>"><?= clean($svc['number']) ?></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-8">
            <a href="/essential-services" class="btn btn-ghost"><?= __('view_all') ?> <?= __('nav_essential') ?> →</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Latest Blog Posts -->
<?php if (!empty($posts)): ?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2><i data-lucide="pen-tool"></i> <?= __('latest_posts') ?></h2>
        </div>
        <div class="grid grid-3">
            <?php foreach ($posts as $post): ?>
            <a href="/community/blog/<?= $post['slug'] ?>" class="card">
                <?php if ($post['featured_image']): ?>
                <div class="card-img">
                    <img src="<?= $post['featured_image'] ?>" alt="<?= clean($post['title']) ?>" loading="lazy">
                </div>
                <?php endif; ?>
                <div class="card-body">
                    <h3 class="card-title"><?= clean($post['title']) ?></h3>
                    <p class="card-text"><?= truncate(clean($post['excerpt'] ?? ''), 120) ?></p>
                    <div class="card-meta">
                        <span><?= clean($post['author_name'] ?? 'Community') ?></span>
                        <span>• <?= format_date($post['published_at'] ?? $post['created_at']) ?></span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="hero" style="padding: var(--space-12) 0;">
    <div class="container hero-content">
        <h2 style="font-family: var(--font-heading); font-size: var(--text-3xl); font-weight: 700; color: white; margin-bottom: var(--space-4);">
            <?= __('hero_cta2') ?>
        </h2>
        <p style="color: rgba(255,255,255,0.8); margin-bottom: var(--space-6);">
            It's free to list your business. Get found by locals and tourists.
        </p>
        <a href="/submit" class="btn btn-accent btn-lg"><?= __('nav_submit') ?> →</a>
    </div>
</section>
