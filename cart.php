    <?php
// =====================================================
// 🛒 TEMPERO E CAFÉ - CARRINHO DE COMPRAS
// =====================================================

require_once 'includes/database.php';
require_once 'admin/config.php';

// Iniciar sessão se não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Obter ID da sessão
$sessionId = session_id();

// Verificar se usuário está logado
$isLoggedIn = isset($_SESSION['user_id']);
$userId = $_SESSION['user_id'] ?? null;

// Buscar dados do usuário se estiver logado
$user = null;
if ($isLoggedIn) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Criar instância do CartManager
$cartManager = new CartManager(Database::getInstance()->getConnection());

// Obter itens do carrinho
$cartItems = $cartManager->getCartItems($sessionId);
$cartTotal = $cartManager->getCartTotal($sessionId);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover, shrink-to-fit=no">
    <meta name="description" content="Carrinho de Compras - Tempero e Café">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#d3a74e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>Carrinho de Compras - Tempero e Café</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="16x16" href="dist/img/icons/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="dist/img/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="dist/img/icons/favicon-96x96.png">
    <link rel="shortcut icon" href="dist/img/icons/favicon.ico">
    <!-- Apple Touch Icon -->
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
    <!-- Microsoft Tiles -->
    <meta name="msapplication-TileColor" content="#d3a74e">
    <meta name="msapplication-TileImage" content="dist/img/icons/ms-icon-144x144.png">
    <meta name="msapplication-config" content="browserconfig.xml">
    
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
    
    <!-- Header Area-->
    <div class="header-area" id="headerArea">
        <div class="container h-100 d-flex align-items-center justify-content-between">
            <!-- Back Button-->
            <div class="back-button me-2">
                <a href="home.php">
                    <i class="ti ti-arrow-left"></i>
                </a>
            </div>
            <!-- Page Title-->
            <div class="page-heading">
                <h6 class="mb-0">Carrinho de Compras</h6>
            </div>
            <!-- Navbar Toggler-->
            <div class="suha-navbar-toggler ms-2" data-bs-toggle="offcanvas" data-bs-target="#suhaOffcanvas">
                <div><span></span><span></span><span></span></div>
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
                    <?php if ($isLoggedIn): ?>
                        <img src="dist/img/bg-img/user/<?php echo htmlspecialchars($user['avatar'] ?? '1.png'); ?>" alt="Avatar" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 3px solid #fff;">
                    <?php else: ?>
                        <img src="dist/img/bg-img/9.jpg" alt="">
                    <?php endif; ?>
                </div>
                <div class="user-info">
                    <?php if ($isLoggedIn): ?>
                        <h5 class="user-name mb-1 text-white"><?php echo htmlspecialchars($user['full_name']); ?></h5>
                        <p class="available-balance text-white">Saldo Atual R$<span class="counter">0,00</span></p>
                    <?php else: ?>
                        <h5 class="user-name mb-1 text-white">Visitante</h5>
                        <p class="available-balance text-white">Faça login para continuar</p>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Sidenav Nav-->
            <ul class="sidenav-nav ps-0">
                <?php if ($isLoggedIn): ?>
                    <li><a href="profile.php"><i class="ti ti-user"></i>Meu Perfil</a></li>
                    <li><a href="notifications.php"><i class="ti ti-bell-ringing"></i>Notificações</a></li>
                    <li><a href="my-orders.php"><i class="ti ti-package"></i>Meus Pedidos</a></li>
                <?php else: ?>
                    <li><a href="login.php"><i class="ti ti-login"></i>Fazer Login</a></li>
                    <li><a href="cadastro.php"><i class="ti ti-user-plus"></i>Criar Conta</a></li>
                <?php endif; ?>
                <li><a href="shop.php"><i class="ti ti-building-store"></i>Loja</a></li>
                <li><a href="pages.php"><i class="ti ti-notebook"></i>Todas as Páginas</a></li>
                <li><a href="settings.php"><i class="ti ti-adjustments-horizontal"></i>Configurações</a></li>
                <?php if ($isLoggedIn): ?>
                    <li><a href="logout.php"><i class="ti ti-logout"></i>Sair</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    
    <div class="page-content-wrapper">
        <div class="container">
            <!-- Cart Items -->
            <div class="cart-items-section py-3">
                <div id="cart-items">
                    <?php if (empty($cartItems)): ?>
                        <div class="text-center py-5">
                            <i class="ti ti-basket" style="font-size: 3rem; color: #ccc;"></i>
                            <h5 class="mt-3">Seu carrinho está vazio</h5>
                            <p class="text-muted">Adicione alguns produtos para começar suas compras!</p>
                            <a href="home.php" class="btn btn-primary">Continuar Comprando</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($cartItems as $item): ?>
                            <?php
                            // Obter imagem do produto
                            $images = json_decode($item['images'], true);
                            if (!is_array($images)) {
                                if (is_string($images)) {
                                    $images = json_decode($images, true);
                                }
                            }
                            $productImage = (!is_array($images) || empty($images)) ? 'dist/img/product/default.png' : 'dist/' . $images[0];
                            $itemTotal = $item['price'] * $item['quantity'];
                            ?>
                            <div class="cart-item d-flex align-items-center mb-3 p-3 bg-white rounded" data-product-id="<?php echo $item['product_id']; ?>">
                                <div class="cart-item-image me-3">
                                    <img src="<?php echo $productImage; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                </div>
                                <div class="cart-item-details flex-grow-1">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                    <p class="text-muted mb-1"><?php echo formatPrice($item['price']); ?></p>
                                    <div class="quantity-controls d-flex align-items-center">
                                        <button class="btn btn-sm btn-outline-secondary" onclick="decreaseQuantity(<?php echo $item['product_id']; ?>)">-</button>
                                        <input type="number" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock_quantity']; ?>" class="form-control form-control-sm mx-2" style="width: 60px;" onchange="updateQuantityDirect(<?php echo $item['product_id']; ?>, this.value)">
                                        <button class="btn btn-sm btn-outline-secondary" onclick="increaseQuantity(<?php echo $item['product_id']; ?>)">+</button>
                                    </div>
                                </div>
                                <div class="cart-item-total text-end">
                                    <h6 class="mb-1"><?php echo formatPrice($itemTotal); ?></h6>
                                    <button class="btn btn-sm btn-outline-danger" onclick="cartManager.removeFromCart(<?php echo $item['product_id']; ?>)">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Cart Summary -->
            <?php if (!empty($cartItems)): ?>
                <div class="cart-summary bg-white p-3 rounded mb-3" id="cart-total">
                    <h6 class="mb-3">Resumo do Pedido</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span><?php echo formatPrice($cartTotal['total']); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Frete:</span>
                        <span>R$ 0,00</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong><?php echo formatPrice($cartTotal['total']); ?></strong>
                    </div>
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" onclick="proceedToCheckout()">
                            <i class="ti ti-credit-card"></i> 
                            <?php if ($isLoggedIn): ?>
                                Finalizar Compra
                            <?php else: ?>
                                Fazer Login e Finalizar Compra
                            <?php endif; ?>
                        </button>
                        <button class="btn btn-outline-secondary" onclick="cartManager.clearCart()">
                            <i class="ti ti-trash"></i> Limpar Carrinho
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Footer Nav-->
    <div class="footer-nav-area" id="footerNav">
        <div class="suha-footer-nav">
            <ul class="h-100 d-flex align-items-center justify-content-between ps-0">
                <li><a href="home.php"><i class="ti ti-home"></i>Início</a></li>
                <li><a href="cart.php" class="active"><i class="ti ti-basket"></i>Carrinho</a></li>
                <li><a href="settings.php"><i class="ti ti-settings"></i>Configurações</a></li>
                <li><a href="profile.php"><i class="ti ti-user"></i>Perfil</a></li>
            </ul>
        </div>
    </div>
    
    <!-- All JavaScript Files -->
    <script src="dist/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/jquery.min.js"></script>
    <script src="dist/js/active.js"></script>
    <script src="js/cart.js"></script>
    
    <script>
        function proceedToCheckout() {
            <?php if ($isLoggedIn): ?>
                // Usuário logado - ir direto para checkout
                window.location.href = 'checkout.php';
            <?php else: ?>
                // Usuário não logado - ir para cadastro
                window.location.href = 'cadastro.php';
            <?php endif; ?>
        }
    </script>
</body>
</html>
