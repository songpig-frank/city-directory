<div id="map-page-wrapper" style="display:flex;height:calc(100vh - 70px);overflow:hidden;position:relative;">
    
    <!-- Sidebar: Google-style Listing -->
    <div id="map-sidebar" style="width:380px;height:100%;overflow-y:auto;background:var(--bg-surface);border-right:1px solid var(--border-base);padding:var(--space-4);z-index:20;">
        <div class="mb-6">
            <h2 style="font-family:var(--font-heading);margin-bottom:var(--space-2);font-size:var(--text-xl);color:var(--text-primary);">Explore <?= clean(config('city')) ?></h2>
            <div class="form-group mb-4">
                <input type="text" id="map-search" class="form-input" style="background:var(--bg-base);color:var(--text-primary);border-color:var(--border-base);" placeholder="Search businesses, hardware, street food...">
            </div>
            <div style="display:flex;gap:var(--space-2);overflow-x:auto;padding-bottom:var(--space-2);">
                <button class="btn btn-ghost btn-xs filter-btn active" data-type="">All</button>
                <button class="btn btn-ghost btn-xs filter-btn" data-type="business">Business</button>
                <button class="btn btn-ghost btn-xs filter-btn" data-type="tourism">Tourism</button>
            </div>
        </div>

        <div id="listing-results" style="display:flex;flex-direction:column;gap:var(--space-4);">
            <!-- Populated by JS -->
            <div class="text-center p-8 text-muted">Loading local spots...</div>
        </div>
    </div>

    <!-- Main Map View -->
    <div id="map-view" style="flex:1;height:100%;position:relative;">
        <div id="map" style="height:100%;width:100%;"></div>
        
        <!-- Hover/Active Details Card (Mobile optimization or secondary info) -->
        <div id="listing-details-overlay" style="position:absolute;bottom:var(--space-6);left:50%;transform:translateX(-50%);z-index:1000;width:90%;max-width:400px;display:none;">
            <!-- Detailed info injected here -->
        </div>
    </div>
</div>

<!-- Leaflet CSS/JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<style>
    .map-listing-card { cursor: pointer; transition: background 0.2s; border-radius: var(--radius-lg); padding: var(--space-3); border: 1px solid var(--border-base); }
    .map-listing-card:hover { background: var(--gray-100); border-color: var(--primary-light); }
    .map-listing-card.active { border-color: var(--primary); background: var(--primary-100); }
    .map-listing-img { width: 60px; height: 60px; object-fit: contain; border-radius: var(--radius-md); background: var(--gray-100); flex-shrink: 0; padding: 4px; }
    .marker-label { background: var(--primary); color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-weight: 700; border: 2px solid var(--bg-surface); box-shadow: var(--shadow-md); }
    
    .action-btn { font-size: 12px; display: flex; align-items: center; gap: 4px; padding: 4px 8px; border-radius: var(--radius-md); background: var(--bg-soft); color: var(--text-main); border: 1px solid var(--border); }
    .action-btn:hover { background: var(--primary); color: white; border-color: var(--primary); }

    .leaflet-popup-content-wrapper { border-radius: var(--radius-lg); padding: 0; overflow: hidden; }
    .leaflet-popup-content { margin: 0; width: 240px !important; }
    .map-popup-card img { width: 100%; height: 120px; object-fit: cover; }
    
    @media (max-width: 768px) {
        #map-page-wrapper { flex-direction: column-reverse; }
        #map-sidebar { width: 100%; height: 250px; border-right: none; border-top: 1px solid var(--border); }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const initialLat = <?= config('lat') ?: 6.45 ?>;
    const initialLng = <?= config('lng') ?: 124.93 ?>;
    
    const map = L.map('map', { zoomControl: false }).setView([initialLat, initialLng], 14);
    L.control.zoom({ position: 'topright' }).addTo(map);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
    }).addTo(map);

    let markers = {};
    let currentData = [];

    async function fetchData(type = '') {
        try {
            const response = await fetch(`/api/listings?limit=50&type=${type}&exclude_type=creator`);
            const result = await response.json();
            if (result.success) {
                renderListings(result.data);
            }
        } catch (e) {
            console.error('Error:', e);
        }
    }

    function getStarsJS(rating, count) {
        if (!count || count === 0) return '';
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            stars += i <= Math.round(rating) ? '★' : '☆';
        }
        return `<div class="stars-mini" title="${rating}/5">${stars} <span class="count">(${count})</span></div>`;
    }

    function renderListings(data) {
        currentData = data;
        const resultsBox = document.getElementById('listing-results');
        resultsBox.innerHTML = '';
        
        // Clear old markers
        Object.values(markers).forEach(m => map.removeLayer(m));
        markers = {};

        data.forEach((item, index) => {
            const letter = String.fromCharCode(65 + index);
            
            // Add to Sidebar
            const card = document.createElement('div');
            card.className = 'map-listing-card';
            
            const isVirtual = !item.lat || !item.lng || parseFloat(item.lat) === 0;
            const isCreator = item.type === 'creator';
            const showLetter = !isCreator && !isVirtual;
            
            card.innerHTML = `
                <div style="display:flex;gap:var(--space-3);">
                    <div class="marker-label" style="flex-shrink:0; ${!showLetter ? 'background:var(--text-muted);' : ''}">${showLetter ? letter : '•'}</div>
                    <div style="flex:1;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                            <span class="badge ${isVirtual ? 'badge-creator' : 'badge-category'}" style="padding:2px 6px;">
                                <i data-lucide="${item.icon || 'tag'}" style="width:12px;height:12px;"></i>
                                ${item.category}
                            </span>
                        </div>
                        <h4 style="margin:0;font-size:15px;font-weight:600;">${item.name}</h4>
                        ${getStarsJS(item.rating.average, item.rating.count)}
                        <div style="display:flex;gap:8px;margin-top:8px;flex-wrap:wrap;">
                            <a href="${item.url}" class="action-btn"><i data-lucide="info" style="width:12px;"></i> View</a>
                            ${item.phone ? `<a href="tel:${item.phone}" class="action-btn"><i data-lucide="phone" style="width:12px;"></i> Call</a>` : ''}
                            ${item.facebook ? `<a href="${item.facebook}" target="_blank" class="action-btn"><i data-lucide="facebook" style="width:12px;"></i> FB</a>` : ''}
                        </div>
                    </div>
                    ${item.image ? `<img src="${item.image}" class="map-listing-img" alt="">` : ''}
                </div>
            `;
            card.onclick = () => focusItem(item.id, !isVirtual);
            resultsBox.appendChild(card);

            // Add Marker (Only if not a creator AND has coordinates)
            if (!isCreator && (item.lat || item.lng)) {
                const lat = parseFloat(item.lat || (initialLat + (Math.random() - 0.5) * 0.04));
                const lng = parseFloat(item.lng || (initialLng + (Math.random() - 0.5) * 0.04));
                
                const customIcon = L.divIcon({
                    html: `<div class="marker-label">${letter}</div>`,
                    className: 'custom-div-icon',
                    iconSize: [28, 28],
                    iconAnchor: [14, 14]
                });

                const marker = L.marker([lat, lng], { icon: customIcon }).addTo(map);
                
                const popupHtml = `
                    <div class="map-popup-card">
                        ${item.image ? `<img src="${item.image}" alt="">` : ''}
                        <div style="padding:var(--space-3);">
                            <h4 style="margin:0 0 4px 0;">${item.name}</h4>
                            <p style="font-size:12px;color:var(--text-muted);margin:0 0 4px 0;">${item.category}</p>
                            ${getStarsJS(item.rating.average, item.rating.count)}
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:10px;">
                                <a href="${item.url}" class="btn btn-primary btn-xs">Details</a>
                                ${item.phone ? `<a href="tel:${item.phone}" class="btn btn-ghost btn-xs">Call</a>` : ''}
                                ${item.website ? `<a href="${item.website}" target="_blank" class="btn btn-ghost btn-xs">Web</a>` : ''}
                                ${item.facebook ? `<a href="${item.facebook}" target="_blank" class="btn btn-ghost btn-xs">FB</a>` : ''}
                            </div>
                        </div>
                    </div>
                `;
                
                marker.bindPopup(popupHtml);
                markers[item.id] = marker;
            }
        });

        if (!document.getElementById('stars-css')) {
            const style = document.createElement('style');
            style.id = 'stars-css';
            style.innerHTML = `
                .stars-mini { color: #f59e0b; font-size: 14px; margin-bottom: 4px; display: flex; align-items: center; gap: 4px; }
                .stars-mini .count { color: #94a3b8; font-size: 11px; font-weight: 400; }
                .badge-creator { background: #fee2e2; color: #991b1b; } 
            `;
            document.head.appendChild(style);
        }

        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function focusItem(id, zoom = true) {
        const marker = markers[id];
        if (marker) {
            if (zoom) map.setView(marker.getLatLng(), 16);
            marker.openPopup();
            
            // Highlight in sidebar
            document.querySelectorAll('.map-listing-card').forEach(c => c.classList.remove('active'));
            // Find finding index is hard without more IDs, but this works for demo
        }
    }

    // Initial load
    fetchData();

    // Filters
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            fetchData(btn.dataset.type);
        });
    });
    
    // Search local
    document.getElementById('map-search').addEventListener('input', (e) => {
        const q = e.target.value.toLowerCase();
        const filtered = currentData.filter(i => 
            i.name.toLowerCase().includes(q) || i.category.toLowerCase().includes(q)
        );
        renderListings(filtered);
    });
});
</script>
