<?php
// =====================================================
// 🍃 TEMPERO E CAFÉ - CONFIGURAÇÕES
// =====================================================

require_once 'config.php';

// Verificar login
$auth->requireLogin();

// Obter conexão com o banco de dados
$db = Database::getInstance()->getConnection();

// Obter alerta se houver
$alert = getAlert();

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        $full_name = sanitizeInput($_POST['full_name']);
        $email = sanitizeInput($_POST['email']);
        $phone = sanitizeInput($_POST['phone']);
        
        $stmt = $db->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?");
        if ($stmt->execute([$full_name, $email, $phone, $auth->getUser()['id']])) {
            // Atualizar sessão
            $_SESSION[ADMIN_SESSION]['full_name'] = $full_name;
            $_SESSION[ADMIN_SESSION]['email'] = $email;
            showAlert('Perfil atualizado com sucesso!', 'success');
        } else {
            showAlert('Erro ao atualizar perfil!', 'danger');
        }
    } elseif ($action === 'change_password') {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if ($new_password !== $confirm_password) {
            showAlert('As senhas não coincidem!', 'danger');
        } elseif (strlen($new_password) < PASSWORD_MIN_LENGTH) {
            showAlert('A senha deve ter pelo menos ' . PASSWORD_MIN_LENGTH . ' caracteres!', 'danger');
        } else {
            // Verificar senha atual
            $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$auth->getUser()['id']]);
            $user = $stmt->fetch();
            
            if (password_verify($current_password, $user['password'])) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
                
                if ($stmt->execute([$hashed_password, $auth->getUser()['id']])) {
                    showAlert('Senha alterada com sucesso!', 'success');
                } else {
                    showAlert('Erro ao alterar senha!', 'danger');
                }
            } else {
                showAlert('Senha atual incorreta!', 'danger');
            }
        }
    }
    
    redirect('settings.php');
}

// Obter dados do usuário
$user = $auth->getUser();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Configurações</title>
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
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 40px;
            font-weight: bold;
            margin: 0 auto;
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
                        <?php echo $user['full_name']; ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="settings.php"><i class="bi bi-person me-2"></i>Perfil</a></li>
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
                    <a class="nav-link" href="orders.php">
                        <i class="bi bi-cart-check me-2"></i>Pedidos
                    </a>
                    <a class="nav-link" href="users.php">
                        <i class="bi bi-people me-2"></i>Usuários
                    </a>
                    <a class="nav-link" href="coupons.php">
                        <i class="bi bi-ticket-perforated me-2"></i>Cupons
                    </a>
                    <a class="nav-link active" href="settings.php">
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
                    <h2><i class="bi bi-gear me-2"></i>Configurações</h2>
                </div>

                <div class="row">
                    <!-- Perfil do Usuário -->
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-person me-2"></i>Perfil do Usuário</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-4">
                                    <div class="profile-avatar">
                                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                    </div>
                                    <h5 class="mt-3"><?php echo $user['full_name']; ?></h5>
                                    <p class="text-muted">@<?php echo $user['username']; ?></p>
                                </div>
                                
                                <form method="POST" action="settings.php">
                                    <input type="hidden" name="action" value="update_profile">
                                    
                                    <div class="mb-3">
                                        <label for="full_name" class="form-label">Nome Completo</label>
                                        <input type="text" class="form-control" id="full_name" name="full_name" 
                                               value="<?php echo $user['full_name']; ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo $user['email']; ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Telefone</label>
                                        <input type="text" class="form-control" id="phone" name="phone" 
                                               value="<?php echo $user['phone'] ?? ''; ?>">
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-check-circle me-2"></i>Atualizar Perfil
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Alterar Senha -->
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-lock me-2"></i>Alterar Senha</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="settings.php">
                                    <input type="hidden" name="action" value="change_password">
                                    
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Senha Atual</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">Nova Senha</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" 
                                               minlength="<?php echo PASSWORD_MIN_LENGTH; ?>" required>
                                        <div class="form-text">Mínimo de <?php echo PASSWORD_MIN_LENGTH; ?> caracteres</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirmar Nova Senha</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                               minlength="<?php echo PASSWORD_MIN_LENGTH; ?>" required>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-key me-2"></i>Alterar Senha
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Informações do Sistema -->
                    <div class="col-lg-12 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informações do Sistema</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Informações da Aplicação</h6>
                                        <ul class="list-unstyled">
                                            <li><strong>Nome:</strong> <?php echo APP_NAME; ?></li>
                                            <li><strong>Versão:</strong> <?php echo APP_VERSION; ?></li>
                                            <li><strong>PHP:</strong> <?php echo PHP_VERSION; ?></li>
                                            <li><strong>Servidor:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'N/A'; ?></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Informações do Banco</h6>
                                        <ul class="list-unstyled">
                                            <li><strong>Host:</strong> <?php echo DB_HOST; ?></li>
                                            <li><strong>Banco:</strong> <?php echo DB_NAME; ?></li>
                                            <li><strong>Usuário:</strong> <?php echo DB_USER; ?></li>
                                            <li><strong>Charset:</strong> <?php echo DB_CHARSET; ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estatísticas Rápidas -->
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Estatísticas Rápidas</h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <div class="h4 text-primary"><?php echo $stats->getDashboardStats()['total_products']; ?></div>
                                        <small class="text-muted">Produtos</small>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="h4 text-success"><?php echo $stats->getDashboardStats()['total_orders']; ?></div>
                                        <small class="text-muted">Pedidos</small>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="h4 text-info"><?php echo $stats->getDashboardStats()['total_users']; ?></div>
                                        <small class="text-muted">Usuários</small>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="h4 text-warning"><?php echo formatPrice($stats->getDashboardStats()['total_revenue']); ?></div>
                                        <small class="text-muted">Receita</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validação de senha em tempo real
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            
            if (newPassword !== confirmPassword) {
                this.setCustomValidity('As senhas não coincidem');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
