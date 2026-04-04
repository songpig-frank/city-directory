<div class="admin-layout">
    <!-- Sidebar -->
    <?php $active_page = 'categories'; include __DIR__ . '/_sidebar.php'; ?>

    <!-- Content -->
    <div class="admin-content">
        <div class="admin-header">
            <a href="/admin/categories" class="btn btn-ghost btn-sm" style="margin-bottom:var(--space-4);">
                <i data-lucide="arrow-left" style="width:16px;height:16px;margin-right:4px;"></i> Back to Categories
            </a>
            <h1 style="font-family:var(--font-heading);font-size:var(--text-2xl);"><?= isset($category) ? '⚙️ Edit Category' : '➕ Add New Category' ?></h1>
        </div>

        <form action="/admin/categories/save" method="POST" class="card" style="margin-top:var(--space-6);">
            <input type="hidden" name="id" value="<?= $category['id'] ?? '' ?>">
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Category Name</label>
                    <input type="text" name="name" id="catName" value="<?= clean($category['name'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label>Slug (URL Identifier)</label>
                    <input type="text" name="slug" id="catSlug" value="<?= clean($category['slug'] ?? '') ?>" placeholder="e.g. restaurant-eateries">
                </div>

                <div class="form-group">
                    <label>Type / Classification</label>
                    <select name="type">
                        <option value="business" <?= ($category['type'] ?? '') === 'business' ? 'selected' : '' ?>>Business Service</option>
                        <option value="tourism" <?= ($category['type'] ?? '') === 'tourism' ? 'selected' : '' ?>>Tourist Attraction</option>
                        <option value="creator" <?= ($category['type'] ?? '') === 'creator' ? 'selected' : '' ?>>Creator / Community</option>
                        <option value="essential" <?= ($category['type'] ?? '') === 'essential' ? 'selected' : '' ?>>Essential Service</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Emoji Icon</label>
                    <input type="text" name="icon" value="<?= clean($category['icon'] ?? '🏷️') ?>" style="width:100px;">
                </div>

                <div class="form-group">
                    <label>Sort Order (Lower appears higher)</label>
                    <input type="number" name="sort_order" value="<?= $category['sort_order'] ?? 0 ?>">
                </div>

                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:var(--space-2);cursor:pointer;">
                        <input type="checkbox" name="is_active" value="1" <?= ($category['is_active'] ?? 1) ? 'checked' : '' ?>>
                        Is Active (Visible on site)
                    </label>
                </div>
            </div>

            <div style="margin-top:var(--space-8);padding-top:var(--space-6);border-top:1px solid var(--gray-100);display:flex;gap:var(--space-4);">
                <button type="submit" class="btn btn-primary btn-lg">💾 Save Category</button>
                <a href="/admin/categories" class="btn btn-ghost btn-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('catName')?.addEventListener('input', function(e) {
    const slugEl = document.getElementById('catSlug');
    if (slugEl && !slugEl.value) {
        slugEl.placeholder = e.target.value
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)+/g, '');
    }
});
</script>
