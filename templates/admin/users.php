<div class="admin-layout">
    <!-- Sidebar -->
    <?php $active_page = 'users'; include __DIR__ . '/_sidebar.php'; ?>

    <!-- Content -->
    <div class="admin-content">
        <div class="admin-header">
            <h1 style="font-family:var(--font-heading);font-size:var(--text-2xl);"><i data-lucide="users"></i> User Management</h1>
            <p class="text-muted">Manage platform administrators, managers, and registered users.</p>
        </div>

        <div class="card" style="padding:0;overflow:hidden;margin-top:var(--space-6);">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center;padding:var(--space-8);color:var(--gray-400);">No users found.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td style="font-weight:600;"><?= clean($user['name']) ?></td>
                        <td class="text-sm"><?= clean($user['email']) ?></td>
                        <td>
                            <span class="badge badge-<?= $user['role'] === 'admin' ? 'open' : ($user['role'] === 'manager' ? 'pending' : 'closed') ?>" style="font-size:var(--text-xs);">
                                <?= ucfirst($user['role']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-<?= $user['status'] === 'active' ? 'open' : 'closed' ?>" style="font-size:var(--text-xs);">
                                <?= ucfirst($user['status']) ?>
                            </span>
                        </td>
                        <td class="text-muted text-sm"><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                        <td style="text-align:right;">
                            <div style="display:flex;justify-content:flex-end;gap:var(--space-2);">
                                <button class="btn btn-ghost btn-sm" title="Edit Role" disabled>
                                    <i data-lucide="shield-check" style="width:16px;height:16px;"></i>
                                </button>
                                <button class="btn btn-ghost btn-sm" style="color:var(--danger);" title="Ban User" disabled>
                                    <i data-lucide="user-minus" style="width:16px;height:16px;"></i>
                                </button>
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
