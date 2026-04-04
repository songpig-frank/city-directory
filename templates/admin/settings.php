<div class="admin-layout">
    <!-- Sidebar -->
    <?php $active_page = 'settings'; include __DIR__ . '/_sidebar.php'; ?>

    <!-- Content -->
    <div class="admin-content">
        <div class="admin-header">
            <h1 style="font-family:var(--font-heading);font-size:var(--text-2xl);">⚙️ Site Settings</h1>
            <p class="text-muted">Manage white-label branding, colors, and site information.</p>
        </div>

        <form action="/admin/settings/save" method="POST" class="form-grid">
            
            <div class="form-section">
                <h2 style="font-size:var(--text-xl);margin-bottom:var(--space-4);padding-bottom:var(--space-2);border-bottom:1px solid var(--gray-200);">Brand Identity</h2>
                
                <div class="form-group">
                    <label>Site Name</label>
                    <input type="text" name="site_name" value="<?= clean($settings['site_name'] ?? config('site_name')) ?>" required>
                </div>

                <div class="form-group">
                    <label>Global Description</label>
                    <textarea name="description" rows="3"><?= clean($settings['description'] ?? config('description')) ?></textarea>
                </div>

                <div class="form-group">
                    <label>City / Focus Area</label>
                    <input type="text" name="city" value="<?= clean($settings['city'] ?? config('city')) ?>" required>
                </div>

                <div class="form-group">
                    <label>Province / State</label>
                    <input type="text" name="province" value="<?= clean($settings['province'] ?? config('province')) ?>" required>
                </div>

                <div class="form-group">
                    <label>Currency Symbol</label>
                    <input type="text" name="currency" value="<?= clean($settings['currency'] ?? config('currency')) ?>" style="width:100px;" required>
                </div>
            </div>

            <div class="form-section" style="margin-top:var(--space-8);">
                <h2 style="font-size:var(--text-xl);margin-bottom:var(--space-4);padding-bottom:var(--space-2);border-bottom:1px solid var(--gray-200);">Homepage Hero</h2>
                
                <div class="form-group">
                    <label>Hero Title (H1)</label>
                    <input type="text" name="hero_title" value="<?= clean($settings['hero_title'] ?? '') ?>" placeholder="e.g. Discover {city}">
                    <small class="text-muted">Leave blank to use default translation keys.</small>
                </div>

                <div class="form-group">
                    <label>Hero Subtitle</label>
                    <textarea name="hero_subtitle" rows="2"><?= clean($settings['hero_subtitle'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="form-section" style="margin-top:var(--space-8);">
                <h2 style="font-size:var(--text-xl);margin-bottom:var(--space-4);padding-bottom:var(--space-2);border-bottom:1px solid var(--gray-200);">📊 Tracking & Analytics</h2>
                
                <div class="form-group">
                    <label>Google Analytics ID (G-XXXXXXX)</label>
                    <input type="text" name="google_analytics_id" value="<?= clean($settings['google_analytics_id'] ?? '') ?>" placeholder="G-XXXXXXXXXX">
                </div>

                <div class="form-group">
                    <label>Google AdSense Publisher ID (pub-XXXXXX)</label>
                    <input type="text" name="google_adsense_id" value="<?= clean($settings['google_adsense_id'] ?? '') ?>" placeholder="pub-XXXXXXXXXXXXXXXX">
                </div>
            </div>

            <div class="form-section" style="margin-top:var(--space-8);">
                <h2 style="font-size:var(--text-xl);margin-bottom:var(--space-4);padding-bottom:var(--space-2);border-bottom:1px solid var(--gray-200);">✉️ Site Information</h2>
                
                <div class="form-group">
                    <label>Contact Email</label>
                    <input type="email" name="contact_email" value="<?= clean($settings['contact_email'] ?? '') ?>" placeholder="admin@tampakan.com">
                </div>

                <div class="form-group">
                    <label>Footer Copyright Text</label>
                    <textarea name="footer_text" rows="2"><?= clean($settings['footer_text'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="form-section" style="margin-top:var(--space-8);">
                <h2 style="font-size:var(--text-xl);margin-bottom:var(--space-4);padding-bottom:var(--space-2);border-bottom:1px solid var(--gray-200);">🎨 Theme & Colors</h2>
                
                <div class="form-group">
                    <label>Primary Brand Color (Hex)</label>
                    <div style="display:flex;align-items:center;gap:var(--space-2);">
                        <input type="color" id="colorPicker" value="<?= clean($settings['theme_primary'] ?? '#0d9488') ?>" style="width:50px;height:40px;padding:0;cursor:pointer;">
                        <input type="text" name="theme_primary" id="colorHex" value="<?= clean($settings['theme_primary'] ?? '#0d9488') ?>" pattern="^#[0-9A-Fa-f]{6}$" style="width:150px;">
                    </div>
                </div>
                
                <script>
                    const picker = document.getElementById('colorPicker');
                    const hex = document.getElementById('colorHex');
                    if (picker && hex) {
                        picker.addEventListener('input', e => hex.value = e.target.value);
                        hex.addEventListener('input', e => {
                            if(/^#[0-9A-Fa-f]{6}$/.test(e.target.value)) picker.value = e.target.value;
                        });
                    }
                </script>
            </div>

            <div style="margin-top:var(--space-8);padding-top:var(--space-6);border-top:1px solid var(--gray-200);">
                <button type="submit" class="btn btn-primary btn-lg">💾 Save All Settings</button>
            </div>

        </form>
    </div>
</div>
