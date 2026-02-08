<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background-color:#FDFBF7;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#FDFBF7;padding:20px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.08);">
                    <!-- Header -->
                    <tr>
                        <td style="background-color:#264653;padding:30px;text-align:center;">
                            <h1 style="color:#FDFBF7;margin:0;font-size:24px;font-weight:700;">A Casa do Gi</h1>
                            <p style="color:#C5A059;margin:8px 0 0;font-size:14px;">Encomenda Confirmada</p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding:30px;">
                            <!-- Success Icon -->
                            <div style="text-align:center;margin-bottom:24px;">
                                <div style="display:inline-block;background-color:#dcfce7;border-radius:50%;padding:16px;">
                                    <span style="font-size:32px;">&#10003;</span>
                                </div>
                            </div>

                            <h2 style="color:#264653;margin:0 0 16px;font-size:20px;text-align:center;">A sua encomenda foi confirmada!</h2>

                            <p style="color:#46433f;line-height:1.6;margin:0 0 8px;">
                                Ola <strong><?= htmlspecialchars($order['customer_name']) ?></strong>,
                            </p>
                            <p style="color:#46433f;line-height:1.6;margin:0 0 20px;">
                                A sua encomenda <strong>#<?= htmlspecialchars($order['order_number']) ?></strong> foi recebida e confirmada com sucesso. Estamos a preparar os seus produtos com todo o cuidado.
                            </p>

                            <!-- Order Summary -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f9fafb;border-radius:8px;overflow:hidden;margin-bottom:20px;">
                                <tr>
                                    <td style="padding:16px;">
                                        <h3 style="color:#264653;margin:0 0 12px;font-size:16px;">Resumo da Encomenda</h3>
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <?php foreach ($items as $item): ?>
                                            <tr>
                                                <td style="padding:6px 0;color:#46433f;font-size:14px;">
                                                    <?= htmlspecialchars($item['product_name']) ?> (x<?= (int)$item['quantity'] ?>)
                                                </td>
                                                <td style="padding:6px 0;text-align:right;color:#46433f;font-size:14px;font-weight:600;">
                                                    <?= number_format($item['total_price'], 2, ',', '.') ?>&euro;
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <tr>
                                                <td colspan="2" style="border-top:1px solid #e5e7eb;padding-top:8px;margin-top:8px;"></td>
                                            </tr>
                                            <tr>
                                                <td style="padding:4px 0;color:#6b7280;font-size:14px;">Subtotal</td>
                                                <td style="padding:4px 0;text-align:right;color:#46433f;font-size:14px;"><?= number_format($order['subtotal'], 2, ',', '.') ?>&euro;</td>
                                            </tr>
                                            <?php if ($order['shipping_fee'] > 0): ?>
                                            <tr>
                                                <td style="padding:4px 0;color:#6b7280;font-size:14px;">Portes de Envio</td>
                                                <td style="padding:4px 0;text-align:right;color:#46433f;font-size:14px;"><?= number_format($order['shipping_fee'], 2, ',', '.') ?>&euro;</td>
                                            </tr>
                                            <?php endif; ?>
                                            <?php if (!empty($order['discount_amount']) && $order['discount_amount'] > 0): ?>
                                            <tr>
                                                <td style="padding:4px 0;color:#6b7280;font-size:14px;">Desconto</td>
                                                <td style="padding:4px 0;text-align:right;color:#16a34a;font-size:14px;">-<?= number_format($order['discount_amount'], 2, ',', '.') ?>&euro;</td>
                                            </tr>
                                            <?php endif; ?>
                                            <tr>
                                                <td style="padding:8px 0 0;color:#264653;font-size:16px;font-weight:700;">Total</td>
                                                <td style="padding:8px 0 0;text-align:right;color:#C5A059;font-size:18px;font-weight:700;"><?= number_format($order['total'], 2, ',', '.') ?>&euro;</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Payment Method -->
                            <?php if (!empty($order['payment_method'])): ?>
                            <p style="color:#6b7280;font-size:14px;margin:0 0 4px;">Metodo de pagamento: <strong style="color:#46433f;"><?= strtoupper(htmlspecialchars($order['payment_method'])) ?></strong></p>
                            <?php endif; ?>

                            <!-- Shipping Address -->
                            <div style="background-color:#f9fafb;border-radius:8px;padding:16px;margin-top:20px;">
                                <h3 style="color:#264653;margin:0 0 8px;font-size:14px;">Morada de Envio</h3>
                                <p style="color:#46433f;font-size:14px;line-height:1.5;margin:0;">
                                    <?= htmlspecialchars($order['customer_name']) ?><br>
                                    <?php
                                    $shippingAddr = !empty($order['shipping_address']) ? $order['shipping_address'] : ($order['billing_address'] ?? '');
                                    $shippingPostal = !empty($order['shipping_postal_code']) ? $order['shipping_postal_code'] : ($order['billing_postal_code'] ?? '');
                                    $shippingCity = !empty($order['shipping_city']) ? $order['shipping_city'] : ($order['billing_city'] ?? '');
                                    ?>
                                    <?= nl2br(htmlspecialchars($shippingAddr)) ?><br>
                                    <?= htmlspecialchars($shippingPostal) ?> <?= htmlspecialchars($shippingCity) ?>
                                </p>
                            </div>

                            <!-- Next Steps -->
                            <div style="margin-top:24px;padding:16px;background-color:#eff6ff;border-radius:8px;border-left:4px solid #264653;">
                                <h3 style="color:#264653;margin:0 0 8px;font-size:14px;">Proximos Passos</h3>
                                <p style="color:#46433f;font-size:14px;line-height:1.6;margin:0;">
                                    A sua encomenda sera preparada e enviada o mais rapidamente possivel. Recebera um email com o codigo de rastreio assim que a encomenda for expedida.
                                </p>
                            </div>

                            <!-- Contact -->
                            <p style="color:#6b7280;font-size:13px;line-height:1.6;margin:20px 0 0;">
                                Se tiver alguma questao, nao hesite em contactar-nos respondendo a este email.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#264653;padding:20px;text-align:center;">
                            <p style="color:#FDFBF7;font-size:12px;margin:0;">
                                &copy; <?= date('Y') ?> A Casa do Gi - Todos os direitos reservados
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
