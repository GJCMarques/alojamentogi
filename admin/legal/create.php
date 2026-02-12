<?php

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;

$db = Database::getInstance();
$base = basePath();

$pageType = $_GET['page'] ?? 'terms';
$pageLabels = [
    'terms' => 'Termos e Condições',
    'privacy' => 'Política de Privacidade'
];

if (!array_key_exists($pageType, $pageLabels)) {
    redirect('/admin/legal/?page=terms');
}

$languages = $db->fetchAll("SELECT * FROM languages WHERE is_active = 1 ORDER BY is_default DESC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        $sectionId = $db->insert('legal_sections', [
            'page' => $pageType,
            'sort_order' => $sortOrder,
            'is_active' => $isActive
        ]);

        foreach ($languages as $lang) {
            $langId = $lang['id'];
            $title = $_POST['title_' . $langId] ?? '';
            $content = $_POST['content_' . $langId] ?? '';

            if ($title || $content) {
                $db->insert('legal_section_translations', [
                    'section_id' => $sectionId,
                    'language_id' => $langId,
                    'title' => $title,
                    'content' => $content
                ]);
            }
        }

        Session::flash('success', 'Secção criada com sucesso.');
        redirect('/admin/legal/?page=' . $pageType);
    }
}

$pageTitle = 'Nova Secção - ' . $pageLabels[$pageType];
$currentPage = 'legal';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="mb-6">
    <a href="./?page=<?= $pageType ?>" class="text-gray-500 hover:text-gray-700 flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Voltar à lista
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm">
    <div class="px-6 py-4 border-b border-gray-200">
        <h1 class="text-xl font-bold text-primary">Nova Secção</h1>
        <p class="text-sm text-gray-600">Adicionar novo ponto a <?= $pageLabels[$pageType] ?></p>
    </div>

    <form action="" method="post" class="p-6">
        <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Ordem</label>
                <input type="number"
                       name="sort_order"
                       id="sort_order"
                       value="<?= isset($_POST['sort_order']) ? e($_POST['sort_order']) : '0' ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                <p class="text-xs text-gray-500 mt-1">Número mais baixo aparece primeiro (ex: 1, 2, 3)</p>
            </div>

            <div class="flex items-center mt-6">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" <?= isset($_POST['is_active']) || !isset($_POST['csrf_token']) ? 'checked' : '' ?> class="sr-only peer">
                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-secondary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-secondary-600"></div>
                    <span class="ml-3 text-sm font-medium text-gray-700">Ativo</span>
                </label>
            </div>
        </div>

        <!-- Language Tabs -->
        <div class="border border-gray-200 rounded-lg overflow-hidden mb-6">
            <div class="flex bg-gray-50 border-b border-gray-200">
                <?php foreach ($languages as $i => $lang): ?>
                <button type="button"
                        onclick="switchTab('<?= $lang['id'] ?>')"
                        class="lang-tab px-4 py-2 text-sm font-medium <?= $i === 0 ? 'bg-white text-secondary-600 border-b-2 border-secondary-600' : 'text-gray-500 hover:text-gray-700' ?>"
                        data-lang="<?= $lang['id'] ?>">
                    <img src="<?= asset('images/flags/' . $lang['flag_icon'] . '.svg') ?>" class="w-4 h-4 inline-block mr-2" alt="">
                    <?= $lang['name'] ?>
                </button>
                <?php endforeach; ?>
            </div>

            <?php foreach ($languages as $i => $lang): ?>
            <div class="lang-content p-4 <?= $i > 0 ? 'hidden' : '' ?>" data-lang="<?= $lang['id'] ?>">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                    <input type="text"
                           name="title_<?= $lang['id'] ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500"
                           placeholder="Ex: 1. Introdução"
                           required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Conteúdo</label>
                    <textarea name="content_<?= $lang['id'] ?>"
                              rows="6"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500 font-mono text-sm"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Suporta HTML básico (&lt;p&gt;, &lt;ul&gt;, etc)</p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="flex justify-end pt-6 border-t border-gray-200">
            <button type="submit" class="px-6 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700">
                Criar Secção
            </button>
        </div>
    </form>
</div>

<script>
function switchTab(langId) {
    // Update tabs
    document.querySelectorAll('.lang-tab').forEach(tab => {
        if (tab.dataset.lang === langId) {
            tab.classList.add('bg-white', 'text-secondary-600', 'border-b-2', 'border-secondary-600');
            tab.classList.remove('text-gray-500');
        } else {
            tab.classList.remove('bg-white', 'text-secondary-600', 'border-b-2', 'border-secondary-600');
            tab.classList.add('text-gray-500');
        }
    });

    // Update content
    document.querySelectorAll('.lang-content').forEach(content => {
        if (content.dataset.lang === langId) {
            content.classList.remove('hidden');
        } else {
            content.classList.add('hidden');
        }
    });
}
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
