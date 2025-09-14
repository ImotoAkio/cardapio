<?php
// =====================================================
// 🍃 TEMPERO E CAFÉ - CATEGORIA DE PRODUTOS
// =====================================================

require_once 'includes/database.php';

// Verificar se o ID foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: home.php');
    exit;
}

$categoryId = (int)$_GET['id'];

try {
    $db = Database::getInstance()->getConnection();
    
    // Buscar a categoria
    $stmt = $db->prepare("SELECT * FROM categories WHERE id = ? AND is_active = 1");
    $stmt->execute([$categoryId]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$category) {
        header('Location: home.php');
        exit;
    }
    
    // Buscar produtos da categoria
    $stmt = $db->prepare("
        SELECT p.*, c.name as category_name, s.name as subcategory_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN subcategories s ON p.subcategory_id = s.id
        WHERE p.category_id = ? AND p.is_active = 1
        ORDER BY p.created_at DESC
    ");
    $stmt->execute([$categoryId]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Função específica para category.php na raiz
    function getProductImagesForCategoryPage($imagesJson) {
        if (empty($imagesJson)) {
            return ['dist/img/product/default.png'];
        }
        
        $images = json_decode($imagesJson, true);
        
        // Se json_decode falhou, pode ser que o JSON esteja duplamente encodado
        if (!is_array($images)) {
            if (is_string($images)) {
                $images = json_decode($images, true);
            }
        }
        
        if (!is_array($images) || empty($images)) {
            return ['dist/img/product/default.png'];
        }
        
        return array_map(function($image) {
            // Se já tem 'dist/' no caminho, não adiciona novamente
            if (strpos($image, 'dist/') === 0) {
                return $image;
            }
            // Se tem 'img/' no caminho, adiciona 'dist/'
            if (strpos($image, 'img/') === 0) {
                return 'dist/' . $image;
            }
            // Se é apenas o nome do arquivo, monta o caminho completo
            return 'dist/img/product/' . basename($image);
        }, $images);
    }
    
    function getCategoryImageForCategoryPage($category) {
        // Se a categoria tem imagem no banco, usa ela
        if (!empty($category['image'])) {
            // Se já tem 'dist/' no caminho, não adiciona novamente
            if (strpos($category['image'], 'dist/') === 0) {
                return $category['image'];
            }
            // Se tem 'img/' no caminho, adiciona 'dist/'
            if (strpos($category['image'], 'img/') === 0) {
                return 'dist/' . $category['image'];
            }
            // Se é apenas o nome do arquivo, monta o caminho completo
            return 'dist/img/category/' . basename($category['image']);
        }
        
        // Fallback para imagens padrão baseadas no ID
        $defaultImages = [
            1 => 'dist/img/core-img/woman-clothes.png',
            2 => 'dist/img/core-img/rowboat.png',
            3 => 'dist/img/core-img/shampoo.png',
            4 => 'dist/img/core-img/rowboat.png',
            5 => 'dist/img/core-img/tv-table.png',
            6 => 'dist/img/core-img/beach.png',
            7 => 'dist/img/core-img/baby-products.png',
            8 => 'dist/img/core-img/price-tag.png'
        ];
        
        return $defaultImages[$category['id']] ?? 'dist/img/core-img/woman-clothes.png';
    }
    
} catch (PDOException $e) {
    header('Location: home.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover, shrink-to-fit=no">
    <meta name="description" content="<?php echo htmlspecialchars($category['name']); ?> - Tempero e Café">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#d3a74e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title><?php echo htmlspecialchars($category['name']); ?> - Tempero e Café</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="16x16" href="dist/img/icons/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="dist/img/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="dist/img/icons/favicon-96x96.png">
    <link rel="shortcut icon" href="dist/img/icons/favicon.ico">
    <link rel="apple-touch-icon" sizes="57x57" href="dist/img/icons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="dist/img/icons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="dist/img/icons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="dist/img/icons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="dist/img/icons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="dist/img/icons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="dist/img/icons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="dist/img/icons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="dist/img/icons/apple-icon-180x180.png">
    <link rel="apple-touch-icon" href="dist/img/icons/apple-icon.png">
    <meta name="msapplication-TileColor" content="#d3a74e">
    <meta name="msapplication-TileImage" content="dist/img/icons/ms-icon-144x144.png">
    <meta name="msapplication-config" content="browserconfig.xml">

    
    <!-- Favicon -->
    
    
    
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="dist/css/tabler-icons.min.css">
    <link rel="stylesheet" href="dist/css/animate.css">
    <link rel="stylesheet" href="dist/css/owl.carousel.min.css">
    <link rel="stylesheet" href="dist/css/magnific-popup.css">
    <link rel="stylesheet" href="dist/css/nice-select.css">
    <link rel="stylesheet" href="dist/style.css">
    
    <!-- Web App Manifest -->
    <link rel="manifest" href="dist/manifest.json">
</head>
<body>
    <!-- Preloader-->
    <div class="preloader" id="preloader">
        <div class="spinner-grow text-secondary" role="status">
            <div class="sr-only"></div>
        </div>
    </div>
    
    <!-- Header Area -->
    <div class="header-area" id="headerArea">
        <div class="container h-100 d-flex align-items-center justify-content-between">
            <!-- Logo Wrapper -->
            <div class="logo-wrapper">
                <a href="home.php">
                    <img src="dist/img/core-img/logo_cafe.png" alt="Tempero e Café">
                </a>
            </div>
            <div class="navbar-logo-container d-flex align-items-center">
                <!-- Cart Icon -->
                <div class="cart-icon-wrap">
                    <a href="cart.html">
                        <i class="ti ti-basket-bolt"></i>
                        <span>0</span>
                    </a>
                </div>
                <!-- User Profile Icon -->
                <div class="user-profile-icon ms-2">
                    <a href="profile.html">
                        <img src="dist/img/bg-img/9.jpg" alt="">
                    </a>
                </div>
                <!-- Navbar Toggler -->
                <div class="suha-navbar-toggler ms-2" data-bs-toggle="offcanvas" data-bs-target="#suhaOffcanvas">
                    <div><span></span><span></span><span></span></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Offcanvas Menu -->
    <div class="offcanvas offcanvas-start suha-offcanvas-wrap" tabindex="-1" id="suhaOffcanvas">
        <button class="btn-close btn-close-white" type="button" data-bs-dismiss="offcanvas"></button>
        <div class="offcanvas-body">
            <!-- Sidenav Profile-->
            <div class="sidenav-profile">
                <div class="user-profile">
                    <img src="dist/img/bg-img/9.jpg" alt="">
                </div>
                <div class="user-info">
                    <h5 class="user-name mb-1 text-white">Cliente</h5>
                    <p class="available-balance text-white">Saldo Atual R$<span class="counter">0,00</span></p>
                </div>
            </div>
            <!-- Sidenav Nav-->
            <ul class="sidenav-nav ps-0">
                <li><a href="profile.html"><i class="ti ti-user"></i>Meu Perfil</a></li>
                <li><a href="notifications.html"><i class="ti ti-bell-ringing"></i>Notificações</a></li>
                <li><a href="shop-grid.html"><i class="ti ti-building-store"></i>Loja</a></li>
                <li><a href="pages.html"><i class="ti ti-notebook"></i>Todas as Páginas</a></li>
                <li><a href="settings.html"><i class="ti ti-adjustments-horizontal"></i>Configurações</a></li>
                <li><a href="intro.html"><i class="ti ti-logout"></i>Sair</a></li>
            </ul>
        </div>
    </div>
    
    <!-- Page Content -->
    <div class="page-content-wrapper">
        <div class="container">
            <!-- Breadcrumb -->
            <div class="breadcrumb-wrapper py-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="home.php">Início</a></li>
                        <li class="breadcrumb-item active"><?php echo htmlspecialchars($category['name']); ?></li>
                    </ol>
                </nav>
            </div>
            
            <!-- Category Header -->
            <div class="category-header mb-4">
                <div class="row align-items-center">
                    <div class="col-3">
                        <img src="<?php echo getCategoryImageForCategoryPage($category); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" class="img-fluid rounded">
                    </div>
                    <div class="col-9">
                        <h1 class="category-title mb-2"><?php echo htmlspecialchars($category['name']); ?></h1>
                        <p class="category-description mb-0"><?php echo htmlspecialchars($category['description'] ?? ''); ?></p>
                        <span class="badge bg-primary mt-2"><?php echo count($products); ?> produtos</span>
                    </div>
                </div>
            </div>
            
            <!-- Products Grid -->
            <?php if (count($products) > 0): ?>
                <div class="row g-3">
                    <?php foreach ($products as $product): ?>
                        <?php $images = getProductImagesForCategoryPage($product['images']); ?>
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="card product-card h-100">
                                <div class="card-body">
                                    <!-- Badge -->
                                    <?php if ($product['is_on_sale']): ?>
                                        <span class="badge rounded-pill badge-warning">Promoção</span>
                                    <?php endif; ?>
                                    
                                    <!-- Wishlist Button -->
                                    
                                    <!-- Thumbnail -->
                                    <a class="product-thumbnail d-block" href="product.php?id=<?php echo $product['id']; ?>">
                                        <img class="mb-2" src="<?php echo $images[0]; ?>" alt="<?php echo $product['name']; ?>">
                                    </a>
                                    
                                    <!-- Product Title -->
                                    <a class="product-title" href="product.php?id=<?php echo $product['id']; ?>"><?php echo $product['name']; ?></a>
                                    
                                    <!-- Product Price -->
                                    <div class="product-price">
                                        <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                                            <div class="price-container">
                                                <div class="current-price-wrapper">
                                                    <span class="current-price text-success fw-bold"><?php echo formatPrice($product['price']); ?></span>
                                                    <span class="discount-badge bg-danger text-white px-1 py-0 rounded ms-1" style="font-size: 0.7rem;">
                                                        <?php echo round((($product['original_price'] - $product['price']) / $product['original_price']) * 100); ?>% OFF
                                                    </span>
                                                </div>
                                                <div class="original-price-wrapper">
                                                    <span class="original-price text-muted text-decoration-line-through" style="font-size: 0.8rem;">
                                                        <?php echo formatPrice($product['original_price']); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="price-container">
                                                <span class="current-price text-primary fw-bold"><?php echo formatPrice($product['price']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Rating -->
                                    <div class="product-rating">
                                        <i class="ti ti-star-filled"></i>
                                        <i class="ti ti-star-filled"></i>
                                        <i class="ti ti-star-filled"></i>
                                        <i class="ti ti-star-filled"></i>
                                        <i class="ti ti-star-filled"></i>
                                    </div>
                                    
                                    <!-- Add to Cart -->
                                    <a class="btn btn-primary btn-sm w-100 mt-2" href="product.php?id=<?php echo $product['id']; ?>">
                                        <i class="ti ti-eye"></i> Ver Detalhes
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="ti ti-package" style="font-size: 4rem; color: #ccc;"></i>
                    <h3 class="mt-3">Nenhum produto encontrado</h3>
                    <p class="text-muted">Esta categoria ainda não possui produtos cadastrados.</p>
                    <a href="home.php" class="btn btn-primary">Voltar ao Início</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Footer Nav -->
    <div class="footer-nav-area" id="footerNav">
        <div class="suha-footer-nav">
            <ul class="h-100 d-flex align-items-center justify-content-between ps-0">
                <li><a href="home.php"><i class="ti ti-home"></i>Início</a></li>
                <li><a href="cart.html"><i class="ti ti-basket"></i>Carrinho</a></li>
                <li><a href="settings.html"><i class="ti ti-settings"></i>Configurações</a></li>
                <li><a href="pages.html"><i class="ti ti-heart"></i>Favoritos</a></li>
            </ul>
        </div>
    </div>
    
    <!-- All JavaScript Files -->
    <script src="dist/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/jquery.min.js"></script>
    <script src="dist/js/active.js"></script>
</body>
</html>
