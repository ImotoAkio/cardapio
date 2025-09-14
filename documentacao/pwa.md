# 📱 PWA e Recursos Mobile - Tempero e Café

## 📋 Visão Geral

O Tempero e Café é uma Progressive Web App (PWA) completa que oferece experiência de aplicativo nativo em dispositivos móveis. Implementa todas as funcionalidades necessárias para ser instalada como app e funcionar offline.

## 🎯 Características PWA

- **Instalável**: Pode ser instalada na tela inicial
- **Offline**: Funciona sem conexão com internet
- **Responsiva**: Adapta-se a qualquer tamanho de tela
- **Rápida**: Carregamento instantâneo
- **Engajante**: Notificações push e atualizações

## 📱 Web App Manifest

### Arquivo `manifest.json`

```json
{
    "name": "Tempero e Café - Produtos Naturais",
    "short_name": "Tempero e Café",
    "description": "E-commerce de produtos naturais, orgânicos e suplementos",
    "start_url": "/home.php",
    "display": "standalone",
    "background_color": "#ffffff",
    "theme_color": "#d3a74e",
    "orientation": "portrait-primary",
    "scope": "/",
    "lang": "pt-BR",
    "dir": "ltr",
    "categories": ["shopping", "food", "lifestyle", "health"],
    "icons": [
        {
            "src": "dist/img/icons/icon-72x72.png",
            "sizes": "72x72",
            "type": "image/png",
            "purpose": "any"
        },
        {
            "src": "dist/img/icons/icon-96x96.png",
            "sizes": "96x96",
            "type": "image/png",
            "purpose": "any"
        },
        {
            "src": "dist/img/icons/icon-128x128.png",
            "sizes": "128x128",
            "type": "image/png",
            "purpose": "any"
        },
        {
            "src": "dist/img/icons/icon-144x144.png",
            "sizes": "144x144",
            "type": "image/png",
            "purpose": "any"
        },
        {
            "src": "dist/img/icons/icon-152x152.png",
            "sizes": "152x152",
            "type": "image/png",
            "purpose": "any"
        },
        {
            "src": "dist/img/icons/icon-192x192.png",
            "sizes": "192x192",
            "type": "image/png",
            "purpose": "any maskable"
        },
        {
            "src": "dist/img/icons/icon-384x384.png",
            "sizes": "384x384",
            "type": "image/png",
            "purpose": "any"
        },
        {
            "src": "dist/img/icons/icon-512x512.png",
            "sizes": "512x512",
            "type": "image/png",
            "purpose": "any maskable"
        }
    ],
    "screenshots": [
        {
            "src": "dist/img/screenshots/mobile-home.png",
            "sizes": "390x844",
            "type": "image/png",
            "form_factor": "narrow"
        },
        {
            "src": "dist/img/screenshots/desktop-home.png",
            "sizes": "1280x720",
            "type": "image/png",
            "form_factor": "wide"
        }
    ],
    "shortcuts": [
        {
            "name": "Buscar Produtos",
            "short_name": "Buscar",
            "description": "Buscar produtos na loja",
            "url": "/search.php",
            "icons": [
                {
                    "src": "dist/img/icons/search-96x96.png",
                    "sizes": "96x96"
                }
            ]
        },
        {
            "name": "Meu Carrinho",
            "short_name": "Carrinho",
            "description": "Ver itens no carrinho",
            "url": "/cart.php",
            "icons": [
                {
                    "src": "dist/img/icons/cart-96x96.png",
                    "sizes": "96x96"
                }
            ]
        },
        {
            "name": "Meus Pedidos",
            "short_name": "Pedidos",
            "description": "Ver histórico de pedidos",
            "url": "/my-orders.php",
            "icons": [
                {
                    "src": "dist/img/icons/orders-96x96.png",
                    "sizes": "96x96"
                }
            ]
        }
    ],
    "related_applications": [],
    "prefer_related_applications": false
}
```

## 🔧 Service Worker

### Arquivo `service-worker.js`

```javascript
const CACHE_NAME = 'tempero-e-cafe-v1.0.0';
const STATIC_CACHE = 'static-v1';
const DYNAMIC_CACHE = 'dynamic-v1';

// URLs para cache estático
const STATIC_URLS = [
    '/',
    '/home.php',
    '/dist/style.css',
    '/dist/css/bootstrap.min.css',
    '/dist/css/tabler-icons.min.css',
    '/dist/js/bootstrap.bundle.min.js',
    '/dist/js/jquery.min.js',
    '/dist/js/active.js',
    '/dist/js/pwa.js',
    '/dist/img/core-img/logo_cafe.png',
    '/dist/img/icons/icon-192x192.png',
    '/dist/img/icons/icon-512x512.png',
    '/dist/img/bg-img/1.jpg',
    '/dist/img/bg-img/2.jpg',
    '/dist/img/bg-img/3.jpg'
];

// URLs para cache dinâmico
const DYNAMIC_URLS = [
    '/api/',
    '/cart_api.php',
    '/product.php',
    '/category.php',
    '/shop.php'
];

// Install Event
self.addEventListener('install', (event) => {
    console.log('Service Worker: Installing...');
    
    event.waitUntil(
        Promise.all([
            // Cache estático
            caches.open(STATIC_CACHE).then((cache) => {
                console.log('Service Worker: Caching static files');
                return cache.addAll(STATIC_URLS);
            }),
            // Cache dinâmico
            caches.open(DYNAMIC_CACHE).then((cache) => {
                console.log('Service Worker: Dynamic cache ready');
                return cache;
            })
        ]).then(() => {
            console.log('Service Worker: Installation complete');
            return self.skipWaiting();
        })
    );
});

// Activate Event
self.addEventListener('activate', (event) => {
    console.log('Service Worker: Activating...');
    
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== STATIC_CACHE && cacheName !== DYNAMIC_CACHE) {
                        console.log('Service Worker: Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => {
            console.log('Service Worker: Activation complete');
            return self.clients.claim();
        })
    );
});

// Fetch Event
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);
    
    // Estratégia de cache baseada no tipo de recurso
    if (request.method === 'GET') {
        if (isStaticAsset(request)) {
            // Cache First para assets estáticos
            event.respondWith(cacheFirst(request, STATIC_CACHE));
        } else if (isAPIRequest(request)) {
            // Network First para APIs
            event.respondWith(networkFirst(request, DYNAMIC_CACHE));
        } else if (isPageRequest(request)) {
            // Stale While Revalidate para páginas
            event.respondWith(staleWhileRevalidate(request, DYNAMIC_CACHE));
        } else {
            // Network First para outros recursos
            event.respondWith(networkFirst(request, DYNAMIC_CACHE));
        }
    } else {
        // Para métodos não-GET, sempre usar network
        event.respondWith(fetch(request));
    }
});

// Estratégias de Cache

// Cache First - para assets estáticos
async function cacheFirst(request, cacheName) {
    try {
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            const cache = await caches.open(cacheName);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        console.error('Cache First failed:', error);
        return new Response('Offline content not available', { status: 503 });
    }
}

// Network First - para APIs e dados dinâmicos
async function networkFirst(request, cacheName) {
    try {
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            const cache = await caches.open(cacheName);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch (error) {
        console.log('Network failed, trying cache:', error);
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Retornar página offline para navegação
        if (request.destination === 'document') {
            return caches.match('/offline.html');
        }
        
        return new Response('Offline content not available', { status: 503 });
    }
}

// Stale While Revalidate - para páginas
async function staleWhileRevalidate(request, cacheName) {
    const cache = await caches.open(cacheName);
    const cachedResponse = await cache.match(request);
    
    const fetchPromise = fetch(request).then((networkResponse) => {
        if (networkResponse.ok) {
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    }).catch(() => cachedResponse);
    
    return cachedResponse || fetchPromise;
}

// Helper Functions
function isStaticAsset(request) {
    return request.destination === 'style' ||
           request.destination === 'script' ||
           request.destination === 'image' ||
           request.destination === 'font' ||
           request.url.includes('/dist/');
}

function isAPIRequest(request) {
    return request.url.includes('/api/') ||
           request.url.includes('cart_api.php') ||
           request.url.includes('search_suggestions.php');
}

function isPageRequest(request) {
    return request.destination === 'document' ||
           request.url.endsWith('.php') ||
           request.url === '/' ||
           request.url.endsWith('/');
}

// Background Sync
self.addEventListener('sync', (event) => {
    console.log('Service Worker: Background sync triggered');
    
    if (event.tag === 'cart-sync') {
        event.waitUntil(syncCartData());
    } else if (event.tag === 'order-sync') {
        event.waitUntil(syncOrderData());
    }
});

// Sync cart data when back online
async function syncCartData() {
    try {
        const cartData = await getStoredCartData();
        if (cartData && cartData.length > 0) {
            await fetch('/cart_api.php?action=sync', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(cartData)
            });
            console.log('Cart data synced successfully');
        }
    } catch (error) {
        console.error('Cart sync failed:', error);
    }
}

// Sync order data when back online
async function syncOrderData() {
    try {
        const orderData = await getStoredOrderData();
        if (orderData) {
            await fetch('/api/sync_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(orderData)
            });
            console.log('Order data synced successfully');
        }
    } catch (error) {
        console.error('Order sync failed:', error);
    }
}

// Push Notifications
self.addEventListener('push', (event) => {
    console.log('Service Worker: Push notification received');
    
    const options = {
        body: event.data ? event.data.text() : 'Nova notificação do Tempero e Café',
        icon: '/dist/img/icons/icon-192x192.png',
        badge: '/dist/img/icons/icon-72x72.png',
        vibrate: [200, 100, 200],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        },
        actions: [
            {
                action: 'explore',
                title: 'Ver Produtos',
                icon: '/dist/img/icons/explore.png'
            },
            {
                action: 'close',
                title: 'Fechar',
                icon: '/dist/img/icons/close.png'
            }
        ]
    };
    
    event.waitUntil(
        self.registration.showNotification('Tempero e Café', options)
    );
});

// Notification Click
self.addEventListener('notificationclick', (event) => {
    console.log('Service Worker: Notification clicked');
    
    event.notification.close();
    
    if (event.action === 'explore') {
        event.waitUntil(
            clients.openWindow('/shop.php')
        );
    } else if (event.action === 'close') {
        // Just close the notification
    } else {
        // Default action - open the app
        event.waitUntil(
            clients.openWindow('/home.php')
        );
    }
});

// Message handling
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data && event.data.type === 'GET_VERSION') {
        event.ports[0].postMessage({ version: CACHE_NAME });
    }
});
```

## 📱 PWA Installation Manager

### Arquivo `pwa.js`

```javascript
class PWAInstaller {
    constructor() {
        this.deferredPrompt = null;
        this.isInstalled = false;
        this.isStandalone = false;
        
        this.init();
    }
    
    init() {
        this.checkInstallationStatus();
        this.bindEvents();
        this.registerServiceWorker();
        this.requestNotificationPermission();
    }
    
    checkInstallationStatus() {
        // Verificar se está rodando como PWA instalada
        this.isStandalone = window.matchMedia('(display-mode: standalone)').matches ||
                           window.navigator.standalone ||
                           document.referrer.includes('android-app://');
        
        // Verificar se já foi instalada
        this.isInstalled = localStorage.getItem('pwa-installed') === 'true';
        
        if (this.isInstalled || this.isStandalone) {
            this.hideInstallButton();
        }
    }
    
    bindEvents() {
        // Evento antes da instalação
        window.addEventListener('beforeinstallprompt', (e) => {
            console.log('PWA: beforeinstallprompt event fired');
            e.preventDefault();
            this.deferredPrompt = e;
            this.showInstallButton();
        });
        
        // Evento após instalação
        window.addEventListener('appinstalled', () => {
            console.log('PWA: App installed successfully');
            this.isInstalled = true;
            this.isStandalone = true;
            localStorage.setItem('pwa-installed', 'true');
            this.hideInstallButton();
            this.showInstallSuccessMessage();
        });
        
        // Botão de instalação
        const installBtn = document.getElementById('installSuha');
        if (installBtn) {
            installBtn.addEventListener('click', () => this.installApp());
        }
        
        // Botão de fechar
        const closeBtn = document.querySelector('.btn-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.hideInstallButton());
        }
        
        // Detectar mudanças na conectividade
        window.addEventListener('online', () => {
            this.showOnlineMessage();
            this.syncOfflineData();
        });
        
        window.addEventListener('offline', () => {
            this.showOfflineMessage();
        });
    }
    
    async registerServiceWorker() {
        if ('serviceWorker' in navigator) {
            try {
                const registration = await navigator.serviceWorker.register('/service-worker.js');
                console.log('PWA: Service Worker registered successfully:', registration);
                
                // Verificar atualizações
                registration.addEventListener('updatefound', () => {
                    this.handleServiceWorkerUpdate(registration);
                });
                
                // Verificar se há atualização disponível
                if (registration.waiting) {
                    this.showUpdateAvailable();
                }
                
            } catch (error) {
                console.error('PWA: Service Worker registration failed:', error);
            }
        } else {
            console.log('PWA: Service Worker not supported');
        }
    }
    
    handleServiceWorkerUpdate(registration) {
        const newWorker = registration.installing;
        
        newWorker.addEventListener('statechange', () => {
            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                this.showUpdateAvailable();
            }
        });
    }
    
    showUpdateAvailable() {
        const updateToast = document.createElement('div');
        updateToast.className = 'toast update-toast position-fixed';
        updateToast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
        updateToast.innerHTML = `
            <div class="toast-body">
                <div class="d-flex align-items-center">
                    <i class="ti ti-refresh me-2"></i>
                    <span>Nova versão disponível!</span>
                    <button class="btn btn-sm btn-primary ms-auto me-2" onclick="updateApp()">
                        Atualizar
                    </button>
                    <button class="btn-close" onclick="this.closest('.toast').remove()"></button>
                </div>
            </div>
        `;
        
        document.body.appendChild(updateToast);
        
        // Auto-hide após 10 segundos
        setTimeout(() => {
            if (updateToast.parentNode) {
                updateToast.remove();
            }
        }, 10000);
    }
    
    async installApp() {
        if (this.deferredPrompt) {
            try {
                this.deferredPrompt.prompt();
                const { outcome } = await this.deferredPrompt.userChoice;
                
                console.log('PWA: User choice outcome:', outcome);
                
                if (outcome === 'accepted') {
                    console.log('PWA: User accepted the install prompt');
                } else {
                    console.log('PWA: User dismissed the install prompt');
                }
                
                this.deferredPrompt = null;
                this.hideInstallButton();
                
            } catch (error) {
                console.error('PWA: Install prompt failed:', error);
            }
        } else {
            // Fallback para navegadores que não suportam beforeinstallprompt
            this.showManualInstallInstructions();
        }
    }
    
    showInstallButton() {
        const installWrap = document.getElementById('installWrap');
        if (installWrap) {
            installWrap.style.display = 'block';
            installWrap.classList.add('show');
        }
    }
    
    hideInstallButton() {
        const installWrap = document.getElementById('installWrap');
        if (installWrap) {
            installWrap.style.display = 'none';
            installWrap.classList.remove('show');
        }
    }
    
    showInstallSuccessMessage() {
        const successToast = document.createElement('div');
        successToast.className = 'toast position-fixed';
        successToast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
        successToast.innerHTML = `
            <div class="toast-body bg-success text-white">
                <i class="ti ti-check me-2"></i>
                App instalado com sucesso!
            </div>
        `;
        
        document.body.appendChild(successToast);
        
        setTimeout(() => {
            successToast.remove();
        }, 3000);
    }
    
    showManualInstallInstructions() {
        const instructions = document.createElement('div');
        instructions.className = 'modal fade';
        instructions.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Instalar App</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Para instalar o Tempero e Café como app:</p>
                        <ul>
                            <li><strong>Chrome/Edge:</strong> Menu → "Instalar Tempero e Café"</li>
                            <li><strong>Safari:</strong> Compartilhar → "Adicionar à Tela Inicial"</li>
                            <li><strong>Firefox:</strong> Menu → "Instalar"</li>
                        </ul>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(instructions);
        const modal = new bootstrap.Modal(instructions);
        modal.show();
        
        instructions.addEventListener('hidden.bs.modal', () => {
            instructions.remove();
        });
    }
    
    async requestNotificationPermission() {
        if ('Notification' in window && Notification.permission === 'default') {
            try {
                const permission = await Notification.requestPermission();
                console.log('PWA: Notification permission:', permission);
            } catch (error) {
                console.error('PWA: Notification permission request failed:', error);
            }
        }
    }
    
    showOnlineMessage() {
        const onlineToast = document.createElement('div');
        onlineToast.className = 'toast position-fixed';
        onlineToast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
        onlineToast.innerHTML = `
            <div class="toast-body bg-success text-white">
                <i class="ti ti-wifi me-2"></i>
                Conectado à internet
            </div>
        `;
        
        document.body.appendChild(onlineToast);
        
        setTimeout(() => {
            onlineToast.remove();
        }, 2000);
    }
    
    showOfflineMessage() {
        const offlineToast = document.createElement('div');
        offlineToast.className = 'toast position-fixed';
        offlineToast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
        offlineToast.innerHTML = `
            <div class="toast-body bg-warning text-dark">
                <i class="ti ti-wifi-off me-2"></i>
                Modo offline ativado
            </div>
        `;
        
        document.body.appendChild(offlineToast);
        
        setTimeout(() => {
            offlineToast.remove();
        }, 3000);
    }
    
    async syncOfflineData() {
        try {
            // Sincronizar dados do carrinho
            const cartData = localStorage.getItem('offline-cart');
            if (cartData) {
                await fetch('/cart_api.php?action=sync', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: cartData
                });
                localStorage.removeItem('offline-cart');
            }
            
            // Sincronizar outros dados offline
            console.log('PWA: Offline data synced successfully');
        } catch (error) {
            console.error('PWA: Offline data sync failed:', error);
        }
    }
}

// Função global para atualizar app
window.updateApp = function() {
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.getRegistration().then((registration) => {
            if (registration && registration.waiting) {
                registration.waiting.postMessage({ type: 'SKIP_WAITING' });
                window.location.reload();
            }
        });
    }
};

// Initialize PWA Installer
document.addEventListener('DOMContentLoaded', () => {
    new PWAInstaller();
});
```

## 📱 Recursos Mobile Específicos

### Touch Gestures

```javascript
class TouchGestures {
    constructor() {
        this.startX = 0;
        this.startY = 0;
        this.endX = 0;
        this.endY = 0;
        
        this.init();
    }
    
    init() {
        document.addEventListener('touchstart', (e) => {
            this.startX = e.touches[0].clientX;
            this.startY = e.touches[0].clientY;
        });
        
        document.addEventListener('touchend', (e) => {
            this.endX = e.changedTouches[0].clientX;
            this.endY = e.changedTouches[0].clientY;
            
            this.handleSwipe();
        });
    }
    
    handleSwipe() {
        const deltaX = this.endX - this.startX;
        const deltaY = this.endY - this.startY;
        const minSwipeDistance = 50;
        
        if (Math.abs(deltaX) > Math.abs(deltaY)) {
            // Swipe horizontal
            if (Math.abs(deltaX) > minSwipeDistance) {
                if (deltaX > 0) {
                    this.onSwipeRight();
                } else {
                    this.onSwipeLeft();
                }
            }
        } else {
            // Swipe vertical
            if (Math.abs(deltaY) > minSwipeDistance) {
                if (deltaY > 0) {
                    this.onSwipeDown();
                } else {
                    this.onSwipeUp();
                }
            }
        }
    }
    
    onSwipeLeft() {
        // Abrir menu lateral
        const offcanvas = document.getElementById('suhaOffcanvas');
        if (offcanvas) {
            const bsOffcanvas = new bootstrap.Offcanvas(offcanvas);
            bsOffcanvas.show();
        }
    }
    
    onSwipeRight() {
        // Fechar menu lateral
        const offcanvas = document.getElementById('suhaOffcanvas');
        if (offcanvas) {
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvas);
            if (bsOffcanvas) {
                bsOffcanvas.hide();
            }
        }
    }
    
    onSwipeUp() {
        // Scroll para cima
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    onSwipeDown() {
        // Refresh da página
        if (window.scrollY === 0) {
            window.location.reload();
        }
    }
}

// Initialize touch gestures
new TouchGestures();
```

### Pull to Refresh

```javascript
class PullToRefresh {
    constructor() {
        this.startY = 0;
        this.currentY = 0;
        this.isPulling = false;
        this.refreshThreshold = 80;
        
        this.init();
    }
    
    init() {
        document.addEventListener('touchstart', (e) => {
            this.startY = e.touches[0].clientY;
        });
        
        document.addEventListener('touchmove', (e) => {
            this.currentY = e.touches[0].clientY;
            const pullDistance = this.currentY - this.startY;
            
            if (pullDistance > 0 && window.scrollY === 0) {
                this.isPulling = true;
                this.updatePullIndicator(pullDistance);
            }
        });
        
        document.addEventListener('touchend', () => {
            if (this.isPulling) {
                const pullDistance = this.currentY - this.startY;
                
                if (pullDistance > this.refreshThreshold) {
                    this.refresh();
                } else {
                    this.resetPullIndicator();
                }
                
                this.isPulling = false;
            }
        });
    }
    
    updatePullIndicator(distance) {
        let indicator = document.getElementById('pull-refresh-indicator');
        
        if (!indicator) {
            indicator = document.createElement('div');
            indicator.id = 'pull-refresh-indicator';
            indicator.className = 'pull-refresh-indicator';
            indicator.innerHTML = '<i class="ti ti-refresh"></i>';
            document.body.insertBefore(indicator, document.body.firstChild);
        }
        
        const opacity = Math.min(distance / this.refreshThreshold, 1);
        const rotation = (distance / this.refreshThreshold) * 180;
        
        indicator.style.opacity = opacity;
        indicator.style.transform = `rotate(${rotation}deg)`;
        
        if (distance > this.refreshThreshold) {
            indicator.classList.add('ready');
        } else {
            indicator.classList.remove('ready');
        }
    }
    
    resetPullIndicator() {
        const indicator = document.getElementById('pull-refresh-indicator');
        if (indicator) {
            indicator.style.opacity = '0';
            indicator.style.transform = 'rotate(0deg)';
            indicator.classList.remove('ready');
        }
    }
    
    async refresh() {
        const indicator = document.getElementById('pull-refresh-indicator');
        if (indicator) {
            indicator.classList.add('refreshing');
        }
        
        try {
            // Simular refresh
            await new Promise(resolve => setTimeout(resolve, 1000));
            window.location.reload();
        } catch (error) {
            console.error('Refresh failed:', error);
        }
    }
}

// Initialize pull to refresh
new PullToRefresh();
```

## 📊 Métricas PWA

### Performance Monitoring

```javascript
class PWAMetrics {
    constructor() {
        this.metrics = {
            loadTime: 0,
            firstContentfulPaint: 0,
            largestContentfulPaint: 0,
            firstInputDelay: 0,
            cumulativeLayoutShift: 0
        };
        
        this.init();
    }
    
    init() {
        this.measureLoadTime();
        this.measureWebVitals();
        this.trackUserEngagement();
    }
    
    measureLoadTime() {
        window.addEventListener('load', () => {
            this.metrics.loadTime = performance.now();
            this.sendMetrics();
        });
    }
    
    measureWebVitals() {
        // First Contentful Paint
        new PerformanceObserver((list) => {
            for (const entry of list.getEntries()) {
                if (entry.name === 'first-contentful-paint') {
                    this.metrics.firstContentfulPaint = entry.startTime;
                }
            }
        }).observe({ entryTypes: ['paint'] });
        
        // Largest Contentful Paint
        new PerformanceObserver((list) => {
            const entries = list.getEntries();
            const lastEntry = entries[entries.length - 1];
            this.metrics.largestContentfulPaint = lastEntry.startTime;
        }).observe({ entryTypes: ['largest-contentful-paint'] });
        
        // First Input Delay
        new PerformanceObserver((list) => {
            for (const entry of list.getEntries()) {
                this.metrics.firstInputDelay = entry.processingStart - entry.startTime;
            }
        }).observe({ entryTypes: ['first-input'] });
        
        // Cumulative Layout Shift
        new PerformanceObserver((list) => {
            for (const entry of list.getEntries()) {
                if (!entry.hadRecentInput) {
                    this.metrics.cumulativeLayoutShift += entry.value;
                }
            }
        }).observe({ entryTypes: ['layout-shift'] });
    }
    
    trackUserEngagement() {
        let engagementStart = Date.now();
        let totalEngagement = 0;
        
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                totalEngagement += Date.now() - engagementStart;
            } else {
                engagementStart = Date.now();
            }
        });
        
        window.addEventListener('beforeunload', () => {
            totalEngagement += Date.now() - engagementStart;
            this.metrics.totalEngagement = totalEngagement;
            this.sendMetrics();
        });
    }
    
    sendMetrics() {
        // Enviar métricas para analytics
        if (navigator.sendBeacon) {
            navigator.sendBeacon('/api/metrics', JSON.stringify({
                type: 'pwa-metrics',
                data: this.metrics,
                timestamp: Date.now(),
                userAgent: navigator.userAgent,
                connection: navigator.connection?.effectiveType || 'unknown'
            }));
        }
    }
}

// Initialize PWA metrics
new PWAMetrics();
```

---

Esta documentação PWA fornece uma base completa para implementação e otimização de Progressive Web App no Tempero e Café.
