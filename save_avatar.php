<?php
require_once 'includes/database.php';

// Verificar se usuário está logado
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Usuário não logado']);
    exit;
}

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$db = Database::getInstance()->getConnection();
$userId = $_SESSION['user_id'];
$avatar = $_POST['avatar'] ?? '';

// Validar avatar
$validAvatars = ['1.png', '2.png', '3.png', '4.png'];
if (!in_array($avatar, $validAvatars)) {
    echo json_encode(['success' => false, 'message' => 'Avatar inválido']);
    exit;
}

try {
    // Atualizar avatar do usuário
    $stmt = $db->prepare("UPDATE users SET avatar = ? WHERE id = ?");
    $stmt->execute([$avatar, $userId]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Avatar atualizado com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nenhuma alteração realizada']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar avatar: ' . $e->getMessage()]);
}
?>
