<?php

return [

    'db' => [
        'host' => 'alojamentogi-mysql-8g3t8r', // O Internal Host do Dokploy
        'name' => 'casadogi',
        'user' => 'casadogi_user',
        'pass' => 'CasadoGi2026',
        'charset' => 'utf8mb4',
        'port' => 3306
    ],

    'app' => [
        'name' => 'A Casa do Gi',
        'url' => 'https://monrion.cloud', // O teu domínio real com HTTPS
        'env' => 'production', // Mudei para production para ser mais seguro
        'debug' => false, // Desligar o debug em produção (se der erro no futuro metes true para testar)
        'timezone' => 'Europe/Lisbon',
        'locale' => 'pt_PT',
        'default_language' => 'pt'
    ],

    'mail' => [
        'host' => '',
        'port' => 587,
        'username' => '',
        'password' => '',
        'encryption' => 'tls',
        'from_email' => 'noreply@acasadogi.pt',
        'from_name' => 'A Casa do Gi',
        'reply_to' => 'info@acasadogi.pt'
    ],

    'payment' => [
        'gateway' => 'ifthenpay',
        'sandbox' => true,
        'ifthenpay' => [
            'mbway_key' => '',
            'multibanco_entity' => '',
            'multibanco_subentity' => '',
            'card_key' => '',
            'anti_phishing_key' => '',
            'callback_url' => 'https://monrion.cloud/api/payment-callback' // Atualizado para o domínio real
        ]
    ],

    'security' => [
        'session_lifetime' => 1800,
        'csrf_token_lifetime' => 3600,
        'max_login_attempts' => 5,
        'lockout_duration' => 900,
        'password_min_length' => 8,
        'bcrypt_cost' => 12
    ],

    'uploads' => [
        'max_file_size' => 5 * 1024 * 1024, // 5MB
        'allowed_image_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
        'path' => __DIR__ . '/../uploads'
    ]
];