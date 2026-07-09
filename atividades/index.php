<?php

require_once dirname(__DIR__) . '/includes/init.php';

use Core\Language;
use Core\Database;

$lang = Language::getInstance();
$db = Database::getInstance();
$base = basePath();

// Slugs de atividades antigas -> redirecionar para a nova página informativa
if (!empty($_GET['slug'])) {
    header('Location: ' . $base . '/atividades/', true, 301);
    exit;
}

$pageHero = $db->fetch("SELECT * FROM page_heroes WHERE page_key = 'activities' AND is_active = 1");
$heroMedia = $pageHero ? $db->fetch("SELECT * FROM media WHERE entity_type = 'hero' AND entity_id = ? AND is_cover = 1", [$pageHero['id']]) : null;
$heroImage = $heroMedia['file_path'] ?? 'images/MogadouroAtividades.webp';
$heroOverlay = $pageHero['hero_overlay_opacity'] ?? 0.45;
$heroUrl = $heroImage[0] === '/' ? basePath() . $heroImage : asset($heroImage);

$pageTitle = 'O Que Fazer em Mogadouro';
$pageDescription = 'Descubra o que visitar e fazer em Mogadouro e Trás-os-Montes através da Câmara Municipal e do Posto de Turismo de Mogadouro.';

// Recursos oficiais + guias de referência (geríveis no admin » Atividades)
$officialLinks = [];
$guideLinks = [];
try {
    $linkRows = $db->fetchAll("SELECT * FROM activity_links WHERE is_active = 1 ORDER BY sort_order, id");
    foreach ($linkRows as $r) {
        if ($r['section'] === 'guide') {
            $guideLinks[] = ['title' => $r['title_pt'], 'url' => $r['url']];
        } else {
            $officialLinks[] = ['title' => $r['title_pt'], 'desc' => $r['desc_pt'], 'url' => $r['url'], 'tag' => $r['tag_pt']];
        }
    }
} catch (\Throwable $e) { /* tabela ainda não migrada — usa fallback abaixo */ }

if (empty($officialLinks)) {
    $officialLinks = [
        ['title' => 'Câmara Municipal de Mogadouro', 'desc' => 'Informação oficial do concelho: o que visitar, património, eventos e contactos.', 'url' => 'https://www.mogadouro.pt/', 'tag' => 'Site Oficial'],
        ['title' => 'Posto de Turismo de Mogadouro', 'desc' => 'Loja Interativa de Turismo — pontos de interesse, percursos e apoio ao visitante.', 'url' => 'https://www.mogadouro.pt/pages/17', 'tag' => 'Turismo'],
    ];
}
if (empty($guideLinks)) {
    $guideLinks = [
        ['title' => 'Roteiro por Mogadouro — Vagamundos', 'url' => 'https://www.vagamundos.pt/visitar-mogadouro-roteiro/'],
        ['title' => 'Atrações em torno de Mogadouro — Komoot', 'url' => 'https://www.komoot.com/pt-pt/guide/900754/atracoes-em-torno-de-mogadouro'],
        ['title' => 'Mogadouro — Tripadvisor', 'url' => 'https://www.tripadvisor.pt/Attractions-g1458520-Activities-Mogadouro_Braganca_District_Northern_Portugal.html'],
    ];
}

include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section -->
<section class="relative h-screen flex items-center bg-primary overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat"
             style="background-image: url('<?= $heroUrl ?>');"></div>
        <div class="absolute inset-0 bg-black" style="opacity: <?= $heroOverlay ?>"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-black/40"></div>
    </div>

    <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
        <span class="inline-block text-accent text-lg font-medium tracking-[0.2em] uppercase mb-4">
            Descubra Mogadouro
        </span>
        <h1 class="font-cursive text-6xl md:text-7xl lg:text-8xl text-cream mb-6 drop-shadow-xl">
            O Que Fazer
        </h1>
        <p class="text-xl md:text-2xl text-cream/90 max-w-2xl mx-auto font-light leading-relaxed">
            Da natureza à gastronomia, história e cultura em Trás-os-Montes.
        </p>
    </div>
</section>

<!-- Recursos Oficiais -->
<section class="py-16 lg:py-24 bg-cream-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="inline-block text-accent text-sm font-medium tracking-[0.2em] uppercase mb-3">Informação Oficial</span>
            <h2 class="font-serif text-3xl md:text-4xl text-primary mb-4">Planeie a sua visita</h2>
            <p class="text-charcoal/70 max-w-2xl mx-auto">
                Para atividades, pontos de interesse e eventos atualizados, consulte as entidades oficiais de Mogadouro.
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <?php foreach ($officialLinks as $link): ?>
            <a href="<?= e($link['url']) ?>" target="_blank" rel="noopener noreferrer"
               class="group bg-white rounded-2xl border border-cream-200 p-8 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">
                <span class="inline-block self-start text-[11px] font-bold uppercase tracking-widest text-secondary bg-secondary/10 px-3 py-1 rounded-full mb-4"><?= e($link['tag']) ?></span>
                <h3 class="font-serif text-2xl text-primary mb-3 group-hover:text-secondary transition-colors"><?= e($link['title']) ?></h3>
                <p class="text-charcoal/70 leading-relaxed mb-6 flex-1"><?= e($link['desc']) ?></p>
                <span class="inline-flex items-center gap-2 text-secondary font-medium">
                    Visitar
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </span>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Guias de referência -->
        <div class="mt-14">
            <h3 class="font-serif text-xl text-primary mb-6 text-center">Guias e roteiros úteis</h3>
            <div class="flex flex-wrap justify-center gap-3">
                <?php foreach ($guideLinks as $g): ?>
                <a href="<?= e($g['url']) ?>" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-2 px-5 py-3 bg-white border border-cream-200 rounded-xl text-charcoal/80 hover:border-secondary hover:text-secondary hover:shadow-md transition-all">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 010 5.656l-3 3a4 4 0 01-5.656-5.656l1.5-1.5m6.828-.828a4 4 0 010-5.656l3-3a4 4 0 015.656 5.656l-1.5 1.5"/>
                    </svg>
                    <span class="text-sm font-medium"><?= e($g['title']) ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
