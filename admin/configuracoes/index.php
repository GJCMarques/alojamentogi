<?php
/**
 * A Casa do Gi - Admin Settings
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;

$db = Database::getInstance();

// Settings groups
$settingsGroups = [
    'general' => [
        'label' => 'Geral',
        'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
        'settings' => [
            ['key' => 'site_name', 'label' => 'Nome do Site', 'type' => 'text', 'default' => 'A Casa do Gi'],
            ['key' => 'site_tagline', 'label' => 'Slogan', 'type' => 'text', 'default' => ''],
            ['key' => 'site_description', 'label' => 'Descricao (SEO)', 'type' => 'textarea', 'default' => ''],
            ['key' => 'maintenance_mode', 'label' => 'Modo Manutencao', 'type' => 'boolean', 'default' => '0'],
        ]
    ],
    'contact' => [
        'label' => 'Contacto',
        'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        'settings' => [
            ['key' => 'contact_email', 'label' => 'Email', 'type' => 'email', 'default' => ''],
            ['key' => 'contact_phone', 'label' => 'Telefone', 'type' => 'text', 'default' => ''],
            ['key' => 'contact_address', 'label' => 'Morada', 'type' => 'textarea', 'default' => ''],
            ['key' => 'google_maps_url', 'label' => 'Google Maps URL', 'type' => 'url', 'default' => ''],
        ]
    ],
    'social' => [
        'label' => 'Redes Sociais',
        'icon' => 'M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z',
        'settings' => [
            ['key' => 'facebook_url', 'label' => 'Facebook URL', 'type' => 'url', 'default' => ''],
            ['key' => 'instagram_url', 'label' => 'Instagram URL', 'type' => 'url', 'default' => ''],
            ['key' => 'tripadvisor_url', 'label' => 'TripAdvisor URL', 'type' => 'url', 'default' => ''],
        ]
    ],
    'booking' => [
        'label' => 'Reservas',
        'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        'settings' => [
            ['key' => 'booking_url', 'label' => 'Booking.com URL', 'type' => 'url', 'default' => ''],
            ['key' => 'airbnb_url', 'label' => 'Airbnb URL', 'type' => 'url', 'default' => ''],
            ['key' => 'guestready_url', 'label' => 'GuestReady URL', 'type' => 'url', 'default' => ''],
        ]
    ],
    'shop' => [
        'label' => 'Loja',
        'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
        'settings' => [
            ['key' => 'shop_enabled', 'label' => 'Loja Ativa', 'type' => 'boolean', 'default' => '1'],
            ['key' => 'shop_min_order', 'label' => 'Encomenda Minima (EUR)', 'type' => 'number', 'default' => '0'],
            ['key' => 'shop_free_shipping_min', 'label' => 'Portes Gratis a partir de (EUR)', 'type' => 'number', 'default' => '50'],
            ['key' => 'shop_default_shipping', 'label' => 'Portes por Defeito (EUR)', 'type' => 'number', 'default' => '5'],
        ]
    ],
    'payment' => [
        'label' => 'Pagamentos',
        'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
        'settings' => [
            ['key' => 'ifthenpay_enabled', 'label' => 'IfthenPay Ativo', 'type' => 'boolean', 'default' => '0'],
            ['key' => 'ifthenpay_entity', 'label' => 'Entidade Multibanco', 'type' => 'text', 'default' => ''],
            ['key' => 'ifthenpay_subentity', 'label' => 'Subentidade', 'type' => 'text', 'default' => ''],
            ['key' => 'ifthenpay_mbway_key', 'label' => 'MBWay Key', 'type' => 'text', 'default' => ''],
            ['key' => 'ifthenpay_anti_phishing_key', 'label' => 'Anti-Phishing Key', 'type' => 'text', 'default' => ''],
            ['key' => 'ifthenpay_callback_url', 'label' => 'Callback URL', 'type' => 'readonly', 'default' => ''],
        ]
    ],
    'email' => [
        'label' => 'Email',
        'icon' => 'M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207',
        'settings' => [
            ['key' => 'smtp_host', 'label' => 'SMTP Host', 'type' => 'text', 'default' => ''],
            ['key' => 'smtp_port', 'label' => 'SMTP Port', 'type' => 'number', 'default' => '587'],
            ['key' => 'smtp_user', 'label' => 'SMTP Usuario', 'type' => 'text', 'default' => ''],
            ['key' => 'smtp_pass', 'label' => 'SMTP Password', 'type' => 'password', 'default' => ''],
            ['key' => 'smtp_from_email', 'label' => 'Email Remetente', 'type' => 'email', 'default' => ''],
            ['key' => 'smtp_from_name', 'label' => 'Nome Remetente', 'type' => 'text', 'default' => ''],
        ]
    ],
];

// Get current group
$currentGroup = isset($_GET['group']) && isset($settingsGroups[$_GET['group']])
    ? $_GET['group']
    : 'general';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        $group = $settingsGroups[$currentGroup];

        foreach ($group['settings'] as $setting) {
            $key = $setting['key'];

            // Skip readonly fields
            if ($setting['type'] === 'readonly') continue;

            $value = '';
            if ($setting['type'] === 'boolean') {
                $value = isset($_POST[$key]) ? '1' : '0';
            } else {
                $value = sanitize($_POST[$key] ?? '');
            }

            // Check if setting exists
            $existing = $db->fetch("SELECT id FROM settings WHERE setting_key = ?", [$key]);

            if ($existing) {
                $db->update('settings', ['setting_value' => $value], 'setting_key = ?', [$key]);
            } else {
                $db->insert('settings', [
                    'setting_key' => $key,
                    'setting_value' => $value
                ]);
            }
        }

        Session::flash('success', 'Configuracoes guardadas com sucesso.');
        redirect('/admin/configuracoes/?group=' . $currentGroup);
    }
}

// Get all settings
$allSettings = [];
$settingsRows = $db->fetchAll("SELECT setting_key, setting_value FROM settings");
foreach ($settingsRows as $row) {
    $allSettings[$row['setting_key']] = $row['setting_value'];
}

// Generate callback URL for payment settings
if ($currentGroup === 'payment') {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $allSettings['ifthenpay_callback_url'] = $protocol . '://' . $host . basePath() . '/api/payment-callback.php';
}

$pageTitle = 'Configuracoes';
$currentPage = 'configuracoes';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Configuracoes</h1>
        <p class="text-gray-600">Configuracoes gerais do website</p>
    </div>
</div>

<div class="flex gap-6">
    <!-- Sidebar -->
    <div class="w-64 flex-shrink-0">
        <nav class="bg-white rounded-lg shadow-sm overflow-hidden">
            <?php foreach ($settingsGroups as $key => $group): ?>
            <a href="?group=<?= $key ?>"
               class="flex items-center px-4 py-3 text-sm font-medium border-b border-gray-100 last:border-0
                      <?= $currentGroup === $key
                          ? 'bg-olive-50 text-olive-700 border-l-4 border-l-olive-600'
                          : 'text-gray-600 hover:bg-gray-50' ?>">
                <svg class="w-5 h-5 mr-3 <?= $currentGroup === $key ? 'text-olive-600' : 'text-gray-400' ?>"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="<?= $group['icon'] ?>"/>
                </svg>
                <?= $group['label'] ?>
            </a>
            <?php endforeach; ?>
        </nav>
    </div>

    <!-- Content -->
    <div class="flex-1">
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-800"><?= $settingsGroups[$currentGroup]['label'] ?></h2>
            </div>

            <form action="" method="post" class="p-6">
                <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">

                <div class="space-y-6">
                    <?php foreach ($settingsGroups[$currentGroup]['settings'] as $setting): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
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
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-olive-500 focus:border-olive-500">
                        <?php break; case 'password': ?>
                        <input type="password"
                               name="<?= $setting['key'] ?>"
                               value="<?= e($value) ?>"
                               placeholder="<?= $value ? '••••••••' : '' ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-olive-500 focus:border-olive-500">
                        <?php break; case 'textarea': ?>
                        <textarea name="<?= $setting['key'] ?>"
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-olive-500 focus:border-olive-500"><?= e($value) ?></textarea>
                        <?php break; case 'boolean': ?>
                        <label class="flex items-center">
                            <input type="checkbox"
                                   name="<?= $setting['key'] ?>"
                                   value="1"
                                   <?= $value === '1' ? 'checked' : '' ?>
                                   class="w-4 h-4 text-olive-600 border-gray-300 rounded focus:ring-olive-500">
                            <span class="ml-2 text-sm text-gray-600">Ativo</span>
                        </label>
                        <?php break; case 'readonly': ?>
                        <div class="flex items-center gap-2">
                            <input type="text"
                                   value="<?= e($value) ?>"
                                   readonly
                                   class="flex-1 px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-500">
                            <button type="button"
                                    onclick="navigator.clipboard.writeText('<?= e($value) ?>'); this.innerHTML='Copiado!'; setTimeout(() => this.innerHTML='Copiar', 2000);"
                                    class="px-3 py-2 text-sm bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">
                                Copiar
                            </button>
                        </div>
                        <?php break; endswitch; ?>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <button type="submit" class="px-6 py-2 bg-olive-600 text-white rounded-lg hover:bg-olive-700">
                        Guardar Alteracoes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
