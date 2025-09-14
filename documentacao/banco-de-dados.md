# 🗄️ Banco de Dados - Tempero e Café

## 📋 Visão Geral

O banco de dados do Tempero e Café foi projetado seguindo os princípios de normalização e otimização para e-commerce. Utiliza MySQL 8.0+ com charset UTF-8 para suporte completo a caracteres especiais e emojis.

## 🏗️ Estrutura Geral

### Características Principais
- **Engine**: InnoDB (transações ACID)
- **Charset**: utf8mb4 (suporte completo Unicode)
- **Collation**: utf8mb4_unicode_ci
- **Timezone**: UTC (padrão)
- **Backup**: Automático diário

## 📊 Diagrama de Relacionamentos

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   users     │    │ categories  │    │subcategories│
│             │    │             │    │             │
│ id (PK)     │    │ id (PK)     │◄───┤ id (PK)     │
│ username    │    │ name        │    │ category_id │
│ email       │    │ slug        │    │ name        │
│ password    │    │ description │    │ slug        │
│ full_name   │    │ image       │    │ description │
│ phone       │    │ is_active   │    │ is_active   │
│ avatar      │    │ sort_order  │    │ sort_order  │
│ is_active   │    │ created_at  │    │ created_at  │
│ created_at  │    │ updated_at  │    │ updated_at  │
└─────────────┘    └─────────────┘    └─────────────┘
       │                   │                   │
       │                   │                   │
       │                   ▼                   │
       │            ┌─────────────┐            │
       │            │  products   │            │
       │            │             │            │
       │            │ id (PK)     │◄───────────┘
       │            │ category_id │
       │            │ subcat_id   │
       │            │ name        │
       │            │ slug        │
       │            │ description │
       │            │ price       │
       │            │ images      │
       │            │ is_active   │
       │            │ created_at  │
       │            └─────────────┘
       │                   │
       │                   │
       ▼                   ▼
┌─────────────┐    ┌─────────────┐
│    cart     │    │order_items │
│             │    │             │
│ id (PK)     │    │ id (PK)     │
│ session_id  │    │ order_id    │
│ user_id     │    │ product_id  │
│ created_at  │    │ quantity    │
└─────────────┘    │ price       │
       │           │ total       │
       │           └─────────────┘
       │                   │
       ▼                   │
┌─────────────┐            │
│ cart_items  │            │
│             │            │
│ id (PK)     │            │
│ cart_id     │            │
│ product_id  │            │
│ quantity    │            │
│ price       │            │
└─────────────┘            │
                           │
                           ▼
                   ┌─────────────┐
                   │   orders    │
                   │             │
                   │ id (PK)     │
                   │ user_id     │
                   │ order_num   │
                   │ status      │
                   │ total       │
                   │ created_at  │
                   └─────────────┘
```

## 📋 Tabelas Detalhadas

### 1. 👤 **users** - Usuários do Sistema

```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    avatar VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    email_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_active (is_active)
);
```

**Campos:**
- `id` - Chave primária auto-incremento
- `username` - Nome de usuário único
- `email` - Email único para login
- `password` - Senha hasheada (password_hash)
- `full_name` - Nome completo do usuário
- `phone` - Telefone de contato
- `avatar` - Caminho para imagem do avatar
- `is_active` - Status ativo/inativo
- `email_verified` - Email verificado
- `created_at` - Data de criação
- `updated_at` - Data de atualização

### 2. 📍 **addresses** - Endereços dos Usuários

```sql
CREATE TABLE addresses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type ENUM('shipping', 'billing') NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    street VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    zip_code VARCHAR(20) NOT NULL,
    country VARCHAR(100) DEFAULT 'Brasil',
    phone VARCHAR(20),
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_type (user_id, type),
    INDEX idx_default (is_default)
);
```

### 3. 🏷️ **categories** - Categorias de Produtos

```sql
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    image VARCHAR(255),
    icon VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_slug (slug),
    INDEX idx_active_sort (is_active, sort_order)
);
```

**Categorias Padrão:**
```sql
INSERT INTO categories (name, slug, description, icon) VALUES
('Temperos Naturais', 'temperos-naturais', 'Especiarias e temperos orgânicos', 'ti-pepper'),
('Grãos e Cereais', 'graos-cereais', 'Grãos integrais e cereais naturais', 'ti-seeding'),
('Produtos Orgânicos', 'produtos-organicos', 'Alimentos orgânicos certificados', 'ti-leaf'),
('Chás e Infusões', 'chas-infusoes', 'Chás medicinais e infusões', 'ti-cup'),
('Óleos Essenciais', 'oleos-essenciais', 'Óleos essenciais puros', 'ti-droplet'),
('Suplementos', 'suplementos', 'Suplementos naturais e vitamínicos', 'ti-pill'),
('Produtos para Bebês', 'produtos-bebes', 'Produtos naturais para bebês', 'ti-baby'),
('Promoções', 'promocoes', 'Ofertas especiais', 'ti-tag');
```

### 4. 🏷️ **subcategories** - Subcategorias

```sql
CREATE TABLE subcategories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    image VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_category (category_id),
    INDEX idx_slug (slug),
    INDEX idx_active_sort (is_active, sort_order)
);
```

### 5. 🛍️ **products** - Produtos

```sql
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    subcategory_id INT,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    description TEXT,
    short_description VARCHAR(500),
    benefits TEXT,
    price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2),
    weight VARCHAR(50),
    dimensions VARCHAR(100),
    sku VARCHAR(100) UNIQUE,
    stock_quantity INT DEFAULT 0,
    min_stock INT DEFAULT 5,
    is_active BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    is_on_sale BOOLEAN DEFAULT FALSE,
    is_new BOOLEAN DEFAULT FALSE,
    rating DECIMAL(3,2) DEFAULT 0.00,
    total_reviews INT DEFAULT 0,
    images JSON,
    specifications JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (subcategory_id) REFERENCES subcategories(id),
    INDEX idx_category (category_id),
    INDEX idx_subcategory (subcategory_id),
    INDEX idx_slug (slug),
    INDEX idx_sku (sku),
    INDEX idx_featured (is_featured),
    INDEX idx_sale (is_on_sale),
    INDEX idx_active (is_active),
    INDEX idx_price (price),
    INDEX idx_rating (rating)
);
```

**Campos Especiais:**
- `images` - JSON com array de URLs das imagens
- `specifications` - JSON com especificações técnicas
- `benefits` - Texto com benefícios do produto
- `rating` - Média de avaliações (0.00 a 5.00)

### 6. 🛒 **cart** - Carrinhos de Compra

```sql
CREATE TABLE cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    session_id VARCHAR(128) NOT NULL,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_session (session_id),
    INDEX idx_user (user_id)
);
```

### 7. 🛒 **cart_items** - Itens do Carrinho

```sql
CREATE TABLE cart_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (cart_id) REFERENCES cart(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_product (cart_id, product_id),
    INDEX idx_cart (cart_id),
    INDEX idx_product (product_id)
);
```

### 8. ❤️ **wishlist** - Lista de Favoritos

```sql
CREATE TABLE wishlist (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id),
    INDEX idx_user (user_id),
    INDEX idx_product (product_id)
);
```

### 9. 🎫 **coupons** - Cupons de Desconto

```sql
CREATE TABLE coupons (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    type ENUM('percentage', 'fixed') NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    min_order_amount DECIMAL(10,2) DEFAULT 0,
    max_discount DECIMAL(10,2),
    usage_limit INT,
    used_count INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    starts_at TIMESTAMP,
    expires_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_code (code),
    INDEX idx_active (is_active),
    INDEX idx_dates (starts_at, expires_at)
);
```

### 10. 📦 **orders** - Pedidos

```sql
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    subtotal DECIMAL(10,2) NOT NULL,
    discount DECIMAL(10,2) DEFAULT 0,
    shipping_cost DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) NOT NULL,
    coupon_id INT,
    shipping_address JSON NOT NULL,
    billing_address JSON NOT NULL,
    payment_method VARCHAR(100),
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (coupon_id) REFERENCES coupons(id),
    INDEX idx_user (user_id),
    INDEX idx_order_number (order_number),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status),
    INDEX idx_created_at (created_at)
);
```

### 11. 📦 **order_items** - Itens dos Pedidos

```sql
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    product_price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    INDEX idx_order (order_id),
    INDEX idx_product (product_id)
);
```

### 12. 📊 **order_status_history** - Histórico de Status

```sql
CREATE TABLE order_status_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled') NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_order (order_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);
```

### 13. 🔔 **notifications** - Notificações

```sql
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('order', 'promotion', 'system', 'product') NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    data JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_type (type),
    INDEX idx_read (is_read),
    INDEX idx_created_at (created_at)
);
```

### 14. ⭐ **reviews** - Avaliações de Produtos

```sql
CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    order_id INT,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    title VARCHAR(200),
    comment TEXT,
    is_verified BOOLEAN DEFAULT FALSE,
    is_approved BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    INDEX idx_user (user_id),
    INDEX idx_product (product_id),
    INDEX idx_order (order_id),
    INDEX idx_rating (rating),
    INDEX idx_approved (is_approved)
);
```

## 🔧 Índices para Performance

### Índices Primários
```sql
-- Produtos
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_featured ON products(is_featured);
CREATE INDEX idx_products_active ON products(is_active);
CREATE INDEX idx_products_price ON products(price);
CREATE INDEX idx_products_rating ON products(rating);

-- Pedidos
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_created ON orders(created_at);

-- Carrinho
CREATE INDEX idx_cart_session ON cart(session_id);
CREATE INDEX idx_cart_user ON cart(user_id);

-- Favoritos
CREATE INDEX idx_wishlist_user ON wishlist(user_id);

-- Avaliações
CREATE INDEX idx_reviews_product ON reviews(product_id);
CREATE INDEX idx_reviews_user ON reviews(user_id);
```

### Índices Compostos
```sql
-- Produtos ativos por categoria
CREATE INDEX idx_products_active_category ON products(is_active, category_id);

-- Pedidos por usuário e status
CREATE INDEX idx_orders_user_status ON orders(user_id, status);

-- Avaliações por produto e aprovação
CREATE INDEX idx_reviews_product_approved ON reviews(product_id, is_approved);
```

## 📊 Views Úteis

### 1. **Produtos com Informações Completas**
```sql
CREATE VIEW v_products_complete AS
SELECT 
    p.*,
    c.name as category_name,
    c.slug as category_slug,
    s.name as subcategory_name,
    s.slug as subcategory_slug,
    COALESCE(AVG(r.rating), 0) as avg_rating,
    COUNT(r.id) as review_count
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
LEFT JOIN subcategories s ON p.subcategory_id = s.id
LEFT JOIN reviews r ON p.id = r.product_id AND r.is_approved = 1
GROUP BY p.id;
```

### 2. **Pedidos com Informações do Cliente**
```sql
CREATE VIEW v_orders_complete AS
SELECT 
    o.*,
    u.full_name as customer_name,
    u.email as customer_email,
    u.phone as customer_phone,
    COUNT(oi.id) as item_count
FROM orders o
JOIN users u ON o.user_id = u.id
LEFT JOIN order_items oi ON o.id = oi.order_id
GROUP BY o.id;
```

### 3. **Estatísticas de Vendas**
```sql
CREATE VIEW v_sales_stats AS
SELECT 
    DATE(o.created_at) as sale_date,
    COUNT(o.id) as total_orders,
    SUM(o.total) as total_revenue,
    AVG(o.total) as avg_order_value
FROM orders o
WHERE o.status = 'delivered'
GROUP BY DATE(o.created_at);
```

## 🔄 Triggers e Procedures

### 1. **Atualizar Estoque ao Confirmar Pedido**
```sql
DELIMITER //
CREATE TRIGGER tr_update_stock_after_order
AFTER INSERT ON order_items
FOR EACH ROW
BEGIN
    UPDATE products 
    SET stock_quantity = stock_quantity - NEW.quantity
    WHERE id = NEW.product_id;
END //
DELIMITER ;
```

### 2. **Gerar Número do Pedido**
```sql
DELIMITER //
CREATE PROCEDURE sp_generate_order_number(OUT order_number VARCHAR(50))
BEGIN
    DECLARE counter INT DEFAULT 1;
    DECLARE new_number VARCHAR(50);
    
    REPEAT
        SET new_number = CONCAT('TC', DATE_FORMAT(NOW(), '%Y%m%d'), LPAD(counter, 4, '0'));
        SET counter = counter + 1;
    UNTIL NOT EXISTS (SELECT 1 FROM orders WHERE order_number = new_number) END REPEAT;
    
    SET order_number = new_number;
END //
DELIMITER ;
```

## 📈 Otimizações de Performance

### 1. **Particionamento por Data**
```sql
-- Particionar tabela de pedidos por mês
ALTER TABLE orders PARTITION BY RANGE (YEAR(created_at) * 100 + MONTH(created_at)) (
    PARTITION p202401 VALUES LESS THAN (202402),
    PARTITION p202402 VALUES LESS THAN (202403),
    -- ... outras partições
    PARTITION p_future VALUES LESS THAN MAXVALUE
);
```

### 2. **Cache de Consultas Frequentes**
```sql
-- Criar tabela de cache para produtos em destaque
CREATE TABLE cache_featured_products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_data JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    
    INDEX idx_expires (expires_at)
);
```

## 🔒 Segurança do Banco

### 1. **Usuários e Permissões**
```sql
-- Criar usuário específico para aplicação
CREATE USER 'tempero_app'@'localhost' IDENTIFIED BY 'senha_segura';
GRANT SELECT, INSERT, UPDATE, DELETE ON cardapio.* TO 'tempero_app'@'localhost';

-- Usuário para backup
CREATE USER 'tempero_backup'@'localhost' IDENTIFIED BY 'senha_backup';
GRANT SELECT, LOCK TABLES ON cardapio.* TO 'tempero_backup'@'localhost';
```

### 2. **Auditoria de Mudanças**
```sql
CREATE TABLE audit_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    table_name VARCHAR(50) NOT NULL,
    record_id INT NOT NULL,
    action ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    old_values JSON,
    new_values JSON,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_table_record (table_name, record_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
);
```

## 📊 Monitoramento e Manutenção

### 1. **Consultas de Monitoramento**
```sql
-- Verificar tabelas com mais registros
SELECT 
    table_name,
    table_rows,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
FROM information_schema.tables 
WHERE table_schema = 'cardapio'
ORDER BY table_rows DESC;

-- Verificar índices não utilizados
SELECT 
    object_schema,
    object_name,
    index_name,
    count_read,
    count_write
FROM performance_schema.table_io_waits_summary_by_index_usage
WHERE object_schema = 'cardapio'
ORDER BY count_read DESC;
```

### 2. **Limpeza Automática**
```sql
-- Event para limpeza de logs antigos
CREATE EVENT ev_cleanup_old_logs
ON SCHEDULE EVERY 1 WEEK
DO
DELETE FROM audit_log WHERE created_at < DATE_SUB(NOW(), INTERVAL 3 MONTH);
```

---

Esta estrutura de banco de dados garante performance, escalabilidade e integridade dos dados para o sistema Tempero e Café.
