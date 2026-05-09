<?php
/**
 * Seed editable page metabox values from the default content model.
 *
 * @package Prismpath_Health
 */

if (!defined('ABSPATH')) {
    exit;
}

function prismpath_seed_meta_if_empty(int $post_id, string $meta_key, string $value): void
{
    if ('' === trim($value)) {
        return;
    }

    $current = get_post_meta($post_id, $meta_key, true);
    if ('' !== trim((string) $current)) {
        return;
    }

    update_post_meta($post_id, $meta_key, $value);
}

function prismpath_seed_format_sections(array $sections): string
{
    $blocks = array();
    foreach ($sections as $section) {
        if (empty($section['heading']) || empty($section['body'])) {
            continue;
        }
        $blocks[] = trim((string) $section['heading']) . "\n" . trim((string) $section['body']);
    }

    return implode("\n\n", $blocks);
}

function prismpath_seed_format_faqs(array $faqs): string
{
    $blocks = array();
    foreach ($faqs as $faq) {
        if (empty($faq['question']) || empty($faq['answer'])) {
            continue;
        }
        $blocks[] = trim((string) $faq['question']) . "\n" . trim((string) $faq['answer']);
    }

    return implode("\n\n", $blocks);
}

function prismpath_seed_page_editor_meta(int $post_id, array $content): void
{
    prismpath_seed_meta_if_empty($post_id, '_prismpath_seo_title', (string) ($content['seo_title'] ?? ''));
    prismpath_seed_meta_if_empty($post_id, 'meta_description', (string) ($content['meta_description'] ?? ''));
    prismpath_seed_meta_if_empty($post_id, '_prismpath_page_hero_title', (string) ($content['title'] ?? ''));
    prismpath_seed_meta_if_empty($post_id, '_prismpath_page_intro', (string) ($content['intro'] ?? ''));
    prismpath_seed_meta_if_empty($post_id, '_prismpath_page_panel_heading', (string) ($content['panel_heading'] ?? 'Care shaped around real life.'));
    prismpath_seed_meta_if_empty($post_id, '_prismpath_page_panel_body', (string) ($content['panel_body'] ?? 'We combine clinical insight, practical strategy, and respectful collaboration so care feels usable outside the session too.'));

    if (!empty($content['points']) && is_array($content['points'])) {
        prismpath_seed_meta_if_empty($post_id, '_prismpath_page_points', implode("\n", array_map('strval', $content['points'])));
    }

    if (!empty($content['sections']) && is_array($content['sections'])) {
        prismpath_seed_meta_if_empty($post_id, '_prismpath_page_sections', prismpath_seed_format_sections($content['sections']));
    }

    if (!empty($content['faqs']) && is_array($content['faqs'])) {
        prismpath_seed_meta_if_empty($post_id, '_prismpath_page_faqs', prismpath_seed_format_faqs($content['faqs']));
    }
}

function prismpath_seed_all_page_editor_meta(array $page_ids_by_slug): void
{
    foreach (prismpath_default_pages() as $slug => $content) {
        $post_id = (int) ($page_ids_by_slug[$slug] ?? 0);
        if (!$post_id) {
            $page = get_page_by_path($slug);
            $post_id = $page instanceof WP_Post ? (int) $page->ID : 0;
        }

        if ($post_id) {
            prismpath_seed_page_editor_meta($post_id, $content);
        }
    }
}

function prismpath_seed_static_page_seo_meta_if_empty(): void
{
    if (!function_exists('prismpath_static_page_seo')) {
        return;
    }

    foreach (array('services', 'insurance-payment', 'team', 'contact', 'privacy-policy', 'hipaa-policy', 'accessibility-statement') as $slug) {
        $page = get_page_by_path($slug);
        $content = prismpath_static_page_seo($slug);
        if (!$page instanceof WP_Post || !is_array($content)) {
            continue;
        }

        prismpath_seed_meta_if_empty((int) $page->ID, '_prismpath_seo_title', (string) ($content['seo_title'] ?? ''));
        prismpath_seed_meta_if_empty((int) $page->ID, 'meta_description', (string) ($content['meta_description'] ?? ''));
    }
}

function prismpath_seed_existing_page_meta_once(): void
{
    $version = '2026-05-09-local-completion-v2';
    if (get_option('prismpath_existing_page_meta_seed_version') === $version) {
        return;
    }

    prismpath_seed_all_page_editor_meta(array());
    prismpath_seed_static_page_seo_meta_if_empty();
    if (function_exists('prismpath_retire_default_hello_world_post')) {
        prismpath_retire_default_hello_world_post();
    }
    update_option('prismpath_existing_page_meta_seed_version', $version, false);
}
add_action('init', 'prismpath_seed_existing_page_meta_once', 30);
