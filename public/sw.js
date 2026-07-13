// ILOBI PWA Service Worker
const CACHE_NAME = 'ilobi-pwa-v1';
const STATIC_ASSETS = [
    '/pwa',
    '/pwa/login',
    '/manifest.json',
    '/images/ilobilogo1.png',
    '/images/fav.png'
];

// Install - cache static assets
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(STATIC_ASSETS))
            .then(() => self.skipWaiting())
    );
});

// Activate - clean old caches
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.filter(name => name !== CACHE_NAME)
                    .map(name => caches.delete(name))
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch - serve from cache if available, else network
self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => response || fetch(event.request))
    );
});

// Push Notifications
self.addEventListener('push', event => {
    const data = event.data.json();
    const options = {
        body: data.body || 'You have a new notification',
        icon: '/images/ilobilogo1.png',
        badge: '/images/fav.png',
        data: {
            url: data.url || '/pwa/dashboard'
        },
        actions: data.actions || [
            { action: 'open', title: 'Open' }
        ]
    };
    event.waitUntil(
        self.registration.showNotification(data.title || 'ILOBI', options)
    );
});

// Notification Click
self.addEventListener('notificationclick', event => {
    event.notification.close();
    event.waitUntil(
        clients.openWindow(event.notification.data.url || '/pwa/dashboard')
    );
});