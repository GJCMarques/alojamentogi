<?php
/**
 * A Casa do Gi - Application Constants
 */

// Base paths
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}
define('CONFIG_PATH', ROOT_PATH . '/config');
define('CORE_PATH', ROOT_PATH . '/core');
define('MODELS_PATH', ROOT_PATH . '/models');
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('TEMPLATES_PATH', ROOT_PATH . '/templates');
define('ASSETS_PATH', ROOT_PATH . '/assets');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');
define('ADMIN_PATH', ROOT_PATH . '/admin');

// Upload subdirectories
define('UPLOADS_PRODUCTS', UPLOADS_PATH . '/products');
define('UPLOADS_GALLERY', UPLOADS_PATH . '/gallery');
define('UPLOADS_ACTIVITIES', UPLOADS_PATH . '/activities');
define('UPLOADS_CONTENT', UPLOADS_PATH . '/content');

// Languages
define('LANG_PT', 'pt');
define('LANG_EN', 'en');
define('DEFAULT_LANG', LANG_PT);

// Order statuses
define('ORDER_STATUS_PENDING', 'pending');
define('ORDER_STATUS_CONFIRMED', 'confirmed');
define('ORDER_STATUS_PROCESSING', 'processing');
define('ORDER_STATUS_SHIPPED', 'shipped');
define('ORDER_STATUS_DELIVERED', 'delivered');
define('ORDER_STATUS_CANCELLED', 'cancelled');

// Payment statuses
define('PAYMENT_STATUS_PENDING', 'pending');
define('PAYMENT_STATUS_PROCESSING', 'processing');
define('PAYMENT_STATUS_PAID', 'paid');
define('PAYMENT_STATUS_FAILED', 'failed');
define('PAYMENT_STATUS_REFUNDED', 'refunded');

// Payment methods
define('PAYMENT_METHOD_MBWAY', 'mbway');
define('PAYMENT_METHOD_MULTIBANCO', 'multibanco');
define('PAYMENT_METHOD_CARD', 'card');

// Admin roles
define('ROLE_SUPER_ADMIN', 'super_admin');
define('ROLE_ADMIN', 'admin');
define('ROLE_EDITOR', 'editor');

// Activity categories
define('ACTIVITY_NATURE', 'nature');
define('ACTIVITY_CULTURE', 'culture');
define('ACTIVITY_GASTRONOMY', 'gastronomy');
define('ACTIVITY_ADVENTURE', 'adventure');
define('ACTIVITY_WELLNESS', 'wellness');
define('ACTIVITY_EVENTS', 'events');

// Pagination defaults
define('ITEMS_PER_PAGE', 12);
define('ADMIN_ITEMS_PER_PAGE', 20);

// Session keys
define('SESSION_USER_ID', 'admin_id');
define('SESSION_USER_IP', 'admin_ip');
define('SESSION_CSRF_TOKEN', 'csrf_token');
define('SESSION_CSRF_TIME', 'csrf_time');
define('SESSION_LANG', 'lang');
define('SESSION_CART', 'cart');
define('SESSION_FLASH', 'flash_messages');

// Cache settings (for future use)
define('CACHE_ENABLED', false);
define('CACHE_TTL', 3600);
