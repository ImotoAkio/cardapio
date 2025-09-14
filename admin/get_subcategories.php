<?php
// =====================================================
// 🍃 TEMPERO E CAFÉ - AJAX SUBCATEGORIAS
// =====================================================

require_once 'config.php';

// Verificar login
$auth->requireLogin();

// Obter conexão com o banco de dados
$db = Database::getInstance()->getConnection();

// Obter categoria ID
$categoryId = (int)($_GET['category_id'] ?? 0);

if ($categoryId > 0) {
    $stmt = $db->prepare("SELECT * FROM subcategories WHERE category_id = ? ORDER BY name");
    $stmt->execute([$categoryId]);
    $subcategories = $stmt->fetchAll();
    
    header('Content-Type: application/json');
    echo json_encode($subcategories);
} else {
    header('Content-Type: application/json');
    echo json_encode([]);
}
