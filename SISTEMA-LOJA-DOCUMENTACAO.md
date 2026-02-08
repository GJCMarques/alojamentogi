# A Casa do Gi - Sistema Completo da Loja

## Documentacao Tecnica das 6 Fases

Este documento descreve tudo o que foi implementado, como funciona, e onde configurar cada componente.

---

## Indice

1. [FASE 1: Layout da Loja](#fase-1-layout-da-loja)
2. [FASE 2: Sistema de Faturas](#fase-2-sistema-de-faturas)
3. [FASE 3: Pagamentos IfthenPay](#fase-3-pagamentos-ifthenpay)
4. [FASE 4: Back Office](#fase-4-back-office)
5. [FASE 5: Emails Automaticos](#fase-5-emails-automaticos)
6. [FASE 6: Seguranca](#fase-6-seguranca)
7. [Configuracao de Chaves e APIs](#configuracao-de-chaves-e-apis)
8. [Migracao da Base de Dados](#migracao-da-base-de-dados)
9. [Mapa de Ficheiros](#mapa-de-ficheiros)

---

## FASE 1: Layout da Loja

### O que foi feito
- Grid de produtos alterado de 3 para 4 colunas no desktop
- Breakpoints responsivos: 1 coluna mobile, 2 tablet, 3 laptop, 4 desktop
- Produtos por pagina: 12 (alinhado com grid de 4 colunas)

### Ficheiros alterados
- `loja/index.php` (linha ~198) - grid de `xl:grid-cols-3` para `lg:grid-cols-3 xl:grid-cols-4`

### Resultado
- Mobile (<640px): 1 coluna
- Tablet (640-1023px): 2 colunas
- Laptop (1024-1279px): 3 colunas
- Desktop (1280px+): 4 colunas

---

## FASE 2: Sistema de Faturas

### Como funciona
Cada fatura tem dois identificadores unicos:

1. **Codigo de Barras (9 digitos)** - Ex: `347 821 956`
   - Gerado aleatoriamente com `random_int()`
   - Verificacao de colisoes na base de dados
   - Sistema de batches: quando se esgotam os codigos (999.999.999), cria-se um novo batch
   - Usado para verificacao rapida (ex: por telefone)

2. **UUID v4** - Ex: `a1b2c3d4-e5f6-4789-abcd-ef0123456789`
   - Gerado com `random_bytes(16)` + formato RFC 4122
   - Criptograficamente seguro
   - Usado para verificacao digital

3. **Hash de Integridade (HMAC-SHA256)**
   - Cada fatura tem um hash calculado a partir dos dados (barcode + UUID + order_id + total + items)
   - Usa uma chave secreta definida em `config.php` (`security.invoice_hmac_key`) ou APP_KEY
   - Se alguem alterar dados da fatura na base de dados, o hash nao vai corresponder
   - O admin pode verificar a integridade no back office

### Classe: `core/Invoice.php`

```php
// Gerar fatura para uma encomenda
$invoice = Invoice::getInstance();
$fatura = $invoice->generate($orderId);

// Buscar por codigo de barras
$fatura = $invoice->findByBarcode('347821956');

// Buscar por UUID
$fatura = $invoice->findByUUID('a1b2c3d4-e5f6-4789-abcd-ef0123456789');

// Verificar autenticidade (valida hash de integridade)
$resultado = $invoice->verify('347821956');
// Retorna: ['valid' => true/false, 'message' => '...', 'invoice' => [...]]

// Enviar fatura por email
$invoice->sendEmail($faturaId);

// Marcar como paga
$invoice->markAsPaid($faturaId);
```

### Tabelas na BD
- `invoices` - Faturas com todos os dados, items_json (snapshot), hash de integridade
- `barcode_batches` - Controlo de batches de codigos de barras

### Ficheiros
- `core/Invoice.php` - Classe principal
- `templates/emails/invoice.php` - Template do email da fatura
- `database/migrations/010_invoices_and_shop_system.sql` - Migracao

---

## FASE 3: Pagamentos IfthenPay

### 3 Metodos de Pagamento

#### MBWay
- Cliente introduz numero de telemovel no checkout
- Sistema envia pedido para API IfthenPay
- Pagina de pagamento faz polling a cada 5 segundos (max 5 minutos)
- Quando o cliente aceita no telemovel, o estado atualiza automaticamente
- Endpoint de polling: `api/check-payment-status.php?order_id=X`

#### Multibanco
- Sistema gera referencia (entidade + referencia + montante)
- Cliente paga num multibanco ou homebanking
- IfthenPay envia callback quando pago
- Referencia valida por 72 horas

#### Cartao (Visa/Mastercard)
- Redireciona para gateway seguro da IfthenPay
- Apos pagamento, redireciona de volta para a confirmacao
- IfthenPay envia callback com resultado

### 3 Modos da Loja

A loja tem 3 modos de funcionamento, configuravel no admin:

| Modo | Comportamento |
|------|---------------|
| **Ativa** (`active`) | Checkout normal com pagamento online (MBWay/MB/Cartao) |
| **Manual** (`manual`) | Carrinho e enviado para backoffice, pagamento por telefone |
| **Fechada** (`closed`) | Loja nao aceita encomendas, mostra mensagem |

O modo e guardado na tabela `settings` com a chave `shop_mode`.

### Callback de Pagamento

Quando a IfthenPay confirma um pagamento, envia um POST/GET para:
```
https://seusite.pt/alojamentogi/api/payment-callback.php
```

O callback faz:
1. Verifica a chave anti-phishing
2. Verifica se o pagamento nao e duplicado (replay protection)
3. Valida o montante
4. Atualiza estado da encomenda para "pago"
5. Gera fatura automaticamente
6. Envia email de confirmacao + fatura

### Classe: `core/Payment/IfthenPay.php`

```php
$gateway = IfthenPay::getInstance();

// MBWay
$result = $gateway->createMBWayPayment($orderId, '912345678', 29.99);

// Multibanco
$result = $gateway->createMultibancoReference($orderId, 29.99);

// Cartao
$result = $gateway->createCardPayment($orderId, 29.99, $returnUrl);

// Verificar estado
$status = $gateway->checkPaymentStatus($orderId);

// Processar callback
$result = $gateway->handleCallback($data);
```

### Ficheiros
- `core/Payment/IfthenPay.php` - Classe de integracao
- `api/payment-callback.php` - Endpoint de callback
- `api/check-payment-status.php` - Endpoint de polling
- `loja/checkout/pagamento/index.php` - Pagina de pagamento (MBWay/MB/Cartao)
- `loja/checkout/confirmacao/index.php` - Pagina de confirmacao
- `loja/checkout/manual/index.php` - Pagina de pedido manual (modo manual)
- `loja/checkout/index.php` - Checkout (verifica modo da loja)

---

## FASE 4: Back Office

### Novas Paginas de Admin

#### Admin Loja (`admin/loja/index.php`)
- Switch visual com os 3 modos: Ativa / Manual / Fechada
- Estatisticas rapidas: total vendas, encomendas pendentes, receita, pedidos manuais
- Tabela com as 5 encomendas mais recentes

#### Admin Faturas (`admin/faturas/index.php`)
- **Ferramenta de Verificacao**: Campo para inserir codigo de barras ou UUID
  - Verde = Fatura autentica e integra
  - Vermelho = Possivel adulteracao detectada
  - Cinza = Fatura nao encontrada
- Lista de todas as faturas com filtros (pesquisa, estado, datas)
- Tabs por estado: Todas, Pagas, Pendentes, Falhadas, Reembolsadas
- Acoes: ver detalhes, reenviar email, verificar autenticidade

#### Admin Faturas Detalhe (`admin/faturas/ver.php`)
- Identificadores completos (barcode + UUID)
- Tabela de itens com precos e totais
- Dados do cliente
- Estado de pagamento e integridade
- Acoes: reenviar email, marcar paga, marcar reembolsada, ver encomenda associada
- Hash SHA-256 visivel para auditoria

#### Admin Pedidos Manuais (`admin/pedidos-manuais/index.php`)
- Lista de pedidos feitos no modo manual
- Tabs por estado: Todos, Novos, Contactados, Convertidos, Cancelados
- Cards com dados do cliente e produtos pretendidos
- Acoes: marcar contactado, converter, cancelar, apagar
- Campo de notas editavel por pedido

#### Admin Encomendas Detalhe (`admin/encomendas/ver.php`) - Melhorado
- Ao marcar como "Enviada", envia email automatico ao cliente com template profissional
- Codigo de rastreio incluido no email
- Timestamps de shipped_at e delivered_at

#### Sidebar (`admin/includes/sidebar.php`) - Atualizado
Novos itens adicionados:
- Loja (switch e dashboard)
- Faturas (verificacao e lista)
- Pedidos Manuais

---

## FASE 5: Emails Automaticos

### Templates HTML Profissionais

Todos os templates usam a paleta do site (#264653 primary, #C5A059 accent, #FDFBF7 cream) e sao compativeis com clientes de email (table-based layout).

| Template | Ficheiro | Quando e enviado |
|----------|----------|-----------------|
| Fatura | `templates/emails/invoice.php` | Apos pagamento confirmado |
| Encomenda Confirmada | `templates/emails/order-confirmed.php` | Apos checkout com pagamento |
| Encomenda Enviada | `templates/emails/order-shipped.php` | Quando admin marca como "Enviada" |
| Pedido Manual Recebido | `templates/emails/manual-order-received.php` | Quando cliente faz pedido manual |

### Metodos do Mailer (`core/Mailer.php`)

```php
$mailer = new \Core\Mailer();

// Enviar fatura
$mailer->sendInvoice($invoiceData, $orderData);

// Encomenda enviada (com tracking)
$mailer->sendOrderShipped($orderData, 'CTT123456', $orderItems);

// Confirmacao de encomenda
$mailer->sendOrderConfirmation($orderData, $orderItems);

// Pedido manual - confirmacao ao cliente
$mailer->sendManualOrderReceived($manualOrderData);

// Pedido manual - notificacao ao admin
$mailer->sendManualOrderNotification($manualOrderData);
```

### Fallback
Se o PHPMailer nao estiver instalado (sem Composer), o sistema usa a funcao nativa `mail()` do PHP como fallback.

---

## FASE 6: Seguranca

### Rate Limiting (`core/RateLimiter.php`)

Sistema de limitacao de pedidos baseado em ficheiros (nao precisa de Redis/Memcached).

| Endpoint | Limite | Janela |
|----------|--------|--------|
| `api/cart.php` | 60 pedidos | 1 minuto |
| `api/check-payment-status.php` | 20 pedidos | 1 minuto |
| `api/payment-callback.php` | 30 pedidos | 1 minuto |
| `loja/checkout/` (POST) | 5 submissoes | 10 minutos |

Quando excedido, retorna HTTP 429 (Too Many Requests).

Os ficheiros de rate limit sao guardados em `logs/rate-limits/` (protegido pelo .htaccess).

```php
$limiter = \Core\RateLimiter::getInstance();

// Verificar se permitido
if (!$limiter->check('minha_acao', 10, 60)) {
    // Bloqueado - 10 tentativas por 60 segundos
}

// Ou forcar (envia 429 e sai automaticamente)
$limiter->enforce('minha_acao', 10, 60);
```

### Nonce Anti-Double-Submit

No checkout, e gerado um nonce unico (`random_bytes(32)`) guardado na sessao. O formulario inclui esse nonce como campo hidden. Ao submeter:
1. Verifica se o nonce do formulario corresponde ao da sessao
2. Invalida o nonce imediatamente apos uso
3. Se alguem tentar resubmeter (F5, back button), o nonce ja nao existe

### Content Security Policy (CSP)

Configurado no `.htaccess`:
- `default-src 'self'` - Apenas recursos do proprio site
- `script-src` - Permite CDNs do Tailwind, JSPM e jsdelivr
- `style-src` - Permite Google Fonts e CDNs
- `frame-src` - Permite iframes da IfthenPay (gateway de cartao)
- `form-action` - Formularios apenas para 'self' e IfthenPay
- `img-src` - Imagens de qualquer HTTPS (para logos de email, etc.)

### Headers de Seguranca

```
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: camera=(), microphone=(), geolocation=()
Content-Security-Policy: [ver acima]
```

### Protecao de Diretorios

Bloqueados via `.htaccess` (retornam 403 Forbidden):
- `config/` - Ficheiros de configuracao
- `core/` - Classes PHP
- `models/` - Modelos
- `includes/` - Includes PHP
- `templates/` - Templates de email
- `database/` - Migracoes SQL
- `logs/` - Logs e rate limits
- `vendor/` - Dependencias Composer

### Seguranca ja existente (verificada)
- Prepared statements em todas as queries (SQL injection)
- CSRF tokens em todos os formularios
- `e()` helper para output (XSS prevention)
- Sessions com HTTPOnly e SameSite
- Login throttling (5 tentativas, 15 min bloqueio)
- Bcrypt com cost 12 para passwords
- Anti-phishing key nos callbacks IfthenPay
- Replay protection nos callbacks (verifica se ja processado)
- Validacao de montantes server-side (precos sempre da BD)

---

## Configuracao de Chaves e APIs

### Ficheiro: `config/config.php`

#### Email (PHPMailer/SMTP)

```php
'mail' => [
    'host' => 'smtp.gmail.com',        // Servidor SMTP
    'port' => 587,                       // Porta (587 para TLS, 465 para SSL)
    'username' => 'seuemail@gmail.com',  // Email de login SMTP
    'password' => 'suasenhaapp',         // Password (para Gmail: App Password)
    'encryption' => 'tls',               // 'tls' ou 'ssl'
    'from_email' => 'noreply@acasadogi.pt',  // Remetente
    'from_name' => 'A Casa do Gi',           // Nome do remetente
    'reply_to' => 'info@acasadogi.pt'        // Responder para
],
```

**Para Gmail:**
1. Ir a https://myaccount.google.com/security
2. Ativar verificacao em 2 passos
3. Gerar "App Password" (Passwords de apps)
4. Usar essa password no campo `password`

**Para outros provedores:**
- Hostinger: `smtp.hostinger.com`, porta 465, SSL
- OVH: `ssl0.ovh.net`, porta 465, SSL
- Ionos: `smtp.ionos.pt`, porta 465, SSL

#### IfthenPay (Pagamentos)

```php
'payment' => [
    'gateway' => 'ifthenpay',
    'sandbox' => true,              // FALSE em producao!
    'ifthenpay' => [
        'mbway_key' => 'XXX-000000',           // Chave MBWay (obtida no backoffice IfthenPay)
        'multibanco_entity' => '11111',         // Entidade Multibanco
        'multibanco_subentity' => '222',        // Subentidade Multibanco
        'card_key' => 'XXX-000000',             // Chave para cartao
        'anti_phishing_key' => 'chave-secreta', // Chave anti-phishing (gerada no backoffice IfthenPay)
        'callback_url' => 'https://seusite.pt/alojamentogi/api/payment-callback.php'
    ]
],
```

**Como obter as chaves IfthenPay:**
1. Criar conta em https://www.ifthenpay.com
2. Ir ao Backoffice IfthenPay
3. Em cada metodo (MBWay, Multibanco, CCard), obter as chaves
4. Em "Configuracoes" > "Anti-Phishing", gerar a chave
5. Em "Configuracoes" > "Callback URL", configurar:
   - URL: `https://seusite.pt/alojamentogi/api/payment-callback.php`
   - Ativar callback para todos os metodos

**IMPORTANTE - Modo Sandbox:**
- `'sandbox' => true` usa endpoints de teste
- Mudar para `false` quando for para producao
- Testar TUDO em sandbox antes de ir para producao

#### Seguranca

```php
'security' => [
    'session_lifetime' => 7200,     // Duracao da sessao (2 horas)
    'csrf_token_lifetime' => 3600,  // Duracao do token CSRF (1 hora)
    'max_login_attempts' => 5,      // Tentativas de login antes de bloqueio
    'lockout_duration' => 900,      // Tempo de bloqueio (15 minutos)
    'password_min_length' => 8,     // Comprimento minimo da password
    'bcrypt_cost' => 12             // Custo do bcrypt (10-12 recomendado)
],
```

### Tabela `settings` (Base de Dados)

Algumas configuracoes tambem podem ser geridas pela BD:

| Chave (`setting_key`) | Descricao | Valor por defeito |
|------------------------|-----------|-------------------|
| `shop_mode` | Modo da loja (active/manual/closed) | `active` |
| `shipping_cost` | Custo de envio | `5.00` |
| `free_shipping_threshold` | Encomenda minima para portes gratis | `50.00` |
| `contact_email` | Email do admin (recebe notificacoes) | config mail |
| `contact_phone` | Telefone de contacto | - |
| `ifthenpay_mbway_key` | Override da chave MBWay | - |
| `ifthenpay_entity` | Override da entidade MB | - |
| `ifthenpay_subentity` | Override da subentidade MB | - |
| `ifthenpay_card_key` | Override da chave cartao | - |
| `ifthenpay_anti_phishing_key` | Override da chave anti-phishing | - |

---

## Migracao da Base de Dados

### IMPORTANTE - Executar antes de testar!

Ficheiro: `database/migrations/010_invoices_and_shop_system.sql`

Para executar no phpMyAdmin:
1. Abrir http://localhost/phpmyadmin
2. Selecionar a base de dados `casadogi`
3. Ir ao separador "SQL"
4. Copiar e colar o conteudo do ficheiro
5. Executar

**O que cria:**
- Tabela `invoices` - Faturas com barcode, UUID, hash de integridade
- Tabela `barcode_batches` - Controlo de batches de codigos
- Tabela `manual_orders` - Pedidos do modo manual
- Altera `orders` - Adiciona colunas `invoice_id`, `tracking_code`, `shipped_at`, `delivered_at`
- Insere `shop_mode = active` na tabela `settings`

---

## Mapa de Ficheiros

### Ficheiros Criados (Novos)

```
core/
  Invoice.php                              # Sistema de faturas
  Payment/
    IfthenPay.php                          # Integracao IfthenPay
  RateLimiter.php                          # Rate limiting

api/
  payment-callback.php                     # Callback IfthenPay (reescrito)
  check-payment-status.php                 # Polling de estado (reescrito)

loja/checkout/
  manual/
    index.php                              # Pedido manual (modo manual)

admin/
  loja/
    index.php                              # Switch de modo + dashboard
  faturas/
    index.php                              # Lista e verificacao de faturas
    ver.php                                # Detalhe de fatura
  pedidos-manuais/
    index.php                              # Gestao de pedidos manuais

templates/emails/
  invoice.php                              # Template email fatura
  order-confirmed.php                      # Template encomenda confirmada
  order-shipped.php                        # Template encomenda enviada
  manual-order-received.php                # Template pedido manual

database/migrations/
  010_invoices_and_shop_system.sql          # Migracao BD
```

### Ficheiros Modificados

```
loja/
  index.php                                # Grid 4 colunas
  checkout/
    index.php                              # Modo loja + nonce + rate limit
    pagamento/
      index.php                            # Pagamento IfthenPay (reescrito)
    confirmacao/
      index.php                            # Confirmacao (reescrito)

api/
  cart.php                                 # Rate limiting adicionado

admin/
  includes/
    sidebar.php                            # Novos itens menu
  encomendas/
    ver.php                                # Email automatico ao enviar

core/
  Mailer.php                               # 4 novos metodos de envio

.htaccess                                  # CSP, headers, vendor protection

SISTEMA-LOJA-DOCUMENTACAO.md               # Este ficheiro
```

---

## Fluxo Completo: Da Compra a Entrega

### Modo Ativa (Pagamento Online)

```
1. Cliente navega na loja (/loja/)
2. Adiciona produtos ao carrinho (api/cart.php)
3. Vai ao checkout (/loja/checkout/)
4. Preenche dados + escolhe metodo pagamento
5. Sistema cria encomenda na BD
6. Redireciona para pagina de pagamento (/loja/checkout/pagamento/)
   - MBWay: polling ate confirmar
   - Multibanco: mostra referencia
   - Cartao: redireciona para gateway
7. IfthenPay envia callback (api/payment-callback.php)
8. Sistema: atualiza estado, gera fatura, envia emails
9. Pagina de confirmacao (/loja/checkout/confirmacao/)
10. Admin gere no backoffice, marca como enviada
11. Email automatico "encomenda enviada" com tracking
```

### Modo Manual (Pagamento por Telefone)

```
1. Cliente navega na loja (/loja/)
2. Adiciona produtos ao carrinho
3. Vai ao checkout -> redirecionado para /loja/checkout/manual/
4. Preenche dados de contacto
5. Pedido guardado na tabela manual_orders
6. Email de confirmacao ao cliente
7. Email de notificacao ao admin
8. Admin ve no backoffice (admin/pedidos-manuais/)
9. Admin liga ao cliente para combinar pagamento
10. Marca como "contactado" -> "convertido"
```

### Modo Fechada

```
1. Cliente tenta ir ao checkout
2. Recebe mensagem: "Nao estamos a aceitar encomendas"
3. Redirecionado para a loja
```

---

## Checklist de Producao

Antes de colocar o site em producao:

- [ ] Executar migracao SQL (`010_invoices_and_shop_system.sql`)
- [ ] Configurar chaves IfthenPay reais no `config.php`
- [ ] Mudar `'sandbox' => false` no config
- [ ] Configurar SMTP real no config (email)
- [ ] Mudar `'debug' => false` no config
- [ ] Mudar `'env' => 'production'` no config
- [ ] Atualizar `'url'` para o dominio real
- [ ] Configurar callback URL real na IfthenPay
- [ ] Testar fluxo completo de compra em sandbox primeiro
- [ ] Verificar que `logs/` tem permissoes de escrita
- [ ] Instalar PHPMailer via Composer (`composer require phpmailer/phpmailer`)
- [ ] Verificar SSL/HTTPS no dominio
- [ ] Testar os 3 modos da loja
- [ ] Testar envio de emails (fatura, confirmacao, envio)
