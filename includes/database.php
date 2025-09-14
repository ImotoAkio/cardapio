<?php
// =====================================================
// 🍃 TEMPERO E CAFÉ - CONFIGURAÇÃO FRONTEND
// =====================================================
// Sistema de conexão com banco para o frontend
// =====================================================

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'cardapio');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configurações da aplicação
define('APP_NAME', 'Tempero e Café');
define('APP_VERSION', '1.0.0');

// =====================================================
// 🔧 CLASSE DE CONEXÃO COM BANCO
// =====================================================
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            die("Erro de conexão: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
}

// =====================================================
// 🏷️ CLASSE DE CATEGORIAS
// =====================================================
class CategoryService {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAllCategories() {
        $stmt = $this->db->query("
            SELECT c.*, COUNT(p.id) as product_count
            FROM categories c
            LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
            WHERE c.is_active = 1
            GROUP BY c.id
            ORDER BY c.sort_order, c.name
            LIMIT 8
        ");
        return $stmt->fetchAll();
    }
}

// =====================================================
// 🛍️ CLASSE DE PRODUTOS
// =====================================================
class ProductService {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getFeaturedProducts($limit = 6) {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.is_active = 1 AND p.is_featured = 1
            ORDER BY p.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    public function getOnSaleProducts($limit = 6) {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.is_active = 1 AND p.is_on_sale = 1
            ORDER BY p.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    public function getTopProducts($limit = 6) {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name,
                   SUM(oi.quantity) as total_sold
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN order_items oi ON p.id = oi.product_id
            LEFT JOIN orders o ON oi.order_id = o.id AND o.status = 'delivered'
            WHERE p.is_active = 1
            GROUP BY p.id
            ORDER BY total_sold DESC, p.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    public function getWeeklyProducts($limit = 4) {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name,
                   AVG(r.rating) as avg_rating,
                   COUNT(r.id) as review_count
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN reviews r ON p.id = r.product_id
            WHERE p.is_active = 1
            GROUP BY p.id
            ORDER BY avg_rating DESC, review_count DESC, p.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}

// =====================================================
// 🎫 CLASSE DE CUPONS
// =====================================================
class CouponService {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getActiveCoupons() {
        $stmt = $this->db->query("
            SELECT * FROM coupons 
            WHERE is_active = 1 
            AND (starts_at IS NULL OR starts_at <= NOW())
            AND (expires_at IS NULL OR expires_at >= NOW())
            ORDER BY created_at DESC
            LIMIT 1
        ");
        return $stmt->fetchAll();
    }
}

// =====================================================
// 🔧 FUNÇÕES AUXILIARES
// =====================================================
function formatPrice($price) {
    return 'R$ ' . number_format($price, 2, ',', '.');
}

function getBasePath() {
    // Detecta o caminho base baseado na estrutura do projeto
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
    $currentDir = __DIR__;
    
    // Se o projeto está em uma subpasta (ex: /cardapio/)
    if (strpos($scriptName, '/cardapio/') !== false) {
        return '/cardapio/'; // Projeto está em /cardapio/
    }
    
    // Detectar se estamos em uma subpasta baseado no diretório atual
    if (strpos($currentDir, 'cardapio') !== false) {
        return '/cardapio/'; // Projeto está em /cardapio/
    }
    
    // Se estamos na raiz do projeto (home.php está na raiz)
    if (basename($scriptName) === 'home.php' || basename($scriptName) === 'index.php') {
        return ''; // Estamos na raiz do projeto
    }
    
    // Se estamos em uma subpasta (ex: admin/, pages/, etc.)
    $dirName = dirname($scriptName);
    if ($dirName !== '/' && $dirName !== '\\' && $dirName !== '.') {
        return '../'; // Estamos em uma subpasta
    }
    
    // Padrão: estamos na raiz
    return '';
}

// Função para obter a primeira imagem de um produto
function getFirstProductImage($imagesJson) {
    $basePath = getBasePath();
    
    if (empty($imagesJson)) {
        return $basePath . 'dist/img/product/default.png';
    }
    
    $images = json_decode($imagesJson, true);
    
    // Se json_decode falhou, pode ser que o JSON esteja duplamente encodado
    if (!is_array($images)) {
        // Tentar decodificar novamente se for uma string
        if (is_string($images)) {
            $images = json_decode($images, true);
        }
    }
    
    // Verificar se json_decode falhou ou retornou algo inválido
    if (!is_array($images) || empty($images)) {
        return $basePath . 'dist/img/product/default.png';
    }
    
    $firstImage = $images[0];
    
    // Se já tem 'dist/' no caminho, não adiciona novamente
    if (strpos($firstImage, 'dist/') === 0) {
        return $basePath . $firstImage;
    }
    
    return $basePath . 'dist/' . $firstImage;
}

function getProductImages($imagesJson) {
    $basePath = getBasePath();
    
    if (empty($imagesJson)) {
        return [$basePath . 'dist/img/product/default.png'];
    }
    
    $images = json_decode($imagesJson, true);
    
    // Se json_decode falhou, pode ser que o JSON esteja duplamente encodado
    if (!is_array($images)) {
        // Tentar decodificar novamente se for uma string
        if (is_string($images)) {
            $images = json_decode($images, true);
        }
    }
    
    // Verificar se json_decode falhou ou retornou algo inválido
    if (!is_array($images) || empty($images)) {
        return [$basePath . 'dist/img/product/default.png'];
    }
    
    return array_map(function($image) use ($basePath) {
        // Se já tem 'dist/' no caminho, não adiciona novamente
        if (strpos($image, 'dist/') === 0) {
            return $basePath . $image;
        }
        // Se tem 'img/' no caminho, adiciona 'dist/'
        if (strpos($image, 'img/') === 0) {
            return $basePath . 'dist/' . $image;
        }
        // Se é apenas o nome do arquivo, monta o caminho completo
        return $basePath . 'dist/img/product/' . basename($image);
    }, $images);
}

function getCategoryImage($category) {
    $basePath = getBasePath();
    
    // Se a categoria tem imagem no banco, usa ela
    if (!empty($category['image'])) {
        // Se já tem 'dist/' no caminho, não adiciona novamente
        if (strpos($category['image'], 'dist/') === 0) {
            return $basePath . $category['image'];
        }
        // Se tem 'img/' no caminho, adiciona 'dist/'
        if (strpos($category['image'], 'img/') === 0) {
            return $basePath . 'dist/' . $category['image'];
        }
        // Se é apenas o nome do arquivo, monta o caminho completo
        return $basePath . 'dist/img/category/' . basename($category['image']);
    }
    
    // Fallback para imagens padrão baseadas no ID
    $defaultImages = [
        1 => 'dist/img/core-img/woman-clothes.png',
        2 => 'dist/img/core-img/grocery.png', 
        3 => 'dist/img/core-img/shampoo.png',
        4 => 'dist/img/core-img/rowboat.png',
        5 => 'dist/img/core-img/tv-table.png',
        6 => 'dist/img/core-img/beach.png',
        7 => 'dist/img/core-img/baby-products.png',
        8 => 'dist/img/core-img/price-tag.png'
    ];
    
    $categoryId = $category['id'] ?? 1;
    return $basePath . ($defaultImages[$categoryId] ?? 'dist/img/core-img/price-tag.png');
}

// =====================================================
// 🚀 INICIALIZAÇÃO DOS SERVIÇOS
// =====================================================
$categoryService = new CategoryService();
$productService = new ProductService();
$couponService = new CouponService();

// Obter dados do banco
$categories = $categoryService->getAllCategories();
$featuredProducts = $productService->getFeaturedProducts(6);
$onSaleProducts = $productService->getOnSaleProducts(6);
$topProducts = $productService->getTopProducts(6);
$weeklyProducts = $productService->getWeeklyProducts(4);
$activeCoupons = $couponService->getActiveCoupons();

// Obter cupom principal (primeiro ativo)
$mainCoupon = !empty($activeCoupons) ? $activeCoupons[0] : null;

// =====================================================
// 🛒 CLASSE: CART MANAGER
// =====================================================
class CartManager {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    // Obter ou criar carrinho para a sessão
    public function getOrCreateCart($sessionId, $userId = null) {
        $stmt = $this->db->prepare("SELECT * FROM cart WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$cart) {
            $stmt = $this->db->prepare("INSERT INTO cart (session_id, user_id) VALUES (?, ?)");
            $stmt->execute([$sessionId, $userId]);
            $cartId = $this->db->lastInsertId();
            
            $stmt = $this->db->prepare("SELECT * FROM cart WHERE id = ?");
            $stmt->execute([$cartId]);
            $cart = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return $cart;
    }
    
    // Associar carrinho a um usuário
    public function associateCartToUser($sessionId, $userId) {
        $stmt = $this->db->prepare("UPDATE cart SET user_id = ? WHERE session_id = ?");
        return $stmt->execute([$userId, $sessionId]);
    }
    
    // Adicionar produto ao carrinho
    public function addToCart($sessionId, $productId, $quantity = 1, $userId = null) {
        try {
            $this->db->beginTransaction();
            
            // Obter ou criar carrinho
            $cart = $this->getOrCreateCart($sessionId, $userId);
            $cartId = $cart['id'];
            
            // Verificar se o produto já está no carrinho
            $stmt = $this->db->prepare("SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ?");
            $stmt->execute([$cartId, $productId]);
            $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Obter informações do produto
            $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ? AND is_active = 1");
            $stmt->execute([$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$product) {
                throw new Exception("Produto não encontrado ou inativo");
            }
            
            if ($existingItem) {
                // Atualizar quantidade
                $newQuantity = $existingItem['quantity'] + $quantity;
                $stmt = $this->db->prepare("UPDATE cart_items SET quantity = ?, price = ? WHERE id = ?");
                $stmt->execute([$newQuantity, $product['price'], $existingItem['id']]);
            } else {
                // Adicionar novo item
                $stmt = $this->db->prepare("INSERT INTO cart_items (cart_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmt->execute([$cartId, $productId, $quantity, $product['price']]);
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    // Remover produto do carrinho
    public function removeFromCart($sessionId, $productId) {
        $cart = $this->getOrCreateCart($sessionId);
        $cartId = $cart['id'];
        
        $stmt = $this->db->prepare("DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?");
        return $stmt->execute([$cartId, $productId]);
    }
    
    // Atualizar quantidade do produto
    public function updateQuantity($sessionId, $productId, $quantity) {
        $cart = $this->getOrCreateCart($sessionId);
        $cartId = $cart['id'];
        
        if ($quantity <= 0) {
            return $this->removeFromCart($sessionId, $productId);
        }
        
        $stmt = $this->db->prepare("UPDATE cart_items SET quantity = ? WHERE cart_id = ? AND product_id = ?");
        return $stmt->execute([$quantity, $cartId, $productId]);
    }
    
    // Obter itens do carrinho
    public function getCartItems($sessionId) {
        $cart = $this->getOrCreateCart($sessionId);
        $cartId = $cart['id'];
        
        $stmt = $this->db->prepare("
            SELECT ci.*, p.name, p.slug, p.images, p.stock_quantity
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            WHERE ci.cart_id = ?
            ORDER BY ci.created_at DESC
        ");
        $stmt->execute([$cartId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obter total do carrinho
    public function getCartTotal($sessionId) {
        $items = $this->getCartItems($sessionId);
        $total = 0;
        $itemCount = 0;
        
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
            $itemCount += $item['quantity'];
        }
        
        return [
            'total' => $total,
            'item_count' => $itemCount,
            'items' => $items
        ];
    }
    
    // Limpar carrinho
    public function clearCart($sessionId) {
        $cart = $this->getOrCreateCart($sessionId);
        $cartId = $cart['id'];
        
        $stmt = $this->db->prepare("DELETE FROM cart_items WHERE cart_id = ?");
        return $stmt->execute([$cartId]);
    }
    
    // Obter contagem de itens
    public function getCartItemCount($sessionId) {
        $cart = $this->getOrCreateCart($sessionId);
        $cartId = $cart['id'];
        
        $stmt = $this->db->prepare("SELECT SUM(quantity) as total FROM cart_items WHERE cart_id = ?");
        $stmt->execute([$cartId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'] ?? 0;
    }
}
?>
