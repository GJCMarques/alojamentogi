<?php

namespace Core;

use Models\Admin;

class Auth
{
    private static ?Admin $currentAdmin = null;

    public static function attempt(string $username, string $password): array
    {

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

        if ($admin->isLocked()) {
            $lockedUntil = strtotime($admin->locked_until);
            $remainingMinutes = ceil(($lockedUntil - time()) / 60);

            return [
                'success' => false,
                'message' => "Conta bloqueada. Tente novamente em {$remainingMinutes} minutos."
            ];
        }

        if (!$admin->isActive()) {
            return [
                'success' => false,
                'message' => 'Conta desativada'
            ];
        }

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

        $admin->resetLoginAttempts();
        $admin->updateLastLogin();

        Session::setAdmin($admin->id);
        Session::set('_admin_last_activity', time());

        self::logAction($admin->id, 'login', 'admin', $admin->id);

        return [
            'success' => true,
            'message' => 'Login bem-sucedido',
            'admin' => $admin
        ];
    }

    public static function logout(): void
    {
        $adminId = Session::getAdminId();

        if ($adminId) {
            self::logAction($adminId, 'logout', 'admin', $adminId);
        }

        Session::clearAdmin();
        self::$currentAdmin = null;
    }

    public static function check(): bool
    {
        return Session::isLoggedIn();
    }

    public static function user(): ?Admin
    {
        if (!self::check()) {
            return null;
        }

        if (self::$currentAdmin === null) {
            $adminId = Session::getAdminId();
            self::$currentAdmin = Admin::find($adminId);

            if (!self::$currentAdmin || !self::$currentAdmin->isActive()) {
                self::logout();
                return null;
            }
        }

        return self::$currentAdmin;
    }

    public static function id(): ?int
    {
        return Session::getAdminId();
    }

    public static function requireAuth(string $redirectTo = '/admin/login.php'): void
    {
        if (!self::check()) {
            // Sem flash de "erro": aceder ao /admin sem sessão mostra apenas o formulário de login.
            redirect($redirectTo);
        }

        // IP do cliente via X-Forwarded-For (estável atrás do proxy), consistente com Session::setAdmin.
        $sessionIp = Session::get(SESSION_USER_IP);
        $currentIp = getClientIp();

        if ($sessionIp && $sessionIp !== $currentIp) {
            self::logout();
            Session::flash('error', 'Sessao expirada por razoes de seguranca');
            redirect($redirectTo);
        }

        if (!self::user()) {
            Session::flash('error', 'Sessao invalida');
            redirect($redirectTo);
        }
    }

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

    public static function requireSuperAdmin(string $redirectTo = '/admin/'): void
    {
        self::requireRole(ROLE_SUPER_ADMIN, $redirectTo);
    }

    public static function hasRole(string $role): bool
    {
        $admin = self::user();
        return $admin && $admin->hasRole($role);
    }

    public static function isSuperAdmin(): bool
    {
        return self::hasRole(ROLE_SUPER_ADMIN);
    }

    public static function canManageUsers(): bool
    {
        $admin = self::user();
        return $admin && $admin->canManageUsers();
    }

    public static function canEditContent(): bool
    {
        $admin = self::user();
        return $admin && $admin->canEditContent();
    }

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

    public static function generatePasswordResetToken(Admin $admin): string
    {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $admin->password_reset_token = hash('sha256', $token);
        $admin->password_reset_expires = $expires;
        $admin->save();

        return $token;
    }

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

    public static function clearPasswordResetToken(Admin $admin): void
    {
        $admin->password_reset_token = null;
        $admin->password_reset_expires = null;
        $admin->save();
    }
}
