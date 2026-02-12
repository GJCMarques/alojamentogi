<?php

namespace Core;

class Encryption
{
    private const CIPHER = 'aes-256-gcm';
    private const KEY_LENGTH = 32;
    private const TAG_LENGTH = 16;

    private const ENCRYPTED_PREFIX = 'enc:';

    private const SENSITIVE_KEYS = [
        'ifthenpay_mbway_key',
        'ifthenpay_card_key',
        'ifthenpay_anti_phishing_key',
        'smtp_pass',
        'smtp_user',
    ];

    private static ?string $key = null;

    public static function isSensitive(string $key): bool
    {
        return in_array($key, self::SENSITIVE_KEYS, true);
    }

    private static function getKey(): string
    {
        if (self::$key !== null) {
            return self::$key;
        }

        $keyFile = defined('CONFIG_PATH') ? CONFIG_PATH . '/encryption.key' : dirname(__DIR__) . '/config/encryption.key';

        if (!file_exists($keyFile)) {

            $newKey = random_bytes(self::KEY_LENGTH);
            $encoded = base64_encode($newKey);

            file_put_contents($keyFile, $encoded);
            if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
                chmod($keyFile, 0600);
            }

            self::$key = $newKey;
            return self::$key;
        }

        $encoded = trim(file_get_contents($keyFile));
        $decoded = base64_decode($encoded, true);

        if ($decoded === false || strlen($decoded) !== self::KEY_LENGTH) {
            throw new \RuntimeException('Invalid encryption key file. Delete config/encryption.key to regenerate.');
        }

        self::$key = $decoded;
        return self::$key;
    }

    public static function encrypt(string $plaintext): string
    {
        if (empty($plaintext)) {
            return '';
        }

        $key = self::getKey();
        $iv = random_bytes(openssl_cipher_iv_length(self::CIPHER));

        $tag = '';
        $ciphertext = openssl_encrypt(
            $plaintext,
            self::CIPHER,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '', // AAD
            self::TAG_LENGTH
        );

        if ($ciphertext === false) {
            throw new \RuntimeException('Encryption failed');
        }

        $packed = $iv . $tag . $ciphertext;

        return self::ENCRYPTED_PREFIX . base64_encode($packed);
    }

    public static function decrypt(string $value): string
    {
        if (empty($value) || !self::isEncrypted($value)) {
            return $value;
        }

        $key = self::getKey();
        $packed = base64_decode(substr($value, strlen(self::ENCRYPTED_PREFIX)), true);

        if ($packed === false) {
            logMessage('Decryption failed: invalid base64', 'error');
            return '';
        }

        $ivLength = openssl_cipher_iv_length(self::CIPHER);
        $minLength = $ivLength + self::TAG_LENGTH + 1;

        if (strlen($packed) < $minLength) {
            logMessage('Decryption failed: data too short', 'error');
            return '';
        }

        $iv = substr($packed, 0, $ivLength);
        $tag = substr($packed, $ivLength, self::TAG_LENGTH);
        $ciphertext = substr($packed, $ivLength + self::TAG_LENGTH);

        $plaintext = openssl_decrypt(
            $ciphertext,
            self::CIPHER,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($plaintext === false) {
            logMessage('Decryption failed: authentication failed or wrong key', 'error');
            return '';
        }

        return $plaintext;
    }

    public static function isEncrypted(string $value): bool
    {
        return str_starts_with($value, self::ENCRYPTED_PREFIX);
    }

    public static function encryptIfNeeded(string $value): string
    {
        if (empty($value) || self::isEncrypted($value)) {
            return $value;
        }
        return self::encrypt($value);
    }

    public static function reEncryptAll(): int
    {
        $db = Database::getInstance();
        $count = 0;

        foreach (self::SENSITIVE_KEYS as $key) {
            $row = $db->fetch("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);
            if ($row && !empty($row['setting_value'])) {

                $plain = self::isEncrypted($row['setting_value'])
                    ? self::decrypt($row['setting_value'])
                    : $row['setting_value'];

                if (!empty($plain)) {
                    $encrypted = self::encrypt($plain);
                    $db->update('settings', ['setting_value' => $encrypted], 'setting_key = ?', [$key]);
                    $count++;
                }
            }
        }

        return $count;
    }
}
