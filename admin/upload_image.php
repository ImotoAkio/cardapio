<?php
// =====================================================
// 🍃 TEMPERO E CAFÉ - UPLOAD DE IMAGENS
// =====================================================

require_once 'config.php';

// Verificar login
$auth->requireLogin();

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Verificar se há arquivo
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Nenhum arquivo enviado']);
    exit;
}

$file = $_FILES['image'];
$type = $_POST['type'] ?? 'product'; // product ou category

// Validar tipo de arquivo
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Tipo de arquivo não permitido']);
    exit;
}

// Validar tamanho
if ($file['size'] > MAX_FILE_SIZE) {
    echo json_encode(['success' => false, 'message' => 'Arquivo muito grande']);
    exit;
}

// Criar diretório se não existir
$uploadDir = '../dist/img/' . ($type === 'product' ? 'product' : 'category') . '/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Gerar nome único
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$fileName = uniqid() . '_' . time() . '.' . $extension;
$filePath = $uploadDir . $fileName;

// Mover arquivo
if (move_uploaded_file($file['tmp_name'], $filePath)) {
    // Retornar URL relativa
    $relativePath = 'img/' . ($type === 'product' ? 'product' : 'category') . '/' . $fileName;
    echo json_encode([
        'success' => true, 
        'message' => 'Imagem enviada com sucesso',
        'url' => $relativePath,
        'filename' => $fileName
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao fazer upload']);
}
