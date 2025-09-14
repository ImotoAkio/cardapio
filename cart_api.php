<?php
// =====================================================
// 🛒 TEMPERO E CAFÉ - API DO CARRINHO
// =====================================================

require_once 'includes/database.php';

// Configurar cabeçalhos para JSON
header('Content-Type: application/json');

// Iniciar sessão se não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Obter ID da sessão
$sessionId = session_id();

// Obter user_id da sessão se estiver logado
$userId = $_SESSION['user_id'] ?? null;

// Criar instância do CartManager
$cartManager = new CartManager(Database::getInstance()->getConnection());

// Obter método HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Obter ação
$action = $_GET['action'] ?? '';

try {
    switch ($method) {
        case 'POST':
            switch ($action) {
                case 'add':
                    $productId = (int)($_POST['product_id'] ?? 0);
                    $quantity = (int)($_POST['quantity'] ?? 1);
                    
                    if ($productId <= 0) {
                        throw new Exception('ID do produto inválido');
                    }
                    
                    $success = $cartManager->addToCart($sessionId, $productId, $quantity, $userId);
                    
                    if ($success) {
                        $itemCount = $cartManager->getCartItemCount($sessionId);
                        echo json_encode([
                            'success' => true,
                            'message' => 'Produto adicionado ao carrinho!',
                            'item_count' => $itemCount
                        ]);
                    } else {
                        throw new Exception('Erro ao adicionar produto ao carrinho');
                    }
                    break;
                    
                case 'update':
                    $productId = (int)($_POST['product_id'] ?? 0);
                    $quantity = (int)($_POST['quantity'] ?? 0);
                    
                    if ($productId <= 0) {
                        throw new Exception('ID do produto inválido');
                    }
                    
                    $success = $cartManager->updateQuantity($sessionId, $productId, $quantity);
                    
                    if ($success) {
                        $cartTotal = $cartManager->getCartTotal($sessionId);
                        echo json_encode([
                            'success' => true,
                            'message' => 'Quantidade atualizada!',
                            'cart_total' => $cartTotal
                        ]);
                    } else {
                        throw new Exception('Erro ao atualizar quantidade');
                    }
                    break;
                    
                case 'remove':
                    $productId = (int)($_POST['product_id'] ?? 0);
                    
                    if ($productId <= 0) {
                        throw new Exception('ID do produto inválido');
                    }
                    
                    $success = $cartManager->removeFromCart($sessionId, $productId);
                    
                    if ($success) {
                        $itemCount = $cartManager->getCartItemCount($sessionId);
                        echo json_encode([
                            'success' => true,
                            'message' => 'Produto removido do carrinho!',
                            'item_count' => $itemCount
                        ]);
                    } else {
                        throw new Exception('Erro ao remover produto do carrinho');
                    }
                    break;
                    
                case 'clear':
                    $success = $cartManager->clearCart($sessionId);
                    
                    if ($success) {
                        echo json_encode([
                            'success' => true,
                            'message' => 'Carrinho limpo!',
                            'item_count' => 0
                        ]);
                    } else {
                        throw new Exception('Erro ao limpar carrinho');
                    }
                    break;
                    
                default:
                    throw new Exception('Ação não encontrada');
            }
            break;
            
        case 'GET':
            switch ($action) {
                case 'count':
                    $itemCount = $cartManager->getCartItemCount($sessionId);
                    echo json_encode([
                        'success' => true,
                        'item_count' => $itemCount
                    ]);
                    break;
                    
                case 'items':
                    $items = $cartManager->getCartItems($sessionId);
                    $cartTotal = $cartManager->getCartTotal($sessionId);
                    
                    echo json_encode([
                        'success' => true,
                        'items' => $items,
                        'cart_total' => $cartTotal
                    ]);
                    break;
                    
                default:
                    throw new Exception('Ação não encontrada');
            }
            break;
            
        default:
            throw new Exception('Método HTTP não suportado');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
