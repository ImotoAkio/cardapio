# 🎨 Frontend e Interface - Tempero e Café

## 📋 Visão Geral

O frontend do Tempero e Café foi desenvolvido com foco em experiência mobile-first, utilizando tecnologias modernas como Bootstrap 5, JavaScript ES6+ e Progressive Web App (PWA). A interface é responsiva, acessível e otimizada para performance.

## 🎯 Características da Interface

- **Mobile-First**: Design otimizado para dispositivos móveis
- **Responsive**: Adaptação automática para diferentes telas
- **PWA**: Funciona como aplicativo nativo
- **Acessível**: Seguindo padrões WCAG 2.1
- **Performance**: Carregamento rápido e experiência fluida
- **Moderno**: Design limpo e intuitivo

## 🏗️ Estrutura do Frontend

```
📁 Frontend/
├── 📁 dist/                    # Assets compilados
│   ├── 📁 css/                 # Estilos CSS
│   ├── 📁 js/                  # Scripts JavaScript
│   ├── 📁 img/                 # Imagens otimizadas
│   └── 📄 manifest.json        # PWA Manifest
├── 📁 src/                     # Código fonte
│   ├── 📁 pug/                 # Templates Pug
│   └── 📁 scss/                # Estilos SCSS
├── 📁 static/                  # Assets estáticos
└── 📄 *.php                    # Páginas PHP
```

## 🎨 Sistema de Design

### Paleta de Cores

```css
:root {
    /* Cores Primárias */
    --primary-color: #d3a74e;        /* Dourado principal */
    --primary-dark: #b8943f;         /* Dourado escuro */
    --primary-light: #e6c266;        /* Dourado claro */
    
    /* Cores Secundárias */
    --secondary-color: #6c757d;      /* Cinza */
    --success-color: #28a745;        /* Verde */
    --danger-color: #dc3545;         /* Vermelho */
    --warning-color: #ffc107;        /* Amarelo */
    --info-color: #17a2b8;           /* Azul */
    
    /* Cores Neutras */
    --light-color: #f8f9fa;          /* Cinza claro */
    --dark-color: #343a40;           /* Cinza escuro */
    --white: #ffffff;                /* Branco */
    --black: #000000;                /* Preto */
    
    /* Cores de Fundo */
    --bg-primary: #ffffff;           /* Fundo principal */
    --bg-secondary: #f8f9fa;         /* Fundo secundário */
    --bg-dark: #343a40;              /* Fundo escuro */
}
```

### Tipografia

```css
/* Fontes */
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;300;400;500;600;700;800&display=swap');

:root {
    --font-family: 'Plus Jakarta Sans', sans-serif;
    --font-size-base: 16px;
    --font-size-sm: 14px;
    --font-size-lg: 18px;
    --font-size-xl: 20px;
    --font-size-xxl: 24px;
    
    /* Pesos da fonte */
    --font-weight-light: 300;
    --font-weight-normal: 400;
    --font-weight-medium: 500;
    --font-weight-semibold: 600;
    --font-weight-bold: 700;
    --font-weight-extrabold: 800;
}
```

### Espaçamentos

```css
:root {
    /* Espaçamentos */
    --spacing-xs: 0.25rem;    /* 4px */
    --spacing-sm: 0.5rem;     /* 8px */
    --spacing-md: 1rem;       /* 16px */
    --spacing-lg: 1.5rem;      /* 24px */
    --spacing-xl: 2rem;        /* 32px */
    --spacing-xxl: 3rem;       /* 48px */
    
    /* Border Radius */
    --border-radius-sm: 0.25rem;   /* 4px */
    --border-radius-md: 0.5rem;    /* 8px */
    --border-radius-lg: 1rem;      /* 16px */
    --border-radius-xl: 1.5rem;    /* 24px */
}
```

## 📱 Componentes Principais

### 1. **Header/Navbar**

```html
<!-- Header Area -->
<div class="header-area" id="headerArea">
    <div class="container h-100 d-flex align-items-center justify-content-between">
        <!-- Logo -->
        <div class="logo-wrapper">
            <a href="home.php">
                <img src="dist/img/core-img/logo_cafe.png" alt="Tempero e Café">
            </a>
        </div>
        
        <!-- Actions -->
        <div class="navbar-logo-container d-flex align-items-center">
            <!-- Cart Icon -->
            <div class="cart-icon-wrap">
                <a href="cart.php">
                    <i class="ti ti-basket-bolt"></i>
                    <span class="cart-count">0</span>
                </a>
            </div>
            
            <!-- User Profile -->
            <div class="user-profile-icon ms-2">
                <a href="profile.php">
                    <img src="dist/img/bg-img/user/avatar.jpg" alt="Avatar">
                </a>
            </div>
            
            <!-- Menu Toggle -->
            <div class="suha-navbar-toggler ms-2" data-bs-toggle="offcanvas" data-bs-target="#suhaOffcanvas">
                <div><span></span><span></span><span></span></div>
            </div>
        </div>
    </div>
</div>
```

### 2. **Menu Offcanvas**

```html
<!-- Offcanvas Menu -->
<div class="offcanvas offcanvas-start suha-offcanvas-wrap" tabindex="-1" id="suhaOffcanvas">
    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="offcanvas"></button>
    
    <div class="offcanvas-body">
        <!-- Profile Section -->
        <div class="sidenav-profile">
            <div class="user-profile">
                <img src="dist/img/bg-img/user/avatar.jpg" alt="Avatar">
            </div>
            <div class="user-info">
                <h5 class="user-name mb-1 text-white">Nome do Usuário</h5>
                <p class="available-balance text-white">Saldo Atual R$<span class="counter">0,00</span></p>
            </div>
        </div>
        
        <!-- Navigation -->
        <ul class="sidenav-nav ps-0">
            <li><a href="profile.php"><i class="ti ti-user"></i>Meu Perfil</a></li>
            <li><a href="notifications.php"><i class="ti ti-bell-ringing"></i>Notificações</a></li>
            <li><a href="home.php"><i class="ti ti-home"></i>Início</a></li>
            <li><a href="shop.php"><i class="ti ti-building-store"></i>Loja</a></li>
            <li><a href="cart.php"><i class="ti ti-basket"></i>Carrinho</a></li>
            <li><a href="settings.php"><i class="ti ti-adjustments-horizontal"></i>Configurações</a></li>
            <li><a href="logout.php"><i class="ti ti-logout"></i>Sair</a></li>
        </ul>
    </div>
</div>
```

### 3. **Barra de Busca**

```html
<!-- Search Form -->
<div class="search-form pt-3">
    <form action="search.php" method="GET" id="searchForm">
        <input class="form-control" type="search" name="q" id="searchInput" 
               placeholder="Buscar no Tempero e Café" autocomplete="off">
        <button type="submit"><i class="ti ti-search"></i></button>
        
        <!-- Search Suggestions -->
        <div class="search-suggestions" id="searchSuggestions" style="display: none;">
            <div class="suggestions-list" id="suggestionsList"></div>
        </div>
    </form>
    
    <!-- Alternative Search Options -->
    <div class="alternative-search-options">
        <div class="dropdown">
            <a class="btn btn-primary dropdown-toggle" id="altSearchOption" href="#" 
               role="button" data-bs-toggle="dropdown">
                <i class="ti ti-adjustments-horizontal"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#" onclick="startVoiceSearch()">
                    <i class="ti ti-microphone"></i>Voz
                </a></li>
                <li><a class="dropdown-item" href="#" onclick="startImageSearch()">
                    <i class="ti ti-layout-collage"></i>Imagem
                </a></li>
            </ul>
        </div>
    </div>
</div>
```

### 4. **Card de Produto**

```html
<!-- Product Card -->
<div class="col-6 col-md-4 col-lg-3">
    <div class="card product-card h-100">
        <div class="card-body">
            <!-- Badge -->
            <span class="badge rounded-pill badge-warning">Promoção</span>
            
            <!-- Wishlist Button -->
            <button class="wishlist-btn" data-product-id="123">
                <i class="ti ti-heart"></i>
            </button>
            
            <!-- Thumbnail -->
            <a class="product-thumbnail d-block" href="product.php?id=123">
                <img class="mb-2" src="dist/img/product/produto.jpg" alt="Nome do Produto">
            </a>
            
            <!-- Product Title -->
            <a class="product-title" href="product.php?id=123">Nome do Produto</a>
            
            <!-- Product Price -->
            <div class="product-price">
                <div class="price-container">
                    <div class="current-price-wrapper">
                        <span class="current-price text-success fw-bold">R$ 12,90</span>
                        <span class="discount-badge bg-danger text-white px-1 py-0 rounded ms-1">
                            30% OFF
                        </span>
                    </div>
                    <div class="original-price-wrapper">
                        <span class="original-price text-muted text-decoration-line-through">
                            R$ 18,90
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Rating -->
            <div class="product-rating">
                <i class="ti ti-star-filled"></i>
                <i class="ti ti-star-filled"></i>
                <i class="ti ti-star-filled"></i>
                <i class="ti ti-star-filled"></i>
                <i class="ti ti-star"></i>
                <span class="ms-1">(24)</span>
            </div>
            
            <!-- Add to Cart -->
            <button class="btn btn-primary btn-sm w-100 mt-2" data-cart-add="123">
                <i class="ti ti-plus"></i> Adicionar
            </button>
        </div>
    </div>
</div>
```

### 5. **Card de Categoria**

```html
<!-- Category Card -->
<div class="col-3">
    <div class="card catagory-card">
        <div class="card-body px-2">
            <a href="category.php?id=1">
                <img src="dist/img/core-img/woman-clothes.png" alt="Temperos Naturais">
                <span>Temperos Naturais</span>
            </a>
        </div>
    </div>
</div>
```

## 🎭 Animações e Transições

### CSS Animations

```css
/* Fade In Animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

/* Slide In Animation */
@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.slide-in-left {
    animation: slideInLeft 0.5s ease-out;
}

/* Pulse Animation */
@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
    100% {
        transform: scale(1);
    }
}

.pulse {
    animation: pulse 2s infinite;
}

/* Loading Spinner */
@keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

.spinner {
    animation: spin 1s linear infinite;
}
```

### JavaScript Animations

```javascript
// Intersection Observer para animações
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('fade-in-up');
        }
    });
}, observerOptions);

// Observar elementos
document.querySelectorAll('.product-card, .catagory-card').forEach(el => {
    observer.observe(el);
});
```

## 📱 Responsividade

### Breakpoints

```css
/* Mobile First Approach */
:root {
    --breakpoint-xs: 0;
    --breakpoint-sm: 576px;
    --breakpoint-md: 768px;
    --breakpoint-lg: 992px;
    --breakpoint-xl: 1200px;
    --breakpoint-xxl: 1400px;
}

/* Mobile (0px - 575px) */
@media (max-width: 575.98px) {
    .container {
        padding: 0 15px;
    }
    
    .product-card {
        margin-bottom: 1rem;
    }
    
    .search-form {
        padding: 0.5rem 0;
    }
}

/* Tablet (576px - 767px) */
@media (min-width: 576px) and (max-width: 767.98px) {
    .product-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
}

/* Desktop (768px+) */
@media (min-width: 768px) {
    .product-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
    }
    
    .hero-slides {
        height: 400px;
    }
}

/* Large Desktop (1200px+) */
@media (min-width: 1200px) {
    .product-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 2rem;
    }
}
```

### Grid System

```css
/* Custom Grid System */
.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    padding: 1rem 0;
}

.category-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.5rem;
    padding: 1rem 0;
}

@media (max-width: 767px) {
    .category-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
```

## 🎨 Temas e Modo Escuro

### CSS Variables para Temas

```css
/* Light Theme (Default) */
:root {
    --bg-primary: #ffffff;
    --bg-secondary: #f8f9fa;
    --text-primary: #212529;
    --text-secondary: #6c757d;
    --border-color: #dee2e6;
    --shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

/* Dark Theme */
[data-theme="dark"] {
    --bg-primary: #1a1a1a;
    --bg-secondary: #2d2d2d;
    --text-primary: #ffffff;
    --text-secondary: #adb5bd;
    --border-color: #495057;
    --shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.3);
}

/* Apply theme variables */
body {
    background-color: var(--bg-primary);
    color: var(--text-primary);
    transition: background-color 0.3s ease, color 0.3s ease;
}

.card {
    background-color: var(--bg-primary);
    border-color: var(--border-color);
    box-shadow: var(--shadow);
}
```

### JavaScript para Toggle de Tema

```javascript
// Theme Toggle
class ThemeManager {
    constructor() {
        this.theme = localStorage.getItem('theme') || 'light';
        this.init();
    }
    
    init() {
        this.applyTheme();
        this.bindEvents();
    }
    
    applyTheme() {
        document.documentElement.setAttribute('data-theme', this.theme);
        localStorage.setItem('theme', this.theme);
    }
    
    toggleTheme() {
        this.theme = this.theme === 'light' ? 'dark' : 'light';
        this.applyTheme();
    }
    
    bindEvents() {
        const toggleBtn = document.getElementById('themeToggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => this.toggleTheme());
        }
    }
}

// Initialize theme manager
new ThemeManager();
```

## 🔍 Funcionalidades de Busca

### Busca com Sugestões

```javascript
class SearchManager {
    constructor() {
        this.searchInput = document.getElementById('searchInput');
        this.suggestionsContainer = document.getElementById('searchSuggestions');
        this.suggestionsList = document.getElementById('suggestionsList');
        this.timeout = null;
        
        this.init();
    }
    
    init() {
        this.searchInput.addEventListener('input', (e) => {
            this.handleInput(e.target.value);
        });
        
        // Hide suggestions when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-form')) {
                this.hideSuggestions();
            }
        });
    }
    
    handleInput(query) {
        clearTimeout(this.timeout);
        
        if (query.length < 2) {
            this.hideSuggestions();
            return;
        }
        
        this.timeout = setTimeout(() => {
            this.fetchSuggestions(query);
        }, 300);
    }
    
    async fetchSuggestions(query) {
        try {
            const response = await fetch(`api/search_suggestions.php?q=${encodeURIComponent(query)}`);
            const data = await response.json();
            
            if (data.success && data.suggestions.length > 0) {
                this.showSuggestions(data.suggestions);
            } else {
                this.hideSuggestions();
            }
        } catch (error) {
            console.error('Erro ao buscar sugestões:', error);
            this.hideSuggestions();
        }
    }
    
    showSuggestions(suggestions) {
        this.suggestionsList.innerHTML = '';
        
        suggestions.forEach(suggestion => {
            const item = document.createElement('div');
            item.className = 'suggestion-item';
            item.innerHTML = `
                <i class="ti ti-search me-2"></i>
                <span>${suggestion.text}</span>
            `;
            
            item.addEventListener('click', () => {
                this.searchInput.value = suggestion.text;
                this.hideSuggestions();
                document.getElementById('searchForm').submit();
            });
            
            this.suggestionsList.appendChild(item);
        });
        
        this.suggestionsContainer.style.display = 'block';
    }
    
    hideSuggestions() {
        this.suggestionsContainer.style.display = 'none';
    }
}

// Initialize search manager
new SearchManager();
```

### Busca por Voz

```javascript
// Voice Search
function startVoiceSearch() {
    if ('webkitSpeechRecognition' in window) {
        const recognition = new webkitSpeechRecognition();
        recognition.lang = 'pt-BR';
        recognition.continuous = false;
        recognition.interimResults = false;
        
        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            document.getElementById('searchInput').value = transcript;
            document.getElementById('searchForm').submit();
        };
        
        recognition.onerror = function(event) {
            console.error('Erro no reconhecimento de voz:', event.error);
        };
        
        recognition.start();
    } else {
        alert('Busca por voz não suportada neste navegador.');
    }
}
```

## 🛒 Funcionalidades do Carrinho

### Gerenciamento do Carrinho

```javascript
class CartManager {
    constructor() {
        this.cartCount = document.querySelector('.cart-count');
        this.init();
    }
    
    init() {
        this.updateCartCount();
        this.bindEvents();
    }
    
    bindEvents() {
        // Add to cart buttons
        document.addEventListener('click', (e) => {
            if (e.target.closest('[data-cart-add]')) {
                const productId = e.target.closest('[data-cart-add]').dataset.cartAdd;
                this.addToCart(productId);
            }
        });
        
        // Remove from cart buttons
        document.addEventListener('click', (e) => {
            if (e.target.closest('[data-cart-remove]')) {
                const productId = e.target.closest('[data-cart-remove]').dataset.cartRemove;
                this.removeFromCart(productId);
            }
        });
    }
    
    async addToCart(productId, quantity = 1) {
        try {
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', quantity);
            
            const response = await fetch('cart_api.php?action=add', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification('Produto adicionado ao carrinho!', 'success');
                this.updateCartCount(data.item_count);
                this.animateCartIcon();
            } else {
                this.showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Erro ao adicionar ao carrinho:', error);
            this.showNotification('Erro ao adicionar produto ao carrinho', 'error');
        }
    }
    
    async removeFromCart(productId) {
        try {
            const formData = new FormData();
            formData.append('product_id', productId);
            
            const response = await fetch('cart_api.php?action=remove', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification('Produto removido do carrinho!', 'success');
                this.updateCartCount(data.item_count);
                this.refreshCartItems();
            } else {
                this.showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Erro ao remover do carrinho:', error);
            this.showNotification('Erro ao remover produto do carrinho', 'error');
        }
    }
    
    async updateCartCount() {
        try {
            const response = await fetch('cart_api.php?action=count');
            const data = await response.json();
            
            if (data.success && this.cartCount) {
                this.cartCount.textContent = data.item_count;
            }
        } catch (error) {
            console.error('Erro ao atualizar contador do carrinho:', error);
        }
    }
    
    animateCartIcon() {
        const cartIcon = document.querySelector('.cart-icon-wrap');
        if (cartIcon) {
            cartIcon.classList.add('pulse');
            setTimeout(() => {
                cartIcon.classList.remove('pulse');
            }, 1000);
        }
    }
    
    showNotification(message, type = 'info') {
        // Implementar sistema de notificações
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
}

// Initialize cart manager
new CartManager();
```

## 📱 PWA Features

### Service Worker

```javascript
// service-worker.js
const CACHE_NAME = 'tempero-e-cafe-v1';
const urlsToCache = [
    '/',
    '/home.php',
    '/dist/style.css',
    '/dist/js/bootstrap.bundle.min.js',
    '/dist/js/jquery.min.js',
    '/dist/js/active.js',
    '/dist/img/icons/icon-72x72.png',
    '/dist/img/icons/icon-96x96.png',
    '/dist/img/icons/icon-152x152.png',
    '/dist/img/icons/icon-167x167.png',
    '/dist/img/icons/icon-180x180.png'
];

// Install event
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(urlsToCache))
    );
});

// Fetch event
self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request)
            .then((response) => {
                // Return cached version or fetch from network
                return response || fetch(event.request);
            })
    );
});

// Activate event
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});
```

### Web App Manifest

```json
{
    "name": "Tempero e Café",
    "short_name": "Tempero",
    "description": "Produtos naturais e orgânicos",
    "start_url": "/home.php",
    "display": "standalone",
    "background_color": "#ffffff",
    "theme_color": "#d3a74e",
    "orientation": "portrait",
    "icons": [
        {
            "src": "dist/img/icons/icon-72x72.png",
            "sizes": "72x72",
            "type": "image/png"
        },
        {
            "src": "dist/img/icons/icon-96x96.png",
            "sizes": "96x96",
            "type": "image/png"
        },
        {
            "src": "dist/img/icons/icon-152x152.png",
            "sizes": "152x152",
            "type": "image/png"
        },
        {
            "src": "dist/img/icons/icon-167x167.png",
            "sizes": "167x167",
            "type": "image/png"
        },
        {
            "src": "dist/img/icons/icon-180x180.png",
            "sizes": "180x180",
            "type": "image/png"
        }
    ],
    "categories": ["shopping", "food", "lifestyle"],
    "lang": "pt-BR"
}
```

### PWA Installation

```javascript
// pwa.js
class PWAInstaller {
    constructor() {
        this.deferredPrompt = null;
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.registerServiceWorker();
    }
    
    bindEvents() {
        // Listen for beforeinstallprompt event
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.deferredPrompt = e;
            this.showInstallButton();
        });
        
        // Listen for appinstalled event
        window.addEventListener('appinstalled', () => {
            this.hideInstallButton();
            console.log('PWA foi instalado');
        });
        
        // Install button click
        const installBtn = document.getElementById('installSuha');
        if (installBtn) {
            installBtn.addEventListener('click', () => this.installApp());
        }
    }
    
    async registerServiceWorker() {
        if ('serviceWorker' in navigator) {
            try {
                const registration = await navigator.serviceWorker.register('/service-worker.js');
                console.log('Service Worker registrado:', registration);
            } catch (error) {
                console.error('Erro ao registrar Service Worker:', error);
            }
        }
    }
    
    showInstallButton() {
        const installWrap = document.getElementById('installWrap');
        if (installWrap) {
            installWrap.style.display = 'block';
        }
    }
    
    hideInstallButton() {
        const installWrap = document.getElementById('installWrap');
        if (installWrap) {
            installWrap.style.display = 'none';
        }
    }
    
    async installApp() {
        if (this.deferredPrompt) {
            this.deferredPrompt.prompt();
            const { outcome } = await this.deferredPrompt.userChoice;
            
            if (outcome === 'accepted') {
                console.log('Usuário aceitou a instalação');
            } else {
                console.log('Usuário rejeitou a instalação');
            }
            
            this.deferredPrompt = null;
            this.hideInstallButton();
        }
    }
}

// Initialize PWA installer
new PWAInstaller();
```

## 🎯 Acessibilidade

### ARIA Labels e Roles

```html
<!-- Navigation with ARIA -->
<nav role="navigation" aria-label="Menu principal">
    <ul class="sidenav-nav ps-0">
        <li role="menuitem">
            <a href="profile.php" aria-label="Acessar perfil do usuário">
                <i class="ti ti-user" aria-hidden="true"></i>Meu Perfil
            </a>
        </li>
    </ul>
</nav>

<!-- Product card with ARIA -->
<div class="card product-card" role="article" aria-labelledby="product-title-123">
    <div class="card-body">
        <h3 id="product-title-123" class="product-title">
            <a href="product.php?id=123">Nome do Produto</a>
        </h3>
        
        <div class="product-price" aria-label="Preço do produto">
            <span class="current-price" aria-label="Preço atual">R$ 12,90</span>
        </div>
        
        <button class="btn btn-primary" 
                aria-label="Adicionar produto ao carrinho"
                data-cart-add="123">
            <i class="ti ti-plus" aria-hidden="true"></i> Adicionar
        </button>
    </div>
</div>
```

### Keyboard Navigation

```css
/* Focus styles */
.btn:focus,
.form-control:focus,
a:focus {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}

/* Skip link */
.skip-link {
    position: absolute;
    top: -40px;
    left: 6px;
    background: var(--primary-color);
    color: white;
    padding: 8px;
    text-decoration: none;
    z-index: 1000;
}

.skip-link:focus {
    top: 6px;
}
```

### Screen Reader Support

```javascript
// Announce changes to screen readers
function announceToScreenReader(message) {
    const announcement = document.createElement('div');
    announcement.setAttribute('aria-live', 'polite');
    announcement.setAttribute('aria-atomic', 'true');
    announcement.className = 'sr-only';
    announcement.textContent = message;
    
    document.body.appendChild(announcement);
    
    setTimeout(() => {
        document.body.removeChild(announcement);
    }, 1000);
}

// Usage example
function addToCart(productName) {
    // ... add to cart logic ...
    announceToScreenReader(`${productName} adicionado ao carrinho`);
}
```

## 📊 Performance

### Lazy Loading

```javascript
// Lazy loading for images
const lazyImages = document.querySelectorAll('img[data-src]');

const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const img = entry.target;
            img.src = img.dataset.src;
            img.classList.remove('lazy');
            observer.unobserve(img);
        }
    });
});

lazyImages.forEach(img => imageObserver.observe(img));
```

### Preloading Critical Resources

```html
<!-- Preload critical resources -->
<link rel="preload" href="dist/css/bootstrap.min.css" as="style">
<link rel="preload" href="dist/js/bootstrap.bundle.min.js" as="script">
<link rel="preload" href="dist/img/core-img/logo_cafe.png" as="image">

<!-- Preconnect to external domains -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
```

---

Esta documentação do frontend fornece uma base sólida para desenvolvimento e manutenção da interface do Tempero e Café.
