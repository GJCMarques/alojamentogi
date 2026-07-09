<?php

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;

$db = Database::getInstance();
$base = basePath();

$sections = ['official' => 'Recursos Oficiais', 'guide' => 'Guias e Roteiros'];

// -------- Ações (guardar / eliminar) --------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
        Session::flash('error', 'Sessão expirada. Tente novamente.');
        redirect('/admin/atividades/');
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $db->delete('activity_links', 'id = ?', [(int)($_POST['id'] ?? 0)]);
        Session::flash('success', 'Link eliminado.');
        redirect('/admin/atividades/');
    }

    if ($action === 'save') {
        $section = in_array($_POST['section'] ?? '', ['official', 'guide']) ? $_POST['section'] : 'official';
        $data = [
            'section'    => $section,
            'tag_pt'     => sanitize($_POST['tag_pt'] ?? ''),
            'tag_en'     => sanitize($_POST['tag_en'] ?? ''),
            'title_pt'   => sanitize($_POST['title_pt'] ?? ''),
            'title_en'   => sanitize($_POST['title_en'] ?? ''),
            'desc_pt'    => sanitize($_POST['desc_pt'] ?? ''),
            'desc_en'    => sanitize($_POST['desc_en'] ?? ''),
            'url'        => trim($_POST['url'] ?? ''),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'is_active'  => isset($_POST['is_active']) ? 1 : 0,
        ];

        if ($data['title_pt'] === '' || $data['url'] === '') {
            Session::flash('error', 'O título (PT) e o link (URL) são obrigatórios.');
            redirect('/admin/atividades/');
        }
        if ($data['title_en'] === '') $data['title_en'] = $data['title_pt'];

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $db->update('activity_links', $data, 'id = ?', [$id]);
            Session::flash('success', 'Link atualizado.');
        } else {
            $db->insert('activity_links', $data);
            Session::flash('success', 'Link adicionado.');
        }
        redirect('/admin/atividades/');
    }
}

$editId = (int)($_GET['edit'] ?? 0);
$editing = $editId > 0 ? $db->fetch("SELECT * FROM activity_links WHERE id = ?", [$editId]) : null;

$links = $db->fetchAll("SELECT * FROM activity_links ORDER BY section, sort_order, id");
$grouped = ['official' => [], 'guide' => []];
foreach ($links as $l) {
    $grouped[$l['section']][] = $l;
}

$pageTitle = 'Atividades';
$currentPage = 'atividades';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="flex justify-between items-center mb-2">
    <div>
        <h1 class="text-2xl font-bold text-primary">Gestão de Atividades</h1>
        <p class="text-gray-600">Links informativos mostrados na página <em>Atividades</em> (recursos oficiais e guias). A página já não usa fotografias.</p>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-6 mt-6 items-start">
    <!-- Lista -->
    <div class="lg:col-span-2 space-y-8">
        <?php foreach ($sections as $key => $label): ?>
        <div class="admin-card">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-bold text-gray-800"><?= $label ?></h2>
                <span class="text-xs text-gray-400"><?= count($grouped[$key]) ?> link(s)</span>
            </div>
            <div class="divide-y divide-gray-100">
                <?php if (empty($grouped[$key])): ?>
                <p class="px-6 py-6 text-sm text-gray-500">Sem links nesta secção.</p>
                <?php else: foreach ($grouped[$key] as $l): ?>
                <div class="px-6 py-4 flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <?php if (!$l['is_active']): ?><span class="text-[10px] uppercase font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded">Inativo</span><?php endif; ?>
                            <?php if ($l['tag_pt']): ?><span class="text-[10px] uppercase font-bold text-secondary bg-secondary/10 px-2 py-0.5 rounded"><?= e($l['tag_pt']) ?></span><?php endif; ?>
                            <p class="font-medium text-gray-800"><?= e($l['title_pt']) ?></p>
                        </div>
                        <a href="<?= e($l['url']) ?>" target="_blank" rel="noopener" class="text-xs text-secondary-600 hover:underline break-all"><?= e($l['url']) ?></a>
                        <?php if ($l['desc_pt']): ?><p class="text-sm text-gray-500 mt-1"><?= e($l['desc_pt']) ?></p><?php endif; ?>
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <a href="?edit=<?= $l['id'] ?>" class="p-2 text-gray-500 hover:text-secondary-600" title="Editar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form method="post" onsubmit="return confirm('Eliminar este link?');" class="inline">
                            <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $l['id'] ?>">
                            <button type="submit" class="p-2 text-gray-500 hover:text-red-600" title="Eliminar">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Formulário -->
    <div class="admin-card p-6 lg:sticky lg:top-6">
        <h2 class="font-bold text-gray-800 mb-4"><?= $editing ? 'Editar Link' : 'Novo Link' ?></h2>
        <form method="post" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
            <input type="hidden" name="action" value="save">
            <?php if ($editing): ?><input type="hidden" name="id" value="<?= $editing['id'] ?>"><?php endif; ?>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Secção</label>
                <select name="section" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <?php foreach ($sections as $k => $v): ?>
                    <option value="<?= $k ?>" <?= ($editing['section'] ?? 'official') === $k ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Título (PT) *</label>
                <input type="text" name="title_pt" required value="<?= e($editing['title_pt'] ?? '') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Título (EN)</label>
                <input type="text" name="title_en" value="<?= e($editing['title_en'] ?? '') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Link (URL) *</label>
                <input type="url" name="url" required placeholder="https://..." value="<?= e($editing['url'] ?? '') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Etiqueta (PT)</label>
                    <input type="text" name="tag_pt" value="<?= e($editing['tag_pt'] ?? '') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Etiqueta (EN)</label>
                    <input type="text" name="tag_en" value="<?= e($editing['tag_en'] ?? '') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Descrição (PT)</label>
                <textarea name="desc_pt" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"><?= e($editing['desc_pt'] ?? '') ?></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Descrição (EN)</label>
                <textarea name="desc_en" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"><?= e($editing['desc_en'] ?? '') ?></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3 items-center">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Ordem</label>
                    <input type="number" name="sort_order" value="<?= e($editing['sort_order'] ?? 0) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
                <label class="flex items-center gap-2 text-sm text-gray-700 mt-5">
                    <input type="checkbox" name="is_active" <?= ($editing['is_active'] ?? 1) ? 'checked' : '' ?> class="rounded border-gray-300 text-secondary-600">
                    Ativo
                </label>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-5 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700 text-sm font-medium"><?= $editing ? 'Guardar' : 'Adicionar' ?></button>
                <?php if ($editing): ?><a href="<?= $base ?>/admin/atividades/" class="text-sm text-gray-500 hover:text-gray-700">Cancelar</a><?php endif; ?>
            </div>
        </form>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
