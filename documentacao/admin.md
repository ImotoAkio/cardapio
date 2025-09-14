# 👨‍💼 Painel Administrativo - Tempero e Café

## 📋 Visão Geral

O painel administrativo do Tempero e Café é uma interface web completa para gerenciar todos os aspectos do e-commerce. Desenvolvido com foco em usabilidade e eficiência, oferece controle total sobre produtos, pedidos, usuários e configurações.

## 🎯 Características Principais

- **Dashboard Intuitivo**: Visão geral com estatísticas em tempo real
- **Gestão Completa**: Produtos, categorias, pedidos e usuários
- **Interface Responsiva**: Funciona em desktop e mobile
- **Segurança Robusta**: Autenticação e autorização
- **Relatórios**: Análises de vendas e performance

## 🏗️ Estrutura do Admin

```
📁 admin/
├── 📄 config.php              # Configurações e classes
├── 📄 login.php               # Página de login
├── 📄 dashboard.php           # Dashboard principal
├── 📄 products.php            # Gestão de produtos
├── 📄 categories.php          # Gestão de categorias
├── 📄 orders.php              # Gestão de pedidos
├── 📄 users.php               # Gestão de usuários
├── 📄 coupons.php             # Gestão de cupons
├── 📄 settings.php            # Configurações gerais
├── 📄 profile.php             # Perfil do admin
└── 📄 upload_image.php        # Upload de imagens
```

## 🔐 Sistema de Autenticação

### Classe Auth

```php
class Auth {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->startSession();
    }
    
    public function login($username, $password) {
        $stmt = $this->db->prepare("
            SELECT id, username, email, password, full_name, is_active 
            FROM users 
            WHERE (username = ? OR email = ?) AND is_active = 1
        ");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION[ADMIN_SESSION] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'full_name' => $user['full_name'],
                'login_time' => time()
            ];
            return true;
        }
        return false;
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }
}
```

### Página de Login

```html
<!-- login.php -->
<div class="login-container">
    <div class="login-form">
        <h2>Painel Administrativo</h2>
        <form method="POST" action="login_process.php">
            <div class="form-group">
                <label>Usuário ou Email</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Entrar</button>
        </form>
    </div>
</div>
```

## 📊 Dashboard Principal

### Estatísticas em Tempo Real

```php
class Statistics {
    public function getDashboardStats() {
        $stats = [];
        
        // Total de produtos
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM products WHERE is_active = 1");
        $stats['total_products'] = $stmt->fetch()['total'];
        
        // Total de pedidos
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM orders");
        $stats['total_orders'] = $stmt->fetch()['total'];
        
        // Receita total
        $stmt = $this->db->query("SELECT SUM(total) as total FROM orders WHERE status = 'delivered'");
        $stats['total_revenue'] = $stmt->fetch()['total'] ?? 0;
        
        return $stats;
    }
}
```

### Interface do Dashboard

```html
<!-- dashboard.php -->
<div class="dashboard-container">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="ti ti-package"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $stats['total_products']; ?></h3>
                <p>Produtos Ativos</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="ti ti-shopping-cart"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $stats['total_orders']; ?></h3>
                <p>Total de Pedidos</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="ti ti-currency-dollar"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo formatPrice($stats['total_revenue']); ?></h3>
                <p>Receita Total</p>
            </div>
        </div>
    </div>
    
    <!-- Gráficos e tabelas -->
    <div class="dashboard-content">
        <div class="recent-orders">
            <h4>Pedidos Recentes</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $order): ?>
                    <tr>
                        <td><?php echo $order['id']; ?></td>
                        <td><?php echo $order['full_name']; ?></td>
                        <td><?php echo formatPrice($order['total']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo getStatusColor($order['status']); ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </td>
                        <td><?php echo formatDate($order['created_at']); ?></td>
                        <td>
                            <a href="orders.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">
                                Ver Detalhes
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
```

## 🛍️ Gestão de Produtos

### CRUD de Produtos

```php
class ProductManager {
    public function createProduct($data) {
        $stmt = $this->db->prepare("
            INSERT INTO products (
                category_id, subcategory_id, name, slug, description, 
                price, original_price, weight, stock_quantity, 
                is_active, is_featured, is_on_sale, images
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['category_id'],
            $data['subcategory_id'],
            $data['name'],
            $data['slug'],
            $data['description'],
            $data['price'],
            $data['original_price'],
            $data['weight'],
            $data['stock_quantity'],
            $data['is_active'] ?? 1,
            $data['is_featured'] ?? 0,
            $data['is_on_sale'] ?? 0,
            json_encode($data['images'] ?? [])
        ]);
    }
    
    public function updateProduct($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE products SET
                category_id = ?, subcategory_id = ?, name = ?, slug = ?, 
                description = ?, price = ?, original_price = ?, 
                weight = ?, stock_quantity = ?, is_active = ?, 
                is_featured = ?, is_on_sale = ?, images = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['category_id'],
            $data['subcategory_id'],
            $data['name'],
            $data['slug'],
            $data['description'],
            $data['price'],
            $data['original_price'],
            $data['weight'],
            $data['stock_quantity'],
            $data['is_active'] ?? 1,
            $data['is_featured'] ?? 0,
            $data['is_on_sale'] ?? 0,
            json_encode($data['images'] ?? []),
            $id
        ]);
    }
}
```

### Interface de Gestão

```html
<!-- products.php -->
<div class="products-container">
    <div class="page-header">
        <h2>Gestão de Produtos</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
            <i class="ti ti-plus"></i> Novo Produto
        </button>
    </div>
    
    <!-- Filtros -->
    <div class="filters">
        <select class="form-select" id="categoryFilter">
            <option value="">Todas as Categorias</option>
            <?php foreach ($categories as $category): ?>
            <option value="<?php echo $category['id']; ?>">
                <?php echo $category['name']; ?>
            </option>
            <?php endforeach; ?>
        </select>
        
        <input type="text" class="form-control" placeholder="Buscar produto..." id="searchInput">
    </div>
    
    <!-- Tabela de Produtos -->
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imagem</th>
                    <th>Nome</th>
                    <th>Categoria</th>
                    <th>Preço</th>
                    <th>Estoque</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo $product['id']; ?></td>
                    <td>
                        <img src="<?php echo getFirstProductImage($product['images']); ?>" 
                             alt="<?php echo $product['name']; ?>" 
                             class="product-thumb">
                    </td>
                    <td><?php echo $product['name']; ?></td>
                    <td><?php echo $product['category_name']; ?></td>
                    <td><?php echo formatPrice($product['price']); ?></td>
                    <td>
                        <span class="badge <?php echo $product['stock_quantity'] <= $product['min_stock'] ? 'badge-danger' : 'badge-success'; ?>">
                            <?php echo $product['stock_quantity']; ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge <?php echo $product['is_active'] ? 'badge-success' : 'badge-secondary'; ?>">
                            <?php echo $product['is_active'] ? 'Ativo' : 'Inativo'; ?>
                        </span>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-primary" onclick="editProduct(<?php echo $product['id']; ?>)">
                                <i class="ti ti-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteProduct(<?php echo $product['id']; ?>)">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
```

## 📦 Gestão de Pedidos

### Controle de Status

```php
class OrderManager {
    public function updateOrderStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        
        if ($stmt->execute([$status, $id])) {
            $this->addStatusHistory($id, $status);
            return true;
        }
        return false;
    }
    
    private function addStatusHistory($orderId, $status) {
        $descriptions = [
            'pending' => 'Pedido aguardando confirmação',
            'confirmed' => 'Pedido confirmado',
            'processing' => 'Pedido sendo processado',
            'shipped' => 'Pedido enviado',
            'delivered' => 'Pedido entregue',
            'cancelled' => 'Pedido cancelado'
        ];
        
        $stmt = $this->db->prepare("
            INSERT INTO order_status_history (order_id, status, description)
            VALUES (?, ?, ?)
        ");
        
        $stmt->execute([$orderId, $status, $descriptions[$status] ?? '']);
    }
}
```

### Interface de Pedidos

```html
<!-- orders.php -->
<div class="orders-container">
    <div class="page-header">
        <h2>Gestão de Pedidos</h2>
        <div class="status-filters">
            <button class="btn btn-outline-primary active" data-status="all">Todos</button>
            <button class="btn btn-outline-warning" data-status="pending">Pendentes</button>
            <button class="btn btn-outline-info" data-status="processing">Processando</button>
            <button class="btn btn-outline-success" data-status="delivered">Entregues</button>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Pedido</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td>
                        <strong>#<?php echo $order['order_number']; ?></strong>
                    </td>
                    <td>
                        <div>
                            <strong><?php echo $order['full_name']; ?></strong><br>
                            <small><?php echo $order['email']; ?></small>
                        </div>
                    </td>
                    <td><?php echo formatPrice($order['total']); ?></td>
                    <td>
                        <select class="form-select status-select" data-order-id="<?php echo $order['id']; ?>">
                            <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pendente</option>
                            <option value="confirmed" <?php echo $order['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmado</option>
                            <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processando</option>
                            <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Enviado</option>
                            <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Entregue</option>
                            <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelado</option>
                        </select>
                    </td>
                    <td><?php echo formatDate($order['created_at']); ?></td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="viewOrder(<?php echo $order['id']; ?>)">
                            <i class="ti ti-eye"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
```

## 🎫 Gestão de Cupons

### Sistema de Cupons

```php
class CouponManager {
    public function createCoupon($data) {
        $stmt = $this->db->prepare("
            INSERT INTO coupons (
                code, name, description, type, value, 
                min_order_amount, max_discount, usage_limit, 
                is_active, starts_at, expires_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['code'],
            $data['name'],
            $data['description'],
            $data['type'],
            $data['value'],
            $data['min_order_amount'],
            $data['max_discount'],
            $data['usage_limit'],
            $data['is_active'] ?? 1,
            $data['starts_at'],
            $data['expires_at']
        ]);
    }
    
    public function validateCoupon($code, $orderAmount) {
        $stmt = $this->db->prepare("
            SELECT * FROM coupons 
            WHERE code = ? AND is_active = 1 
            AND (starts_at IS NULL OR starts_at <= NOW())
            AND (expires_at IS NULL OR expires_at >= NOW())
            AND (usage_limit IS NULL OR used_count < usage_limit)
            AND min_order_amount <= ?
        ");
        
        $stmt->execute([$code, $orderAmount]);
        return $stmt->fetch();
    }
}
```

## 📊 Relatórios e Analytics

### Relatório de Vendas

```php
class Reports {
    public function getSalesReport($startDate, $endDate) {
        $stmt = $this->db->prepare("
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as total_orders,
                SUM(total) as total_revenue,
                AVG(total) as avg_order_value
            FROM orders 
            WHERE created_at BETWEEN ? AND ?
            AND status = 'delivered'
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll();
    }
    
    public function getTopProducts($limit = 10) {
        $stmt = $this->db->prepare("
            SELECT 
                p.name,
                p.price,
                SUM(oi.quantity) as total_sold,
                SUM(oi.total) as total_revenue
            FROM products p
            JOIN order_items oi ON p.id = oi.product_id
            JOIN orders o ON oi.order_id = o.id
            WHERE o.status = 'delivered'
            GROUP BY p.id
            ORDER BY total_sold DESC
            LIMIT ?
        ");
        
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
```

## 🔧 Configurações Gerais

### Painel de Configurações

```html
<!-- settings.php -->
<div class="settings-container">
    <div class="settings-tabs">
        <button class="tab-btn active" data-tab="general">Geral</button>
        <button class="tab-btn" data-tab="email">Email</button>
        <button class="tab-btn" data-tab="payment">Pagamento</button>
        <button class="tab-btn" data-tab="shipping">Entrega</button>
    </div>
    
    <div class="settings-content">
        <div class="tab-content active" id="general">
            <form method="POST" action="save_settings.php">
                <div class="form-group">
                    <label>Nome da Loja</label>
                    <input type="text" name="store_name" value="<?php echo $settings['store_name']; ?>" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Email de Contato</label>
                    <input type="email" name="contact_email" value="<?php echo $settings['contact_email']; ?>" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Telefone</label>
                    <input type="text" name="phone" value="<?php echo $settings['phone']; ?>" class="form-control">
                </div>
                
                <button type="submit" class="btn btn-primary">Salvar Configurações</button>
            </form>
        </div>
    </div>
</div>
```

## 🛡️ Segurança do Admin

### Middleware de Segurança

```php
// Verificação de permissões
function checkAdminPermission($requiredPermission) {
    $user = $_SESSION[ADMIN_SESSION];
    
    // Verificar se usuário tem permissão
    $stmt = $db->prepare("
        SELECT * FROM user_permissions 
        WHERE user_id = ? AND permission = ?
    ");
    $stmt->execute([$user['id'], $requiredPermission]);
    
    if ($stmt->rowCount() === 0) {
        header('HTTP/1.0 403 Forbidden');
        die('Acesso negado');
    }
}

// Rate limiting
function checkRateLimit($action, $limit = 60) {
    $key = $_SERVER['REMOTE_ADDR'] . '_' . $action;
    $current = $_SESSION['rate_limit'][$key] ?? 0;
    
    if ($current >= $limit) {
        header('HTTP/1.0 429 Too Many Requests');
        die('Rate limit exceeded');
    }
    
    $_SESSION['rate_limit'][$key] = $current + 1;
}
```

## 📱 Interface Responsiva

### CSS para Mobile

```css
@media (max-width: 768px) {
    .dashboard-container {
        padding: 1rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn-group {
        flex-direction: column;
    }
    
    .settings-tabs {
        flex-direction: column;
    }
}
```

---

Esta documentação do painel administrativo fornece uma visão completa das funcionalidades de gestão do Tempero e Café.
