<?php
/**
 * Asset loading.
 *
 * @package Prismpath_Health
 */

if (!defined('ABSPATH')) {
    exit;
}

function prismpath_enqueue_assets(): void
{
    wp_enqueue_style(
        'prismpath-fonts',
        'https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&family=Quicksand:wght@500;600;700&display=swap',
        array(),
        null
    );

    $css_path = PRISMPATH_THEME_DIR . '/assets/css/main.css';
    wp_enqueue_style(
        'prismpath-main',
        PRISMPATH_THEME_URI . '/assets/css/main.css',
        array('prismpath-fonts'),
        file_exists($css_path) ? (string) filemtime($css_path) : PRISMPATH_VERSION
    );

    $js_path = PRISMPATH_THEME_DIR . '/assets/js/main.js';
    wp_enqueue_script(
        'prismpath-main',
        PRISMPATH_THEME_URI . '/assets/js/main.js',
        array(),
        file_exists($js_path) ? (string) filemtime($js_path) : PRISMPATH_VERSION,
        true
    );

    wp_localize_script('prismpath-main', 'prismpathData', array(
        'homeUrl' => home_url('/'),
        'consultSelector' => '#consult',
    ));
}
add_action('wp_enqueue_scripts', 'prismpath_enqueue_assets');

function prismpath_resource_hints(array $urls, string $relation_type): array
{
    if ('preconnect' === $relation_type) {
        $urls[] = 'https://fonts.googleapis.com';
        $urls[] = array('href' => 'https://fonts.gstatic.com', 'crossorigin' => 'anonymous');
    }
    return $urls;
}
add_filter('wp_resource_hints', 'prismpath_resource_hints', 10, 2);
