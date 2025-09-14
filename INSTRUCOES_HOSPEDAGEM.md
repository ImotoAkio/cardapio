# 🚀 **INSTRUÇÕES PARA HOSPEDAGEM**

## ✅ **DEPLOY CORRIGIDO**

O problema do `composer.lock` foi resolvido e o projeto está pronto para hospedagem!

---

## 📋 **CONFIGURAÇÃO NA HOSPEDAGEM**

### **1. 🗄️ Banco de Dados:**
```sql
-- Importar o arquivo: tempero_e_cafe_database.sql
-- Configurar credenciais em: includes/database.php
```

### **2. ⚙️ Arquivo de Configuração:**
Editar `includes/database.php`:
```php
define('DB_HOST', 'localhost'); // Host da hospedagem
define('DB_NAME', 'seu_banco'); // Nome do banco
define('DB_USER', 'seu_usuario'); // Usuário
define('DB_PASS', 'sua_senha'); // Senha
```

### **3. 🌐 URLs:**
Para hospedagem, ajustar a função `getBasePath()` em `includes/database.php`:
```php
function getBasePath() {
    // Para hospedagem na raiz
    return '/';
    
    // Para hospedagem em subpasta (ex: /cardapio/)
    // return '/cardapio/';
}
```

### **4. 🔒 HTTPS:**
O arquivo `.htaccess` já está configurado para forçar HTTPS (necessário para PWA).

---

## 🧪 **TESTES PÓS-DEPLOY**

### **1. 🔍 Testes Básicos:**
```
✅ Acesse: https://seudominio.com/home.php
✅ Teste login/registro
✅ Teste carrinho
✅ Teste checkout
```

### **2. 📱 Testes PWA:**
```
✅ Manifest carregando
✅ Service Worker ativo
✅ Instalação em mobile
✅ Funcionamento offline
```

### **3. 🔧 Testes Admin:**
```
✅ Login: admin/admin123
✅ Upload de imagens
✅ Gerenciamento de produtos
✅ Gerenciamento de pedidos
```

---

## 🚨 **PROBLEMAS COMUNS**

### **❌ Erro 500:**
```
1. Verificar logs de erro
2. Verificar permissões (644 para arquivos, 755 para pastas)
3. Verificar configurações PHP
4. Verificar .htaccess
```

### **❌ Banco não conecta:**
```
1. Verificar credenciais em includes/database.php
2. Verificar se banco existe
3. Verificar host do banco
4. Testar conexão manual
```

### **❌ PWA não funciona:**
```
1. Verificar HTTPS ativo
2. Verificar manifest.json
3. Verificar service-worker.js
4. Verificar ícones PWA
```

---

## 📞 **SUPORTE**

Em caso de problemas:
1. Verificar logs de erro
2. Testar configurações
3. Verificar permissões
4. Contatar suporte da hospedagem

**🎉 Projeto pronto para produção!**
