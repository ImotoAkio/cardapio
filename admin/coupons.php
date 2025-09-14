<?php
// =====================================================
// 🍃 TEMPERO E CAFÉ - GESTÃO DE CUPONS
// =====================================================

require_once 'config.php';

// Verificar login
$auth->requireLogin();

// Obter alerta se houver
$alert = getAlert();

// Processar ações
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Processar exclusão
if ($action === 'delete' && $id) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("DELETE FROM coupons WHERE id = ?");
    if ($stmt->execute([$id])) {
        showAlert('Cupom excluído com sucesso!', 'success');
    } else {
        showAlert('Erro ao excluir cupom!', 'danger');
    }
    redirect('coupons.php');
}

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'code' => strtoupper(sanitizeInput($_POST['code'])),
        'name' => sanitizeInput($_POST['name']),
        'description' => sanitizeInput($_POST['description']),
        'type' => $_POST['type'],
        'value' => (float)$_POST['value'],
        'min_order_amount' => (float)$_POST['min_order_amount'],
        'max_discount' => !empty($_POST['max_discount']) ? (float)$_POST['max_discount'] : null,
        'usage_limit' => !empty($_POST['usage_limit']) ? (int)$_POST['usage_limit'] : null,
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
        'starts_at' => !empty($_POST['starts_at']) ? $_POST['starts_at'] : null,
        'expires_at' => !empty($_POST['expires_at']) ? $_POST['expires_at'] : null
    ];
    
    $db = Database::getInstance()->getConnection();
    
    if ($action === 'create') {
        $stmt = $db->prepare("
            INSERT INTO coupons (code, name, description, type, value, min_order_amount, 
                               max_discount, usage_limit, is_active, starts_at, expires_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        if ($stmt->execute([
            $data['code'], $data['name'], $data['description'], $data['type'], 
            $data['value'], $data['min_order_amount'], $data['max_discount'], 
            $data['usage_limit'], $data['is_active'], $data['starts_at'], $data['expires_at']
        ])) {
            showAlert('Cupom criado com sucesso!', 'success');
        } else {
            showAlert('Erro ao criar cupom!', 'danger');
        }
    } elseif ($action === 'edit' && $id) {
        $stmt = $db->prepare("
            UPDATE coupons SET
                code = ?, name = ?, description = ?, type = ?, value = ?, 
                min_order_amount = ?, max_discount = ?, usage_limit = ?, 
                is_active = ?, starts_at = ?, expires_at = ?
            WHERE id = ?
        ");
        
        if ($stmt->execute([
            $data['code'], $data['name'], $data['description'], $data['type'], 
            $data['value'], $data['min_order_amount'], $data['max_discount'], 
            $data['usage_limit'], $data['is_active'], $data['starts_at'], 
            $data['expires_at'], $id
        ])) {
            showAlert('Cupom atualizado com sucesso!', 'success');
        } else {
            showAlert('Erro ao atualizar cupom!', 'danger');
        }
    }
    
    redirect('coupons.php');
}

// Obter cupons para listagem
$db = Database::getInstance()->getConnection();
$stmt = $db->query("
    SELECT * FROM coupons 
    ORDER BY created_at DESC
");
$coupons = $stmt->fetchAll();

// Obter cupom específico se necessário
$coupon = null;
if ($action === 'edit' && $id) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM coupons WHERE id = ?");
    $stmt->execute([$id]);
    $coupon = $stmt->fetch();
    
    if (!$coupon) {
        showAlert('Cupom não encontrado!', 'danger');
        redirect('coupons.php');
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Cupons</title>
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
        
        .coupon-card {
            border: 2px dashed var(--primary-color);
            background: linear-gradient(135deg, #fff9e6, #ffffff);
        }
        
        .coupon-code {
            font-family: 'Courier New', monospace;
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary-color);
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
                    <a class="nav-link" href="orders.php">
                        <i class="bi bi-cart-check me-2"></i>Pedidos
                    </a>
                    <a class="nav-link" href="users.php">
                        <i class="bi bi-people me-2"></i>Usuários
                    </a>
                    <a class="nav-link active" href="coupons.php">
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

                <!-- Título e Botões -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-ticket-perforated me-2"></i>Gestão de Cupons</h2>
                    <div>
                        <a href="coupons.php?action=create" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Novo Cupom
                        </a>
                    </div>
                </div>

                <?php if ($action === 'create' || $action === 'edit'): ?>
                    <!-- Formulário de Cupom -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-<?php echo $action === 'create' ? 'plus-circle' : 'pencil'; ?> me-2"></i>
                                <?php echo $action === 'create' ? 'Novo Cupom' : 'Editar Cupom'; ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="coupons.php?action=<?php echo $action; ?><?php echo $id ? '&id=' . $id : ''; ?>">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="code" class="form-label">Código do Cupom *</label>
                                            <input type="text" class="form-control" id="code" name="code" 
                                                   value="<?php echo $coupon['code'] ?? ''; ?>" 
                                                   placeholder="Ex: TEMPERO20" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Nome do Cupom *</label>
                                            <input type="text" class="form-control" id="name" name="name" 
                                                   value="<?php echo $coupon['name'] ?? ''; ?>" 
                                                   placeholder="Ex: Desconto Tempero e Café" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Descrição</label>
                                            <textarea class="form-control" id="description" name="description" rows="2"><?php echo $coupon['description'] ?? ''; ?></textarea>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="type" class="form-label">Tipo de Desconto *</label>
                                            <select class="form-select" id="type" name="type" required>
                                                <option value="percentage" <?php echo ($coupon['type'] ?? '') === 'percentage' ? 'selected' : ''; ?>>Porcentagem (%)</option>
                                                <option value="fixed" <?php echo ($coupon['type'] ?? '') === 'fixed' ? 'selected' : ''; ?>>Valor Fixo (R$)</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="value" class="form-label">Valor do Desconto *</label>
                                            <input type="number" class="form-control" id="value" name="value" 
                                                   step="0.01" value="<?php echo $coupon['value'] ?? ''; ?>" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="min_order_amount" class="form-label">Valor Mínimo do Pedido</label>
                                            <input type="number" class="form-control" id="min_order_amount" name="min_order_amount" 
                                                   step="0.01" value="<?php echo $coupon['min_order_amount'] ?? 0; ?>">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="max_discount" class="form-label">Desconto Máximo</label>
                                            <input type="number" class="form-control" id="max_discount" name="max_discount" 
                                                   step="0.01" value="<?php echo $coupon['max_discount'] ?? ''; ?>">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="usage_limit" class="form-label">Limite de Uso</label>
                                            <input type="number" class="form-control" id="usage_limit" name="usage_limit" 
                                                   value="<?php echo $coupon['usage_limit'] ?? ''; ?>" placeholder="Deixe vazio para ilimitado">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="starts_at" class="form-label">Data de Início</label>
                                            <input type="datetime-local" class="form-control" id="starts_at" name="starts_at" 
                                                   value="<?php echo $coupon['starts_at'] ? date('Y-m-d\TH:i', strtotime($coupon['starts_at'])) : ''; ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="expires_at" class="form-label">Data de Expiração</label>
                                            <input type="datetime-local" class="form-control" id="expires_at" name="expires_at" 
                                                   value="<?php echo $coupon['expires_at'] ? date('Y-m-d\TH:i', strtotime($coupon['expires_at'])) : ''; ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                               <?php echo ($coupon['is_active'] ?? 1) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="is_active">Cupom Ativo</label>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="coupons.php" class="btn btn-secondary">Cancelar</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>
                                        <?php echo $action === 'create' ? 'Criar Cupom' : 'Atualizar Cupom'; ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Lista de Cupons -->
                    <div class="row">
                        <?php foreach ($coupons as $coupon): ?>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card coupon-card h-100">
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <div class="coupon-code"><?php echo $coupon['code']; ?></div>
                                            <h6 class="mt-2"><?php echo $coupon['name']; ?></h6>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <p class="text-muted small"><?php echo $coupon['description']; ?></p>
                                        </div>
                                        
                                        <div class="row text-center mb-3">
                                            <div class="col-6">
                                                <div class="fw-bold text-primary">
                                                    <?php echo $coupon['type'] === 'percentage' ? $coupon['value'] . '%' : formatPrice($coupon['value']); ?>
                                                </div>
                                                <small class="text-muted">Desconto</small>
                                            </div>
                                            <div class="col-6">
                                                <div class="fw-bold text-success"><?php echo $coupon['used_count']; ?></div>
                                                <small class="text-muted">Usado</small>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-<?php echo $coupon['is_active'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $coupon['is_active'] ? 'Ativo' : 'Inativo'; ?>
                                            </span>
                                            
                                            <div class="btn-group" role="group">
                                                <a href="coupons.php?action=edit&id=<?php echo $coupon['id']; ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="coupons.php?action=delete&id=<?php echo $coupon['id']; ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Tem certeza que deseja excluir este cupom?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
