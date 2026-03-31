<article class="section" style="padding-top:var(--space-12);">
    <div class="container">
        <!-- Breadcrumbs -->
        <nav style="margin-bottom:var(--space-6);font-size:var(--text-sm);">
            <a href="/community/blog" style="color:var(--gray-500);">Blog</a>
            <span style="margin:0 var(--space-2);color:var(--gray-300);">/</span>
            <span class="text-muted"><?= clean($post['title']) ?></span>
        </nav>

        <header style="max-width:800px;margin:0 auto var(--space-8) auto;text-align:center;">
            <h1 style="font-family:var(--font-heading);font-size:var(--text-4xl);line-height:1.2;margin-bottom:var(--space-4);"><?= clean($post['title']) ?></h1>
            <div style="display:flex;align-items:center;justify-content:center;gap:var(--space-3);color:var(--gray-500);font-size:var(--text-sm);">
                <span><i data-lucide="calendar" style="width:14px;height:14px;vertical-align:middle;margin-right:2px;"></i> <?= date('F j, Y', strtotime($post['created_at'])) ?></span>
                <span>•</span>
                <span><i data-lucide="user" style="width:14px;height:14px;vertical-align:middle;margin-right:2px;"></i> Community Team</span>
            </div>
        </header>

        <?php if ($post['featured_image']): ?>
        <div style="max-width:1000px;margin:0 auto var(--space-10) auto;border-radius:var(--radius-xl);overflow:hidden;aspect-ratio:21/9;">
            <img src="<?= $post['featured_image'] ?>" alt="<?= clean($post['title']) ?>" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <?php endif; ?>

        <div style="max-width:800px;margin:0 auto;line-height:1.8;font-size:var(--text-lg);color:var(--gray-700);">
            <?= nl2br(clean($post['content'])) ?>
        </div>

        <footer style="max-width:800px;margin:var(--space-12) auto 0 auto;padding-top:var(--space-8);border-top:1px solid var(--gray-200);display:flex;justify-content:space-between;align-items:center;">
            <div style="display:flex;gap:var(--space-4);">
                <button class="btn btn-ghost btn-sm" onclick="shareListing('<?= addslashes($post['title']) ?>','<?= $_SERVER['REQUEST_URI'] ?>')">
                    <i data-lucide="share-2" style="width:16px;height:16px;margin-right:4px;"></i> Share
                </button>
            </div>
            <a href="/community/blog" class="btn btn-ghost btn-sm">← Back to all stories</a>
        </footer>
    </div>
</article>
