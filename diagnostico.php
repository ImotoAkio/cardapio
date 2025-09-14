<?php
// =====================================================
// 🔍 DIAGNÓSTICO DE ERRO - TEMPERO E CAFÉ
// =====================================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Diagnóstico de Erro</h1>";

echo "<h2>📋 Informações do Sistema</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Name: " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "<br>";

echo "<h2>📁 Verificação de Arquivos</h2>";
$files = [
    'home.php',
    'includes/database.php',
    'includes/n8n_helper.php',
    'includes/header.php',
    'includes/favicons.php',
    '.htaccess'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file - OK<br>";
    } else {
        echo "❌ $file - ERRO<br>";
    }
}

echo "<h2>🗄️ Teste de Conexão com Banco</h2>";
try {
    $pdo = new PDO('mysql:host=localhost;dbname=cardapio;charset=utf8mb4', 'root', '');
    echo "✅ Conexão com banco: OK<br>";
    
    // Teste de query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
    $result = $stmt->fetch();
    echo "✅ Query de teste: OK (Produtos: " . $result['count'] . ")<br>";
    
} catch (PDOException $e) {
    echo "❌ Erro de conexão: " . $e->getMessage() . "<br>";
}

echo "<h2>🔧 Teste de Includes</h2>";
try {
    require_once 'includes/database.php';
    echo "✅ includes/database.php - OK<br>";
} catch (Exception $e) {
    echo "❌ Erro em includes/database.php: " . $e->getMessage() . "<br>";
}

echo "<h2>📱 Teste de PWA</h2>";
if (file_exists('dist/manifest.json')) {
    echo "✅ Manifest PWA: OK<br>";
} else {
    echo "❌ Manifest PWA: ERRO<br>";
}

if (file_exists('dist/service-worker.js')) {
    echo "✅ Service Worker: OK<br>";
} else {
    echo "❌ Service Worker: ERRO<br>";
}

echo "<h2>🌐 Teste de URLs</h2>";
echo "Base URL: " . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . "<br>";

echo "<h2>📊 Logs de Erro</h2>";
$errorLog = ini_get('error_log');
if ($errorLog && file_exists($errorLog)) {
    echo "Log de erro: $errorLog<br>";
    $lastErrors = file_get_contents($errorLog);
    if ($lastErrors) {
        echo "<pre>" . htmlspecialchars(substr($lastErrors, -1000)) . "</pre>";
    }
} else {
    echo "Log de erro não encontrado<br>";
}

echo "<h2>✅ Diagnóstico Concluído</h2>";
echo "Se todos os testes acima estão OK, o problema pode estar em:<br>";
echo "1. Configuração do servidor web<br>";
echo "2. Permissões de arquivos<br>";
echo "3. Configuração do PHP<br>";
echo "4. Problema específico em algum arquivo<br>";
?>
