# ⚙️ Instalação e Configuração - Tempero e Café

## 📋 Pré-requisitos

### Requisitos Mínimos do Sistema
- **PHP**: 7.4 ou superior
- **MySQL**: 8.0 ou superior
- **Apache**: 2.4+ ou **Nginx**: 1.18+
- **Node.js**: 16+ (para desenvolvimento)
- **Composer**: Última versão (opcional)

### Extensões PHP Necessárias
```bash
# Verificar extensões instaladas
php -m | grep -E "(pdo|mysqli|json|mbstring|openssl|curl|gd|zip)"
```

**Extensões Obrigatórias:**
- `pdo_mysql` - Conexão com MySQL
- `json` - Manipulação de JSON
- `mbstring` - Manipulação de strings multibyte
- `openssl` - Criptografia e HTTPS
- `curl` - Requisições HTTP
- `gd` - Manipulação de imagens
- `zip` - Arquivos compactados

## 🚀 Instalação Passo a Passo

### 1. **Clone do Repositório**

```bash
# Clone o repositório
git clone https://github.com/seu-usuario/tempero-e-cafe.git
cd tempero-e-cafe

# Ou baixe o ZIP e extraia
wget https://github.com/seu-usuario/tempero-e-cafe/archive/main.zip
unzip main.zip
cd tempero-e-cafe-main
```

### 2. **Configuração do Banco de Dados**

#### Criar Banco de Dados
```sql
-- Conectar ao MySQL
mysql -u root -p

-- Criar banco de dados
CREATE DATABASE cardapio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Criar usuário específico (recomendado)
CREATE USER 'tempero_user'@'localhost' IDENTIFIED BY 'senha_segura';
GRANT ALL PRIVILEGES ON cardapio.* TO 'tempero_user'@'localhost';
FLUSH PRIVILEGES;
```

#### Importar Estrutura do Banco
```bash
# Importar arquivo SQL
mysql -u tempero_user -p cardapio < tempero_e_cafe_database.sql

# Ou via phpMyAdmin
# 1. Acesse phpMyAdmin
# 2. Selecione o banco 'cardapio'
# 3. Vá em 'Importar'
# 4. Selecione o arquivo 'tempero_e_cafe_database.sql'
# 5. Clique em 'Executar'
```

### 3. **Configuração do Ambiente**

#### Arquivo de Configuração Principal
Edite `includes/database.php`:

```php
<?php
// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'cardapio');
define('DB_USER', 'tempero_user');        // Seu usuário
define('DB_PASS', 'senha_segura');        // Sua senha
define('DB_CHARSET', 'utf8mb4');

// Configurações da aplicação
define('APP_NAME', 'Tempero e Café');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'production');          // ou 'development'
define('DEBUG', false);                   // true para desenvolvimento
```

#### Arquivo de Configuração do Admin
Edite `admin/config.php`:

```php
<?php
// Configurações específicas do admin
define('ADMIN_SESSION', 'tempero_admin_session');
define('PASSWORD_MIN_LENGTH', 6);
define('SESSION_TIMEOUT', 3600); // 1 hora

// Configurações de upload
define('UPLOAD_PATH', '../dist/img/product/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);
```

### 4. **Configuração do Servidor Web**

#### Apache (.htaccess)
Crie/edite o arquivo `.htaccess` na raiz:

```apache
# Habilitar mod_rewrite
RewriteEngine On

# Redirecionar para HTTPS (produção)
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Página inicial
DirectoryIndex home.php

# Proteger arquivos sensíveis
<Files "*.sql">
    Order allow,deny
    Deny from all
</Files>

<Files ".env">
    Order allow,deny
    Deny from all
</Files>

# Cache de assets
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
</IfModule>

# Compressão
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>
```

#### Nginx
Configuração para Nginx:

```nginx
server {
    listen 80;
    listen 443 ssl http2;
    server_name temperoecafe.com www.temperoecafe.com;
    
    root /var/www/tempero-e-cafe;
    index home.php;
    
    # SSL (produção)
    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;
    
    # Redirecionar HTTP para HTTPS
    if ($scheme != "https") {
        return 301 https://$server_name$request_uri;
    }
    
    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index home.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    # Cache de assets
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
    
    # Proteger arquivos sensíveis
    location ~ /\.(env|sql)$ {
        deny all;
    }
    
    # PWA Service Worker
    location = /service-worker.js {
        add_header Cache-Control "no-cache";
        proxy_cache_bypass $http_pragma;
        proxy_cache_revalidate on;
        expires off;
        access_log off;
    }
}
```

### 5. **Instalação de Dependências (Desenvolvimento)**

```bash
# Instalar Node.js (Ubuntu/Debian)
curl -fsSL https://deb.nodesource.com/setup_16.x | sudo -E bash -
sudo apt-get install -y nodejs

# Instalar dependências
npm install

# Verificar instalação
node --version
npm --version
```

### 6. **Compilação de Assets**

```bash
# Desenvolvimento (watch mode)
npm run dev

# Produção
npm run build

# Verificar arquivos compilados
ls -la dist/
```

### 7. **Configuração de Permissões**

```bash
# Definir proprietário correto
sudo chown -R www-data:www-data /var/www/tempero-e-cafe

# Permissões para uploads
sudo chmod -R 755 /var/www/tempero-e-cafe
sudo chmod -R 777 /var/www/tempero-e-cafe/dist/img/product/
sudo chmod -R 777 /var/www/tempero-e-cafe/dist/img/category/

# Logs
sudo mkdir -p /var/www/tempero-e-cafe/logs
sudo chmod 777 /var/www/tempero-e-cafe/logs
```

## 🔧 Configurações Avançadas

### 1. **Variáveis de Ambiente**

Crie arquivo `.env` na raiz:

```env
# Banco de Dados
DB_HOST=localhost
DB_NAME=cardapio
DB_USER=tempero_user
DB_PASS=senha_segura

# Aplicação
APP_NAME=Tempero e Café
APP_ENV=production
DEBUG=false
APP_URL=https://temperoecafe.com

# Email (opcional)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=sua-senha-app

# Integração N8N
N8N_WEBHOOK_URL=https://n8n.exemplo.com/webhook/tempero

# Cache
CACHE_DRIVER=file
CACHE_TTL=3600

# Upload
MAX_FILE_SIZE=5242880
ALLOWED_EXTENSIONS=jpg,jpeg,png,webp
```

### 2. **Configuração de Email**

Para notificações por email, configure em `includes/email.php`:

```php
<?php
// Configurações de email
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'seu-email@gmail.com');
define('MAIL_PASSWORD', 'sua-senha-app');
define('MAIL_FROM_NAME', 'Tempero e Café');
define('MAIL_FROM_EMAIL', 'contato@temperoecafe.com');

// Função para enviar email
function sendEmail($to, $subject, $message) {
    // Implementação usando PHPMailer ou mail() nativo
}
```

### 3. **Configuração de Cache**

Para melhorar performance, configure cache:

```php
<?php
// Cache com APCu (se disponível)
if (extension_loaded('apcu')) {
    define('CACHE_ENABLED', true);
    define('CACHE_TTL', 3600); // 1 hora
} else {
    define('CACHE_ENABLED', false);
}

// Função de cache
function cacheGet($key) {
    if (!CACHE_ENABLED) return false;
    return apcu_fetch($key);
}

function cacheSet($key, $value, $ttl = null) {
    if (!CACHE_ENABLED) return false;
    return apcu_store($key, $value, $ttl ?? CACHE_TTL);
}
```

## 🔒 Configurações de Segurança

### 1. **Configuração de Sessões**

```php
<?php
// Configurações de sessão segura
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // HTTPS apenas
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

// Regenerar ID da sessão
session_start();
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}
```

### 2. **Configuração de Upload Seguro**

```php
<?php
// Validação de uploads
function validateUpload($file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Tipo de arquivo não permitido');
    }
    
    if ($file['size'] > $maxSize) {
        throw new Exception('Arquivo muito grande');
    }
    
    // Verificar se é realmente uma imagem
    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        throw new Exception('Arquivo não é uma imagem válida');
    }
    
    return true;
}
```

### 3. **Configuração de Headers de Segurança**

```php
<?php
// Headers de segurança
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\'; style-src \'self\' \'unsafe-inline\'; img-src \'self\' data:;');
```

## 📊 Configuração de Monitoramento

### 1. **Logs de Erro**

```php
<?php
// Configuração de logs
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

// Log personalizado
function logError($message, $context = []) {
    $log = [
        'timestamp' => date('Y-m-d H:i:s'),
        'message' => $message,
        'context' => $context,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ];
    
    file_put_contents('logs/app.log', json_encode($log) . "\n", FILE_APPEND);
}
```

### 2. **Monitoramento de Performance**

```php
<?php
// Medir tempo de execução
function startTimer() {
    return microtime(true);
}

function endTimer($start) {
    return (microtime(true) - $start) * 1000; // em ms
}

// Exemplo de uso
$start = startTimer();
// ... código ...
$executionTime = endTimer($start);

if ($executionTime > 1000) { // > 1 segundo
    logError("Slow execution: {$executionTime}ms", [
        'script' => $_SERVER['SCRIPT_NAME'],
        'query' => $_SERVER['QUERY_STRING'] ?? ''
    ]);
}
```

## 🧪 Verificação da Instalação

### 1. **Script de Verificação**

Crie `check_installation.php`:

```php
<?php
echo "<h1>Verificação da Instalação - Tempero e Café</h1>";

// Verificar PHP
echo "<h2>PHP</h2>";
echo "Versão: " . PHP_VERSION . "<br>";
echo "Extensões necessárias:<br>";

$required = ['pdo_mysql', 'json', 'mbstring', 'openssl', 'curl', 'gd'];
foreach ($required as $ext) {
    $status = extension_loaded($ext) ? '✅' : '❌';
    echo "{$status} {$ext}<br>";
}

// Verificar banco de dados
echo "<h2>Banco de Dados</h2>";
try {
    require_once 'includes/database.php';
    $db = Database::getInstance()->getConnection();
    echo "✅ Conexão com banco estabelecida<br>";
    
    // Verificar tabelas
    $tables = ['users', 'products', 'categories', 'orders'];
    foreach ($tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '{$table}'");
        $status = $stmt->rowCount() > 0 ? '✅' : '❌';
        echo "{$status} Tabela {$table}<br>";
    }
} catch (Exception $e) {
    echo "❌ Erro de conexão: " . $e->getMessage() . "<br>";
}

// Verificar permissões
echo "<h2>Permissões</h2>";
$paths = ['dist/img/product/', 'dist/img/category/', 'logs/'];
foreach ($paths as $path) {
    $writable = is_writable($path) ? '✅' : '❌';
    echo "{$writable} {$path} gravável<br>";
}

// Verificar arquivos
echo "<h2>Arquivos</h2>";
$files = ['home.php', 'includes/database.php', 'dist/style.css'];
foreach ($files as $file) {
    $exists = file_exists($file) ? '✅' : '❌';
    echo "{$exists} {$file}<br>";
}
?>
```

### 2. **Teste de Funcionalidades**

```bash
# Testar APIs
curl -X GET "http://localhost/api/search_suggestions.php?q=teste"

# Testar carrinho
curl -X POST "http://localhost/cart_api.php?action=count"

# Verificar logs
tail -f logs/app.log
```

## 🚀 Deploy em Produção

### 1. **Preparação para Produção**

```bash
# Compilar assets para produção
npm run build

# Otimizar imagens
find dist/img/ -name "*.jpg" -exec jpegoptim --max=85 {} \;
find dist/img/ -name "*.png" -exec optipng -o2 {} \;

# Minificar CSS/JS
# (já feito pelo build process)
```

### 2. **Configuração de Backup**

```bash
#!/bin/bash
# backup.sh - Script de backup automático

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/tempero-e-cafe"
DB_NAME="cardapio"

# Backup do banco
mysqldump -u root -p${DB_PASS} ${DB_NAME} > ${BACKUP_DIR}/db_${DATE}.sql

# Backup dos arquivos
tar -czf ${BACKUP_DIR}/files_${DATE}.tar.gz /var/www/tempero-e-cafe

# Manter apenas últimos 7 backups
find ${BACKUP_DIR} -name "*.sql" -mtime +7 -delete
find ${BACKUP_DIR} -name "*.tar.gz" -mtime +7 -delete
```

### 3. **Configuração de SSL**

```bash
# Usando Let's Encrypt (Certbot)
sudo apt install certbot python3-certbot-apache

# Gerar certificado
sudo certbot --apache -d temperoecafe.com -d www.temperoecafe.com

# Renovação automática
sudo crontab -e
# Adicionar: 0 12 * * * /usr/bin/certbot renew --quiet
```

## 🔧 Troubleshooting

### Problemas Comuns

#### 1. **Erro de Conexão com Banco**
```bash
# Verificar se MySQL está rodando
sudo systemctl status mysql

# Testar conexão
mysql -u tempero_user -p -h localhost cardapio
```

#### 2. **Permissões de Arquivo**
```bash
# Corrigir permissões
sudo chown -R www-data:www-data /var/www/tempero-e-cafe
sudo chmod -R 755 /var/www/tempero-e-cafe
sudo chmod -R 777 /var/www/tempero-e-cafe/dist/img/
```

#### 3. **Erro de Compilação**
```bash
# Limpar cache do npm
npm cache clean --force

# Reinstalar dependências
rm -rf node_modules package-lock.json
npm install
```

#### 4. **Problemas de PWA**
```bash
# Verificar Service Worker
# 1. Abrir DevTools (F12)
# 2. Ir em Application > Service Workers
# 3. Verificar se está registrado e ativo
```

---

Esta documentação de instalação garante uma configuração completa e segura do sistema Tempero e Café.
