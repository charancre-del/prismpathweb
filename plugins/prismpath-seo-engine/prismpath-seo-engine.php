<?php
/**
 * Plugin Name: Prismpath SEO Engine
 * Description: SEO metadata, schema, sitemap, robots, and legacy migration helpers for Prismpath Health.
 * Version: 1.0.0
 * Author: Prismpath Health
 * Text Domain: prismpath-seo-engine
 */

if (!defined('ABSPATH')) {
    exit;
}

function prismpath_seo_setting(string $key, string $default = ''): string
{
    if (function_exists('prismpath_setting')) {
        return prismpath_setting($key, $default);
    }
    $settings = get_option('prismpath_global_settings', array());
    $value = is_array($settings) ? ($settings[$key] ?? $default) : $default;
    return is_string($value) ? $value : $default;
}

function prismpath_seo_content_record(int $post_id): ?array
{
    if (!$post_id || !function_exists('prismpath_content_record_by_slug')) {
        return null;
    }

    $slug = get_post_field('post_name', $post_id);
    if (!$slug) {
        return null;
    }

    $record = prismpath_content_record_by_slug((string) $slug);
    return is_array($record) ? $record : null;
}

function prismpath_seo_document_title(string $title): string
{
    if (!is_singular()) {
        return $title;
    }

    $post_id = get_the_ID();
    $custom = get_post_meta($post_id, '_prismpath_seo_title', true);
    if ($custom) {
        return wp_strip_all_tags($custom);
    }

    $record = prismpath_seo_content_record((int) $post_id);
    if ($record && !empty($record['seo_title'])) {
        return wp_strip_all_tags($record['seo_title']);
    }

    return $title;
}
add_filter('pre_get_document_title', 'prismpath_seo_document_title');

function prismpath_seo_description(): string
{
    if (is_front_page()) {
        return 'Prismpath Health provides adult neuroaffirming therapy, psychiatric care, occupational therapy, ADHD and Autism assessments, and caregiver-centered whole-family mental health support.';
    }
    $custom = get_post_meta(get_the_ID(), 'meta_description', true);
    if ($custom) {
        return wp_strip_all_tags($custom);
    }
    $record = prismpath_seo_content_record((int) get_the_ID());
    if ($record && !empty($record['meta_description'])) {
        return wp_strip_all_tags($record['meta_description']);
    }
    if (has_excerpt()) {
        return wp_strip_all_tags(get_the_excerpt());
    }
    return 'A clearer path to neuroaffirming mental health care for every brain and every family.';
}

function prismpath_seo_meta_tags(): void
{
    if (is_admin() || is_feed()) {
        return;
    }
    $title = is_front_page() ? 'Prismpath Health | Neuroaffirming Mental Health Care' : wp_get_document_title();
    $description = prismpath_seo_description();
    $url = is_singular() ? get_permalink() : home_url(add_query_arg(array(), $GLOBALS['wp']->request ?? ''));
    $image = is_singular() && has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'full') : get_template_directory_uri() . '/assets/images/hero-family-prismpath-health.png';

    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    echo '<link rel="canonical" href="' . esc_url($url) . '">' . "\n";
    echo '<meta property="og:type" content="website">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
    echo '<meta property="og:site_name" content="Prismpath Health">' . "\n";
    echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";
    echo '<meta name="twitter:image" content="' . esc_url($image) . '">' . "\n";
}
add_action('wp_head', 'prismpath_seo_meta_tags', 2);

function prismpath_jsonld(array $schema): void
{
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}

function prismpath_schema_output(): void
{
    if (is_front_page()) {
        $same_as = array_filter(array(
            prismpath_seo_setting('facebook_url'),
            prismpath_seo_setting('instagram_url'),
            prismpath_seo_setting('linkedin_url'),
        ));
        prismpath_jsonld(array_filter(array(
            '@context' => 'https://schema.org',
            '@type' => 'MedicalOrganization',
            'name' => 'Prismpath Health',
            'url' => home_url('/'),
            'description' => prismpath_seo_description(),
            'email' => prismpath_seo_setting('primary_email'),
            'telephone' => prismpath_seo_setting('phone'),
            'areaServed' => array('@type' => 'Country', 'name' => 'United States'),
            'medicalSpecialty' => array('MentalHealth', 'Psychiatric'),
            'sameAs' => $same_as,
        )));
        prismpath_jsonld(array(
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'Prismpath Health',
            'url' => home_url('/'),
            'potentialAction' => array(
                '@type' => 'SearchAction',
                'target' => home_url('/?s={search_term_string}'),
                'query-input' => 'required name=search_term_string',
            ),
        ));
    }

    if (is_page(array('therapy', 'psychiatry', 'adhd-autism-assessments', 'occupational-therapy', 'whole-family-mental-health', 'group-support', 'referral-partners', 'accommodations'))) {
        $record = prismpath_seo_content_record((int) get_the_ID());
        prismpath_jsonld(array_filter(array(
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => get_the_title(),
            'description' => prismpath_seo_description(),
            'serviceType' => $record['schema_service_type'] ?? null,
            'provider' => array('@type' => 'MedicalOrganization', 'name' => 'Prismpath Health'),
            'audience' => array('@type' => 'Audience', 'audienceType' => 'Adults, caregivers, families, and referral partners'),
            'areaServed' => array('@type' => 'Country', 'name' => 'United States'),
            'availableChannel' => array('@type' => 'ServiceChannel', 'serviceUrl' => get_permalink(), 'availableLanguage' => 'English'),
            'url' => get_permalink(),
        )));
    }

    if (is_page() && function_exists('prismpath_resource_by_slug')) {
        $slug = get_post_field('post_name', get_the_ID());
        $resource = prismpath_resource_by_slug((string) $slug);
        if ($resource) {
            prismpath_jsonld(array(
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => get_the_title(),
                'description' => prismpath_seo_description(),
                'author' => array('@type' => 'Organization', 'name' => 'Prismpath Health'),
                'publisher' => array('@type' => 'MedicalOrganization', 'name' => 'Prismpath Health'),
                'mainEntityOfPage' => get_permalink(),
            ));
        }
    }

    if (is_singular()) {
        $record = prismpath_seo_content_record((int) get_the_ID());
        if ($record && !empty($record['faqs']) && is_array($record['faqs'])) {
            $questions = array();
            foreach ($record['faqs'] as $faq) {
                if (empty($faq['question']) || empty($faq['answer'])) {
                    continue;
                }
                $questions[] = array(
                    '@type' => 'Question',
                    'name' => wp_strip_all_tags($faq['question']),
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text' => wp_strip_all_tags($faq['answer']),
                    ),
                );
            }
            if ($questions) {
                prismpath_jsonld(array(
                    '@context' => 'https://schema.org',
                    '@type' => 'FAQPage',
                    'mainEntity' => $questions,
                ));
            }
        }
    }

    if (!is_front_page()) {
        prismpath_jsonld(array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array(
                array('@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => home_url('/')),
                array('@type' => 'ListItem', 'position' => 2, 'name' => wp_get_document_title(), 'item' => is_singular() ? get_permalink() : home_url('/')),
            ),
        ));
    }
}
add_action('wp_head', 'prismpath_schema_output', 20);

function prismpath_is_sitemap_request(): bool
{
    return (isset($_GET['sitemap']) && 'xml' === sanitize_text_field(wp_unslash($_GET['sitemap'])))
        || '1' === get_query_var('prismpath_sitemap');
}

function prismpath_sitemap_rewrite(): void
{
    add_rewrite_rule('^sitemap\.xml$', 'index.php?prismpath_sitemap=1', 'top');
}
add_action('init', 'prismpath_sitemap_rewrite');

function prismpath_sitemap_query_vars(array $vars): array
{
    $vars[] = 'prismpath_sitemap';
    return $vars;
}
add_filter('query_vars', 'prismpath_sitemap_query_vars');

function prismpath_prevent_sitemap_canonical_redirect($redirect_url)
{
    return prismpath_is_sitemap_request() ? false : $redirect_url;
}
add_filter('redirect_canonical', 'prismpath_prevent_sitemap_canonical_redirect');

function prismpath_custom_sitemap(): void
{
    if (!prismpath_is_sitemap_request()) {
        return;
    }
    status_header(200);
    header('Content-Type: application/xml; charset=' . get_bloginfo('charset'));
    $urls = array(home_url('/'));
    $pages = get_posts(array('post_type' => array('page', 'team_member'), 'post_status' => 'publish', 'posts_per_page' => -1));
    foreach ($pages as $page) {
        $urls[] = get_permalink($page);
    }
    $urls = array_unique(array_filter($urls));
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($urls as $url) {
        echo '  <url><loc>' . esc_url($url) . '</loc></url>' . "\n";
    }
    echo '</urlset>';
    exit;
}
add_action('template_redirect', 'prismpath_custom_sitemap', 0);

function prismpath_robots_txt(string $output): string
{
    $output .= "Sitemap: " . home_url('/sitemap.xml') . "\n";
    return $output;
}
add_filter('robots_txt', 'prismpath_robots_txt');

function prismpath_add_monthly_cron_interval(array $schedules): array
{
    $schedules['monthly'] = array('interval' => 30 * DAY_IN_SECONDS, 'display' => __('Once Monthly', 'prismpath-seo-engine'));
    return $schedules;
}
add_filter('cron_schedules', 'prismpath_add_monthly_cron_interval');

function prismpath_schedule_seo_cron(): void
{
    prismpath_sitemap_rewrite();
    flush_rewrite_rules();
    if (!wp_next_scheduled('prismpath_monthly_seo_event')) {
        wp_schedule_event(time(), 'monthly', 'prismpath_monthly_seo_event');
    }
}
register_activation_hook(__FILE__, 'prismpath_schedule_seo_cron');

function prismpath_unschedule_seo_cron(): void
{
    flush_rewrite_rules();
    $timestamp = wp_next_scheduled('prismpath_monthly_seo_event');
    if ($timestamp) {
        wp_unschedule_event($timestamp, 'prismpath_monthly_seo_event');
    }
}
register_deactivation_hook(__FILE__, 'prismpath_unschedule_seo_cron');

function prismpath_monthly_seo_ping(): void
{
    $sitemap = home_url('/sitemap.xml');
    wp_remote_get('https://www.google.com/ping?sitemap=' . rawurlencode($sitemap), array('timeout' => 5, 'blocking' => false));
    wp_remote_get('https://www.bing.com/ping?sitemap=' . rawurlencode($sitemap), array('timeout' => 5, 'blocking' => false));
}
add_action('prismpath_monthly_seo_event', 'prismpath_monthly_seo_ping');
