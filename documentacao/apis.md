# 🔌 APIs e Endpoints - Tempero e Café

## 📋 Visão Geral

O sistema Tempero e Café possui uma arquitetura de APIs RESTful que permite comunicação entre frontend e backend, além de integração com sistemas externos. As APIs são desenvolvidas em PHP e seguem padrões REST.

## 🎯 Características das APIs

- **RESTful**: Seguem padrões REST
- **JSON**: Respostas em formato JSON
- **Autenticação**: Sistema de sessões PHP
- **Validação**: Validação de entrada robusta
- **Error Handling**: Tratamento de erros padronizado
- **CORS**: Suporte a Cross-Origin Resource Sharing

## 📁 Estrutura das APIs

```
📁 api/
├── 📄 search_suggestions.php    # Sugestões de busca
├── 📄 get_order_details.php     # Detalhes do pedido
├── 📄 cancel_order.php          # Cancelamento de pedido
├── 📄 save_settings.php         # Salvar configurações
├── 📄 change_password.php       # Alterar senha
└── 📄 confirm_order_n8n.php     # Confirmação via N8N

📄 cart_api.php                  # API do carrinho (raiz)
```

## 🛒 API do Carrinho (`cart_api.php`)

### Base URL
```
POST/GET /cart_api.php
```

### Endpoints

#### 1. **Adicionar Produto ao Carrinho**
```http
POST /cart_api.php?action=add
Content-Type: application/x-www-form-urlencoded

product_id=123&quantity=2
```

**Resposta de Sucesso:**
```json
{
    "success": true,
    "message": "Produto adicionado ao carrinho!",
    "item_count": 5
}
```

**Resposta de Erro:**
```json
{
    "success": false,
    "message": "ID do produto inválido"
}
```

#### 2. **Atualizar Quantidade**
```http
POST /cart_api.php?action=update
Content-Type: application/x-www-form-urlencoded

product_id=123&quantity=3
```

**Resposta:**
```json
{
    "success": true,
    "message": "Quantidade atualizada!",
    "cart_total": {
        "total": 89.70,
        "item_count": 3,
        "items": [...]
    }
}
```

#### 3. **Remover Produto**
```http
POST /cart_api.php?action=remove
Content-Type: application/x-www-form-urlencoded

product_id=123
```

**Resposta:**
```json
{
    "success": true,
    "message": "Produto removido do carrinho!",
    "item_count": 2
}
```

#### 4. **Limpar Carrinho**
```http
POST /cart_api.php?action=clear
```

**Resposta:**
```json
{
    "success": true,
    "message": "Carrinho limpo!",
    "item_count": 0
}
```

#### 5. **Obter Contagem de Itens**
```http
GET /cart_api.php?action=count
```

**Resposta:**
```json
{
    "success": true,
    "item_count": 3
}
```

#### 6. **Obter Itens do Carrinho**
```http
GET /cart_api.php?action=items
```

**Resposta:**
```json
{
    "success": true,
    "items": [
        {
            "id": 1,
            "product_id": 123,
            "name": "Açafrão da Terra",
            "slug": "acafrao-da-terra",
            "quantity": 2,
            "price": 12.90,
            "images": "[\"dist/img/product/acafrao.jpg\"]",
            "stock_quantity": 50
        }
    ],
    "cart_total": {
        "total": 25.80,
        "item_count": 2,
        "items": [...]
    }
}
```

## 🔍 API de Busca (`api/search_suggestions.php`)

### Endpoint
```http
GET /api/search_suggestions.php?q=termo
```

### Parâmetros
- `q` (string, obrigatório): Termo de busca (mínimo 2 caracteres)

### Exemplo de Requisição
```http
GET /api/search_suggestions.php?q=açafrão
```

### Resposta de Sucesso
```json
{
    "success": true,
    "suggestions": [
        {
            "text": "Açafrão da Terra",
            "type": "product",
            "url": "product.php?id=123"
        },
        {
            "text": "Temperos Naturais",
            "type": "category",
            "url": "category.php?id=1"
        }
    ]
}
```

### Resposta de Erro
```json
{
    "success": false,
    "message": "Termo de busca muito curto"
}
```

## 📦 API de Pedidos (`api/get_order_details.php`)

### Endpoint
```http
GET /api/get_order_details.php?id=123
```

### Parâmetros
- `id` (int, obrigatório): ID do pedido

### Exemplo de Requisição
```http
GET /api/get_order_details.php?id=123
```

### Resposta de Sucesso
```json
{
    "success": true,
    "order": {
        "id": 123,
        "order_number": "TC20240115001",
        "status": "processing",
        "subtotal": 89.70,
        "discount": 10.00,
        "shipping_cost": 15.00,
        "total": 94.70,
        "created_at": "2024-01-15 10:30:00",
        "items": [
            {
                "id": 1,
                "product_name": "Açafrão da Terra",
                "product_price": 12.90,
                "quantity": 2,
                "total": 25.80
            }
        ],
        "shipping_address": {
            "full_name": "João Silva",
            "street": "Rua das Flores, 123",
            "city": "São Paulo",
            "state": "SP",
            "zip_code": "01234-567"
        }
    }
}
```

## ❌ API de Cancelamento (`api/cancel_order.php`)

### Endpoint
```http
POST /api/cancel_order.php
Content-Type: application/x-www-form-urlencoded

order_id=123&reason=Produto indisponível
```

### Parâmetros
- `order_id` (int, obrigatório): ID do pedido
- `reason` (string, opcional): Motivo do cancelamento

### Resposta de Sucesso
```json
{
    "success": true,
    "message": "Pedido cancelado com sucesso",
    "order_id": 123,
    "new_status": "cancelled"
}
```

## ⚙️ API de Configurações (`api/save_settings.php`)

### Endpoint
```http
POST /api/save_settings.php
Content-Type: application/json

{
    "theme": "dark",
    "notifications": true,
    "language": "pt-BR"
}
```

### Resposta de Sucesso
```json
{
    "success": true,
    "message": "Configurações salvas com sucesso",
    "settings": {
        "theme": "dark",
        "notifications": true,
        "language": "pt-BR"
    }
}
```

## 🔐 API de Alteração de Senha (`api/change_password.php`)

### Endpoint
```http
POST /api/change_password.php
Content-Type: application/x-www-form-urlencoded

current_password=senha_atual&new_password=nova_senha&confirm_password=nova_senha
```

### Resposta de Sucesso
```json
{
    "success": true,
    "message": "Senha alterada com sucesso"
}
```

### Resposta de Erro
```json
{
    "success": false,
    "message": "Senha atual incorreta"
}
```

## 🔗 API de Integração N8N (`api/confirm_order_n8n.php`)

### Endpoint
```http
POST /api/confirm_order_n8n.php
Content-Type: application/json

{
    "order_id": 123,
    "webhook_url": "https://n8n.example.com/webhook/order-confirmation"
}
```

### Resposta de Sucesso
```json
{
    "success": true,
    "message": "Pedido confirmado via N8N",
    "order_id": 123,
    "webhook_sent": true
}
```

## 📊 Códigos de Status HTTP

### Sucesso
- `200 OK` - Requisição bem-sucedida
- `201 Created` - Recurso criado com sucesso

### Erro do Cliente
- `400 Bad Request` - Dados inválidos
- `401 Unauthorized` - Não autenticado
- `403 Forbidden` - Sem permissão
- `404 Not Found` - Recurso não encontrado
- `422 Unprocessable Entity` - Dados válidos mas não processáveis

### Erro do Servidor
- `500 Internal Server Error` - Erro interno do servidor
- `503 Service Unavailable` - Serviço indisponível

## 🔒 Autenticação e Autorização

### Sistema de Sessões
```php
// Verificar se usuário está logado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autenticado']);
    exit;
}
```

### Validação de Dados
```php
// Validar ID do produto
$productId = filter_var($_POST['product_id'], FILTER_VALIDATE_INT);
if ($productId === false || $productId <= 0) {
    throw new Exception('ID do produto inválido');
}
```

## 🛡️ Segurança das APIs

### 1. **Validação de Entrada**
```php
// Sanitizar entrada
$input = filter_input(INPUT_POST, 'data', FILTER_SANITIZE_STRING);

// Validar formato
if (!preg_match('/^[a-zA-Z0-9\s]+$/', $input)) {
    throw new Exception('Formato inválido');
}
```

### 2. **Rate Limiting**
```php
// Implementar rate limiting simples
$key = $_SERVER['REMOTE_ADDR'];
$requests = $_SESSION['api_requests'][$key] ?? 0;

if ($requests > 100) { // 100 requests por sessão
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Rate limit exceeded']);
    exit;
}
```

### 3. **CORS Headers**
```php
// Configurar CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
```

## 📝 Padrões de Resposta

### Formato Padrão de Sucesso
```json
{
    "success": true,
    "message": "Operação realizada com sucesso",
    "data": {
        // Dados específicos da operação
    },
    "timestamp": "2024-01-15T10:30:00Z"
}
```

### Formato Padrão de Erro
```json
{
    "success": false,
    "message": "Descrição do erro",
    "error_code": "VALIDATION_ERROR",
    "details": {
        // Detalhes específicos do erro
    },
    "timestamp": "2024-01-15T10:30:00Z"
}
```

## 🧪 Testes das APIs

### Exemplo com cURL

#### Testar API do Carrinho
```bash
# Adicionar produto
curl -X POST "http://localhost/cart_api.php?action=add" \
  -d "product_id=123&quantity=2"

# Obter itens
curl "http://localhost/cart_api.php?action=items"
```

#### Testar API de Busca
```bash
curl "http://localhost/api/search_suggestions.php?q=açafrão"
```

### Exemplo com JavaScript
```javascript
// Adicionar ao carrinho
async function addToCart(productId, quantity) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);
    
    try {
        const response = await fetch('cart_api.php?action=add', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            console.log('Produto adicionado:', data.message);
            updateCartCount(data.item_count);
        } else {
            console.error('Erro:', data.message);
        }
    } catch (error) {
        console.error('Erro de rede:', error);
    }
}
```

## 📈 Monitoramento e Logs

### Log de Requisições
```php
// Log de todas as requisições API
function logApiRequest($endpoint, $method, $data, $response) {
    $log = [
        'timestamp' => date('Y-m-d H:i:s'),
        'endpoint' => $endpoint,
        'method' => $method,
        'ip' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        'request_data' => $data,
        'response' => $response
    ];
    
    file_put_contents('logs/api.log', json_encode($log) . "\n", FILE_APPEND);
}
```

### Métricas de Performance
```php
// Medir tempo de resposta
$startTime = microtime(true);
// ... processamento da API ...
$endTime = microtime(true);
$responseTime = ($endTime - $startTime) * 1000; // em ms

// Log de performance
if ($responseTime > 1000) { // > 1 segundo
    error_log("Slow API response: {$endpoint} took {$responseTime}ms");
}
```

## 🔄 Versionamento das APIs

### Estratégia de Versionamento
- **URL Versioning**: `/api/v1/search_suggestions.php`
- **Header Versioning**: `API-Version: 1.0`
- **Query Parameter**: `?version=1.0`

### Exemplo de Versionamento
```php
// Detectar versão da API
$version = $_GET['version'] ?? $_SERVER['HTTP_API_VERSION'] ?? '1.0';

switch ($version) {
    case '1.0':
        // Implementação v1.0
        break;
    case '2.0':
        // Implementação v2.0
        break;
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Versão não suportada']);
        exit;
}
```

## 🚀 Otimizações

### 1. **Cache de Respostas**
```php
// Cache simples para consultas frequentes
$cacheKey = 'api_search_' . md5($query);
$cached = apcu_fetch($cacheKey);

if ($cached !== false) {
    echo json_encode($cached);
    exit;
}

// Processar e cachear
$result = processSearch($query);
apcu_store($cacheKey, $result, 300); // 5 minutos
```

### 2. **Compressão de Resposta**
```php
// Habilitar compressão gzip
if (extension_loaded('zlib') && !ob_get_level()) {
    ob_start('ob_gzhandler');
}
```

### 3. **Paginação**
```php
// Implementar paginação
$page = (int)($_GET['page'] ?? 1);
$limit = min((int)($_GET['limit'] ?? 20), 100); // máximo 100
$offset = ($page - 1) * $limit;

$results = getProducts($limit, $offset);
$total = getTotalProducts();

echo json_encode([
    'success' => true,
    'data' => $results,
    'pagination' => [
        'page' => $page,
        'limit' => $limit,
        'total' => $total,
        'pages' => ceil($total / $limit)
    ]
]);
```

---

Esta documentação das APIs fornece uma base sólida para integração e desenvolvimento com o sistema Tempero e Café.
