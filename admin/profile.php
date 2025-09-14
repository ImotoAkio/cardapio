<?php
// =====================================================
// 🍃 TEMPERO E CAFÉ - PERFIL DO ADMINISTRADOR
// =====================================================

require_once 'config.php';

// Verificar login
$auth->requireLogin();

// Obter alerta se houver
$alert = getAlert();

// Obter dados do usuário
$user = $auth->getUser();

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        $full_name = sanitizeInput($_POST['full_name']);
        $email = sanitizeInput($_POST['email']);
        $phone = sanitizeInput($_POST['phone']);
        
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?");
        if ($stmt->execute([$full_name, $email, $phone, $user['id']])) {
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
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$user['id']]);
            $userData = $stmt->fetch();
            
            if (password_verify($current_password, $userData['password'])) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
                
                if ($stmt->execute([$hashed_password, $user['id']])) {
                    showAlert('Senha alterada com sucesso!', 'success');
                } else {
                    showAlert('Erro ao alterar senha!', 'danger');
                }
            } else {
                showAlert('Senha atual incorreta!', 'danger');
            }
        }
    }
    
    redirect('profile.php');
}

// Obter estatísticas do usuário
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("
    SELECT 
        COUNT(DISTINCT p.id) as products_created,
        COUNT(DISTINCT o.id) as orders_managed,
        COUNT(DISTINCT c.id) as categories_created
    FROM users u
    LEFT JOIN products p ON u.id = 1  -- Admin sempre tem ID 1
    LEFT JOIN orders o ON u.id = 1
    LEFT JOIN categories c ON u.id = 1
    WHERE u.id = ?
");
$stmt->execute([$user['id']]);
$userStats = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Perfil</title>
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
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
            font-weight: bold;
            margin: 0 auto;
            box-shadow: 0 10px 30px rgba(211, 167, 78, 0.3);
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border: none;
            transition: transform 0.3s ease;
            text-align: center;
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
            margin: 0 auto 15px;
        }
        
        .stat-icon.products { background: linear-gradient(45deg, #28a745, #20c997); }
        .stat-icon.orders { background: linear-gradient(45deg, #007bff, #6610f2); }
        .stat-icon.categories { background: linear-gradient(45deg, #fd7e14, #e83e8c); }
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
                    <h2><i class="bi bi-person me-2"></i>Meu Perfil</h2>
                    <div>
                        <a href="dashboard.php" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left me-2"></i>Voltar ao Dashboard
                        </a>
                    </div>
                </div>

                <!-- Informações do Perfil -->
                <div class="row mb-4">
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="profile-avatar">
                                    <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                </div>
                                <h4 class="mt-3"><?php echo $user['full_name']; ?></h4>
                                <p class="text-muted">@<?php echo $user['username']; ?></p>
                                <p class="text-muted"><?php echo $user['email']; ?></p>
                                <?php if ($user['phone']): ?>
                                    <p class="text-muted"><i class="bi bi-phone me-1"></i><?php echo $user['phone']; ?></p>
                                <?php endif; ?>
                                
                                <div class="mt-3">
                                    <span class="badge bg-success">Administrador</span>
                                    <span class="badge bg-primary">Ativo</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <!-- Estatísticas -->
                        <div class="row mb-4">
                            <div class="col-md-4 mb-3">
                                <div class="stat-card">
                                    <div class="stat-icon products">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                    <h3 class="mb-0"><?php echo $userStats['products_created']; ?></h3>
                                    <p class="text-muted mb-0">Produtos Criados</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="stat-card">
                                    <div class="stat-icon orders">
                                        <i class="bi bi-cart-check"></i>
                                    </div>
                                    <h3 class="mb-0"><?php echo $userStats['orders_managed']; ?></h3>
                                    <p class="text-muted mb-0">Pedidos Gerenciados</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="stat-card">
                                    <div class="stat-icon categories">
                                        <i class="bi bi-tags"></i>
                                    </div>
                                    <h3 class="mb-0"><?php echo $userStats['categories_created']; ?></h3>
                                    <p class="text-muted mb-0">Categorias Criadas</p>
                                </div>
                            </div>
                        </div>

                        <!-- Formulários -->
                        <div class="row">
                            <!-- Atualizar Perfil -->
                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="bi bi-person-gear me-2"></i>Atualizar Perfil</h5>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST" action="profile.php">
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
                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="bi bi-lock me-2"></i>Alterar Senha</h5>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST" action="profile.php">
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
