<!-- Add Photo Template -->
<section class="section">
    <div class="container" style="max-width: 500px;">
        <div class="section-header" style="text-align: left; margin-bottom: var(--space-6);">
            <h1 style="font-family:var(--font-heading); font-size:var(--text-2xl);"><?= __('add_storefront_photo') ?></h1>
            <p style="font-size:var(--text-sm); color:var(--gray-500);">Is this your business? Upload a fresh picture of your storefront so people can find you easily.</p>
            <div class="badge badge-category" style="margin-top: var(--space-2);">
                <i data-lucide="building" style="width:14px;height:14px;"></i> <?= clean($listing['name']) ?>
            </div>
        </div>

        <form action="/actions/photo-upload" method="POST" enctype="multipart/form-data" class="card" style="padding: var(--space-6);">
            <?= csrf_field() ?>
            <input type="hidden" name="listing_id" value="<?= $listing['id'] ?>">

            <div class="form-group" style="margin-bottom: var(--space-6);">
                <label for="storefront_photo" class="upload-trigger" id="upload-label">
                    <div class="upload-icon">
                        <i data-lucide="camera" style="width:48px;height:48px;"></i>
                    </div>
                    <strong><?= __('tap_to_take_photo') ?></strong>
                    <span style="font-size:var(--text-xs); color:var(--gray-400);"><?= __('best_practice_storefront') ?></span>
                    
                    <!-- Preview Area -->
                    <img id="photo-preview" style="display:none; width:100%; border-radius:var(--radius-lg); margin-top:var(--space-4);">
                </label>
                <input type="file" id="storefront_photo" name="photo" accept="image/*" capture="environment" style="display:none;" required onchange="previewImage(this)">
            </div>

            <!-- Location Detection (User's Feedback) -->
            <div class="location-box" style="margin-bottom: var(--space-6); padding: var(--space-4); background: var(--gray-50); border-radius: var(--radius-lg); border: 1px solid var(--gray-200);">
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:var(--space-3);">
                    <i data-lucide="map-pin" style="width:16px;height:16px;color:var(--primary);"></i>
                    <strong style="font-size:var(--text-sm);"><?= __('detected_location') ?></strong>
                </div>
                <div class="grid grid-2" style="gap:var(--space-3);">
                    <div class="form-group">
                        <label style="font-size:var(--text-xs); color:var(--gray-500); display:block; margin-bottom:2px;"><?= __('latitude') ?></label>
                        <input type="text" name="lat" id="loc-lat" class="form-input text-xs" readonly placeholder="Detecting...">
                    </div>
                    <div class="form-group">
                        <label style="font-size:var(--text-xs); color:var(--gray-500); display:block; margin-bottom:2px;"><?= __('longitude') ?></label>
                        <input type="text" name="lng" id="loc-lng" class="form-input text-xs" readonly placeholder="Detecting...">
                    </div>
                </div>
                <p id="loc-status" style="font-size:10px; color:var(--gray-400); margin-top:var(--space-2);">
                    <?= __('location_help') ?>
                </p>
                <button type="button" onclick="detectLocation()" class="btn btn-ghost btn-xs" style="margin-top:var(--space-2); padding:2px 8px;">
                    <i data-lucide="refresh-cw" style="width:12px;height:12px;margin-right:4px;"></i> Refresh GPS
                </button>
            </div>

            <div class="form-group" style="margin-bottom: var(--space-6);">
                <label for="uploader_name" style="display:block; font-weight:600; margin-bottom:var(--space-2);">Your Name / Relationship</label>
                <input type="text" id="uploader_name" name="uploader_name" class="form-input" placeholder="e.g. Owner, Employee, or Loyal Customer" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; min-height:56px;"><?= __('upload_photo') ?></button>
            <a href="<?= listing_url($listing) ?>" class="btn btn-ghost" style="width:100%; margin-top:var(--space-3);"><?= __('cancel') ?></a>
        </form>
    </div>
</section>

<style>
    .upload-trigger {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: var(--space-8);
        border: 2px dashed var(--gray-300);
        border-radius: var(--radius-xl);
        cursor: pointer;
        text-align: center;
        transition: all 0.2s;
    }
    .upload-trigger:hover { border-color: var(--primary); background: var(--primary-50); }
    .upload-icon { color: var(--primary); margin-bottom: var(--space-4); }
    .form-input { 
        width: 100%; padding: var(--space-3) var(--space-4); 
        border: 1px solid var(--gray-200); border-radius: var(--radius-lg); 
        font-size: var(--text-sm); background: white;
    }
    .form-input:focus { outline: none; border-color: var(--primary); }
    .text-xs { font-size: 12px; padding: 6px 10px; }
</style>

<script>
function previewImage(input) {
    const preview = document.getElementById('photo-preview');
    const label = document.getElementById('upload-label');
    const icon = label.querySelector('.upload-icon');
    const text = label.querySelector('strong');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            icon.style.display = 'none';
            text.innerText = "<?= __('photo_captured') ?>";
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function detectLocation() {
    const latInp = document.getElementById('loc-lat');
    const lngInp = document.getElementById('loc-lng');
    const status = document.getElementById('loc-status');

    if (!navigator.geolocation) {
        status.innerText = "Geolocation not supported by your browser.";
        return;
    }

    status.innerText = "Detecting GPS coordinates...";
    
    navigator.geolocation.getCurrentPosition(
        (position) => {
            latInp.value = position.coords.latitude.toFixed(6);
            lngInp.value = position.coords.longitude.toFixed(6);
            latInp.readOnly = false; // Allow overwrite as requested
            lngInp.readOnly = false;
            status.innerText = "Location detected! Feel free to adjust if needed.";
            status.style.color = "var(--primary)";
        },
        (error) => {
            status.innerText = "Error: " + error.message + ". Please enter coordinates manually.";
            latInp.readOnly = false;
            lngInp.readOnly = false;
        },
        { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
    );
}

// Auto-detect on load
document.addEventListener('DOMContentLoaded', detectLocation);
</script>
