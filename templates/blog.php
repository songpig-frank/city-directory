<section class="section" style="padding-top:var(--space-12);">
    <div class="container">
        <div class="section-header">
            <h1 style="font-family:var(--font-heading);font-size:var(--text-4xl);"><?= __('community_stories') ?></h1>
            <p>Gathering news, events, and highlights from around <?= clean(config('city')) ?>.</p>
        </div>

        <?php if (empty($posts)): ?>
        <div class="text-center" style="padding:var(--space-16) 0;">
            <p class="text-muted">No stories have been shared yet. Check back soon!</p>
        </div>
        <?php else: ?>
        <div class="grid grid-3">
            <?php foreach ($posts as $post): ?>
            <a href="/community/blog/<?= $post['slug'] ?>" class="card fade-in">
                <div class="card-img" style="aspect-ratio:16/9;">
                    <?php if ($post['featured_image']): ?>
                    <img src="<?= $post['featured_image'] ?>" alt="<?= clean($post['title']) ?>" loading="lazy">
                    <?php else: ?>
                    <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--gray-100),var(--gray-200));display:flex;align-items:center;justify-content:center;">
                        <i data-lucide="pen-tool" style="width:32px;height:32px;color:var(--gray-300);"></i>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <span class="text-xs text-muted"><?= date('M j, Y', strtotime($post['created_at'])) ?></span>
                    <h2 class="card-title" style="margin-top:var(--space-1);"><?= clean($post['title']) ?></h2>
                    <p class="card-text text-sm"><?= clean($post['excerpt'] ?: truncate($post['content'], 120)) ?></p>
                    <span class="btn btn-ghost btn-sm" style="margin-top:var(--space-4);padding-left:0;">Read More →</span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <?php if ($pagination['pages'] > 1): ?>
        <div class="pagination">
            <?php if ($pagination['current'] > 1): ?>
            <a href="?page=<?= $pagination['current'] - 1 ?>">← Previous</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $pagination['pages']; $i++): ?>
            <a href="?page=<?= $i ?>" class="<?= $i === $pagination['current'] ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <?php if ($pagination['current'] < $pagination['pages']): ?>
            <a href="?page=<?= $pagination['current'] + 1 ?>">Next →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
