<?php
/**
 * A Casa do Gi - Admin Profile
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;
use Core\Validator;
use Core\Auth;

$db = Database::getInstance();
$admin = Auth::user();

$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token de segurança inválido.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'update_profile') {
            // Update Profile
            $data = [
                'full_name' => sanitize($_POST['full_name'] ?? ''),
                'email' => sanitize($_POST['email'] ?? ''),
                'username' => sanitize($_POST['username'] ?? '')
            ];

            $validator = new Validator();
            $validator->required($data['full_name'], 'full_name', 'Nome Completo');
            $validator->required($data['email'], 'email', 'Email');
            $validator->email($data['email'], 'email', 'Email inválido');
            $validator->required($data['username'], 'username', 'Nome de Utilizador');

            // Check uniqueness if changed
            if ($data['email'] !== $admin->email) {
                $exists = $db->fetch("SELECT id FROM admins WHERE email = ? AND id != ?", [$data['email'], $admin->id]);
                if ($exists) $validator->addError('email', 'Este email já está em uso.');
            }
            if ($data['username'] !== $admin->username) {
                $exists = $db->fetch("SELECT id FROM admins WHERE username = ? AND id != ?", [$data['username'], $admin->id]);
                if ($exists) $validator->addError('username', 'Este nome de utilizador já está em uso.');
            }

            $errors = $validator->getErrors();

            if (empty($errors)) {
                $db->update('admins', $data, 'id = ?', [$admin->id]);
                Session::flash('success', 'Perfil atualizado com sucesso.');
                // Refresh admin object
                $admin = Auth::user(); 
                redirect('/admin/perfil/');
            }

        } elseif ($action === 'change_password') {
            // Change Password
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            $validator = new Validator();
            $validator->required($currentPassword, 'current_password', 'Password Atual');
            $validator->required($newPassword, 'new_password', 'Nova Password');
            $validator->required($confirmPassword, 'confirm_password', 'Confirmar Password');

            if (!empty($newPassword)) {
                if (strlen($newPassword) < 8) {
                    $validator->addError('new_password', 'A nova password deve ter pelo menos 8 caracteres.');
                }
                if ($newPassword !== $confirmPassword) {
                    $validator->addError('confirm_password', 'As passwords não coincidem.');
                }
            }

            // Verify current password
            if (!password_verify($currentPassword, $admin->password_hash)) {
                $validator->addError('current_password', 'A password atual está incorreta.');
            }

            $errors = $validator->getErrors();

            if (empty($errors)) {
                $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $db->update('admins', ['password_hash' => $newHash], 'id = ?', [$admin->id]);
                Session::flash('success', 'Password alterada com sucesso.');
                redirect('/admin/perfil/');
            }
        }
    }
}

$pageTitle = 'O Meu Perfil';
$currentPage = 'perfil';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">O Meu Perfil</h1>
    <p class="text-gray-600">Gerir informações da conta e segurança</p>
</div>

<div class="grid lg:grid-cols-2 gap-8">
    <!-- Profile Info -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-medium text-gray-800 mb-4 border-b pb-2">Informações Pessoais</h2>

        <form action="" method="post">
            <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
            <input type="hidden" name="action" value="update_profile">

            <?php if (!empty($errors) && $_POST['action'] === 'update_profile'): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                    <li><?= is_array($error) ? implode(', ', $error) : e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
                    <input type="text" name="full_name" value="<?= e($_POST['full_name'] ?? $admin->full_name) ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-olive-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="<?= e($_POST['email'] ?? $admin->email) ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-olive-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome de Utilizador</label>
                    <input type="text" name="username" value="<?= e($_POST['username'] ?? $admin->username) ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-olive-500">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full px-4 py-2 bg-olive-600 text-white rounded-lg hover:bg-olive-700">
                        Atualizar Perfil
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Password Change -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-medium text-gray-800 mb-4 border-b pb-2">Alterar Password</h2>

        <form action="" method="post">
            <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
            <input type="hidden" name="action" value="change_password">

            <?php if (!empty($errors) && $_POST['action'] === 'change_password'): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                    <li><?= is_array($error) ? implode(', ', $error) : e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Atual</label>
                    <input type="password" name="current_password" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-olive-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nova Password</label>
                    <input type="password" name="new_password" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-olive-500">
                    <p class="text-xs text-gray-500 mt-1">Mínimo 8 caracteres</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nova Password</label>
                    <input type="password" name="confirm_password" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-olive-500">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        Alterar Password
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
