<?php

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;

$db = Database::getInstance();

$languages = $db->fetchAll("SELECT * FROM languages WHERE is_active = 1 ORDER BY is_default DESC");

$currentSection = $_GET['section'] ?? 'activities';
if (!in_array($currentSection, ['activities', 'links'])) {
    $currentSection = 'activities';
}

$activityCategories = [
    'nature' => 'Natureza',
    'culture' => 'Cultura',
    'gastronomy' => 'Gastronomia',
    'restaurants' => 'Restaurantes',
    'cafes' => 'Cafés',
    'architecture' => 'Arquitetura',
    'adventure' => 'Aventura',
    'events' => 'Eventos',
    'wellness' => 'Bem-estar',
    'rural_tourism' => 'Turismo Rural',
    'leisure' => 'Lazer',
];

$linkCategories = [
    'tourism' => 'Turismo',
    'government' => 'Governo',
    'news' => 'Notícias',
    'gastronomy' => 'Gastronomia',
    'culture' => 'Cultura',
    'nature' => 'Natureza',
    'events' => 'Eventos',
    'accommodation' => 'Alojamento',
    'other' => 'Outros',
];

if (isset($_GET['delete']) && isset($_GET['token']) && CSRF::validate($_GET['token'])) {
    $deleteId = (int)$_GET['delete'];
    $deleteType = $_GET['type'] ?? 'activity';

    if ($deleteType === 'activity') {

        $images = $db->fetchAll("SELECT file_path FROM media WHERE entity_type = 'activity' AND entity_id = ?", [$deleteId]);
        foreach ($images as $img) {
            $path = ROOT_PATH . ltrim($img['file_path'], '/');
            if (file_exists($path)) @unlink($path);
        }

        $db->delete('media', 'entity_type = ? AND entity_id = ?', ['activity', $deleteId]);
        $db->delete('activity_translations', 'activity_id = ?', [$deleteId]);
        $db->delete('activities', 'id = ?', [$deleteId]);
        Session::flash('success', 'Atividade eliminada com sucesso.');
    } elseif ($deleteType === 'link') {
        $db->delete('external_link_translations', 'link_id = ?', [$deleteId]);
        $db->delete('external_links', 'id = ?', [$deleteId]);
        Session::flash('success', 'Link externo eliminado com sucesso.');
    }

    redirect('/admin/atividades/?section=' . $currentSection);
}

if (isset($_GET['toggle']) && isset($_GET['token']) && CSRF::validate($_GET['token'])) {
    $toggleId = (int)$_GET['toggle'];
    $toggleType = $_GET['type'] ?? 'activity';

    if ($toggleType === 'activity') {
        $item = $db->fetch("SELECT is_active FROM activities WHERE id = ?", [$toggleId]);
        if ($item) {
            $db->update('activities', ['is_active' => $item['is_active'] ? 0 : 1], 'id = ?', [$toggleId]);
        }
    } elseif ($toggleType === 'link') {
        $item = $db->fetch("SELECT is_active FROM external_links WHERE id = ?", [$toggleId]);
        if ($item) {
            $db->update('external_links', ['is_active' => $item['is_active'] ? 0 : 1], 'id = ?', [$toggleId]);
        }
    }

    redirect('/admin/atividades/?section=' . $currentSection);
}

if (isset($_GET['featured']) && isset($_GET['token']) && CSRF::validate($_GET['token'])) {
    $featuredId = (int)$_GET['featured'];
    $featuredType = $_GET['type'] ?? 'activity';

    if ($featuredType === 'activity') {
        $item = $db->fetch("SELECT is_featured FROM activities WHERE id = ?", [$featuredId]);
        if ($item) {
            $db->update('activities', ['is_featured' => $item['is_featured'] ? 0 : 1], 'id = ?', [$featuredId]);
        }
    } elseif ($featuredType === 'link') {
        $item = $db->fetch("SELECT is_featured FROM external_links WHERE id = ?", [$featuredId]);
        if ($item) {
            $db->update('external_links', ['is_featured' => $item['is_featured'] ? 0 : 1], 'id = ?', [$featuredId]);
        }
    }

    redirect('/admin/atividades/?section=' . $currentSection);
}

if ($currentSection === 'activities') {

    $filterCategory = $_GET['category'] ?? '';
    $filterSearch = $_GET['search'] ?? '';

    $whereConditions = ['1=1'];
    $whereParams = [];

    if ($filterCategory) {
        $whereConditions[] = 'a.category = ?';
        $whereParams[] = $filterCategory;
    }

    if ($filterSearch) {
        $whereConditions[] = '(at.title LIKE ? OR at.short_description LIKE ?)';
        $whereParams[] = '%' . $filterSearch . '%';
        $whereParams[] = '%' . $filterSearch . '%';
    }

    $whereClause = implode(' AND ', $whereConditions);

    $countResult = $db->fetch("SELECT COUNT(DISTINCT a.id) as total FROM activities a WHERE 1=1");
    $totalActivities = $countResult ? (int)$countResult['total'] : 0;

    $activities = $db->fetchAll(
        "SELECT a.*, at.title, at.short_description,
                (SELECT file_path FROM media WHERE entity_type = 'activity' AND entity_id = a.id AND is_cover = 1 LIMIT 1) as cover_image_path
         FROM activities a
         LEFT JOIN activity_translations at ON a.id = at.activity_id AND at.language_id = 1
         WHERE {$whereClause}
         ORDER BY a.is_featured DESC, a.sort_order, a.id DESC",
        $whereParams
    );

    error_log("Activities query returned: " . count($activities) . " rows");
    error_log("Total activities count: " . $totalActivities);

} else {

    $links = $db->fetchAll(
        "SELECT el.*, elt.title, elt.description
         FROM external_links el
         LEFT JOIN external_link_translations elt ON el.id = elt.link_id AND elt.language_id = 1
         ORDER BY el.is_featured DESC, el.sort_order, el.id DESC"
    );

    $totalLinks = count($links);
}

$pageTitle = 'Gestão de Atividades';

include dirname(__DIR__) . '/includes/header.php';
?>

<div class="p-6 lg:p-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-primary">Gestão de Atividades</h1>
                <p class="text-gray-600 mt-1">Gerencie as atividades turísticas e links externos de Mogadouro</p>
            </div>

            <!-- Section Switcher -->
            <div class="flex items-center gap-2 bg-gray-100 rounded-xl p-1.5">
                <a href="?section=activities"
                   class="px-6 py-2.5 rounded-lg text-sm font-semibold transition-all <?= $currentSection === 'activities' ? 'bg-white text-primary shadow-sm' : 'text-gray-600 hover:text-gray-800' ?>">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Atividades
                    </span>
                </a>
                <a href="?section=links"
                   class="px-6 py-2.5 rounded-lg text-sm font-semibold transition-all <?= $currentSection === 'links' ? 'bg-white text-primary shadow-sm' : 'text-gray-600 hover:text-gray-800' ?>">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                        Links Externos
                    </span>
                </a>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if ($successMessages = Session::getFlash('success')): $success = implode('<br>', $successMessages); ?>
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <?= e($success) ?>
    </div>
    <?php endif; ?>

    <?php if ($errorMessages = Session::getFlash('error')): $error = implode('<br>', $errorMessages); ?>
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        <?= e($error) ?>
    </div>
    <?php endif; ?>

    <?php if ($currentSection === 'activities'): ?>
    <!-- ============================================ -->
    <!-- ACTIVITIES SECTION                          -->
    <!-- ============================================ -->

    <!-- Stats & Actions -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="bg-white px-4 py-2 rounded-xl border border-gray-200">
                <span class="text-sm text-gray-500">Total:</span>
                <span class="font-bold text-gray-800 ml-1"><?= $totalActivities ?></span>
            </div>
        </div>

        <a href="<?= basePath() ?>/admin/atividades/criar/"
           class="inline-flex items-center gap-2 bg-secondary-600 text-white px-6 py-3 rounded-lg hover:bg-secondary-700 transition-colors shadow-sm font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nova Atividade
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-col lg:flex-row gap-4">
            <input type="hidden" name="section" value="activities">

            <div class="flex-1">
                <input type="text"
                       name="search"
                       value="<?= e($filterSearch ?? '') ?>"
                       placeholder="Pesquisar por título..."
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all">
            </div>

            <div class="w-full lg:w-48">
                <select name="category"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all">
                    <option value="">Todas as categorias</option>
                    <?php foreach ($activityCategories as $key => $label): ?>
                    <option value="<?= $key ?>" <?= ($filterCategory ?? '') === $key ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    Filtrar
                </button>
                <?php if (!empty($filterSearch) || !empty($filterCategory)): ?>
                <a href="?section=activities" class="px-4 py-2.5 text-gray-500 hover:text-gray-700 transition-colors">
                    Limpar
                </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Activities Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <?php if (empty($activities)): ?>
        <div class="p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p class="text-gray-500 mb-4">Nenhuma atividade encontrada</p>
            <a href="<?= basePath() ?>/admin/atividades/criar/" class="text-primary hover:underline">
                Criar primeira atividade
            </a>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Atividade</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Categoria</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Destaque</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Views</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php

                    error_log("Total activities in array: " . count($activities));
                    foreach ($activities as $act):
                    ?>

                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-12 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                    <?php if (!empty($act['cover_image_path'])): ?>
                                    <img loading="lazy" decoding="async" src="<?= basePath() ?><?= htmlspecialchars($act['cover_image_path']) ?>" alt="" class="w-full h-full object-cover">
                                    <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-800 truncate"><?= e($act['title'] ?? 'Sem título') ?></p>
                                    <p class="text-sm text-gray-500 truncate max-w-xs"><?= e($act['short_description'] ?? '') ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                <?= $activityCategories[$act['category']] ?? ucfirst($act['category'] ?? 'Outro') ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="?section=activities&featured=<?= $act['id'] ?>&type=activity&token=<?= CSRF::getToken() ?>"
                               class="inline-flex items-center justify-center w-8 h-8 rounded-full transition-colors <?= $act['is_featured'] ? 'bg-yellow-100 text-yellow-600 hover:bg-yellow-200' : 'bg-gray-100 text-gray-400 hover:bg-gray-200' ?>">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </a>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="?section=activities&toggle=<?= $act['id'] ?>&type=activity&token=<?= CSRF::getToken() ?>"
                               class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium transition-colors <?= $act['is_active'] ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' ?>">
                                <?= $act['is_active'] ? 'Ativo' : 'Inativo' ?>
                            </a>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm text-gray-600"><?= number_format($act['views_count'] ?? 0) ?></span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="<?= basePath() ?>/atividades/?slug=<?= e($act['slug']) ?>" target="_blank"
                                   class="p-2 text-gray-400 hover:text-primary hover:bg-gray-100 rounded-lg transition-colors"
                                   title="Ver no site">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                                <a href="<?= basePath() ?>/admin/atividades/editar/?id=<?= $act['id'] ?>"
                                   class="p-2 text-gray-400 hover:text-primary hover:bg-gray-100 rounded-lg transition-colors"
                                   title="Editar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <button type="button"
                                        class="delete-activity-btn p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        data-id="<?= $act['id'] ?>"
                                        data-type="activity"
                                        data-name="<?= e($act['title'] ?? 'esta atividade') ?>"
                                        title="Eliminar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <?php else: ?>
    <!-- ============================================ -->
    <!-- EXTERNAL LINKS SECTION                      -->
    <!-- ============================================ -->

    <!-- Stats & Actions -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="bg-white px-4 py-2 rounded-xl border border-gray-200">
                <span class="text-sm text-gray-500">Total:</span>
                <span class="font-bold text-gray-800 ml-1"><?= $totalLinks ?? 0 ?></span>
            </div>
        </div>

        <button onclick="openLinkModal()"
                class="inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-xl hover:bg-primary/90 transition-colors shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Novo Link
        </button>
    </div>

    <!-- Links Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <?php if (empty($links)): ?>
        <div class="p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
            </svg>
            <p class="text-gray-500 mb-4">Nenhum link externo encontrado</p>
            <button onclick="openLinkModal()" class="text-primary hover:underline">
                Adicionar primeiro link
            </button>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Link</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Categoria</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Destaque</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Cliques</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($links as $link): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-800"><?= e($link['title'] ?? 'Sem título') ?></p>
                                <a href="<?= e($link['url']) ?>" target="_blank" class="text-sm text-primary hover:underline truncate block max-w-xs">
                                    <?= e($link['url']) ?>
                                </a>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                <?= $linkCategories[$link['category']] ?? ucfirst($link['category'] ?? 'Outro') ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="?section=links&featured=<?= $link['id'] ?>&type=link&token=<?= CSRF::getToken() ?>"
                               class="inline-flex items-center justify-center w-8 h-8 rounded-full transition-colors <?= $link['is_featured'] ? 'bg-yellow-100 text-yellow-600 hover:bg-yellow-200' : 'bg-gray-100 text-gray-400 hover:bg-gray-200' ?>">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </a>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="?section=links&toggle=<?= $link['id'] ?>&type=link&token=<?= CSRF::getToken() ?>"
                               class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium transition-colors <?= $link['is_active'] ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' ?>">
                                <?= $link['is_active'] ? 'Ativo' : 'Inativo' ?>
                            </a>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm text-gray-600"><?= number_format($link['clicks_count'] ?? 0) ?></span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="<?= e($link['url']) ?>" target="_blank"
                                   class="p-2 text-gray-400 hover:text-primary hover:bg-gray-100 rounded-lg transition-colors"
                                   title="Abrir link">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                                <button onclick='editLink(<?= json_encode($link, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'
                                        class="p-2 text-gray-400 hover:text-primary hover:bg-gray-100 rounded-lg transition-colors"
                                        title="Editar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button type="button"
                                        class="delete-activity-btn p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        data-id="<?= $link['id'] ?>"
                                        data-type="link"
                                        data-name="<?= e($link['title'] ?? 'este link') ?>"
                                        title="Eliminar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Link Modal -->
    <div id="link-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50" onclick="closeLinkModal()"></div>
        <div class="absolute inset-4 md:inset-auto md:top-1/2 md:left-1/2 md:-translate-x-1/2 md:-translate-y-1/2 md:w-full md:max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden">
            <form id="link-form" method="POST" action="<?= basePath() ?>/admin/atividades/save-link.php">
                <?= CSRF::tokenField() ?>
                <input type="hidden" name="link_id" id="link_id" value="">

                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 id="modal-title" class="text-xl font-bold text-gray-800">Novo Link Externo</h3>
                    <button type="button" onclick="closeLinkModal()" class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-6 max-h-[60vh] overflow-y-auto space-y-6">
                    <!-- URL -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">URL *</label>
                        <input type="url" name="url" id="link_url" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all"
                               placeholder="https://...">
                    </div>

                    <!-- Title PT -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Título (PT) *</label>
                        <input type="text" name="title_pt" id="link_title_pt" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all"
                               placeholder="Nome do website">
                    </div>

                    <!-- Title EN -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Título (EN)</label>
                        <input type="text" name="title_en" id="link_title_en"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all"
                               placeholder="Website name">
                    </div>

                    <!-- Description PT -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Descrição (PT)</label>
                        <textarea name="description_pt" id="link_desc_pt" rows="2"
                                  class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all resize-none"
                                  placeholder="Breve descrição do link..."></textarea>
                    </div>

                    <!-- Description EN -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Descrição (EN)</label>
                        <textarea name="description_en" id="link_desc_en" rows="2"
                                  class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all resize-none"
                                  placeholder="Brief description..."></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Categoria</label>
                            <select name="category" id="link_category"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all">
                                <?php foreach ($linkCategories as $key => $label): ?>
                                <option value="<?= $key ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Icon -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Ícone</label>
                            <select name="icon" id="link_icon"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all">
                                <option value="map">🗺️ Mapa</option>
                                <option value="building">🏛️ Edifício</option>
                                <option value="tree">🌳 Árvore</option>
                                <option value="star">⭐ Estrela</option>
                                <option value="compass">🧭 Bússola</option>
                                <option value="mountain">⛰️ Montanha</option>
                                <option value="water">💧 Água</option>
                                <option value="utensils">🍴 Talheres</option>
                                <option value="coffee">☕ Café</option>
                                <option value="wine-glass">🍷 Vinho</option>
                                <option value="camera">📷 Câmara</option>
                                <option value="ticket">🎫 Bilhete</option>
                                <option value="calendar">📅 Calendário</option>
                                <option value="clock">🕐 Relógio</option>
                                <option value="info">ℹ️ Informação</option>
                                <option value="phone">📞 Telefone</option>
                                <option value="mail">✉️ Email</option>
                                <option value="globe">🌍 Website</option>
                                <option value="heart">❤️ Favorito</option>
                                <option value="bookmark">🔖 Marcador</option>
                            </select>
                        </div>
                    </div>

                    <!-- Sort Order -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Ordem</label>
                        <input type="number" name="sort_order" id="link_sort" min="0" value="0"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all">
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                    <button type="button" onclick="closeLinkModal()"
                            class="px-6 py-2.5 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-6 py-2.5 bg-primary text-white rounded-xl hover:bg-primary/90 transition-colors">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeDeleteModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-2xl shadow-2xl p-6">
        <div class="text-center">
            <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Confirmar Eliminação</h3>
            <p class="text-gray-600 mb-6">Tem certeza que deseja eliminar "<span id="delete-item-name"></span>"? Esta ação não pode ser desfeita.</p>
            <div class="flex gap-3 justify-center">
                <button onclick="closeDeleteModal()"
                        class="px-6 py-2.5 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <a id="delete-confirm-btn" href="#"
                   class="px-6 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors">
                    Eliminar
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Delete confirmation using event delegation
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('delete-modal');
    const itemName = document.getElementById('delete-item-name');
    const confirmBtn = document.getElementById('delete-confirm-btn');

    // Event delegation for all delete buttons
    document.addEventListener('click', function(e) {
        const deleteBtn = e.target.closest('.delete-activity-btn');

        if (deleteBtn) {
            e.preventDefault();
            e.stopPropagation();

            const id = deleteBtn.dataset.id;
            const type = deleteBtn.dataset.type;
            const name = deleteBtn.dataset.name;

            console.log('Delete clicked:', id, type, name);

            if (modal && itemName && confirmBtn) {
                itemName.textContent = name;
                confirmBtn.href = '?section=<?= $currentSection ?>&delete=' + id + '&type=' + type + '&token=<?= CSRF::getToken() ?>';
                modal.classList.remove('hidden');
            }

            return false;
        }
    });
});

function closeDeleteModal() {
    const modal = document.getElementById('delete-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Link modal functions
function openLinkModal() {
    document.getElementById('modal-title').textContent = 'Novo Link Externo';
    document.getElementById('link-form').reset();
    document.getElementById('link_id').value = '';
    document.getElementById('link-modal').classList.remove('hidden');
}

function closeLinkModal() {
    document.getElementById('link-modal').classList.add('hidden');
}

function editLink(link) {
    document.getElementById('modal-title').textContent = 'Editar Link';
    document.getElementById('link_id').value = link.id;
    document.getElementById('link_url').value = link.url || '';
    document.getElementById('link_title_pt').value = link.title || '';
    document.getElementById('link_title_en').value = link.title_en || '';
    document.getElementById('link_desc_pt').value = link.description || '';
    document.getElementById('link_desc_en').value = link.description_en || '';
    document.getElementById('link_category').value = link.category || 'tourism';
    document.getElementById('link_icon').value = link.icon || 'map';
    document.getElementById('link_sort').value = link.sort_order || 0;
    document.getElementById('link-modal').classList.remove('hidden');
}

// Close modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
        closeLinkModal();
    }
});
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
