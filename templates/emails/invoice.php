<?php

$issuedAt = date('d/m/Y H:i', strtotime($invoice['issued_at']));
$paymentLabel = match($invoice['payment_method'] ?? '') {
    'mbway' => 'MB WAY',
    'multibanco' => 'Multibanco',
    'card' => 'Cartao de Credito/Debito',
    default => ucfirst($invoice['payment_method'] ?? '-'),
};
$statusLabel = match($invoice['payment_status']) {
    'paid' => 'Pago',
    'pending' => 'Pendente',
    'failed' => 'Falhado',
    'refunded' => 'Reembolsado',
    default => ucfirst($invoice['payment_status']),
};
$statusColor = match($invoice['payment_status']) {
    'paid' => '#22c55e',
    'pending' => '#eab308',
    'failed' => '#ef4444',
    'refunded' => '#6b7280',
    default => '#6b7280',
};
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fatura #<?= htmlspecialchars($invoice['barcode']) ?></title>
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Arial,sans-serif;background-color:#f5f3ee;color:#2D3748;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f5f3ee;">
<tr><td align="center" style="padding:30px 15px;">

<table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">

    <!-- Header -->
    <tr>
        <td style="background-color:#264653;padding:35px 40px;text-align:center;">
            <h1 style="margin:0;font-family:Georgia,serif;font-size:28px;color:#FDFBF7;letter-spacing:1px;">
                <?= htmlspecialchars($siteName) ?>
            </h1>
            <div style="width:60px;height:2px;background-color:#C5A059;margin:12px auto;"></div>
            <p style="margin:0;color:#C5A059;font-size:13px;text-transform:uppercase;letter-spacing:3px;">Fatura</p>
        </td>
    </tr>

    <!-- Invoice Codes -->
    <tr>
        <td style="padding:30px 40px 0;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f9f7f2;border-radius:8px;border:1px solid #e8e4db;">
                <tr>
                    <td style="padding:20px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="padding-bottom:12px;">
                                    <span style="font-size:12px;color:#768A68;text-transform:uppercase;letter-spacing:1px;">Codigo de Barras</span><br>
                                    <span style="font-size:26px;font-weight:bold;color:#264653;letter-spacing:3px;font-family:'Courier New',monospace;">
                                        <?= substr($invoice['barcode'], 0, 3) ?> <?= substr($invoice['barcode'], 3, 3) ?> <?= substr($invoice['barcode'], 6, 3) ?>
                                    </span>
                                </td>
                                <td style="text-align:right;padding-bottom:12px;">
                                    <span style="font-size:12px;color:#768A68;text-transform:uppercase;letter-spacing:1px;">Data</span><br>
                                    <span style="font-size:14px;color:#264653;"><?= $issuedAt ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="border-top:1px solid #e8e4db;padding-top:12px;">
                                    <span style="font-size:12px;color:#768A68;text-transform:uppercase;letter-spacing:1px;">UUID</span><br>
                                    <span style="font-size:11px;color:#666;font-family:'Courier New',monospace;"><?= htmlspecialchars($invoice['invoice_uuid']) ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-top:12px;">
                                    <span style="font-size:12px;color:#768A68;text-transform:uppercase;letter-spacing:1px;">Pagamento</span><br>
                                    <span style="font-size:14px;color:#264653;"><?= $paymentLabel ?></span>
                                </td>
                                <td style="text-align:right;padding-top:12px;">
                                    <span style="display:inline-block;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:bold;color:#fff;background-color:<?= $statusColor ?>;">
                                        <?= $statusLabel ?>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Customer Info -->
    <tr>
        <td style="padding:25px 40px 0;">
            <h3 style="margin:0 0 12px;font-size:14px;color:#264653;text-transform:uppercase;letter-spacing:1px;border-bottom:2px solid #C5A059;padding-bottom:8px;">
                Dados do Cliente
            </h3>
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="padding:4px 0;font-size:14px;">
                        <strong>Nome:</strong> <?= htmlspecialchars($invoice['customer_name']) ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding:4px 0;font-size:14px;">
                        <strong>Email:</strong> <?= htmlspecialchars($invoice['customer_email']) ?>
                    </td>
                </tr>
                <?php if (!empty($invoice['customer_phone'])): ?>
                <tr>
                    <td style="padding:4px 0;font-size:14px;">
                        <strong>Telefone:</strong> <?= htmlspecialchars($invoice['customer_phone']) ?>
                    </td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($invoice['billing_address'])): ?>
                <tr>
                    <td style="padding:4px 0;font-size:14px;">
                        <strong>Morada:</strong> <?= htmlspecialchars($invoice['billing_address']) ?><?php if (!empty($invoice['billing_postal_code'])): ?>, <?= htmlspecialchars($invoice['billing_postal_code']) ?> <?= htmlspecialchars($invoice['billing_city'] ?? '') ?><?php endif; ?>
                    </td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($invoice['customer_nif'])): ?>
                <tr>
                    <td style="padding:4px 0;font-size:14px;">
                        <strong>NIF:</strong> <?= htmlspecialchars($invoice['customer_nif']) ?>
                    </td>
                </tr>
                <?php endif; ?>
            </table>
        </td>
    </tr>

    <!-- Products Table -->
    <tr>
        <td style="padding:25px 40px 0;">
            <h3 style="margin:0 0 12px;font-size:14px;color:#264653;text-transform:uppercase;letter-spacing:1px;border-bottom:2px solid #C5A059;padding-bottom:8px;">
                Produtos
            </h3>
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                <tr style="background-color:#f5f3ee;">
                    <th style="padding:10px;text-align:left;font-size:12px;color:#264653;text-transform:uppercase;">Produto</th>
                    <th style="padding:10px;text-align:center;font-size:12px;color:#264653;text-transform:uppercase;">Qtd</th>
                    <th style="padding:10px;text-align:right;font-size:12px;color:#264653;text-transform:uppercase;">Preco</th>
                    <th style="padding:10px;text-align:right;font-size:12px;color:#264653;text-transform:uppercase;">Total</th>
                </tr>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td style="padding:10px;border-bottom:1px solid #eee;font-size:14px;">
                        <?= htmlspecialchars($item['product_name']) ?>
                    </td>
                    <td style="padding:10px;border-bottom:1px solid #eee;text-align:center;font-size:14px;">
                        <?= (int)$item['quantity'] ?>
                    </td>
                    <td style="padding:10px;border-bottom:1px solid #eee;text-align:right;font-size:14px;">
                        <?= number_format($item['unit_price'], 2, ',', '.') ?>&euro;
                    </td>
                    <td style="padding:10px;border-bottom:1px solid #eee;text-align:right;font-size:14px;font-weight:bold;">
                        <?= number_format($item['total_price'], 2, ',', '.') ?>&euro;
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </td>
    </tr>

    <!-- Totals -->
    <tr>
        <td style="padding:20px 40px 0;">
            <table role="presentation" width="250" cellspacing="0" cellpadding="0" align="right">
                <tr>
                    <td style="padding:6px 0;font-size:14px;color:#666;">Subtotal</td>
                    <td style="padding:6px 0;font-size:14px;text-align:right;"><?= number_format($invoice['subtotal'], 2, ',', '.') ?>&euro;</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;font-size:14px;color:#666;">Envio</td>
                    <td style="padding:6px 0;font-size:14px;text-align:right;">
                        <?php if ((float)$invoice['shipping_fee'] === 0.0): ?>
                            <span style="color:#768A68;">Gratis</span>
                        <?php else: ?>
                            <?= number_format($invoice['shipping_fee'], 2, ',', '.') ?>&euro;
                        <?php endif; ?>
                    </td>
                </tr>
                <?php if ((float)$invoice['discount_amount'] > 0): ?>
                <tr>
                    <td style="padding:6px 0;font-size:14px;color:#666;">Desconto</td>
                    <td style="padding:6px 0;font-size:14px;text-align:right;color:#ef4444;">-<?= number_format($invoice['discount_amount'], 2, ',', '.') ?>&euro;</td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td style="padding:12px 0 6px;font-size:18px;font-weight:bold;color:#264653;border-top:2px solid #264653;">Total</td>
                    <td style="padding:12px 0 6px;font-size:18px;font-weight:bold;color:#264653;text-align:right;border-top:2px solid #264653;">
                        <?= number_format($invoice['total'], 2, ',', '.') ?>&euro;
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Important Notice -->
    <tr>
        <td style="padding:30px 40px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f0f7f4;border-radius:8px;border-left:4px solid #768A68;">
                <tr>
                    <td style="padding:15px 20px;">
                        <p style="margin:0 0 8px;font-size:13px;font-weight:bold;color:#264653;">Guarde esta fatura</p>
                        <p style="margin:0;font-size:12px;color:#555;line-height:1.6;">
                            Este email serve como comprovativo da sua compra. Os codigos acima identificam unicamente esta fatura e podem ser usados para verificacao a qualquer momento.
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Footer -->
    <tr>
        <td style="background-color:#264653;padding:25px 40px;text-align:center;">
            <p style="margin:0 0 8px;font-size:12px;color:#FDFBF7;">
                Duvidas? Contacte-nos:
                <a href="mailto:<?= htmlspecialchars($contactEmail) ?>" style="color:#C5A059;text-decoration:none;"><?= htmlspecialchars($contactEmail) ?></a>
                <?php if (!empty($contactPhone)): ?>
                    | <a href="tel:<?= htmlspecialchars($contactPhone) ?>" style="color:#C5A059;text-decoration:none;"><?= htmlspecialchars($contactPhone) ?></a>
                <?php endif; ?>
            </p>
            <p style="margin:0;font-size:11px;color:#C5A059;">
                &copy; <?= date('Y') ?> <?= htmlspecialchars($siteName) ?>. Todos os direitos reservados.
            </p>
        </td>
    </tr>

</table>

</td></tr>
</table>
</body>
</html>
