<?php
// =====================================================
// 🍃 TEMPERO E CAFÉ - PROCESSAMENTO DE LOGIN
// =====================================================

require_once 'config.php';

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('login.php');
}

// Obter dados do formulário
$username = sanitizeInput($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Validações básicas
if (empty($username) || empty($password)) {
    showAlert('Por favor, preencha todos os campos.', 'danger');
    redirect('login.php');
}

// Tentar fazer login
if ($auth->login($username, $password)) {
    showAlert('Login realizado com sucesso!', 'success');
    redirect('dashboard.php');
} else {
    showAlert('Usuário ou senha incorretos.', 'danger');
    redirect('login.php');
}
