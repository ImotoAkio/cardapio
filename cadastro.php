<?php
require_once 'includes/database.php';

// Verificar se há dados do carrinho
session_start();
$db = Database::getInstance()->getConnection();
$cartManager = new CartManager($db);
$sessionId = session_id();
$userId = $_SESSION['user_id'] ?? null;

// Obter itens do carrinho
$cartItems = $cartManager->getCartItems($sessionId, $userId);
$cartTotal = $cartManager->getCartTotal($sessionId, $userId);

// Se carrinho estiver vazio, redirecionar para home
if (empty($cartItems)) {
    header('Location: home.php');
    exit;
}

$message = '';
$messageType = '';

// Processar formulário de cadastro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $whatsapp = trim($_POST['whatsapp'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validações
    if (empty($name) || empty($whatsapp) || empty($email) || empty($address) || empty($password)) {
        $message = 'Todos os campos são obrigatórios!';
        $messageType = 'danger';
    } elseif ($password !== $confirmPassword) {
        $message = 'As senhas não coincidem!';
        $messageType = 'danger';
    } elseif (strlen($password) < 6) {
        $message = 'A senha deve ter pelo menos 6 caracteres!';
        $messageType = 'danger';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Email inválido!';
        $messageType = 'danger';
    } else {
        // Verificar se email já existe
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $message = 'Este email já está cadastrado!';
            $messageType = 'danger';
        } else {
            // Criar usuário
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (full_name, email, phone, address, password, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            
            if ($stmt->execute([$name, $email, $whatsapp, $address, $hashedPassword])) {
                $userId = $db->lastInsertId();
                
                // Associar carrinho ao usuário
                $cartManager->associateCartToUser($sessionId, $userId);
                
                // Fazer login automático
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                
                $message = 'Cadastro realizado com sucesso! Redirecionando...';
                $messageType = 'success';
                
                // Redirecionar para checkout após 2 segundos
                header("refresh:2;url=checkout.php");
            } else {
                $message = 'Erro ao criar conta. Tente novamente!';
                $messageType = 'danger';
            }
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
    <!-- The above tags *must* come first in the head, any other head content must come *after* these tags -->
    <!-- Title -->
    <title>Tempero e Café - Produtos Naturais e Orgânicos</title>
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
        <div class="container h-100 d-flex align-items-center justify-content-between rtl-flex-d-row-r">
            <!-- Back Button-->
            <div class="back-button me-2"><a href="cart.php"><i class="ti ti-arrow-left"></i></a></div>
            <!-- Page Title-->
            <div class="page-heading">
                <h6 class="mb-0">Finalizar Compra</h6>
            </div>
            <!-- Navbar Toggler-->
            <div class="suha-navbar-toggler ms-2" data-bs-toggle="offcanvas" data-bs-target="#suhaOffcanvas" aria-controls="suhaOffcanvas">
                <div><span></span><span></span><span></span></div>
            </div>
        </div>
    </div>
    
    <div class="offcanvas offcanvas-start suha-offcanvas-wrap" tabindex="-1" id="suhaOffcanvas" aria-labelledby="suhaOffcanvasLabel">
        <!-- Close button-->
        <button class="btn-close btn-close-white" type="button" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        <!-- Offcanvas body-->
        <div class="offcanvas-body">
            <!-- Sidenav Profile-->
            <div class="sidenav-profile">
                <div class="user-profile"><img src="dist/img/bg-img/9.jpg" alt=""></div>
                <div class="user-info">
                    <h5 class="user-name mb-1 text-white">Cliente</h5>
                    <p class="available-balance text-white">Saldo Atual R$<span class="counter">0,00</span></p>
                </div>
            </div>
            <!-- Sidenav Nav-->
            <ul class="sidenav-nav ps-0">
                <li><a href="home.php"><i class="ti ti-home"></i>Início</a></li>
                <li><a href="cart.php"><i class="ti ti-basket"></i>Carrinho</a></li>
                <li><a href="settings.html"><i class="ti ti-settings"></i>Configurações</a></li>
                <li><a href="pages.html"><i class="ti ti-heart"></i>Favoritos</a></li>
            </ul>
        </div>
    </div>
    
    <div class="page-content-wrapper">
        <div class="container">
            <!-- Alert Messages-->
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show mt-3" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Profile Wrapper-->
            <div class="profile-wrapper-area py-3">
                <!-- User Information-->
                <div class="card user-info-card">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="user-profile me-3">
                            <img src="dist/img/bg-img/9.jpg" alt="">
                        </div>
                        <div class="user-info">
                            <p class="mb-0 text-white">@tempero-e-cafe</p>
                            <h5 class="mb-0 text-white">Cadastro Rápido</h5>
                        </div>
                    </div>
                </div>
                
                <!-- User Meta Data-->
                <div class="card user-data-card">
                    <div class="card-body">
                        <form action="" method="POST">
                            <div class="mb-3">
                                <div class="title mb-2"><i class="ti ti-user"></i><span>Nome Completo</span></div>
                                <input class="form-control" type="text" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" placeholder="Digite seu nome completo" required>
                            </div>
                            
                            <div class="mb-3">
                                <div class="title mb-2"><i class="ti ti-mail"></i><span>Email</span></div>
                                <input class="form-control" type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" placeholder="Digite seu email" required>
                            </div>
                            
                            <div class="mb-3">
                                <div class="title mb-2"><i class="ti ti-phone"></i><span>WhatsApp</span></div>
                                <input class="form-control" type="tel" name="whatsapp" value="<?php echo htmlspecialchars($_POST['whatsapp'] ?? ''); ?>" placeholder="(11) 99999-9999" required>
                            </div>
                            
                            <div class="mb-3">
                                <div class="title mb-2"><i class="ti ti-location"></i><span>Endereço Completo</span></div>
                                <textarea class="form-control" name="address" rows="3" placeholder="Rua, número, bairro, cidade - CEP" required><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <div class="title mb-2"><i class="ti ti-lock"></i><span>Senha</span></div>
                                <input class="form-control" type="password" name="password" placeholder="Mínimo 6 caracteres" required>
                            </div>
                            
                            <div class="mb-3">
                                <div class="title mb-2"><i class="ti ti-lock"></i><span>Confirmar Senha</span></div>
                                <input class="form-control" type="password" name="confirm_password" placeholder="Digite a senha novamente" required>
                            </div>
                            
                            <button class="btn btn-primary btn-lg w-100" type="submit">Finalizar Cadastro e Compra</button>
                        </form>
                        
                        <div class="text-center mt-4">
                            <p class="text-muted">Já tem uma conta?</p>
                            <a href="login.php" class="btn btn-outline-primary">
                                <i class="ti ti-login me-2"></i>
                                Fazer Login
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Resumo do Pedido-->
                <div class="card user-data-card">
                    <div class="card-body">
                        <div class="title mb-3"><i class="ti ti-shopping-cart"></i><span>Resumo do Pedido</span></div>
                        
                        <?php foreach ($cartItems as $item): ?>
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <?php
                                    $productImage = getFirstProductImage($item['images']);
                                    if ($productImage) {
                                        echo '<img src="' . $productImage . '" alt="' . htmlspecialchars($item['name']) . '" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">';
                                    } else {
                                        echo '<div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;"><i class="ti ti-image text-muted"></i></div>';
                                    }
                                    ?>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                    <small class="text-muted">Qtd: <?php echo $item['quantity']; ?></small>
                                </div>
                                <div class="text-end">
                                    <strong><?php echo formatPrice($item['price'] * $item['quantity']); ?></strong>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong><?php echo formatPrice($cartTotal['total']); ?></strong>
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
                <li><a href="settings.html"><i class="ti ti-settings"></i>Configurações</a></li>
                <li><a href="pages.html"><i class="ti ti-heart"></i>Favoritos</a></li>
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
    <script src="js/cart.js"></script>
    
    <script>
        // Máscara para WhatsApp
        document.querySelector('input[name="whatsapp"]').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
                if (value.length < 14) {
                    value = value.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
                }
            }
            e.target.value = value;
        });
        
        // Validação de senha em tempo real
        document.querySelector('input[name="confirm_password"]').addEventListener('input', function() {
            const password = document.querySelector('input[name="password"]').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('As senhas não coincidem');
            } else {
                this.setCustomValidity('');
            }
        });
        
        // Atualizar contador do carrinho
        document.addEventListener('DOMContentLoaded', function() {
            if (window.cartManager) {
                window.cartManager.updateCartCount();
            }
        });
    </script>
</body>
</html>