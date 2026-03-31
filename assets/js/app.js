/**
 * CityDirectory — Main Application JavaScript
 * Vanilla JS — no frameworks, no dependencies, fast on slow connections.
 */

// ── PWA Service Worker Registration ────────────────────────────
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}

// ── Theme Switcher ─────────────────────────────────────────────
function initTheme() {
    const toggles = document.querySelectorAll('.theme-toggle');
    const updateIcons = (isDark) => {
        document.querySelectorAll('.sun-icon').forEach(s => s.style.display = isDark ? 'block' : 'none');
        document.querySelectorAll('.moon-icon').forEach(m => m.style.display = isDark ? 'none' : 'block');
    };

    const currentTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    if (currentTheme === 'dark') {
        document.documentElement.classList.add('dark');
        updateIcons(true);
    } else {
        updateIcons(false);
    }

    toggles.forEach(btn => {
        btn.addEventListener('click', () => {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            updateIcons(isDark);
        });
    });
}
document.addEventListener('DOMContentLoaded', initTheme);

// ── Mobile Menu Toggle ─────────────────────────────────────────
document.addEventListener('click', (e) => {
    const nav = document.getElementById('nav-main');
    if (nav && nav.classList.contains('open') && !e.target.closest('.menu-toggle') && !e.target.closest('#nav-main')) {
        nav.classList.remove('open');
    }
});

// ── Lazy Load Images ───────────────────────────────────────────
if ('IntersectionObserver' in window) {
    const imgObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                }
                imgObserver.unobserve(img);
            }
        });
    }, { rootMargin: '200px' });

    document.querySelectorAll('img[data-src]').forEach((img) => imgObserver.observe(img));
}

// ── Fade In Animation on Scroll ────────────────────────────────
if ('IntersectionObserver' in window) {
    const fadeObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                fadeObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.card, .category-card, .emergency-card').forEach((el) => {
        el.style.opacity = '0';
        fadeObserver.observe(el);
    });
}

// ── Leaflet Map Helpers ────────────────────────────────────────

/**
 * Initialize a display map (for viewing a listing).
 */
function initDisplayMap(elementId, lat, lng, name, zoom = 15) {
    const map = L.map(elementId).setView([lat, lng], zoom);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap',
        maxZoom: 19,
    }).addTo(map);

    L.marker([lat, lng]).addTo(map)
        .bindPopup(`<strong>${name}</strong>`)
        .openPopup();

    return map;
}

/**
 * Initialize a map with multiple markers (for directory/explore view).
 */
function initMultiMap(elementId, listings, zoom = 13, center = null) {
    const mapCenter = center || [
        parseFloat(document.body.dataset.mapLat || 0),
        parseFloat(document.body.dataset.mapLng || 0),
    ];

    const map = L.map(elementId).setView(mapCenter, zoom);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap',
        maxZoom: 19,
    }).addTo(map);

    const bounds = L.latLngBounds();

    listings.forEach((item) => {
        if (item.lat && item.lng) {
            const marker = L.marker([item.lat, item.lng]).addTo(map);
            marker.bindPopup(`
                <div style="min-width:180px">
                    <strong><a href="${item.url}">${item.name}</a></strong>
                    ${item.category ? `<br><small>${item.category}</small>` : ''}
                    ${item.address ? `<br><small>📍 ${item.address}</small>` : ''}
                </div>
            `);
            bounds.extend([item.lat, item.lng]);
        }
    });

    if (listings.length > 1 && bounds.isValid()) {
        map.fitBounds(bounds, { padding: [30, 30] });
    }

    return map;
}

/**
 * Initialize a map picker (for listing submission).
 * Clicking places a marker and fills hidden lat/lng inputs.
 */
function initMapPicker(elementId, latInputId, lngInputId, addressInputId, defaultLat, defaultLng, defaultZoom = 13) {
    const map = L.map(elementId).setView([defaultLat, defaultLng], defaultZoom);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap',
        maxZoom: 19,
    }).addTo(map);

    let marker = null;

    map.on('click', (e) => {
        const { lat, lng } = e.latlng;

        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            marker.on('dragend', () => {
                const pos = marker.getLatLng();
                updateLocationInputs(pos.lat, pos.lng);
            });
        }

        updateLocationInputs(lat, lng);
    });

    function updateLocationInputs(lat, lng) {
        document.getElementById(latInputId).value = lat.toFixed(7);
        document.getElementById(lngInputId).value = lng.toFixed(7);

        // Reverse geocode for address suggestion
        if (addressInputId) {
            fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&zoom=18`)
                .then((r) => r.json())
                .then((data) => {
                    if (data.display_name) {
                        const addrInput = document.getElementById(addressInputId);
                        if (addrInput && !addrInput.value) {
                            addrInput.value = data.display_name;
                        }
                    }
                })
                .catch(() => {});
        }
    }

    return map;
}

// ── Hours of Operation Form Helper ─────────────────────────────
function toggleHoursRow(checkbox, dayKey) {
    const openInput = document.getElementById(`hours_${dayKey}_open`);
    const closeInput = document.getElementById(`hours_${dayKey}_close`);
    if (checkbox.checked) {
        openInput.disabled = false;
        closeInput.disabled = false;
    } else {
        openInput.disabled = true;
        closeInput.disabled = true;
        openInput.value = '';
        closeInput.value = '';
    }
}

// ── Share Button ───────────────────────────────────────────────
function shareListing(title, url) {
    if (navigator.share) {
        navigator.share({ title, url }).catch(() => {});
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(url).then(() => {
            const btn = event.target;
            const original = btn.textContent;
            btn.textContent = 'Link Copied!';
            setTimeout(() => btn.textContent = original, 2000);
        }).catch(() => {
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
        });
    }
}

// ── Directions Helper ──────────────────────────────────────────
function openDirections(lat, lng, name) {
    // Try Google Maps first, fallback to Waze
    const googleUrl = `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}&travelmode=driving`;
    window.open(googleUrl, '_blank');
}

// ── Search with Debounce ───────────────────────────────────────
function debounce(func, wait = 300) {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

// Live search (optional — for AJAX search)
const searchInput = document.querySelector('.search-bar input[name="q"]');
if (searchInput) {
    const searchResults = document.getElementById('search-suggestions');
    if (searchResults) {
        searchInput.addEventListener('input', debounce((e) => {
            const q = e.target.value.trim();
            if (q.length < 2) {
                searchResults.innerHTML = '';
                searchResults.classList.add('hidden');
                return;
            }
            fetch(`/api/search?q=${encodeURIComponent(q)}&limit=5`)
                .then((r) => r.json())
                .then((data) => {
                    if (data.results && data.results.length) {
                        searchResults.innerHTML = data.results.map((r) =>
                            `<a href="${r.url}" class="search-suggestion">${r.icon || '📍'} ${r.name} <small>${r.category}</small></a>`
                        ).join('');
                        searchResults.classList.remove('hidden');
                    } else {
                        searchResults.classList.add('hidden');
                    }
                })
                .catch(() => {});
        }));
    }
}

// ── Flash Message Auto-Dismiss ─────────────────────────────────
document.querySelectorAll('.flash').forEach((flash) => {
    setTimeout(() => {
        flash.style.transition = 'opacity 0.3s, transform 0.3s';
        flash.style.opacity = '0';
        flash.style.transform = 'translateY(-10px)';
        setTimeout(() => flash.remove(), 300);
    }, 5000);
});
