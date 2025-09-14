# 🏗️ Arquitetura do Sistema - Tempero e Café

## 📋 Visão Geral

O sistema Tempero e Café foi desenvolvido seguindo uma arquitetura MVC (Model-View-Controller) simplificada, com separação clara entre frontend, backend e banco de dados. A aplicação é uma Progressive Web App (PWA) que oferece experiência mobile-first.

## 🎯 Princípios Arquiteturais

- **Separação de Responsabilidades**: Cada componente tem uma função específica
- **Mobile-First**: Interface otimizada para dispositivos móveis
- **Progressive Enhancement**: Funciona sem JavaScript, melhora com ele
- **Performance**: Carregamento rápido e experiência fluida
- **Escalabilidade**: Estrutura preparada para crescimento

## 🏛️ Estrutura de Camadas

```
┌─────────────────────────────────────────┐
│              Frontend (View)            │
│  • HTML5 + Bootstrap 5                 │
│  • JavaScript ES6+                     │
│  • PWA (Service Worker)                │
│  • Responsive Design                   │
└─────────────────────────────────────────┘
                    │
┌─────────────────────────────────────────┐
│            Controller (PHP)             │
│  • Páginas PHP (.php)                  │
│  • APIs REST                           │
│  • Lógica de Negócio                   │
│  • Validação de Dados                  │
└─────────────────────────────────────────┘
                    │
┌─────────────────────────────────────────┐
│              Model (Database)           │
│  • MySQL 8.0+                          │
│  • PDO (PHP Data Objects)              │
│  • Classes de Serviço                  │
│  • Relacionamentos Normalizados        │
└─────────────────────────────────────────┘
```

## 📁 Estrutura de Diretórios

```
📁 tempero-e-cafe/
├── 📁 admin/                    # Painel Administrativo
│   ├── 📄 config.php           # Configurações do admin
│   ├── 📄 dashboard.php        # Dashboard principal
│   ├── 📄 products.php         # Gestão de produtos
│   ├── 📄 categories.php       # Gestão de categorias
│   ├── 📄 orders.php           # Gestão de pedidos
│   ├── 📄 users.php            # Gestão de usuários
│   └── 📄 settings.php         # Configurações gerais
│
├── 📁 api/                      # APIs REST
│   ├── 📄 search_suggestions.php
│   ├── 📄 get_order_details.php
│   ├── 📄 cancel_order.php
│   └── 📄 save_settings.php
│
├── 📁 dist/                     # Assets Compilados
│   ├── 📁 css/                  # Estilos CSS
│   ├── 📁 js/                   # Scripts JavaScript
│   ├── 📁 img/                  # Imagens otimizadas
│   └── 📄 manifest.json         # PWA Manifest
│
├── 📁 includes/                 # Arquivos de Configuração
│   ├── 📄 database.php          # Conexão e classes do banco
│   ├── 📄 header.php            # Cabeçalho comum
│   └── 📄 n8n_helper.php        # Integração com N8N
│
├── 📁 src/                      # Código Fonte
│   ├── 📁 pug/                  # Templates Pug
│   └── 📁 scss/                 # Estilos SCSS
│
├── 📁 static/                   # Assets Estáticos
│   ├── 📁 img/                  # Imagens originais
│   ├── 📁 js/                   # Scripts originais
│   └── 📄 manifest.json         # Manifest original
│
├── 📄 home.php                  # Página inicial
├── 📄 product.php               # Página do produto
├── 📄 category.php              # Página da categoria
├── 📄 cart.php                  # Carrinho de compras
├── 📄 checkout.php              # Finalização de compra
├── 📄 login.php                 # Login de usuário
├── 📄 profile.php               # Perfil do usuário
└── 📄 cart_api.php              # API do carrinho
```

## 🔄 Fluxo de Dados

### 1. Requisição do Usuário
```
Usuário → Frontend (HTML/JS) → Controller (PHP) → Model (Database)
```

### 2. Processamento
```
Controller valida dados → Model executa query → Database retorna dados
```

### 3. Resposta
```
Database → Model → Controller → Frontend → Usuário
```

## 🧩 Componentes Principais

### Frontend Components

#### 1. **Páginas Principais**
- `home.php` - Página inicial com produtos em destaque
- `product.php` - Detalhes do produto
- `category.php` - Lista de produtos por categoria
- `shop.php` - Catálogo completo
- `cart.php` - Carrinho de compras

#### 2. **Sistema de Autenticação**
- `login.php` - Login de usuário
- `cadastro.php` - Registro de usuário
- `profile.php` - Perfil do usuário
- `logout.php` - Logout

#### 3. **E-commerce**
- `checkout.php` - Finalização de compra
- `my-orders.php` - Histórico de pedidos
- `wishlist.php` - Lista de favoritos

### Backend Components

#### 1. **Classes de Serviço** (`includes/database.php`)
```php
class Database          // Singleton para conexão
class CategoryService   // Gestão de categorias
class ProductService   // Gestão de produtos
class CouponService    // Gestão de cupons
class CartManager      // Gestão do carrinho
```

#### 2. **APIs REST** (`api/`)
- `search_suggestions.php` - Sugestões de busca
- `get_order_details.php` - Detalhes do pedido
- `cancel_order.php` - Cancelamento de pedido
- `save_settings.php` - Salvar configurações

#### 3. **Painel Administrativo** (`admin/`)
- `dashboard.php` - Dashboard com estatísticas
- `products.php` - CRUD de produtos
- `categories.php` - CRUD de categorias
- `orders.php` - Gestão de pedidos
- `users.php` - Gestão de usuários

## 🗄️ Estrutura do Banco de Dados

### Tabelas Principais
- `users` - Usuários do sistema
- `categories` - Categorias de produtos
- `subcategories` - Subcategorias
- `products` - Produtos
- `cart` / `cart_items` - Carrinho de compras
- `orders` / `order_items` - Pedidos
- `coupons` - Cupons de desconto
- `notifications` - Notificações

### Relacionamentos
- **1:N** - Usuário → Pedidos
- **1:N** - Categoria → Produtos
- **1:N** - Pedido → Itens do Pedido
- **N:N** - Usuário ↔ Produtos (Favoritos)

## 🔧 Padrões de Desenvolvimento

### 1. **Singleton Pattern**
```php
class Database {
    private static $instance = null;
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
```

### 2. **Service Layer Pattern**
```php
class ProductService {
    private $db;
    
    public function getFeaturedProducts($limit = 6) {
        // Lógica de negócio para produtos em destaque
    }
}
```

### 3. **Repository Pattern** (Simplificado)
```php
class CartManager {
    public function addToCart($sessionId, $productId, $quantity) {
        // Operações de carrinho
    }
}
```

## 🚀 Performance e Otimização

### 1. **Frontend**
- **Lazy Loading** - Carregamento sob demanda
- **Image Optimization** - Imagens otimizadas
- **CSS/JS Minification** - Arquivos comprimidos
- **CDN Ready** - Preparado para CDN

### 2. **Backend**
- **Prepared Statements** - Prevenção de SQL Injection
- **Connection Pooling** - Reutilização de conexões
- **Caching** - Cache de consultas frequentes
- **Indexing** - Índices otimizados no banco

### 3. **PWA**
- **Service Worker** - Cache offline
- **App Shell** - Interface base em cache
- **Background Sync** - Sincronização em background

## 🔒 Segurança

### 1. **Autenticação**
- Senhas hasheadas com `password_hash()`
- Sessões seguras com timeout
- Validação de entrada

### 2. **Autorização**
- Controle de acesso por roles
- Middleware de autenticação
- Proteção de rotas administrativas

### 3. **Dados**
- Prepared Statements (PDO)
- Sanitização de entrada
- Validação de tipos

## 📱 PWA Architecture

### Service Worker
```javascript
// Cache Strategy
- Cache First: Assets estáticos
- Network First: APIs dinâmicas
- Stale While Revalidate: Imagens
```

### Web App Manifest
```json
{
  "name": "Tempero e Café",
  "short_name": "Tempero",
  "theme_color": "#d3a74e",
  "background_color": "#ffffff",
  "display": "standalone"
}
```

## 🔄 Integração com N8N

O sistema integra com N8N para automação de processos:

- **Webhooks** - Recebimento de dados externos
- **APIs** - Comunicação bidirecional
- **Automação** - Processamento de pedidos
- **Notificações** - Alertas automáticos

## 📈 Escalabilidade

### Horizontal Scaling
- **Load Balancer** - Distribuição de carga
- **Database Replication** - Réplicas de leitura
- **CDN** - Distribuição de assets

### Vertical Scaling
- **Caching** - Redis/Memcached
- **Database Optimization** - Índices e queries
- **Code Optimization** - Profiling e otimização

## 🛠️ Ferramentas de Desenvolvimento

### Build Tools
- **Gulp** - Automação de tarefas
- **Pug** - Template engine
- **SCSS** - Pré-processador CSS
- **Babel** - Transpilação JavaScript

### Development Environment
- **PHP 7.4+** - Runtime
- **MySQL 8.0+** - Database
- **Node.js 16+** - Build tools
- **Apache/Nginx** - Web server

---

Esta arquitetura garante um sistema robusto, escalável e de fácil manutenção, preparado para crescer com as necessidades do negócio.
