<?php
// =====================================================
// 🍃 TEMPERO E CAFÉ - LOGOUT
// =====================================================

require_once 'config.php';

// Fazer logout
$auth->logout();

// Redirecionar para login
redirect('login.php');
