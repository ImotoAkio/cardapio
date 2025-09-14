<?php
require_once 'includes/database.php';

session_start();

// Se já está logado, redirecionar para home
if (isset($_SESSION['user_id'])) {
    header('Location: home.php');
    exit;
}

$message = '';
$messageType = '';

// Processar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $message = 'Email e senha são obrigatórios!';
        $messageType = 'danger';
    } else {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            // Login bem-sucedido
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            
            // Associar carrinho ao usuário se existir
            $cartManager = new CartManager($db);
            $sessionId = session_id();
            $cartManager->associateCartToUser($sessionId, $user['id']);
            
            $message = 'Login realizado com sucesso! Redirecionando...';
            $messageType = 'success';
            
            // Redirecionar para home após 2 segundos
            header("refresh:2;url=home.php");
        } else {
            $message = 'Email ou senha incorretos!';
            $messageType = 'danger';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover, shrink-to-fit=no">
    <meta name="description" content="Tempero e Café - Produtos Naturais e Orgânicos">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#d3a74e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>Login - Tempero e Café</title>
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
    
    
    
    
    
    <link rel="stylesheet" href="dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="dist/css/tabler-icons.min.css">
    <link rel="stylesheet" href="dist/css/animate.css">
    <link rel="stylesheet" href="dist/css/owl.carousel.min.css">
    <link rel="stylesheet" href="dist/css/magnific-popup.css">
    <link rel="stylesheet" href="dist/css/nice-select.css">
    <link rel="stylesheet" href="dist/style.css">
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
        <div class="container h-100 d-flex align-items-center justify-content-between rtl-flex-d-row-r">
            <!-- Back Button-->
            <div class="back-button me-2"><a href="home.php"><i class="ti ti-arrow-left"></i></a></div>
            <!-- Page Title-->
            <div class="page-heading">
                <h6 class="mb-0">Login</h6>
            </div>
            <!-- Cart Icon-->
            <div class="cart-icon-wrap">
                <a href="cart.php">
                    <i class="ti ti-basket-bolt"></i>
                    <span class="cart-count" style="display: none;">0</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Page Content-->
    <div class="page-content-wrapper">
        <div class="container">
            <!-- Alert Messages-->
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show mt-3" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Login Form-->
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title mb-4 text-center">
                        <i class="ti ti-login text-primary me-2"></i>
                        Faça seu Login
                    </h5>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                                   placeholder="Digite seu email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Senha</label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Digite sua senha" required>
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="ti ti-login me-2"></i>
                                Entrar
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="text-muted">Não tem uma conta?</p>
                        <a href="cadastro.php" class="btn btn-outline-primary">
                            <i class="ti ti-user-plus me-2"></i>
                            Criar Conta
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Informações Adicionais-->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title mb-3">
                        <i class="ti ti-info-circle text-info me-2"></i>
                        Por que fazer login?
                    </h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="ti ti-check text-success me-2"></i>
                            Acompanhe seus pedidos
                        </li>
                        <li class="mb-2">
                            <i class="ti ti-check text-success me-2"></i>
                            Histórico de compras
                        </li>
                        <li class="mb-2">
                            <i class="ti ti-check text-success me-2"></i>
                            Carrinho salvo entre sessões
                        </li>
                        <li class="mb-0">
                            <i class="ti ti-check text-success me-2"></i>
                            Ofertas exclusivas
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer Nav-->
    <div class="footer-nav-area" id="footerNav">
        <div class="suha-footer-nav">
            <ul class="h-100 d-flex align-items-center justify-content-between ps-0">
                <li><a href="home.php"><i class="ti ti-home"></i>Início</a></li>
                <li><a href="cart.php"><i class="ti ti-basket"></i>Carrinho</a></li>
                <li><a href="settings.html"><i class="ti ti-settings"></i>Configurações</a></li>
                <li><a href="pages.html"><i class="ti ti-heart"></i>Favoritos</a></li>
            </ul>
        </div>
    </div>
    
    <!-- All JavaScript Files -->
    <script src="dist/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/jquery.min.js"></script>
    <script src="dist/js/active.js"></script>
    <script src="js/cart.js"></script>
    
    <script>
        // Atualizar contador do carrinho
        document.addEventListener('DOMContentLoaded', function() {
            if (window.cartManager) {
                window.cartManager.updateCartCount();
            }
        });
    </script>
</body>
</html>
