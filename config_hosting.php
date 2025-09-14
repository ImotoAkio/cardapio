<?php
// =====================================================
// 🍃 TEMPERO E CAFÉ - CONFIGURAÇÃO PARA HOSPEDAGEM
// =====================================================

// Configurações do banco de dados - HOSPEDAGEM
define('DB_HOST', 'localhost'); // Altere para o host da hospedagem
define('DB_NAME', 'seu_banco'); // Altere para o nome do banco
define('DB_USER', 'seu_usuario'); // Altere para o usuário
define('DB_PASS', 'sua_senha'); // Altere para a senha
define('DB_CHARSET', 'utf8mb4');

// Configurações da aplicação
define('APP_NAME', 'Tempero e Café');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'production');
define('APP_DEBUG', false);

// Configurações de URL - HOSPEDAGEM
define('APP_URL', 'https://seudominio.com'); // Altere para seu domínio
define('APP_BASE_PATH', '/'); // Altere se estiver em subpasta

// Configurações de Webhook N8N
define('N8N_WEBHOOK_URL', 'https://webhook.echo.dev.br/webhook/8cea05f1-e082-45ea-83ca-f80809af9cfd');
define('N8N_WEBHOOK_TEST_URL', 'https://n8n.echo.dev.br/webhook-test/8cea05f1-e082-45ea-83ca-f0809af9cfd');
define('N8N_WEBHOOK_PROGRESSION_URL', 'https://n8n.echo.dev.br/webhook-test/e8a2f4db-eefd-498e-9547-a0200442c108');

// Configurações de Upload
define('UPLOAD_MAX_SIZE', 5242880); // 5MB
define('UPLOAD_ALLOWED_TYPES', 'jpg,jpeg,png,gif,webp');

// Configurações de Sessão
define('SESSION_LIFETIME', 7200); // 2 horas
define('SESSION_SECURE', true); // HTTPS obrigatório
define('SESSION_HTTPONLY', true);

// Configurações de Cache
define('CACHE_ENABLED', true);
define('CACHE_LIFETIME', 3600); // 1 hora

// Configurações de Log
define('LOG_LEVEL', 'error');
define('LOG_FILE', 'logs/app.log');

// Configurações de Segurança
define('APP_KEY', 'base64:your-app-key-here'); // Gere uma chave única
define('CSRF_TOKEN_LIFETIME', 3600);
