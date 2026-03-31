<section class="section">
    <div class="container" style="max-width:800px;">
        <div class="section-header">
            <h1 style="font-family:var(--font-heading);font-size:var(--text-3xl);"><?= __('submit_title') ?></h1>
            <p><?= __('submit_subtitle') ?></p>
        </div>

        <form method="POST" action="/submit" enctype="multipart/form-data" class="card" style="padding:var(--space-8);">
            <?= csrf_field() ?>

            <!-- Type -->
            <div class="form-group">
                <label class="form-label"><?= __('submit_type') ?> *</label>
                <select name="type" class="form-select" id="listing-type" onchange="handleTypeChange()">
                    <option value="business"><?= __('submit_type_business') ?></option>
                    <option value="tourism"><?= __('submit_type_tourism') ?></option>
                    <option value="creator"><?= __('submit_type_creator') ?></option>
                    <option value="artist"><?= __('submit_type_artist') ?></option>
                    <option value="service"><?= __('submit_type_service') ?></option>
                    <option value="community"><?= __('submit_type_community') ?></option>
                </select>
            </div>

            <!-- Name -->
            <div class="form-group">
                <label class="form-label"><?= __('submit_name') ?> *</label>
                <input type="text" name="name" class="form-input" required maxlength="200" placeholder="e.g. Kolon Cafe, Jada's Farm...">
            </div>

            <!-- Category -->
            <div class="form-group">
                <label class="form-label"><?= __('submit_category') ?> *</label>
                <select name="category_id" class="form-select" required>
                    <option value="">-- Select Category --</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" data-type="<?= $cat['type'] ?>">
                        <?= $cat['icon'] ?? '' ?> <?= clean($cat['name']) ?> (<?= ucfirst($cat['type']) ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Description -->
            <div class="form-group">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--space-2);">
                    <label class="form-label" style="margin-bottom:0;"><?= __('submit_description') ?></label>
                    <button type="button" id="ai-write-btn" class="btn btn-ghost btn-sm" style="color:var(--primary);font-size:var(--text-xs);display:flex;align-items:center;gap:4px;">
                        <i data-lucide="sparkles" style="width:14px;height:14px;"></i> Use AI to write for me
                    </button>
                </div>
                <textarea name="description" id="business-description" class="form-textarea" maxlength="2000" placeholder="Tell people about your business or place..."></textarea>
                <div id="ai-loading" style="display:none;font-size:var(--text-xs);color:var(--primary);margin-top:var(--space-1);">
                    <i data-lucide="loader-2" class="animate-spin" style="width:12px;height:12px;vertical-align:middle;"></i> Generating your professional profile...
                </div>
            </div>

            <script>
                document.getElementById('ai-write-btn').addEventListener('click', async () => {
                    const name = document.querySelector('input[name="name"]').value;
                    const catSelect = document.querySelector('select[name="category_id"]');
                    const category = catSelect.options[catSelect.selectedIndex].text;
                    const descArea = document.getElementById('business-description');
                    const loader = document.getElementById('ai-loading');
                    const btn = document.getElementById('ai-write-btn');

                    if (!name) {
                        alert('Please enter a business name first so the AI knows what to write about.');
                        return;
                    }

                    try {
                        btn.disabled = true;
                        loader.style.display = 'block';
                        
                        const res = await fetch('/api/ai-writer', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                name: name,
                                category: category,
                                notes: descArea.value // Use current text as context if any
                            })
                        });
                        
                        const data = await res.json();
                        if (data.text) {
                            descArea.value = data.text;
                            // Trigger auto-resize if any
                        } else if (data.error) {
                            alert('AI Error: ' + data.error);
                        }
                    } catch (e) {
                        alert('Failed to connect to AI service.');
                    } finally {
                        loader.style.display = 'none';
                        btn.disabled = false;
                    }
                });
            </script>

            <!-- Location -->
            <div id="section-location">
                <div class="form-group">
                    <label class="form-label"><?= __('submit_address') ?></label>
                    <input type="text" name="address" id="address" class="form-input" placeholder="Street address">
                </div>
                <div class="form-group">
                    <label class="form-label"><?= __('submit_barangay') ?></label>
                    <input type="text" name="barangay" class="form-input" placeholder="Barangay name">
                </div>
            </div>

            <!-- Map Picker -->
            <div class="form-group" id="section-map">
                <label class="form-label"><?= __('submit_map_label') ?></label>
                <div id="map-picker" class="map-container map-picker"></div>
                <input type="hidden" name="lat" id="lat">
                <input type="hidden" name="lng" id="lng">
                <p class="form-hint">Click the map to pin your exact location. You can drag the pin to adjust.</p>
            </div>

            <!-- Contact -->
            <div class="grid grid-2">
                <div class="form-group" id="section-phone">
                    <label class="form-label"><?= __('submit_phone') ?></label>
                    <input type="tel" name="phone" class="form-input" placeholder="09XX XXX XXXX">
                </div>
                <div class="form-group">
                    <label class="form-label"><?= __('submit_email') ?></label>
                    <input type="email" name="email" class="form-input" placeholder="you@example.com">
                </div>
            </div>
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label"><?= __('submit_website') ?></label>
                    <input type="url" name="website" class="form-input" placeholder="https://">
                </div>
                <div class="form-group">
                    <label class="form-label"><?= __('submit_facebook') ?></label>
                    <input type="url" name="facebook" class="form-input" placeholder="https://facebook.com/yourpage">
                </div>
            </div>
            
            <div class="grid grid-3" style="gap:var(--space-4);">
                <div class="form-group">
                    <label class="form-label">YouTube Link</label>
                    <input type="url" name="youtube" class="form-input" placeholder="https://youtube.com/@channel">
                </div>
                <div class="form-group">
                    <label class="form-label">TikTok Link</label>
                    <input type="url" name="tiktok" class="form-input" placeholder="https://tiktok.com/@user">
                </div>
                <div class="form-group">
                    <label class="form-label">Instagram Link</label>
                    <input type="url" name="instagram" class="form-input" placeholder="https://instagram.com/user">
                </div>
            </div>

            <!-- Hours -->
            <div class="form-group" id="section-hours">
                <label class="form-label"><?= __('submit_hours') ?></label>
                <div class="hours-grid">
                    <?php $days = ['mon'=>'Monday','tue'=>'Tuesday','wed'=>'Wednesday','thu'=>'Thursday','fri'=>'Friday','sat'=>'Saturday','sun'=>'Sunday']; ?>
                    <?php foreach ($days as $key => $label): ?>
                    <div class="hours-row">
                        <label>
                            <input type="checkbox" checked onchange="toggleHoursRow(this,'<?= $key ?>')"> <?= $label ?>
                        </label>
                        <input type="time" name="hours_<?= $key ?>_open" id="hours_<?= $key ?>_open" class="form-input" value="08:00">
                        <input type="time" name="hours_<?= $key ?>_close" id="hours_<?= $key ?>_close" class="form-input" value="17:00">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Photos -->
            <div class="form-group">
                <label class="form-label"><?= __('submit_photos', ['max' => config('max_images_per_listing') ?? 5]) ?></label>
                <input type="file" name="images[]" multiple accept="image/*" class="form-input">
                <p class="form-hint">Max <?= config('max_image_size_mb') ?? 2 ?>MB per image. First image becomes the cover photo.</p>
            </div>

            <!-- External Links -->
            <div class="form-group">
                <label class="form-label"><?= __('submit_links') ?></label>
                <div style="display:grid;gap:var(--space-3);">
                    <input type="url" name="shopee_link" class="form-input" placeholder="🛒 Shopee link">
                    <input type="url" name="lazada_link" class="form-input" placeholder="🛍️ Lazada link">
                    <input type="url" name="amazon_link" class="form-input" placeholder="📦 Amazon link">
                    <input type="url" name="food_ordering_link" class="form-input" placeholder="🍔 Food ordering link (GrabFood, etc.)">
                </div>
            </div>

            <!-- Real Estate (hidden by default) -->
            <div id="real-estate-fields" style="display:none;">
                <h3 style="font-family:var(--font-heading);margin:var(--space-6) 0 var(--space-4);">🏠 Property Details</h3>
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label"><?= __('re_property_type') ?></label>
                        <select name="property_type" class="form-select">
                            <option value="">-- Select --</option>
                            <option value="lot"><?= __('re_lot') ?></option>
                            <option value="house_lot"><?= __('re_house_lot') ?></option>
                            <option value="farm"><?= __('re_farm') ?></option>
                            <option value="commercial"><?= __('re_commercial') ?></option>
                            <option value="apartment"><?= __('re_apartment') ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= __('re_sqm') ?></label>
                        <input type="number" name="property_sqm" class="form-input" step="0.01" placeholder="e.g. 500">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= __('re_price') ?> (<?= config('currency') ?>)</label>
                        <input type="number" name="property_price" class="form-input" step="0.01" placeholder="e.g. 500000">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= __('re_terms') ?></label>
                        <input type="text" name="property_terms" class="form-input" placeholder="e.g. Cash, Installment">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label"><?= __('re_broker_license') ?></label>
                    <input type="text" name="broker_license" class="form-input" placeholder="PRC License # (if applicable)">
                    <p class="form-hint"><?= clean(config('disclaimers')['real_estate'] ?? '') ?></p>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg" style="width:100%;margin-top:var(--space-6);">
                <?= __('submit_button') ?>
            </button>
        </form>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    initMapPicker('map-picker', 'lat', 'lng', 'address',
        <?= config('map_center_lat') ?>, <?= config('map_center_lng') ?>, <?= config('map_zoom') ?>);
    
    filterCategories();
    toggleCreatorFields();
});

function handleTypeChange() {
    filterCategories();
    toggleRealEstate();
    toggleCreatorFields();
}

function toggleCreatorFields() {
    const type = document.getElementById('listing-type').value;
    const isMobile = (type === 'creator' || type === 'artist' || type === 'community');
    
    const els = ['section-location', 'section-map', 'section-phone', 'section-hours'];
    els.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.style.display = isMobile ? 'none' : '';
        }
    });
}

function filterCategories() {
    const type = document.getElementById('listing-type').value;
    const catSelect = document.querySelector('[name="category_id"]');
    const options = catSelect.querySelectorAll('option');

    options.forEach(opt => {
        if (opt.value === "") return;
        
        if (opt.getAttribute('data-type') === type) {
            opt.style.display = '';
            opt.disabled = false;
        } else {
            opt.style.display = 'none';
            opt.disabled = true;
        }
    });

    const selectedOpt = catSelect.options[catSelect.selectedIndex];
    if (selectedOpt && selectedOpt.value !== "" && selectedOpt.disabled) {
        catSelect.value = "";
    }
}

function toggleRealEstate() {
    const type = document.getElementById('listing-type').value;
    const cat = document.querySelector('[name="category_id"]').value;
    const catOption = document.querySelector(`[name="category_id"] option[value="${cat}"]`);
    const fields = document.getElementById('real-estate-fields');
    // Show real estate fields if category contains "real estate" or "property"
    const catText = catOption ? catOption.textContent.toLowerCase() : '';
    fields.style.display = catText.includes('real estate') || catText.includes('property') ? 'block' : 'none';
}

document.querySelector('[name="category_id"]').addEventListener('change', toggleRealEstate);
</script>
