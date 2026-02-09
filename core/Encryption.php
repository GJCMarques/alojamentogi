<?php
/**
 * A Casa do Gi - AES-256-GCM Encryption
 *
 * Encrypts/decrypts sensitive settings stored in the database.
 * Uses AES-256-GCM for authenticated encryption (confidentiality + integrity).
 *
 * The encryption key is derived from a master key stored in config/encryption.key
 * (outside the webroot is ideal, but for XAMPP we use a non-web-accessible file).
 */

namespace Core;

class Encryption
{
    private const CIPHER = 'aes-256-gcm';
    private const KEY_LENGTH = 32; // 256 bits
    private const TAG_LENGTH = 16;

    // Prefix to identify encrypted values in the database
    private const ENCRYPTED_PREFIX = 'enc:';

    // Settings keys that should be encrypted
    private const SENSITIVE_KEYS = [
        'ifthenpay_mbway_key',
        'ifthenpay_card_key',
        'ifthenpay_anti_phishing_key',
        'smtp_pass',
        'smtp_user',
    ];

    private static ?string $key = null;

    /**
     * Check if a setting key is sensitive and should be encrypted
     */
    public static function isSensitive(string $key): bool
    {
        return in_array($key, self::SENSITIVE_KEYS, true);
    }

    /**
     * Get the encryption key, generating one if it doesn't exist
     */
    private static function getKey(): string
    {
        if (self::$key !== null) {
            return self::$key;
        }

        $keyFile = defined('CONFIG_PATH') ? CONFIG_PATH . '/encryption.key' : dirname(__DIR__) . '/config/encryption.key';

        if (!file_exists($keyFile)) {
            // Generate a new key
            $newKey = random_bytes(self::KEY_LENGTH);
            $encoded = base64_encode($newKey);

            // Write key file with restrictive permissions
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

    /**
     * Encrypt a plaintext value
     *
     * Format: enc:base64(iv + tag + ciphertext)
     */
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

        // Pack: IV + TAG + CIPHERTEXT
        $packed = $iv . $tag . $ciphertext;

        return self::ENCRYPTED_PREFIX . base64_encode($packed);
    }

    /**
     * Decrypt an encrypted value
     *
     * Returns the plaintext, or the original value if not encrypted
     */
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

    /**
     * Check if a value is already encrypted
     */
    public static function isEncrypted(string $value): bool
    {
        return str_starts_with($value, self::ENCRYPTED_PREFIX);
    }

    /**
     * Encrypt a value only if it's not already encrypted
     */
    public static function encryptIfNeeded(string $value): string
    {
        if (empty($value) || self::isEncrypted($value)) {
            return $value;
        }
        return self::encrypt($value);
    }

    /**
     * Re-encrypt all sensitive settings (useful after key rotation)
     */
    public static function reEncryptAll(): int
    {
        $db = Database::getInstance();
        $count = 0;

        foreach (self::SENSITIVE_KEYS as $key) {
            $row = $db->fetch("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);
            if ($row && !empty($row['setting_value'])) {
                // Decrypt with current key (if encrypted), then re-encrypt
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
