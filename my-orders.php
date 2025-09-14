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

// Buscar pedidos do usuário
$stmt = $db->prepare("
    SELECT o.*, 
           COUNT(oi.id) as item_count,
           SUM(oi.total) as total_amount
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Função para obter status em português
function getStatusText($status) {
    $statusMap = [
        'pending' => 'Pendente',
        'confirmed' => 'Confirmado',
        'processing' => 'Processando',
        'shipped' => 'Enviado',
        'delivered' => 'Entregue',
        'cancelled' => 'Cancelado'
    ];
    return $statusMap[$status] ?? ucfirst($status);
}

// Função para obter cor do status
function getStatusColor($status) {
    $colorMap = [
        'pending' => 'warning',
        'confirmed' => 'info',
        'processing' => 'primary',
        'shipped' => 'success',
        'delivered' => 'success',
        'cancelled' => 'danger'
    ];
    return $colorMap[$status] ?? 'secondary';
}

$isLoggedIn = true;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover, shrink-to-fit=no">
    <meta name="description" content="Tempero e Café - Meus Pedidos">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#d3a74e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <!-- Title -->
    <title>Meus Pedidos - Tempero e Café</title>
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
                <li><a href="my-orders.php" class="active"><i class="ti ti-package"></i>Meus Pedidos</a></li>
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
            <div class="orders-wrapper py-3">
                
                <!-- Título da Página -->
                <div class="page-title-area">
                    <h2 class="page-title">Meus Pedidos</h2>
                    <p class="page-subtitle">Acompanhe seus pedidos</p>
                </div>
                
                <!-- Filtros -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="statusFilter" class="form-label">Filtrar por Status</label>
                                <select class="form-select" id="statusFilter" onchange="filterOrders()">
                                    <option value="">Todos os status</option>
                                    <option value="pending">Pendente</option>
                                    <option value="confirmed">Confirmado</option>
                                    <option value="processing">Processando</option>
                                    <option value="shipped">Enviado</option>
                                    <option value="delivered">Entregue</option>
                                    <option value="cancelled">Cancelado</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="dateFilter" class="form-label">Período</label>
                                <select class="form-select" id="dateFilter" onchange="filterOrders()">
                                    <option value="">Todos os períodos</option>
                                    <option value="today">Hoje</option>
                                    <option value="week">Esta semana</option>
                                    <option value="month">Este mês</option>
                                    <option value="year">Este ano</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Lista de Pedidos -->
                <div id="ordersContainer">
                    <?php if (empty($orders)): ?>
                    <!-- Mensagem quando não há pedidos -->
                    <div class="text-center py-5">
                        <i class="ti ti-package" style="font-size: 64px; color: #e9ecef;"></i>
                        <h5 class="mt-3">Nenhum pedido encontrado</h5>
                        <p class="text-muted">Você ainda não fez nenhum pedido.</p>
                        <a href="shop.php" class="btn btn-primary">
                            <i class="ti ti-shopping-cart me-2"></i>
                            Fazer Primeiro Pedido
                        </a>
                    </div>
                    <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                    <div class="card order-card mb-3" data-status="<?php echo $order['status']; ?>" data-date="<?php echo date('Y-m-d', strtotime($order['created_at'])); ?>">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="order-info">
                                        <h6 class="order-number mb-1">
                                            Pedido #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?>
                                        </h6>
                                        <p class="order-date text-muted mb-1">
                                            <i class="ti ti-calendar me-1"></i>
                                            <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                        </p>
                                        <p class="order-items text-muted mb-0">
                                            <i class="ti ti-package me-1"></i>
                                            <?php echo $order['item_count']; ?> item(s) - 
                                            <strong><?php echo formatPrice($order['total_amount']); ?></strong>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="order-status">
                                        <span class="badge bg-<?php echo getStatusColor($order['status']); ?>">
                                            <?php echo getStatusText($order['status']); ?>
                                        </span>
                                    </div>
                                    <div class="order-actions mt-2">
                                        <button class="btn btn-sm btn-outline-primary" onclick="viewOrderDetails(<?php echo $order['id']; ?>)">
                                            <i class="ti ti-eye me-1"></i>
                                            Ver Detalhes
                                        </button>
                                        <?php if (in_array($order['status'], ['pending', 'confirmed', 'processing'])): ?>
                                        <button class="btn btn-sm btn-outline-danger ms-1" onclick="cancelOrder(<?php echo $order['id']; ?>)">
                                            <i class="ti ti-x me-1"></i>
                                            Cancelar
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
            </div>
        </div>
    </div>
    
    <!-- Modal de Detalhes do Pedido -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderDetailsModalLabel">Detalhes do Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                    <!-- Conteúdo será carregado via JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
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
    
    <script>
        function filterOrders() {
            const statusFilter = document.getElementById('statusFilter').value;
            const dateFilter = document.getElementById('dateFilter').value;
            const orders = document.querySelectorAll('.order-card');
            let visibleCount = 0;
            
            orders.forEach(order => {
                const orderStatus = order.getAttribute('data-status');
                const orderDate = order.getAttribute('data-date');
                let showOrder = true;
                
                // Filtro por status
                if (statusFilter && orderStatus !== statusFilter) {
                    showOrder = false;
                }
                
                // Filtro por data
                if (dateFilter && showOrder) {
                    const today = new Date();
                    const orderDateObj = new Date(orderDate);
                    
                    switch (dateFilter) {
                        case 'today':
                            if (orderDateObj.toDateString() !== today.toDateString()) {
                                showOrder = false;
                            }
                            break;
                        case 'week':
                            const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
                            if (orderDateObj < weekAgo) {
                                showOrder = false;
                            }
                            break;
                        case 'month':
                            const monthAgo = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);
                            if (orderDateObj < monthAgo) {
                                showOrder = false;
                            }
                            break;
                        case 'year':
                            const yearAgo = new Date(today.getTime() - 365 * 24 * 60 * 60 * 1000);
                            if (orderDateObj < yearAgo) {
                                showOrder = false;
                            }
                            break;
                    }
                }
                
                if (showOrder) {
                    order.style.display = 'block';
                    visibleCount++;
                } else {
                    order.style.display = 'none';
                }
            });
            
            // Mostrar mensagem se não há pedidos visíveis
            const container = document.getElementById('ordersContainer');
            let noOrdersMessage = container.querySelector('.no-orders-message');
            
            if (visibleCount === 0 && orders.length > 0) {
                if (!noOrdersMessage) {
                    noOrdersMessage = document.createElement('div');
                    noOrdersMessage.className = 'text-center py-5 no-orders-message';
                    noOrdersMessage.innerHTML = `
                        <i class="ti ti-search" style="font-size: 48px; color: #6c757d;"></i>
                        <h5 class="mt-3">Nenhum pedido encontrado</h5>
                        <p class="text-muted">Tente ajustar os filtros para encontrar seus pedidos.</p>
                    `;
                    container.appendChild(noOrdersMessage);
                }
                noOrdersMessage.style.display = 'block';
            } else if (noOrdersMessage) {
                noOrdersMessage.style.display = 'none';
            }
        }
        
        function viewOrderDetails(orderId) {
            const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
            
            // Mostrar loading
            document.getElementById('orderDetailsContent').innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-2">Carregando detalhes do pedido...</p>
                </div>
            `;
            modal.show();
            
            // Carregar detalhes do pedido
            fetch(`order_details_content.php?orderId=${orderId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.text();
                })
                .then(html => {
                    document.getElementById('orderDetailsContent').innerHTML = html;
                })
                .catch(error => {
                    console.error('Erro completo:', error);
                    document.getElementById('orderDetailsContent').innerHTML = `
                        <div class="text-center">
                            <i class="ti ti-alert-circle" style="font-size: 48px; color: #dc3545;"></i>
                            <h5 class="mt-3">Erro</h5>
                            <p class="text-muted">Erro ao carregar detalhes do pedido.</p>
                            <small class="text-muted">Detalhes: ${error.message}</small>
                        </div>
                    `;
                });
        }
        
        function cancelOrder(orderId) {
            if (confirm('Tem certeza que deseja cancelar este pedido?')) {
                // Aqui você implementaria a lógica para cancelar o pedido
                fetch('api/cancel_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        orderId: orderId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Pedido cancelado com sucesso!');
                        location.reload();
                    } else {
                        alert('Erro ao cancelar pedido: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao cancelar pedido. Tente novamente.');
                });
            }
        }
    </script>
</body>
</html>
