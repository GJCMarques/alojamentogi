<?php

namespace Core\Payment;

use Core\Database;

class IfthenPay
{
    private const API_BASE = 'https://api.ifthenpay.com';
    private const SANDBOX_BASE = 'https://sandbox.ifthenpay.com';

    private const MBWAY_INIT = '/spg/payment/mbway';
    private const MBWAY_STATUS = '/spg/payment/mbway/status';

    private const MB_INIT = '/multibanco/reference/init';
    private const MB_STATUS = '/multibanco/reference/status';

    private const CARD_INIT = '/spg/payment/ccard';
    private const CARD_STATUS = '/spg/payment/ccard/status';

    private array $config;
    private bool $sandbox;
    private Database $db;

    private static ?IfthenPay $instance = null;

    private function __construct()
    {
        $appConfig = require CONFIG_PATH . '/config.php';
        $this->db = Database::getInstance();
        $this->sandbox = (bool) ($appConfig['payment']['sandbox'] ?? true);

        $this->config = [
            'mbway_key' => setting('ifthenpay_mbway_key', $appConfig['payment']['ifthenpay']['mbway_key'] ?? ''),
            'multibanco_entity' => setting('ifthenpay_entity', $appConfig['payment']['ifthenpay']['multibanco_entity'] ?? ''),
            'multibanco_subentity' => setting('ifthenpay_subentity', $appConfig['payment']['ifthenpay']['multibanco_subentity'] ?? ''),
            'card_key' => setting('ifthenpay_card_key', $appConfig['payment']['ifthenpay']['card_key'] ?? ''),
            'anti_phishing_key' => setting('ifthenpay_anti_phishing_key', $appConfig['payment']['ifthenpay']['anti_phishing_key'] ?? ''),
            'callback_url' => setting('ifthenpay_callback_url', $appConfig['payment']['ifthenpay']['callback_url'] ?? ''),
        ];
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function createMBWayPayment(int $orderId, string $phone, float $amount): array
    {
        if (empty($this->config['mbway_key'])) {
            return ['success' => false, 'request_id' => null, 'message' => 'MBWay key not configured'];
        }

        $phone = preg_replace('/\D/', '', $phone);
        if (strlen($phone) === 9) {
            $phone = '351' . $phone;
        }

        $order = $this->db->fetch("SELECT order_number FROM orders WHERE id = ?", [$orderId]);

        $requestId = $this->generateRequestId();

        $payload = [
            'mbWayKey' => $this->config['mbway_key'],
            'orderId' => $order['order_number'] ?? $orderId,
            'amount' => number_format($amount, 2, '.', ''),
            'mobileNumber' => $phone,
            'email' => '',
            'description' => 'Encomenda #' . ($order['order_number'] ?? $orderId),
        ];

        $response = $this->apiRequest(self::MBWAY_INIT, $payload);

        if ($response && isset($response['Status']) && $response['Status'] === '000') {

            $this->db->update('orders', [
                'payment_reference' => $response['RequestId'] ?? $requestId,
                'payment_status' => 'processing',
            ], 'id = ?', [$orderId]);

            logMessage("MBWay payment initiated for order #{$orderId}", 'info');

            return [
                'success' => true,
                'request_id' => $response['RequestId'] ?? $requestId,
                'message' => 'Pedido MBWay enviado. Confirme no telemovel.',
            ];
        }

        $errorMsg = $response['Message'] ?? 'Erro ao criar pagamento MBWay';
        logMessage("MBWay payment failed for order #{$orderId}: {$errorMsg}", 'error');

        return ['success' => false, 'request_id' => null, 'message' => $errorMsg];
    }

    public function createMultibancoReference(int $orderId, float $amount): array
    {
        if (empty($this->config['multibanco_entity']) || empty($this->config['multibanco_subentity'])) {
            return ['success' => false, 'message' => 'Multibanco not configured'];
        }

        $order = $this->db->fetch("SELECT order_number FROM orders WHERE id = ?", [$orderId]);

        $payload = [
            'mbEntity' => $this->config['multibanco_entity'],
            'mbSubEntity' => $this->config['multibanco_subentity'],
            'orderId' => $order['order_number'] ?? $orderId,
            'amount' => number_format($amount, 2, '.', ''),
        ];

        $response = $this->apiRequest(self::MB_INIT, $payload);

        if ($response && isset($response['Status']) && $response['Status'] === '0') {
            $entity = $response['Entity'] ?? $this->config['multibanco_entity'];
            $reference = $response['Reference'] ?? '';

            $this->db->update('orders', [
                'payment_entity' => $entity,
                'payment_reference' => $reference,
                'payment_status' => 'pending',
            ], 'id = ?', [$orderId]);

            logMessage("Multibanco reference generated for order #{$orderId}: {$entity}/{$reference}", 'info');

            return [
                'success' => true,
                'entity' => $entity,
                'reference' => $reference,
                'amount' => $amount,
                'message' => 'Referencia Multibanco gerada.',
            ];
        }

        $reference = $this->generateLocalMultibancoReference($orderId);
        $entity = $this->config['multibanco_entity'];

        $this->db->update('orders', [
            'payment_entity' => $entity,
            'payment_reference' => $reference,
            'payment_status' => 'pending',
        ], 'id = ?', [$orderId]);

        return [
            'success' => true,
            'entity' => $entity,
            'reference' => $reference,
            'amount' => $amount,
            'message' => 'Referencia Multibanco gerada.',
        ];
    }

    public function createCardPayment(int $orderId, float $amount, string $returnUrl): array
    {
        if (empty($this->config['card_key'])) {
            return ['success' => false, 'payment_url' => null, 'message' => 'Card payment not configured'];
        }

        $order = $this->db->fetch("SELECT order_number, customer_email FROM orders WHERE id = ?", [$orderId]);

        $requestId = $this->generateRequestId();

        $payload = [
            'ccardKey' => $this->config['card_key'],
            'orderId' => $order['order_number'] ?? $orderId,
            'amount' => number_format($amount, 2, '.', ''),
            'successUrl' => $returnUrl . '?status=success&order=' . $orderId,
            'errorUrl' => $returnUrl . '?status=error&order=' . $orderId,
            'cancelUrl' => $returnUrl . '?status=cancel&order=' . $orderId,
            'language' => 'pt',
        ];

        $response = $this->apiRequest(self::CARD_INIT, $payload);

        if ($response && isset($response['Status']) && $response['Status'] === '0') {
            $paymentUrl = $response['PaymentUrl'] ?? null;

            $this->db->update('orders', [
                'payment_reference' => $response['RequestId'] ?? $requestId,
                'payment_status' => 'processing',
            ], 'id = ?', [$orderId]);

            logMessage("Card payment initiated for order #{$orderId}", 'info');

            return [
                'success' => true,
                'payment_url' => $paymentUrl,
                'request_id' => $response['RequestId'] ?? $requestId,
                'message' => 'Redirecionando para pagamento seguro.',
            ];
        }

        $errorMsg = $response['Message'] ?? 'Erro ao criar pagamento por cartao';
        logMessage("Card payment failed for order #{$orderId}: {$errorMsg}", 'error');

        return ['success' => false, 'payment_url' => null, 'message' => $errorMsg];
    }

    public function handleCallback(array $data): array
    {

        $receivedKey = $data['key'] ?? $data['anti_phishing_key'] ?? '';
        if (!$this->verifyAntiPhishing($receivedKey)) {
            logMessage("Payment callback rejected: invalid anti-phishing key", 'error');
            return ['success' => false, 'message' => 'Invalid anti-phishing key'];
        }

        $transactionId = $data['requestId'] ?? $data['reference'] ?? '';
        if (!empty($transactionId)) {
            $existing = $this->db->fetch(
                "SELECT id FROM orders WHERE payment_transaction_id = ? AND payment_status = 'paid'",
                [$transactionId]
            );
            if ($existing) {
                logMessage("Payment callback replay detected: {$transactionId}", 'warning');
                return ['success' => false, 'message' => 'Transaction already processed'];
            }
        }

        $orderId = $data['orderId'] ?? null;
        $reference = $data['reference'] ?? $data['requestId'] ?? null;

        $order = null;
        if ($orderId) {
            $order = $this->db->fetch(
                "SELECT * FROM orders WHERE order_number = ? OR id = ?",
                [$orderId, (int) $orderId]
            );
        }
        if (!$order && $reference) {
            $order = $this->db->fetch(
                "SELECT * FROM orders WHERE payment_reference = ?",
                [$reference]
            );
        }

        if (!$order) {
            logMessage("Payment callback: order not found (orderId={$orderId}, ref={$reference})", 'error');
            return ['success' => false, 'message' => 'Order not found'];
        }

        $callbackAmount = (float) ($data['amount'] ?? 0);
        if ($callbackAmount > 0 && abs($callbackAmount - (float) $order['total']) > 0.01) {
            logMessage("Payment callback amount mismatch: expected {$order['total']}, got {$callbackAmount}", 'error');
            return ['success' => false, 'message' => 'Amount mismatch'];
        }

        $status = $data['status'] ?? $data['paymentStatus'] ?? 'paid';
        $paymentStatus = $this->mapCallbackStatus($status);

        $updateData = [
            'payment_status' => $paymentStatus,
            'payment_transaction_id' => $transactionId,
        ];

        if ($paymentStatus === 'paid') {
            $updateData['paid_at'] = date('Y-m-d H:i:s');
            $updateData['status'] = 'confirmed';
        }

        $this->db->update('orders', $updateData, 'id = ?', [$order['id']]);

        $this->db->insert('order_status_history', [
            'order_id' => $order['id'],
            'status' => $paymentStatus === 'paid' ? 'confirmed' : $order['status'],
            'notes' => "Pagamento {$paymentStatus} via callback (TX: {$transactionId})",
        ]);

        if ($paymentStatus === 'paid') {
            $this->onPaymentConfirmed($order['id']);
        }

        logMessage("Payment callback processed: order #{$order['id']}, status={$paymentStatus}", 'info');

        return ['success' => true, 'message' => 'Callback processed'];
    }

    public function checkPaymentStatus(int $orderId): array
    {
        $order = $this->db->fetch(
            "SELECT id, payment_status, payment_method, payment_reference, total FROM orders WHERE id = ?",
            [$orderId]
        );

        if (!$order) {
            return ['status' => 'unknown', 'message' => 'Order not found'];
        }

        if (in_array($order['payment_status'], ['paid', 'failed', 'refunded'])) {
            return [
                'status' => $order['payment_status'],
                'message' => $this->getStatusMessage($order['payment_status']),
            ];
        }

        if ($order['payment_method'] === 'mbway' && !empty($order['payment_reference'])) {
            $apiStatus = $this->checkMBWayStatus($order['payment_reference']);
            if ($apiStatus) {
                return $apiStatus;
            }
        }

        return [
            'status' => $order['payment_status'],
            'message' => $this->getStatusMessage($order['payment_status']),
        ];
    }

    public function verifyAntiPhishing(string $receivedKey): bool
    {
        $expectedKey = $this->config['anti_phishing_key'];

        if (empty($expectedKey)) {

            return $this->sandbox;
        }

        return hash_equals($expectedKey, $receivedKey);
    }

    public function isConfigured(string $method = 'any'): bool
    {
        return match ($method) {
            'mbway' => !empty($this->config['mbway_key']),
            'multibanco' => !empty($this->config['multibanco_entity']),
            'card' => !empty($this->config['card_key']),
            'any' => !empty($this->config['mbway_key']) || !empty($this->config['multibanco_entity']) || !empty($this->config['card_key']),
            default => false,
        };
    }

    // ============================================================
    // PRIVATE METHODS
    // ============================================================

    private function onPaymentConfirmed(int $orderId): void
    {
        try {

            $invoice = \Core\Invoice::getInstance();
            $invoiceData = $invoice->generate($orderId);

            if ($invoiceData) {

                $invoice->markAsPaid($invoiceData['id']);

                $invoice->sendEmail($invoiceData['id']);
            }

            $mailer = new \Core\Mailer();
            $order = $this->db->fetch("SELECT * FROM orders WHERE id = ?", [$orderId]);
            $items = $this->db->fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$orderId]);

            if ($order) {
                $mailer->sendOrderConfirmation($order, $items);
                $mailer->sendOrderNotification($order, $items);
            }
        } catch (\Exception $e) {
            logMessage("Post-payment processing error for order #{$orderId}: " . $e->getMessage(), 'error');
        }
    }

    private function checkMBWayStatus(string $requestId): ?array
    {
        $payload = [
            'mbWayKey' => $this->config['mbway_key'],
            'requestId' => $requestId,
        ];

        $response = $this->apiRequest(self::MBWAY_STATUS, $payload);

        if ($response && isset($response['Status'])) {
            $status = match ($response['Status']) {
                '000' => 'paid',
                '020', '101' => 'pending',
                '122', '999' => 'expired',
                default => 'pending',
            };

            return [
                'status' => $status,
                'message' => $this->getStatusMessage($status),
            ];
        }

        return null;
    }

    private function apiRequest(string $endpoint, array $payload): ?array
    {
        $baseUrl = $this->sandbox ? self::SANDBOX_BASE : self::API_BASE;
        $url = $baseUrl . $endpoint;

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => !$this->sandbox,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            logMessage("IfthenPay API error ({$endpoint}): {$error}", 'error');
            return null;
        }

        if ($httpCode >= 400) {
            logMessage("IfthenPay API HTTP {$httpCode} ({$endpoint}): {$response}", 'error');
            return null;
        }

        return json_decode($response, true);
    }

    private function generateRequestId(): string
    {
        return bin2hex(random_bytes(16));
    }

    private function generateLocalMultibancoReference(int $orderId): string
    {
        $ref = str_pad((string) $orderId, 9, '0', STR_PAD_LEFT);
        return substr($ref, 0, 3) . ' ' . substr($ref, 3, 3) . ' ' . substr($ref, 6, 3);
    }

    private function mapCallbackStatus(string $status): string
    {
        $status = strtolower($status);
        return match (true) {
            in_array($status, ['paid', 'success', 'confirmed', '000']) => 'paid',
            in_array($status, ['failed', 'error', 'declined']) => 'failed',
            in_array($status, ['refunded', 'reversed']) => 'refunded',
            in_array($status, ['expired', 'cancelled', 'cancel']) => 'failed',
            default => 'pending',
        };
    }

    private function getStatusMessage(string $status): string
    {
        return match ($status) {
            'paid' => 'Pagamento confirmado.',
            'pending' => 'A aguardar pagamento.',
            'processing' => 'Pagamento em processamento.',
            'failed' => 'Pagamento falhado.',
            'expired' => 'Pagamento expirado.',
            'refunded' => 'Pagamento reembolsado.',
            default => 'Estado desconhecido.',
        };
    }
}
