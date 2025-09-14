<?php
// =====================================================
// 🔗 HELPER N8N - TEMPERO E CAFÉ
// =====================================================

class N8nHelper {
    private static $webhookUrl = 'https://webhook.echo.dev.br/webhook/8cea05f1-e082-45ea-83ca-f80809af9cfd';
    
    /**
     * Enviar dados do pedido para o n8n
     */
    public static function sendOrderToN8n($orderId, $db) {
        try {
            // Buscar dados do pedido
            $stmt = $db->prepare("
                SELECT o.*, u.full_name, u.email, u.phone
                FROM orders o
                JOIN users u ON o.user_id = u.id
                WHERE o.id = ?
            ");
            $stmt->execute([$orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$order) {
                throw new Exception('Pedido não encontrado');
            }
            
            // Buscar itens do pedido
            $stmt = $db->prepare("
                SELECT oi.*, p.name as product_name, p.images
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
                ORDER BY oi.created_at
            ");
            $stmt->execute([$orderId]);
            $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Estrutura fixa e simples do JSON
            $n8nData = [
                'nome_usuario' => $order['full_name'],
                'telefone_usuario' => self::formatPhoneNumber($order['phone']),
                'produtos_comprados' => [],
                'valor_total' => floatval($order['total']),
                'status_compra' => $order['status']
            ];
            
            // Adicionar produtos comprados
            foreach ($orderItems as $item) {
                $n8nData['produtos_comprados'][] = $item['product_name'];
            }
            
            // Enviar para o webhook
            return self::sendWebhook($n8nData);
            
        } catch (Exception $e) {
            error_log("Erro ao enviar pedido para n8n: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Enviar webhook para o n8n
     */
    private static function sendWebhook($data) {
        // Verificar se já existe uma requisição em andamento
        $lockFile = sys_get_temp_dir() . '/n8n_webhook_lock_' . md5(self::$webhookUrl);
        
        if (file_exists($lockFile)) {
            $lockTime = filemtime($lockFile);
            // Se o lock tem menos de 30 segundos, considerar como bloqueado
            if ((time() - $lockTime) < 30) {
                throw new Exception('Webhook já está sendo processado. Aguarde alguns segundos.');
            }
        }
        
        // Criar arquivo de lock
        file_put_contents($lockFile, time());
        
        try {
            // Converter produtos para string separada por vírgulas
            $produtosString = implode(',', $data['produtos_comprados']);
            
            // Enviar dados como parâmetros de query separados
            $queryParams = http_build_query([
                'nome_usuario' => $data['nome_usuario'],
                'telefone_usuario' => $data['telefone_usuario'],
                'produtos_comprados' => $produtosString,
                'valor_total' => $data['valor_total'],
                'status_compra' => $data['status_compra']
            ]);
            
            $url = self::$webhookUrl . '?' . $queryParams;
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'User-Agent: Tempero-e-Cafe/1.0',
                'X-Source: PHP-Application'
            ]);
            
            $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            throw new Exception('Erro cURL: ' . $curlError);
        }
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return [
                'success' => true,
                'http_code' => $httpCode,
                'response' => $response,
                'data_sent' => $data
            ];
        } else {
            throw new Exception('Erro HTTP: ' . $httpCode . ' - Resposta: ' . $response);
        }
        
        } finally {
            // Remover arquivo de lock
            if (file_exists($lockFile)) {
                unlink($lockFile);
            }
        }
    }
    
    /**
     * Testar conexão com n8n
     */
    public static function testConnection() {
        $testData = [
            'nome_usuario' => 'Teste de Conexão',
            'telefone_usuario' => self::formatPhoneNumber('11999999999'),
            'produtos_comprados' => ['Produto de Teste'],
            'valor_total' => 15.50,
            'status_compra' => 'test'
        ];
        
        try {
            $result = self::sendWebhook($testData);
            return [
                'success' => true,
                'message' => 'Conexão com n8n funcionando',
                'details' => $result
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro na conexão com n8n: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Formatar número de telefone para o padrão brasileiro (55ddd87991682773)
     */
    public static function formatPhoneNumber($phone) {
        if (empty($phone)) {
            return '';
        }
        
        // Remover todos os caracteres não numéricos
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Se já começar com 55, retornar como está
        if (strpos($phone, '55') === 0) {
            return $phone;
        }
        
        // Se começar com 0, remover o 0
        if (strpos($phone, '0') === 0) {
            $phone = substr($phone, 1);
        }
        
        // Adicionar código do país 55 se não tiver
        if (strpos($phone, '55') !== 0) {
            $phone = '55' . $phone;
        }
        
        return $phone;
    }
    
    /**
     * Obter URL do webhook
     */
    public static function getWebhookUrl() {
        return self::$webhookUrl;
    }
}
?>
