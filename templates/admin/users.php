<div class="admin-layout">
    <!-- Sidebar -->
    <?php $active_page = 'users'; include __DIR__ . '/_sidebar.php'; ?>

    <!-- Content -->
    <div class="admin-content">
        <div class="admin-header" style="margin-bottom:var(--space-8);">
            <div style="display:flex; justify-content:space-between; align-items:center; width:100%;">
                <div>
                    <h1 style="font-family:var(--font-heading);font-size:var(--text-3xl);color:var(--text-main); margin-bottom:4px;">
                        <i data-lucide="shield-check" style="width:32px;height:32px;color:var(--primary);margin-right:8px;vertical-align:bottom;"></i> 
                        Security & Access
                    </h1>
                    <p class="text-sm text-muted">Manage roles, permissions, and account status for your team.</p>
                </div>
            </div>
        </div>

        <div class="card" style="padding:0;overflow:hidden;border:1px solid var(--border-base);box-shadow:var(--shadow-sm);">
            <div style="padding:var(--space-4) var(--space-6); background:var(--bg-muted); border-bottom:1px solid var(--border-base); display:flex; justify-content:space-between; align-items:center;">
                <span style="font-weight:600;font-size:var(--text-sm);color:var(--text-muted);display:flex;align-items:center;gap:8px;">
                    <i data-lucide="users" style="width:16px;height:16px;"></i> All Active Accounts
                </span>
                <span class="badge" style="background:var(--primary-light);color:var(--primary);font-size:var(--text-xs);"><?= count($users) ?> total</span>
            </div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="padding-left:var(--space-6);">Identity</th>
                        <th>Status</th>
                        <th>Administrative Level</th>
                        <th>Created</th>
                        <th style="text-align:right;padding-right:var(--space-6);">Settings</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center;padding:var(--space-12);color:var(--gray-400);">
                            <i data-lucide="user-minus" style="width:48px;height:48px;opacity:0.2;margin:12px auto;display:block;"></i>
                            No users found in current scope.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($users as $user): ?>
                    <tr class="user-row-hover" style="transition:all 0.2s ease;">
                        <td style="padding:var(--space-4) var(--space-6);">
                            <div style="display:flex;align-items:center;gap:var(--space-4);">
                                <div class="avatar-circle" style="background:var(--primary-light);color:var(--primary);width:40px;height:40px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;border:1px solid var(--primary-light);">
                                    <?= get_initials($user['name']) ?>
                                </div>
                                <div style="display:flex;flex-direction:column;">
                                    <span style="font-weight:700;color:var(--text-main);font-size:15px;"><?= clean($user['name']) ?></span>
                                    <span class="text-xs text-muted"><?= clean($user['email']) ?></span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:6px;">
                                <?php $isActive = (int)($user['is_active'] ?? 1); ?>
                                <div style="width:8px;height:8px;border-radius:50%;background:<?= $isActive ? 'var(--success)' : 'var(--error)' ?>;"></div>
                                <span class="text-xs font-semibold uppercase tracking-wider" style="color:<?= $isActive ? 'var(--success)' : 'var(--error)' ?>;">
                                    <?= $isActive ? 'Active' : 'Disabled' ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="badge" style="background:var(--bg-muted);color:var(--text-muted);border:1px solid var(--border-base);border-radius:6px;font-size:11px;font-weight:700;letter-spacing:0.02em;">
                                <?= strtoupper($user['role'] ?? 'USER') ?>
                            </span>
                        </td>
                        <td class="text-muted text-xs"><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                        <td style="text-align:right;padding-right:var(--space-6);">
                            <button class="btn btn-ghost btn-icon-only" title="Manage Security" 
                                    onclick="openUserDrawer(<?= htmlspecialchars(json_encode($user)) ?>)"
                                    style="border-radius:10px;padding:8px;background:var(--bg-muted);">
                                <i data-lucide="settings-2" style="width:18px;height:18px;color:var(--text-main);"></i>
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

<!-- User Management Drawer (Glassmorphism Overhaul) -->
<div id="userDrawer" class="premium-drawer-overlay" onclick="closeUserDrawer(event)">
    <div class="premium-drawer-content" onclick="event.stopPropagation()">
        
        <!-- Header Section -->
        <div class="drawer-top-banner">
            <div id="drawerAvatar" class="drawer-hero-avatar">JD</div>
            <div style="flex:1;">
                <h2 id="drawerTitle" style="font-family:var(--font-heading);font-size:var(--text-xl);color:white;margin:0;">Manage Access</h2>
                <span id="drawerSubTitle" class="text-xs" style="color:rgba(255,255,255,0.7);font-weight:500;">User Configuration Portal</span>
            </div>
            <button class="glass-close-btn" onclick="closeUserDrawer()">
                <i data-lucide="x" style="width:20px;height:20px;"></i>
            </button>
        </div>
        
        <form id="userForm" method="POST" action="/admin/users/save" class="premium-drawer-body">
            <?= csrf_field() ?>
            <input type="hidden" name="user_id" id="drawerUserId">

            <div class="form-section-label">Identity Overview</div>
            
            <div class="premium-form-row">
                <div class="premium-form-col">
                    <label class="premium-label">Full Account Name</label>
                    <div class="premium-readonly-box">
                        <i data-lucide="user" class="input-icon"></i>
                        <input type="text" id="drawerName" readonly class="invisible-input">
                    </div>
                </div>
            </div>

            <div class="premium-form-row">
                <div class="premium-form-col">
                    <label class="premium-label">Access Email Address</label>
                    <div class="premium-readonly-box">
                        <i data-lucide="mail" class="input-icon"></i>
                        <input type="text" id="drawerEmail" readonly class="invisible-input">
                    </div>
                </div>
            </div>

            <div class="form-section-label">Administrative Controls</div>

            <div class="premium-form-row">
                <div class="premium-form-col">
                    <label class="premium-label">Access Level (Role)</label>
                    <div class="premium-select-wrapper">
                        <i data-lucide="shield-check" class="input-icon"></i>
                        <select name="role" id="drawerRole" class="premium-select">
                            <?php if (isset($roles) && is_array($roles)): foreach ($roles as $role): ?>
                            <option value="<?= $role['slug'] ?>"><?= clean($role['name']) ?></option>
                            <?php endforeach; endif; ?>
                            <option value="user">Standard User (Portal Only)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="premium-form-row">
                <div class="premium-form-col">
                    <label class="premium-label">Account Lifecycle Status</label>
                    <div class="premium-select-wrapper">
                        <i data-lucide="toggle-right" class="input-icon"></i>
                        <select name="is_active" id="drawerStatus" class="premium-select">
                            <option value="1">Account Active (Granted)</option>
                            <option value="0">Account Disabled (Suspended)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="lockStatusBox" class="premium-lockout-alert" style="display:none;">
                <div class="alert-content">
                    <i data-lucide="lock" class="alert-icon"></i>
                    <div>
                        <strong>Security Lock Detected</strong>
                        <p>This user is currently locked due to brute-force protection logic.</p>
                        <button type="button" class="btn btn-sm btn-outline-white" onclick="unlockAccount()">Release Security Lock</button>
                    </div>
                </div>
            </div>

            <div class="form-section-label">Credential Management</div>

            <div class="premium-form-row">
                <div class="premium-form-col">
                    <label class="premium-label">Force Primary Password Reset</label>
                    <div class="premium-input-box">
                        <i data-lucide="key-round" class="input-icon"></i>
                        <input type="password" name="new_password" class="invisible-input" placeholder="Enter secure override password...">
                    </div>
                    <p class="premium-hint">Leave blank to maintain existing credentials.</p>
                </div>
            </div>

            <div class="drawer-action-container">
                <button type="submit" class="premium-save-btn">
                    <i data-lucide="save" style="width:18px;height:18px;"></i>
                    Apply Changes to User
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Premium Glass Drawer System */
.premium-drawer-overlay {
    position: fixed;
    top: 0; right: 0; bottom: 0; left: 0;
    backdrop-filter: blur(8px);
    background: rgba(0,0,0,0.4);
    z-index: 10000; /* Ensure high priority */
    opacity: 0; visibility: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.premium-drawer-overlay.active {
    opacity: 1; visibility: visible;
}

.premium-drawer-content {
    position: absolute;
    top: 16px; bottom: 16px;
    right: -500px;
    width: 460px;
    background: var(--bg-card);
    border-radius: 24px 0 0 24px;
    box-shadow: -10px 0 50px rgba(0,0,0,0.25);
    transition: all 0.5s cubic-bezier(0.19, 1, 0.22, 1);
    display: flex; flex-direction: column;
    overflow: hidden;
    border-left: 1px solid rgba(255,255,255,0.1);
}

.premium-drawer-overlay.active .premium-drawer-content {
    right: 0;
}

/* Hero Header */
.drawer-top-banner {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    padding: var(--space-8) var(--space-6);
    display: flex; align-items: center; gap: var(--space-4);
    position: relative;
}

.drawer-hero-avatar {
    width: 56px; height: 56px; border-radius: 16px;
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
    display: flex; align-items: center; justify-content: center;
    color: white; font-weight: 800; font-size: var(--text-lg);
    border: 1px solid rgba(255,255,255,0.3);
}

.glass-close-btn {
    background: rgba(255,255,255,0.1); border: none; cursor: pointer;
    width: 36px; height: 36px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: white; transition: all 0.2s;
}
.glass-close-btn:hover { background: rgba(255,255,255,0.2); transform: rotate(90deg); }

/* Body & Forms */
.premium-drawer-body {
    padding: var(--space-8) var(--space-6);
    flex: 1; overflow-y: auto;
}

.form-section-label {
    font-size: 11px; font-weight: 800; text-transform: uppercase;
    letter-spacing: 0.1em; color: var(--text-muted);
    margin-bottom: var(--space-4); margin-top: var(--space-6);
    display: flex; align-items: center; gap: 8px;
}
.form-section-label::after { content: ''; flex: 1; height: 1px; background: var(--border-base); }

.premium-form-row { margin-bottom: var(--space-6); }
.premium-label { display: block; font-size: 13px; font-weight: 600; color: var(--text-main); margin-bottom: 8px; }

.premium-readonly-box, .premium-input-box, .premium-select-wrapper {
    display: flex; align-items: center; gap: 12px;
    padding: 0 16px; height: 48px;
    background: var(--bg-muted); border-radius: 14px;
    border: 1.5px solid var(--border-base);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.premium-input-box:focus-within, .premium-select-wrapper:focus-within {
    border-color: var(--primary); background: var(--bg-card);
    box-shadow: 0 0 0 4px var(--primary-light);
}

.input-icon { width: 18px; height: 18px; color: var(--text-muted); }
.invisible-input, .premium-select {
    flex: 1; background: none; border: none; outline: none;
    font-size: 14px; color: var(--text-main); font-weight: 500;
    width: 100%;
}
.premium-select { cursor: pointer; -webkit-appearance: none; }

.premium-hint { font-size: 11px; color: var(--text-muted); margin-top: 6px; padding-left: 4px; }

/* Lockout Alert */
.premium-lockout-alert {
    background: var(--error-light); border: 1px solid var(--error);
    border-radius: 16px; padding: 16px; margin: var(--space-4) 0;
}
.alert-content { display: flex; gap: 12px; }
.alert-icon { width: 24px; height: 24px; color: var(--error); margin-top: 2px; }
.premium-lockout-alert strong { display: block; color: var(--error); font-size: 14px; margin-bottom: 2px; }
.premium-lockout-alert p { font-size: 12px; color: var(--error); margin: 0; opacity: 0.8; }
.btn-outline-white { background: white; border: 1px solid var(--error); color: var(--error); font-weight: 700; margin-top: 12px; }

/* Actions */
.drawer-action-container { margin-top: var(--space-8); }
.premium-save-btn {
    width: 100%; height: 50px; border-radius: 16px;
    background: var(--primary); color: white;
    font-size: 15px; font-weight: 700; border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 10px;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    box-shadow: 0 4px 15px var(--primary-light);
}
.premium-save-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px var(--primary-light); background: var(--primary-dark); }
.premium-save-btn:active { transform: translateY(0); }

/* Table Hover */
.user-row-hover:hover { background: var(--bg-muted); cursor: pointer; }
.btn-icon-only:hover { transform: scale(1.1); box-shadow: 0 4px 12px rgba(0,0,0,0.1); color: var(--primary) !important; }
</style>

<script>
function openUserDrawer(user) {
    document.getElementById('drawerUserId').value = user.id;
    document.getElementById('drawerTitle').innerText = user.name;
    document.getElementById('drawerName').value = user.name;
    document.getElementById('drawerEmail').value = user.email;
    document.getElementById('drawerRole').value = user.role;
    document.getElementById('drawerStatus').value = user.is_active || '1';

    // Set Initials in Hero
    const initials = user.name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0,2);
    document.getElementById('drawerAvatar').innerText = initials;

    const lockBox = document.getElementById('lockStatusBox');
    if (user.locked_until && new Date(user.locked_until) > new Date()) {
        lockBox.style.display = 'block';
    } else {
        lockBox.style.display = 'none';
    }

    document.getElementById('userDrawer').classList.add('active');
    
    // Refresh Icons (Lucide)
    if (window.lucide) {
        lucide.createIcons({
            attrs: { 'stroke-width': 2 }
        });
    }
}

function closeUserDrawer(e) {
    // Only close if clicking overlay or close button
    if (!e || e.target === document.getElementById('userDrawer')) {
        document.getElementById('userDrawer').classList.remove('active');
    }
}

function unlockAccount() {
    const userId = document.getElementById('drawerUserId').value;
    if (!confirm('Security Release: Are you sure you want to manually unlock this account and bypass the timeout?')) return;

    fetch('/admin/users/unlock', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'user_id=' + userId + '&_token=<?= csrf_token() ?>'
    }).then(res => res.json()).then(data => {
        if (data.success) {
            document.getElementById('lockStatusBox').style.display = 'none';
            // Visual feedback
            const btn = document.querySelector('.premium-save-btn');
            const originalTxt = btn.innerHTML;
            btn.innerHTML = '<i data-lucide="check-circle"></i> Successfully Unlocked';
            btn.style.background = 'var(--success)';
            lucide.createIcons();
            
            setTimeout(() => {
                btn.innerHTML = originalTxt;
                btn.style.background = '';
                lucide.createIcons();
            }, 2000);
        } else {
            alert('Security Unlock Failed: ' + (data.message || 'Unknown server error'));
        }
    });
}
</script>
