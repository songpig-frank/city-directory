<div class="admin-layout">
    <!-- Sidebar -->
    <?php $active_page = 'blog'; include __DIR__ . '/_sidebar.php'; ?>

    <!-- Content -->
    <div class="admin-content">
        <div class="admin-header">
            <h1 style="font-family:var(--font-heading);font-size:var(--text-2xl);"><?= $post ? 'Edit Post' : 'New blog Post' ?></h1>
            <p class="text-muted">Share updates, tourism stories, or business news.</p>
        </div>

        <form action="/admin/blog/save" method="POST" class="form-grid">
            <?= csrf_field() ?>
            <?php if ($post): ?>
            <input type="hidden" name="id" value="<?= $post['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Post Title</label>
                <input type="text" name="title" value="<?= clean($post['title'] ?? '') ?>" required placeholder="Enter a catchy title...">
            </div>

            <div class="form-group">
                <label>Slug (URL Segment)</label>
                <input type="text" name="slug" value="<?= clean($post['slug'] ?? '') ?>" placeholder="e.g. amazing-new-cafe-opening">
                <small class="text-muted">Leave blank to auto-generate from title.</small>
            </div>

            <div class="form-group">
                <label>Excerpt (Short Summary)</label>
                <textarea name="excerpt" rows="2" placeholder="Brief summary for list views..."><?= clean($post['excerpt'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>Content (Full Post)</label>
                <textarea name="content" rows="12" placeholder="Write your story here... Markdown is supported."><?= clean($post['content'] ?? '') ?></textarea>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
                <div class="form-group">
                    <label>Featured Image URL</label>
                    <input type="text" name="featured_image" value="<?= clean($post['featured_image'] ?? '') ?>" placeholder="/uploads/general/...">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="draft" <?= ($post['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="published" <?= ($post['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                    </select>
                </div>
            </div>

            <div style="margin-top:var(--space-8);display:flex;gap:var(--space-4);">
                <button type="submit" class="btn btn-primary btn-lg">💾 Save Post</button>
                <a href="/admin/blog" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
