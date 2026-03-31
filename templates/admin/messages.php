<div class="admin-layout">
    <!-- Sidebar -->
    <?php $active_page = 'messages'; include __DIR__ . '/_sidebar.php'; ?>

    <!-- Content -->
    <div class="admin-content">
        <div class="admin-header">
            <h1 style="font-family:var(--font-heading);font-size:var(--text-2xl);"><i data-lucide="mail"></i> Inbox</h1>
            <p class="text-muted">Manage inquiries and feedback from the contact form.</p>
        </div>

        <div class="card" style="padding:0;overflow:hidden;margin-top:var(--space-6);">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:200px;">From</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($messages)): ?>
                    <tr>
                        <td colspan="4" style="text-align:center;padding:var(--space-8);color:var(--gray-400);">No messages yet.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($messages as $msg): ?>
                    <tr class="<?= $msg['is_read'] ? '' : 'font-bold' ?>" style="background:<?= $msg['is_read'] ? 'transparent' : 'var(--primary-50)' ?>;">
                        <td>
                            <div style="font-weight:600;"><?= clean($msg['name']) ?></div>
                            <div class="text-xs text-muted"><?= clean($msg['email']) ?></div>
                        </td>
                        <td>
                            <div style="font-weight:500;"><?= clean($msg['subject']) ?></div>
                            <div class="text-sm text-muted" style="max-width:400px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                <?= clean($msg['message']) ?>
                            </div>
                        </td>
                        <td class="text-muted text-sm"><?= date('M j, h:i A', strtotime($msg['created_at'])) ?></td>
                        <td style="text-align:right;">
                            <button class="btn btn-ghost btn-sm" onclick="alert('Message body:\n\n<?= addslashes(clean($msg['message'])) ?>')">
                                <i data-lucide="eye" style="width:16px;height:16px;"></i> View
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
