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
