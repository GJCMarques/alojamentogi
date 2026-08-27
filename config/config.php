<?php

return [

    'db' => [
        'host' => '127.0.0.1',
        'name' => 'acasadogi',
        'user' => 'root',
        'pass' => '',
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

    'security' => [
        'session_lifetime' => 43200, // 12 hours
        'csrf_token_lifetime' => 43200, // 12 hours
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