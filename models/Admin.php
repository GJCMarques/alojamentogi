<?php
/**
 * A Casa do Gi - Admin Model
 */

namespace Models;

class Admin extends Model
{
    protected static string $table = 'admins';
    protected static string $primaryKey = 'id';

    protected static array $fillable = [
        'username',
        'email',
        'password_hash',
        'full_name',
        'role',
        'avatar',
        'is_active',
        'last_login',
        'login_attempts',
        'locked_until'
    ];

    protected static array $hidden = ['password_hash', 'password_reset_token'];

    /**
     * Find admin by username
     */
    public static function findByUsername(string $username): ?self
    {
        return self::findBy('username', $username);
    }

    /**
     * Find admin by email
     */
    public static function findByEmail(string $email): ?self
    {
        return self::findBy('email', $email);
    }

    /**
     * Check if account is locked
     */
    public function isLocked(): bool
    {
        if (!$this->locked_until) {
            return false;
        }

        $lockedUntil = strtotime($this->locked_until);
        return $lockedUntil > time();
    }

    /**
     * Check if account is active
     */
    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /**
     * Verify password
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password_hash);
    }

    /**
     * Set password (hashes automatically)
     */
    public function setPassword(string $password): void
    {
        $config = require CONFIG_PATH . '/config.php';
        $cost = $config['security']['bcrypt_cost'] ?? 12;

        $this->password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => $cost]);
    }

    /**
     * Increment login attempts
     */
    public function incrementLoginAttempts(): void
    {
        $this->login_attempts = ($this->login_attempts ?? 0) + 1;

        $config = require CONFIG_PATH . '/config.php';
        $maxAttempts = $config['security']['max_login_attempts'] ?? 5;
        $lockoutDuration = $config['security']['lockout_duration'] ?? 900;

        if ($this->login_attempts >= $maxAttempts) {
            $this->locked_until = date('Y-m-d H:i:s', time() + $lockoutDuration);
        }

        $this->save();
    }

    /**
     * Reset login attempts
     */
    public function resetLoginAttempts(): void
    {
        $this->login_attempts = 0;
        $this->locked_until = null;
        $this->save();
    }

    /**
     * Update last login time
     */
    public function updateLastLogin(): void
    {
        $this->last_login = date('Y-m-d H:i:s');
        $this->save();
    }

    /**
     * Check if admin has role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if admin is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === ROLE_SUPER_ADMIN;
    }

    /**
     * Check if admin can manage users
     */
    public function canManageUsers(): bool
    {
        return in_array($this->role, [ROLE_SUPER_ADMIN, ROLE_ADMIN]);
    }

    /**
     * Check if admin can edit content
     */
    public function canEditContent(): bool
    {
        return in_array($this->role, [ROLE_SUPER_ADMIN, ROLE_ADMIN, ROLE_EDITOR]);
    }

    /**
     * Get all active admins
     */
    public static function getActive(): array
    {
        return self::where('is_active', 1)->orderBy('full_name')->get();
    }

    /**
     * Get display name
     */
    public function getDisplayName(): string
    {
        return $this->full_name ?: $this->username;
    }

    /**
     * Get avatar URL or default
     */
    public function getAvatarUrl(): string
    {
        if ($this->avatar) {
            return upload('avatars/' . $this->avatar);
        }

        // Generate initials-based avatar
        $initials = '';
        $parts = explode(' ', $this->full_name);
        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= mb_substr($part, 0, 1);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($initials) . '&background=657954&color=fff&size=128';
    }
}
