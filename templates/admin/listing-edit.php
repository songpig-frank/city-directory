<div class="admin-layout">
    <!-- Sidebar -->
    <?php $active_page = 'listings'; include __DIR__ . '/_sidebar.php'; ?>

    <!-- Content -->
    <div class="admin-content">
        <div class="admin-header" style="display:flex;justify-content:space-between;align-items:center;">
            <div>
                <h1 style="font-family:var(--font-heading);font-size:var(--text-2xl);">Edit Listing</h1>
                <p class="text-muted">Modifying: <?= clean($listing['name']) ?></p>
            </div>
            <div style="display:flex;gap:var(--space-3);">
                <a href="<?= listing_url($listing) ?>" target="_blank" class="btn btn-ghost btn-sm">
                    <i data-lucide="external-link"></i> View Page
                </a>
                <form action="/admin/listings/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this listing?');" style="display:inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= $listing['id'] ?>">
                    <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);">
                        <i data-lucide="trash-2"></i> Delete
                    </button>
                </form>
            </div>
        </div>

        <form action="/admin/listings/save" method="POST" class="grid grid-1-2" style="margin-top:var(--space-8);gap:var(--space-8);">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= $listing['id'] ?>">

            <div style="display:grid;gap:var(--space-6);">
                <div class="card p-6">
                    <h3 style="font-family:var(--font-heading);margin-bottom:var(--space-4);">Core Information</h3>
                    
                    <div class="form-group">
                        <label class="form-label">Business Name</label>
                        <input type="text" name="name" value="<?= clean($listing['name']) ?>" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select">
                            <option value="business" <?= $listing['type'] === 'business' ? 'selected' : '' ?>>Business</option>
                            <option value="tourism" <?= $listing['type'] === 'tourism' ? 'selected' : '' ?>>Tourism</option>
                            <option value="creator" <?= $listing['type'] === 'creator' ? 'selected' : '' ?>>Creator</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $listing['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                [<?= ucfirst($cat['type']) ?>] <?= clean($cat['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-textarea" rows="8"><?= clean($listing['description']) ?></textarea>
                    </div>
                </div>

                <div class="card p-6">
                    <h3 style="font-family:var(--font-heading);margin-bottom:var(--space-4);">Contact & Location</h3>
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" value="<?= clean($listing['phone'] ?? '') ?>" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" value="<?= clean($listing['address'] ?? '') ?>" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Barangay</label>
                        <input type="text" name="barangay" value="<?= clean($listing['barangay'] ?? '') ?>" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Website</label>
                        <input type="url" name="website" value="<?= clean($listing['website'] ?? '') ?>" class="form-input">
                    </div>
                </div>
            </div>

            <div style="display:grid;gap:var(--space-6);align-self:start;">
                <div class="card p-6">
                    <h3 style="font-family:var(--font-heading);margin-bottom:var(--space-4);">Status & Visibility</h3>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="pending" <?= $listing['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="active" <?= $listing['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="suspended" <?= $listing['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                            <option value="rejected" <?= $listing['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </div>
                    <div style="margin-top:var(--space-4);">
                        <label style="display:flex;align-items:center;gap:var(--space-2);cursor:pointer;">
                            <input type="checkbox" name="is_featured" value="1" <?= $listing['is_featured'] ? 'checked' : '' ?>>
                            <span>Featured Listing</span>
                        </label>
                    </div>
                </div>

                <div class="card p-6">
                    <button type="submit" class="btn btn-primary btn-lg" style="width:100%;">
                        <i data-lucide="check"></i> Save Changes
                    </button>
                    <a href="/admin/listings" class="btn btn-ghost mt-2" style="width:100%;text-align:center;">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
