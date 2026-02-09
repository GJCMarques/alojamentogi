<?php
/**
 * A Casa do Gi - Admin Profile
 *
 * Security: Rate limited password changes, requires current password
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
$rateLimiter = \Core\RateLimiter::getInstance();

$errors = [];
$profileErrors = [];
$passwordErrors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Rate limit: 15 profile actions per 5 min
    if (!$rateLimiter->check('admin_profile_update', 15, 300)) {
        Session::flash('error', 'Demasiadas alteracoes. Aguarde alguns minutos.');
        redirect('/admin/perfil/');
    }

    if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
        Session::flash('error', 'Token de seguranca invalido.');
        redirect('/admin/perfil/');
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $data = [
            'full_name' => sanitize($_POST['full_name'] ?? ''),
            'email' => sanitize($_POST['email'] ?? ''),
            'username' => sanitize($_POST['username'] ?? '')
        ];

        $validator = new Validator();
        $validator->required($data['full_name'], 'full_name', 'Nome Completo');
        $validator->required($data['email'], 'email', 'Email');
        $validator->email($data['email'], 'email', 'Email invalido');
        $validator->required($data['username'], 'username', 'Nome de Utilizador');

        if (strlen($data['username']) < 3) {
            $validator->addError('username', 'O nome de utilizador deve ter pelo menos 3 caracteres.');
        }

        // Check uniqueness if changed
        if ($data['email'] !== $admin->email) {
            $exists = $db->fetch("SELECT id FROM admins WHERE email = ? AND id != ?", [$data['email'], $admin->id]);
            if ($exists) $validator->addError('email', 'Este email ja esta em uso.');
        }
        if ($data['username'] !== $admin->username) {
            $exists = $db->fetch("SELECT id FROM admins WHERE username = ? AND id != ?", [$data['username'], $admin->id]);
            if ($exists) $validator->addError('username', 'Este nome de utilizador ja esta em uso.');
        }

        $profileErrors = $validator->getErrors();

        if (empty($profileErrors)) {
            $db->update('admins', $data, 'id = ?', [$admin->id]);
            Session::flash('success', 'Perfil atualizado com sucesso.');
            logMessage("Admin {$admin->username} updated their profile", 'info');
            redirect('/admin/perfil/');
        }

    } elseif ($action === 'change_password') {
        // Rate limit password changes specifically: 5 per 15 min
        if (!$rateLimiter->check('admin_password_change', 5, 900)) {
            Session::flash('error', 'Demasiadas tentativas de alteracao de password. Aguarde 15 minutos.');
            redirect('/admin/perfil/');
        }

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
            if (!preg_match('/[A-Z]/', $newPassword)) {
                $validator->addError('new_password', 'A password deve conter pelo menos uma letra maiuscula.');
            }
            if (!preg_match('/[0-9]/', $newPassword)) {
                $validator->addError('new_password', 'A password deve conter pelo menos um numero.');
            }
            if ($newPassword !== $confirmPassword) {
                $validator->addError('confirm_password', 'As passwords nao coincidem.');
            }
        }

        // Verify current password
        if (!password_verify($currentPassword, $admin->password_hash)) {
            $validator->addError('current_password', 'A password atual esta incorreta.');
            $rateLimiter->recordFailure('admin_password_verify');
            logMessage("Failed password verification for profile change by admin ID {$admin->id}", 'warning');
        }

        $passwordErrors = $validator->getErrors();

        if (empty($passwordErrors)) {
            $newHash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
            $db->update('admins', ['password_hash' => $newHash], 'id = ?', [$admin->id]);
            Session::flash('success', 'Password alterada com sucesso.');
            logMessage("Admin {$admin->username} changed their password", 'info');
            redirect('/admin/perfil/');
        }
    }
}

// Refresh admin data
$admin = Auth::user();

$pageTitle = 'O Meu Perfil';
$currentPage = 'perfil';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-granite-800">O Meu Perfil</h1>
    <p class="text-granite-500 text-sm">Gerir informacoes da conta e seguranca</p>
</div>

<!-- Account Overview Card -->
<div class="bg-white rounded-lg shadow-sm p-6 border border-granite-200 mb-8">
    <div class="flex items-center gap-6">
        <div class="w-16 h-16 rounded-full bg-secondary-100 flex items-center justify-center text-secondary-600 text-2xl font-bold flex-shrink-0">
            <?= strtoupper(substr($admin->full_name ?: $admin->username, 0, 1)) ?>
        </div>
        <div class="flex-1">
            <h2 class="text-lg font-semibold text-granite-800"><?= e($admin->full_name ?: $admin->username) ?></h2>
            <p class="text-sm text-granite-500"><?= e($admin->email) ?></p>
        </div>
        <div class="text-right text-sm space-y-1">
            <div class="flex items-center gap-2 justify-end">
                <span class="inline-flex px-2 py-1 text-xs font-medium rounded <?= $admin->role === 'admin' || $admin->role === 'super_admin' ? 'bg-purple-100 text-purple-800' : 'bg-granite-100 text-granite-800' ?>">
                    <?= $admin->role === 'super_admin' ? 'Super Admin' : ($admin->role === 'admin' ? 'Administrador' : 'Editor') ?>
                </span>
            </div>
            <?php if ($admin->last_login): ?>
            <p class="text-granite-400 text-xs">Ultimo login: <?= timeAgo($admin->last_login) ?></p>
            <?php endif; ?>
            <p class="text-granite-400 text-xs">Conta criada: <?= date('d/m/Y', strtotime($admin->created_at)) ?></p>
        </div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-8">
    <!-- Profile Info -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-granite-200">
        <h2 class="text-lg font-semibold text-granite-800 mb-1">Informacoes Pessoais</h2>
        <p class="text-xs text-granite-400 mb-6">Atualize o seu nome, email e nome de utilizador</p>

        <form action="" method="post">
            <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
            <input type="hidden" name="action" value="update_profile">

            <?php if (!empty($profileErrors)): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
                <ul class="list-disc list-inside text-sm">
                    <?php foreach ($profileErrors as $error): ?>
                    <li><?= is_array($error) ? implode(', ', $error) : e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-granite-700 mb-1">Nome Completo</label>
                    <input type="text" name="full_name"
                           value="<?= e($_POST['full_name'] ?? $admin->full_name) ?>"
                           class="w-full px-3 py-2 border border-granite-300 rounded-lg focus:ring-2 focus:ring-secondary-500 focus:border-secondary-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-granite-700 mb-1">Email</label>
                    <input type="email" name="email"
                           value="<?= e($_POST['email'] ?? $admin->email) ?>"
                           class="w-full px-3 py-2 border border-granite-300 rounded-lg focus:ring-2 focus:ring-secondary-500 focus:border-secondary-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-granite-700 mb-1">Nome de Utilizador</label>
                    <input type="text" name="username"
                           value="<?= e($_POST['username'] ?? $admin->username) ?>"
                           minlength="3"
                           class="w-full px-3 py-2 border border-granite-300 rounded-lg focus:ring-2 focus:ring-secondary-500 focus:border-secondary-500 outline-none">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full px-4 py-2.5 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700 transition-colors font-medium">
                        Atualizar Perfil
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Password Change -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-granite-200">
        <h2 class="text-lg font-semibold text-granite-800 mb-1">Alterar Password</h2>
        <p class="text-xs text-granite-400 mb-6">Utilize uma password forte e unica</p>

        <form action="" method="post">
            <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
            <input type="hidden" name="action" value="change_password">

            <?php if (!empty($passwordErrors)): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
                <ul class="list-disc list-inside text-sm">
                    <?php foreach ($passwordErrors as $error): ?>
                    <li><?= is_array($error) ? implode(', ', $error) : e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-granite-700 mb-1">Password Atual</label>
                    <input type="password" name="current_password" required
                           class="w-full px-3 py-2 border border-granite-300 rounded-lg focus:ring-2 focus:ring-secondary-500 focus:border-secondary-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-granite-700 mb-1">Nova Password</label>
                    <input type="password" name="new_password" required id="newPassword"
                           minlength="8"
                           class="w-full px-3 py-2 border border-granite-300 rounded-lg focus:ring-2 focus:ring-secondary-500 focus:border-secondary-500 outline-none">
                    <!-- Password Strength Indicator -->
                    <div class="mt-2 space-y-1" id="passwordStrength" style="display:none">
                        <div class="flex gap-1">
                            <div class="h-1 flex-1 rounded-full bg-granite-200" id="str1"></div>
                            <div class="h-1 flex-1 rounded-full bg-granite-200" id="str2"></div>
                            <div class="h-1 flex-1 rounded-full bg-granite-200" id="str3"></div>
                            <div class="h-1 flex-1 rounded-full bg-granite-200" id="str4"></div>
                        </div>
                        <p class="text-xs text-granite-400" id="strengthText"></p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-granite-700 mb-1">Confirmar Nova Password</label>
                    <input type="password" name="confirm_password" required
                           class="w-full px-3 py-2 border border-granite-300 rounded-lg focus:ring-2 focus:ring-secondary-500 focus:border-secondary-500 outline-none">
                </div>

                <!-- Requirements -->
                <div class="bg-granite-50 rounded-lg p-3 text-xs text-granite-500 space-y-1">
                    <p class="font-medium text-granite-600 mb-1">Requisitos da password:</p>
                    <p id="req-length" class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full border border-granite-300 flex items-center justify-center text-[8px]"></span>
                        Minimo 8 caracteres
                    </p>
                    <p id="req-upper" class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full border border-granite-300 flex items-center justify-center text-[8px]"></span>
                        Pelo menos 1 letra maiuscula
                    </p>
                    <p id="req-number" class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full border border-granite-300 flex items-center justify-center text-[8px]"></span>
                        Pelo menos 1 numero
                    </p>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full px-4 py-2.5 bg-primary text-cream rounded-lg hover:bg-primary-700 transition-colors font-medium">
                        Alterar Password
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Session Info -->
<div class="mt-8 bg-white rounded-lg shadow-sm p-6 border border-granite-200">
    <h2 class="text-lg font-semibold text-granite-800 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-granite-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
        Seguranca da Conta
    </h2>
    <div class="grid md:grid-cols-3 gap-6">
        <div>
            <p class="text-xs text-granite-500 uppercase tracking-wider font-medium mb-1">Sessao Atual</p>
            <p class="text-sm text-granite-700">Ativa</p>
            <p class="text-xs text-granite-400 mt-0.5">CSRF token renova a cada hora</p>
        </div>
        <div>
            <p class="text-xs text-granite-500 uppercase tracking-wider font-medium mb-1">Nivel de Acesso</p>
            <p class="text-sm text-granite-700"><?= $admin->role === 'super_admin' ? 'Super Administrador' : ($admin->role === 'admin' ? 'Administrador' : 'Editor') ?></p>
            <p class="text-xs text-granite-400 mt-0.5">
                <?= $admin->role === 'editor' ? 'Pode gerir conteudos e produtos' : 'Acesso total ao painel' ?>
            </p>
        </div>
        <div>
            <p class="text-xs text-granite-500 uppercase tracking-wider font-medium mb-1">Acoes</p>
            <a href="<?= basePath() ?>/admin/logout.php" class="inline-flex items-center text-sm text-red-600 hover:text-red-800 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Terminar Sessao
            </a>
        </div>
    </div>
</div>

<?php
$pageScripts = <<<'JS'
<script>
// Password strength indicator
document.getElementById('newPassword')?.addEventListener('input', function() {
    const pw = this.value;
    const panel = document.getElementById('passwordStrength');
    const bars = [document.getElementById('str1'), document.getElementById('str2'), document.getElementById('str3'), document.getElementById('str4')];
    const text = document.getElementById('strengthText');

    if (pw.length === 0) { panel.style.display = 'none'; return; }
    panel.style.display = 'block';

    let score = 0;
    if (pw.length >= 8) score++;
    if (/[A-Z]/.test(pw)) score++;
    if (/[0-9]/.test(pw)) score++;
    if (/[^A-Za-z0-9]/.test(pw)) score++;

    const colors = ['bg-red-400', 'bg-yellow-400', 'bg-blue-400', 'bg-green-400'];
    const labels = ['Fraca', 'Razoavel', 'Boa', 'Forte'];

    bars.forEach((bar, i) => {
        bar.className = 'h-1 flex-1 rounded-full ' + (i < score ? colors[score - 1] : 'bg-granite-200');
    });
    text.textContent = labels[score - 1] || '';

    // Update requirements
    const check = '<span class="w-3 h-3 rounded-full bg-green-500 flex items-center justify-center text-white text-[8px]">✓</span>';
    const uncheck = '<span class="w-3 h-3 rounded-full border border-granite-300 flex items-center justify-center text-[8px]"></span>';

    document.querySelector('#req-length span').outerHTML = pw.length >= 8 ? check : uncheck;
    document.querySelector('#req-upper span').outerHTML = /[A-Z]/.test(pw) ? check : uncheck;
    document.querySelector('#req-number span').outerHTML = /[0-9]/.test(pw) ? check : uncheck;
});
</script>
JS;
?>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
