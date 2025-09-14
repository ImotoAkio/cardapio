<?php
// =====================================================
// 📦 API DETALHES DO PEDIDO - TEMPERO E CAFÉ
// =====================================================

// Suprimir warnings de headers
error_reporting(E_ERROR | E_PARSE);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Limpar qualquer output anterior
while (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: application/json');

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não logado']);
    exit();
}

require_once 'includes/database.php';

try {
    $user_id = $_SESSION['user_id'];
    $db = Database::getInstance()->getConnection();
    
    // Obter ID do pedido
    $orderId = $_GET['orderId'] ?? null;
    
    if (!$orderId) {
        echo json_encode(['success' => false, 'message' => 'ID do pedido não fornecido']);
        exit();
    }
    
    // Verificar se o pedido pertence ao usuário
    $stmt = $db->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$orderId, $user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Pedido não encontrado']);
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
    function getStatusTextAPI($status) {
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
    function getStatusColorAPI($status) {
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
    function formatPriceAPI($price) {
        return 'R$ ' . number_format($price, 2, ',', '.');
    }
    
    // Função para obter primeira imagem do produto
    function getFirstProductImageAPI($imagesJson) {
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
    
    echo json_encode([
        'success' => true,
        'order' => [
            'id' => $order['id'],
            'status' => $order['status'],
            'status_text' => getStatusTextAPI($order['status']),
            'status_color' => getStatusColorAPI($order['status']),
            'created_at' => date('d/m/Y H:i', strtotime($order['created_at'])),
            'total' => $total,
            'total_formatted' => formatPriceAPI($total),
            'items' => array_map(function($item) {
                return [
                    'id' => $item['id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['product_price'],
                    'price_formatted' => formatPriceAPI($item['product_price']),
                    'subtotal' => $item['total'],
                    'subtotal_formatted' => formatPriceAPI($item['total']),
                    'image' => getFirstProductImageAPI($item['images'])
                ];
            }, $items)
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()]);
}
?>
