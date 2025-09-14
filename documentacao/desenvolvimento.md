# 🛠️ Guia de Desenvolvimento - Tempero e Café

## 📋 Visão Geral

Este guia fornece informações essenciais para desenvolvedores que trabalham no projeto Tempero e Café. Inclui padrões de código, ferramentas de desenvolvimento, testes e boas práticas.

## 🎯 Padrões de Desenvolvimento

### Convenções de Código

#### PHP
```php
<?php
// =====================================================
// 🍃 TEMPERO E CAFÉ - NOME DO ARQUIVO
// =====================================================
// Descrição breve do arquivo
// =====================================================

// Configurações
define('CONSTANT_NAME', 'value');

// Classes seguem PascalCase
class ClassName {
    private $propertyName;
    
    // Métodos seguem camelCase
    public function methodName($parameterName) {
        // Código aqui
    }
}

// Funções seguem snake_case
function function_name($parameter_name) {
    // Código aqui
}

// Variáveis seguem camelCase
$variableName = 'value';
$arrayName = [];
```

#### JavaScript
```javascript
// Classes seguem PascalCase
class ClassName {
    constructor() {
        this.propertyName = 'value';
    }
    
    // Métodos seguem camelCase
    methodName(parameterName) {
        // Código aqui
    }
}

// Funções seguem camelCase
function functionName(parameterName) {
    // Código aqui
}

// Constantes seguem UPPER_SNAKE_CASE
const CONSTANT_NAME = 'value';

// Variáveis seguem camelCase
let variableName = 'value';
const arrayName = [];
```

#### CSS/SCSS
```scss
// Classes seguem kebab-case
.class-name {
    // Propriedades em ordem alfabética
    background-color: #fff;
    border: 1px solid #ccc;
    color: #333;
    display: block;
    font-size: 16px;
    margin: 0;
    padding: 10px;
    
    // Modificadores seguem BEM
    &--modifier {
        color: #f00;
    }
    
    // Elementos seguem BEM
    &__element {
        font-weight: bold;
    }
}

// Variáveis CSS seguem kebab-case
:root {
    --primary-color: #d3a74e;
    --secondary-color: #6c757d;
    --font-size-base: 16px;
}
```

## 🏗️ Estrutura de Arquivos

### Organização de Diretórios

```
📁 tempero-e-cafe/
├── 📁 admin/                 # Painel administrativo
├── 📁 api/                   # APIs REST
├── 📁 dist/                  # Assets compilados
├── 📁 includes/              # Arquivos de configuração
├── 📁 src/                   # Código fonte
│   ├── 📁 pug/              # Templates
│   └── 📁 scss/             # Estilos
├── 📁 static/                # Assets estáticos
├── 📁 tests/                 # Testes
├── 📁 docs/                  # Documentação
├── 📄 *.php                  # Páginas principais
├── 📄 package.json           # Dependências Node
├── 📄 gulpfile.js            # Build tasks
└── 📄 .gitignore             # Arquivos ignorados
```

### Nomenclatura de Arquivos

- **PHP**: `snake_case.php` (ex: `user_profile.php`)
- **JavaScript**: `camelCase.js` (ex: `cartManager.js`)
- **CSS**: `kebab-case.css` (ex: `product-card.css`)
- **Imagens**: `kebab-case.ext` (ex: `product-image.jpg`)

## 🔧 Ferramentas de Desenvolvimento

### Build System (Gulp)

```javascript
// gulpfile.js
const gulp = require('gulp');
const sass = require('gulp-sass');
const pug = require('gulp-pug');
const autoprefixer = require('gulp-autoprefixer');
const cleanCSS = require('gulp-clean-css');
const uglify = require('gulp-uglify');
const imagemin = require('gulp-imagemin');

// Compilar SCSS
gulp.task('sass', () => {
    return gulp.src('src/scss/**/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer())
        .pipe(cleanCSS())
        .pipe(gulp.dest('dist/css'));
});

// Compilar Pug
gulp.task('pug', () => {
    return gulp.src('src/pug/**/*.pug')
        .pipe(pug())
        .pipe(gulp.dest('dist'));
});

// Minificar JavaScript
gulp.task('js', () => {
    return gulp.src('static/js/**/*.js')
        .pipe(uglify())
        .pipe(gulp.dest('dist/js'));
});

// Otimizar imagens
gulp.task('images', () => {
    return gulp.src('static/img/**/*')
        .pipe(imagemin())
        .pipe(gulp.dest('dist/img'));
});

// Watch files
gulp.task('watch', () => {
    gulp.watch('src/scss/**/*.scss', gulp.series('sass'));
    gulp.watch('src/pug/**/*.pug', gulp.series('pug'));
    gulp.watch('static/js/**/*.js', gulp.series('js'));
    gulp.watch('static/img/**/*', gulp.series('images'));
});

// Build completo
gulp.task('build', gulp.parallel('sass', 'pug', 'js', 'images'));

// Desenvolvimento
gulp.task('dev', gulp.series('build', 'watch'));
```

### Package.json

```json
{
    "name": "tempero-e-cafe",
    "version": "1.0.0",
    "description": "E-commerce de produtos naturais",
    "scripts": {
        "dev": "gulp dev",
        "build": "gulp build",
        "test": "phpunit tests/",
        "lint": "phpcs src/ && eslint static/js/",
        "format": "phpcbf src/ && prettier --write static/js/"
    },
    "devDependencies": {
        "gulp": "^4.0.2",
        "gulp-sass": "^5.1.0",
        "gulp-pug": "^5.0.0",
        "gulp-autoprefixer": "^8.0.0",
        "gulp-clean-css": "^4.3.0",
        "gulp-uglify": "^3.0.2",
        "gulp-imagemin": "^8.0.0"
    }
}
```

## 🧪 Testes

### Testes PHP (PHPUnit)

```php
<?php
// tests/ProductServiceTest.php
use PHPUnit\Framework\TestCase;

class ProductServiceTest extends TestCase {
    private $productService;
    private $db;
    
    protected function setUp(): void {
        // Setup database connection
        $this->db = new PDO('sqlite::memory:');
        $this->createTestTables();
        $this->productService = new ProductService($this->db);
    }
    
    public function testGetFeaturedProducts() {
        // Arrange
        $this->insertTestProduct(['is_featured' => 1]);
        
        // Act
        $products = $this->productService->getFeaturedProducts();
        
        // Assert
        $this->assertCount(1, $products);
        $this->assertEquals(1, $products[0]['is_featured']);
    }
    
    public function testAddToCart() {
        // Arrange
        $productId = $this->insertTestProduct();
        $sessionId = 'test-session';
        
        // Act
        $result = $this->productService->addToCart($sessionId, $productId, 2);
        
        // Assert
        $this->assertTrue($result);
        
        $cartItems = $this->productService->getCartItems($sessionId);
        $this->assertCount(1, $cartItems);
        $this->assertEquals(2, $cartItems[0]['quantity']);
    }
    
    private function createTestTables() {
        $sql = "
            CREATE TABLE products (
                id INTEGER PRIMARY KEY,
                name VARCHAR(200),
                price DECIMAL(10,2),
                is_featured BOOLEAN DEFAULT 0
            );
            
            CREATE TABLE cart_items (
                id INTEGER PRIMARY KEY,
                session_id VARCHAR(128),
                product_id INTEGER,
                quantity INTEGER
            );
        ";
        
        $this->db->exec($sql);
    }
    
    private function insertTestProduct($data = []) {
        $defaultData = [
            'name' => 'Test Product',
            'price' => 10.00,
            'is_featured' => 0
        ];
        
        $data = array_merge($defaultData, $data);
        
        $stmt = $this->db->prepare("
            INSERT INTO products (name, price, is_featured) 
            VALUES (?, ?, ?)
        ");
        
        $stmt->execute([$data['name'], $data['price'], $data['is_featured']]);
        return $this->db->lastInsertId();
    }
}
```

### Testes JavaScript (Jest)

```javascript
// tests/cartManager.test.js
import CartManager from '../static/js/cartManager.js';

describe('CartManager', () => {
    let cartManager;
    let mockFetch;
    
    beforeEach(() => {
        cartManager = new CartManager();
        mockFetch = jest.fn();
        global.fetch = mockFetch;
    });
    
    afterEach(() => {
        jest.clearAllMocks();
    });
    
    test('should add product to cart', async () => {
        // Arrange
        const mockResponse = {
            success: true,
            message: 'Produto adicionado ao carrinho!',
            item_count: 1
        };
        
        mockFetch.mockResolvedValueOnce({
            json: () => Promise.resolve(mockResponse)
        });
        
        // Act
        const result = await cartManager.addToCart(123, 2);
        
        // Assert
        expect(mockFetch).toHaveBeenCalledWith(
            'cart_api.php?action=add',
            expect.objectContaining({
                method: 'POST',
                body: expect.any(FormData)
            })
        );
        
        expect(result).toBe(true);
    });
    
    test('should handle cart API errors', async () => {
        // Arrange
        const mockResponse = {
            success: false,
            message: 'Produto não encontrado'
        };
        
        mockFetch.mockResolvedValueOnce({
            json: () => Promise.resolve(mockResponse)
        });
        
        // Act & Assert
        await expect(cartManager.addToCart(999, 1))
            .rejects.toThrow('Produto não encontrado');
    });
});
```

## 🔍 Linting e Formatação

### PHP CodeSniffer

```xml
<!-- phpcs.xml -->
<?xml version="1.0"?>
<ruleset name="Tempero e Café">
    <description>Coding standard for Tempero e Café</description>
    
    <file>src/</file>
    <file>admin/</file>
    <file>api/</file>
    
    <exclude-pattern>*/vendor/*</exclude-pattern>
    
    <rule ref="PSR12"/>
    
    <rule ref="Generic.Files.LineLength">
        <properties>
            <property name="lineLimit" value="120"/>
            <property name="absoluteLineLimit" value="140"/>
        </properties>
    </rule>
    
    <rule ref="Generic.Commenting.DocComment">
        <properties>
            <property name="required" value="true"/>
        </properties>
    </rule>
</ruleset>
```

### ESLint

```javascript
// .eslintrc.js
module.exports = {
    env: {
        browser: true,
        es2021: true,
        node: true
    },
    extends: [
        'eslint:recommended'
    ],
    parserOptions: {
        ecmaVersion: 12,
        sourceType: 'module'
    },
    rules: {
        'indent': ['error', 4],
        'linebreak-style': ['error', 'unix'],
        'quotes': ['error', 'single'],
        'semi': ['error', 'always'],
        'no-unused-vars': 'warn',
        'no-console': 'warn'
    },
    globals: {
        'bootstrap': 'readonly',
        '$': 'readonly'
    }
};
```

## 📊 Debugging

### PHP Debugging

```php
<?php
// Debug helper functions
function debug($data, $label = 'DEBUG') {
    if (defined('DEBUG') && DEBUG) {
        echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px; border: 1px solid #ccc;'>";
        echo "<strong>{$label}:</strong><br>";
        echo "<pre>" . print_r($data, true) . "</pre>";
        echo "</div>";
    }
}

function logError($message, $context = []) {
    $log = [
        'timestamp' => date('Y-m-d H:i:s'),
        'message' => $message,
        'context' => $context,
        'file' => debug_backtrace()[0]['file'],
        'line' => debug_backtrace()[0]['line']
    ];
    
    error_log(json_encode($log));
}

// Database query logging
class DatabaseLogger {
    private $db;
    private $logQueries;
    
    public function __construct($database, $logQueries = false) {
        $this->db = $database;
        $this->logQueries = $logQueries;
    }
    
    public function query($sql, $params = []) {
        if ($this->logQueries) {
            logError('SQL Query', [
                'sql' => $sql,
                'params' => $params
            ]);
        }
        
        $startTime = microtime(true);
        $result = $this->db->query($sql);
        $executionTime = (microtime(true) - $startTime) * 1000;
        
        if ($this->logQueries) {
            logError('Query executed', [
                'execution_time' => $executionTime . 'ms'
            ]);
        }
        
        return $result;
    }
}
```

### JavaScript Debugging

```javascript
// Debug utilities
class Debugger {
    static log(message, data = null) {
        if (window.DEBUG) {
            console.log(`[DEBUG] ${message}`, data);
        }
    }
    
    static error(message, error = null) {
        console.error(`[ERROR] ${message}`, error);
        
        // Send to error tracking service
        if (window.errorTracker) {
            window.errorTracker.captureException(error);
        }
    }
    
    static time(label) {
        if (window.DEBUG) {
            console.time(label);
        }
    }
    
    static timeEnd(label) {
        if (window.DEBUG) {
            console.timeEnd(label);
        }
    }
    
    static measurePerformance(fn, label) {
        return function(...args) {
            this.time(label);
            const result = fn.apply(this, args);
            this.timeEnd(label);
            return result;
        };
    }
}

// Usage examples
Debugger.log('Cart updated', { itemCount: 5 });
Debugger.time('API Request');
// ... API call
Debugger.timeEnd('API Request');

// Performance measurement
const optimizedFunction = Debugger.measurePerformance(originalFunction, 'Function Name');
```

## 🚀 Deploy e CI/CD

### GitHub Actions

```yaml
# .github/workflows/ci.yml
name: CI/CD Pipeline

on:
    push:
        branches: [ main, develop ]
    pull_request:
        branches: [ main ]

jobs:
    test:
        runs-on: ubuntu-latest
        
        steps:
        - uses: actions/checkout@v3
        
        - name: Setup PHP
          uses: shivammathur/setup-php@v2
          with:
              php-version: '7.4'
              extensions: pdo_mysql, mysqli, json, mbstring
              
        - name: Setup Node.js
          uses: actions/setup-node@v3
          with:
              node-version: '16'
              
        - name: Install dependencies
          run: |
              composer install --no-dev --optimize-autoloader
              npm ci
              
        - name: Run PHP tests
          run: vendor/bin/phpunit tests/
          
        - name: Run JavaScript tests
          run: npm test
          
        - name: Lint PHP code
          run: vendor/bin/phpcs src/ admin/ api/
          
        - name: Lint JavaScript code
          run: npx eslint static/js/
          
        - name: Build assets
          run: npm run build
          
        - name: Deploy to staging
          if: github.ref == 'refs/heads/develop'
          run: |
              # Deploy to staging server
              rsync -avz --delete dist/ user@staging-server:/var/www/tempero-e-cafe/
              
        - name: Deploy to production
          if: github.ref == 'refs/heads/main'
          run: |
              # Deploy to production server
              rsync -avz --delete dist/ user@production-server:/var/www/tempero-e-cafe/
```

### Docker

```dockerfile
# Dockerfile
FROM php:7.4-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy Apache configuration
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80

CMD ["apache2-foreground"]
```

## 📈 Performance

### Otimizações PHP

```php
<?php
// Opcache configuration
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=2
opcache.fast_shutdown=1

// Database connection pooling
class ConnectionPool {
    private static $connections = [];
    private static $maxConnections = 10;
    
    public static function getConnection() {
        if (count(self::$connections) < self::$maxConnections) {
            $connection = new PDO($dsn, $user, $pass);
            self::$connections[] = $connection;
            return $connection;
        }
        
        return array_shift(self::$connections);
    }
    
    public static function releaseConnection($connection) {
        self::$connections[] = $connection;
    }
}

// Query caching
class QueryCache {
    private static $cache = [];
    
    public static function get($key) {
        return self::$cache[$key] ?? null;
    }
    
    public static function set($key, $value, $ttl = 3600) {
        self::$cache[$key] = [
            'value' => $value,
            'expires' => time() + $ttl
        ];
    }
    
    public static function isValid($key) {
        return isset(self::$cache[$key]) && 
               self::$cache[$key]['expires'] > time();
    }
}
```

### Otimizações Frontend

```javascript
// Lazy loading
class LazyLoader {
    constructor() {
        this.observer = new IntersectionObserver(
            this.handleIntersection.bind(this),
            { threshold: 0.1 }
        );
        
        this.observeElements();
    }
    
    observeElements() {
        document.querySelectorAll('[data-lazy]').forEach(el => {
            this.observer.observe(el);
        });
    }
    
    handleIntersection(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                this.loadElement(entry.target);
                this.observer.unobserve(entry.target);
            }
        });
    }
    
    loadElement(element) {
        const src = element.dataset.lazy;
        if (src) {
            element.src = src;
            element.classList.add('loaded');
        }
    }
}

// Image optimization
class ImageOptimizer {
    static optimizeImage(img) {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        
        canvas.width = img.width;
        canvas.height = img.height;
        
        ctx.drawImage(img, 0, 0);
        
        return canvas.toDataURL('image/jpeg', 0.8);
    }
    
    static preloadImages(urls) {
        urls.forEach(url => {
            const img = new Image();
            img.src = url;
        });
    }
}
```

## 🔒 Segurança

### Validação de Dados

```php
<?php
class Validator {
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public static function validatePhone($phone) {
        return preg_match('/^\(\d{2}\)\s\d{4,5}-\d{4}$/', $phone);
    }
    
    public static function sanitizeInput($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    public static function validateCSRF($token) {
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    public static function generateCSRF() {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }
}

// Rate limiting
class RateLimiter {
    private $redis;
    
    public function __construct($redis) {
        $this->redis = $redis;
    }
    
    public function isAllowed($key, $limit, $window) {
        $current = $this->redis->incr($key);
        
        if ($current === 1) {
            $this->redis->expire($key, $window);
        }
        
        return $current <= $limit;
    }
}
```

## 📚 Documentação de Código

### PHPDoc

```php
<?php
/**
 * Classe para gerenciamento de produtos
 * 
 * @package TemperoCafe
 * @subpackage Services
 * @author Desenvolvedor
 * @version 1.0.0
 */
class ProductService {
    /**
     * Instância do banco de dados
     * 
     * @var PDO
     */
    private $db;
    
    /**
     * Construtor da classe
     * 
     * @param PDO $database Instância do banco de dados
     */
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Busca produtos em destaque
     * 
     * @param int $limit Limite de produtos a retornar
     * @return array Lista de produtos em destaque
     * @throws PDOException Quando há erro na consulta
     */
    public function getFeaturedProducts($limit = 6) {
        // Implementação
    }
}
```

### JSDoc

```javascript
/**
 * Classe para gerenciamento do carrinho
 * @class CartManager
 */
class CartManager {
    /**
     * Construtor da classe
     * @constructor
     */
    constructor() {
        this.items = [];
    }
    
    /**
     * Adiciona produto ao carrinho
     * @param {number} productId - ID do produto
     * @param {number} quantity - Quantidade
     * @returns {Promise<boolean>} Sucesso da operação
     * @throws {Error} Quando há erro na API
     */
    async addToCart(productId, quantity = 1) {
        // Implementação
    }
}
```

---

Este guia de desenvolvimento fornece as diretrizes essenciais para contribuir com o projeto Tempero e Café de forma consistente e profissional.
