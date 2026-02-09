<?php
/**
 * A Casa do Gi - Email Mailer Class
 * Wrapper for PHPMailer
 */

namespace Core;

// PHPMailer will be loaded via Composer or manually
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private ?PHPMailer $mailer = null;
    private array $config;
    private bool $phpMailerAvailable = false;

    public function __construct()
    {
        $this->config = require CONFIG_PATH . '/config.php';
        $this->phpMailerAvailable = class_exists('PHPMailer\PHPMailer\PHPMailer');

        if ($this->phpMailerAvailable) {
            $this->initPHPMailer();
        }
    }

    /**
     * Initialize PHPMailer with SMTP settings
     *
     * Priority: DB settings (encrypted, via setting()) > config.php fallback
     */
    private function initPHPMailer(): void
    {
        $this->mailer = new PHPMailer(true);

        try {
            $fileMail = $this->config['mail'];

            // Read from DB settings (auto-decrypted) with config.php fallback
            $host = setting('smtp_host', $fileMail['host'] ?? '');
            $port = (int) setting('smtp_port', $fileMail['port'] ?? 587);
            $username = setting('smtp_user', $fileMail['username'] ?? '');
            $password = setting('smtp_pass', $fileMail['password'] ?? '');
            $fromEmail = setting('smtp_from_email', $fileMail['from_email'] ?? 'noreply@acasadogi.pt');
            $fromName = setting('smtp_from_name', $fileMail['from_name'] ?? 'A Casa do Gi');

            // Server settings
            if (!empty($host)) {
                $this->mailer->isSMTP();
                $this->mailer->Host = $host;
                $this->mailer->Port = $port;

                if (!empty($username)) {
                    $this->mailer->SMTPAuth = true;
                    $this->mailer->Username = $username;
                    $this->mailer->Password = $password;
                }

                $encryption = $fileMail['encryption'] ?? 'tls';
                $this->mailer->SMTPSecure = $encryption === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            }

            // Default sender
            $this->mailer->setFrom($fromEmail, $fromName);

            $replyTo = $fileMail['reply_to'] ?? '';
            if (!empty($replyTo)) {
                $this->mailer->addReplyTo($replyTo);
            }

            // Character encoding
            $this->mailer->CharSet = 'UTF-8';
            $this->mailer->Encoding = 'base64';

        } catch (Exception $e) {
            logMessage("Mailer initialization failed: " . $e->getMessage(), 'error');
        }
    }

    /**
     * Send email
     */
    public function send(
        string|array $to,
        string $subject,
        string $body,
        bool $isHtml = true,
        array $attachments = []
    ): bool {
        // If PHPMailer is not available, use fallback
        if (!$this->phpMailerAvailable || !$this->mailer) {
            return $this->sendFallback($to, $subject, $body, $isHtml);
        }

        try {
            // Reset previous recipients
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();

            // Add recipients
            $recipients = is_array($to) ? $to : [$to];
            foreach ($recipients as $email => $name) {
                if (is_numeric($email)) {
                    $this->mailer->addAddress($name);
                } else {
                    $this->mailer->addAddress($email, $name);
                }
            }

            // Content
            $this->mailer->isHTML($isHtml);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;

            if ($isHtml) {
                $this->mailer->AltBody = strip_tags($body);
            }

            // Attachments
            foreach ($attachments as $attachment) {
                if (is_array($attachment)) {
                    $this->mailer->addAttachment($attachment['path'], $attachment['name'] ?? '');
                } else {
                    $this->mailer->addAttachment($attachment);
                }
            }

            $result = $this->mailer->send();

            if ($result) {
                logMessage("Email sent to: " . implode(', ', $recipients), 'info');
            }

            return $result;

        } catch (Exception $e) {
            logMessage("Email sending failed: " . $this->mailer->ErrorInfo, 'error');
            return false;
        }
    }

    /**
     * Fallback using PHP mail() function
     */
    private function sendFallback(string|array $to, string $subject, string $body, bool $isHtml): bool
    {
        $recipients = is_array($to) ? implode(', ', array_values($to)) : $to;

        $headers = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = $isHtml
            ? 'Content-Type: text/html; charset=UTF-8'
            : 'Content-Type: text/plain; charset=UTF-8';
        $headers[] = 'From: ' . ($this->config['mail']['from_name'] ?? 'A Casa do Gi') .
                     ' <' . ($this->config['mail']['from_email'] ?? 'noreply@acasadogi.pt') . '>';

        $result = @mail($recipients, $subject, $body, implode("\r\n", $headers));

        if ($result) {
            logMessage("Email sent (fallback) to: {$recipients}", 'info');
        } else {
            logMessage("Email fallback failed to: {$recipients}", 'error');
        }

        return $result;
    }

    /**
     * Send contact form notification
     */
    public function sendContactNotification(array $data): bool
    {
        $adminEmail = setting('contact_email', $this->config['mail']['from_email'] ?? '');

        if (empty($adminEmail)) {
            logMessage("Contact notification failed: No admin email configured", 'error');
            return false;
        }

        $subject = "Nova mensagem de contacto: " . ($data['subject'] ?? 'Sem assunto');

        $body = $this->renderTemplate('contact-notification', [
            'name' => $data['name'] ?? '',
            'email' => $data['email'] ?? '',
            'phone' => $data['phone'] ?? '',
            'subject' => $data['subject'] ?? '',
            'message' => $data['message'] ?? '',
            'date' => date('d/m/Y H:i'),
        ]);

        return $this->send($adminEmail, $subject, $body);
    }

    /**
     * Send contact form confirmation to user
     */
    public function sendContactConfirmation(array $data): bool
    {
        if (empty($data['email'])) {
            return false;
        }

        $subject = "Recebemos a sua mensagem - A Casa do Gi";

        $body = $this->renderTemplate('contact-confirmation', [
            'name' => $data['name'] ?? '',
            'message' => $data['message'] ?? '',
        ]);

        return $this->send($data['email'], $subject, $body);
    }

    /**
     * Send order confirmation
     */
    public function sendOrderConfirmation(array $order, array $items): bool
    {
        if (empty($order['customer_email'])) {
            return false;
        }

        $subject = "Confirmação de Encomenda #{$order['order_number']} - A Casa do Gi";

        $body = $this->renderTemplate('order-confirmation', [
            'order' => $order,
            'items' => $items,
        ]);

        return $this->send($order['customer_email'], $subject, $body);
    }

    /**
     * Send order notification to admin
     */
    public function sendOrderNotification(array $order, array $items): bool
    {
        $adminEmail = setting('contact_email', $this->config['mail']['from_email'] ?? '');

        if (empty($adminEmail)) {
            return false;
        }

        $subject = "Nova Encomenda #{$order['order_number']}";

        $body = $this->renderTemplate('order-notification', [
            'order' => $order,
            'items' => $items,
        ]);

        return $this->send($adminEmail, $subject, $body);
    }

    /**
     * Send invoice email
     */
    public function sendInvoice(array $invoice, array $order): bool
    {
        if (empty($invoice['customer_email'])) {
            return false;
        }

        $barcodeFormatted = substr($invoice['barcode'], 0, 3) . ' ' . substr($invoice['barcode'], 3, 3) . ' ' . substr($invoice['barcode'], 6, 3);
        $subject = "Fatura {$barcodeFormatted} - A Casa do Gi";

        $items = json_decode($invoice['items_json'], true) ?: [];

        $body = $this->renderTemplate('invoice', [
            'invoice' => $invoice,
            'order' => $order,
            'items' => $items,
        ]);

        return $this->send($invoice['customer_email'], $subject, $body);
    }

    /**
     * Send order shipped notification
     */
    public function sendOrderShipped(array $order, string $trackingCode = '', array $items = []): bool
    {
        if (empty($order['customer_email'])) {
            return false;
        }

        $subject = "Encomenda Enviada - #{$order['order_number']} - A Casa do Gi";

        $body = $this->renderTemplate('order-shipped', [
            'order' => $order,
            'trackingCode' => $trackingCode,
            'items' => $items,
        ]);

        return $this->send($order['customer_email'], $subject, $body);
    }

    /**
     * Send manual order received confirmation to customer
     */
    public function sendManualOrderReceived(array $manualOrder): bool
    {
        if (empty($manualOrder['customer_email'])) {
            return false;
        }

        $subject = "Pedido Recebido - A Casa do Gi";

        $body = $this->renderTemplate('manual-order-received', [
            'manualOrder' => $manualOrder,
        ]);

        return $this->send($manualOrder['customer_email'], $subject, $body);
    }

    /**
     * Send manual order notification to admin
     */
    public function sendManualOrderNotification(array $manualOrder): bool
    {
        $adminEmail = setting('contact_email', $this->config['mail']['from_email'] ?? '');

        if (empty($adminEmail)) {
            return false;
        }

        $items = json_decode($manualOrder['items_json'] ?? '[]', true) ?: [];
        $itemsList = '';
        foreach ($items as $item) {
            $name = $item['name'] ?? $item['product_name'] ?? 'Produto';
            $qty = (int)($item['quantity'] ?? 1);
            $itemsList .= "- {$name} (x{$qty})\n";
        }

        $subject = "Novo Pedido Manual - {$manualOrder['customer_name']}";

        $body = "<div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:#264653;padding:30px;text-align:center;border-radius:8px 8px 0 0;'>
                <h1 style='color:#FDFBF7;margin:0;'>A Casa do Gi</h1>
                <p style='color:#C5A059;margin:5px 0 0;'>Novo Pedido Manual</p>
            </div>
            <div style='padding:30px;background:#fff;border:1px solid #eee;'>
                <h2 style='color:#264653;margin-top:0;'>Novo pedido manual recebido</h2>
                <p><strong>Nome:</strong> " . htmlspecialchars($manualOrder['customer_name']) . "</p>
                <p><strong>Email:</strong> " . htmlspecialchars($manualOrder['customer_email']) . "</p>
                <p><strong>Telefone:</strong> " . htmlspecialchars($manualOrder['customer_phone'] ?? 'N/A') . "</p>
                <p><strong>Total:</strong> " . number_format($manualOrder['total'] ?? 0, 2, ',', '.') . "&euro;</p>
                <hr style='border:none;border-top:1px solid #eee;margin:15px 0;'>
                <p><strong>Produtos:</strong></p>
                <pre style='background:#f9f9f9;padding:12px;border-radius:4px;font-size:14px;'>" . htmlspecialchars($itemsList) . "</pre>
                " . (!empty($manualOrder['notes']) ? "<p><strong>Notas:</strong> " . htmlspecialchars($manualOrder['notes']) . "</p>" : '') . "
            </div>
            <div style='background:#264653;padding:15px;text-align:center;border-radius:0 0 8px 8px;'>
                <p style='color:#FDFBF7;font-size:12px;margin:0;'>&copy; " . date('Y') . " A Casa do Gi</p>
            </div>
        </div>";

        return $this->send($adminEmail, $subject, $body);
    }

    /**
     * Render email template
     */
    private function renderTemplate(string $template, array $data = []): string
    {
        $templateFile = TEMPLATES_PATH . '/emails/' . $template . '.php';

        if (!file_exists($templateFile)) {
            // Fallback to simple template
            return $this->getDefaultTemplate($template, $data);
        }

        ob_start();
        extract($data);
        include $templateFile;
        return ob_get_clean();
    }

    /**
     * Get default email template
     */
    private function getDefaultTemplate(string $template, array $data): string
    {
        $siteName = setting('site_name', 'A Casa do Gi');

        $baseStyle = "
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #faf3e6;
            padding: 20px;
        ";

        $containerStyle = "
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        ";

        $headerStyle = "
            background-color: #657954;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        ";

        $contentStyle = "
            padding: 30px;
            color: #46433f;
            line-height: 1.6;
        ";

        $footerStyle = "
            background-color: #f7f7f6;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #87837b;
        ";

        switch ($template) {
            case 'contact-notification':
                return "
                <div style='{$baseStyle}'>
                    <div style='{$containerStyle}'>
                        <div style='{$headerStyle}'>
                            <h1 style='margin:0;font-size:24px;'>{$siteName}</h1>
                            <p style='margin:10px 0 0;opacity:0.9;'>Nova Mensagem de Contacto</p>
                        </div>
                        <div style='{$contentStyle}'>
                            <p><strong>Nome:</strong> {$data['name']}</p>
                            <p><strong>Email:</strong> {$data['email']}</p>
                            <p><strong>Telefone:</strong> {$data['phone']}</p>
                            <p><strong>Assunto:</strong> {$data['subject']}</p>
                            <hr style='border:none;border-top:1px solid #e5e4e2;margin:20px 0;'>
                            <p><strong>Mensagem:</strong></p>
                            <p style='background:#f7f7f6;padding:15px;border-radius:4px;'>" . nl2br(htmlspecialchars($data['message'])) . "</p>
                            <p style='font-size:12px;color:#87837b;margin-top:20px;'>Recebido em: {$data['date']}</p>
                        </div>
                        <div style='{$footerStyle}'>
                            <p>&copy; " . date('Y') . " {$siteName}</p>
                        </div>
                    </div>
                </div>";

            case 'contact-confirmation':
                return "
                <div style='{$baseStyle}'>
                    <div style='{$containerStyle}'>
                        <div style='{$headerStyle}'>
                            <h1 style='margin:0;font-size:24px;'>{$siteName}</h1>
                        </div>
                        <div style='{$contentStyle}'>
                            <h2 style='color:#657954;margin-top:0;'>Olá {$data['name']},</h2>
                            <p>Recebemos a sua mensagem e iremos responder o mais brevemente possível.</p>
                            <p>Obrigado por entrar em contacto connosco!</p>
                            <hr style='border:none;border-top:1px solid #e5e4e2;margin:20px 0;'>
                            <p style='font-size:14px;color:#87837b;'>A sua mensagem:</p>
                            <p style='background:#f7f7f6;padding:15px;border-radius:4px;font-style:italic;'>" . nl2br(htmlspecialchars($data['message'])) . "</p>
                        </div>
                        <div style='{$footerStyle}'>
                            <p>Com os melhores cumprimentos,<br><strong>{$siteName}</strong></p>
                            <p>&copy; " . date('Y') . " {$siteName}</p>
                        </div>
                    </div>
                </div>";

            default:
                return "<p>" . print_r($data, true) . "</p>";
        }
    }
}
