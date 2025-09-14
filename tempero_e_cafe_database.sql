-- =====================================================
-- 🍃 TEMPERO E CAFÉ - BANCO DE DADOS
-- =====================================================
-- Estrutura completa do banco de dados para o cardápio digital
-- Especializado em produtos naturais e orgânicos
-- =====================================================

-- Configurações do banco
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- =====================================================
-- 🗄️ CRIAÇÃO DO BANCO DE DADOS
-- =====================================================
CREATE DATABASE IF NOT EXISTS `cardapio` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `cardapio`;

-- =====================================================
-- 👤 TABELA: USUÁRIOS
-- =====================================================
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `email_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 📍 TABELA: ENDEREÇOS
-- =====================================================
CREATE TABLE `addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` enum('shipping','billing') NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `street` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `zip_code` varchar(20) NOT NULL,
  `country` varchar(100) DEFAULT 'Brasil',
  `phone` varchar(20) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 🏷️ TABELA: CATEGORIAS
-- =====================================================
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 🏷️ TABELA: SUBCATEGORIAS
-- =====================================================
CREATE TABLE `subcategories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `subcategories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 🛍️ TABELA: PRODUTOS
-- =====================================================
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `subcategory_id` int(11) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `short_description` varchar(500) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `original_price` decimal(10,2) DEFAULT NULL,
  `weight` varchar(50) DEFAULT NULL,
  `dimensions` varchar(100) DEFAULT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `min_stock` int(11) DEFAULT 5,
  `is_active` tinyint(1) DEFAULT 1,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_on_sale` tinyint(1) DEFAULT 0,
  `is_new` tinyint(1) DEFAULT 0,
  `rating` decimal(3,2) DEFAULT 0.00,
  `total_reviews` int(11) DEFAULT 0,
  `images` json DEFAULT NULL,
  `specifications` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `sku` (`sku`),
  KEY `category_id` (`category_id`),
  KEY `subcategory_id` (`subcategory_id`),
  KEY `is_featured` (`is_featured`),
  KEY `is_active` (`is_active`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 🛒 TABELA: CARRINHO
-- =====================================================
CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_product` (`user_id`,`product_id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ❤️ TABELA: FAVORITOS
-- =====================================================
CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_product` (`user_id`,`product_id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 🎫 TABELA: CUPONS
-- =====================================================
CREATE TABLE `coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('percentage','fixed') NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) DEFAULT 0,
  `max_discount` decimal(10,2) DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `starts_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 📦 TABELA: PEDIDOS
-- =====================================================
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `status` enum('pending','confirmed','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `subtotal` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) DEFAULT 0,
  `shipping_cost` decimal(10,2) DEFAULT 0,
  `total` decimal(10,2) NOT NULL,
  `coupon_id` int(11) DEFAULT NULL,
  `shipping_address` json NOT NULL,
  `billing_address` json NOT NULL,
  `payment_method` varchar(100) DEFAULT NULL,
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `user_id` (`user_id`),
  KEY `coupon_id` (`coupon_id`),
  KEY `status` (`status`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 📦 TABELA: ITENS DO PEDIDO
-- =====================================================
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 📊 TABELA: STATUS DO PEDIDO
-- =====================================================
CREATE TABLE `order_status_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `status` enum('pending','confirmed','processing','shipped','delivered','cancelled') NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_status_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 🔔 TABELA: NOTIFICAÇÕES
-- =====================================================
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `type` enum('order','promotion','system','product') NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `data` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ⭐ TABELA: AVALIAÇÕES
-- =====================================================
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 AND `rating` <= 5),
  `title` varchar(200) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `is_approved` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 🔧 ÍNDICES PARA PERFORMANCE
-- =====================================================
CREATE INDEX `idx_products_category` ON `products` (`category_id`);
CREATE INDEX `idx_products_featured` ON `products` (`is_featured`);
CREATE INDEX `idx_products_active` ON `products` (`is_active`);
CREATE INDEX `idx_orders_user` ON `orders` (`user_id`);
CREATE INDEX `idx_orders_status` ON `orders` (`status`);
CREATE INDEX `idx_cart_user` ON `cart_items` (`user_id`);
CREATE INDEX `idx_wishlist_user` ON `wishlist` (`user_id`);
CREATE INDEX `idx_reviews_product` ON `reviews` (`product_id`);

-- =====================================================
-- 📊 DADOS INICIAIS - CATEGORIAS
-- =====================================================
INSERT INTO `categories` (`name`, `slug`, `description`, `icon`) VALUES
('Temperos Naturais', 'temperos-naturais', 'Especiarias e temperos orgânicos', 'ti-pepper'),
('Grãos e Cereais', 'graos-cereais', 'Grãos integrais e cereais naturais', 'ti-seeding'),
('Produtos Orgânicos', 'produtos-organicos', 'Alimentos orgânicos certificados', 'ti-leaf'),
('Chás e Infusões', 'chas-infusoes', 'Chás medicinais e infusões', 'ti-cup'),
('Óleos Essenciais', 'oleos-essenciais', 'Óleos essenciais puros', 'ti-droplet'),
('Suplementos', 'suplementos', 'Suplementos naturais e vitamínicos', 'ti-pill'),
('Produtos para Bebês', 'produtos-bebes', 'Produtos naturais para bebês', 'ti-baby'),
('Promoções', 'promocoes', 'Ofertas especiais', 'ti-tag');

-- =====================================================
-- 📊 DADOS INICIAIS - SUBCATEGORIAS
-- =====================================================
INSERT INTO `subcategories` (`category_id`, `name`, `slug`, `description`) VALUES
(1, 'Temperos Secos', 'temperos-secos', 'Temperos desidratados e moídos'),
(1, 'Temperos Frescos', 'temperos-frescos', 'Temperos frescos e desidratados'),
(2, 'Grãos Integrais', 'graos-integrais', 'Grãos com casca e integrais'),
(2, 'Cereais Matinais', 'cereais-matinais', 'Cereais para café da manhã'),
(3, 'Frutas Orgânicas', 'frutas-organicas', 'Frutas certificadas orgânicas'),
(3, 'Verduras Orgânicas', 'verduras-organicas', 'Verduras certificadas orgânicas'),
(4, 'Chás Medicinais', 'chas-medicinais', 'Chás com propriedades medicinais'),
(4, 'Infusões', 'infusoes', 'Infusões de ervas e flores'),
(5, 'Óleos Aromáticos', 'oleos-aromaticos', 'Óleos para aromaterapia'),
(5, 'Óleos Terapêuticos', 'oleos-terapeuticos', 'Óleos para uso terapêutico'),
(6, 'Vitaminas', 'vitaminas', 'Suplementos vitamínicos'),
(6, 'Minerais', 'minerais', 'Suplementos minerais'),
(7, 'Alimentação', 'alimentacao-bebes', 'Alimentos para bebês'),
(7, 'Cuidados', 'cuidados-bebes', 'Produtos de cuidado para bebês');

-- =====================================================
-- 📊 DADOS INICIAIS - PRODUTOS
-- =====================================================
INSERT INTO `products` (`category_id`, `subcategory_id`, `name`, `slug`, `description`, `short_description`, `price`, `original_price`, `weight`, `images`, `is_featured`, `is_on_sale`, `is_new`) VALUES
(1, 1, 'Açafrão da Terra', 'acafrao-da-terra', 'Açafrão da terra orgânico, rico em curcumina e propriedades anti-inflamatórias', 'Açafrão da terra orgânico', 12.90, 18.90, '100g', '["img/product/1.png"]', 1, 1, 1),
(4, 7, 'Chá Verde Premium', 'cha-verde-premium', 'Chá verde orgânico de alta qualidade, rico em antioxidantes', 'Chá verde orgânico premium', 8.50, 12.00, '50g', '["img/product/2.png"]', 1, 1, 1),
(2, 3, 'Quinoa Orgânica', 'quinoa-organica', 'Quinoa orgânica rica em proteínas e aminoácidos essenciais', 'Quinoa orgânica rica em proteínas', 15.90, 22.50, '500g', '["img/product/3.png"]', 1, 1, 1),
(1, 1, 'Pimenta Rosa', 'pimenta-rosa', 'Pimenta rosa orgânica, sabor suave e aromático', 'Pimenta rosa orgânica', 9.90, 14.90, '50g', '["img/product/4.png"]', 1, 1, 0),
(4, 7, 'Chá de Camomila', 'cha-camomila', 'Chá de camomila orgânico, ideal para relaxamento', 'Chá de camomila orgânico', 6.90, 9.90, '30g', '["img/product/5.png"]', 1, 1, 0),
(6, 11, 'Maca Peruana', 'maca-peruana', 'Maca peruana em pó, superalimento energético', 'Maca peruana em pó', 18.90, 25.90, '200g', '["img/product/6.png"]', 1, 1, 0),
(5, 9, 'Óleo de Lavanda', 'oleo-lavanda', 'Óleo essencial de lavanda puro, relaxante', 'Óleo essencial de lavanda', 24.90, 32.90, '10ml', '["img/product/7.png"]', 1, 1, 0),
(6, 11, 'Whey Protein', 'whey-protein', 'Whey protein orgânico, rico em proteínas', 'Whey protein orgânico', 45.90, 59.90, '500g', '["img/product/8.png"]', 1, 1, 0),
(2, 3, 'Chia Orgânica', 'chia-organica', 'Sementes de chia orgânicas, ricas em ômega-3', 'Sementes de chia orgânicas', 12.90, 18.90, '200g', '["img/product/9.png"]', 1, 1, 0),
(6, 11, 'Goji Berry', 'goji-berry', 'Goji berry desidratado, superalimento antioxidante', 'Goji berry desidratado', 16.90, 22.90, '150g', '["img/product/10.png"]', 1, 1, 0);

-- =====================================================
-- 📊 DADOS INICIAIS - CUPONS
-- =====================================================
INSERT INTO `coupons` (`code`, `name`, `description`, `type`, `value`, `min_order_amount`, `max_discount`, `usage_limit`, `is_active`, `starts_at`, `expires_at`) VALUES
('TEMPERO20', 'Desconto Tempero e Café', '20% de desconto em produtos orgânicos', 'percentage', 20.00, 50.00, 100.00, 100, 1, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY)),
('FRETE10', 'Frete Grátis', 'Frete grátis para pedidos acima de R$ 100', 'fixed', 10.00, 100.00, 10.00, 50, 1, NOW(), DATE_ADD(NOW(), INTERVAL 15 DAY)),
('NOVO15', 'Cliente Novo', '15% de desconto para novos clientes', 'percentage', 15.00, 30.00, 50.00, 200, 1, NOW(), DATE_ADD(NOW(), INTERVAL 60 DAY));

-- =====================================================
-- 📊 DADOS INICIAIS - USUÁRIO ADMIN
-- =====================================================
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `phone`, `is_active`, `email_verified`) VALUES
('admin', 'admin@temperoecafe.com.br', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador Tempero e Café', '(11) 99999-9999', 1, 1);

-- =====================================================
-- 📊 DADOS INICIAIS - ENDEREÇO PADRÃO
-- =====================================================
INSERT INTO `addresses` (`user_id`, `type`, `full_name`, `street`, `city`, `state`, `zip_code`, `country`, `phone`, `is_default`) VALUES
(1, 'shipping', 'Administrador Tempero e Café', 'Rua das Flores, 123', 'São Paulo', 'SP', '01234-567', 'Brasil', '(11) 99999-9999', 1),
(1, 'billing', 'Administrador Tempero e Café', 'Rua das Flores, 123', 'São Paulo', 'SP', '01234-567', 'Brasil', '(11) 99999-9999', 1);

-- =====================================================
-- 🔧 CONFIGURAÇÕES FINAIS
-- =====================================================
COMMIT;

-- =====================================================
-- ✅ BANCO DE DADOS CRIADO COM SUCESSO!
-- =====================================================
-- 
-- 🍃 Tempero e Café - Cardápio Digital
-- 
-- Estrutura completa criada com:
-- ✅ 13 tabelas principais
-- ✅ Relacionamentos com integridade
-- ✅ Índices para performance
-- ✅ Dados iniciais populados
-- ✅ Configurações otimizadas
-- 
-- Pronto para integração com PHP!
-- =====================================================
