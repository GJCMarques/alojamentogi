<?php

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

    public static function findByUsername(string $username): ?self
    {
        return self::findBy('username', $username);
    }

    public static function findByEmail(string $email): ?self
    {
        return self::findBy('email', $email);
    }

    public function isLocked(): bool
    {
        if (!$this->locked_until) {
            return false;
        }

        $lockedUntil = strtotime($this->locked_until);
        return $lockedUntil > time();
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password_hash);
    }

    public function setPassword(string $password): void
    {
        $config = require CONFIG_PATH . '/config.php';
        $cost = $config['security']['bcrypt_cost'] ?? 12;

        $this->password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => $cost]);
    }

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

    public function resetLoginAttempts(): void
    {
        $this->login_attempts = 0;
        $this->locked_until = null;
        $this->save();
    }

    public function updateLastLogin(): void
    {
        $this->last_login = date('Y-m-d H:i:s');
        $this->save();
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === ROLE_SUPER_ADMIN;
    }

    public function canManageUsers(): bool
    {
        return in_array($this->role, [ROLE_SUPER_ADMIN, ROLE_ADMIN]);
    }

    public function canEditContent(): bool
    {
        return in_array($this->role, [ROLE_SUPER_ADMIN, ROLE_ADMIN, ROLE_EDITOR]);
    }

    public static function getActive(): array
    {
        return self::where('is_active', 1)->orderBy('full_name')->get();
    }

    public function getDisplayName(): string
    {
        return $this->full_name ?: $this->username;
    }

    public function getAvatarUrl(): string
    {
        if ($this->avatar) {
            return upload('avatars/' . $this->avatar);
        }

        $initials = '';
        $parts = explode(' ', $this->full_name);
        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= mb_substr($part, 0, 1);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($initials) . '&background=657954&color=fff&size=128';
    }
}
