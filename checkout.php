<?php
require_once 'includes/database.php';
require_once 'includes/n8n_helper.php';

// Verificar se usuário está logado
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: home.php');
    exit;
}

$db = Database::getInstance()->getConnection();
$cartManager = new CartManager($db);
$sessionId = session_id();
$userId = $_SESSION['user_id'];

// Obter dados do usuário
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Obter itens do carrinho
$cartItems = $cartManager->getCartItems($sessionId);
$cartTotal = $cartManager->getCartTotal($sessionId);

// Se carrinho estiver vazio, redirecionar para home
if (empty($cartItems)) {
    header('Location: home.php');
    exit;
}

$message = '';
$messageType = '';

// Função específica para checkout que sempre inclui o prefixo correto
function getFirstProductImageForCheckout($imagesJson) {
    if (empty($imagesJson)) {
        return '/cardapio/dist/img/product/default.png';
    }
    
    $images = json_decode($imagesJson, true);
    
    // Se json_decode falhou, pode ser que o JSON esteja duplamente encodado
    if (!is_array($images)) {
        if (is_string($images)) {
            $images = json_decode($images, true);
        }
    }
    
    if (!is_array($images) || empty($images)) {
        return '/cardapio/dist/img/product/default.png';
    }
    
    $firstImage = $images[0];
    
    // Se já tem 'dist/' no caminho, adiciona apenas o prefixo
    if (strpos($firstImage, 'dist/') === 0) {
        return '/cardapio/' . $firstImage;
    }
    
    // Se tem 'img/' no caminho, adiciona 'dist/' e o prefixo
    if (strpos($firstImage, 'img/') === 0) {
        return '/cardapio/dist/' . $firstImage;
    }
    
    // Se é apenas o nome do arquivo, monta o caminho completo
    return '/cardapio/dist/img/product/' . basename($firstImage);
}

// Processar finalização do pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db->beginTransaction();
        
        // Gerar número do pedido
        $orderNumber = 'ORD' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        // Preparar dados de endereço
        $shippingAddress = json_encode([
            'name' => $user['full_name'],
            'email' => $user['email'],
            'phone' => $user['phone'] ?? '',
            'address' => $_POST['shipping_address'] ?? 'Endereço não informado',
            'city' => $_POST['shipping_city'] ?? 'Cidade não informada',
            'state' => $_POST['shipping_state'] ?? 'Estado não informado',
            'zipcode' => $_POST['shipping_zipcode'] ?? '00000-000'
        ]);
        
        $billingAddress = json_encode([
            'name' => $user['full_name'],
            'email' => $user['email'],
            'phone' => $user['phone'] ?? '',
            'address' => $_POST['billing_address'] ?? $_POST['shipping_address'] ?? 'Endereço não informado',
            'city' => $_POST['billing_city'] ?? $_POST['shipping_city'] ?? 'Cidade não informada',
            'state' => $_POST['billing_state'] ?? $_POST['shipping_state'] ?? 'Estado não informado',
            'zipcode' => $_POST['billing_zipcode'] ?? $_POST['shipping_zipcode'] ?? '00000-000'
        ]);
        
        // Criar pedido
        $stmt = $db->prepare("
            INSERT INTO orders (user_id, order_number, status, subtotal, total, shipping_address, billing_address, payment_method, payment_status, created_at) 
            VALUES (?, ?, 'pending', ?, ?, ?, ?, ?, 'pending', NOW())
        ");
        $paymentMethod = $_POST['payment_method'] ?? 'pix';
        $stmt->execute([$userId, $orderNumber, $cartTotal['total'], $cartTotal['total'], $shippingAddress, $billingAddress, $paymentMethod]);
        $orderId = $db->lastInsertId();
        
        // Adicionar itens do pedido
        foreach ($cartItems as $item) {
            $itemTotal = $item['price'] * $item['quantity'];
            $stmt = $db->prepare("
                INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity, total, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$orderId, $item['product_id'], $item['name'], $item['price'], $item['quantity'], $itemTotal]);
        }
        
        // Limpar carrinho
        $cartManager->clearCart($sessionId);
        
        // Enviar pedido para n8n
        try {
            $n8nResult = N8nHelper::sendOrderToN8n($orderId, $db);
            if ($n8nResult && $n8nResult['success']) {
                // Atualizar status do pedido para confirmado
                $stmt = $db->prepare("UPDATE orders SET status = 'confirmed', updated_at = NOW() WHERE id = ?");
                $stmt->execute([$orderId]);
                
                error_log("Pedido $orderId enviado para n8n com sucesso");
            } else {
                $errorMessage = is_array($n8nResult) ? ($n8nResult['message'] ?? 'Erro desconhecido') : 'Falha ao enviar para n8n';
                error_log("Erro ao enviar pedido $orderId para n8n: " . $errorMessage);
            }
        } catch (Exception $e) {
            error_log("Exceção ao enviar pedido $orderId para n8n: " . $e->getMessage());
        }
        
        $db->commit();
        
        $message = 'Pedido realizado com sucesso! Você receberá um email de confirmação.';
        $messageType = 'success';
        
        // Redirecionar para página de sucesso após 3 segundos
        header("refresh:3;url=home.php");
        
    } catch (Exception $e) {
        $db->rollBack();
        $message = 'Erro ao processar pedido: ' . $e->getMessage();
        $messageType = 'danger';
        
        // Log do erro para debug
        error_log("Erro no checkout: " . $e->getMessage() . " - File: " . $e->getFile() . " - Line: " . $e->getLine());
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
                <h6 class="mb-0">Finalizar Pedido</h6>
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
                    <h5 class="user-name mb-1 text-white"><?php echo htmlspecialchars($user['full_name']); ?></h5>
                    <p class="available-balance text-white">Saldo Atual R$<span class="counter">0,00</span></p>
                </div>
            </div>
            <!-- Sidenav Nav-->
            <ul class="sidenav-nav ps-0">
                <li><a href="home.php"><i class="ti ti-home"></i>Início</a></li>
                <li><a href="cart.php"><i class="ti ti-basket"></i>Carrinho</a></li>
                <li><a href="settings.php"><i class="ti ti-settings"></i>Configurações</a></li>
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
            
            <!-- Checkout Wrapper-->
            <div class="checkout-wrapper-area py-3">
                <!-- Billing Address-->
                <div class="billing-information-card mb-3">
                    <div class="card billing-information-title-card">
                        <div class="card-body">
                            <h6 class="text-center mb-0">Informações de Cobrança</h6>
                        </div>
                    </div>
                    <div class="card user-data-card">
                        <div class="card-body">                                   
                            <div class="single-profile-data d-flex align-items-center justify-content-between">
                                <div class="title d-flex align-items-center"><i class="ti ti-user"></i><span>Nome Completo</span></div>
                                <div class="data-content"><?php echo htmlspecialchars($user['full_name']); ?></div>
                            </div>
                            <div class="single-profile-data d-flex align-items-center justify-content-between">
                                <div class="title d-flex align-items-center"><i class="ti ti-mail"></i><span>Email</span></div>
                                <div class="data-content"><?php echo htmlspecialchars($user['email']); ?></div>
                            </div>
                            <div class="single-profile-data d-flex align-items-center justify-content-between">
                                <div class="title d-flex align-items-center"><i class="ti ti-phone"></i><span>WhatsApp</span></div>
                                <div class="data-content"><?php echo htmlspecialchars($user['phone']); ?></div>
                            </div>
                            <div class="single-profile-data d-flex align-items-center justify-content-between">
                                <div class="title d-flex align-items-center"><i class="ti ti-ship"></i><span>Endereço:</span></div>
                                <div class="data-content"><?php echo htmlspecialchars($user['address']); ?></div>
                            </div>
                            <!-- Edit Address--><a class="btn btn-primary w-100" href="cadastro.php">Editar Informações</a>
                        </div>
                    </div>
                </div>
                
                <!-- Resumo do Pedido-->
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="text-center mb-3">Resumo do Pedido</h6>
                        
                        <?php foreach ($cartItems as $item): ?>
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <?php
                                    $productImage = getFirstProductImageForCheckout($item['images']);
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
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span><?php echo formatPrice($cartTotal['total']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Frete:</span>
                            <span>R$ 0,00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong><?php echo formatPrice($cartTotal['total']); ?></strong>
                        </div>
                    </div>
                </div>
                
                <!-- Shipping Address-->
                <div class="shipping-address mb-3">
                    <div class="card shipping-address-title-card">
                        <div class="card-body">
                            <h6 class="text-center mb-0">Endereço de Entrega</h6>
                        </div>
                    </div>
                    <div class="card shipping-address-form-card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label">Endereço Completo</label>
                                    <input type="text" class="form-control" name="shipping_address" placeholder="Rua, número, bairro" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">Cidade</label>
                                    <input type="text" class="form-control" name="shipping_city" placeholder="Cidade" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">Estado</label>
                                    <input type="text" class="form-control" name="shipping_state" placeholder="Estado" required>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">CEP</label>
                                    <input type="text" class="form-control" name="shipping_zipcode" placeholder="00000-000" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Method Choose-->
                <div class="shipping-method-choose mb-3">
                    <div class="card shipping-method-choose-title-card">
                        <div class="card-body">
                            <h6 class="text-center mb-0">Forma de Pagamento</h6>
                        </div>
                    </div>
                    <div class="card shipping-method-choose-card">
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="shipping-method-choose">
                                    <ul class="ps-0">
                                        <li>
                                            <input id="pix" type="radio" name="payment_method" value="pix" checked>
                                            <label for="pix">PIX<span>5% de desconto - Pagamento instantâneo</span></label>
                                            <div class="check"></div>
                                        </li>
                                        <li>
                                            <input id="credit_card" type="radio" name="payment_method" value="credit_card">
                                            <label for="credit_card">Cartão de Crédito<span>Parcelamento em até 12x</span></label>
                                            <div class="check"></div>
                                        </li>
                                        <li>
                                            <input id="cash" type="radio" name="payment_method" value="cash">
                                            <label for="cash">Dinheiro na Entrega<span>Pague quando receber</span></label>
                                            <div class="check"></div>
                                        </li>
                                    </ul>
                                </div>
                                
                                <!-- Cart Amount Area-->
                                <div class="card cart-amount-area mt-3">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <h5 class="total-price mb-0"><?php echo formatPrice($cartTotal['total']); ?></h5>
                                        <button type="submit" class="btn btn-primary">Confirmar Pedido</button>
                                    </div>
                                </div>
                            </form>
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