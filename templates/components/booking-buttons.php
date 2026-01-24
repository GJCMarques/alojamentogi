<?php
/**
 * A Casa do Gi - Booking Buttons Component
 *
 * Reusable component for booking platform buttons with official brand colors and logos.
 *
 * Usage:
 *   component('booking-buttons', [
 *       'layout' => 'vertical', // 'vertical', 'horizontal', 'grid'
 *       'size' => 'default',    // 'small', 'default', 'large'
 *       'show_labels' => true,  // Show "Direto" / "Parceiro" labels
 *       'platforms' => ['guestready', 'booking', 'airbnb'] // Which platforms to show
 *   ]);
 */

$layout = $layout ?? 'vertical';
$size = $size ?? 'default';
$showLabels = $show_labels ?? true;
$platforms = $platforms ?? ['guestready', 'booking', 'airbnb'];

$lang = \Core\Language::getInstance();
$isEnglish = $lang->isEnglish();

// Get URLs from settings
$guestreadyUrl = setting('guestready_url', '');
$bookingUrl = setting('booking_url', '');
$airbnbUrl = setting('airbnb_url', '');

// Size classes
$sizeClasses = [
    'small' => 'p-2.5 text-sm',
    'default' => 'p-3.5',
    'large' => 'p-4 text-lg',
];

$iconSizes = [
    'small' => 'w-8 h-8',
    'default' => 'w-10 h-10',
    'large' => 'w-12 h-12',
];

$iconInnerSizes = [
    'small' => 'w-4 h-4',
    'default' => 'w-5 h-5',
    'large' => 'w-6 h-6',
];

$currentSize = $sizeClasses[$size] ?? $sizeClasses['default'];
$currentIconSize = $iconSizes[$size] ?? $iconSizes['default'];
$currentIconInnerSize = $iconInnerSizes[$size] ?? $iconInnerSizes['default'];

// Layout classes
$containerClasses = [
    'vertical' => 'flex flex-col space-y-3',
    'horizontal' => 'flex flex-wrap gap-3',
    'grid' => 'grid grid-cols-1 sm:grid-cols-3 gap-3',
];

$buttonClasses = [
    'vertical' => 'w-full',
    'horizontal' => 'flex-1 min-w-[180px]',
    'grid' => 'w-full',
];

$currentContainer = $containerClasses[$layout] ?? $containerClasses['vertical'];
$currentButton = $buttonClasses[$layout] ?? $buttonClasses['vertical'];

// Base button classes
$baseButtonClasses = "flex items-center {$currentSize} rounded-lg shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 {$currentButton}";
?>

<div class="booking-buttons <?= $currentContainer ?>">

    <?php $base = basePath(); ?>

    <?php if (in_array('guestready', $platforms) && $guestreadyUrl): ?>
    <!-- GuestReady - Direct Booking (Cream #FAF9F6 with Bordeaux Text #800020) -->
    <a href="<?= e($guestreadyUrl) ?>"
       target="_blank"
       rel="noopener noreferrer"
       class="<?= $baseButtonClasses ?> bg-[#FAF9F6] hover:bg-[#EAE8E0] text-[#800020] group border border-[#800020]/10">
        <div class="<?= $currentIconSize ?> bg-[#800020]/10 rounded-lg flex items-center justify-center mr-3 group-hover:bg-[#800020]/20 transition-colors p-1">
            <!-- GuestReady Logo -->
            <img src="<?= $base ?>/assets/images/guestreadylogo.png" alt="GuestReady" class="w-full h-full object-contain">
        </div>
        <div class="flex flex-col">
            <?php if ($showLabels): ?>
            <span class="text-[10px] text-[#800020]/70 uppercase tracking-wider font-medium">
                <?= $isEnglish ? 'Direct' : 'Direto' ?>
            </span>
            <?php endif; ?>
            <span class="font-bold">GuestReady</span>
        </div>
        <svg class="w-4 h-4 ml-auto text-[#800020] opacity-50 group-hover:opacity-100 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>
    <?php endif; ?>

    <?php if (in_array('booking', $platforms) && $bookingUrl): ?>
    <!-- Booking.com - Official Blue (#003580) -->
    <a href="<?= e($bookingUrl) ?>"
       target="_blank"
       rel="noopener noreferrer"
       class="<?= $baseButtonClasses ?> bg-[#003580] hover:bg-[#00264d] text-white group">
        <div class="<?= $currentIconSize ?> bg-white/20 rounded-lg flex items-center justify-center mr-3 group-hover:bg-white/30 transition-colors p-1">
            <!-- Booking.com Logo -->
            <img src="<?= $base ?>/assets/images/bookinglogo.jpg" alt="Booking.com" class="w-full h-full object-contain">
        </div>
        <div class="flex flex-col">
            <?php if ($showLabels): ?>
            <span class="text-[10px] text-white/70 uppercase tracking-wider font-medium">
                <?= $isEnglish ? 'Partner' : 'Parceiro' ?>
            </span>
            <?php endif; ?>
            <span class="font-bold">Booking.com</span>
        </div>
        <svg class="w-4 h-4 ml-auto opacity-50 group-hover:opacity-100 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>
    <?php endif; ?>

    <?php if (in_array('airbnb', $platforms) && $airbnbUrl): ?>
    <!-- Airbnb - Official Red (#FF385C) -->
    <a href="<?= e($airbnbUrl) ?>"
       target="_blank"
       rel="noopener noreferrer"
       class="<?= $baseButtonClasses ?> bg-[#FF385C] hover:bg-[#e62e50] text-white group">
        <div class="<?= $currentIconSize ?> bg-white/20 rounded-lg flex items-center justify-center mr-3 group-hover:bg-white/30 transition-colors p-1">
            <!-- Airbnb Logo -->
            <img src="<?= $base ?>/assets/images/airbnblogo.png" alt="Airbnb" class="w-full h-full object-contain">
        </div>
        <div class="flex flex-col">
            <?php if ($showLabels): ?>
            <span class="text-[10px] text-white/70 uppercase tracking-wider font-medium">
                <?= $isEnglish ? 'Partner' : 'Parceiro' ?>
            </span>
            <?php endif; ?>
            <span class="font-bold">Airbnb</span>
        </div>
        <svg class="w-4 h-4 ml-auto opacity-50 group-hover:opacity-100 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>
    <?php endif; ?>

</div>
