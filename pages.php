<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Incluir arquivo de configuração do banco de dados
require_once 'includes/database.php';

// Buscar dados do usuário
$user_id = $_SESSION['user_id'];
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: login.php');
    exit();
}

// Buscar contagem do carrinho
$stmt = $db->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
$stmt->execute([$user_id]);
$cartCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$isLoggedIn = true;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover, shrink-to-fit=no">
    <meta name="description" content="Tempero e Café - Páginas">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#d3a74e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <!-- Title -->
    <title>Páginas - Tempero e Café</title>
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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <!-- Favicon -->
    
    <!-- Apple Touch Icon -->
    
    
    
    
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="dist/css/tabler-icons.min.css">
    <link rel="stylesheet" href="dist/css/animate.css">
    <link rel="stylesheet" href="dist/css/owl.carousel.min.css">
    <link rel="stylesheet" href="dist/css/magnific-popup.css">
    <link rel="stylesheet" href="dist/css/nice-select.css">
    <!-- Stylesheet -->
    <link rel="stylesheet" href="dist/style.css">
    <link rel="stylesheet" href="dist/css/avatar-styles.css">
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
        <div class="container h-100 d-flex align-items-center justify-content-between d-flex rtl-flex-d-row-r">
            <!-- Logo Wrapper -->
            <div class="logo-wrapper"><a href="home.php"><img src="dist/img/core-img/logo_cafe.png" alt="Tempero e Café"></a></div>
            <div class="navbar-logo-container d-flex align-items-center">
                <!-- Cart Icon -->
                <div class="cart-icon-wrap"><a href="cart.php"><i class="ti ti-basket-bolt"></i><span class="cart-count"><?php echo $cartCount; ?></span></a></div>
                <!-- User Profile Icon -->
                <div class="user-profile-icon ms-2"><a href="profile.php"><img src="dist/img/bg-img/user/<?php echo htmlspecialchars($user['avatar'] ?? '1.png'); ?>" alt="Avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #fff;"></a></div>
                <!-- Navbar Toggler -->
                <div class="suha-navbar-toggler ms-2" data-bs-toggle="offcanvas" data-bs-target="#suhaOffcanvas" aria-controls="suhaOffcanvas">
                    <div><span></span><span></span><span></span></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Offcanvas Menu -->
    <div class="offcanvas offcanvas-start suha-offcanvas-wrap" tabindex="-1" id="suhaOffcanvas" aria-labelledby="suhaOffcanvasLabel">
        <!-- Close button-->
        <button class="btn-close btn-close-white" type="button" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        <!-- Offcanvas body-->
        <div class="offcanvas-body">
            <!-- Sidenav Profile-->
            <div class="sidenav-profile">
                <div class="user-profile"><img src="dist/img/bg-img/user/<?php echo htmlspecialchars($user['avatar'] ?? '1.png'); ?>" alt="Avatar" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 3px solid #fff;"></div>
                <div class="user-info">
                    <h5 class="user-name mb-1 text-white"><?php echo htmlspecialchars($user['full_name']); ?></h5>
                    <p class="available-balance text-white">Saldo Atual R$<span class="counter">0,00</span></p>
                </div>
            </div>
            <!-- Sidenav Nav-->
            <ul class="sidenav-nav ps-0">
                <li><a href="profile.php"><i class="ti ti-user"></i>Meu Perfil</a></li>
                <li><a href="notifications.php"><i class="ti ti-bell-ringing"></i>Notificações</a></li>
                <li><a href="home.php"><i class="ti ti-home"></i>Início</a></li>
                <li><a href="cart.php"><i class="ti ti-basket"></i>Carrinho</a></li>
                <li><a href="settings.php"><i class="ti ti-adjustments-horizontal"></i>Configurações</a></li>
                <li><a href="logout.php"><i class="ti ti-logout"></i>Sair</a></li>
            </ul>
        </div>
    </div>
    
    <!-- Page Content -->
    <div class="page-content-wrapper">
        <div class="container">
            <div class="pages-wrapper py-3">
                
                <!-- Título da Página -->
                <div class="page-title-area">
                    <h2 class="page-title">Todas as Páginas</h2>
                    <p class="page-subtitle">Navegue pelo sistema</p>
                </div>
                
                <!-- Lista de Páginas -->
                <div class="row g-3">
                    <div class="col-6 col-md-4">
                        <div class="card page-card h-100">
                            <div class="card-body text-center">
                                <i class="ti ti-home" style="font-size: 32px; color: #007bff;"></i>
                                <h6 class="mt-2">Início</h6>
                                <a href="home.php" class="btn btn-sm btn-outline-primary">Acessar</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-6 col-md-4">
                        <div class="card page-card h-100">
                            <div class="card-body text-center">
                                <i class="ti ti-shopping-cart" style="font-size: 32px; color: #28a745;"></i>
                                <h6 class="mt-2">Loja</h6>
                                <a href="shop.php" class="btn btn-sm btn-outline-success">Acessar</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-6 col-md-4">
                        <div class="card page-card h-100">
                            <div class="card-body text-center">
                                <i class="ti ti-basket" style="font-size: 32px; color: #ffc107;"></i>
                                <h6 class="mt-2">Carrinho</h6>
                                <a href="cart.php" class="btn btn-sm btn-outline-warning">Acessar</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-6 col-md-4">
                        <div class="card page-card h-100">
                            <div class="card-body text-center">
                                <i class="ti ti-user" style="font-size: 32px; color: #6f42c1;"></i>
                                <h6 class="mt-2">Perfil</h6>
                                <a href="profile.php" class="btn btn-sm btn-outline-purple">Acessar</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-6 col-md-4">
                        <div class="card page-card h-100">
                            <div class="card-body text-center">
                                <i class="ti ti-bell-ringing" style="font-size: 32px; color: #dc3545;"></i>
                                <h6 class="mt-2">Notificações</h6>
                                <a href="notifications.php" class="btn btn-sm btn-outline-danger">Acessar</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-6 col-md-4">
                        <div class="card page-card h-100">
                            <div class="card-body text-center">
                                <i class="ti ti-settings" style="font-size: 32px; color: #17a2b8;"></i>
                                <h6 class="mt-2">Configurações</h6>
                                <a href="settings.php" class="btn btn-sm btn-outline-info">Acessar</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-6 col-md-4">
                        <div class="card page-card h-100">
                            <div class="card-body text-center">
                                <i class="ti ti-heart" style="font-size: 32px; color: #e83e8c;"></i>
                                <h6 class="mt-2">Favoritos</h6>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-6 col-md-4">
                        <div class="card page-card h-100">
                            <div class="card-body text-center">
                                <i class="ti ti-logout" style="font-size: 32px; color: #6c757d;"></i>
                                <h6 class="mt-2">Sair</h6>
                                <a href="logout.php" class="btn btn-sm btn-outline-secondary">Acessar</a>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
    <!-- Internet Connection Status-->
    <div class="internet-connection-status" id="internetStatus"></div>
    
    <!-- Footer Nav-->
    <div class="footer-nav-area" id="footerNav">
        <div class="suha-footer-nav">
            <ul class="h-100 d-flex align-items-center justify-content-between ps-0 d-flex rtl-flex-d-row-r">
                <li><a href="home.php"><i class="ti ti-home"></i>Início</a></li>
                <li><a href="cart.php"><i class="ti ti-basket"></i>Carrinho</a></li>
                <li><a href="settings.php"><i class="ti ti-settings"></i>Configurações</a></li>
                <li><a href="profile.php"><i class="ti ti-user"></i>Perfil</a></li>
            </ul>
        </div>
    </div>
    
    <!-- All JavaScript Files-->
    <script src="dist/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/jquery.min.js"></script>
    <script src="dist/js/waypoints.min.js"></script>
    <script src="dist/js/jquery.easing.min.js"></script>
    <script src="dist/js/owl.carousel.min.js"></script>
    <script src="dist/js/jquery.magnific-popup.min.js"></script>
    <script src="dist/js/jquery.counterup.min.js"></script>
    <script src="dist/js/jquery.countdown.min.js"></script>
    <script src="dist/js/jquery.passwordstrength.js"></script>
    <script src="dist/js/jquery.nice-select.min.js"></script>
    <script src="dist/js/theme-switching.js"></script>
    <script src="dist/js/no-internet.js"></script>
    <script src="dist/js/active.js"></script>
    <script src="dist/js/pwa.js"></script>
</body>
</html>
