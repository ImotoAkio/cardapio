<?php
// =====================================================
// 🍃 TEMPERO E CAFÉ - DASHBOARD ADMINISTRATIVO
// =====================================================

require_once 'config.php';

// Verificar login
$auth->requireLogin();

// Obter estatísticas
$dashboardStats = $stats->getDashboardStats();
$recentOrders = $stats->getRecentOrders(5);
$topProducts = $stats->getTopProducts(5);

// Obter alerta se houver
$alert = getAlert();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Dashboard</title>
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
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border: none;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
        
        .stat-icon.products { background: linear-gradient(45deg, #28a745, #20c997); }
        .stat-icon.categories { background: linear-gradient(45deg, #007bff, #6610f2); }
        .stat-icon.users { background: linear-gradient(45deg, #fd7e14, #e83e8c); }
        .stat-icon.orders { background: linear-gradient(45deg, #6f42c1, #d63384); }
        .stat-icon.revenue { background: linear-gradient(45deg, #198754, #0dcaf0); }
        .stat-icon.pending { background: linear-gradient(45deg, #ffc107, #fd7e14); }
        
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
                    <a class="nav-link active" href="dashboard.php">
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
                    <a class="nav-link" href="orders.php">
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
                    <h2><i class="bi bi-speedometer2 me-2"></i>Dashboard</h2>
                    <small class="text-muted">Última atualização: <?php echo date('d/m/Y H:i'); ?></small>
                </div>

                <!-- Cards de Estatísticas -->
                <div class="row mb-4">
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon products me-3">
                                    <i class="bi bi-box-seam"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0"><?php echo $dashboardStats['total_products']; ?></h3>
                                    <p class="text-muted mb-0">Produtos Ativos</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon categories me-3">
                                    <i class="bi bi-tags"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0"><?php echo $dashboardStats['total_categories']; ?></h3>
                                    <p class="text-muted mb-0">Categorias</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon users me-3">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0"><?php echo $dashboardStats['total_users']; ?></h3>
                                    <p class="text-muted mb-0">Usuários</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon orders me-3">
                                    <i class="bi bi-cart-check"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0"><?php echo $dashboardStats['total_orders']; ?></h3>
                                    <p class="text-muted mb-0">Total de Pedidos</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon pending me-3">
                                    <i class="bi bi-clock"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0"><?php echo $dashboardStats['pending_orders']; ?></h3>
                                    <p class="text-muted mb-0">Pedidos Pendentes</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon revenue me-3">
                                    <i class="bi bi-currency-dollar"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0"><?php echo formatPrice($dashboardStats['total_revenue']); ?></h3>
                                    <p class="text-muted mb-0">Receita Total</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabelas -->
                <div class="row">
                    <!-- Pedidos Recentes -->
                    <div class="col-lg-8 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Pedidos Recentes</h5>
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
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentOrders as $order): ?>
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
                                                        <span class="badge bg-<?php echo $statusClass[$order['status']]; ?>">
                                                            <?php echo $statusText[$order['status']]; ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo formatPrice($order['total']); ?></td>
                                                    <td><?php echo formatDate($order['created_at']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Produtos Mais Vendidos -->
                    <div class="col-lg-4 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-trophy me-2"></i>Top Produtos</h5>
                            </div>
                            <div class="card-body">
                                <?php foreach ($topProducts as $index => $product): ?>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="me-3">
                                            <span class="badge bg-primary"><?php echo $index + 1; ?></span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold"><?php echo $product['name']; ?></div>
                                            <small class="text-muted">
                                                <?php echo $product['total_sold']; ?> vendidos
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold"><?php echo formatPrice($product['price']); ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh a cada 5 minutos
        setTimeout(() => {
            location.reload();
        }, 300000);
    </script>
</body>
</html>
