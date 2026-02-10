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
        'label' => 'Página Inicial',
        'blocks' => [
            'home_hero_subtitle' => ['label' => 'Hero - Subtítulo', 'type' => 'text'],
            'home_split_left_label' => ['label' => 'Split Hero - Label Esquerdo', 'type' => 'text'],
            'home_split_left_title' => ['label' => 'Split Hero - Título Esquerdo', 'type' => 'text'],
            'home_split_right_label' => ['label' => 'Split Hero - Label Direito', 'type' => 'text'],
            'home_split_right_title' => ['label' => 'Split Hero - Título Direito', 'type' => 'text'],
            'home_explore_title' => ['label' => 'Explorar - Título Secção', 'type' => 'text'],
            'home_card1_label' => ['label' => 'Card 1 (Alojamento) - Label', 'type' => 'text'],
            'home_card1_title' => ['label' => 'Card 1 (Alojamento) - Título', 'type' => 'text'],
            'home_card1_text' => ['label' => 'Card 1 (Alojamento) - Texto', 'type' => 'text'],
            'home_card1_cta' => ['label' => 'Card 1 (Alojamento) - Botão', 'type' => 'text'],
            'home_card2_label' => ['label' => 'Card 2 (Atividades) - Label', 'type' => 'text'],
            'home_card2_title' => ['label' => 'Card 2 (Atividades) - Título', 'type' => 'text'],
            'home_card2_text' => ['label' => 'Card 2 (Atividades) - Texto', 'type' => 'text'],
            'home_card2_cta' => ['label' => 'Card 2 (Atividades) - Botão', 'type' => 'text'],
            'home_card3_label' => ['label' => 'Card 3 (Loja) - Label', 'type' => 'text'],
            'home_card3_title' => ['label' => 'Card 3 (Loja) - Título', 'type' => 'text'],
            'home_card3_text' => ['label' => 'Card 3 (Loja) - Texto', 'type' => 'text'],
            'home_card3_cta' => ['label' => 'Card 3 (Loja) - Botão', 'type' => 'text'],
            'home_card4_label' => ['label' => 'Card 4 (Contactos) - Label', 'type' => 'text'],
            'home_card4_title' => ['label' => 'Card 4 (Contactos) - Título', 'type' => 'text'],
            'home_card4_text' => ['label' => 'Card 4 (Contactos) - Texto', 'type' => 'text'],
            'home_card4_cta' => ['label' => 'Card 4 (Contactos) - Botão', 'type' => 'text'],
            'home_about_label' => ['label' => 'Sobre Nós - Label', 'type' => 'text'],
            'home_about_title' => ['label' => 'Sobre Nós - Título (HTML)', 'type' => 'wysiwyg'],
            'home_about_text1' => ['label' => 'Sobre Nós - Parágrafo 1', 'type' => 'textarea'],
            'home_about_text2' => ['label' => 'Sobre Nós - Parágrafo 2', 'type' => 'textarea'],
            'home_about_cta' => ['label' => 'Sobre Nós - Botão', 'type' => 'text'],
        ]
    ],
    'accommodation' => [
        'label' => 'Alojamento',
        'blocks' => [
            'accommodation_hero_tagline' => ['label' => 'Hero - Tagline', 'type' => 'text'],
            'accommodation_hero_title' => ['label' => 'Hero - Título', 'type' => 'text'],
            'accommodation_hero_subtitle' => ['label' => 'Hero - Subtítulo', 'type' => 'textarea'],
            
            'accommodation_section_subtitle' => ['label' => 'Secção Escolha - Subtítulo', 'type' => 'text'],
            'accommodation_section_title' => ['label' => 'Secção Escolha - Título', 'type' => 'text'],
            'accommodation_intro' => ['label' => 'Secção Escolha - Texto', 'type' => 'textarea'],

            'accommodation_features_title' => ['label' => 'Features - Título', 'type' => 'text'],
            'accommodation_feature_1' => ['label' => 'Feature 1 - Label', 'type' => 'text'],
            'accommodation_feature_2' => ['label' => 'Feature 2 - Label', 'type' => 'text'],
            'accommodation_feature_3' => ['label' => 'Feature 3 - Label', 'type' => 'text'],
            'accommodation_feature_4' => ['label' => 'Feature 4 - Label', 'type' => 'text'],
        ]
    ],
    'activities' => [
        'label' => 'Atividades',
        'blocks' => [
            'activities_hero_tagline' => ['label' => 'Hero - Tagline', 'type' => 'text'],
            'activities_hero_title' => ['label' => 'Hero - Título', 'type' => 'text'],
            'activities_hero_subtitle' => ['label' => 'Hero - Subtítulo', 'type' => 'textarea'],
        ]
    ],
    'shop' => [
        'label' => 'Loja',
        'blocks' => [
            'shop_intro' => ['label' => 'Introdução', 'type' => 'textarea'],
            'shop_empty_message' => ['label' => 'Mensagem Vazio', 'type' => 'text'],
        ]
    ],
    'about' => [
        'label' => 'Sobre Nós',
        'blocks' => [
            'about_hero_tagline' => ['label' => 'Hero - Tagline', 'type' => 'text'],
            'about_hero_title' => ['label' => 'Hero - Título', 'type' => 'text'],
            'about_hero_subtitle' => ['label' => 'Hero - Subtítulo', 'type' => 'textarea'],

            'about_origin_label' => ['label' => 'Origem - Label', 'type' => 'text'],
            'about_origin_title' => ['label' => 'Origem - Título (HTML)', 'type' => 'wysiwyg'],
            'about_origin_text1' => ['label' => 'Origem - Parágrafo 1', 'type' => 'textarea'],
            'about_origin_text2' => ['label' => 'Origem - Parágrafo 2', 'type' => 'textarea'],
            'about_origin_caption' => ['label' => 'Origem - Legenda Foto', 'type' => 'text'],
            'about_origin_signature' => ['label' => 'Origem - Assinatura', 'type' => 'text'],
            'about_values_label' => ['label' => 'Valores - Label', 'type' => 'text'],
            'about_values_title' => ['label' => 'Valores - Título (HTML)', 'type' => 'wysiwyg'],
            'about_values_intro' => ['label' => 'Valores - Texto Introdução', 'type' => 'textarea'],
            'about_value1_title' => ['label' => 'Valor 1 - Título', 'type' => 'text'],
            'about_value1_text' => ['label' => 'Valor 1 - Texto', 'type' => 'textarea'],
            'about_value2_title' => ['label' => 'Valor 2 - Título', 'type' => 'text'],
            'about_value2_text' => ['label' => 'Valor 2 - Texto', 'type' => 'textarea'],
            'about_value3_title' => ['label' => 'Valor 3 - Título', 'type' => 'text'],
            'about_value3_text' => ['label' => 'Valor 3 - Texto', 'type' => 'textarea'],
            'about_value4_title' => ['label' => 'Valor 4 - Título', 'type' => 'text'],
            'about_value4_text' => ['label' => 'Valor 4 - Texto', 'type' => 'textarea'],
            'about_region_label' => ['label' => 'Região - Label', 'type' => 'text'],
            'about_region_text' => ['label' => 'Região - Texto', 'type' => 'textarea'],
            'about_region_cta1' => ['label' => 'Região - Botão 1', 'type' => 'text'],
            'about_region_cta2' => ['label' => 'Região - Botão 2', 'type' => 'text'],
        ]
    ],
    'contact' => [
        'label' => 'Contactos',
        'blocks' => [
            'contact_hero_tagline' => ['label' => 'Hero - Tagline', 'type' => 'text'],
            'contact_hero_title' => ['label' => 'Hero - Título', 'type' => 'text'],
            'contact_hero_subtitle' => ['label' => 'Hero - Subtítulo', 'type' => 'textarea'],
            'contact_success_message' => ['label' => 'Mensagem Sucesso', 'type' => 'textarea'],
        ]
    ],
    'privacy_policy' => [
        'label' => 'Política de Privacidade',
        'blocks' => [
            'privacy_hero_tagline' => ['label' => 'Hero - Tagline', 'type' => 'text'],
            'privacy_hero_title' => ['label' => 'Hero - Título', 'type' => 'text'],
            'privacy_hero_subtitle' => ['label' => 'Hero - Subtítulo', 'type' => 'textarea'],
            'privacy_date' => ['label' => 'Data de Atualização', 'type' => 'text'],
            'privacy_content' => ['label' => 'Conteúdo Principal', 'type' => 'wysiwyg'],
        ]
    ],
    'terms_conditions' => [
        'label' => 'Termos e Condições',
        'blocks' => [
            'terms_hero_tagline' => ['label' => 'Hero - Tagline', 'type' => 'text'],
            'terms_hero_title' => ['label' => 'Hero - Título', 'type' => 'text'],
            'terms_hero_subtitle' => ['label' => 'Hero - Subtítulo', 'type' => 'textarea'],
            'terms_date' => ['label' => 'Data de Atualização', 'type' => 'text'],
            'terms_content' => ['label' => 'Conteúdo Principal', 'type' => 'wysiwyg'],
        ]
    ],
    'footer' => [
        'label' => 'Rodapé',
        'blocks' => [
            'footer_description' => ['label' => 'Descrição Marca', 'type' => 'textarea'],
            'footer_quicklinks_title' => ['label' => 'Título Links Rápidos', 'type' => 'text'],
            'footer_contact_title' => ['label' => 'Título Contactos', 'type' => 'text'],
            'footer_address' => ['label' => 'Morada', 'type' => 'text'],
            'footer_book_title' => ['label' => 'Título Reservas', 'type' => 'text'],
            'footer_rights_text' => ['label' => 'Texto Direitos Reservados', 'type' => 'text'],
            'cookie_banner_text' => ['label' => 'Texto Banner Cookies', 'type' => 'wysiwyg'],
            'cookie_banner_accept' => ['label' => 'Botão Aceitar Cookies', 'type' => 'text'],
            'cookie_banner_details' => ['label' => 'Botão Detalhes Cookies', 'type' => 'text'],
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

        Session::flash('success', 'Conteúdos guardados com sucesso.');
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

$pageTitle = 'Conteúdos';
$currentPage = 'conteudos';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Gestão de Conteúdos</h1>
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
                                <p class="text-xs text-gray-500 mt-1">Pode usar HTML básico: &lt;p&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;br&gt;</p>
                                <?php break; endswitch; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <button type="submit" class="px-6 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700">
                        Guardar Alterações
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
