<div class="admin-layout">
    <!-- Sidebar -->
    <?php $active_page = 'media'; include __DIR__ . '/_sidebar.php'; ?>

    <!-- Content -->
    <div class="admin-content">
        <div class="admin-header">
            <h1 style="font-family:var(--font-heading);font-size:var(--text-2xl);"><i data-lucide="image"></i> Media Library</h1>
            <p class="text-muted">Manage all uploaded photos and assets for the platform.</p>
        </div>

        <!-- Upload Bar -->
        <div class="card" style="margin-bottom: var(--space-8); padding: var(--space-4);">
            <form action="/admin/media/upload" method="POST" enctype="multipart/form-data" style="display:flex;align-items:center;gap:var(--space-4);">
                <?= csrf_field() ?>
                <div style="flex:1;">
                    <label style="display:block;margin-bottom:var(--space-1);font-size:var(--text-sm);font-weight:600;">Upload New Photo</label>
                    <input type="file" name="image" accept="image/*" required style="border:1px dashed var(--gray-300);padding:var(--space-2);width:100%;border-radius:var(--radius-md);">
                </div>
                <button type="submit" class="btn btn-primary" style="align-self:flex-end;">
                    <i data-lucide="upload" style="width:18px;height:18px;margin-right:2px;"></i> Upload
                </button>
            </form>
        </div>

        <?php if (empty($media_items)): ?>
        <div class="text-center" style="padding:var(--space-16) 0;">
            <i data-lucide="image-off" style="width:48px;height:48px;color:var(--gray-300);margin-bottom:var(--space-4);"></i>
            <p class="text-muted">No media items found. Start by uploading some photos.</p>
        </div>
        <?php else: ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(150px, 1fr));gap:var(--space-4);">
            <?php foreach ($media_items as $item): ?>
            <div class="card" style="overflow:hidden; position:relative; group">
                <div style="aspect-ratio:1/1;">
                    <img src="<?= $item['filepath'] ?>" alt="<?= clean($item['filename']) ?>" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
                </div>
                <div style="padding:var(--space-2);font-size:var(--text-xs);">
                    <div style="font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="<?= clean($item['filename']) ?>">
                        <?= clean($item['filename']) ?>
                    </div>
                    <div class="text-muted"><?= round($item['file_size'] / 1024) ?> KB</div>
                </div>
                <!-- Delete Button (Overlay) -->
                <form action="/admin/media/delete" method="POST" onsubmit="return confirm('Permanently delete this file?');" style="position:absolute;top:5px;right:5px;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                    <button type="submit" style="background:rgba(239, 68, 68, 0.9);color:white;border:none;border-radius:50%;width:24px;height:24px;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 4px rgba(0,0,0,0.1);">
                        <i data-lucide="x" style="width:14px;height:14px;"></i>
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['pages'] > 1): ?>
        <div class="pagination" style="margin-top:var(--space-8);">
            <?php if ($pagination['has_prev']): ?>
            <a href="?page=<?= $pagination['current'] - 1 ?>">← Prev</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $pagination['pages']; $i++): ?>
            <a href="?page=<?= $i ?>" class="<?= $i === $pagination['current'] ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <?php if ($pagination['has_next']): ?>
            <a href="?page=<?= $pagination['current'] + 1 ?>">Next →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
