<?php
// =====================================================
// 🍃 TEMPERO E CAFÉ - GESTÃO DE PEDIDOS
// =====================================================

require_once 'config.php';
require_once 'includes/order_progression_helper.php';

// Verificar login
$auth->requireLogin();

// Obter conexão com o banco de dados
$db = Database::getInstance()->getConnection();

// Obter alerta se houver
$alert = getAlert();

// Processar ações
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Processar atualização de status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update_status') {
    $status = $_POST['status'];
    $oldStatus = $_POST['old_status'] ?? '';
    
    if ($orderManager->updateOrderStatus($id, $status)) {
        // Enviar atualização para o webhook de progressão
        try {
            $webhookResult = OrderProgressionHelper::sendStatusUpdate($id, $status, $db);
            if ($webhookResult['success']) {
                showAlert('Status do pedido atualizado com sucesso e notificação enviada!', 'success');
                error_log("Status do pedido $id atualizado de '$oldStatus' para '$status' - Webhook enviado com sucesso");
            } else {
                showAlert('Status atualizado, mas erro ao enviar notificação: ' . $webhookResult['message'], 'warning');
                error_log("Status do pedido $id atualizado, mas webhook falhou: " . $webhookResult['message']);
            }
        } catch (Exception $e) {
            showAlert('Status atualizado, mas erro ao enviar notificação: ' . $e->getMessage(), 'warning');
            error_log("Status do pedido $id atualizado, mas exceção no webhook: " . $e->getMessage());
        }
    } else {
        showAlert('Erro ao atualizar status do pedido!', 'danger');
    }
    redirect('orders.php');
}

// Processar progressão rápida de status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'progress_status') {
    $currentStatus = $_POST['current_status'];
    $orderId = $_POST['order_id'];
    
    // Definir próximo status baseado no atual
    $statusProgression = [
        'pending' => 'confirmed',
        'confirmed' => 'processing',
        'processing' => 'shipped',
        'shipped' => 'delivered'
    ];
    
    $nextStatus = $statusProgression[$currentStatus] ?? $currentStatus;
    
    if ($orderManager->updateOrderStatus($orderId, $nextStatus)) {
        // Enviar atualização para o webhook
        try {
            $webhookResult = OrderProgressionHelper::sendStatusUpdate($orderId, $nextStatus, $db);
            if ($webhookResult['success']) {
                showAlert("Pedido progredido para '" . ucfirst($nextStatus) . "' com sucesso!", 'success');
            } else {
                showAlert("Status atualizado, mas erro na notificação: " . $webhookResult['message'], 'warning');
            }
        } catch (Exception $e) {
            showAlert("Status atualizado, mas erro na notificação: " . $e->getMessage(), 'warning');
        }
    } else {
        showAlert('Erro ao progredir status do pedido!', 'danger');
    }
    redirect('orders.php?action=view&id=' . $orderId);
}

// Obter pedidos para listagem
$page = (int)($_GET['page'] ?? 1);
$orders = $orderManager->getAllOrders($page, 20);

// Obter pedido específico se necessário
$order = null;
$orderItems = [];
if ($action === 'view' && $id) {
    $order = $orderManager->getOrderById($id);
    if (!$order) {
        showAlert('Pedido não encontrado!', 'danger');
        redirect('orders.php');
    }
    $orderItems = $orderManager->getOrderItems($id);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #d3a74e;
            --dark-color: #0d2b2b;
            --light-bg: #f8f9fa;
        }
        
        body {
            background-color: var(--light-bg);
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--dark-color), #1a4a4a);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            color: var(--primary-color) !important;
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .sidebar {
            background: white;
            min-height: calc(100vh - 76px);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: #666;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 2px 10px;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }
        
        .main-content {
            padding: 30px;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .card-header {
            background: linear-gradient(45deg, var(--primary-color), #b8943f);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
        }
        
        .btn-primary {
            background: var(--primary-color);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: #b8943f;
            transform: translateY(-2px);
        }
        
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .badge {
            padding: 8px 12px;
            border-radius: 20px;
        }
        
        .status-badge {
            font-size: 0.8rem;
            padding: 6px 10px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-shop me-2"></i>
                Tempero e Café Admin
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i>
                        <?php echo $auth->getUser()['full_name']; ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sair</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <nav class="nav flex-column pt-3">
                    <a class="nav-link" href="dashboard.php">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="products.php">
                        <i class="bi bi-box-seam me-2"></i>Produtos
                    </a>
                    <a class="nav-link" href="categories.php">
                        <i class="bi bi-tags me-2"></i>Categorias
                    </a>
                    <a class="nav-link" href="subcategories.php">
                        <i class="bi bi-tag me-2"></i>Subcategorias
                    </a>
                    <a class="nav-link active" href="orders.php">
                        <i class="bi bi-cart-check me-2"></i>Pedidos
                    </a>
                    <a class="nav-link" href="users.php">
                        <i class="bi bi-people me-2"></i>Usuários
                    </a>
                    <a class="nav-link" href="coupons.php">
                        <i class="bi bi-ticket-perforated me-2"></i>Cupons
                    </a>
                    <a class="nav-link" href="settings.php">
                        <i class="bi bi-gear me-2"></i>Configurações
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Alertas -->
                <?php if ($alert): ?>
                    <div class="alert alert-<?php echo $alert['type']; ?> alert-dismissible fade show" role="alert">
                        <?php echo $alert['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Título -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-cart-check me-2"></i>Gestão de Pedidos</h2>
                    <div>
                        <a href="orders.php" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-clockwise me-2"></i>Atualizar
                        </a>
                    </div>
                </div>

                <?php if ($action === 'view' && $order): ?>
                    <!-- Detalhes do Pedido -->
                    <div class="row">
                        <div class="col-lg-8">
                            <!-- Informações do Pedido -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="bi bi-receipt me-2"></i>
                                        Pedido #<?php echo $order['order_number']; ?>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Informações do Cliente</h6>
                                            <p class="mb-1"><strong>Nome:</strong> <?php echo $order['full_name']; ?></p>
                                            <p class="mb-1"><strong>Email:</strong> <?php echo $order['email']; ?></p>
                                            <p class="mb-1"><strong>Telefone:</strong> <?php echo $order['phone']; ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Informações do Pedido</h6>
                                            <p class="mb-1"><strong>Data:</strong> <?php echo formatDate($order['created_at']); ?></p>
                                            <p class="mb-1"><strong>Status:</strong> 
                                                <?php
                                                $statusClass = [
                                                    'pending' => 'warning',
                                                    'confirmed' => 'info',
                                                    'processing' => 'primary',
                                                    'shipped' => 'secondary',
                                                    'delivered' => 'success',
                                                    'cancelled' => 'danger'
                                                ];
                                                $statusText = [
                                                    'pending' => 'Pendente',
                                                    'confirmed' => 'Confirmado',
                                                    'processing' => 'Processando',
                                                    'shipped' => 'Enviado',
                                                    'delivered' => 'Entregue',
                                                    'cancelled' => 'Cancelado'
                                                ];
                                                ?>
                                                <span class="badge bg-<?php echo $statusClass[$order['status']]; ?>">
                                                    <?php echo $statusText[$order['status']]; ?>
                                                </span>
                                            </p>
                                            <p class="mb-1"><strong>Pagamento:</strong> 
                                                <span class="badge bg-<?php echo $order['payment_status'] === 'paid' ? 'success' : 'warning'; ?>">
                                                    <?php echo ucfirst($order['payment_status']); ?>
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Itens do Pedido -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Itens do Pedido</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Produto</th>
                                                    <th>Preço</th>
                                                    <th>Quantidade</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($orderItems as $item): ?>
                                                    <tr>
                                                        <td>
                                                            <div class="fw-bold"><?php echo $item['product_name']; ?></div>
                                                        </td>
                                                        <td><?php echo formatPrice($item['product_price']); ?></td>
                                                        <td><?php echo $item['quantity']; ?></td>
                                                        <td><?php echo formatPrice($item['total']); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <!-- Resumo do Pedido -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="bi bi-calculator me-2"></i>Resumo</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal:</span>
                                        <span><?php echo formatPrice($order['subtotal']); ?></span>
                                    </div>
                                    <?php if ($order['discount'] > 0): ?>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Desconto:</span>
                                            <span class="text-success">-<?php echo formatPrice($order['discount']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Frete:</span>
                                        <span><?php echo formatPrice($order['shipping_cost']); ?></span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <strong>Total:</strong>
                                        <strong><?php echo formatPrice($order['total']); ?></strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Progressão de Status -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="bi bi-arrow-right-circle me-2"></i>Progressão Rápida</h5>
                                </div>
                                <div class="card-body">
                                    <?php
                                    $statusProgression = [
                                        'pending' => ['next' => 'confirmed', 'label' => 'Confirmar Pedido', 'icon' => 'bi-check-circle', 'color' => 'success'],
                                        'confirmed' => ['next' => 'processing', 'label' => 'Iniciar Preparo', 'icon' => 'bi-play-circle', 'color' => 'primary'],
                                        'processing' => ['next' => 'shipped', 'label' => 'Marcar como Enviado', 'icon' => 'bi-truck', 'color' => 'info'],
                                        'shipped' => ['next' => 'delivered', 'label' => 'Marcar como Entregue', 'icon' => 'bi-check2-all', 'color' => 'success']
                                    ];
                                    
                                    $currentStatus = $order['status'];
                                    $canProgress = isset($statusProgression[$currentStatus]);
                                    ?>
                                    
                                    <?php if ($canProgress): ?>
                                        <?php $nextStep = $statusProgression[$currentStatus]; ?>
                                        <div class="d-grid">
                                            <form method="POST" action="orders.php?action=progress_status">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <input type="hidden" name="current_status" value="<?php echo $currentStatus; ?>">
                                                <button type="submit" class="btn btn-<?php echo $nextStep['color']; ?> btn-lg">
                                                    <i class="<?php echo $nextStep['icon']; ?> me-2"></i>
                                                    <?php echo $nextStep['label']; ?>
                                                </button>
                                            </form>
                                        </div>
                                        <small class="text-muted mt-2 d-block text-center">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Esta ação enviará uma notificação automática para o cliente
                                        </small>
                                    <?php else: ?>
                                        <div class="text-center">
                                            <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem;"></i>
                                            <p class="mt-2 mb-0">Pedido finalizado</p>
                                            <small class="text-muted">Não há mais etapas para este pedido</small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Atualizar Status Manual -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Atualização Manual</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="orders.php?action=update_status&id=<?php echo $order['id']; ?>">
                                        <input type="hidden" name="old_status" value="<?php echo $order['status']; ?>">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Novo Status</label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pendente</option>
                                                <option value="confirmed" <?php echo $order['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmado</option>
                                                <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processando</option>
                                                <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Enviado</option>
                                                <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Entregue</option>
                                                <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelado</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-outline-primary w-100">
                                            <i class="bi bi-check-circle me-2"></i>Atualizar Status
                                        </button>
                                    </form>
                                    <small class="text-muted mt-2 d-block text-center">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Use para mudanças específicas ou correções
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Lista de Pedidos -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-list me-2"></i>Lista de Pedidos</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Pedido</th>
                                            <th>Cliente</th>
                                            <th>Status</th>
                                            <th>Total</th>
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
                                                        <div><?php echo $order['full_name']; ?></div>
                                                        <small class="text-muted"><?php echo $order['email']; ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php
                                                    $statusClass = [
                                                        'pending' => 'warning',
                                                        'confirmed' => 'info',
                                                        'processing' => 'primary',
                                                        'shipped' => 'secondary',
                                                        'delivered' => 'success',
                                                        'cancelled' => 'danger'
                                                    ];
                                                    $statusText = [
                                                        'pending' => 'Pendente',
                                                        'confirmed' => 'Confirmado',
                                                        'processing' => 'Processando',
                                                        'shipped' => 'Enviado',
                                                        'delivered' => 'Entregue',
                                                        'cancelled' => 'Cancelado'
                                                    ];
                                                    ?>
                                                    <span class="badge bg-<?php echo $statusClass[$order['status']]; ?> status-badge">
                                                        <?php echo $statusText[$order['status']]; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo formatPrice($order['total']); ?></td>
                                                <td><?php echo formatDate($order['created_at']); ?></td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="orders.php?action=view&id=<?php echo $order['id']; ?>" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <?php
                                                        // Botão de progressão rápida
                                                        $statusProgression = [
                                                            'pending' => ['next' => 'confirmed', 'icon' => 'bi-check-circle', 'color' => 'success', 'title' => 'Confirmar'],
                                                            'confirmed' => ['next' => 'processing', 'icon' => 'bi-play-circle', 'color' => 'primary', 'title' => 'Iniciar Preparo'],
                                                            'processing' => ['next' => 'shipped', 'icon' => 'bi-truck', 'color' => 'info', 'title' => 'Enviar'],
                                                            'shipped' => ['next' => 'delivered', 'icon' => 'bi-check2-all', 'color' => 'success', 'title' => 'Entregar']
                                                        ];
                                                        
                                                        if (isset($statusProgression[$order['status']])) {
                                                            $nextStep = $statusProgression[$order['status']];
                                                            ?>
                                                            <form method="POST" action="orders.php?action=progress_status" class="d-inline">
                                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                                <input type="hidden" name="current_status" value="<?php echo $order['status']; ?>">
                                                                <button type="submit" class="btn btn-sm btn-<?php echo $nextStep['color']; ?>" 
                                                                        title="<?php echo $nextStep['title']; ?>">
                                                                    <i class="<?php echo $nextStep['icon']; ?>"></i>
                                                                </button>
                                                            </form>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
