<?php
// =====================================================
// 🍃 TEMPERO E CAFÉ - PAINEL ADMINISTRATIVO
// =====================================================
// Sistema de gerenciamento para o cardápio digital
// =====================================================

// Incluir configurações do banco de dados
require_once __DIR__ . '/../includes/database.php';

// Configurações específicas do admin
define('ADMIN_SESSION', 'tempero_admin_session');

// Configurações de segurança
define('PASSWORD_MIN_LENGTH', 6);
define('SESSION_TIMEOUT', 3600); // 1 hora

// Configurações de upload
define('UPLOAD_PATH', '../dist/img/product/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);

// =====================================================
// 🔐 CLASSE DE AUTENTICAÇÃO
// =====================================================
class Auth {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->startSession();
    }
    
    private function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
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
    
    public function logout() {
        unset($_SESSION[ADMIN_SESSION]);
        session_destroy();
    }
    
    public function isLoggedIn() {
        if (!isset($_SESSION[ADMIN_SESSION])) {
            return false;
        }
        
        // Verificar timeout da sessão
        if (time() - $_SESSION[ADMIN_SESSION]['login_time'] > SESSION_TIMEOUT) {
            $this->logout();
            return false;
        }
        
        return true;
    }
    
    public function getUser() {
        return $_SESSION[ADMIN_SESSION] ?? null;
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }
}

// =====================================================
// 📊 CLASSE DE ESTATÍSTICAS
// =====================================================
class Statistics {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getDashboardStats() {
        $stats = [];
        
        // Total de produtos
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM products WHERE is_active = 1");
        $stats['total_products'] = $stmt->fetch()['total'];
        
        // Total de categorias
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM categories WHERE is_active = 1");
        $stats['total_categories'] = $stmt->fetch()['total'];
        
        // Total de usuários
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM users WHERE is_active = 1");
        $stats['total_users'] = $stmt->fetch()['total'];
        
        // Total de pedidos
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM orders");
        $stats['total_orders'] = $stmt->fetch()['total'];
        
        // Pedidos pendentes
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
        $stats['pending_orders'] = $stmt->fetch()['total'];
        
        // Receita total
        $stmt = $this->db->query("SELECT SUM(total) as total FROM orders WHERE status = 'delivered'");
        $stats['total_revenue'] = $stmt->fetch()['total'] ?? 0;
        
        // Produtos em estoque baixo
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM products WHERE stock_quantity <= min_stock");
        $stats['low_stock'] = $stmt->fetch()['total'];
        
        return $stats;
    }
    
    public function getRecentOrders($limit = 10) {
        $stmt = $this->db->prepare("
            SELECT o.*, u.full_name, u.email 
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            ORDER BY o.created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    public function getTopProducts($limit = 5) {
        $stmt = $this->db->prepare("
            SELECT p.name, p.price, SUM(oi.quantity) as total_sold
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

// =====================================================
// 🛍️ CLASSE DE PRODUTOS
// =====================================================
class ProductManager {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAllProducts($page = 1, $limit = 20) {
        $offset = ($page - 1) * $limit;
        
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name, s.name as subcategory_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN subcategories s ON p.subcategory_id = s.id
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }
    
    public function getProductById($id) {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name, s.name as subcategory_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN subcategories s ON p.subcategory_id = s.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function createProduct($data) {
        $stmt = $this->db->prepare("
            INSERT INTO products (
                category_id, subcategory_id, name, slug, description, 
                short_description, benefits, price, original_price, weight, 
                dimensions, sku, stock_quantity, min_stock, 
                is_active, is_featured, is_on_sale, is_new, images
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['category_id'],
            $data['subcategory_id'],
            $data['name'],
            $data['slug'],
            $data['description'],
            $data['short_description'],
            $data['benefits'] ?? '',
            $data['price'],
            $data['original_price'],
            $data['weight'],
            $data['dimensions'],
            $data['sku'],
            $data['stock_quantity'],
            $data['min_stock'],
            $data['is_active'] ?? 1,
            $data['is_featured'] ?? 0,
            $data['is_on_sale'] ?? 0,
            $data['is_new'] ?? 0,
            json_encode($data['images'] ?? [])
        ]);
    }
    
    public function updateProduct($id, $data) {
        // Gerar SKU único se estiver vazio
        if (empty($data['sku'])) {
            $data['sku'] = 'SKU-' . strtoupper(substr(md5($data['name'] . time()), 0, 8));
        }
        
        $stmt = $this->db->prepare("
            UPDATE products SET
                category_id = ?, subcategory_id = ?, name = ?, slug = ?, 
                description = ?, short_description = ?, benefits = ?, price = ?, 
                original_price = ?, weight = ?, dimensions = ?, sku = ?, 
                stock_quantity = ?, min_stock = ?, is_active = ?, 
                is_featured = ?, is_on_sale = ?, is_new = ?, images = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['category_id'],
            $data['subcategory_id'],
            $data['name'],
            $data['slug'],
            $data['description'],
            $data['short_description'],
            $data['benefits'] ?? '',
            $data['price'],
            $data['original_price'],
            $data['weight'],
            $data['dimensions'],
            $data['sku'],
            $data['stock_quantity'],
            $data['min_stock'],
            $data['is_active'] ?? 1,
            $data['is_featured'] ?? 0,
            $data['is_on_sale'] ?? 0,
            $data['is_new'] ?? 0,
            json_encode($data['images'] ?? []),
            $id
        ]);
    }
    
    public function deleteProduct($id) {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

// =====================================================
// 🏷️ CLASSE DE CATEGORIAS
// =====================================================
class CategoryManager {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAllCategories() {
        $stmt = $this->db->query("
            SELECT c.*, COUNT(p.id) as product_count
            FROM categories c
            LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
            GROUP BY c.id
            ORDER BY c.sort_order, c.name
        ");
        return $stmt->fetchAll();
    }
    
    public function getCategoryById($id) {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function createCategory($data) {
        $stmt = $this->db->prepare("
            INSERT INTO categories (name, slug, description, image, sort_order, is_active)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['name'],
            $data['slug'],
            $data['description'],
            $data['image'],
            $data['sort_order'] ?? 0,
            $data['is_active'] ?? 1
        ]);
    }
    
    public function updateCategory($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE categories SET
                name = ?, slug = ?, description = ?, image = ?, 
                sort_order = ?, is_active = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['name'],
            $data['slug'],
            $data['description'],
            $data['image'],
            $data['sort_order'] ?? 0,
            $data['is_active'] ?? 1,
            $id
        ]);
    }
    
    public function deleteCategory($id) {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

// =====================================================
// 🏷️ CLASSE DE SUBCATEGORIAS
// =====================================================
class SubcategoryManager {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAllSubcategories() {
        $stmt = $this->db->query("
            SELECT s.*, c.name as category_name
            FROM subcategories s
            LEFT JOIN categories c ON s.category_id = c.id
            ORDER BY s.sort_order, s.name
        ");
        return $stmt->fetchAll();
    }
    
    public function getSubcategoryById($id) {
        $stmt = $this->db->prepare("SELECT * FROM subcategories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function createSubcategory($data) {
        $stmt = $this->db->prepare("
            INSERT INTO subcategories (category_id, name, slug, description, image, sort_order, is_active)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['category_id'],
            $data['name'],
            $data['slug'],
            $data['description'],
            $data['image'],
            $data['sort_order'] ?? 0,
            $data['is_active'] ?? 1
        ]);
    }
    
    public function updateSubcategory($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE subcategories SET
                category_id = ?, name = ?, slug = ?, description = ?, image = ?, 
                sort_order = ?, is_active = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['category_id'],
            $data['name'],
            $data['slug'],
            $data['description'],
            $data['image'],
            $data['sort_order'] ?? 0,
            $data['is_active'] ?? 1,
            $id
        ]);
    }
    
    public function deleteSubcategory($id) {
        $stmt = $this->db->prepare("DELETE FROM subcategories WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

// 📦 CLASSE DE PEDIDOS
// =====================================================
class OrderManager {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAllOrders($page = 1, $limit = 20) {
        $offset = ($page - 1) * $limit;
        
        $stmt = $this->db->prepare("
            SELECT o.*, u.full_name, u.email, u.phone
            FROM orders o
            JOIN users u ON o.user_id = u.id
            ORDER BY o.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }
    
    public function getOrderById($id) {
        $stmt = $this->db->prepare("
            SELECT o.*, u.full_name, u.email, u.phone
            FROM orders o
            JOIN users u ON o.user_id = u.id
            WHERE o.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getOrderItems($orderId) {
        $stmt = $this->db->prepare("
            SELECT oi.*, p.name as product_name, p.images
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
    
    public function updateOrderStatus($id, $status) {
        $stmt = $this->db->prepare("
            UPDATE orders SET status = ? WHERE id = ?
        ");
        
        if ($stmt->execute([$status, $id])) {
            // Adicionar ao histórico
            $this->addStatusHistory($id, $status);
            return true;
        }
        return false;
    }
    
    private function addStatusHistory($orderId, $status) {
        $stmt = $this->db->prepare("
            INSERT INTO order_status_history (order_id, status, description)
            VALUES (?, ?, ?)
        ");
        
        $descriptions = [
            'pending' => 'Pedido aguardando confirmação',
            'confirmed' => 'Pedido confirmado',
            'processing' => 'Pedido sendo processado',
            'shipped' => 'Pedido enviado',
            'delivered' => 'Pedido entregue',
            'cancelled' => 'Pedido cancelado'
        ];
        
        $stmt->execute([$orderId, $status, $descriptions[$status] ?? '']);
    }
}

// =====================================================
// 🔧 FUNÇÕES AUXILIARES
// =====================================================
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function generateSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}


function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function showAlert($message, $type = 'info') {
    $_SESSION['alert'] = [
        'message' => $message,
        'type' => $type
    ];
}

function getAlert() {
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        unset($_SESSION['alert']);
        return $alert;
    }
    return null;
}

// =====================================================
// 🚀 INICIALIZAÇÃO
// =====================================================
$auth = new Auth();
$stats = new Statistics();
$productManager = new ProductManager();
$categoryManager = new CategoryManager();
$subcategoryManager = new SubcategoryManager();
$orderManager = new OrderManager();
