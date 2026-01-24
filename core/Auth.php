<?php
/**
 * A Casa do Gi - Authentication Handler
 */

namespace Core;

use Models\Admin;

class Auth
{
    private static ?Admin $currentAdmin = null;

    /**
     * Attempt to authenticate admin
     */
    public static function attempt(string $username, string $password): array
    {
        // Find admin by username or email
        $admin = Admin::findByUsername($username);

        if (!$admin) {
            $admin = Admin::findByEmail($username);
        }

        if (!$admin) {
            return [
                'success' => false,
                'message' => 'Credenciais invalidas'
            ];
        }

        // Check if account is locked
        if ($admin->isLocked()) {
            $lockedUntil = strtotime($admin->locked_until);
            $remainingMinutes = ceil(($lockedUntil - time()) / 60);

            return [
                'success' => false,
                'message' => "Conta bloqueada. Tente novamente em {$remainingMinutes} minutos."
            ];
        }

        // Check if account is active
        if (!$admin->isActive()) {
            return [
                'success' => false,
                'message' => 'Conta desativada'
            ];
        }

        // Verify password
        if (!$admin->verifyPassword($password)) {
            $admin->incrementLoginAttempts();

            $config = require CONFIG_PATH . '/config.php';
            $maxAttempts = $config['security']['max_login_attempts'] ?? 5;
            $remainingAttempts = $maxAttempts - $admin->login_attempts;

            if ($remainingAttempts > 0) {
                return [
                    'success' => false,
                    'message' => "Credenciais invalidas. {$remainingAttempts} tentativas restantes."
                ];
            }

            return [
                'success' => false,
                'message' => 'Conta bloqueada devido a tentativas excessivas'
            ];
        }

        // Success - reset attempts and update last login
        $admin->resetLoginAttempts();
        $admin->updateLastLogin();

        // Set session
        Session::setAdmin($admin->id);

        // Log the action
        self::logAction($admin->id, 'login', 'admin', $admin->id);

        return [
            'success' => true,
            'message' => 'Login bem-sucedido',
            'admin' => $admin
        ];
    }

    /**
     * Logout current admin
     */
    public static function logout(): void
    {
        $adminId = Session::getAdminId();

        if ($adminId) {
            self::logAction($adminId, 'logout', 'admin', $adminId);
        }

        Session::clearAdmin();
        self::$currentAdmin = null;
    }

    /**
     * Check if user is authenticated
     */
    public static function check(): bool
    {
        return Session::isLoggedIn();
    }

    /**
     * Get current authenticated admin
     */
    public static function user(): ?Admin
    {
        if (!self::check()) {
            return null;
        }

        if (self::$currentAdmin === null) {
            $adminId = Session::getAdminId();
            self::$currentAdmin = Admin::find($adminId);

            // Verify admin is still valid
            if (!self::$currentAdmin || !self::$currentAdmin->isActive()) {
                self::logout();
                return null;
            }
        }

        return self::$currentAdmin;
    }

    /**
     * Get current admin ID
     */
    public static function id(): ?int
    {
        return Session::getAdminId();
    }

    /**
     * Require authentication (redirect if not logged in)
     */
    public static function requireAuth(string $redirectTo = '/admin/login.php'): void
    {
        if (!self::check()) {
            Session::flash('error', 'Por favor, faca login para continuar');
            redirect($redirectTo);
        }

        // Verify session IP matches
        $sessionIp = Session::get(SESSION_USER_IP);
        $currentIp = $_SERVER['REMOTE_ADDR'] ?? '';

        if ($sessionIp && $sessionIp !== $currentIp) {
            self::logout();
            Session::flash('error', 'Sessao expirada por razoes de seguranca');
            redirect($redirectTo);
        }

        // Verify admin still exists and is active
        if (!self::user()) {
            Session::flash('error', 'Sessao invalida');
            redirect($redirectTo);
        }
    }

    /**
     * Require specific role
     */
    public static function requireRole(string|array $roles, string $redirectTo = '/admin/'): void
    {
        self::requireAuth();

        $admin = self::user();
        $roles = is_array($roles) ? $roles : [$roles];

        if (!in_array($admin->role, $roles)) {
            Session::flash('error', 'Nao tem permissao para aceder a esta area');
            redirect($redirectTo);
        }
    }

    /**
     * Require super admin role
     */
    public static function requireSuperAdmin(string $redirectTo = '/admin/'): void
    {
        self::requireRole(ROLE_SUPER_ADMIN, $redirectTo);
    }

    /**
     * Check if current user has role
     */
    public static function hasRole(string $role): bool
    {
        $admin = self::user();
        return $admin && $admin->hasRole($role);
    }

    /**
     * Check if current user is super admin
     */
    public static function isSuperAdmin(): bool
    {
        return self::hasRole(ROLE_SUPER_ADMIN);
    }

    /**
     * Check if current user can manage users
     */
    public static function canManageUsers(): bool
    {
        $admin = self::user();
        return $admin && $admin->canManageUsers();
    }

    /**
     * Check if current user can edit content
     */
    public static function canEditContent(): bool
    {
        $admin = self::user();
        return $admin && $admin->canEditContent();
    }

    /**
     * Log admin action to audit log
     */
    public static function logAction(
        ?int $adminId,
        string $action,
        string $entityType,
        ?int $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        $db = Database::getInstance();

        $db->insert('audit_log', [
            'admin_id' => $adminId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => getClientIp(),
            'user_agent' => substr(getUserAgent(), 0, 500)
        ]);
    }

    /**
     * Generate password reset token
     */
    public static function generatePasswordResetToken(Admin $admin): string
    {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $admin->password_reset_token = hash('sha256', $token);
        $admin->password_reset_expires = $expires;
        $admin->save();

        return $token;
    }

    /**
     * Verify password reset token
     */
    public static function verifyPasswordResetToken(string $token): ?Admin
    {
        $hashedToken = hash('sha256', $token);

        $db = Database::getInstance();
        $row = $db->fetch(
            "SELECT * FROM admins
             WHERE password_reset_token = ?
             AND password_reset_expires > NOW()
             AND is_active = 1",
            [$hashedToken]
        );

        if (!$row) {
            return null;
        }

        $admin = new Admin($row);
        $admin->exists = true;

        return $admin;
    }

    /**
     * Clear password reset token
     */
    public static function clearPasswordResetToken(Admin $admin): void
    {
        $admin->password_reset_token = null;
        $admin->password_reset_expires = null;
        $admin->save();
    }
}
