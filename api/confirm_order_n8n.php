<?php
// =====================================================
// 🔗 API N8N WEBHOOK - TEMPERO E CAFÉ
// =====================================================

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Suprimir warnings de headers
error_reporting(E_ERROR | E_PARSE);

// Limpar qualquer output anterior
while (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: application/json');

require_once 'includes/database.php';

/**
 * Formatar número de telefone para o padrão brasileiro (55ddd87991682773)
 */
function formatPhoneNumber($phone) {
    if (empty($phone)) {
        return '';
    }
    
    // Remover todos os caracteres não numéricos
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Se já começar com 55, retornar como está
    if (strpos($phone, '55') === 0) {
        return $phone;
    }
    
    // Se começar com 0, remover o 0
    if (strpos($phone, '0') === 0) {
        $phone = substr($phone, 1);
    }
    
    // Adicionar código do país 55 se não tiver
    if (strpos($phone, '55') !== 0) {
        $phone = '55' . $phone;
    }
    
    return $phone;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Verificar se é uma requisição POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Método não permitido']);
        exit();
    }
    
    // Obter dados do pedido
    $orderId = $_POST['order_id'] ?? null;
    
    if (!$orderId) {
        echo json_encode(['success' => false, 'message' => 'ID do pedido não fornecido']);
        exit();
    }
    
    // Buscar dados do pedido
    $stmt = $db->prepare("
        SELECT o.*, u.full_name, u.email, u.phone
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.id = ?
    ");
    $stmt->execute([$orderId]);
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
    $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Estrutura fixa e simples do JSON
    $n8nData = [
        'nome_usuario' => $order['full_name'],
        'produtos_comprados' => [],
        'valor_total' => floatval($order['total']),
        'status_compra' => $order['status']
    ];
    
    // Adicionar produtos comprados
    foreach ($orderItems as $item) {
        $n8nData['produtos_comprados'][] = $item['product_name'];
    }
    
    // Enviar para o webhook de produção do n8n (GET request com parâmetros separados)
    $webhookUrl = 'https://webhook.echo.dev.br/webhook/8cea05f1-e082-45ea-83ca-f80809af9cfd';
    
    // Converter produtos para string separada por vírgulas
    $produtosString = implode(',', $n8nData['produtos_comprados']);
    
    // Enviar dados como parâmetros de query separados
    $queryParams = http_build_query([
        'nome_usuario' => $n8nData['nome_usuario'],
        'produtos_comprados' => $produtosString,
        'valor_total' => $n8nData['valor_total'],
        'status_compra' => $n8nData['status_compra']
    ]);
    
    $url = $webhookUrl . '?' . $queryParams;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Tempero-e-Cafe/1.0'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($curlError) {
        throw new Exception('Erro cURL: ' . $curlError);
    }
    
    if ($httpCode >= 200 && $httpCode < 300) {
        // Sucesso - atualizar status do pedido para 'confirmed'
        $stmt = $db->prepare("UPDATE orders SET status = 'confirmed', updated_at = NOW() WHERE id = ?");
        $stmt->execute([$orderId]);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Pedido confirmado e enviado para n8n com sucesso',
            'order_id' => $orderId,
            'n8n_response' => $response,
            'http_code' => $httpCode
        ]);
    } else {
        throw new Exception('Erro HTTP: ' . $httpCode . ' - Resposta: ' . $response);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Erro ao enviar para n8n: ' . $e->getMessage()
    ]);
}
?>
