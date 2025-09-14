# 🚀 **GUIA DE DEPLOY - TEMPERO E CAFÉ**

## 📋 **ARQUIVOS NECESSÁRIOS PARA HOSPEDAGEM**

### **✅ Arquivos Criados:**
- **`composer.json`** - Dependências do projeto
- **`composer.lock`** - Versões específicas das dependências
- **`config.env.example`** - Configuração de ambiente

---

## 🔧 **REQUISITOS DO SERVIDOR**

### **📱 PHP:**
- **Versão:** PHP 7.4 ou superior
- **Extensões obrigatórias:**
  - `ext-pdo` - Conexão com banco de dados
  - `ext-pdo_mysql` - Driver MySQL
  - `ext-curl` - Requisições HTTP (webhooks)
  - `ext-json` - Manipulação de JSON
  - `ext-mbstring` - Strings multibyte
  - `ext-fileinfo` - Detecção de tipos de arquivo
  - `ext-gd` - Manipulação de imagens

### **🗄️ Banco de Dados:**
- **MySQL 5.7+** ou **MariaDB 10.2+**
- **Charset:** UTF-8 (utf8mb4)

### **🌐 Servidor Web:**
- **Apache 2.4+** ou **Nginx 1.18+**
- **HTTPS** obrigatório para PWA
- **Mod_rewrite** habilitado (Apache)

---

## 📦 **PASSOS PARA DEPLOY**

### **1. 📁 Upload dos Arquivos:**
```
1. Faça upload de todos os arquivos para o servidor
2. Mantenha a estrutura de pastas intacta
3. Certifique-se de que as permissões estão corretas
```

### **2. 🗄️ Configuração do Banco:**
```
1. Crie o banco de dados MySQL
2. Importe o arquivo SQL (se disponível)
3. Configure as credenciais em includes/database.php
```

### **3. ⚙️ Configuração do Ambiente:**
```
1. Copie config.env.example para config.env
2. Configure as variáveis de ambiente
3. Ajuste as URLs e credenciais
```

### **4. 🔒 Configuração HTTPS:**
```
1. Configure SSL no servidor
2. Force redirecionamento HTTPS
3. Atualize as URLs nos arquivos de configuração
```

### **5. 📱 Configuração PWA:**
```
1. Verifique se o manifest.json está acessível
2. Teste o service-worker.js
3. Configure os ícones PWA
```

---

## 🔧 **CONFIGURAÇÕES IMPORTANTES**

### **📁 Permissões de Pastas:**
```
dist/img/ - 755 (leitura/escrita)
dist/uploads/ - 755 (leitura/escrita)
logs/ - 755 (leitura/escrita)
```

### **🌐 URLs de Produção:**
```
APP_URL=https://seudominio.com
APP_BASE_PATH=/
N8N_WEBHOOK_URL=https://webhook.echo.dev.br/webhook/8cea05f1-e082-45ea-83ca-f80809af9cfd
```

### **🔒 Configurações de Segurança:**
```
SESSION_SECURE=true
APP_DEBUG=false
LOG_LEVEL=error
```

---

## 🧪 **TESTES PÓS-DEPLOY**

### **1. 🔍 Testes Básicos:**
```
✅ Acesso à homepage
✅ Login de usuário
✅ Navegação entre páginas
✅ Carregamento de imagens
```

### **2. 🛒 Testes de E-commerce:**
```
✅ Adicionar produtos ao carrinho
✅ Finalizar compra
✅ Webhook N8N funcionando
✅ Emails de confirmação
```

### **3. 📱 Testes PWA:**
```
✅ Manifest carregando
✅ Service Worker ativo
✅ Instalação em mobile
✅ Funcionamento offline
```

### **4. 🔧 Testes Admin:**
```
✅ Login administrativo
✅ Gerenciamento de produtos
✅ Gerenciamento de pedidos
✅ Upload de imagens
```

---

## 🚨 **PROBLEMAS COMUNS**

### **❌ Erro 500:**
```
1. Verifique logs de erro
2. Confirme permissões de arquivos
3. Verifique configurações PHP
```

### **❌ Banco não conecta:**
```
1. Verifique credenciais
2. Confirme se o banco existe
3. Teste conexão manual
```

### **❌ PWA não instala:**
```
1. Confirme HTTPS ativo
2. Verifique manifest.json
3. Teste service-worker.js
```

### **❌ Webhooks não funcionam:**
```
1. Verifique URLs dos webhooks
2. Teste conectividade externa
3. Confirme permissões cURL
```

---

## 📊 **MONITORAMENTO**

### **📈 Métricas Importantes:**
```
- Uptime do servidor
- Tempo de resposta
- Erros 404/500
- Uso de CPU/RAM
- Espaço em disco
```

### **🔍 Logs para Acompanhar:**
```
- Logs de erro PHP
- Logs de acesso web
- Logs de webhook N8N
- Logs de banco de dados
```

---

## ✅ **CHECKLIST FINAL**

### **🎯 Antes de Go-Live:**
```
□ Todos os arquivos enviados
□ Banco de dados configurado
□ HTTPS configurado
□ PWA funcionando
□ Webhooks testados
□ Backup realizado
□ Monitoramento ativo
```

### **🎉 Após Go-Live:**
```
□ Testes em produção
□ Monitoramento ativo
□ Backup automático
□ Logs sendo coletados
□ Performance OK
□ Usuários conseguem usar
```

---

## 🆘 **SUPORTE**

### **📞 Em caso de problemas:**
```
1. Verifique logs de erro
2. Teste em ambiente local
3. Confirme configurações
4. Entre em contato com suporte
```

**🎉 Deploy realizado com sucesso!**
