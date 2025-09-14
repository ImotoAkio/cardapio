<?php
// =====================================================
// 📦 PÁGINA DETALHES DO PEDIDO - TEMPERO E CAFÉ
// =====================================================

session_start();
if (!isset($_SESSION['user_id'])) {
    echo '<div class="text-center"><p class="text-danger">Usuário não logado</p></div>';
    exit();
}

require_once 'includes/database.php';

try {
    $user_id = $_SESSION['user_id'];
    $db = Database::getInstance()->getConnection();
    
    // Obter ID do pedido
    $orderId = $_GET['orderId'] ?? null;
    
    if (!$orderId) {
        echo '<div class="text-center"><p class="text-danger">ID do pedido não fornecido</p></div>';
        exit();
    }
    
    // Verificar se o pedido pertence ao usuário
    $stmt = $db->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$orderId, $user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        echo '<div class="text-center"><p class="text-danger">Pedido não encontrado</p></div>';
        exit();
    }
    
    // Buscar itens do pedido
    $stmt = $db->prepare("
        SELECT oi.*, p.name as product_name, p.images
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
        ORDER BY oi.created_at
    ");
    $stmt->execute([$orderId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calcular total
    $total = 0;
    foreach ($items as $item) {
        $total += $item['total'];
    }
    
    // Função para obter status em português
    function getStatusTextPage($status) {
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
    function getStatusColorPage($status) {
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
    
    // Função para formatar preço
    function formatPricePage($price) {
        return 'R$ ' . number_format($price, 2, ',', '.');
    }
    
    // Função para obter primeira imagem do produto
    function getFirstProductImagePage($imagesJson) {
        if (empty($imagesJson)) {
            return 'dist/img/product/default.png';
        }
        
        $images = json_decode($imagesJson, true);
        if (!is_array($images) || empty($images)) {
            return 'dist/img/product/default.png';
        }
        
        $firstImage = $images[0];
        if (strpos($firstImage, 'dist/') === 0) {
            return $firstImage;
        }
        
        return 'dist/img/product/' . basename($firstImage);
    }
    
    // Exibir detalhes do pedido
    ?>
    <div class="order-details">
        <div class="row mb-3">
            <div class="col-md-6">
                <h6>Pedido #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></h6>
                <p class="text-muted mb-0">Data: <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
            </div>
            <div class="col-md-6 text-end">
                <span class="badge bg-<?php echo getStatusColorPage($order['status']); ?>">
                    <?php echo getStatusTextPage($order['status']); ?>
                </span>
            </div>
        </div>
        
        <h6>Itens do Pedido:</h6>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Qtd</th>
                        <th>Preço</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="<?php echo getFirstProductImagePage($item['images']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                     class="me-2" 
                                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                <span><?php echo htmlspecialchars($item['product_name']); ?></span>
                            </div>
                        </td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo formatPricePage($item['product_price']); ?></td>
                        <td><strong><?php echo formatPricePage($item['total']); ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="table-active">
                        <th colspan="3">Total:</th>
                        <th><?php echo formatPricePage($total); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <?php
    
} catch (Exception $e) {
    echo '<div class="text-center"><p class="text-danger">Erro interno: ' . htmlspecialchars($e->getMessage()) . '</p></div>';
}
?>
