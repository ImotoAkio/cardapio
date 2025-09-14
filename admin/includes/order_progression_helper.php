<?php
// =====================================================
// 🔗 HELPER N8N - PROGRESSÃO DE PEDIDOS
// =====================================================

class OrderProgressionHelper {
    private static $webhookUrl = 'https://webhook.echo.dev.br/webhook/e8a2f4db-eefd-498e-9547-a0200442c108';
    
    /**
     * Enviar atualização de status do pedido para o n8n
     */
    public static function sendStatusUpdate($orderId, $newStatus, $db) {
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
                SELECT oi.*, p.name as product_name
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
                ORDER BY oi.created_at
            ");
            $stmt->execute([$orderId]);
            $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Estrutura dos dados para o webhook
            $webhookData = [
                'pedido_id' => $order['id'],
                'numero_pedido' => $order['order_number'],
                'nome_cliente' => $order['full_name'],
                'email_cliente' => $order['email'],
                'telefone_cliente' => self::formatPhoneNumber($order['phone']),
                'status_anterior' => $order['status'],
                'status_novo' => $newStatus,
                'valor_total' => floatval($order['total']),
                'produtos' => [],
                'data_atualizacao' => date('Y-m-d H:i:s'),
                'tipo_evento' => 'status_update'
            ];
            
            // Adicionar produtos
            foreach ($orderItems as $item) {
                $webhookData['produtos'][] = [
                    'nome' => $item['product_name'],
                    'quantidade' => $item['quantity'],
                    'preco_unitario' => floatval($item['product_price']),
                    'total' => floatval($item['total'])
                ];
            }
            
            // Enviar para o webhook
            return self::sendWebhook($webhookData);
            
        } catch (Exception $e) {
            error_log("Erro ao enviar atualização de status para n8n: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Enviar webhook para o n8n
     */
    private static function sendWebhook($data) {
        // Verificar se já existe uma requisição em andamento
        $lockFile = sys_get_temp_dir() . '/n8n_order_progression_lock_' . md5(self::$webhookUrl);
        
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
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::$webhookUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'User-Agent: Tempero-e-Cafe-Admin/1.0',
                'X-Source: Order-Progression'
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
     * Testar conexão com o webhook de progressão
     */
    public static function testConnection() {
        $testData = [
            'pedido_id' => 999,
            'numero_pedido' => 'TEST001',
            'nome_cliente' => 'Cliente Teste',
            'email_cliente' => 'teste@exemplo.com',
            'telefone_cliente' => self::formatPhoneNumber('11999999999'),
            'status_anterior' => 'pending',
            'status_novo' => 'confirmed',
            'valor_total' => 50.00,
            'produtos' => [
                [
                    'nome' => 'Produto Teste',
                    'quantidade' => 1,
                    'preco_unitario' => 50.00,
                    'total' => 50.00
                ]
            ],
            'data_atualizacao' => date('Y-m-d H:i:s'),
            'tipo_evento' => 'test'
        ];
        
        try {
            $result = self::sendWebhook($testData);
            return [
                'success' => true,
                'message' => 'Conexão com webhook de progressão funcionando',
                'details' => $result
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro na conexão com webhook de progressão: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Obter URL do webhook
     */
    public static function getWebhookUrl() {
        return self::$webhookUrl;
    }
    
    /**
     * Formatar número de telefone para o padrão brasileiro (55ddd991682773)
     */
    private static function formatPhoneNumber($phone) {
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
}
?>
