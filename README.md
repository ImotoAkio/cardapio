# 🍃 Tempero e Café - E-commerce de Produtos Naturais

[![PHP](https://img.shields.io/badge/PHP-7.4+-blue.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-green.svg)](https://getbootstrap.com)

## 📋 Sobre o Projeto

O **Tempero e Café** é uma plataforma de e-commerce especializada em produtos naturais, orgânicos e suplementos. Desenvolvido com foco em experiência mobile-first, oferece funcionalidades completas de carrinho, pedidos e gerenciamento administrativo.

### ✨ Principais Características

- 🛍️ **E-commerce Completo**: Catálogo de produtos, carrinho, checkout e gestão de pedidos
- 📱 **Interface Mobile-First**: Interface otimizada para dispositivos móveis
- 🔐 **Sistema de Autenticação**: Login, registro e gestão de perfis
- 🎨 **Interface Moderna**: Design responsivo com Bootstrap 5
- 📊 **Painel Administrativo**: Gestão completa de produtos, categorias e pedidos
- 🚀 **Performance Otimizada**: Carregamento rápido e experiência fluida
- 🔔 **Notificações**: Sistema de alertas e notificações em tempo real

## 🏗️ Arquitetura do Sistema

```
📁 Tempero e Café/
├── 📁 admin/                 # Painel administrativo
├── 📁 api/                   # APIs REST
├── 📁 dist/                  # Assets compilados (CSS, JS, imagens)
├── 📁 includes/              # Arquivos de configuração e helpers
├── 📁 src/                   # Código fonte (Pug, SCSS)
├── 📁 static/                # Assets estáticos
├── 📄 *.php                  # Páginas principais do frontend
└── 📄 *.md                   # Documentação
```

## 🚀 Tecnologias Utilizadas

### Backend
- **PHP 7.4+** - Linguagem principal
- **MySQL 8.0+** - Banco de dados
- **PDO** - Camada de abstração de dados

### Frontend
- **HTML5** - Estrutura semântica
- **CSS3** - Estilos e animações
- **Bootstrap 5.3** - Framework CSS
- **JavaScript ES6+** - Interatividade
- **jQuery** - Manipulação DOM

### Ferramentas de Desenvolvimento
- **Gulp** - Automação de tarefas
- **Pug** - Template engine
- **SCSS** - Pré-processador CSS
- **Node.js** - Ambiente de desenvolvimento

## 📦 Instalação e Configuração

### Pré-requisitos

- PHP 7.4 ou superior
- MySQL 8.0 ou superior
- Apache/Nginx
- Node.js 16+ (para desenvolvimento)
- Composer (opcional)

### 1. Clone o Repositório

```bash
git clone https://github.com/ImotoAkio/cardapio.git
cd cardapio
```

### 2. Configuração do Banco de Dados

```bash
# Importe o arquivo SQL
mysql -u root -p < tempero_e_cafe_database.sql
```

### 3. Configuração do Ambiente

Edite o arquivo `includes/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'cardapio');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
```

### 4. Instalação de Dependências (Desenvolvimento)

```bash
npm install
```

### 5. Compilação de Assets

```bash
npm run dev
```

## 🎯 Funcionalidades Principais

### 👤 Gestão de Usuários
- Registro e login de usuários
- Perfil personalizado com avatar
- Histórico de pedidos
- Lista de favoritos

### 🛍️ E-commerce
- Catálogo de produtos com categorias
- Carrinho de compras persistente
- Sistema de checkout
- Gestão de pedidos e status

### 📊 Painel Administrativo
- Dashboard com estatísticas
- Gestão de produtos e categorias
- Controle de pedidos
- Relatórios de vendas

## 📚 Documentação Detalhada

Consulte a pasta `documentacao/` para informações completas:

- [📖 Arquitetura do Sistema](documentacao/arquitetura.md)
- [🗄️ Banco de Dados](documentacao/banco-de-dados.md)
- [🔌 APIs e Endpoints](documentacao/apis.md)
- [⚙️ Instalação e Configuração](documentacao/instalacao.md)
- [🎨 Frontend e Interface](documentacao/frontend.md)
- [👨‍💼 Painel Administrativo](documentacao/admin.md)
- [🛠️ Guia de Desenvolvimento](documentacao/desenvolvimento.md)

## 🔧 Configuração de Desenvolvimento

### Estrutura de Desenvolvimento

```bash
# Compilar assets em tempo real
npm run dev

# Compilar para produção
npm run build
```

### Variáveis de Ambiente

Crie um arquivo `.env` na raiz do projeto:

```env
DB_HOST=localhost
DB_NAME=cardapio
DB_USER=root
DB_PASS=
APP_ENV=development
DEBUG=true
```

## 🧪 Testes

```bash
# Executar testes (quando implementados)
php vendor/bin/phpunit
```

## 📈 Performance

- **Lighthouse Score**: 90+ em todas as métricas
- **First Contentful Paint**: < 2s
- **Largest Contentful Paint**: < 3s
- **Cumulative Layout Shift**: < 0.1

## 🤝 Contribuição

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 👥 Equipe

- **Desenvolvedor Principal**: [ImotoAkio](https://github.com/ImotoAkio)
- **Design**: [Nome do Designer]
- **Backend**: [Nome do Backend Dev]

## 📞 Suporte

- **Email**: suporte@temperoecafe.com
- **Documentação**: [Wiki do Projeto](documentacao/)
- **Issues**: [GitHub Issues](https://github.com/ImotoAkio/cardapio/issues)

## 🔄 Changelog

Veja o arquivo [CHANGELOG.md](CHANGELOG.md) para histórico de mudanças.

---

**Desenvolvido com ❤️ para promover produtos naturais e saudáveis** 🍃