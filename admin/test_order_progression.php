<?php
// =====================================================
// 🧪 TESTE WEBHOOK PROGRESSÃO DE PEDIDOS
// =====================================================

require_once 'config.php';
require_once 'includes/order_progression_helper.php';

// Verificar login
$auth->requireLogin();

// Obter conexão com o banco de dados
$db = Database::getInstance()->getConnection();

$message = '';
$messageType = '';
$testResults = [];

// Processar testes
if ($_POST['action'] ?? '') {
    switch ($_POST['action']) {
        case 'test_connection':
            $testResults['connection'] = OrderProgressionHelper::testConnection();
            break;
            
        case 'test_status_update':
            $orderId = $_POST['order_id'] ?? null;
            $newStatus = $_POST['new_status'] ?? 'confirmed';
            
            if ($orderId) {
                try {
                    $result = OrderProgressionHelper::sendStatusUpdate($orderId, $newStatus, $db);
                    $testResults['status_update'] = $result;
                } catch (Exception $e) {
                    $testResults['status_update'] = [
                        'success' => false,
                        'error' => $e->getMessage()
                    ];
                }
            } else {
                $testResults['status_update'] = [
                    'success' => false,
                    'error' => 'ID do pedido não fornecido'
                ];
            }
            break;
    }
}

// Buscar pedidos para teste
$orders = $orderManager->getAllOrders(1, 10);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Teste Webhook Progressão</title>
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
            <div class="col-md-3 col-lg-2 sidebar p-0" style="background: white; min-height: calc(100vh - 76px); box-shadow: 2px 0 10px rgba(0,0,0,0.1);">
                <nav class="nav flex-column pt-3">
                    <a class="nav-link" href="dashboard.php">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="orders.php">
                        <i class="bi bi-cart-check me-2"></i>Pedidos
                    </a>
                    <a class="nav-link active" href="test_order_progression.php">
                        <i class="bi bi-bug me-2"></i>Teste Webhook
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10" style="padding: 30px;">
                <!-- Título -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-bug me-2"></i>Teste Webhook Progressão de Pedidos</h2>
                    <div>
                        <a href="orders.php" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left me-2"></i>Voltar para Pedidos
                        </a>
                    </div>
                </div>

                <!-- Informações do Webhook -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-link-45deg me-2"></i>Informações do Webhook</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <p><strong>URL:</strong> <code><?php echo OrderProgressionHelper::getWebhookUrl(); ?></code></p>
                                <p><strong>Método:</strong> POST (JSON)</p>
                                <p><strong>Timeout:</strong> 30 segundos</p>
                                <p><strong>Status:</strong> <span class="badge bg-success">Produção</span></p>
                            </div>
                            <div class="col-md-4 text-end">
                                <span class="badge bg-info">
                                    <i class="bi bi-shield-check me-1"></i>
                                    Proteção Anti-Duplicação Ativa
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Testes Disponíveis -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">🧪 Testes Disponíveis</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="bi bi-wifi" style="font-size: 32px; color: #007bff;"></i>
                                        <h6 class="mt-2">Teste de Conexão</h6>
                                        <p class="small text-muted">Verifica se consegue acessar o webhook</p>
                                        <div class="small text-info mb-2">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Dados fixos de teste
                                        </div>
                                        <form method="POST">
                                            <input type="hidden" name="action" value="test_connection">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="bi bi-play me-1"></i>
                                                Executar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="bi bi-arrow-right-circle" style="font-size: 32px; color: #28a745;"></i>
                                        <h6 class="mt-2">Teste de Progressão</h6>
                                        <p class="small text-muted">Simula atualização de status</p>
                                        <div class="small text-success mb-2">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Dados reais de pedido
                                        </div>
                                        <form method="POST">
                                            <input type="hidden" name="action" value="test_status_update">
                                            <div class="mb-2">
                                                <select name="order_id" class="form-select form-select-sm" required>
                                                    <option value="">Selecione um pedido</option>
                                                    <?php foreach ($orders as $order): ?>
                                                        <option value="<?php echo $order['id']; ?>">
                                                            #<?php echo $order['order_number']; ?> - <?php echo $order['full_name']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-2">
                                                <select name="new_status" class="form-select form-select-sm">
                                                    <option value="confirmed">Confirmado</option>
                                                    <option value="processing">Processando</option>
                                                    <option value="shipped">Enviado</option>
                                                    <option value="delivered">Entregue</option>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="bi bi-play me-1"></i>
                                                Executar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="bi bi-code" style="font-size: 32px; color: #6f42c1;"></i>
                                        <h6 class="mt-2">Estrutura dos Dados</h6>
                                        <p class="small text-muted">Mostra como os dados são estruturados</p>
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="showDataStructure()">
                                            <i class="bi bi-eye me-1"></i>
                                            Ver Estrutura
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resultados dos Testes -->
                <?php if (!empty($testResults)): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">📊 Resultados dos Testes</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($testResults as $testName => $result): ?>
                        <div class="mb-3">
                            <h6>
                                <?php if ($testName === 'connection'): ?>
                                    <i class="bi bi-wifi me-2"></i>Teste de Conexão
                                <?php elseif ($testName === 'status_update'): ?>
                                    <i class="bi bi-arrow-right-circle me-2"></i>Teste de Progressão
                                <?php endif; ?>
                            </h6>
                            
                            <?php if ($result['success']): ?>
                                <div class="alert alert-success">
                                    <h6 class="alert-heading">✅ Sucesso!</h6>
                                    <p><strong>Status HTTP:</strong> <?php echo $result['http_code']; ?></p>
                                    <p><strong>Resposta:</strong> <?php echo htmlspecialchars($result['response']); ?></p>
                                    <?php if (isset($result['data_sent'])): ?>
                                        <p><strong>Dados enviados:</strong></p>
                                        <pre class="bg-light p-2 rounded"><?php echo json_encode($result['data_sent'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?></pre>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-danger">
                                    <h6 class="alert-heading">❌ Erro!</h6>
                                    <p><strong>Erro:</strong> <?php echo htmlspecialchars($result['message'] ?? $result['error'] ?? 'Erro desconhecido'); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Modal Estrutura dos Dados -->
                <div class="modal fade" id="dataStructureModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">📋 Estrutura dos Dados do Webhook</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <h6>Dados enviados para o webhook:</h6>
                                <pre class="bg-light p-3 rounded">{
    "pedido_id": 123,
    "numero_pedido": "ORD20241201001",
    "nome_cliente": "João Silva",
    "email_cliente": "joao@exemplo.com",
    "telefone_cliente": "5511999999999",
    "status_anterior": "pending",
    "status_novo": "confirmed",
    "valor_total": 89.50,
    "produtos": [
        {
            "nome": "Café Especial",
            "quantidade": 2,
            "preco_unitario": 15.00,
            "total": 30.00
        }
    ],
    "data_atualizacao": "2024-12-01 14:30:00",
    "tipo_evento": "status_update"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showDataStructure() {
            const modal = new bootstrap.Modal(document.getElementById('dataStructureModal'));
            modal.show();
        }
        
        // Sistema de proteção contra múltiplos cliques
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const button = form.querySelector('button[type="submit"]');
                    const originalText = button.innerHTML;
                    
                    // Desabilitar botão e mostrar loading
                    button.disabled = true;
                    button.innerHTML = '<i class="bi bi-loader me-1"></i>Processando...';
                    
                    // Reabilitar após 15 segundos
                    setTimeout(() => {
                        button.disabled = false;
                        button.innerHTML = originalText;
                    }, 15000);
                });
            });
        });
    </script>
</body>
</html>
