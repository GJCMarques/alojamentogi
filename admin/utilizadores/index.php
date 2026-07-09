<?php

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;
use Core\Auth;

if (!Auth::canManageUsers()) {
    Session::flash('error', 'Sem permissões para aceder a esta página.');
    redirect('/admin/');
}

$db = Database::getInstance();
$rateLimiter = \Core\RateLimiter::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['delete']) || isset($_GET['toggle'])) {
    if (!$rateLimiter->check('admin_user_mgmt', 20, 300)) {
        Session::flash('error', 'Demasiadas ações. Aguarde alguns minutos.');
        redirect('/admin/utilizadores/');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_user') {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        $id = (int)($_POST['user_id'] ?? 0);
        $adminPassword = $_POST['admin_password'] ?? '';
        $currentAdmin = Auth::user();

        if ($id === (int)$currentAdmin->id) {
            Session::flash('error', 'Não pode eliminar a sua própria conta.');
        }

        elseif (!password_verify($adminPassword, $currentAdmin->password_hash)) {
            Session::flash('error', 'Password de confirmação incorreta.');
            $rateLimiter->recordFailure('admin_user_mgmt_auth');
            logMessage("Failed admin password verification for user delete by admin ID {$currentAdmin->id}", 'warning');
        } else {
            $targetUser = $db->fetch("SELECT full_name, username FROM admins WHERE id = ?", [$id]);
            if ($targetUser) {
                $db->delete('admins', 'id = ?', [$id]);
                Session::flash('success', 'Utilizador "' . e($targetUser['full_name'] ?: $targetUser['username']) . '" eliminado.');
                logMessage("Admin {$currentAdmin->username} deleted user ID {$id} ({$targetUser['username']})", 'info');
            }
        }
    }
    redirect('/admin/utilizadores/');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_user') {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        $id = (int)($_POST['user_id'] ?? 0);
        $currentAdmin = Auth::user();

        if ($id === (int)$currentAdmin->id) {
            Session::flash('error', 'Não pode desativar a sua própria conta.');
        } else {
            $db->query("UPDATE admins SET is_active = NOT is_active WHERE id = ?", [$id]);
            Session::flash('success', 'Estado atualizado.');
            logMessage("Admin {$currentAdmin->username} toggled active state for user ID {$id}", 'info');
        }
    }
    redirect('/admin/utilizadores/');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['action']) || $_POST['action'] === 'save_user')) {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        $errors = [];
        $editId = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;
        $currentAdmin = Auth::user();

        $username = sanitize($_POST['username'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $fullName = sanitize($_POST['full_name'] ?? '');
        $role = $_POST['role'] ?? 'editor';
        $password = $_POST['password'] ?? '';
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $adminPassword = $_POST['admin_password'] ?? '';

        $needsPasswordConfirm = !$editId || !empty($password);
        if ($needsPasswordConfirm) {
            if (empty($adminPassword)) {
                $errors[] = 'Insira a sua password para confirmar esta ação.';
            } elseif (!password_verify($adminPassword, $currentAdmin->password_hash)) {
                $errors[] = 'Password de confirmação incorreta.';
                $rateLimiter->recordFailure('admin_user_mgmt_auth');
                logMessage("Failed admin password verification for user save by admin ID {$currentAdmin->id}", 'warning');
            }
        }

        if (empty($username)) {
            $errors[] = 'O nome de utilizador é obrigatório.';
        } elseif (strlen($username) < 3) {
            $errors[] = 'O nome de utilizador deve ter pelo menos 3 caracteres.';
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email inválido.';
        }
        if (!$editId && empty($password)) {
            $errors[] = 'A password é obrigatória para novos utilizadores.';
        }
        if ($password && strlen($password) < 8) {
            $errors[] = 'A password deve ter pelo menos 8 caracteres.';
        }
        if ($password && !preg_match('/[A-Z]/', $password)) {
            $errors[] = 'A password deve conter pelo menos uma letra maiúscula.';
        }
        if ($password && !preg_match('/[0-9]/', $password)) {
            $errors[] = 'A password deve conter pelo menos um número.';
        }
        if (!in_array($role, ['admin', 'editor'])) {
            $errors[] = 'Role inválido.';
        }

        $checkWhere = $editId ? "AND id != ?" : "";
        $checkParams = $editId ? [$username, $editId] : [$username];
        $existingUsername = $db->fetch("SELECT id FROM admins WHERE username = ? {$checkWhere}", $checkParams);
        if ($existingUsername) {
            $errors[] = 'Este nome de utilizador já existe.';
        }

        $checkParams = $editId ? [$email, $editId] : [$email];
        $existingEmail = $db->fetch("SELECT id FROM admins WHERE email = ? {$checkWhere}", $checkParams);
        if ($existingEmail) {
            $errors[] = 'Este email já está registado.';
        }

        if (empty($errors)) {
            $data = [
                'username' => $username,
                'email' => $email,
                'full_name' => $fullName,
                'role' => $role,
                'is_active' => $isActive
            ];

            if ($password) {
                $data['password_hash'] = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            }

            if ($editId) {

                if ($editId === (int)$currentAdmin->id) {
                    unset($data['role']);
                    unset($data['is_active']);
                }
                $db->update('admins', $data, 'id = ?', [$editId]);
                Session::flash('success', 'Utilizador atualizado.');
                logMessage("Admin {$currentAdmin->username} updated user ID {$editId}", 'info');
            } else {
                $db->insert('admins', $data);
                Session::flash('success', 'Utilizador criado.');
                logMessage("Admin {$currentAdmin->username} created new user: {$username}", 'info');
            }
            redirect('/admin/utilizadores/');
        } else {
            Session::flash('error', implode('<br>', $errors));
        }
    }
}

$editUser = null;
if (isset($_GET['edit'])) {
    $editUser = $db->fetch("SELECT * FROM admins WHERE id = ?", [(int)$_GET['edit']]);
}

$users = $db->fetchAll("SELECT * FROM admins ORDER BY role, full_name");

$roles = [
    'admin' => 'Administrador',
    'editor' => 'Editor'
];

$authFailures = $rateLimiter->getFailureCount('admin_user_mgmt_auth', 300);
$authLocked = $authFailures >= 5;

$pageTitle = 'Utilizadores';
$currentPage = 'utilizadores';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-primary">Utilizadores</h1>
        <p class="text-granite-500 text-sm">Gerir utilizadores do back-office</p>
    </div>
    <div class="flex items-center gap-2 text-xs text-granite-400">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
        Ações protegidas por password
    </div>
</div>

<?php if ($authLocked): ?>
<div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
    <div class="flex items-center gap-2">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        Demasiadas tentativas de confirmação falhadas. Ações de utilizador bloqueadas temporariamente (5 min).
    </div>
</div>
<?php endif; ?>

<div class="grid lg:grid-cols-3 gap-6">
    <!-- Users List -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-sm overflow-x-auto border border-granite-200">
            <table class="min-w-full divide-y divide-granite-200">
                <thead class="bg-granite-50">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-granite-500 uppercase">Utilizador</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-granite-500 uppercase">Role</th>
                        <th class="px-4 sm:px-6 py-3 text-center text-xs font-medium text-granite-500 uppercase">Estado</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-granite-500 uppercase hidden md:table-cell">Último Login</th>
                        <th class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-granite-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-granite-200">
                    <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-granite-50 <?= $user['id'] === ($_SESSION['admin_id'] ?? 0) ? 'bg-secondary-50/50' : '' ?>">
                        <td class="px-4 sm:px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-secondary-100 flex items-center justify-center text-secondary-600 font-semibold mr-3">
                                    <?= strtoupper(substr($user['full_name'] ?: $user['username'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-granite-800">
                                        <?= e($user['full_name'] ?: $user['username']) ?>
                                        <?php if ($user['id'] === ($_SESSION['admin_id'] ?? 0)): ?>
                                        <span class="text-xs text-secondary-600 ml-1">(você)</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-xs text-granite-500"><?= e($user['email']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 sm:px-6 py-4">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded <?= $user['role'] === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-granite-100 text-granite-800' ?>">
                                <?= $roles[$user['role']] ?? $user['role'] ?>
                            </span>
                        </td>
                        <td class="px-4 sm:px-6 py-4 text-center">
                            <?php if ($user['id'] !== ($_SESSION['admin_id'] ?? 0) && !$authLocked): ?>
                            <form method="POST" class="inline">
                                <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
                                <input type="hidden" name="action" value="toggle_user">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <button type="submit" class="inline-flex px-2 py-1 text-xs font-medium rounded cursor-pointer <?= $user['is_active'] ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-granite-100 text-granite-600 hover:bg-granite-200' ?> transition-colors">
                                    <?= $user['is_active'] ? 'Ativo' : 'Inativo' ?>
                                </button>
                            </form>
                            <?php else: ?>
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-800">
                                Ativo
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 sm:px-6 py-4 text-xs text-granite-500 hidden md:table-cell">
                            <?= $user['last_login'] ? timeAgo($user['last_login']) : 'Nunca' ?>
                        </td>
                        <td class="px-4 sm:px-6 py-4 text-right">
                            <a href="?edit=<?= $user['id'] ?>"
                               class="text-secondary-600 hover:text-secondary-800 text-sm mr-3">Editar</a>
                            <?php if ($user['id'] !== ($_SESSION['admin_id'] ?? 0) && !$authLocked): ?>
                            <button type="button"
                                    onclick="showDeleteModal(<?= $user['id'] ?>, '<?= e($user['full_name'] ?: $user['username']) ?>')"
                                    class="text-red-600 hover:text-red-800 text-sm">Eliminar</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create/Edit Form -->
    <div>
        <div class="bg-white rounded-lg shadow-sm p-6 border border-granite-200">
            <h2 class="text-lg font-semibold text-granite-800 mb-4">
                <?= $editUser ? 'Editar Utilizador' : 'Novo Utilizador' ?>
            </h2>

            <form action="" method="post">
                <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
                <input type="hidden" name="action" value="save_user">
                <?php if ($editUser): ?>
                <input type="hidden" name="edit_id" value="<?= $editUser['id'] ?>">
                <?php endif; ?>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-granite-700 mb-1">Nome Completo</label>
                        <input type="text"
                               name="full_name"
                               value="<?= e($editUser['full_name'] ?? $_POST['full_name'] ?? '') ?>"
                               class="w-full px-3 py-2 border border-granite-300 rounded-lg focus:ring-2 focus:ring-secondary-500 focus:border-secondary-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-granite-700 mb-1">Nome de Utilizador <span class="text-red-500">*</span></label>
                        <input type="text"
                               name="username"
                               value="<?= e($editUser['username'] ?? $_POST['username'] ?? '') ?>"
                               required
                               minlength="3"
                               class="w-full px-3 py-2 border border-granite-300 rounded-lg focus:ring-2 focus:ring-secondary-500 focus:border-secondary-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-granite-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email"
                               name="email"
                               value="<?= e($editUser['email'] ?? $_POST['email'] ?? '') ?>"
                               required
                               class="w-full px-3 py-2 border border-granite-300 rounded-lg focus:ring-2 focus:ring-secondary-500 focus:border-secondary-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-granite-700 mb-1">
                            Password <?= !$editUser ? '<span class="text-red-500">*</span>' : '' ?>
                        </label>
                        <input type="password"
                               name="password"
                               <?= !$editUser ? 'required' : '' ?>
                               minlength="8"
                               placeholder="<?= $editUser ? 'Deixe vazio para manter' : '' ?>"
                               class="w-full px-3 py-2 border border-granite-300 rounded-lg focus:ring-2 focus:ring-secondary-500 focus:border-secondary-500 outline-none">
                        <p class="text-xs text-granite-400 mt-1">Min. 8 caracteres, 1 maiúscula, 1 número</p>
                    </div>

                    <?php if (!$editUser || $editUser['id'] !== ($_SESSION['admin_id'] ?? 0)): ?>
                    <div>
                        <label class="block text-sm font-medium text-granite-700 mb-1">Role</label>
                        <select name="role"
                                class="w-full px-3 py-2 border border-granite-300 rounded-lg focus:ring-2 focus:ring-secondary-500 focus:border-secondary-500 outline-none">
                            <?php foreach ($roles as $key => $label): ?>
                            <option value="<?= $key ?>" <?= ($editUser['role'] ?? 'editor') === $key ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox"
                                   name="is_active"
                                   value="1"
                                   <?= ($editUser['is_active'] ?? 1) ? 'checked' : '' ?>
                                   class="w-4 h-4 text-secondary-600 border-granite-300 rounded focus:ring-secondary-500">
                            <span class="ml-2 text-sm text-granite-700">Conta ativa</span>
                        </label>
                    </div>
                    <?php endif; ?>

                    <!-- Admin Password Confirmation -->
                    <div class="pt-4 mt-4 border-t border-granite-200">
                        <label class="block text-sm font-medium text-granite-700 mb-1">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                A Sua Password <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <input type="password"
                               name="admin_password"
                               required
                               placeholder="Confirme com a sua password"
                               class="w-full px-3 py-2 border border-granite-300 rounded-lg focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none bg-accent-50/30"
                               <?= $authLocked ? 'disabled' : '' ?>>
                        <p class="text-xs text-granite-400 mt-1">Necessária para confirmar alterações de utilizadores</p>
                    </div>
                </div>

                <div class="mt-6 flex gap-2">
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                            <?= $authLocked ? 'disabled' : '' ?>>
                        <?= $editUser ? 'Guardar' : 'Criar Utilizador' ?>
                    </button>
                    <?php if ($editUser): ?>
                    <a href="<?= basePath() ?>/admin/utilizadores/" class="px-4 py-2 text-granite-600 hover:text-granite-800 border border-granite-300 rounded-lg hover:bg-granite-50 transition-colors">
                        Cancelar
                    </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Roles Info -->
        <div class="bg-granite-50 rounded-lg p-4 mt-4 border border-granite-200">
            <h3 class="text-sm font-semibold text-granite-700 mb-2">Permissões por Role</h3>
            <ul class="text-xs text-granite-600 space-y-1.5">
                <li class="flex items-start gap-2">
                    <span class="inline-flex px-1.5 py-0.5 bg-purple-100 text-purple-800 rounded text-[10px] font-bold mt-0.5">ADM</span>
                    <span>Acesso total incluindo gestão de utilizadores e configurações</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="inline-flex px-1.5 py-0.5 bg-granite-200 text-granite-700 rounded text-[10px] font-bold mt-0.5">EDT</span>
                    <span>Gerir conteúdos, produtos, encomendas, media e faturas</span>
                </li>
            </ul>
        </div>

        <!-- Security Info -->
        <div class="bg-blue-50 rounded-lg p-4 mt-4 border border-blue-200">
            <h3 class="text-sm font-semibold text-blue-800 mb-2 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Segurança
            </h3>
            <ul class="text-xs text-blue-700 space-y-1">
                <li>Criar/editar utilizadores requer a sua password</li>
                <li>Eliminar utilizadores requer confirmação por password</li>
                <li>5 tentativas falhadas bloqueiam ações por 5 minutos</li>
                <li>Todas as ações são registadas em log</li>
            </ul>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[200] hidden">
    <div class="flex items-center justify-center min-h-full p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
            <div class="px-6 pt-6 pb-0">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center bg-red-100">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-granite-800">Eliminar Utilizador</h3>
                </div>
            </div>
            <form method="POST" id="deleteForm">
                <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
                <input type="hidden" name="action" value="delete_user">
                <input type="hidden" name="user_id" id="deleteUserId" value="">

                <div class="px-6 py-4">
                    <p class="text-granite-600 mb-4">
                        Tem a certeza que deseja eliminar <strong id="deleteUserName"></strong>?
                        Esta ação é irreversível.
                    </p>
                    <div>
                        <label class="block text-sm font-medium text-granite-700 mb-1">
                            Confirme com a sua password
                        </label>
                        <input type="password"
                               name="admin_password"
                               id="deleteAdminPassword"
                               required
                               class="w-full px-3 py-2 border border-granite-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
                    </div>
                </div>
                <div class="px-6 pb-6 flex gap-3 justify-end">
                    <button type="button" onclick="hideDeleteModal()"
                            class="px-5 py-2.5 text-granite-600 font-medium rounded-xl border-2 border-granite-200 hover:bg-granite-50 hover:border-granite-300 transition-all">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 font-semibold rounded-xl bg-red-600 text-white hover:bg-red-700 transition-all shadow-sm hover:shadow-md">
                        Eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$pageScripts = <<<'JS'
<script>
function showDeleteModal(userId, userName) {
    document.getElementById('deleteUserId').value = userId;
    document.getElementById('deleteUserName').textContent = userName;
    document.getElementById('deleteAdminPassword').value = '';
    document.getElementById('deleteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    setTimeout(() => document.getElementById('deleteAdminPassword').focus(), 100);
}

function hideDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// Close modal on backdrop click
document.getElementById('deleteModal')?.addEventListener('click', function(e) {
    if (e.target === this || e.target === this.firstElementChild) hideDeleteModal();
});

// Close modal on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('deleteModal').classList.contains('hidden')) {
        hideDeleteModal();
    }
});
</script>
JS;
?>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
