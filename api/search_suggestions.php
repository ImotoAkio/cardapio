<?php
// =====================================================
// 🔍 API DE SUGESTÕES DE BUSCA
// =====================================================

require_once 'includes/database.php';

// Configurar cabeçalhos para JSON
header('Content-Type: application/json');

try {
    $query = trim($_GET['q'] ?? '');
    
    if (strlen($query) < 2) {
        echo json_encode([
            'success' => true,
            'suggestions' => []
        ]);
        exit;
    }
    
    $db = Database::getInstance()->getConnection();
    $searchTerm = "%{$query}%";
    
    // Buscar sugestões de produtos
    $stmt = $db->prepare("
        SELECT DISTINCT name as text, 'product' as type
        FROM products 
        WHERE is_active = 1 AND name LIKE ?
        ORDER BY name
        LIMIT 5
    ");
    $stmt->execute([$searchTerm]);
    $productSuggestions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Buscar sugestões de categorias
    $stmt = $db->prepare("
        SELECT DISTINCT name as text, 'category' as type
        FROM categories 
        WHERE name LIKE ?
        ORDER BY name
        LIMIT 3
    ");
    $stmt->execute([$searchTerm]);
    $categorySuggestions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Combinar sugestões
    $suggestions = array_merge($productSuggestions, $categorySuggestions);
    
    // Limitar total de sugestões
    $suggestions = array_slice($suggestions, 0, 8);
    
    echo json_encode([
        'success' => true,
        'suggestions' => $suggestions
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao buscar sugestões: ' . $e->getMessage()
    ]);
}
?>
