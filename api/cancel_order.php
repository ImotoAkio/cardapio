<?php
// =====================================================
// 🚫 API CANCELAR PEDIDO - TEMPERO E CAFÉ
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
    
    // Obter dados do POST
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['orderId'])) {
        echo json_encode(['success' => false, 'message' => 'ID do pedido não fornecido']);
        exit();
    }
    
    $orderId = $input['orderId'];
    
    // Verificar se o pedido pertence ao usuário
    $stmt = $db->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$orderId, $user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Pedido não encontrado']);
        exit();
    }
    
    // Verificar se o pedido pode ser cancelado
    if (!in_array($order['status'], ['pending', 'confirmed', 'processing'])) {
        echo json_encode(['success' => false, 'message' => 'Este pedido não pode ser cancelado']);
        exit();
    }
    
    // Cancelar o pedido
    $stmt = $db->prepare("UPDATE orders SET status = 'cancelled', updated_at = NOW() WHERE id = ?");
    $stmt->execute([$orderId]);
    
    echo json_encode(['success' => true, 'message' => 'Pedido cancelado com sucesso']);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()]);
}
?>
