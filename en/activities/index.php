<?php
/**
 * A Casa do Gi - Activities Page (English)
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';

use Core\Language;

// Force English language
Language::getInstance()->setLanguage(LANG_EN);
$lang = Language::getInstance();
$base = basePath();

// Get page content
$content = $lang->getPageContents('activities');

// Page configuration
$pageTitle = 'Things To Do in Mogadouro';
$pageDescription = 'Discover the best activities and tourist attractions in Mogadouro and Tras-os-Montes. Nature, gastronomy, history and culture.';

include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section -->
<section class="relative py-20 lg:py-32 bg-primary overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmZmZmYiIGZpbGwtb3BhY2l0eT0iMC4xIj48cGF0aCBkPSJNMzYgMzRjMC0yLjIwOS0xLjc5MS00LTQtNHMtNCAxLjc5MS00IDQgMS43OTEgNCA0IDQgNC0xLjc5MSA0LTR6Ii8+PC9nPjwvZz48L3N2Zz4=')]"></div>
    </div>
    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-b from-primary/50 to-primary"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
        <span class="inline-block text-accent text-lg font-medium tracking-[0.2em] uppercase mb-4 animate-fade-in">
            Discover Mogadouro
        </span>
        <h1 class="font-cursive text-6xl md:text-7xl lg:text-8xl text-cream mb-6 drop-shadow-lg">
            Things To Do
        </h1>
        <p class="text-xl md:text-2xl text-cream/90 max-w-3xl mx-auto font-light leading-relaxed">
            Explore everything the region has to offer - from stunning nature to rich history and delicious gastronomy.
        </p>
    </div>
</section>

<!-- Category Filter -->
<section class="py-8 bg-cream border-b border-accent/20 sticky top-20 z-40 shadow-sm backdrop-blur-md bg-cream/95">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-center gap-4">
            <button class="activity-filter active px-8 py-3 rounded-full text-sm font-bold tracking-wide uppercase transition-all duration-300 bg-secondary text-cream shadow-md hover:scale-105 hover:bg-secondary-600" data-filter="all">
                All
            </button>
            <button class="activity-filter px-8 py-3 rounded-full text-sm font-bold tracking-wide uppercase transition-all duration-300 bg-cream-100 text-charcoal hover:bg-accent hover:text-white border border-charcoal/5 hover:border-transparent hover:shadow-md hover:scale-105" data-filter="nature">
                Nature
            </button>
            <button class="activity-filter px-8 py-3 rounded-full text-sm font-bold tracking-wide uppercase transition-all duration-300 bg-cream-100 text-charcoal hover:bg-accent hover:text-white border border-charcoal/5 hover:border-transparent hover:shadow-md hover:scale-105" data-filter="culture">
                Culture
            </button>
            <button class="activity-filter px-8 py-3 rounded-full text-sm font-bold tracking-wide uppercase transition-all duration-300 bg-cream-100 text-charcoal hover:bg-accent hover:text-white border border-charcoal/5 hover:border-transparent hover:shadow-md hover:scale-105" data-filter="gastronomy">
                Gastronomy
            </button>
        </div>
    </div>
</section>

<!-- Activities Grid -->
<section class="py-16 lg:py-24 bg-cream-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8" id="activities-grid">
            
            <!-- Nature -->
            <article class="activity-card bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow" data-category="nature">
                <div class="aspect-[4/3] relative overflow-hidden">
                    <div class="w-full h-full bg-gradient-to-br from-accent/20 to-accent/40 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    <span class="absolute top-4 left-4 bg-secondary text-cream text-xs font-medium px-3 py-1 rounded-full">
                        Nature
                    </span>
                </div>
                <div class="p-6">
                    <h3 class="font-serif text-xl text-primary mb-2">Nature & Trails</h3>
                    <p class="text-charcoal text-sm mb-4">
                        Explore the Douro International Natural Park, hiking trails and breathtaking viewpoints.
                    </p>
                    <ul class="text-sm text-charcoal/70 space-y-1 ml-4 list-disc">
                        <li>Medal Serpent Viewpoint</li>
                        <li>Douro International Natural Park</li>
                        <li>Walking and hiking trails</li>
                    </ul>
                </div>
            </article>

            <!-- Culture -->
            <article class="activity-card bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow" data-category="culture">
                <div class="aspect-[4/3] relative overflow-hidden">
                    <div class="w-full h-full bg-gradient-to-br from-terracotta-200 to-granite-400 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <span class="absolute top-4 left-4 bg-terracotta-500 text-cream text-xs font-medium px-3 py-1 rounded-full">
                        Culture
                    </span>
                </div>
                <div class="p-6">
                    <h3 class="font-serif text-xl text-primary mb-2">History & Culture</h3>
                    <p class="text-charcoal text-sm mb-4">
                        Discover centuries of history through castles, churches and traditional villages.
                    </p>
                    <ul class="text-sm text-charcoal/70 space-y-1 ml-4 list-disc">
                        <li>Mogadouro Castle (13th century)</li>
                        <li>Mother Church</li>
                        <li>Traditional villages</li>
                    </ul>
                </div>
            </article>

            <!-- Gastronomy -->
            <article class="activity-card bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow" data-category="gastronomy">
                <div class="aspect-[4/3] relative overflow-hidden">
                    <div class="w-full h-full bg-gradient-to-br from-olive-200 to-wood-400 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="absolute top-4 left-4 bg-wood-500 text-cream text-xs font-medium px-3 py-1 rounded-full">
                        Gastronomy
                    </span>
                </div>
                <div class="p-6">
                    <h3 class="font-serif text-xl text-primary mb-2">Gastronomy</h3>
                    <p class="text-charcoal text-sm mb-4">
                        Taste the authentic flavors of Transmontana cuisine - cured meats, olive oil, honey and regional wines.
                    </p>
                    <ul class="text-sm text-charcoal/70 space-y-1 ml-4 list-disc">
                        <li>Traditional restaurants</li>
                        <li>Local markets</li>
                        <li>Wine tastings</li>
                    </ul>
                </div>
            </article>

        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 lg:py-24 bg-secondary">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="font-serif text-3xl md:text-4xl text-cream mb-6">
            Plan Your Visit
        </h2>
        <p class="text-xl text-accent/80 mb-10">
            Stay at A Casa do Gi and explore everything Mogadouro has to offer.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="<?= $base ?>/en/accommodation/" class="inline-flex items-center px-8 py-4 bg-cream text-secondary font-medium rounded hover:bg-cream-100 transition-colors">
                View Accommodation
            </a>
            <a href="<?= $base ?>/en/contact/" class="inline-flex items-center px-8 py-4 bg-secondary text-cream font-medium rounded hover:bg-secondary-700 transition-colors border border-secondary">
                Contact Us
            </a>
        </div>
    </div>
</section>

<!-- Filter Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.activity-filter');
    const activityCards = document.querySelectorAll('.activity-card');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.dataset.filter;

            // Update active button
            filterButtons.forEach(btn => {
                btn.classList.remove('active', 'bg-secondary', 'text-cream', 'shadow-md', 'hover:bg-secondary-600');
                btn.classList.add('bg-cream-100', 'text-charcoal');
            });
            this.classList.add('active', 'bg-secondary', 'text-cream', 'shadow-md', 'hover:bg-secondary-600');
            this.classList.remove('bg-cream-100', 'text-charcoal');

            // Filter cards
            activityCards.forEach(card => {
                if (filter === 'all' || card.dataset.category === filter) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeIn 0.3s ease-in-out';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<?php include INCLUDES_PATH . '/footer.php'; ?>
