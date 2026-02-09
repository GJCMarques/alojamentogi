<?php
/**
 * A Casa do Gi - Admin Content Blocks
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;

$db = Database::getInstance();

// Get languages
$languages = $db->fetchAll("SELECT * FROM languages WHERE is_active = 1 ORDER BY is_default DESC");

// Content block definitions
$blockDefinitions = [
    'homepage' => [
        'label' => 'Pagina Inicial',
        'blocks' => [
            'home_hero_subtitle' => ['label' => 'Hero - Subtitulo', 'type' => 'text'],
            'home_split_left_label' => ['label' => 'Split Hero - Label Esquerdo', 'type' => 'text'],
            'home_split_left_title' => ['label' => 'Split Hero - Titulo Esquerdo', 'type' => 'text'],
            'home_split_right_label' => ['label' => 'Split Hero - Label Direito', 'type' => 'text'],
            'home_split_right_title' => ['label' => 'Split Hero - Titulo Direito', 'type' => 'text'],
            'home_explore_title' => ['label' => 'Explorar - Titulo Seccao', 'type' => 'text'],
            'home_card1_label' => ['label' => 'Card 1 (Alojamento) - Label', 'type' => 'text'],
            'home_card1_title' => ['label' => 'Card 1 (Alojamento) - Titulo', 'type' => 'text'],
            'home_card1_text' => ['label' => 'Card 1 (Alojamento) - Texto', 'type' => 'text'],
            'home_card1_cta' => ['label' => 'Card 1 (Alojamento) - Botao', 'type' => 'text'],
            'home_card2_label' => ['label' => 'Card 2 (Atividades) - Label', 'type' => 'text'],
            'home_card2_title' => ['label' => 'Card 2 (Atividades) - Titulo', 'type' => 'text'],
            'home_card2_text' => ['label' => 'Card 2 (Atividades) - Texto', 'type' => 'text'],
            'home_card2_cta' => ['label' => 'Card 2 (Atividades) - Botao', 'type' => 'text'],
            'home_card3_label' => ['label' => 'Card 3 (Loja) - Label', 'type' => 'text'],
            'home_card3_title' => ['label' => 'Card 3 (Loja) - Titulo', 'type' => 'text'],
            'home_card3_text' => ['label' => 'Card 3 (Loja) - Texto', 'type' => 'text'],
            'home_card3_cta' => ['label' => 'Card 3 (Loja) - Botao', 'type' => 'text'],
            'home_card4_label' => ['label' => 'Card 4 (Contactos) - Label', 'type' => 'text'],
            'home_card4_title' => ['label' => 'Card 4 (Contactos) - Titulo', 'type' => 'text'],
            'home_card4_text' => ['label' => 'Card 4 (Contactos) - Texto', 'type' => 'text'],
            'home_card4_cta' => ['label' => 'Card 4 (Contactos) - Botao', 'type' => 'text'],
            'home_about_label' => ['label' => 'Sobre Nos - Label', 'type' => 'text'],
            'home_about_title' => ['label' => 'Sobre Nos - Titulo (HTML)', 'type' => 'wysiwyg'],
            'home_about_text1' => ['label' => 'Sobre Nos - Paragrafo 1', 'type' => 'textarea'],
            'home_about_text2' => ['label' => 'Sobre Nos - Paragrafo 2', 'type' => 'textarea'],
            'home_about_cta' => ['label' => 'Sobre Nos - Botao', 'type' => 'text'],
        ]
    ],
    'about' => [
        'label' => 'Sobre Nos',
        'blocks' => [
            'about_hero_label' => ['label' => 'Hero - Label', 'type' => 'text'],
            'about_hero_subtitle' => ['label' => 'Hero - Subtitulo', 'type' => 'textarea'],
            'about_origin_label' => ['label' => 'Origem - Label', 'type' => 'text'],
            'about_origin_title' => ['label' => 'Origem - Titulo (HTML)', 'type' => 'wysiwyg'],
            'about_origin_text1' => ['label' => 'Origem - Paragrafo 1', 'type' => 'textarea'],
            'about_origin_text2' => ['label' => 'Origem - Paragrafo 2', 'type' => 'textarea'],
            'about_origin_caption' => ['label' => 'Origem - Legenda Foto', 'type' => 'text'],
            'about_origin_signature' => ['label' => 'Origem - Assinatura', 'type' => 'text'],
            'about_values_label' => ['label' => 'Valores - Label', 'type' => 'text'],
            'about_values_title' => ['label' => 'Valores - Titulo (HTML)', 'type' => 'wysiwyg'],
            'about_values_intro' => ['label' => 'Valores - Texto Introducao', 'type' => 'textarea'],
            'about_value1_title' => ['label' => 'Valor 1 - Titulo', 'type' => 'text'],
            'about_value1_text' => ['label' => 'Valor 1 - Texto', 'type' => 'textarea'],
            'about_value2_title' => ['label' => 'Valor 2 - Titulo', 'type' => 'text'],
            'about_value2_text' => ['label' => 'Valor 2 - Texto', 'type' => 'textarea'],
            'about_value3_title' => ['label' => 'Valor 3 - Titulo', 'type' => 'text'],
            'about_value3_text' => ['label' => 'Valor 3 - Texto', 'type' => 'textarea'],
            'about_value4_title' => ['label' => 'Valor 4 - Titulo', 'type' => 'text'],
            'about_value4_text' => ['label' => 'Valor 4 - Texto', 'type' => 'textarea'],
            'about_region_label' => ['label' => 'Regiao - Label', 'type' => 'text'],
            'about_region_text' => ['label' => 'Regiao - Texto', 'type' => 'textarea'],
            'about_region_cta1' => ['label' => 'Regiao - Botao 1', 'type' => 'text'],
            'about_region_cta2' => ['label' => 'Regiao - Botao 2', 'type' => 'text'],
        ]
    ],
    'accommodation' => [
        'label' => 'Alojamento',
        'blocks' => [
            'accommodation_intro' => ['label' => 'Introducao', 'type' => 'textarea'],
            'accommodation_cta_title' => ['label' => 'CTA - Titulo', 'type' => 'text'],
            'accommodation_cta_text' => ['label' => 'CTA - Texto', 'type' => 'textarea'],
        ]
    ],
    'shop' => [
        'label' => 'Loja',
        'blocks' => [
            'shop_intro' => ['label' => 'Introducao', 'type' => 'textarea'],
            'shop_empty_message' => ['label' => 'Mensagem Vazio', 'type' => 'text'],
        ]
    ],
    'contact' => [
        'label' => 'Contactos',
        'blocks' => [
            'contact_intro' => ['label' => 'Introducao', 'type' => 'textarea'],
            'contact_success_message' => ['label' => 'Mensagem Sucesso', 'type' => 'textarea'],
        ]
    ],
    'footer' => [
        'label' => 'Rodape',
        'blocks' => [
            'footer_tagline' => ['label' => 'Tagline', 'type' => 'text'],
        ]
    ],
];

// Get current section
$currentSection = isset($_GET['section']) && isset($blockDefinitions[$_GET['section']])
    ? $_GET['section']
    : 'homepage';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        $section = $blockDefinitions[$currentSection];

        foreach ($section['blocks'] as $blockKey => $blockDef) {
            foreach ($languages as $lang) {
                $fieldName = $blockKey . '_' . $lang['id'];
                $content = $_POST[$fieldName] ?? '';

                // Check if block exists
                $existing = $db->fetch(
                    "SELECT id FROM content_blocks WHERE block_key = ? AND language_id = ?",
                    [$blockKey, $lang['id']]
                );

                if ($existing) {
                    $db->update('content_blocks', [
                        'content' => $content,
                        'updated_at' => date('Y-m-d H:i:s')
                    ], 'id = ?', [$existing['id']]);
                } else {
                    $db->insert('content_blocks', [
                        'block_key' => $blockKey,
                        'language_id' => $lang['id'],
                        'content' => $content
                    ]);
                }
            }
        }

        Session::flash('success', 'Conteudos guardados com sucesso.');
        redirect('/admin/conteudos/?section=' . $currentSection);
    }
}

// Get all content blocks for current section
$blockKeys = array_keys($blockDefinitions[$currentSection]['blocks']);
$placeholders = implode(',', array_fill(0, count($blockKeys), '?'));
$contentBlocks = [];

if (!empty($blockKeys)) {
    $rows = $db->fetchAll(
        "SELECT block_key, language_id, content FROM content_blocks WHERE block_key IN ({$placeholders})",
        $blockKeys
    );
    foreach ($rows as $row) {
        $contentBlocks[$row['block_key']][$row['language_id']] = $row['content'];
    }
}

$pageTitle = 'Conteudos';
$currentPage = 'conteudos';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Gestao de Conteudos</h1>
        <p class="text-gray-600">Editar textos do website</p>
    </div>
</div>

<div class="flex gap-6">
    <!-- Sidebar -->
    <div class="w-64 flex-shrink-0">
        <nav class="bg-white rounded-lg shadow-sm overflow-hidden">
            <?php foreach ($blockDefinitions as $key => $section): ?>
            <a href="?section=<?= $key ?>"
               class="flex items-center px-4 py-3 text-sm font-medium border-b border-gray-100 last:border-0
                      <?= $currentSection === $key
                          ? 'bg-secondary-50 text-secondary-700 border-l-4 border-l-secondary-600'
                          : 'text-gray-600 hover:bg-gray-50' ?>">
                <?= $section['label'] ?>
            </a>
            <?php endforeach; ?>
        </nav>
    </div>

    <!-- Content -->
    <div class="flex-1">
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-800"><?= $blockDefinitions[$currentSection]['label'] ?></h2>
            </div>

            <form action="" method="post" class="p-6">
                <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">

                <div class="space-y-8">
                    <?php foreach ($blockDefinitions[$currentSection]['blocks'] as $blockKey => $blockDef): ?>
                    <div class="border-b border-gray-100 pb-6 last:border-0 last:pb-0">
                        <h3 class="text-sm font-semibold text-gray-700 mb-4"><?= $blockDef['label'] ?></h3>

                        <!-- Language tabs -->
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="flex bg-gray-50 border-b border-gray-200">
                                <?php foreach ($languages as $i => $lang): ?>
                                <button type="button"
                                        onclick="switchBlockTab('<?= $blockKey ?>', '<?= $lang['id'] ?>')"
                                        class="block-tab px-4 py-2 text-xs font-medium <?= $i === 0 ? 'bg-white text-secondary-600 border-b-2 border-secondary-600' : 'text-gray-500 hover:text-gray-700' ?>"
                                        data-block="<?= $blockKey ?>"
                                        data-lang="<?= $lang['id'] ?>">
                                    <?= strtoupper($lang['code']) ?>
                                </button>
                                <?php endforeach; ?>
                            </div>

                            <?php foreach ($languages as $i => $lang): ?>
                            <div class="block-content p-4 <?= $i > 0 ? 'hidden' : '' ?>"
                                 data-block="<?= $blockKey ?>"
                                 data-lang="<?= $lang['id'] ?>">
                                <?php
                                $value = $contentBlocks[$blockKey][$lang['id']] ?? '';

                                switch ($blockDef['type']):
                                    case 'text':
                                ?>
                                <input type="text"
                                       name="<?= $blockKey ?>_<?= $lang['id'] ?>"
                                       value="<?= e($value) ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500 focus:border-secondary-500">
                                <?php break; case 'textarea': ?>
                                <textarea name="<?= $blockKey ?>_<?= $lang['id'] ?>"
                                          rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500 focus:border-secondary-500"><?= e($value) ?></textarea>
                                <?php break; case 'wysiwyg': ?>
                                <textarea name="<?= $blockKey ?>_<?= $lang['id'] ?>"
                                          rows="8"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500 focus:border-secondary-500"><?= e($value) ?></textarea>
                                <p class="text-xs text-gray-500 mt-1">Pode usar HTML basico: &lt;p&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;br&gt;</p>
                                <?php break; endswitch; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <button type="submit" class="px-6 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700">
                        Guardar Alteracoes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function switchBlockTab(blockKey, langId) {
    // Update tabs for this block
    document.querySelectorAll(`.block-tab[data-block="${blockKey}"]`).forEach(tab => {
        if (tab.dataset.lang === langId) {
            tab.classList.add('bg-white', 'text-secondary-600', 'border-b-2', 'border-secondary-600');
            tab.classList.remove('text-gray-500');
        } else {
            tab.classList.remove('bg-white', 'text-secondary-600', 'border-b-2', 'border-secondary-600');
            tab.classList.add('text-gray-500');
        }
    });

    // Update content for this block
    document.querySelectorAll(`.block-content[data-block="${blockKey}"]`).forEach(content => {
        if (content.dataset.lang === langId) {
            content.classList.remove('hidden');
        } else {
            content.classList.add('hidden');
        }
    });
}
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
