# 🚀 **INSTRUÇÕES DE INSTALAÇÃO - TEMPERO E CAFÉ**

## 📋 **ARQUIVOS CRIADOS PARA HOSPEDAGEM**

### **✅ Arquivos Principais:**
- **`composer.json`** - Dependências do projeto
- **`composer.lock`** - Versões específicas das dependências
- **`config.env.example`** - Configuração de ambiente
- **`.htaccess`** - Configuração Apache
- **`nginx.conf`** - Configuração Nginx
- **`Dockerfile`** - Configuração Docker
- **`docker-compose.yml`** - Orquestração Docker
- **`GUIA_DEPLOY.md`** - Guia completo de deploy

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

## 📦 **OPÇÕES DE INSTALAÇÃO**

### **1. 🐳 Docker (Recomendado):**
```bash
# Clonar o projeto
git clone https://github.com/seu-usuario/cardapio.git
cd cardapio

# Configurar ambiente
cp config.env.example config.env
# Editar config.env com suas configurações

# Iniciar com Docker
docker-compose up -d

# Acessar aplicação
http://localhost
```

### **2. 🌐 Hospedagem Compartilhada:**
```bash
# Upload dos arquivos via FTP/SFTP
# Manter estrutura de pastas intacta

# Configurar banco de dados
# Importar arquivo SQL

# Configurar .htaccess
# Ajustar permissões de pastas

# Testar aplicação
```

### **3. 🖥️ VPS/Dedicado:**
```bash
# Instalar dependências
sudo apt update
sudo apt install apache2 mysql-server php7.4 php7.4-mysql php7.4-curl php7.4-gd

# Configurar Apache
sudo a2enmod rewrite
sudo systemctl restart apache2

# Configurar MySQL
sudo mysql_secure_installation

# Upload dos arquivos
# Configurar banco de dados
# Testar aplicação
```

---

## ⚙️ **CONFIGURAÇÃO DO AMBIENTE**

### **1. 📁 Configuração de Arquivos:**
```bash
# Copiar arquivo de configuração
cp config.env.example config.env

# Editar configurações
nano config.env
```

### **2. 🗄️ Configuração do Banco:**
```sql
-- Criar banco de dados
CREATE DATABASE cardapio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Criar usuário
CREATE USER 'cardapio'@'localhost' IDENTIFIED BY 'senha_segura';
GRANT ALL PRIVILEGES ON cardapio.* TO 'cardapio'@'localhost';
FLUSH PRIVILEGES;
```

### **3. 🔒 Configuração HTTPS:**
```bash
# Obter certificado SSL
# Configurar redirecionamento HTTPS
# Atualizar URLs nos arquivos de configuração
```

---

## 🧪 **TESTES PÓS-INSTALAÇÃO**

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

**🎉 Instalação realizada com sucesso!**
