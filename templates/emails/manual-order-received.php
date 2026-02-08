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
                            <p style="color:#C5A059;margin:8px 0 0;font-size:14px;">Pedido Manual Recebido</p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding:30px;">
                            <!-- Phone Icon -->
                            <div style="text-align:center;margin-bottom:24px;">
                                <div style="display:inline-block;background-color:#fef3c7;border-radius:50%;padding:16px;">
                                    <span style="font-size:32px;">&#128222;</span>
                                </div>
                            </div>

                            <h2 style="color:#264653;margin:0 0 16px;font-size:20px;text-align:center;">Recebemos o seu pedido!</h2>

                            <p style="color:#46433f;line-height:1.6;margin:0 0 8px;">
                                Ola <strong><?= htmlspecialchars($manualOrder['customer_name']) ?></strong>,
                            </p>
                            <p style="color:#46433f;line-height:1.6;margin:0 0 20px;">
                                O seu pedido foi recebido com sucesso. A nossa equipa vai entrar em contacto consigo nas proximas <strong>24 horas</strong> para combinar os detalhes do pagamento e da entrega.
                            </p>

                            <!-- Order Items -->
                            <?php
                            $items = [];
                            if (!empty($manualOrder['items_json'])) {
                                $items = json_decode($manualOrder['items_json'], true) ?: [];
                            }
                            ?>
                            <?php if (!empty($items)): ?>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f9fafb;border-radius:8px;overflow:hidden;margin-bottom:20px;">
                                <tr>
                                    <td style="padding:16px;">
                                        <h3 style="color:#264653;margin:0 0 12px;font-size:16px;">Os seus produtos:</h3>
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <?php foreach ($items as $item): ?>
                                            <tr>
                                                <td style="padding:6px 0;color:#46433f;font-size:14px;">
                                                    <?= htmlspecialchars($item['name'] ?? $item['product_name'] ?? 'Produto') ?> (x<?= (int)($item['quantity'] ?? 1) ?>)
                                                </td>
                                                <td style="padding:6px 0;text-align:right;color:#46433f;font-size:14px;font-weight:600;">
                                                    <?php
                                                    $itemTotal = ($item['total_price'] ?? ($item['price'] ?? 0) * ($item['quantity'] ?? 1));
                                                    echo number_format($itemTotal, 2, ',', '.') . '&euro;';
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <tr>
                                                <td colspan="2" style="border-top:1px solid #e5e7eb;padding-top:8px;"></td>
                                            </tr>
                                            <tr>
                                                <td style="padding:4px 0;color:#264653;font-size:16px;font-weight:700;">Total Estimado</td>
                                                <td style="padding:4px 0;text-align:right;color:#C5A059;font-size:18px;font-weight:700;">
                                                    <?= number_format($manualOrder['total'] ?? 0, 2, ',', '.') ?>&euro;
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <?php endif; ?>

                            <!-- What Happens Next -->
                            <div style="background-color:#fefce8;border-radius:8px;padding:16px;border-left:4px solid #C5A059;margin-bottom:20px;">
                                <h3 style="color:#264653;margin:0 0 8px;font-size:14px;">O que acontece a seguir?</h3>
                                <ol style="color:#46433f;font-size:14px;line-height:1.8;margin:0;padding-left:20px;">
                                    <li>A nossa equipa vai analisar o seu pedido</li>
                                    <li>Entraremos em contacto por telefone ou email</li>
                                    <li>Combinamos o pagamento e forma de entrega</li>
                                    <li>Preparamos e enviamos a sua encomenda</li>
                                </ol>
                            </div>

                            <!-- Contact Info -->
                            <div style="background-color:#f9fafb;border-radius:8px;padding:16px;">
                                <h3 style="color:#264653;margin:0 0 8px;font-size:14px;">Os seus dados de contacto</h3>
                                <p style="color:#46433f;font-size:14px;line-height:1.6;margin:0;">
                                    <strong>Email:</strong> <?= htmlspecialchars($manualOrder['customer_email']) ?><br>
                                    <?php if (!empty($manualOrder['customer_phone'])): ?>
                                    <strong>Telefone:</strong> <?= htmlspecialchars($manualOrder['customer_phone']) ?><br>
                                    <?php endif; ?>
                                    <?php if (!empty($manualOrder['address'])): ?>
                                    <strong>Morada:</strong> <?= htmlspecialchars($manualOrder['address']) ?>
                                    <?php endif; ?>
                                </p>
                            </div>

                            <!-- Help -->
                            <p style="color:#6b7280;font-size:13px;line-height:1.6;margin:20px 0 0;">
                                Se tiver alguma questao urgente, pode contactar-nos diretamente respondendo a este email.
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
