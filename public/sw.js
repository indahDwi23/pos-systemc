const CACHE_NAME = 'kasir-sultan-v1';
const urlsToCache = [
    '/',
    '/css/style.css',
    '/css/portal.css',
    '/css/dashboard-modern.css',
    '/js/app.js',
    '/js/jquery-3.6.3.min.js',
    '/js/order.js',
    '/js/formatmoney.js',
    '/images/logofood.png',
    '/images/logofood.ico',
    '/fontawesome-free-6.2.1-web/css/all.css',
    '/fontawesome-free-6.2.1-web/webfonts/fa-solid-900.woff2'
];

// Install event
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(urlsToCache))
    );
    self.skipWaiting();
});

// Fetch event
self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                // Cache hit - return response
                if (response) {
                    return response;
                }
                return fetch(event.request).then(response => {
                    // Check if valid response
                    if (!response || response.status !== 200 || response.type !== 'basic') {
                        return response;
                    }
                    // Clone the response
                    const responseToCache = response.clone();
                    caches.open(CACHE_NAME)
                        .then(cache => {
                            cache.put(event.request, responseToCache);
                        });
                    return response;
                });
            })
    );
});

// Activate event
self.addEventListener('activate', event => {
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheWhitelist.indexOf(cacheName) === -1) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    self.clients.claim();
});
