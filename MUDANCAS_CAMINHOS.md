# Configuração de Caminhos - XAMPP

## Configuração Atual (Desenvolvimento)

A aplicação está configurada para funcionar em **subpasta do XAMPP**:
```
http://localhost/alojamentogi
```

## Ficheiros de Configuração

### 1. config/config.php
```php
'url' => 'http://localhost/alojamentogi',
```

### 2. .htaccess
```apache
RewriteBase /alojamentogi/
```

### 3. Como Funciona

A função `basePath()` em `includes/functions.php` deteta automaticamente o caminho base da aplicação a partir da configuração `app.url` e retorna `/alojamentogi`.

Todos os links no site usam `basePath()`, então os URLs ficam automaticamente corretos:
```php
$base = basePath(); // Retorna: '/alojamentogi'
echo $base . '/loja/'; // Output: '/alojamentogi/loja/'
```

## URLs Corretos (Desenvolvimento)

### Frontend Português
- Homepage: `http://localhost/alojamentogi/`
- Alojamento: `http://localhost/alojamentogi/alojamento/`
- Loja: `http://localhost/alojamentogi/loja/`
- Carrinho: `http://localhost/alojamentogi/loja/carrinho/`
- Checkout: `http://localhost/alojamentogi/loja/checkout/`
- Pagamento: `http://localhost/alojamentogi/loja/checkout/pagamento/`
- Confirmação: `http://localhost/alojamentogi/loja/checkout/confirmacao/`
- Atividades: `http://localhost/alojamentogi/atividades/`
- Sobre Nós: `http://localhost/alojamentogi/sobre-nos/`
- Contactos: `http://localhost/alojamentogi/contactos/`

### Frontend English
- Homepage: `http://localhost/alojamentogi/en/`
- Accommodation: `http://localhost/alojamentogi/en/accommodation/`
- Shop: `http://localhost/alojamentogi/en/shop/`
- Activities: `http://localhost/alojamentogi/en/activities/`
- About Us: `http://localhost/alojamentogi/en/about-us/`
- Contact: `http://localhost/alojamentogi/en/contact/`

### Admin
- Login: `http://localhost/alojamentogi/admin/login/`
- Dashboard: `http://localhost/alojamentogi/admin/`
- Produtos: `http://localhost/alojamentogi/admin/produtos/`
- Encomendas: `http://localhost/alojamentogi/admin/encomendas/`
- Categorias: `http://localhost/alojamentogi/admin/categorias/`
- Atividades: `http://localhost/alojamentogi/admin/atividades/`
- Mensagens: `http://localhost/alojamentogi/admin/mensagens/`
- Configurações: `http://localhost/alojamentogi/admin/configuracoes/`

### API
- Carrinho: `http://localhost/alojamentogi/api/cart.php`
- Processar Pagamento: `http://localhost/alojamentogi/api/payment-process.php`
- Callback Pagamento: `http://localhost/alojamentogi/api/payment-callback.php`
- Status Pagamento: `http://localhost/alojamentogi/api/check-payment-status.php`

## Deploy para Produção

Quando fizer deploy para produção, apenas precisa de alterar 2 ficheiros:

### 1. config/config.php
```php
'app' => [
    'name' => 'A Casa do Gi',
    'url' => 'https://acasadogi.pt',  // URL de produção (sem /alojamentogi)
    'env' => 'production',
    'debug' => false,
    // ...
],
```

### 2. .htaccess
```apache
RewriteBase /  # Na raiz do domínio
```

## Configuração IfthenPay

### Desenvolvimento (XAMPP)
Callback URL:
```
http://localhost/alojamentogi/api/payment-callback.php
```

### Produção
Callback URL:
```
https://acasadogi.pt/api/payment-callback.php
```

## Estrutura de Pastas XAMPP

```
C:/xampp/htdocs/
└── alojamentogi/          ← Pasta raiz da aplicação
    ├── admin/             ← Área de administração
    ├── api/               ← Endpoints da API
    ├── alojamento/        ← Página de alojamento
    ├── atividades/        ← Página de atividades
    ├── config/            ← Configurações
    ├── contactos/         ← Página de contactos
    ├── core/              ← Classes do sistema
    ├── database/          ← Scripts SQL
    ├── en/                ← Versão inglesa
    ├── includes/          ← Header, footer, init
    ├── loja/              ← Sistema de loja
    ├── logs/              ← Ficheiros de log
    ├── models/            ← Modelos de dados
    ├── sobre-nos/         ← Página sobre nós
    ├── uploads/           ← Ficheiros enviados
    ├── vendor/            ← Dependências Composer
    ├── .htaccess          ← Configuração Apache
    ├── index.php          ← Homepage
    └── ...
```

## Verificações

✅ **basePath()** retorna `/alojamentogi` corretamente
✅ **Todos os links** do frontend usam `basePath()`
✅ **Todos os links** do admin usam `basePath()`
✅ **JavaScript fetch()** usa `<?= $base ?>`
✅ **Redirects** funcionam automaticamente
✅ **Função redirect()** adiciona basePath() quando necessário

## Exemplo de Uso no Código

```php
<?php
$base = basePath(); // Retorna: '/alojamentogi'
?>

<!-- Links HTML -->
<a href="<?= $base ?>/loja/">Loja</a>
<!-- Output: <a href="/alojamentogi/loja/">Loja</a> -->

<!-- JavaScript -->
<script>
fetch('<?= $base ?>/api/cart.php', { ... })
// Faz fetch para: http://localhost/alojamentogi/api/cart.php
</script>

<!-- Redirects PHP -->
<?php
redirect('/loja/');
// Redireciona para: http://localhost/alojamentogi/loja/
?>
```

## Notas Importantes

1. **Nunca** use URLs hardcoded como `/loja/` sem `basePath()`
2. **Sempre** use `$base = basePath()` no início das páginas
3. Em **JavaScript**, use `<?= $base ?>` (não `{$base}`)
4. A função `redirect()` já adiciona automaticamente o basePath se o URL começar com `/`

## Troubleshooting

### Links vão para localhost/ sem /alojamentogi
➜ Verifique se `config/config.php` tem `'url' => 'http://localhost/alojamentogi'`

### Erro 404 no Apache
➜ Verifique se `.htaccess` tem `RewriteBase /alojamentogi/`

### JavaScript fetch() dá erro 404
➜ Verifique se está a usar `<?= $base ?>` e não `{$base}`

A aplicação está configurada corretamente para XAMPP! 🎉
