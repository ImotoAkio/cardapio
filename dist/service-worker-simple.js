// Service Worker Simples para Teste
const CACHE_NAME = 'tempero-cafe-simple-v1.0.0';

// Assets essenciais mínimos
const STATIC_ASSETS = [
    './',
    './home.php',
    './dist/css/bootstrap.min.css',
    './dist/js/bootstrap.bundle.min.js',
    './dist/js/jquery.min.js',
    './dist/img/icons/android-icon-192x192.png',
    './dist/img/icons/icon-512x512.png'
];

// Install Event
self.addEventListener('install', function (event) {
    console.log('Service Worker Simple: Instalando...');
    event.waitUntil(
        caches.open(CACHE_NAME).then(function (cache) {
            console.log('Service Worker Simple: Cacheando assets...');
            return cache.addAll(STATIC_ASSETS);
        }).then(() => {
            console.log('Service Worker Simple: Instalação concluída');
            return self.skipWaiting();
        }).catch(error => {
            console.error('Service Worker Simple: Erro na instalação:', error);
            return self.skipWaiting();
        })
    );
});

// Activate Event
self.addEventListener('activate', function (event) {
    console.log('Service Worker Simple: Ativando...');
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(keys
                .filter(key => key !== CACHE_NAME)
                .map(key => {
                    console.log('Service Worker Simple: Removendo cache antigo:', key);
                    return caches.delete(key);
                })
            );
        }).then(() => {
            console.log('Service Worker Simple: Ativação concluída');
            return self.clients.claim();
        })
    );
});

// Fetch Event - Cache First simples
self.addEventListener('fetch', function (event) {
    const request = event.request;
    
    // Ignorar requests não-HTTP
    if (!request.url.startsWith('http')) {
        return;
    }

    event.respondWith(
        caches.match(request).then(function (response) {
            // Retornar do cache se disponível
            if (response) {
                return response;
            }
            
            // Buscar da rede
            return fetch(request).then(function (response) {
                // Verificar se a resposta é válida
                if (!response || response.status !== 200 || response.type !== 'basic') {
                    return response;
                }
                
                // Clonar a resposta
                const responseToCache = response.clone();
                
                // Adicionar ao cache
                caches.open(CACHE_NAME).then(function (cache) {
                    cache.put(request, responseToCache);
                });
                
                return response;
            });
        })
    );
});

// Message Handler
self.addEventListener('message', event => {
    if (event.data && event.data.action === 'skipWaiting') {
        console.log('Service Worker Simple: Recebida mensagem skipWaiting');
        self.skipWaiting();
    }
});
