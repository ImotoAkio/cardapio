<?php
// =====================================================
// 🔐 API ALTERAR SENHA - TEMPERO E CAFÉ
// =====================================================

session_start();
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
    
    if (!$input || !isset($input['currentPassword']) || !isset($input['newPassword'])) {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
        exit();
    }
    
    $currentPassword = $input['currentPassword'];
    $newPassword = $input['newPassword'];
    
    // Validar nova senha
    if (strlen($newPassword) < 6) {
        echo json_encode(['success' => false, 'message' => 'A nova senha deve ter pelo menos 6 caracteres']);
        exit();
    }
    
    // Buscar usuário
    $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Usuário não encontrado']);
        exit();
    }
    
    // Verificar senha atual
    if (!password_verify($currentPassword, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Senha atual incorreta']);
        exit();
    }
    
    // Hash da nova senha
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    // Atualizar senha
    $stmt = $db->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$hashedPassword, $user_id]);
    
    echo json_encode(['success' => true, 'message' => 'Senha alterada com sucesso']);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()]);
}
?>
