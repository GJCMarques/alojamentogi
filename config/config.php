<?php

return [

    'db' => [
        'host' => getenv('DB_HOST') ?: '10.0.1.7',
        'name' => getenv('DB_NAME') ?: 'casadogi',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: '',
        'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
        'port' => (int) (getenv('DB_PORT') ?: 3306)
    ],

    'app' => [
        'name' => getenv('APP_NAME') ?: 'A Casa do Gi',
        'url' => getenv('APP_URL') ?: 'https://monrion.cloud',
        'env' => getenv('APP_ENV') ?: 'production',
        'debug' => filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOLEAN),
        'timezone' => getenv('APP_TIMEZONE') ?: 'Europe/Lisbon',
        'locale' => getenv('APP_LOCALE') ?: 'pt_PT',
        'default_language' => getenv('APP_DEFAULT_LANG') ?: 'pt'
    ],

    'mail' => [
        'host' => getenv('MAIL_HOST') ?: '',
        'port' => (int) (getenv('MAIL_PORT') ?: 587),
        'username' => getenv('MAIL_USERNAME') ?: '',
        'password' => getenv('MAIL_PASSWORD') ?: '',
        'encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls',
        'from_email' => getenv('MAIL_FROM_EMAIL') ?: 'noreply@acasadogi.pt',
        'from_name' => getenv('MAIL_FROM_NAME') ?: 'A Casa do Gi',
        'reply_to' => getenv('MAIL_REPLY_TO') ?: 'info@acasadogi.pt'
    ],

    'payment' => [
        'gateway' => getenv('PAYMENT_GATEWAY') ?: 'ifthenpay',
        'sandbox' => filter_var(getenv('PAYMENT_SANDBOX') ?: true, FILTER_VALIDATE_BOOLEAN),
        'ifthenpay' => [
            'mbway_key' => getenv('IFTHENPAY_MBWAY_KEY') ?: '',
            'multibanco_entity' => getenv('IFTHENPAY_MB_ENTITY') ?: '',
            'multibanco_subentity' => getenv('IFTHENPAY_MB_SUBENTITY') ?: '',
            'card_key' => getenv('IFTHENPAY_CARD_KEY') ?: '',
            'anti_phishing_key' => getenv('IFTHENPAY_ANTI_PHISHING_KEY') ?: '',
            'callback_url' => getenv('IFTHENPAY_CALLBACK_URL') ?: 'https://monrion.cloud/api/payment-callback'
        ]
    ],

    'security' => [
        'session_lifetime' => (int) (getenv('SESSION_LIFETIME') ?: 1800),
        'csrf_token_lifetime' => (int) (getenv('CSRF_TOKEN_LIFETIME') ?: 3600),
        'max_login_attempts' => (int) (getenv('MAX_LOGIN_ATTEMPTS') ?: 5),
        'lockout_duration' => (int) (getenv('LOCKOUT_DURATION') ?: 900),
        'password_min_length' => (int) (getenv('PASSWORD_MIN_LENGTH') ?: 8),
        'bcrypt_cost' => (int) (getenv('BCRYPT_COST') ?: 12)
    ],

    'uploads' => [
        'max_file_size' => 5 * 1024 * 1024, // 5MB
        'allowed_image_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
        'path' => __DIR__ . '/../uploads'
    ]
];
