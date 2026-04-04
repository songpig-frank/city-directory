<div class="admin-layout portal-layout">
    <!-- Sidebar -->
    <?php $active_page = 'listings'; include __DIR__ . '/_sidebar.php'; ?>

    <!-- Content -->
    <div class="admin-content">
        <div class="admin-header">
            <a href="/portal" class="btn btn-ghost btn-sm" style="margin-bottom:var(--space-4);">
                <i data-lucide="arrow-left" style="width:16px;height:16px;margin-right:4px;"></i> Back to Dashboard
            </a>
            <h1 style="font-family:var(--font-heading);font-size:var(--text-2xl);">📝 Edit Business Details</h1>
            <p class="text-muted">Keep your business information up to date to help customers find you.</p>
        </div>

        <form action="/portal/listings/save" method="POST" class="grid grid-2-1" style="margin-top:var(--space-8);gap:var(--space-8);">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= $listing['id'] ?>">
            <input type="hidden" name="lat" id="lat-input" value="<?= $listing['lat'] ?>">
            <input type="hidden" name="lng" id="lng-input" value="<?= $listing['lng'] ?>">

            <!-- Main Column -->
            <div style="display:grid;gap:var(--space-8);">
                
                <!-- Core Identity -->
                <div class="card p-6">
                    <h3 style="font-family:var(--font-heading);margin-bottom:var(--space-6);padding-bottom:var(--space-2);border-bottom:1px solid var(--gray-200);">🏢 Basic Information</h3>
                    
                    <div class="form-group mb-4">
                        <label class="form-label">Business/Entity Name</label>
                        <input type="text" name="name" id="listing-name" value="<?= clean($listing['name']) ?>" class="form-input" required>
                    </div>

                    <div class="grid grid-2 gap-4">
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select">
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $listing['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                    <?= clean($cat['icon']) ?> <?= clean($cat['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <label class="form-label">Full Description</label>
                        <textarea name="description" class="form-textarea" rows="8"><?= clean($listing['description'] ?? '') ?></textarea>
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
                            <input type="text" name="facebook" value="<?= clean($listing['facebook'] ?? '') ?>" class="form-input" placeholder="URL or @username">
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
                </div>

                <!-- Precise Location -->
                <div class="card p-6">
                    <h3 style="font-family:var(--font-heading);margin-bottom:var(--space-6);padding-bottom:var(--space-2);border-bottom:1px solid var(--gray-200);">📍 Geospatial Location</h3>
                    <p class="text-muted mb-4">Drag the pin to update your business location on the city map.</p>
                    
                    <div id="admin-map" style="height:350px; border-radius:var(--radius-lg); border:1px solid var(--gray-300); margin-bottom:var(--space-4);"></div>
                    
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
            </div>

            <!-- Sidebar Column -->
            <div style="display:grid;gap:var(--space-8);align-self:start;">
                
                <!-- Status Preview -->
                <div class="card p-6">
                    <h3 style="font-family:var(--font-heading);margin-bottom:var(--space-4);">Listing Status</h3>
                    <div style="margin-bottom:var(--space-6);">
                        <span class="badge badge-<?= $listing['status'] === 'active' ? 'active' : ($listing['status'] === 'pending' ? 'pending' : 'expired') ?>" style="font-size:var(--text-lg);padding:var(--space-2) var(--space-4);">
                            <?= ucfirst($listing['status']) ?>
                        </span>
                    </div>
                    
                    <?php if ($listing['is_featured']): ?>
                    <div class="badge badge-featured" style="width:100%;justify-content:center;margin-bottom:var(--space-4);">⭐ Featured Listing</div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary btn-lg" style="width:100%; justify-content:center;">
                        <i data-lucide="save" style="margin-right:8px;"></i> Save & Update
                    </button>
                    
                    <small class="text-muted text-center" style="display:block;margin-top:12px;">
                        <?php if (($_SESSION['user_trusted'] ?? 0) == 1): ?>
                            <i data-lucide="check-square" style="width:12px;height:12px;vertical-align:middle;"></i> Edits will go live immediately.
                        <?php else: ?>
                            <i data-lucide="clock" style="width:12px;height:12px;vertical-align:middle;"></i> Edits may require moderator review.
                        <?php endif; ?>
                    </small>
                </div>

                <!-- Photos Reminder -->
                <div class="card p-6">
                    <h3 style="font-family:var(--font-heading);margin-bottom:var(--space-4);">Photos</h3>
                    <div style="aspect-ratio:3/2;background:var(--gray-100);border-radius:var(--radius-md);overflow:hidden;margin-bottom:var(--space-4);">
                        <img src="<?= get_listing_image($listing) ?>" style="width:100%;height:100%;object-fit:cover;">
                    </div>
                    <p class="text-xs text-muted">To change photos, please contact our support team or use the media manager (if enabled for your plan).</p>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Dynamic Map Pin-Dropper
    const lat = <?= $listing['lat'] ?? config('map_center_lat') ?>;
    const lng = <?= $listing['lng'] ?? config('map_center_lng') ?>;
    
    const map = L.map('admin-map', {
        zoomControl: false // Simpler for owners
    }).setView([lat, lng], 16);
    
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

    // Refresh icons
    if (window.lucide) lucide.createIcons();
});
</script>
