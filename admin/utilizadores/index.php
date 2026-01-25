<?php
/**
 * A Casa do Gi - Admin Users Management
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;
use Core\Auth;

// Check permissions
if (!Auth::canManageUsers()) {
    Session::flash('error', 'Sem permissoes para aceder a esta pagina.');
    redirect('/admin/');
}

$db = Database::getInstance();

// Handle delete
if (isset($_GET['delete']) && isset($_GET['token'])) {
    if (CSRF::validate($_GET['token'])) {
        $id = (int)$_GET['delete'];

        // Cannot delete yourself
        if ($id === ($_SESSION['admin_id'] ?? 0)) {
            Session::flash('error', 'Nao pode eliminar a sua propria conta.');
        } else {
            $db->delete('admins', 'id = ?', [$id]);
            Session::flash('success', 'Utilizador eliminado.');
        }
    }
    redirect('/admin/utilizadores/');
}

// Handle toggle active
if (isset($_GET['toggle']) && isset($_GET['token'])) {
    if (CSRF::validate($_GET['token'])) {
        $id = (int)$_GET['toggle'];

        // Cannot deactivate yourself
        if ($id === ($_SESSION['admin_id'] ?? 0)) {
            Session::flash('error', 'Nao pode desativar a sua propria conta.');
        } else {
            $db->query("UPDATE admins SET is_active = NOT is_active WHERE id = ?", [$id]);
            Session::flash('success', 'Estado atualizado.');
        }
    }
    redirect('/admin/utilizadores/');
}

// Handle form submission (create/edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        $errors = [];
        $editId = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;

        $username = sanitize($_POST['username'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $fullName = sanitize($_POST['full_name'] ?? '');
        $role = $_POST['role'] ?? 'editor';
        $password = $_POST['password'] ?? '';
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        // Validation
        if (empty($username)) {
            $errors[] = 'O nome de utilizador e obrigatorio.';
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalido.';
        }
        if (!$editId && empty($password)) {
            $errors[] = 'A password e obrigatoria.';
        }
        if ($password && strlen($password) < 6) {
            $errors[] = 'A password deve ter pelo menos 6 caracteres.';
        }
        if (!in_array($role, ['admin', 'editor'])) {
            $errors[] = 'Role invalido.';
        }

        // Check for duplicates
        $checkWhere = $editId ? "AND id != ?" : "";
        $checkParams = $editId ? [$username, $editId] : [$username];
        $existingUsername = $db->fetch("SELECT id FROM admins WHERE username = ? {$checkWhere}", $checkParams);
        if ($existingUsername) {
            $errors[] = 'Este nome de utilizador ja existe.';
        }

        $checkParams = $editId ? [$email, $editId] : [$email];
        $existingEmail = $db->fetch("SELECT id FROM admins WHERE email = ? {$checkWhere}", $checkParams);
        if ($existingEmail) {
            $errors[] = 'Este email ja esta registado.';
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
                $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
            }

            if ($editId) {
                // Cannot change your own role
                if ($editId === ($_SESSION['admin_id'] ?? 0)) {
                    unset($data['role']);
                    unset($data['is_active']);
                }
                $db->update('admins', $data, 'id = ?', [$editId]);
                Session::flash('success', 'Utilizador atualizado.');
            } else {
                $db->insert('admins', $data);
                Session::flash('success', 'Utilizador criado.');
            }
            redirect('/admin/utilizadores/');
        } else {
            Session::flash('error', implode('<br>', $errors));
        }
    }
}

// Get editing user
$editUser = null;
if (isset($_GET['edit'])) {
    $editUser = $db->fetch("SELECT * FROM admins WHERE id = ?", [(int)$_GET['edit']]);
}

// Get all users
$users = $db->fetchAll("SELECT * FROM admins ORDER BY role, full_name");

// Roles
$roles = [
    'admin' => 'Administrador',
    'editor' => 'Editor'
];

$pageTitle = 'Utilizadores';
$currentPage = 'utilizadores';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Utilizadores</h1>
        <p class="text-gray-600">Gerir utilizadores do back-office</p>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <!-- Users List -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilizador</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acoes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-gray-50 <?= $user['id'] === ($_SESSION['admin_id'] ?? 0) ? 'bg-secondary-50' : '' ?>">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-secondary-100 flex items-center justify-center text-secondary-600 font-semibold mr-3">
                                    <?= strtoupper(substr($user['full_name'] ?: $user['username'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= e($user['full_name'] ?: $user['username']) ?>
                                        <?php if ($user['id'] === ($_SESSION['admin_id'] ?? 0)): ?>
                                        <span class="text-xs text-secondary-600">(voce)</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-xs text-gray-500"><?= e($user['email']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded <?= $user['role'] === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' ?>">
                                <?= $roles[$user['role']] ?? $user['role'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <?php if ($user['id'] !== ($_SESSION['admin_id'] ?? 0)): ?>
                            <a href="?toggle=<?= $user['id'] ?>&token=<?= CSRF::getToken() ?>"
                               class="inline-flex px-2 py-1 text-xs font-medium rounded <?= $user['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' ?>">
                                <?= $user['is_active'] ? 'Ativo' : 'Inativo' ?>
                            </a>
                            <?php else: ?>
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-800">
                                Ativo
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="?edit=<?= $user['id'] ?>"
                               class="text-secondary-600 hover:text-olive-800 text-sm mr-3">Editar</a>
                            <?php if ($user['id'] !== ($_SESSION['admin_id'] ?? 0)): ?>
                            <a href="?delete=<?= $user['id'] ?>&token=<?= CSRF::getToken() ?>"
                               onclick="return confirm('Eliminar este utilizador?')"
                               class="text-red-600 hover:text-red-800 text-sm">Eliminar</a>
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
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-medium text-gray-800 mb-4">
                <?= $editUser ? 'Editar Utilizador' : 'Novo Utilizador' ?>
            </h2>

            <form action="" method="post">
                <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
                <?php if ($editUser): ?>
                <input type="hidden" name="edit_id" value="<?= $editUser['id'] ?>">
                <?php endif; ?>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
                        <input type="text"
                               name="full_name"
                               value="<?= e($editUser['full_name'] ?? $_POST['full_name'] ?? '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome de Utilizador <span class="text-red-500">*</span></label>
                        <input type="text"
                               name="username"
                               value="<?= e($editUser['username'] ?? $_POST['username'] ?? '') ?>"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email"
                               name="email"
                               value="<?= e($editUser['email'] ?? $_POST['email'] ?? '') ?>"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Password <?= !$editUser ? '<span class="text-red-500">*</span>' : '' ?>
                        </label>
                        <input type="password"
                               name="password"
                               <?= !$editUser ? 'required' : '' ?>
                               placeholder="<?= $editUser ? 'Deixe vazio para manter' : '' ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                        <?php if ($editUser): ?>
                        <p class="text-xs text-gray-500 mt-1">Deixe vazio para manter a password atual.</p>
                        <?php endif; ?>
                    </div>

                    <?php if (!$editUser || $editUser['id'] !== ($_SESSION['admin_id'] ?? 0)): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select name="role"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
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
                                   class="w-4 h-4 text-secondary-600 border-gray-300 rounded focus:ring-secondary-500">
                            <span class="ml-2 text-sm text-gray-700">Conta ativa</span>
                        </label>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="mt-6 flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700">
                        <?= $editUser ? 'Guardar' : 'Criar' ?>
                    </button>
                    <?php if ($editUser): ?>
                    <a href="<?= basePath() ?>/admin/utilizadores/" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                        Cancelar
                    </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Roles Info -->
        <div class="bg-gray-50 rounded-lg p-4 mt-4">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Permissoes por Role</h3>
            <ul class="text-xs text-gray-600 space-y-1">
                <li><strong>Administrador:</strong> Acesso total, incluindo gestao de utilizadores</li>
                <li><strong>Editor:</strong> Gerir conteudos, produtos, encomendas e media</li>
            </ul>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
