# A Casa do Gi - Guia de Instalação

## Requisitos do Sistema

- PHP 8.0 ou superior
- MySQL 5.7 ou superior
- Apache/Nginx com mod_rewrite
- Composer (opcional, para dependências futuras)

## Instalação

### 1. Base de Dados

Execute o script SQL para criar as tabelas:

```bash
mysql -u root -p < database/schema.sql
```

Insira os dados iniciais:

```bash
mysql -u root -p casadogi < database/seed.sql
```

### 2. Configuração

O ficheiro de configuração está localizado em `config/config.php`.

#### Base de Dados

```php
'db' => [
    'host' => 'localhost',
    'name' => 'casadogi',
    'user' => 'root',
    'pass' => '',
    'charset' => 'utf8mb4',
    'port' => 3306
],
```

#### Configuração IfthenPay

Para ativar os pagamentos, precisa de configurar as suas chaves IfthenPay:

```php
'payment' => [
    'gateway' => 'ifthenpay',
    'sandbox' => true, // Altere para false em produção
    'ifthenpay' => [
        'mbway_key' => 'SUA_CHAVE_MBWAY',
        'multibanco_entity' => 'ENTIDADE',      // Ex: 11249
        'multibanco_subentity' => 'SUBENTIDADE', // Ex: 001
        'card_key' => 'SUA_CHAVE_CARTAO',
        'anti_phishing_key' => 'SUA_CHAVE_ANTIPHISHING',
        'callback_url' => 'https://seu-dominio.pt/api/payment-callback'
    ]
],
```

**Como obter as chaves IfthenPay:**

1. Aceda ao backoffice IfthenPay em https://ifthenpay.com/
2. No menu, aceda a "API" ou "Configurações"
3. Obtenha as seguintes chaves:
   - **MBWay Key**: Para pagamentos MBWay
   - **Entidade Multibanco**: Para referências Multibanco
   - **Card Key**: Para pagamentos com cartão
   - **Anti-Phishing Key**: Para validar callbacks

4. Configure o URL de callback no backoffice IfthenPay:
   - URL: `https://seu-dominio.pt/api/payment-callback.php`
   - Este URL receberá notificações de pagamentos confirmados

### 3. Permissões

Certifique-se que as pastas têm permissões de escrita:

```bash
chmod -R 755 uploads/
chmod -R 755 logs/
```

### 4. .htaccess (se usar subpasta)

Se o site estiver numa subpasta (ex: localhost/alojamentogi), o `.htaccess` já está configurado.

Para usar na raiz, altere em `config/config.php`:

```php
'app' => [
    'url' => 'http://seu-dominio.pt',
],
```

## Credenciais de Administração

Depois de executar o `seed.sql`, use as seguintes credenciais:

- **Username**: admin
- **Password**: admin123

**IMPORTANTE**: Altere a password após o primeiro login!

## Estrutura de URLs

### Frontend (Português)
- Homepage: `/`
- Alojamento: `/alojamento/`
- Loja: `/loja/`
- Atividades: `/atividades/`
- Sobre Nós: `/sobre-nos/`
- Contactos: `/contactos/`

### Frontend (English)
- Homepage: `/en/`
- Accommodation: `/en/accommodation/`
- Shop: `/en/shop/`
- Activities: `/en/activities/`
- About Us: `/en/about-us/`
- Contact: `/en/contact/`

### Admin
- Login: `/admin/login/`
- Dashboard: `/admin/`

### API
- Carrinho: `/api/cart.php`
- Pagamento: `/api/payment-callback.php`

## Fluxo de Checkout e Pagamento

### 1. Carrinho de Compras
- Os utilizadores adicionam produtos ao carrinho via API REST
- O carrinho é guardado na sessão PHP
- Endpoint: `/api/cart.php`

### 2. Checkout
- Página: `/loja/checkout/`
- O utilizador preenche:
  - Dados pessoais (nome, email, telefone)
  - Morada de envio
  - Método de pagamento (MBWay, Multibanco ou Cartão)
- É criada uma encomenda na base de dados com status "pending"

### 3. Pagamento
- Página: `/loja/checkout/pagamento/`
- Consoante o método escolhido:

  **MBWay:**
  - É enviado um pedido de pagamento via API IfthenPay
  - O utilizador confirma no telemóvel
  - A página faz polling a cada 5 segundos para verificar o estado
  - Após confirmação, redireciona para a página de confirmação

  **Multibanco:**
  - É gerada uma referência Multibanco
  - São exibidos: Entidade, Referência e Valor
  - O utilizador paga num ATM ou homebanking
  - Quando o IfthenPay confirma, chama o callback
  - O callback atualiza o estado da encomenda

  **Cartão:**
  - O utilizador é redirecionado para o gateway IfthenPay
  - Após pagamento, redireciona de volta para o site
  - O callback confirma o pagamento

### 4. Confirmação
- Página: `/loja/checkout/confirmacao/`
- Exibe resumo da encomenda
- Se pago: limpa o carrinho
- Envia email de confirmação ao cliente

### 5. Callback IfthenPay
- Endpoint: `/api/payment-callback.php`
- Recebe notificações do IfthenPay
- Valida a autenticidade (anti-phishing key)
- Atualiza o estado da encomenda para "paid"
- Deduz stock dos produtos
- Envia email de confirmação

## Configuração do IfthenPay Callback

No backoffice IfthenPay, configure:

1. **URL de Callback**: `https://seu-dominio.pt/api/payment-callback.php`
2. **Método**: POST
3. **Parâmetros enviados**:
   - `order_id`: ID da encomenda
   - `reference`: Referência de pagamento
   - `amount`: Valor pago
   - `key`: Chave de autenticação

## Logs

Os callbacks de pagamento são registados em:
```
logs/payment-callbacks.log
```

Útil para debugging de problemas com pagamentos.

## Modo Sandbox

Para testar sem efetuar pagamentos reais, mantenha:

```php
'sandbox' => true
```

Em produção, altere para:

```php
'sandbox' => false
```

## Emails

Configure o SMTP em `config/config.php`:

```php
'mail' => [
    'host' => 'smtp.seu-servidor.com',
    'port' => 587,
    'username' => 'seu-email@dominio.pt',
    'password' => 'sua-password',
    'encryption' => 'tls',
    'from_email' => 'noreply@acasadogi.pt',
    'from_name' => 'A Casa do Gi',
],
```

## Segurança

### Em Produção

1. Altere em `config/config.php`:
```php
'app' => [
    'env' => 'production',
    'debug' => false,
],
```

2. Use HTTPS (obrigatório para pagamentos)

3. Altere todas as passwords padrão

4. Configure permissões corretas nos ficheiros:
```bash
chmod 644 config/config.php
chmod 755 uploads/
```

5. Adicione `config/config.php` ao `.gitignore`

## Problemas Comuns

### "Call to undefined method Core\Language::getCurrentLanguageId()"
- Certifique-se que executou a última versão dos ficheiros
- O método correto é `getCurrentLangId()`

### Não consigo fazer login no admin
- Verifique que executou o `seed.sql` atualizado
- Password: admin123 (não "password")

### Pagamentos não funcionam
- Verifique as chaves IfthenPay em `config/config.php`
- Verifique se o callback URL está configurado no backoffice IfthenPay
- Consulte os logs em `logs/payment-callbacks.log`

### Imagens não aparecem
- Verifique permissões da pasta `uploads/`
- Verifique se `UPLOADS_URL` está definido em `includes/init.php`

## Suporte

Para questões ou problemas:
- Email: info@acasadogi.pt
- Telefone: +351 279 340 100
