<section class="section">
    <div class="container">
        <div class="section-header">
            <h1 style="font-family:var(--font-heading);font-size:var(--text-4xl);">Community Hub</h1>
            <p>Connect with local creators, stories, and community initiatives in <?= clean(config('city')) ?>.</p>
        </div>

        <!-- Latest Stories -->
        <div style="margin-bottom:var(--space-16);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--space-6);">
                <h2 style="font-family:var(--font-heading);">Community Stories</h2>
                <a href="/community/blog" class="btn btn-ghost">View All Blog →</a>
            </div>
            <div class="grid grid-3">
                <?php foreach ($posts as $post): ?>
                <a href="/community/blog/<?= $post['slug'] ?>" class="card">
                    <div class="card-img" style="aspect-ratio:16/9;">
                        <?php if ($post['featured_image']): ?>
                        <img src="<?= $post['featured_image'] ?>" alt="<?= clean($post['title']) ?>" loading="lazy">
                        <?php else: ?>
                        <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--gray-100),var(--gray-200));display:flex;align-items:center;justify-content:center;">
                            <i data-lucide="pen-tool" style="width:24px;height:24px;color:var(--gray-300);"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title"><?= clean($post['title']) ?></h3>
                        <p class="card-text text-sm"><?= truncate(clean($post['excerpt'] ?: $post['content']), 80) ?></p>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Community Creators -->
        <div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--space-6);">
                <h2 style="font-family:var(--font-heading);">Local Vloggers & Creators</h2>
                <a href="/community/vloggers" class="btn btn-ghost">Meet All Creators →</a>
            </div>
            <div class="grid grid-4">
                <?php foreach ($vloggers as $item): ?>
                <a href="<?= listing_url($item) ?>" class="card">
                    <div class="card-img" style="aspect-ratio:4/3;">
                        <?php if ($item['primary_image']): ?>
                        <img src="<?= $item['primary_image'] ?>" alt="<?= clean($item['name']) ?>" loading="lazy">
                        <?php else: ?>
                        <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--primary-100),var(--primary-200));display:flex;align-items:center;justify-content:center;">
                            <i data-lucide="video" style="width:24px;height:24px;color:rgba(255,255,255,0.5);"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body" style="text-align:center;">
                        <h3 class="card-title"><?= clean($item['name']) ?></h3>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
