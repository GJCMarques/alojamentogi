<?php

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;
use Core\Auth;
use Core\Encryption;

if (!Auth::canManageUsers()) {
    Session::flash('error', 'Sem permissões para aceder às configurações.');
    redirect('/admin/');
}

$db = Database::getInstance();

$settingsGroups = [
    'general' => [
        'label' => 'Geral',
        'description' => 'Informações básicas do website',
        'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
        'settings' => [
            ['key' => 'site_name', 'label' => 'Nome do Site', 'type' => 'text', 'default' => 'A Casa do Gi'],
            ['key' => 'site_tagline_pt', 'label' => 'Slogan (PT)', 'type' => 'text', 'default' => ''],
            ['key' => 'site_tagline_en', 'label' => 'Slogan (EN)', 'type' => 'text', 'default' => ''],
            ['key' => 'site_description', 'label' => 'Descrição (SEO)', 'type' => 'textarea', 'default' => ''],
            ['key' => 'maintenance_mode', 'label' => 'Modo Manutenção', 'type' => 'boolean', 'default' => '0', 'hint' => 'Ativa uma página de manutenção para visitantes'],
        ]
    ],
    'contact' => [
        'label' => 'Contacto',
        'description' => 'Informações de contacto exibidas no website',
        'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        'settings' => [
            ['key' => 'contact_email', 'label' => 'Email', 'type' => 'email', 'default' => ''],
            ['key' => 'contact_phone', 'label' => 'Telefone', 'type' => 'text', 'default' => ''],
            ['key' => 'contact_address', 'label' => 'Morada', 'type' => 'textarea', 'default' => ''],
            ['key' => 'google_maps_url', 'label' => 'Google Maps URL', 'type' => 'url', 'default' => '', 'hint' => 'Link do Google Maps para o mapa de contactos'],
            ['key' => 'contact_form_enabled', 'label' => 'Formulário de Contacto', 'type' => 'boolean', 'default' => '1', 'hint' => 'Permite que visitantes enviem mensagens pelo formulário'],
        ]
    ],
    'social' => [
        'label' => 'Redes Sociais',
        'description' => 'Links das redes sociais no footer e página de contactos',
        'icon' => 'M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z',
        'settings' => [
            ['key' => 'facebook_url', 'label' => 'Facebook URL', 'type' => 'url', 'default' => ''],
            ['key' => 'instagram_url', 'label' => 'Instagram URL', 'type' => 'url', 'default' => ''],
            ['key' => 'tripadvisor_url', 'label' => 'TripAdvisor URL', 'type' => 'url', 'default' => ''],
        ]
    ],
    'email' => [
        'label' => 'Email (SMTP)',
        'description' => 'Configuração de envio de emails',
        'icon' => 'M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207',
        'settings' => [
            ['key' => 'smtp_host', 'label' => 'SMTP Host', 'type' => 'text', 'default' => '', 'hint' => 'Ex: smtp.gmail.com'],
            ['key' => 'smtp_port', 'label' => 'SMTP Port', 'type' => 'number', 'default' => '587'],
            ['key' => 'smtp_user', 'label' => 'SMTP Usuário', 'type' => 'text', 'default' => ''],
            ['key' => 'smtp_pass', 'label' => 'SMTP Password', 'type' => 'password', 'default' => ''],
            ['key' => 'smtp_from_email', 'label' => 'Email Remetente', 'type' => 'email', 'default' => '', 'hint' => 'O email que aparece como remetente'],
            ['key' => 'smtp_from_name', 'label' => 'Nome Remetente', 'type' => 'text', 'default' => 'A Casa do Gi'],
        ]
    ],
];

$currentGroup = isset($_GET['group']) && isset($settingsGroups[$_GET['group']])
    ? $_GET['group']
    : 'general';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        $group = $settingsGroups[$currentGroup];

        foreach ($group['settings'] as $setting) {
            $key = $setting['key'];

            if ($setting['type'] === 'readonly') continue;

            $value = '';
            if ($setting['type'] === 'boolean') {
                $value = isset($_POST[$key]) ? '1' : '0';
            } elseif ($setting['type'] === 'password') {

                $value = $_POST[$key] ?? '';
                if (empty($value)) continue;
            } else {
                $value = sanitize($_POST[$key] ?? '');
            }

            if (Encryption::isSensitive($key) && !empty($value)) {
                $value = Encryption::encrypt($value);
            }

            $existing = $db->fetch("SELECT id FROM settings WHERE setting_key = ?", [$key]);

            if ($existing) {
                $db->update('settings', ['setting_value' => $value], 'setting_key = ?', [$key]);
            } else {
                $db->insert('settings', [
                    'setting_key' => $key,
                    'setting_value' => $value,
                    'setting_type' => $setting['type'] === 'boolean' ? 'boolean' : ($setting['type'] === 'number' ? 'number' : 'text'),
                    'setting_group' => $currentGroup,
                    'description' => $setting['label'],
                ]);
            }
        }

        $admin = Auth::user();
        logMessage("Admin {$admin->username} updated settings group: {$currentGroup}", 'info');
        Session::flash('success', 'Configurações guardadas com sucesso.');
        redirect('/admin/configuracoes/?group=' . $currentGroup);
    }
}

$allSettings = [];
$settingsRows = $db->fetchAll("SELECT setting_key, setting_value FROM settings");
foreach ($settingsRows as $row) {
    $allSettings[$row['setting_key']] = $row['setting_value'];
}

$pageTitle = 'Configurações';
$currentPage = 'configuracoes';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-primary">Configurações</h1>
        <p class="text-granite-500 text-sm">Configurações gerais do website</p>
    </div>
</div>

<div class="flex gap-6">
    <!-- Sidebar -->
    <div class="w-64 flex-shrink-0">
        <nav class="bg-white rounded-lg shadow-sm overflow-hidden border border-granite-200">
            <?php foreach ($settingsGroups as $key => $group): ?>
            <a href="?group=<?= $key ?>"
               class="flex items-center px-4 py-3 text-sm font-medium border-b border-granite-100 last:border-0
                      <?= $currentGroup === $key
                          ? 'bg-secondary-50 text-secondary-700 border-l-4 border-l-secondary-600'
                          : 'text-granite-600 hover:bg-granite-50' ?>">
                <svg class="w-5 h-5 mr-3 <?= $currentGroup === $key ? 'text-secondary-600' : 'text-granite-400' ?>"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="<?= $group['icon'] ?>"/>
                </svg>
                <?= $group['label'] ?>
            </a>
            <?php endforeach; ?>
        </nav>

        <!-- Info note -->
        <div class="bg-blue-50 rounded-lg p-4 mt-4 border border-blue-200">
            <p class="text-xs text-blue-700 leading-relaxed">
                <strong>Nota:</strong> O link de reservas (GuestReady) e os dados de cada casa são geridos na página
                <a href="<?= basePath() ?>/admin/alojamento/" class="underline hover:text-blue-900">Alojamento</a>.
                A loja online é gerida externamente (shopk.it).
            </p>
        </div>
    </div>

    <!-- Content -->
    <div class="flex-1">
        <div class="bg-white rounded-lg shadow-sm border border-granite-200">
            <div class="px-6 py-4 border-b border-granite-200">
                <h2 class="text-lg font-semibold text-granite-800"><?= $settingsGroups[$currentGroup]['label'] ?></h2>
                <p class="text-xs text-granite-400 mt-0.5"><?= $settingsGroups[$currentGroup]['description'] ?></p>
            </div>

            <form action="" method="post" class="p-6">
                <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">

                <div class="space-y-6">
                    <?php foreach ($settingsGroups[$currentGroup]['settings'] as $setting): ?>
                    <div>
                        <label class="block text-sm font-medium text-granite-700 mb-1">
                            <?= $setting['label'] ?>
                        </label>

                        <?php
                        $value = $allSettings[$setting['key']] ?? $setting['default'];

                        switch ($setting['type']):
                            case 'text':
                            case 'email':
                            case 'url':
                            case 'number':
                        ?>
                        <input type="<?= $setting['type'] ?>"
                               name="<?= $setting['key'] ?>"
                               value="<?= e($value) ?>"
                               <?= $setting['type'] === 'number' ? 'step="0.01" min="0"' : '' ?>
                               class="w-full px-3 py-2 border border-granite-300 rounded-lg focus:ring-2 focus:ring-secondary-500 focus:border-secondary-500 outline-none">
                        <?php break; case 'password': ?>
                        <input type="password"
                               name="<?= $setting['key'] ?>"
                               value=""
                               placeholder="<?= $value ? '••••••••  (deixe vazio para manter)' : '' ?>"
                               class="w-full px-3 py-2 border border-granite-300 rounded-lg focus:ring-2 focus:ring-secondary-500 focus:border-secondary-500 outline-none">
                        <?php break; case 'textarea': ?>
                        <textarea name="<?= $setting['key'] ?>"
                                  rows="3"
                                  class="w-full px-3 py-2 border border-granite-300 rounded-lg focus:ring-2 focus:ring-secondary-500 focus:border-secondary-500 outline-none"><?= e($value) ?></textarea>
                        <?php break; case 'boolean': ?>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox"
                                   name="<?= $setting['key'] ?>"
                                   value="1"
                                   <?= $value === '1' ? 'checked' : '' ?>
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-granite-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-granite-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-secondary-600"></div>
                            <span class="ml-3 text-sm text-granite-600"><?= $value === '1' ? 'Ativo' : 'Inativo' ?></span>
                        </label>
                        <?php break; case 'readonly': ?>
                        <div class="flex items-center gap-2">
                            <input type="text"
                                   value="<?= e($value) ?>"
                                   readonly
                                   class="flex-1 px-3 py-2 bg-granite-50 border border-granite-300 rounded-lg text-granite-500 text-sm font-mono">
                            <button type="button"
                                    onclick="navigator.clipboard.writeText(this.previousElementSibling.value); this.textContent='Copiado!'; this.classList.add('bg-green-100','text-green-700'); setTimeout(() => { this.textContent='Copiar'; this.classList.remove('bg-green-100','text-green-700'); }, 2000);"
                                    class="px-3 py-2 text-sm bg-granite-100 text-granite-600 rounded-lg hover:bg-granite-200 transition-colors">
                                Copiar
                            </button>
                        </div>
                        <?php break; endswitch; ?>

                        <?php if (!empty($setting['hint'])): ?>
                        <p class="text-xs text-granite-400 mt-1"><?= $setting['hint'] ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-6 pt-6 border-t border-granite-200">
                    <button type="submit" class="px-6 py-2.5 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700 transition-colors font-medium">
                        Guardar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
