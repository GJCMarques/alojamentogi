<?php
/**
 * A Casa do Gi - Terms & Conditions (Termos e Condições)
 */

require_once dirname(__DIR__) . '/includes/init.php';

$lang = \Core\Language::getInstance();
$isEnglish = $lang->isEnglish();
$base = basePath();

// Page meta
$pageTitle = $isEnglish ? 'Terms & Conditions' : 'Termos e Condições';
$pageDescription = $isEnglish
    ? 'Terms and conditions for purchases at A Casa do Gi online store.'
    : 'Termos e condições de compra na loja online A Casa do Gi.';

include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section -->
<section class="relative min-h-[40vh] flex items-center justify-center bg-gradient-to-br from-primary via-primary-600 to-primary-700">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 right-10 w-64 h-64 bg-accent rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 left-10 w-80 h-80 bg-secondary rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
        <h1 class="font-serif text-4xl sm:text-5xl md:text-6xl font-bold text-cream mb-4 animate-on-scroll">
            <?= $isEnglish ? 'Terms & Conditions' : 'Termos e Condições' ?>
        </h1>
        <p class="text-cream-200 text-lg sm:text-xl animate-on-scroll" data-delay="100">
            <?= $isEnglish
                ? 'General conditions of sale and use'
                : 'Condições gerais de venda e utilização' ?>
        </p>
    </div>
</section>

<!-- Breadcrumbs -->
<nav class="bg-cream-100 border-b border-primary/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <ol class="flex items-center space-x-2 text-sm">
            <li>
                <a href="<?= $isEnglish ? $base . '/en/' : $base . '/' ?>" class="text-primary-600 hover:text-accent transition-colors">
                    <?= $isEnglish ? 'Home' : 'Início' ?>
                </a>
            </li>
            <li class="text-primary-400">/</li>
            <li class="text-charcoal font-medium">
                <?= $isEnglish ? 'Terms & Conditions' : 'Termos e Condições' ?>
            </li>
        </ol>
    </div>
</nav>

<!-- Content Section -->
<section class="py-16 bg-cream">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12 animate-on-scroll">

            <?php if ($isEnglish): ?>
            <!-- ENGLISH VERSION -->
            <div class="prose prose-lg max-w-none">
                <p class="text-sm text-charcoal-600 mb-8">Last updated: <?= date('F d, Y') ?></p>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">1. Company Identification</h2>
                    <p class="text-charcoal-700 leading-relaxed">
                        Trade name: <strong>A Casa do Gi</strong><br>
                        Address: 52 Avenida Nossa Senhora do Caminho, Mogadouro, Portugal<br>
                        Email: <?= e(setting('contact_email', 'info@acasadogi.com')) ?><br>
                        Phone: <?= e(setting('contact_phone', '+351 XXX XXX XXX')) ?>
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">2. Purpose and Acceptance</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        These Terms and Conditions regulate the use of the A Casa do Gi online store and the purchase of regional products from Trás-os-Montes.
                    </p>
                    <p class="text-charcoal-700 leading-relaxed">
                        By placing an order, you confirm that you have read, understood, and accepted these Terms and Conditions in full.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">3. Products and Prices</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        All products displayed in our store are regional products from Trás-os-Montes, Portugal, carefully selected for their quality and authenticity.
                    </p>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        Prices are shown in Euros (€) and include VAT at the applicable legal rate. Shipping costs are displayed separately during checkout.
                    </p>
                    <p class="text-charcoal-700 leading-relaxed">
                        We reserve the right to adjust prices at any time, but orders already placed will honor the price displayed at the time of purchase.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">4. Purchase Process</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        To purchase products:
                    </p>
                    <ol class="list-decimal list-inside space-y-2 text-charcoal-700 ml-4">
                        <li>Select products and add them to your cart</li>
                        <li>Review your order in the shopping cart</li>
                        <li>Provide delivery information and billing details</li>
                        <li>Choose your payment method</li>
                        <li>Confirm your order</li>
                    </ol>
                    <p class="text-charcoal-700 leading-relaxed mt-4">
                        You will receive an order confirmation email. Your order is only confirmed after successful payment verification.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">5. Payment</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        We accept the following payment methods via IfthenPay:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-charcoal-700 ml-4">
                        <li><strong>MBWay</strong> - Instant payment via mobile phone</li>
                        <li><strong>Multibanco</strong> - Bank transfer with reference (payment within 3 days)</li>
                        <li><strong>Credit/Debit Card</strong> - Secure payment through IfthenPay gateway</li>
                    </ul>
                    <p class="text-charcoal-700 leading-relaxed mt-4">
                        All payments are processed securely through IfthenPay. A Casa do Gi does not store your payment card information.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">6. Shipping and Delivery</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        Orders are shipped within 2-3 business days after payment confirmation.
                    </p>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        Delivery times:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-charcoal-700 ml-4">
                        <li>Portugal Mainland: 3-5 business days</li>
                        <li>Azores and Madeira: 5-7 business days</li>
                    </ul>
                    <p class="text-charcoal-700 leading-relaxed mt-4">
                        You will receive a tracking number to monitor your shipment. Shipping costs are calculated during checkout based on weight and destination.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">7. Right of Withdrawal</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        In accordance with Portuguese law (Decree-Law No. 24/2014), you have the right to withdraw from your purchase within 14 calendar days from the date of receipt, without providing a reason.
                    </p>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        To exercise your right of withdrawal, please contact us at <a href="mailto:<?= e(setting('contact_email', 'info@acasadogi.com')) ?>" class="text-accent hover:underline"><?= e(setting('contact_email', 'info@acasadogi.com')) ?></a> and return the products in their original condition and packaging.
                    </p>
                    <p class="text-charcoal-700 leading-relaxed">
                        <strong>Note:</strong> Food products cannot be returned once opened, for health and safety reasons.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">8. Returns and Exchanges</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        If you receive a defective or damaged product, please contact us within 48 hours of receipt. We will arrange for a replacement or full refund.
                    </p>
                    <p class="text-charcoal-700 leading-relaxed">
                        Return shipping costs for defective products are covered by A Casa do Gi.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">9. Warranties</h2>
                    <p class="text-charcoal-700 leading-relaxed">
                        All products are covered by the legal warranty period of 2 years for defects in conformity, in accordance with Portuguese consumer protection law.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">10. Intellectual Property</h2>
                    <p class="text-charcoal-700 leading-relaxed">
                        All content on this website, including text, images, logos, and graphics, is the property of A Casa do Gi and is protected by Portuguese and international copyright laws. Unauthorized use is prohibited.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">11. Data Protection</h2>
                    <p class="text-charcoal-700 leading-relaxed">
                        Your personal data is processed in accordance with our <a href="<?= $lang->url('politica-privacidade') ?>" class="text-accent hover:underline font-semibold">Privacy Policy</a> and the General Data Protection Regulation (GDPR).
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">12. Applicable Law and Jurisdiction</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        These Terms and Conditions are governed by Portuguese law.
                    </p>
                    <p class="text-charcoal-700 leading-relaxed">
                        Any disputes arising from these terms will be resolved by the competent Portuguese courts, without prejudice to the consumer's right to use alternative dispute resolution mechanisms.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">13. Contact</h2>
                    <p class="text-charcoal-700 leading-relaxed">
                        For questions or concerns about these Terms and Conditions, please contact us:
                    </p>
                    <div class="mt-4 p-4 bg-cream-100 rounded-lg">
                        <p class="text-charcoal-700">
                            <strong>Email:</strong> <a href="mailto:<?= e(setting('contact_email', 'info@acasadogi.com')) ?>" class="text-accent hover:underline"><?= e(setting('contact_email', 'info@acasadogi.com')) ?></a><br>
                            <strong>Phone:</strong> <?= e(setting('contact_phone', '+351 XXX XXX XXX')) ?><br>
                            <strong>Address:</strong> 52 Avenida Nossa Senhora do Caminho, Mogadouro, Portugal
                        </p>
                    </div>
                </section>
            </div>

            <?php else: ?>
            <!-- PORTUGUESE VERSION -->
            <div class="prose prose-lg max-w-none">
                <p class="text-sm text-charcoal-600 mb-8">Última atualização: <?= date('d') ?> de <?= date('F') ?> de <?= date('Y') ?></p>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">1. Identificação da Empresa</h2>
                    <p class="text-charcoal-700 leading-relaxed">
                        Denominação comercial: <strong>A Casa do Gi</strong><br>
                        Morada: Avenida Nossa Senhora do Caminho, nº 52, Mogadouro, Portugal<br>
                        Email: <?= e(setting('contact_email', 'info@acasadogi.com')) ?><br>
                        Telefone: <?= e(setting('contact_phone', '+351 XXX XXX XXX')) ?>
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">2. Objeto e Aceitação</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        Os presentes Termos e Condições regulam a utilização da loja online A Casa do Gi e a compra de produtos regionais de Trás-os-Montes.
                    </p>
                    <p class="text-charcoal-700 leading-relaxed">
                        Ao efetuar uma encomenda, o cliente confirma que leu, compreendeu e aceitou na íntegra os presentes Termos e Condições.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">3. Produtos e Preços</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        Todos os produtos apresentados na nossa loja são produtos regionais de Trás-os-Montes, Portugal, cuidadosamente selecionados pela sua qualidade e autenticidade.
                    </p>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        Os preços são apresentados em Euros (€) e incluem IVA à taxa legal em vigor. Os custos de envio são apresentados separadamente durante o processo de compra.
                    </p>
                    <p class="text-charcoal-700 leading-relaxed">
                        Reservamo-nos o direito de ajustar os preços a qualquer momento, mas as encomendas já efetuadas respeitarão o preço apresentado no momento da compra.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">4. Processo de Compra</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        Para efetuar uma compra:
                    </p>
                    <ol class="list-decimal list-inside space-y-2 text-charcoal-700 ml-4">
                        <li>Selecione os produtos e adicione-os ao carrinho</li>
                        <li>Reveja a sua encomenda no carrinho de compras</li>
                        <li>Forneça os dados de entrega e faturação</li>
                        <li>Escolha o método de pagamento</li>
                        <li>Confirme a sua encomenda</li>
                    </ol>
                    <p class="text-charcoal-700 leading-relaxed mt-4">
                        Receberá um email de confirmação da encomenda. A encomenda apenas é confirmada após a verificação do pagamento.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">5. Pagamento</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        Aceitamos os seguintes métodos de pagamento através da IfthenPay:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-charcoal-700 ml-4">
                        <li><strong>MBWay</strong> - Pagamento instantâneo via telemóvel</li>
                        <li><strong>Multibanco</strong> - Transferência bancária com referência (pagamento em 3 dias)</li>
                        <li><strong>Cartão de Crédito/Débito</strong> - Pagamento seguro através do gateway IfthenPay</li>
                    </ul>
                    <p class="text-charcoal-700 leading-relaxed mt-4">
                        Todos os pagamentos são processados de forma segura através da IfthenPay. A Casa do Gi não armazena informações do seu cartão de pagamento.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-accent/30">6. Envio e Entrega</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        As encomendas são enviadas no prazo de 2-3 dias úteis após a confirmação do pagamento.
                    </p>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        Prazos de entrega:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-charcoal-700 ml-4">
                        <li>Portugal Continental: 3-5 dias úteis</li>
                        <li>Açores e Madeira: 5-7 dias úteis</li>
                    </ul>
                    <p class="text-charcoal-700 leading-relaxed mt-4">
                        Receberá um número de rastreamento para acompanhar a sua encomenda. Os custos de envio são calculados durante o checkout com base no peso e destino.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">7. Direito de Resolução</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        De acordo com a legislação portuguesa (Decreto-Lei n.º 24/2014), o cliente tem direito a resolver o contrato de compra no prazo de 14 dias de calendário a contar da data de receção, sem necessidade de indicar o motivo.
                    </p>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        Para exercer o direito de resolução, contacte-nos através de <a href="mailto:<?= e(setting('contact_email', 'info@acasadogi.com')) ?>" class="text-accent hover:underline"><?= e(setting('contact_email', 'info@acasadogi.com')) ?></a> e devolva os produtos na sua condição e embalagem originais.
                    </p>
                    <p class="text-charcoal-700 leading-relaxed">
                        <strong>Nota:</strong> Os produtos alimentares não podem ser devolvidos após abertura, por razões de saúde e segurança.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">8. Devoluções e Trocas</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        Se receber um produto defeituoso ou danificado, contacte-nos no prazo de 48 horas após a receção. Providenciaremos a substituição ou o reembolso integral.
                    </p>
                    <p class="text-charcoal-700 leading-relaxed">
                        Os custos de envio de devolução de produtos defeituosos são suportados pela A Casa do Gi.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">9. Garantias</h2>
                    <p class="text-charcoal-700 leading-relaxed">
                        Todos os produtos estão cobertos pelo período legal de garantia de 2 anos por defeitos de conformidade, de acordo com a legislação portuguesa de defesa do consumidor.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">10. Propriedade Intelectual</h2>
                    <p class="text-charcoal-700 leading-relaxed">
                        Todo o conteúdo deste website, incluindo textos, imagens, logótipos e gráficos, é propriedade da A Casa do Gi e está protegido pelas leis portuguesas e internacionais de direitos de autor. É proibida a utilização não autorizada.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">11. Proteção de Dados</h2>
                    <p class="text-charcoal-700 leading-relaxed">
                        Os seus dados pessoais são tratados de acordo com a nossa <a href="<?= $lang->url('politica-privacidade') ?>" class="text-accent hover:underline font-semibold">Política de Privacidade</a> e o Regulamento Geral de Proteção de Dados (RGPD).
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">12. Lei Aplicável e Foro Competente</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        Os presentes Termos e Condições regem-se pela legislação portuguesa.
                    </p>
                    <p class="text-charcoal-700 leading-relaxed">
                        Quaisquer litígios emergentes destes termos serão dirimidos pelos tribunais portugueses competentes, sem prejuízo do direito do consumidor de recorrer a mecanismos alternativos de resolução de litígios.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">13. Contactos</h2>
                    <p class="text-charcoal-700 leading-relaxed">
                        Para questões ou dúvidas sobre estes Termos e Condições, contacte-nos:
                    </p>
                    <div class="mt-4 p-4 bg-cream-100 rounded-lg">
                        <p class="text-charcoal-700">
                            <strong>Email:</strong> <a href="mailto:<?= e(setting('contact_email', 'info@acasadogi.com')) ?>" class="text-accent hover:underline"><?= e(setting('contact_email', 'info@acasadogi.com')) ?></a><br>
                            <strong>Telefone:</strong> <?= e(setting('contact_phone', '+351 XXX XXX XXX')) ?><br>
                            <strong>Morada:</strong> Avenida Nossa Senhora do Caminho, nº 52, Mogadouro, Portugal
                        </p>
                    </div>
                </section>
            </div>
            <?php endif; ?>

        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
