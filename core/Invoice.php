<?php
/**
 * A Casa do Gi - Invoice System
 * Handles invoice generation, verification, and management
 * with cryptographically secure UUID and 9-digit barcode system.
 */

namespace Core;

class Invoice
{
    private Database $db;
    private static ?Invoice $instance = null;

    private function __construct()
    {
        $this->db = Database::getInstance();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Generate a new invoice for an order
     */
    public function generate(int $orderId): ?array
    {
        $order = $this->db->fetch(
            "SELECT * FROM orders WHERE id = ?",
            [$orderId]
        );

        if (!$order) {
            logMessage("Invoice generation failed: Order #{$orderId} not found", 'error');
            return null;
        }

        // Check if invoice already exists for this order
        $existing = $this->db->fetch(
            "SELECT * FROM invoices WHERE order_id = ?",
            [$orderId]
        );

        if ($existing) {
            return $existing;
        }

        // Get order items
        $items = $this->db->fetchAll(
            "SELECT * FROM order_items WHERE order_id = ?",
            [$orderId]
        );

        // Generate unique identifiers
        $uuid = $this->generateUUID();
        $barcode = $this->generateBarcode();

        if (!$barcode) {
            logMessage("Invoice generation failed: Could not generate barcode", 'error');
            return null;
        }

        // Get current batch
        $batch = $this->db->fetch(
            "SELECT batch_number FROM barcode_batches WHERE is_active = 1 ORDER BY id DESC LIMIT 1"
        );

        // Build items JSON snapshot
        $itemsSnapshot = array_map(function ($item) {
            return [
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'product_sku' => $item['product_sku'] ?? '',
                'quantity' => (int) $item['quantity'],
                'unit_price' => (float) ($item['unit_price'] ?? $item['price'] ?? 0),
                'total_price' => (float) ($item['total_price'] ?? $item['subtotal'] ?? 0),
            ];
        }, $items);

        $itemsJson = json_encode($itemsSnapshot, JSON_UNESCAPED_UNICODE);

        // Calculate integrity hash (tamper detection)
        $integrityHash = $this->calculateIntegrityHash(
            $uuid,
            $barcode,
            $order['customer_email'],
            (float) $order['total'],
            $itemsJson
        );

        // Insert invoice
        try {
            $invoiceId = $this->db->insert('invoices', [
                'order_id' => $orderId,
                'invoice_uuid' => $uuid,
                'barcode' => $barcode,
                'barcode_batch' => $batch['batch_number'] ?? 1,
                'integrity_hash' => $integrityHash,
                'customer_name' => $order['customer_name'],
                'customer_email' => $order['customer_email'],
                'customer_phone' => $order['customer_phone'] ?? null,
                'customer_nif' => $order['customer_nif'] ?? null,
                'billing_address' => $order['shipping_address'] ?? $order['billing_address'] ?? null,
                'billing_postal_code' => $order['shipping_postal_code'] ?? $order['billing_postal_code'] ?? null,
                'billing_city' => $order['shipping_city'] ?? $order['billing_city'] ?? null,
                'items_json' => $itemsJson,
                'subtotal' => $order['subtotal'],
                'shipping_fee' => $order['shipping_fee'] ?? $order['shipping'] ?? 0,
                'discount_amount' => $order['discount_amount'] ?? 0,
                'total' => $order['total'],
                'payment_method' => $order['payment_method'],
                'payment_status' => $order['payment_status'] ?? 'pending',
            ]);

            // Update order with invoice_id
            $this->db->update('orders', ['invoice_id' => $invoiceId], 'id = ?', [$orderId]);

            // Increment batch counter
            $this->db->query(
                "UPDATE barcode_batches SET codes_used = codes_used + 1 WHERE is_active = 1 ORDER BY id DESC LIMIT 1"
            );

            return $this->db->fetch("SELECT * FROM invoices WHERE id = ?", [$invoiceId]);

        } catch (\Exception $e) {
            logMessage("Invoice generation error: " . $e->getMessage(), 'error');
            return null;
        }
    }

    /**
     * Find invoice by 9-digit barcode
     */
    public function findByBarcode(string $barcode): ?array
    {
        $barcode = preg_replace('/[^0-9]/', '', $barcode);

        if (strlen($barcode) !== 9) {
            return null;
        }

        return $this->db->fetch(
            "SELECT i.*, o.order_number, o.status as order_status
             FROM invoices i
             LEFT JOIN orders o ON i.order_id = o.id
             WHERE i.barcode = ?",
            [$barcode]
        );
    }

    /**
     * Find invoice by UUID
     */
    public function findByUUID(string $uuid): ?array
    {
        $uuid = trim($uuid);

        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $uuid)) {
            return null;
        }

        return $this->db->fetch(
            "SELECT i.*, o.order_number, o.status as order_status
             FROM invoices i
             LEFT JOIN orders o ON i.order_id = o.id
             WHERE i.invoice_uuid = ?",
            [$uuid]
        );
    }

    /**
     * Find invoice by barcode OR UUID (auto-detect)
     */
    public function findByCode(string $code): ?array
    {
        $code = trim($code);

        // If it looks like a UUID
        if (strlen($code) === 36 && strpos($code, '-') !== false) {
            return $this->findByUUID($code);
        }

        // If it's numeric (barcode) - strip any formatting
        $numericCode = preg_replace('/[^0-9]/', '', $code);
        if (strlen($numericCode) === 9) {
            return $this->findByBarcode($numericCode);
        }

        return null;
    }

    /**
     * Verify invoice integrity (check if tampered)
     */
    public function verify(string $code): array
    {
        $invoice = $this->findByCode($code);

        if (!$invoice) {
            return [
                'valid' => false,
                'message' => 'Fatura nao encontrada.',
                'invoice' => null,
            ];
        }

        // Recalculate integrity hash
        $expectedHash = $this->calculateIntegrityHash(
            $invoice['invoice_uuid'],
            $invoice['barcode'],
            $invoice['customer_email'],
            (float) $invoice['total'],
            $invoice['items_json']
        );

        $isValid = hash_equals($expectedHash, $invoice['integrity_hash']);

        return [
            'valid' => $isValid,
            'message' => $isValid
                ? 'Fatura verificada com sucesso.'
                : 'ALERTA: A integridade desta fatura nao pode ser verificada. Possivel adulteracao.',
            'invoice' => $invoice,
        ];
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid(int $invoiceId): bool
    {
        return $this->db->update(
            'invoices',
            ['payment_status' => 'paid', 'paid_at' => date('Y-m-d H:i:s')],
            'id = ?',
            [$invoiceId]
        ) > 0;
    }

    /**
     * Mark invoice as emailed
     */
    public function markAsEmailed(int $invoiceId): bool
    {
        return $this->db->update(
            'invoices',
            ['emailed_at' => date('Y-m-d H:i:s')],
            'id = ?',
            [$invoiceId]
        ) > 0;
    }

    /**
     * Send invoice email to customer
     */
    public function sendEmail(int $invoiceId): bool
    {
        $invoice = $this->db->fetch("SELECT * FROM invoices WHERE id = ?", [$invoiceId]);

        if (!$invoice || empty($invoice['customer_email'])) {
            return false;
        }

        $items = json_decode($invoice['items_json'], true);

        $mailer = new Mailer();

        // Render invoice email template
        $templateFile = TEMPLATES_PATH . '/emails/invoice.php';
        if (file_exists($templateFile)) {
            ob_start();
            $data = [
                'invoice' => $invoice,
                'items' => $items,
                'siteName' => setting('site_name', 'A Casa do Gi'),
                'contactEmail' => setting('contact_email', 'info@acasadogi.pt'),
                'contactPhone' => setting('contact_phone', ''),
            ];
            extract($data);
            include $templateFile;
            $body = ob_get_clean();
        } else {
            $body = $this->getDefaultInvoiceEmailBody($invoice, $items);
        }

        $subject = 'Fatura #' . $invoice['barcode'] . ' - A Casa do Gi';

        $sent = $mailer->send($invoice['customer_email'], $subject, $body);

        if ($sent) {
            $this->markAsEmailed($invoiceId);
            logMessage("Invoice email sent: #{$invoice['barcode']} to {$invoice['customer_email']}", 'info');
        }

        return $sent;
    }

    /**
     * Get all invoices with optional filters
     */
    public function getAll(array $filters = [], int $limit = 20, int $offset = 0): array
    {
        $where = "WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $where .= " AND i.payment_status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $where .= " AND (i.barcode LIKE ? OR i.invoice_uuid LIKE ? OR i.customer_name LIKE ? OR i.customer_email LIKE ?)";
            $search = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$search, $search, $search, $search]);
        }

        if (!empty($filters['date_from'])) {
            $where .= " AND i.issued_at >= ?";
            $params[] = $filters['date_from'] . ' 00:00:00';
        }

        if (!empty($filters['date_to'])) {
            $where .= " AND i.issued_at <= ?";
            $params[] = $filters['date_to'] . ' 23:59:59';
        }

        return $this->db->fetchAll(
            "SELECT i.*, o.order_number, o.status as order_status
             FROM invoices i
             LEFT JOIN orders o ON i.order_id = o.id
             {$where}
             ORDER BY i.issued_at DESC
             LIMIT {$limit} OFFSET {$offset}",
            $params
        );
    }

    /**
     * Count invoices with optional filters
     */
    public function count(array $filters = []): int
    {
        $where = "WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $where .= " AND payment_status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $where .= " AND (barcode LIKE ? OR invoice_uuid LIKE ? OR customer_name LIKE ? OR customer_email LIKE ?)";
            $search = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$search, $search, $search, $search]);
        }

        $result = $this->db->fetch(
            "SELECT COUNT(*) as total FROM invoices {$where}",
            $params
        );

        return (int) ($result['total'] ?? 0);
    }

    // ============================================================
    // PRIVATE METHODS
    // ============================================================

    /**
     * Generate a cryptographically secure UUID v4
     */
    private function generateUUID(): string
    {
        $data = random_bytes(16);

        // Set version to 4 (0100)
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set variant to RFC 4122 (10xx)
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Generate a unique 9-digit barcode
     * Uses cryptographically secure random generation with collision detection
     */
    private function generateBarcode(): ?string
    {
        // Check if current batch has capacity
        $batch = $this->db->fetch(
            "SELECT * FROM barcode_batches WHERE is_active = 1 ORDER BY id DESC LIMIT 1"
        );

        if (!$batch) {
            // Create first batch
            $this->db->insert('barcode_batches', [
                'batch_number' => 1,
                'codes_used' => 0,
                'is_active' => 1,
            ]);
        } elseif ($batch['codes_used'] >= $batch['max_codes']) {
            // Current batch exhausted - create new batch
            $newBatchNumber = $batch['batch_number'] + 1;

            // Deactivate current batch
            $this->db->update('barcode_batches', ['is_active' => 0], 'id = ?', [$batch['id']]);

            // Create new batch
            $this->db->insert('barcode_batches', [
                'batch_number' => $newBatchNumber,
                'codes_used' => 0,
                'is_active' => 1,
            ]);

            logMessage("Barcode batch #{$newBatchNumber} created (previous batch exhausted)", 'info');
        }

        // Generate unique barcode with retry logic
        $maxAttempts = 100;
        for ($i = 0; $i < $maxAttempts; $i++) {
            // Generate 9-digit code using secure random
            $randomBytes = random_bytes(5);
            $number = abs(unpack('N', "\0" . substr($randomBytes, 0, 3))[1]) % 900000000 + 100000000;
            $barcode = str_pad((string) $number, 9, '0', STR_PAD_LEFT);

            // Ensure first digit is not 0
            if ($barcode[0] === '0') {
                $barcode[0] = (string) (random_int(1, 9));
            }

            // Check uniqueness
            $exists = $this->db->exists('invoices', 'barcode = ?', [$barcode]);

            if (!$exists) {
                return $barcode;
            }
        }

        logMessage("Failed to generate unique barcode after {$maxAttempts} attempts", 'error');
        return null;
    }

    /**
     * Calculate SHA-256 integrity hash for tamper detection
     */
    private function calculateIntegrityHash(
        string $uuid,
        string $barcode,
        string $email,
        float $total,
        string $itemsJson
    ): string {
        // Use app key or a fixed secret for HMAC
        $config = require CONFIG_PATH . '/config.php';
        $secret = $config['security']['invoice_secret'] ?? $config['payment']['ifthenpay']['anti_phishing_key'] ?? 'acasadogi_invoice_secret_2024';

        $payload = implode('|', [
            $uuid,
            $barcode,
            $email,
            number_format($total, 2, '.', ''),
            hash('sha256', $itemsJson),
        ]);

        return hash_hmac('sha256', $payload, $secret);
    }

    /**
     * Default invoice email body when template doesn't exist
     */
    private function getDefaultInvoiceEmailBody(array $invoice, array $items): string
    {
        $siteName = setting('site_name', 'A Casa do Gi');
        $contactEmail = setting('contact_email', 'info@acasadogi.pt');

        $itemsHtml = '';
        foreach ($items as $item) {
            $price = number_format($item['unit_price'], 2, ',', '.');
            $total = number_format($item['total_price'], 2, ',', '.');
            $itemsHtml .= "<tr>
                <td style='padding:8px;border-bottom:1px solid #eee;'>" . htmlspecialchars($item['product_name']) . "</td>
                <td style='padding:8px;border-bottom:1px solid #eee;text-align:center;'>{$item['quantity']}</td>
                <td style='padding:8px;border-bottom:1px solid #eee;text-align:right;'>{$price} EUR</td>
                <td style='padding:8px;border-bottom:1px solid #eee;text-align:right;'>{$total} EUR</td>
            </tr>";
        }

        $subtotal = number_format($invoice['subtotal'], 2, ',', '.');
        $shipping = number_format($invoice['shipping_fee'], 2, ',', '.');
        $total = number_format($invoice['total'], 2, ',', '.');
        $issuedAt = date('d/m/Y H:i', strtotime($invoice['issued_at']));

        return "<!DOCTYPE html>
<html>
<head><meta charset='UTF-8'></head>
<body style='font-family:Arial,sans-serif;background:#f5f5f5;padding:20px;'>
<div style='max-width:600px;margin:0 auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.1);'>
    <div style='background:#264653;padding:30px;text-align:center;'>
        <h1 style='color:#FDFBF7;margin:0;font-size:24px;'>{$siteName}</h1>
        <p style='color:#C5A059;margin:5px 0 0;font-size:14px;'>Fatura</p>
    </div>

    <div style='padding:30px;'>
        <div style='background:#f9f7f2;padding:15px;border-radius:6px;margin-bottom:20px;'>
            <table style='width:100%;'>
                <tr>
                    <td><strong>Codigo de Barras:</strong></td>
                    <td style='text-align:right;font-size:18px;font-weight:bold;color:#264653;'>" . htmlspecialchars($invoice['barcode']) . "</td>
                </tr>
                <tr>
                    <td><strong>UUID:</strong></td>
                    <td style='text-align:right;font-size:11px;color:#666;'>" . htmlspecialchars($invoice['invoice_uuid']) . "</td>
                </tr>
                <tr>
                    <td><strong>Data:</strong></td>
                    <td style='text-align:right;'>{$issuedAt}</td>
                </tr>
                <tr>
                    <td><strong>Estado:</strong></td>
                    <td style='text-align:right;'>" . ($invoice['payment_status'] === 'paid' ? '<span style=\"color:green;\">Pago</span>' : '<span style=\"color:orange;\">Pendente</span>') . "</td>
                </tr>
            </table>
        </div>

        <h3 style='color:#264653;border-bottom:2px solid #C5A059;padding-bottom:8px;'>Dados do Cliente</h3>
        <p><strong>Nome:</strong> " . htmlspecialchars($invoice['customer_name']) . "</p>
        <p><strong>Email:</strong> " . htmlspecialchars($invoice['customer_email']) . "</p>
        " . ($invoice['customer_phone'] ? "<p><strong>Telefone:</strong> " . htmlspecialchars($invoice['customer_phone']) . "</p>" : '') . "
        " . ($invoice['billing_address'] ? "<p><strong>Morada:</strong> " . htmlspecialchars($invoice['billing_address']) . ", " . htmlspecialchars($invoice['billing_postal_code'] ?? '') . " " . htmlspecialchars($invoice['billing_city'] ?? '') . "</p>" : '') . "

        <h3 style='color:#264653;border-bottom:2px solid #C5A059;padding-bottom:8px;'>Produtos</h3>
        <table style='width:100%;border-collapse:collapse;'>
            <thead>
                <tr style='background:#f5f5f5;'>
                    <th style='padding:10px;text-align:left;'>Produto</th>
                    <th style='padding:10px;text-align:center;'>Qtd</th>
                    <th style='padding:10px;text-align:right;'>Preco</th>
                    <th style='padding:10px;text-align:right;'>Total</th>
                </tr>
            </thead>
            <tbody>{$itemsHtml}</tbody>
        </table>

        <div style='margin-top:20px;text-align:right;'>
            <p>Subtotal: <strong>{$subtotal} EUR</strong></p>
            <p>Envio: <strong>{$shipping} EUR</strong></p>
            <p style='font-size:18px;color:#264653;'>Total: <strong>{$total} EUR</strong></p>
        </div>
    </div>

    <div style='background:#264653;padding:20px;text-align:center;color:#FDFBF7;font-size:12px;'>
        <p>Guarde esta fatura como comprovativo da sua compra.</p>
        <p>Duvidas? Contacte-nos: {$contactEmail}</p>
        <p style='margin-top:10px;color:#C5A059;'>&copy; " . date('Y') . " {$siteName}. Todos os direitos reservados.</p>
    </div>
</div>
</body>
</html>";
    }
}
