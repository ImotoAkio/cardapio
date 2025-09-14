// -----------------------------------------------------------------
// Tempero e Café - Service Worker Otimizado
// PWA para Produtos Naturais e Orgânicos
// -----------------------------------------------------------------

const CACHE_NAME = 'tempero-cafe-v1.0.0';
const STATIC_CACHE = 'static-cache-v1.0.0';
const DYNAMIC_CACHE = 'dynamic-cache-v1.0.0';

// Assets essenciais para cache estático
const STATIC_ASSETS = [
    './',
    './home.php',
    './dist/css/bootstrap.min.css',
    './dist/css/tabler-icons.min.css',
    './dist/css/animate.css',
    './dist/css/owl.carousel.min.css',
    './dist/css/magnific-popup.css',
    './dist/css/nice-select.css',
    './dist/style.css',
    './dist/css/avatar-styles.css',
    './dist/js/bootstrap.bundle.min.js',
    './dist/js/jquery.min.js',
    './dist/js/active.js',
    './dist/img/icons/android-icon-192x192.png',
    './dist/img/icons/icon-512x512.png',
    './offline.html'
];

// Estratégias de cache
const CACHE_STRATEGIES = {
    // Cache First: Para assets estáticos
    static: ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'woff', 'woff2', 'ico'],
    // Network First: Para páginas dinâmicas
    dynamic: ['php', 'html'],
    // Stale While Revalidate: Para APIs
    api: ['api/']
};

// Install Event - Cache estático
self.addEventListener('install', function (event) {
    console.log('Service Worker: Instalando...');
    event.waitUntil(
        caches.open(STATIC_CACHE).then(function (cache) {
            console.log('Service Worker: Cacheando assets estáticos...');
            // Tentar adicionar assets um por um para evitar falhas
            return Promise.allSettled(
                STATIC_ASSETS.map(asset => 
                    cache.add(asset).catch(error => {
                        console.warn('Service Worker: Falha ao cachear:', asset, error);
                        return null;
                    })
                )
            );
        }).then(() => {
            console.log('Service Worker: Instalação concluída');
            // Forçar ativação imediata
            return self.skipWaiting();
        }).catch(error => {
            console.error('Service Worker: Erro na instalação:', error);
            // Mesmo com erro, tentar ativar
            return self.skipWaiting();
        })
    );
});

// Activate Event - Limpar caches antigos
self.addEventListener('activate', function (event) {
    console.log('Service Worker: Ativando...');
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(keys
                .filter(key => key !== STATIC_CACHE && key !== DYNAMIC_CACHE)
                .map(key => {
                    console.log('Service Worker: Removendo cache antigo:', key);
                    return caches.delete(key);
                })
            );
        }).then(() => {
            console.log('Service Worker: Ativação concluída');
            return self.clients.claim();
        })
    );
});

// Fetch Event - Estratégias de cache
self.addEventListener('fetch', function (event) {
    const request = event.request;
    const url = new URL(request.url);
    
    // Ignorar requests não-HTTP
    if (!request.url.startsWith('http')) {
        return;
    }

    // Estratégia: Cache First para assets estáticos
    if (isStaticAsset(request)) {
        event.respondWith(cacheFirst(request));
        return;
    }

    // Estratégia: Network First para páginas dinâmicas
    if (isDynamicPage(request)) {
        event.respondWith(networkFirst(request));
        return;
    }

    // Estratégia: Stale While Revalidate para APIs
    if (isApiRequest(request)) {
        event.respondWith(staleWhileRevalidate(request));
        return;
    }

    // Estratégia padrão: Network First
    event.respondWith(networkFirst(request));
});

// Funções auxiliares
function isStaticAsset(request) {
    const url = new URL(request.url);
    const extension = url.pathname.split('.').pop().toLowerCase();
    return CACHE_STRATEGIES.static.includes(extension);
}

function isDynamicPage(request) {
    const url = new URL(request.url);
    const extension = url.pathname.split('.').pop().toLowerCase();
    return CACHE_STRATEGIES.dynamic.includes(extension);
}

function isApiRequest(request) {
    const url = new URL(request.url);
    return url.pathname.startsWith('/api/');
}

// Cache First Strategy
async function cacheFirst(request) {
    try {
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            const cache = await caches.open(STATIC_CACHE);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch (error) {
        console.log('Cache First failed:', error);
        return new Response('Asset não encontrado', { status: 404 });
    }
}

// Network First Strategy
async function networkFirst(request) {
    try {
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch (error) {
        console.log('Network First failed, trying cache:', error);
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Fallback para página offline
        if (request.mode === 'navigate') {
            return caches.match('./offline.html');
        }
        
        return new Response('Recurso não disponível offline', { status: 503 });
    }
}

// Stale While Revalidate Strategy
async function staleWhileRevalidate(request) {
    const cache = await caches.open(DYNAMIC_CACHE);
    const cachedResponse = await cache.match(request);
    
    const fetchPromise = fetch(request).then(networkResponse => {
        if (networkResponse.ok) {
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    }).catch(error => {
        console.log('Network request failed:', error);
        return cachedResponse;
    });
    
    return cachedResponse || fetchPromise;
}

// Background Sync (se disponível)
self.addEventListener('sync', event => {
    if (event.tag === 'background-sync') {
        event.waitUntil(doBackgroundSync());
    }
});

async function doBackgroundSync() {
    console.log('Service Worker: Executando background sync...');
    // Implementar sincronização de dados offline
}

// Push Notifications (se disponível)
self.addEventListener('push', event => {
    if (event.data) {
        const data = event.data.json();
        const options = {
            body: data.body || 'Nova notificação do Tempero e Café',
            icon: './dist/img/icons/android-icon-192x192.png',
            badge: './dist/img/icons/favicon-32x32.png',
            vibrate: [200, 100, 200],
            data: {
                url: data.url || './home.php'
            }
        };
        
        event.waitUntil(
            self.registration.showNotification(data.title || 'Tempero e Café', options)
        );
    }
});

// Notification Click
self.addEventListener('notificationclick', event => {
    event.notification.close();
    
    if (event.notification.data && event.notification.data.url) {
        event.waitUntil(
            clients.openWindow(event.notification.data.url)
        );
    }
});

// Message Handler
self.addEventListener('message', event => {
    if (event.data && event.data.action === 'skipWaiting') {
        console.log('Service Worker: Recebida mensagem skipWaiting');
        self.skipWaiting();
    }
});