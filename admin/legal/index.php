<?php
/**
 * A Casa do Gi - Admin Legal Sections
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;

$db = Database::getInstance();
$base = basePath();

// Get current page filter (terms or privacy)
$currentLegalPage = $_GET['page'] ?? 'terms';
$pageLabels = [
    'terms' => 'Termos e Condições',
    'privacy' => 'Política de Privacidade'
];

if (!array_key_exists($currentLegalPage, $pageLabels)) {
    redirect('/admin/legal/?page=terms');
}

// Handle delete
if (isset($_GET['delete']) && isset($_GET['token'])) {
    if (\Core\CSRF::validate($_GET['token'])) {
        $sectionId = (int)$_GET['delete'];
        $db->delete('legal_sections', 'id = ?', [$sectionId]);
        Session::flash('success', 'Secção eliminada com sucesso.');
    }
    redirect('/admin/legal/?page=' . $currentLegalPage);
}

// Handle toggle active
if (isset($_GET['toggle']) && isset($_GET['token'])) {
    if (\Core\CSRF::validate($_GET['token'])) {
        $sectionId = (int)$_GET['toggle'];
        $section = $db->fetch("SELECT is_active FROM legal_sections WHERE id = ?", [$sectionId]);
        if ($section) {
            $newStatus = $section['is_active'] ? 0 : 1;
            $db->update('legal_sections', ['is_active' => $newStatus], 'id = ?', [$sectionId]);
            Session::flash('success', 'Estado atualizado.');
        }
    }
    redirect('/admin/legal/?page=' . $currentLegalPage);
}

// Get sections
$sections = $db->fetchAll(
    "SELECT s.*, st.title, st.content 
     FROM legal_sections s 
     LEFT JOIN legal_section_translations st ON s.id = st.section_id AND st.language_id = 1
     WHERE s.page = ? 
     ORDER BY s.sort_order ASC",
    [$currentLegalPage]
);

$pageTitle = $pageLabels[$currentLegalPage];
$currentPage = 'legal';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-primary"><?= $pageTitle ?></h1>
        <p class="text-gray-600">Gerir pontos e secções</p>
    </div>
    <a href="./create.php?page=<?= $currentLegalPage ?>" class="inline-flex items-center px-4 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nova Secção
    </a>
</div>

<!-- Page Tabs -->
<div class="mb-6 border-b border-gray-200">
    <nav class="-mb-px flex space-x-8">
        <?php foreach ($pageLabels as $key => $label): ?>
        <a href="?page=<?= $key ?>"
           class="<?= $currentLegalPage === $key 
               ? 'border-secondary-500 text-secondary-600' 
               : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> 
               whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
            <?= $label ?>
        </a>
        <?php endforeach; ?>
    </nav>
</div>

<!-- Sections List -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <?php if (empty($sections)): ?>
    <div class="p-12 text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <h3 class="text-lg font-medium text-gray-700 mb-2">Nenhuma secção encontrada</h3>
        <p class="text-gray-500 mb-4">Adicione o primeiro ponto a esta página.</p>
        <a href="./create.php?page=<?= $currentLegalPage ?>" class="inline-flex items-center px-4 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700">
            Criar Secção
        </a>
    </div>
    <?php else: ?>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">Ordem</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conteúdo (Resumo)</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($sections as $section): ?>
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <?= $section['sort_order'] ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                        <?= e($section['title'] ?? 'Sem título') ?>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm text-gray-500 line-clamp-2 max-w-lg">
                        <?= strip_tags($section['content'] ?? '') ?>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <?php if ($section['is_active']): ?>
                    <span class="inline-flex px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">
                        Ativo
                    </span>
                    <?php else: ?>
                    <span class="inline-flex px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">
                        Inativo
                    </span>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="./edit.php?id=<?= $section['id'] ?>"
                       class="text-secondary-600 hover:text-olive-900 mr-3">
                        Editar
                    </a>
                    <a href="?page=<?= $currentLegalPage ?>&toggle=<?= $section['id'] ?>&token=<?= \Core\CSRF::getToken() ?>"
                       class="text-gray-600 hover:text-gray-900 mr-3"
                       title="<?= $section['is_active'] ? 'Desativar' : 'Ativar' ?>">
                        <?= $section['is_active'] ? 'Desativar' : 'Ativar' ?>
                    </a>
                    <button onclick="openDeleteModal(<?= $section['id'] ?>, '<?= e($section['title']) ?>')"
                            class="text-red-600 hover:text-red-900">
                        Eliminar
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeDeleteModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md m-4 transform transition-all scale-100">
        <div class="text-center">
            <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Eliminar Secção</h3>
            <p class="text-gray-600 mb-6">
                Tem a certeza que deseja eliminar "<span id="deleteItemName" class="font-semibold text-gray-900"></span>"?
                Esta ação não pode ser revertida.
            </p>
            <div class="flex gap-3 justify-center">
                <button type="button"
                        onclick="closeDeleteModal()"
                        class="px-6 py-2.5 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors font-medium">
                    Cancelar
                </button>
                <a href="#"
                   id="confirmDelete"
                   class="px-6 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors font-medium shadow-sm">
                    Eliminar
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function openDeleteModal(id, name) {
    const modal = document.getElementById('deleteModal');
    const nameSpan = document.getElementById('deleteItemName');
    const confirmBtn = document.getElementById('confirmDelete');

    nameSpan.textContent = name;
    confirmBtn.href = '?page=<?= $currentLegalPage ?>&delete=' + id + '&token=<?= \Core\CSRF::getToken() ?>';
    modal.classList.remove('hidden');
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.add('hidden');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('deleteModal').classList.contains('hidden')) {
        closeDeleteModal();
    }
});
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
