<div class="admin-container">
    <div class="admin-header">
        <h1 class="admin-title">Managed Business Claims</h1>
        <div class="admin-actions">
            <div class="btn-group">
                <a href="?status=pending" class="btn <?= $status === 'pending' ? 'btn-primary' : 'btn-ghost' ?>">Pending</a>
                <a href="?status=approved" class="btn <?= $status === 'approved' ? 'btn-primary' : 'btn-ghost' ?>">Approved</a>
                <a href="?status=rejected" class="btn <?= $status === 'rejected' ? 'btn-primary' : 'btn-ghost' ?>">Rejected</a>
            </div>
        </div>
    </div>

    <div class="card overflow-hidden">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Business</th>
                    <th>Claimant</th>
                    <th>Proof / Details</th>
                    <th>Submitted</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($claims)): ?>
                <tr>
                    <td colspan="5" class="text-center p-8 text-muted">No <?= $status ?> claims found.</td>
                </tr>
                <?php endif; ?>
                
                <?php foreach ($claims as $claim): ?>
                <tr>
                    <td>
                        <a href="/<?= $claim['listing_slug'] ?>" target="_blank" class="font-bold text-primary">
                            <?= clean($claim['listing_name']) ?>
                        </a>
                    </td>
                    <td>
                        <div class="font-medium"><?= clean($claim['full_name']) ?></div>
                        <div class="text-xs text-muted"><?= clean($claim['user_email']) ?></div>
                        <div class="text-xs font-mono"><?= clean($claim['contact_phone']) ?></div>
                    </td>
                    <td>
                        <div class="text-sm truncate-2" title="<?= clean($claim['proof_text']) ?>">
                            <?= clean($claim['proof_text']) ?>
                        </div>
                    </td>
                    <td>
                        <div class="text-xs">
                            <?= date('M j, Y', strtotime($claim['created_at'])) ?><br>
                            <?= date('H:i', strtotime($claim['created_at'])) ?>
                        </div>
                    </td>
                    <td class="text-right">
                        <?php if ($claim['status'] === 'pending'): ?>
                        <div style="display:flex; gap:8px; justify-content:flex-end;">
                            <form action="/admin/claims/process" method="POST">
                                <?= csrf_field() ?>
                                <input type="hidden" name="claim_id" value="<?= $claim['id'] ?>">
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="btn btn-success btn-xs">Approve</button>
                            </form>
                            <form action="/admin/claims/process" method="POST">
                                <?= csrf_field() ?>
                                <input type="hidden" name="claim_id" value="<?= $claim['id'] ?>">
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="btn btn-danger btn-xs">Reject</button>
                            </form>
                        </div>
                        <?php else: ?>
                        <span class="badge badge-<?= $claim['status'] === 'approved' ? 'success' : 'danger' ?>">
                            <?= ucfirst($claim['status']) ?>
                        </span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        max-width: 300px;
    }
    .btn-success { background: #10b981; color: white; border: none; }
    .btn-success:hover { background: #059669; }
    .btn-danger { background: #ef4444; color: white; border: none; }
    .btn-danger:hover { background: #dc2626; }
    .badge-success { background: #d1fae5; color: #065f46; }
    .badge-danger { background: #fee2e2; color: #991b1b; }
</style>
