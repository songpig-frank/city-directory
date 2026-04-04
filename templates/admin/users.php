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
                                <button class="btn btn-ghost btn-sm" title="Manage User" 
                                        onclick="openUserDrawer(<?= htmlspecialchars(json_encode($user)) ?>)">
                                    <i data-lucide="settings-2" style="width:16px;height:16px;"></i>
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

<!-- User Management Drawer -->
<div id="userDrawer" class="drawer-overlay" onclick="closeUserDrawer(event)">
    <div class="drawer-content" onclick="event.stopPropagation()">
        <div class="drawer-header">
            <h2 id="drawerTitle">Manage User</h2>
            <button class="btn-close" onclick="closeUserDrawer()">
                <i data-lucide="x"></i>
            </button>
        </div>
        
        <form id="userForm" method="POST" action="/admin/users/save" class="drawer-body">
            <?= csrf_field() ?>
            <input type="hidden" name="user_id" id="drawerUserId">

            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" id="drawerName" class="form-input" readonly style="background:var(--gray-100);">
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="text" id="drawerEmail" class="form-input" readonly style="background:var(--gray-100);">
            </div>

            <div class="form-group">
                <label class="form-label">Administrative Role</label>
                <select name="role" id="drawerRole" class="form-input">
                    <?php foreach ($roles ?? [] as $role): ?>
                    <option value="<?= $role['slug'] ?>"><?= clean($role['name']) ?></option>
                    <?php endforeach; ?>
                    <option value="user">Standard User</option>
                </select>
                <p class="text-xs text-muted" style="margin-top:4px;">Controls which "Duties" this staff member can perform.</p>
            </div>

            <div class="form-group">
                <label class="form-label">Account Status</label>
                <select name="is_active" id="drawerStatus" class="form-input">
                    <option value="1">Active (Enabled)</option>
                    <option value="0">Disabled (Locked Out)</option>
                </select>
            </div>

            <div id="lockStatusBox" class="alert alert-warning text-sm" style="display:none;margin-bottom:var(--space-4);">
                <i data-lucide="lock" style="width:14px;height:14px;display:inline-block;margin-right:4px;"></i>
                Account is currently locked due to too many failed login attempts.
                <button type="button" class="btn btn-sm" style="margin-top:8px;display:block;" onclick="unlockAccount()">Unlock Now</button>
            </div>

            <hr style="border:0;border-top:1px solid var(--gray-200);margin:var(--space-6) 0;">

            <div class="form-group">
                <label class="form-label">Force Password Reset</label>
                <input type="password" name="new_password" class="form-input" placeholder="Enter new password to reset">
                <p class="text-xs text-muted" style="margin-top:4px;">Leave blank to keep the current password.</p>
            </div>

            <div style="margin-top:var(--space-8);">
                <button type="submit" class="btn btn-primary" style="width:100%;">Save User Configuration</button>
            </div>
        </form>
    </div>
</div>

<style>
.drawer-overlay {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}
.drawer-overlay.active {
    opacity: 1;
    visibility: visible;
}
.drawer-content {
    position: absolute;
    top: 0;
    right: -400px;
    width: 400px;
    height: 100%;
    background: var(--bg-card);
    box-shadow: -4px 0 15px rgba(0,0,0,0.1);
    transition: right 0.3s ease;
    display: flex;
    flex-direction: column;
}
.drawer-overlay.active .drawer-content {
    right: 0;
}
.drawer-header {
    padding: var(--space-6);
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.drawer-body {
    padding: var(--space-6);
    flex: 1;
    overflow-y: auto;
}
.btn-close {
    background: none;
    border: none;
    cursor: pointer;
    color: var(--gray-500);
    padding: 4px;
}
</style>

<script>
function openUserDrawer(user) {
    document.getElementById('drawerUserId').value = user.id;
    document.getElementById('drawerTitle').innerText = 'Manage: ' + user.name;
    document.getElementById('drawerName').value = user.name;
    document.getElementById('drawerEmail').value = user.email;
    document.getElementById('drawerRole').value = user.role;
    document.getElementById('drawerStatus').value = user.is_active;

    const lockBox = document.getElementById('lockStatusBox');
    if (user.locked_until && new Date(user.locked_until) > new Date()) {
        lockBox.style.display = 'block';
    } else {
        lockBox.style.display = 'none';
    }

    document.getElementById('userDrawer').classList.add('active');
}

function closeUserDrawer() {
    document.getElementById('userDrawer').classList.remove('active');
}

function unlockAccount() {
    const userId = document.getElementById('drawerUserId').value;
    if (!confirm('Are you sure you want to unlock this account?')) return;

    fetch('/admin/users/unlock', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'user_id=' + userId + '&csrf_token=<?= csrf_token() ?>'
    }).then(res => res.json()).then(data => {
        if (data.success) {
            document.getElementById('lockStatusBox').style.display = 'none';
            alert('Account unlocked successfully.');
        }
    });
}
</script>
