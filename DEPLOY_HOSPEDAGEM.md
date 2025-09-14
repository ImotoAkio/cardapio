# 🚀 **GUIA DE DEPLOY PARA HOSPEDAGEM**

## ✅ **PROBLEMA RESOLVIDO NO LOCALHOST**

O erro foi causado pelo arquivo `.htaccess` que estava forçando HTTPS em localhost. Agora está funcionando perfeitamente!

---

## 🌐 **PREPARAÇÃO PARA HOSPEDAGEM**

### **📁 Arquivos Essenciais para Upload:**

#### **1. 🏠 Arquivos Principais:**
```
✅ home.php
✅ cart.php
✅ checkout.php
✅ product.php
✅ shop.php
✅ profile.php
✅ login.php
✅ cadastro.php
✅ category.php
✅ search.php
✅ settings.php
✅ my-orders.php
✅ notifications.php
✅ pages.php
✅ edit-profile.php
✅ logout.php
✅ save_avatar.php
✅ order_details_content.php
✅ cart_api.php
```

#### **2. 📁 Pastas Importantes:**
```
✅ admin/ (painel administrativo)
✅ api/ (APIs REST)
✅ dist/ (assets compilados)
✅ includes/ (configurações)
✅ js/ (JavaScript customizado)
✅ documentacao/ (documentação)
```

#### **3. ⚙️ Arquivos de Configuração:**
```
✅ .htaccess (configuração Apache)
✅ composer.json (dependências)
✅ composer.lock (versões)
✅ config.env.example (configuração)
✅ README.md (documentação)
✅ LICENSE (licença)
✅ CHANGELOG.md (histórico)
```

---

## 🔧 **CONFIGURAÇÕES PARA HOSPEDAGEM**

### **1. 📝 Arquivo de Configuração:**

Crie um arquivo `config.env` na hospedagem:

```env
# Configurações do Banco de Dados
DB_HOST=localhost
DB_NAME=seu_banco
DB_USER=seu_usuario
DB_PASS=sua_senha
DB_CHARSET=utf8mb4

# Configurações da Aplicação
APP_NAME=Tempero e Café
APP_VERSION=1.0.0
APP_ENV=production
APP_DEBUG=false

# Configurações de URL
APP_URL=https://seudominio.com
APP_BASE_PATH=/

# Configurações de Webhook N8N
N8N_WEBHOOK_URL=https://webhook.echo.dev.br/webhook/8cea05f1-e082-45ea-83ca-f80809af9cfd
N8N_WEBHOOK_TEST_URL=https://n8n.echo.dev.br/webhook-test/8cea05f1-e082-45ea-83ca-f0809af9cfd
N8N_WEBHOOK_PROGRESSION_URL=https://n8n.echo.dev.br/webhook-test/e8a2f4db-eefd-498e-9547-a0200442c108
```

### **2. 🗄️ Configuração do Banco:**

Atualize `includes/database.php` com as credenciais da hospedagem:

```php
// Configurações do banco de dados
define('DB_HOST', 'localhost'); // ou IP do servidor
define('DB_NAME', 'seu_banco');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
define('DB_CHARSET', 'utf8mb4');
```

### **3. 🌐 Configuração de URLs:**

Atualize as URLs nos arquivos que precisam:

```php
// Em includes/database.php - função getBasePath()
function getBasePath() {
    // Para hospedagem, ajustar conforme necessário
    return '/'; // ou '/cardapio/' se estiver em subpasta
}
```

---

## 📦 **PASSOS PARA DEPLOY**

### **1. 📁 Upload dos Arquivos:**
```
1. Conecte via FTP/SFTP
2. Faça upload de todos os arquivos
3. Mantenha a estrutura de pastas
4. Configure permissões (644 para arquivos, 755 para pastas)
```

### **2. 🗄️ Configuração do Banco:**
```
1. Crie o banco de dados MySQL
2. Importe o arquivo tempero_e_cafe_database.sql
3. Configure as credenciais em includes/database.php
```

### **3. ⚙️ Configuração do Servidor:**
```
1. Configure .htaccess (já está otimizado)
2. Configure SSL/HTTPS (obrigatório para PWA)
3. Configure permissões de pastas
```

### **4. 🧪 Testes Pós-Deploy:**
```
1. Acesse a homepage
2. Teste login/registro
3. Teste carrinho
4. Teste checkout
5. Teste PWA
6. Teste painel admin
```

---

## 🔒 **CONFIGURAÇÕES DE SEGURANÇA**

### **1. 🔐 HTTPS Obrigatório:**
```apache
# No .htaccess da hospedagem
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### **2. 🛡️ Headers de Segurança:**
```apache
# Headers de segurança
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
```

### **3. 🔒 Proteção de Arquivos:**
```apache
# Proteger arquivos sensíveis
<FilesMatch "\.(env|log|sql)$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

---

## 📱 **CONFIGURAÇÃO PWA PARA HOSPEDAGEM**

### **1. 🌐 URLs Absolutas:**
Atualize `dist/manifest.json`:

```json
{
    "name": "Tempero e Café",
    "short_name": "Tempero e Café",
    "start_url": "https://seudominio.com/home.php",
    "scope": "https://seudominio.com/",
    "icons": [
        {
            "src": "https://seudominio.com/dist/img/icons/android-icon-192x192.png",
            "sizes": "192x192",
            "type": "image/png"
        }
    ]
}
```

### **2. 🔧 Service Worker:**
Atualize `dist/service-worker.js` com URLs absolutas se necessário.

---

## 🧪 **TESTES ESPECÍFICOS PARA HOSPEDAGEM**

### **1. 🔍 Teste de Conectividade:**
```
✅ Acesso à homepage
✅ Carregamento de CSS/JS
✅ Carregamento de imagens
✅ Conexão com banco
```

### **2. 🛒 Teste de E-commerce:**
```
✅ Adicionar ao carrinho
✅ Finalizar compra
✅ Webhook N8N
✅ Emails de confirmação
```

### **3. 📱 Teste de PWA:**
```
✅ Manifest carregando
✅ Service Worker ativo
✅ Instalação em mobile
✅ Funcionamento offline
```

### **4. 🔧 Teste de Admin:**
```
✅ Login administrativo
✅ Upload de imagens
✅ Gerenciamento de produtos
✅ Gerenciamento de pedidos
```

---

## 🚨 **PROBLEMAS COMUNS NA HOSPEDAGEM**

### **❌ Erro 500:**
```
1. Verificar logs de erro
2. Verificar permissões
3. Verificar configurações PHP
4. Verificar .htaccess
```

### **❌ Banco não conecta:**
```
1. Verificar credenciais
2. Verificar se banco existe
3. Verificar host do banco
4. Testar conexão manual
```

### **❌ PWA não funciona:**
```
1. Verificar HTTPS
2. Verificar manifest.json
3. Verificar service-worker.js
4. Verificar ícones
```

### **❌ Upload não funciona:**
```
1. Verificar permissões de pasta
2. Verificar limite de upload
3. Verificar configurações PHP
4. Verificar espaço em disco
```

---

## ✅ **CHECKLIST DE DEPLOY**

### **🎯 Antes do Deploy:**
```
□ Arquivos preparados
□ Banco configurado
□ Credenciais atualizadas
□ URLs configuradas
□ SSL configurado
```

### **🎉 Após o Deploy:**
```
□ Homepage funcionando
□ Login/registro funcionando
□ Carrinho funcionando
□ Checkout funcionando
□ PWA funcionando
□ Admin funcionando
□ Webhooks funcionando
```

---

## 🆘 **SUPORTE PÓS-DEPLOY**

### **📞 Em caso de problemas:**
```
1. Verificar logs de erro
2. Testar arquivos de diagnóstico
3. Verificar configurações
4. Contatar suporte da hospedagem
```

**🚀 Pronto para deploy na hospedagem!**

**📋 Consulte o arquivo `GUIA_DEPLOY.md` para instruções detalhadas!**
