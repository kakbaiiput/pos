const CACHE_NAME = 'pos-app-v2';
const STATIC_CACHE = 'pos-static-v2';
const DYNAMIC_CACHE = 'pos-dynamic-v2';

// Only cache same-origin assets on install (CDN blocked by CORS)
const STATIC_ASSETS = ['/'];

// Offline API data endpoints
const OFFLINE_APIS = [
    '/api/offline/products',
    '/api/offline/stocks',
    '/api/offline/categories',
];

// ── Install ───────────────────────────────────────────────────────────────────
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then((cache) => cache.addAll(STATIC_ASSETS))
            .then(() => self.skipWaiting())
    );
});

// ── Activate ──────────────────────────────────────────────────────────────────
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys.filter((k) => k !== STATIC_CACHE && k !== DYNAMIC_CACHE)
                    .map((k) => caches.delete(k))
            )
        ).then(() => self.clients.claim())
    );
});

// ── Fetch ─────────────────────────────────────────────────────────────────────
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    if (request.method !== 'GET' || !url.protocol.startsWith('http')) return;

    // Skip cross-origin (CDN etc) — let browser handle normally
    if (url.origin !== self.location.origin) return;

    // Offline API data — cache-first, update in background
    if (OFFLINE_APIS.some((p) => url.pathname === p)) {
        event.respondWith(
            caches.open(DYNAMIC_CACHE).then((cache) =>
                cache.match(request).then((cached) => {
                    const networkFetch = fetch(request).then((res) => {
                        cache.put(request, res.clone());
                        return res;
                    });
                    return cached || networkFetch;
                })
            )
        );
        return;
    }

    // HTML pages — network first, fall back to cache
    if (request.headers.get('accept')?.includes('text/html')) {
        event.respondWith(
            fetch(request)
                .then((res) => {
                    caches.open(DYNAMIC_CACHE).then((c) => c.put(request, res.clone()));
                    return res;
                })
                .catch(() => caches.match(request).then((r) => r || caches.match('/')))
        );
        return;
    }

    // Static assets — network first, cache fallback
    event.respondWith(
        fetch(request)
            .then((res) => {
                if (res.ok) caches.open(DYNAMIC_CACHE).then((c) => c.put(request, res.clone()));
                return res;
            })
            .catch(() => caches.match(request))
    );
});

// ── Background Sync ───────────────────────────────────────────────────────────
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-transactions') {
        event.waitUntil(syncPendingTransactions());
    }
});

async function syncPendingTransactions() {
    const transactions = await getAllOfflineTx();
    for (const tx of transactions) {
        try {
            const res = await fetch('/api/offline/transactions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': tx.csrfToken || '',
                },
                body: JSON.stringify(tx),
            });
            if (res.ok) {
                await deleteOfflineTx(tx.offlineId);
                const clients = await self.clients.matchAll();
                clients.forEach((c) => c.postMessage({ type: 'SYNC_SUCCESS', offlineId: tx.offlineId }));
            }
        } catch (e) {
            console.error('[SW] Sync failed:', tx.offlineId, e);
        }
    }
}

// ── IndexedDB helpers ─────────────────────────────────────────────────────────
function openOfflineDB() {
    return new Promise((resolve, reject) => {
        const req = indexedDB.open('pos-offline', 2);
        req.onupgradeneeded = (e) => {
            const db = e.target.result;
            if (!db.objectStoreNames.contains('transactions')) {
                db.createObjectStore('transactions', { keyPath: 'offlineId' });
            }
        };
        req.onsuccess = (e) => resolve(e.target.result);
        req.onerror = () => reject(req.error);
    });
}

async function getAllOfflineTx() {
    const db = await openOfflineDB();
    return new Promise((resolve) => {
        const tx = db.transaction('transactions', 'readonly');
        const req = tx.objectStore('transactions').getAll();
        req.onsuccess = () => resolve(req.result || []);
    });
}

async function deleteOfflineTx(offlineId) {
    const db = await openOfflineDB();
    return new Promise((resolve) => {
        const tx = db.transaction('transactions', 'readwrite');
        tx.objectStore('transactions').delete(offlineId);
        tx.oncomplete = resolve;
    });
}

self.addEventListener('push', (event) => {
    if (event.data) {
        const data = event.data.json();
        self.registration.showNotification(data.title, {
            body: data.body,
            icon: '/pwa/icon-192.png',
        });
    }
});
