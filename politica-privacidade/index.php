<?php
/**
 * A Casa do Gi - Privacy Policy (Política de Privacidade)
 */

require_once dirname(__DIR__) . '/includes/init.php';

$lang = \Core\Language::getInstance();
$isEnglish = $lang->isEnglish();
$base = basePath();

// Page meta
$pageTitle = $isEnglish ? 'Privacy Policy' : 'Política de Privacidade';
$pageDescription = $isEnglish
    ? 'Privacy policy and personal data protection at A Casa do Gi.'
    : 'Política de privacidade e proteção de dados pessoais da A Casa do Gi.';

include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Banner with Breadcrumbs -->
<section class="relative h-[40vh] min-h-[350px] bg-gray-900 overflow-hidden">
    <!-- Hero Image -->
    <div class="absolute inset-0">
        <img src="<?= $base ?>/assets/images/MogadouroNeve.jpeg"
             alt="Privacy Policy"
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/50 to-black/70"></div>
    </div>

    <!-- Content -->
    <div class="relative h-full flex flex-col justify-end max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <!-- Breadcrumbs -->
        <nav class="mb-4 animate-on-scroll" data-animation="fade-up">
            <ol class="flex items-center space-x-2 text-sm text-white/90">
                <li>
                    <a href="<?= $isEnglish ? $base . '/en/' : $base . '/' ?>" class="hover:text-accent transition-colors">
                        <?= $isEnglish ? 'Home' : 'Início' ?>
                    </a>
                </li>
                <li>
                    <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </li>
                <li class="text-white font-medium">
                    <?= $isEnglish ? 'Privacy Policy' : 'Política de Privacidade' ?>
                </li>
            </ol>
        </nav>

        <!-- Title -->
        <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl text-white drop-shadow-xl animate-on-scroll" data-animation="fade-up" data-delay="100">
            <?= $isEnglish ? 'Privacy Policy' : 'Política de Privacidade' ?>
        </h1>
        <p class="mt-4 text-lg text-white/90 max-w-2xl animate-on-scroll" data-delay="200">
             <?= $isEnglish
                ? 'How we protect and process your personal data'
                : 'Como protegemos e tratamos os seus dados pessoais' ?>
        </p>
    </div>
</section>

<!-- Content Section -->
<section class="py-16 bg-cream">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12 animate-on-scroll">

            <?php if ($isEnglish): ?>
            <!-- ENGLISH VERSION -->
            <div class="prose prose-lg max-w-none">
                <p class="text-sm text-charcoal-600 mb-8">Effective date: <?= date('F d, Y') ?></p>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">1. Data Controller</h2>
                    <p class="text-charcoal-700 leading-relaxed">
                        The data controller responsible for the processing of your personal data is:
                    </p>
                    <div class="mt-4 p-4 bg-cream-100 rounded-lg">
                        <p class="text-charcoal-700">
                            <strong>A Casa do Gi</strong><br>
                            52 Avenida Nossa Senhora do Caminho, Mogadouro, Portugal<br>
                            Email: <?= e(setting('contact_email', 'info@acasadogi.com')) ?><br>
                            Phone: <?= e(setting('contact_phone', '+351 XXX XXX XXX')) ?>
                        </p>
                    </div>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">2. Personal Data Collected</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        We collect and process the following categories of personal data:
                    </p>
                    <ul class="list-disc list-inside space-y-3 text-charcoal-700 ml-4">
                        <li><strong>Identification data:</strong> Name, email address, phone number</li>
                        <li><strong>Billing information:</strong> Billing address, NIF/VAT number (if applicable)</li>
                        <li><strong>Delivery information:</strong> Delivery address, contact details</li>
                        <li><strong>Payment data:</strong> Payment method (processed securely by IfthenPay - we do not store card details)</li>
                        <li><strong>Purchase history:</strong> Orders, products purchased, dates, amounts</li>
                        <li><strong>Technical data:</strong> IP address, browser type, device information, cookies</li>
                        <li><strong>Communication data:</strong> Messages sent via contact forms or email</li>
                    </ul>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">3. Purposes of Processing</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        We process your personal data for the following purposes:
                    </p>
                    <ul class="list-disc list-inside space-y-3 text-charcoal-700 ml-4">
                        <li><strong>Order processing:</strong> To process, fulfill, and ship your orders</li>
                        <li><strong>Customer service:</strong> To respond to your inquiries and provide support</li>
                        <li><strong>Payment processing:</strong> To process payments securely via IfthenPay</li>
                        <li><strong>Legal compliance:</strong> To comply with tax, accounting, and legal obligations</li>
                        <li><strong>Marketing:</strong> To send promotional communications (only with your consent)</li>
                        <li><strong>Website improvement:</strong> To analyze usage and improve our services</li>
                        <li><strong>Fraud prevention:</strong> To detect and prevent fraudulent activities</li>
                    </ul>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">4. Legal Basis</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        We process your personal data based on:
                    </p>
                    <ul class="list-disc list-inside space-y-3 text-charcoal-700 ml-4">
                        <li><strong>Contractual necessity:</strong> To fulfill our contract with you (orders, deliveries)</li>
                        <li><strong>Legal obligation:</strong> To comply with tax and legal requirements</li>
                        <li><strong>Consent:</strong> For marketing communications (you can withdraw consent at any time)</li>
                        <li><strong>Legitimate interest:</strong> For fraud prevention and website improvement</li>
                    </ul>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">5. Cookies</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        Our website uses cookies to:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-charcoal-700 ml-4">
                        <li>Maintain your shopping cart session</li>
                        <li>Remember your language preference</li>
                        <li>Analyze website traffic and usage patterns</li>
                        <li>Improve user experience</li>
                    </ul>
                    <p class="text-charcoal-700 leading-relaxed mt-4">
                        You can manage cookie preferences through your browser settings. Please note that disabling cookies may affect website functionality.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">6. Sharing Data with Third Parties</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        We may share your personal data with:
                    </p>
                    <ul class="list-disc list-inside space-y-3 text-charcoal-700 ml-4">
                        <li><strong>Payment processors:</strong> IfthenPay (for secure payment processing)</li>
                        <li><strong>Shipping carriers:</strong> CTT, DPD, or other carriers (for order delivery)</li>
                        <li><strong>Service providers:</strong> Hosting, email, and technical support providers</li>
                        <li><strong>Legal authorities:</strong> When required by law or to protect our legal rights</li>
                    </ul>
                    <p class="text-charcoal-700 leading-relaxed mt-4">
                        We ensure that all third parties comply with GDPR and handle your data securely.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">7. Data Retention</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        We retain your personal data for:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-charcoal-700 ml-4">
                        <li><strong>Order data:</strong> 10 years (tax and legal requirements)</li>
                        <li><strong>Customer account:</strong> Until you request deletion</li>
                        <li><strong>Marketing data:</strong> Until you withdraw consent</li>
                        <li><strong>Technical logs:</strong> 12 months</li>
                    </ul>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">8. Your Rights</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        Under GDPR, you have the following rights:
                    </p>
                    <ul class="list-disc list-inside space-y-3 text-charcoal-700 ml-4">
                        <li><strong>Right of access:</strong> Request a copy of your personal data</li>
                        <li><strong>Right to rectification:</strong> Correct inaccurate or incomplete data</li>
                        <li><strong>Right to erasure:</strong> Request deletion of your data ("right to be forgotten")</li>
                        <li><strong>Right to restriction:</strong> Limit how we use your data</li>
                        <li><strong>Right to data portability:</strong> Receive your data in a structured format</li>
                        <li><strong>Right to object:</strong> Object to processing based on legitimate interests</li>
                        <li><strong>Right to withdraw consent:</strong> Withdraw consent for marketing at any time</li>
                    </ul>
                    <p class="text-charcoal-700 leading-relaxed mt-4">
                        To exercise any of these rights, please contact us at <a href="mailto:<?= e(setting('contact_email', 'info@acasadogi.com')) ?>" class="text-accent hover:underline"><?= e(setting('contact_email', 'info@acasadogi.com')) ?></a>.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">9. Data Security</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        We implement appropriate technical and organizational measures to protect your personal data against:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-charcoal-700 ml-4">
                        <li>Unauthorized access</li>
                        <li>Accidental loss or destruction</li>
                        <li>Alteration or disclosure</li>
                    </ul>
                    <p class="text-charcoal-700 leading-relaxed mt-4">
                        Security measures include encryption, secure hosting, access controls, and regular security audits.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">10. Changes to This Policy</h2>
                    <p class="text-charcoal-700 leading-relaxed">
                        We may update this Privacy Policy from time to time. Any changes will be posted on this page with an updated effective date. We encourage you to review this policy periodically.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">11. Contact and Data Protection Officer</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        For questions about this Privacy Policy or to exercise your rights, please contact:
                    </p>
                    <div class="p-4 bg-cream-100 rounded-lg">
                        <p class="text-charcoal-700">
                            <strong>Email:</strong> <a href="mailto:<?= e(setting('contact_email', 'info@acasadogi.com')) ?>" class="text-accent hover:underline"><?= e(setting('contact_email', 'info@acasadogi.com')) ?></a><br>
                            <strong>Phone:</strong> <?= e(setting('contact_phone', '+351 XXX XXX XXX')) ?><br>
                            <strong>Address:</strong> 52 Avenida Nossa Senhora do Caminho, Mogadouro, Portugal
                        </p>
                    </div>
                    <p class="text-charcoal-700 leading-relaxed mt-4">
                        You also have the right to lodge a complaint with the Portuguese Data Protection Authority (CNPD - Comissão Nacional de Proteção de Dados) at <a href="https://www.cnpd.pt" target="_blank" class="text-accent hover:underline">www.cnpd.pt</a>.
                    </p>
                </section>
            </div>

            <?php else: ?>
            <!-- PORTUGUESE VERSION -->
            <div class="prose prose-lg max-w-none">
                <p class="text-sm text-charcoal-600 mb-8">Data de entrada em vigor: <?= date('d') ?> de <?= date('F') ?> de <?= date('Y') ?></p>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">1. Identificação do Responsável pelo Tratamento</h2>
                    <p class="text-charcoal-700 leading-relaxed">
                        O responsável pelo tratamento dos seus dados pessoais é:
                    </p>
                    <div class="mt-4 p-4 bg-cream-100 rounded-lg">
                        <p class="text-charcoal-700">
                            <strong>A Casa do Gi</strong><br>
                            Avenida Nossa Senhora do Caminho, nº 52, Mogadouro, Portugal<br>
                            Email: <?= e(setting('contact_email', 'info@acasadogi.com')) ?><br>
                            Telefone: <?= e(setting('contact_phone', '+351 XXX XXX XXX')) ?>
                        </p>
                    </div>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">2. Dados Pessoais Recolhidos</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        Recolhemos e tratamos as seguintes categorias de dados pessoais:
                    </p>
                    <ul class="list-disc list-inside space-y-3 text-charcoal-700 ml-4">
                        <li><strong>Dados de identificação:</strong> Nome, endereço de email, número de telefone</li>
                        <li><strong>Dados de faturação:</strong> Morada de faturação, NIF (se aplicável)</li>
                        <li><strong>Dados de entrega:</strong> Morada de entrega, contacto</li>
                        <li><strong>Dados de pagamento:</strong> Método de pagamento (processado de forma segura pela IfthenPay - não armazenamos dados de cartão)</li>
                        <li><strong>Histórico de compras:</strong> Encomendas, produtos adquiridos, datas, montantes</li>
                        <li><strong>Dados técnicos:</strong> Endereço IP, tipo de navegador, informações do dispositivo, cookies</li>
                        <li><strong>Dados de comunicação:</strong> Mensagens enviadas através de formulários de contacto ou email</li>
                    </ul>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">3. Finalidades do Tratamento</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        Tratamos os seus dados pessoais para as seguintes finalidades:
                    </p>
                    <ul class="list-disc list-inside space-y-3 text-charcoal-700 ml-4">
                        <li><strong>Processamento de encomendas:</strong> Para processar, preparar e enviar as suas encomendas</li>
                        <li><strong>Atendimento ao cliente:</strong> Para responder às suas questões e prestar apoio</li>
                        <li><strong>Processamento de pagamentos:</strong> Para processar pagamentos de forma segura através da IfthenPay</li>
                        <li><strong>Cumprimento legal:</strong> Para cumprir obrigações fiscais, contabilísticas e legais</li>
                        <li><strong>Marketing:</strong> Para enviar comunicações promocionais (apenas com o seu consentimento)</li>
                        <li><strong>Melhoria do website:</strong> Para analisar a utilização e melhorar os nossos serviços</li>
                        <li><strong>Prevenção de fraude:</strong> Para detetar e prevenir atividades fraudulentas</li>
                    </ul>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">4. Base Legal</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        Tratamos os seus dados pessoais com base em:
                    </p>
                    <ul class="list-disc list-inside space-y-3 text-charcoal-700 ml-4">
                        <li><strong>Execução de contrato:</strong> Para cumprir o contrato consigo (encomendas, entregas)</li>
                        <li><strong>Obrigação legal:</strong> Para cumprir requisitos fiscais e legais</li>
                        <li><strong>Consentimento:</strong> Para comunicações de marketing (pode retirar o consentimento a qualquer momento)</li>
                        <li><strong>Interesse legítimo:</strong> Para prevenção de fraude e melhoria do website</li>
                    </ul>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">5. Cookies</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        O nosso website utiliza cookies para:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-charcoal-700 ml-4">
                        <li>Manter a sua sessão do carrinho de compras</li>
                        <li>Lembrar a sua preferência de idioma</li>
                        <li>Analisar o tráfego e padrões de utilização do website</li>
                        <li>Melhorar a experiência do utilizador</li>
                    </ul>
                    <p class="text-charcoal-700 leading-relaxed mt-4">
                        Pode gerir as preferências de cookies através das definições do seu navegador. Note que desativar os cookies pode afetar a funcionalidade do website.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">6. Partilha de Dados com Terceiros</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        Podemos partilhar os seus dados pessoais com:
                    </p>
                    <ul class="list-disc list-inside space-y-3 text-charcoal-700 ml-4">
                        <li><strong>Processadores de pagamento:</strong> IfthenPay (para processamento seguro de pagamentos)</li>
                        <li><strong>Transportadoras:</strong> CTT, DPD ou outras transportadoras (para entrega de encomendas)</li>
                        <li><strong>Prestadores de serviços:</strong> Fornecedores de alojamento, email e suporte técnico</li>
                        <li><strong>Autoridades legais:</strong> Quando exigido por lei ou para proteger os nossos direitos legais</li>
                    </ul>
                    <p class="text-charcoal-700 leading-relaxed mt-4">
                        Asseguramos que todos os terceiros cumprem o RGPD e tratam os seus dados de forma segura.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">7. Conservação dos Dados</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        Conservamos os seus dados pessoais durante:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-charcoal-700 ml-4">
                        <li><strong>Dados de encomendas:</strong> 10 anos (requisitos fiscais e legais)</li>
                        <li><strong>Conta de cliente:</strong> Até solicitar a eliminação</li>
                        <li><strong>Dados de marketing:</strong> Até retirar o consentimento</li>
                        <li><strong>Registos técnicos:</strong> 12 meses</li>
                    </ul>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">8. Direitos do Titular dos Dados</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        Ao abrigo do RGPD, tem os seguintes direitos:
                    </p>
                    <ul class="list-disc list-inside space-y-3 text-charcoal-700 ml-4">
                        <li><strong>Direito de acesso:</strong> Solicitar uma cópia dos seus dados pessoais</li>
                        <li><strong>Direito de retificação:</strong> Corrigir dados inexatos ou incompletos</li>
                        <li><strong>Direito ao apagamento:</strong> Solicitar a eliminação dos seus dados ("direito a ser esquecido")</li>
                        <li><strong>Direito à limitação:</strong> Limitar a forma como utilizamos os seus dados</li>
                        <li><strong>Direito à portabilidade:</strong> Receber os seus dados num formato estruturado</li>
                        <li><strong>Direito de oposição:</strong> Opor-se ao tratamento baseado em interesses legítimos</li>
                        <li><strong>Direito de retirar consentimento:</strong> Retirar o consentimento para marketing a qualquer momento</li>
                    </ul>
                    <p class="text-charcoal-700 leading-relaxed mt-4">
                        Para exercer qualquer um destes direitos, contacte-nos através de <a href="mailto:<?= e(setting('contact_email', 'info@acasadogi.com')) ?>" class="text-accent hover:underline"><?= e(setting('contact_email', 'info@acasadogi.com')) ?></a>.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">9. Segurança dos Dados</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        Implementamos medidas técnicas e organizativas adequadas para proteger os seus dados pessoais contra:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-charcoal-700 ml-4">
                        <li>Acesso não autorizado</li>
                        <li>Perda ou destruição acidental</li>
                        <li>Alteração ou divulgação</li>
                    </ul>
                    <p class="text-charcoal-700 leading-relaxed mt-4">
                        As medidas de segurança incluem encriptação, alojamento seguro, controlos de acesso e auditorias de segurança regulares.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">10. Alterações à Política</h2>
                    <p class="text-charcoal-700 leading-relaxed">
                        Podemos atualizar esta Política de Privacidade periodicamente. Quaisquer alterações serão publicadas nesta página com uma data de entrada em vigor atualizada. Recomendamos que reveja esta política periodicamente.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-serif text-3xl font-bold text-primary mb-4 pb-2 border-b-2 border-accent/30">11. Contactos e Encarregado de Proteção de Dados</h2>
                    <p class="text-charcoal-700 leading-relaxed mb-4">
                        Para questões sobre esta Política de Privacidade ou para exercer os seus direitos, contacte:
                    </p>
                    <div class="p-4 bg-cream-100 rounded-lg">
                        <p class="text-charcoal-700">
                            <strong>Email:</strong> <a href="mailto:<?= e(setting('contact_email', 'info@acasadogi.com')) ?>" class="text-accent hover:underline"><?= e(setting('contact_email', 'info@acasadogi.com')) ?></a><br>
                            <strong>Telefone:</strong> <?= e(setting('contact_phone', '+351 XXX XXX XXX')) ?><br>
                            <strong>Morada:</strong> Avenida Nossa Senhora do Caminho, nº 52, Mogadouro, Portugal
                        </p>
                    </div>
                    <p class="text-charcoal-700 leading-relaxed mt-4">
                        Tem também o direito de apresentar uma reclamação junto da Comissão Nacional de Proteção de Dados (CNPD) em <a href="https://www.cnpd.pt" target="_blank" class="text-accent hover:underline">www.cnpd.pt</a>.
                    </p>
                </section>
            </div>
            <?php endif; ?>

        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
