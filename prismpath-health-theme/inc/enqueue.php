<?php
/**
 * Asset loading.
 *
 * @package Prismpath_Health
 */

if (!defined('ABSPATH')) {
    exit;
}

function prismpath_disable_remote_emoji_assets(): void
{
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('init', 'prismpath_disable_remote_emoji_assets');
add_filter('emoji_svg_url', '__return_false');

function prismpath_remove_remote_resource_hints(array $urls, string $relation_type): array
{
    if ('dns-prefetch' !== $relation_type && 'preconnect' !== $relation_type) {
        return $urls;
    }

    return array_values(array_filter($urls, static function ($url): bool {
        $href = is_array($url) ? (string) ($url['href'] ?? '') : (string) $url;
        return false === strpos($href, 's.w.org')
            && false === strpos($href, 'fonts.googleapis.com')
            && false === strpos($href, 'fonts.gstatic.com');
    }));
}
add_filter('wp_resource_hints', 'prismpath_remove_remote_resource_hints', 10, 2);

function prismpath_render_local_icons(): void
{
    $icon_base = PRISMPATH_THEME_URI . '/assets/icons/';
    echo '<link rel="icon" href="' . esc_url($icon_base . 'favicon.ico') . '" sizes="any">' . "\n";
    echo '<link rel="icon" href="' . esc_url($icon_base . 'favicon.svg') . '" type="image/svg+xml">' . "\n";
    echo '<link rel="icon" href="' . esc_url($icon_base . 'favicon-32x32.png') . '" sizes="32x32" type="image/png">' . "\n";
    echo '<link rel="apple-touch-icon" href="' . esc_url($icon_base . 'apple-touch-icon.png') . '" sizes="180x180">' . "\n";
    echo '<link rel="manifest" href="' . esc_url($icon_base . 'site.webmanifest') . '">' . "\n";
    echo '<meta name="theme-color" content="#07565a">' . "\n";
}
add_action('wp_head', 'prismpath_render_local_icons', 1);

function prismpath_enqueue_assets(): void
{
    $css_path = PRISMPATH_THEME_DIR . '/assets/css/main.min.css';
    if (!file_exists($css_path)) {
        $css_path = PRISMPATH_THEME_DIR . '/assets/css/main.css';
    }
    $css_version = file_exists($css_path) ? (string) filemtime($css_path) : PRISMPATH_VERSION;
    $inline_css = file_exists($css_path) ? file_get_contents($css_path) : false;
    if (is_string($inline_css) && '' !== trim($inline_css)) {
        wp_register_style('prismpath-main', false, array(), $css_version);
        wp_enqueue_style('prismpath-main');
        wp_add_inline_style('prismpath-main', $inline_css);
    } else {
        wp_enqueue_style(
            'prismpath-main',
            PRISMPATH_THEME_URI . '/assets/css/' . basename($css_path),
            array(),
            $css_version
        );
    }

    $js_path = PRISMPATH_THEME_DIR . '/assets/js/main.js';
    $js_version = file_exists($js_path) ? (string) filemtime($js_path) : PRISMPATH_VERSION;
    $script_data = array(
        'homeUrl' => home_url('/'),
        'consultSelector' => '#consult',
    );
    $inline_js = file_exists($js_path) ? file_get_contents($js_path) : false;
    if (is_string($inline_js) && '' !== trim($inline_js)) {
        wp_register_script('prismpath-main', false, array(), $js_version, true);
        wp_enqueue_script('prismpath-main');
        wp_add_inline_script('prismpath-main', 'window.prismpathData = ' . wp_json_encode($script_data) . ';', 'before');
        wp_add_inline_script('prismpath-main', $inline_js);
    } else {
        wp_enqueue_script(
            'prismpath-main',
            PRISMPATH_THEME_URI . '/assets/js/main.js',
            array(),
            $js_version,
            true
        );
        wp_script_add_data('prismpath-main', 'defer', true);
        wp_localize_script('prismpath-main', 'prismpathData', $script_data);
    }
}
add_action('wp_enqueue_scripts', 'prismpath_enqueue_assets');
