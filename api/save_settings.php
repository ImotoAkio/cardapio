<?php
// =====================================================
// 🔧 API SALVAR CONFIGURAÇÕES - TEMPERO E CAFÉ
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
    
    if (!$input || !isset($input['setting']) || !isset($input['value'])) {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
        exit();
    }
    
    $setting = $input['setting'];
    $value = $input['value'];
    
    // Validar configuração
    $allowedSettings = ['dark_mode', 'notifications', 'language'];
    if (!in_array($setting, $allowedSettings)) {
        echo json_encode(['success' => false, 'message' => 'Configuração inválida']);
        exit();
    }
    
    // Verificar se existe configuração para o usuário
    $stmt = $db->prepare("SELECT id FROM user_settings WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $existingSettings = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingSettings) {
        // Atualizar configuração existente
        $stmt = $db->prepare("UPDATE user_settings SET $setting = ?, updated_at = NOW() WHERE user_id = ?");
        $stmt->execute([$value, $user_id]);
    } else {
        // Criar nova configuração
        $stmt = $db->prepare("INSERT INTO user_settings (user_id, $setting, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$user_id, $value]);
    }
    
    echo json_encode(['success' => true, 'message' => 'Configuração salva com sucesso']);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()]);
}
?>
