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
                            <p style="color:#C5A059;margin:8px 0 0;font-size:14px;">A sua encomenda foi enviada!</p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding:30px;">
                            <!-- Truck Icon -->
                            <div style="text-align:center;margin-bottom:24px;">
                                <div style="display:inline-block;background-color:#dbeafe;border-radius:50%;padding:16px;">
                                    <span style="font-size:32px;">&#128666;</span>
                                </div>
                            </div>

                            <h2 style="color:#264653;margin:0 0 16px;font-size:20px;text-align:center;">A sua encomenda esta a caminho!</h2>

                            <p style="color:#46433f;line-height:1.6;margin:0 0 8px;">
                                Ola <strong><?= htmlspecialchars($order['customer_name']) ?></strong>,
                            </p>
                            <p style="color:#46433f;line-height:1.6;margin:0 0 20px;">
                                Temos boas noticias! A sua encomenda <strong>#<?= htmlspecialchars($order['order_number']) ?></strong> foi enviada e esta a caminho.
                            </p>

                            <?php if (!empty($trackingCode)): ?>
                            <!-- Tracking Code -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0fdf4;border:2px solid #bbf7d0;border-radius:8px;overflow:hidden;margin-bottom:20px;">
                                <tr>
                                    <td style="padding:20px;text-align:center;">
                                        <p style="color:#166534;font-size:13px;margin:0 0 8px;text-transform:uppercase;letter-spacing:1px;">Codigo de Rastreio</p>
                                        <p style="color:#264653;font-size:24px;font-weight:700;margin:0;letter-spacing:2px;font-family:'Courier New',monospace;">
                                            <?= htmlspecialchars($trackingCode) ?>
                                        </p>
                                        <p style="color:#6b7280;font-size:13px;margin:12px 0 0;">
                                            Pode acompanhar a sua encomenda no site da transportadora usando este codigo.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            <?php endif; ?>

                            <!-- Order Items -->
                            <?php if (!empty($items)): ?>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f9fafb;border-radius:8px;overflow:hidden;margin-bottom:20px;">
                                <tr>
                                    <td style="padding:16px;">
                                        <h3 style="color:#264653;margin:0 0 12px;font-size:14px;">O que vai receber:</h3>
                                        <?php foreach ($items as $item): ?>
                                        <p style="color:#46433f;font-size:14px;margin:0 0 4px;">
                                            &#8226; <?= htmlspecialchars($item['product_name']) ?> (x<?= (int)$item['quantity'] ?>)
                                        </p>
                                        <?php endforeach; ?>
                                    </td>
                                </tr>
                            </table>
                            <?php endif; ?>

                            <!-- Delivery Info -->
                            <div style="background-color:#eff6ff;border-radius:8px;padding:16px;border-left:4px solid #264653;margin-bottom:20px;">
                                <h3 style="color:#264653;margin:0 0 8px;font-size:14px;">Informacao de Entrega</h3>
                                <p style="color:#46433f;font-size:14px;line-height:1.6;margin:0;">
                                    <strong>Prazo estimado:</strong> 3-5 dias uteis<br>
                                    <?php
                                    $shippingAddr = !empty($order['shipping_address']) ? $order['shipping_address'] : ($order['billing_address'] ?? '');
                                    $shippingPostal = !empty($order['shipping_postal_code']) ? $order['shipping_postal_code'] : ($order['billing_postal_code'] ?? '');
                                    $shippingCity = !empty($order['shipping_city']) ? $order['shipping_city'] : ($order['billing_city'] ?? '');
                                    ?>
                                    <strong>Morada:</strong> <?= htmlspecialchars($shippingAddr) ?>, <?= htmlspecialchars($shippingPostal) ?> <?= htmlspecialchars($shippingCity) ?>
                                </p>
                            </div>

                            <!-- Help -->
                            <p style="color:#6b7280;font-size:13px;line-height:1.6;margin:0;">
                                Se tiver alguma questao sobre a sua encomenda, nao hesite em contactar-nos respondendo a este email.
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
