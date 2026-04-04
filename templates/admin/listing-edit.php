<div class="admin-layout">
    <!-- Sidebar -->
    <?php $active_page = 'listings'; include __DIR__ . '/_sidebar.php'; ?>

    <!-- Content -->
    <div class="admin-content">
        <div class="admin-header" style="display:flex;justify-content:space-between;align-items:center;">
            <div>
                <h1 style="font-family:var(--font-heading);font-size:var(--text-2xl);">🛠️ Professional Listing Editor</h1>
                <p class="text-muted">Modifying: <strong><?= clean($listing['name']) ?></strong></p>
            </div>
            <div style="display:flex;gap:var(--space-3);">
                <a href="<?= listing_url($listing) ?>" target="_blank" class="btn btn-ghost btn-sm">
                    <i data-lucide="external-link" style="width:16px;height:16px;margin-right:4px;"></i> View Public Page
                </a>
                <form action="/admin/listings/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this listing?');" style="display:inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= $listing['id'] ?>">
                    <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--error);">
                        <i data-lucide="trash-2" style="width:16px;height:16px;margin-right:4px;"></i> Delete
                    </button>
                </form>
            </div>
        </div>

        <form action="/admin/listings/save" method="POST" class="form-mega-grid" style="margin-top:var(--space-8);">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= $listing['id'] ?>">
            <input type="hidden" name="latitude" id="lat-input" value="<?= $listing['latitude'] ?>">
            <input type="hidden" name="longitude" id="lng-input" value="<?= $listing['longitude'] ?>">

            <div class="form-columns" style="display:grid;grid-template-columns: 2fr 1fr;gap:var(--space-8);">
                
                <!-- Main Column -->
                <div style="display:grid;gap:var(--space-8);">
                    
                    <!-- Core Identity -->
                    <div class="card p-6">
                        <h3 style="font-family:var(--font-heading);margin-bottom:var(--space-6);padding-bottom:var(--space-2);border-bottom:1px solid var(--gray-200);">🏢 Core Identity</h3>
                        
                        <div class="form-group mb-4">
                            <label class="form-label">Business/Entity Name</label>
                            <input type="text" name="name" id="listing-name" value="<?= clean($listing['name']) ?>" class="form-input" required>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label">Custom Slug (URL Path)</label>
                            <div style="display:flex;gap:4px;align-items:center;">
                                <span class="text-muted" style="font-size:var(--text-sm); white-space:nowrap;"><?= config('base_url') ?>/<?= $listing['type'] ?>/</span>
                                <input type="text" name="slug" id="listing-slug" value="<?= clean($listing['slug']) ?>" class="form-input" style="font-family:monospace;font-size:var(--text-sm);">
                            </div>
                            <small class="text-muted">Careful: Changing this breaks existing links unless you set up 301 redirects.</small>
                        </div>

                        <div class="grid grid-2 gap-4">
                            <div class="form-group">
                                <label class="form-label">Primary Category</label>
                                <select name="category_id" class="form-select">
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $listing['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                        <?= clean($cat['icon']) ?> <?= clean($cat['name'] ?? 'Uncategorized') ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Classification Type</label>
                                <select name="type" class="form-select">
                                    <option value="business" <?= $listing['type'] === 'business' ? 'selected' : '' ?>>Business</option>
                                    <option value="tourism" <?= $listing['type'] === 'tourism' ? 'selected' : '' ?>>Tourism</option>
                                    <option value="creator" <?= $listing['type'] === 'creator' ? 'selected' : '' ?>>Creator</option>
                                    <option value="essential" <?= $listing['type'] === 'essential' ? 'selected' : '' ?>>Essential Service</option>
                                </select>
                            </div>
                        </div>

                        <!-- Secondary Categories -->
                        <div class="form-group mt-6">
                            <label class="form-label">Secondary Categories</label>
                            <p class="text-xs text-muted mb-3">Does this business belong to other categories? (e.g. A Resort + Restaurant)</p>
                            <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 10px; max-height: 200px; overflow-y: auto; padding: 12px; background: var(--gray-50); border: 1px solid var(--gray-200); border-radius: var(--radius-md);">
                                <?php foreach ($categories as $cat): ?>
                                    <?php if ($cat['id'] == $listing['category_id']) continue; ?>
                                    <label style="display:flex; align-items:center; gap:8px; font-size:var(--text-sm); cursor:pointer;">
                                        <input type="checkbox" name="secondary_categories[]" value="<?= $cat['id'] ?>" <?= in_array($cat['id'], $secondary_ids) ? 'checked' : '' ?>>
                                        <span><?= clean($cat['icon']) ?> <?= clean($cat['name']) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <label class="form-label">Full Description (SEO Optimized)</label>
                            <textarea name="description" class="form-textarea" rows="10" placeholder="Rich description for SEO and AI discovery..."><?= clean($listing['description'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <!-- Precise Location -->
                    <div class="card p-6">
                        <h3 style="font-family:var(--font-heading);margin-bottom:var(--space-6);padding-bottom:var(--space-2);border-bottom:1px solid var(--gray-200);">📍 Geospatial Location</h3>
                        <p class="text-muted mb-4">Drag the pin to the exact location of the business. This powers the interactive map discovery.</p>
                        
                        <div id="admin-map" style="height:400px; border-radius:var(--radius-lg); border:1px solid var(--gray-300); margin-bottom:var(--space-4);"></div>
                        
                        <div class="grid grid-3 gap-4">
                            <div class="form-group">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" value="<?= clean($listing['phone'] ?? '') ?>" class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Barangay</label>
                                <input type="text" name="barangay" value="<?= clean($listing['barangay'] ?? '') ?>" class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Street Address</label>
                                <input type="text" name="address" value="<?= clean($listing['address'] ?? '') ?>" class="form-input">
                            </div>
                        </div>
                    </div>

                    <!-- Digital Presence & Socials -->
                    <div class="card p-6">
                        <h3 style="font-family:var(--font-heading);margin-bottom:var(--space-6);padding-bottom:var(--space-2);border-bottom:1px solid var(--gray-200);">🌐 Digital Presence</h3>
                        
                        <div class="form-group mb-4">
                            <label class="form-label">Direct Website</label>
                            <input type="url" name="website" value="<?= clean($listing['website'] ?? '') ?>" class="form-input" placeholder="https://...">
                        </div>

                        <div class="grid grid-2 gap-4">
                            <div class="form-group">
                                <label class="form-label">Facebook Page</label>
                                <input type="text" name="facebook" value="<?= clean($listing['facebook'] ?? '') ?>" class="form-input" placeholder="https://facebook.com/yourpage">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Instagram Profile</label>
                                <input type="text" name="instagram" value="<?= clean($listing['instagram'] ?? '') ?>" class="form-input" placeholder="@username">
                            </div>
                            <div class="form-group">
                                <label class="form-label">TikTok Profile</label>
                                <input type="text" name="tiktok" value="<?= clean($listing['tiktok'] ?? '') ?>" class="form-input" placeholder="@username">
                            </div>
                            <div class="form-group">
                                <label class="form-label">YouTube Channel</label>
                                <input type="text" name="youtube" value="<?= clean($listing['youtube'] ?? '') ?>" class="form-input">
                            </div>
                        </div>

                        <h4 style="margin:var(--space-6) 0 var(--space-2) 0; font-size:var(--text-md); color:var(--gray-700);">🛍️ E-commerce Links</h4>
                        <div class="grid grid-2 gap-4">
                            <div class="form-group">
                                <label class="form-label">Shopee Store</label>
                                <input type="url" name="shopee_link" value="<?= clean($listing['shopee_link'] ?? '') ?>" class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Lazada Store</label>
                                <input type="url" name="lazada_link" value="<?= clean($listing['lazada_link'] ?? '') ?>" class="form-input">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Column -->
                <div style="display:grid;gap:var(--space-8);align-self:start;">
                    
                    <!-- Publishing Control -->
                    <div class="card p-6" style="border-top: 4px solid var(--primary);">
                        <h3 style="font-family:var(--font-heading);margin-bottom:var(--space-4);">🚀 Publishing</h3>
                        
                        <div class="form-group">
                            <label class="form-label">Listing Status</label>
                            <select name="status" class="form-select">
                                <option value="pending" <?= $listing['status'] === 'pending' ? 'selected' : '' ?>>Pending Review</option>
                                <option value="active" <?= $listing['status'] === 'active' ? 'selected' : '' ?>>Published / Active</option>
                                <option value="expired" <?= $listing['status'] === 'expired' ? 'selected' : '' ?>>Expired / Closed</option>
                                <option value="rejected" <?= $listing['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                            </select>
                        </div>

                        <div style="margin-top:var(--space-6); background:var(--gray-50); padding:var(--space-4); border-radius:var(--radius-md);">
                            <label style="display:flex;align-items:center;gap:var(--space-2);cursor:pointer;font-weight:600;">
                                <input type="checkbox" name="is_featured" value="1" <?= $listing['is_featured'] ? 'checked' : '' ?>>
                                <span style="color:var(--indigo-700);">⭐ Featured Listing</span>
                            </label>
                            
                            <div style="margin-top:var(--space-3);">
                                <label class="form-label" style="font-size:var(--text-xs);">Featured Until (Date)</label>
                                <input type="date" name="featured_until" value="<?= $listing['featured_until'] ?? '' ?>" class="form-input" style="font-size:var(--text-xs);">
                                <small class="text-muted" style="display:block;margin-top:4px;">Leave blank for indefinite.</small>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg mt-6" style="width:100%; justify-content:center;">
                            <i data-lucide="check" style="margin-right:8px;"></i> Commit Changes
                        </button>
                    </div>

                    <!-- Images Manager -->
                    <div class="card p-6">
                        <h3 style="font-family:var(--font-heading);margin-bottom:var(--space-4);">🖼️ Media Assets</h3>
                        <div class="listing-image-preview mb-4" style="background:var(--gray-100); border-radius:var(--radius-md); overflow:hidden; aspect-ratio:16/9; display:flex; align-items:center; justify-content:center;">
                            <?php if (!empty($listing['primary_image'])): ?>
                                <img src="<?= $listing['primary_image'] ?>" style="width:100%; height:100%; object-fit:cover;">
                            <?php else: ?>
                                <i data-lucide="image" class="text-muted" style="width:48px;height:48px;"></i>
                            <?php endif; ?>
                        </div>
                        <a href="/admin/media?listing_id=<?= $listing['id'] ?>" class="btn btn-ghost" style="width:100%;justify-content:center;">Manage Photos</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Dynamic Map Pin-Dropper
    const lat = <?= $listing['latitude'] ?? config('map_center_lat') ?>;
    const lng = <?= $listing['longitude'] ?? config('map_center_lng') ?>;
    
    const map = L.map('admin-map').setView([lat, lng], 16);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const marker = L.marker([lat, lng], {
        draggable: true
    }).addTo(map);

    marker.on('dragend', function(e) {
        const position = marker.getLatLng();
        document.getElementById('lat-input').value = position.lat;
        document.getElementById('lng-input').value = position.lng;
    });

    // 2. Slug Auto-generation
    const nameInput = document.getElementById('listing-name');
    const slugInput = document.getElementById('listing-slug');
    
    if (nameInput && slugInput && !slugInput.value) {
        nameInput.addEventListener('input', function(e) {
            slugInput.value = e.target.value
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/(^-|-$)+/g, '');
        });
    }

    // Refresh icons
    if (window.lucide) lucide.createIcons();
});
</script>
