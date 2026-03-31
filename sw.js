/**
 * CityDirectory — Service Worker
 * Caches pages and assets for offline/slow-connection performance.
 */
const CACHE_NAME = 'citydirectory-v1';
const STATIC_ASSETS = [
    '/',
    '/assets/css/style.css',
    '/assets/js/app.js',
    '/manifest.json',
];

// Install — cache static assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(STATIC_ASSETS))
    );
    self.skipWaiting();
});

// Activate — clean old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((names) =>
            Promise.all(names.filter((n) => n !== CACHE_NAME).map((n) => caches.delete(n)))
        )
    );
    self.clients.claim();
});

// Fetch — network-first for HTML, cache-first for assets
self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);

    // Skip non-GET and admin routes
    if (event.request.method !== 'GET' || url.pathname.startsWith('/admin')) return;

    // Cache-first for static assets
    if (url.pathname.match(/\.(css|js|png|jpg|jpeg|webp|gif|svg|ico|woff2?)$/)) {
        event.respondWith(
            caches.match(event.request).then((cached) =>
                cached || fetch(event.request).then((response) => {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
                    return response;
                })
            )
        );
        return;
    }

    // Network-first for HTML pages (stale-while-revalidate)
    event.respondWith(
        fetch(event.request)
            .then((response) => {
                const clone = response.clone();
                caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
                return response;
            })
            .catch(() => caches.match(event.request).then((cached) =>
                cached || caches.match('/'))
            )
    );
});
