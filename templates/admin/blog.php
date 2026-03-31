<div class="admin-layout">
    <!-- Sidebar -->
    <?php $active_page = 'blog'; include __DIR__ . '/_sidebar.php'; ?>

    <!-- Content -->
    <div class="admin-content">
        <div class="admin-header" style="display:flex;justify-content:space-between;align-items:center;">
            <div>
                <h1 style="font-family:var(--font-heading);font-size:var(--text-2xl);"><i data-lucide="pen-tool"></i> Blog Management</h1>
                <p class="text-muted">Write and manage news, updates, and community stories.</p>
            </div>
            <a href="/admin/blog/new" class="btn btn-primary">
                <i data-lucide="plus" style="width:18px;height:18px;margin-right:2px;"></i> New Post
            </a>
        </div>

        <div class="card" style="padding:0;overflow:hidden;margin-top:var(--space-6);">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($posts)): ?>
                    <tr>
                        <td colspan="4" style="text-align:center;padding:var(--space-8);color:var(--gray-400);">No blog posts found.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                    <tr>
                        <td style="font-weight:600;"><?= clean($post['title']) ?></td>
                        <td>
                            <span class="badge badge-<?= $post['status'] === 'published' ? 'open' : 'closed' ?>" style="font-size:var(--text-xs);">
                                <?= ucfirst($post['status']) ?>
                            </span>
                        </td>
                        <td class="text-muted"><?= date('M j, Y', strtotime($post['created_at'])) ?></td>
                        <td style="text-align:right;">
                            <div style="display:flex;justify-content:flex-end;gap:var(--space-2);">
                                <a href="/admin/blog/<?= $post['id'] ?>" class="btn btn-ghost btn-sm" title="Edit">
                                    <i data-lucide="edit-3" style="width:16px;height:16px;"></i>
                                </a>
                                <form action="/admin/blog/delete" method="POST" onsubmit="return confirm('Delete this post?');" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= $post['id'] ?>">
                                    <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);" title="Delete">
                                        <i data-lucide="trash-2" style="width:16px;height:16px;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
