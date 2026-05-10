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

remove_action('wp_head', 'rel_canonical');

function prismpath_seo_admin_menu(): void
{
    add_menu_page(
        __('Prismpath SEO', 'prismpath-seo-engine'),
        __('Prismpath SEO', 'prismpath-seo-engine'),
        'manage_options',
        'prismpath-seo',
        'prismpath_seo_render_admin_page',
        'dashicons-search',
        58
    );
}
add_action('admin_menu', 'prismpath_seo_admin_menu');

function prismpath_seo_plugin_action_links(array $links): array
{
    $settings_link = sprintf(
        '<a href="%s">%s</a>',
        esc_url(admin_url('admin.php?page=prismpath-seo')),
        esc_html__('SEO Dashboard', 'prismpath-seo-engine')
    );

    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'prismpath_seo_plugin_action_links');

function prismpath_seo_admin_meta_value(int $post_id, string $field): string
{
    if ('seo_title' === $field) {
        $custom = get_post_meta($post_id, '_prismpath_seo_title', true);
        if (is_string($custom) && $custom) {
            return wp_strip_all_tags($custom);
        }
    }

    if ('meta_description' === $field) {
        $custom = get_post_meta($post_id, 'meta_description', true);
        if (is_string($custom) && $custom) {
            return wp_strip_all_tags($custom);
        }
    }

    $record = prismpath_seo_content_record($post_id);
    if ($record && !empty($record[$field]) && is_string($record[$field])) {
        return wp_strip_all_tags($record[$field]);
    }

    $static_record = prismpath_seo_static_record($post_id);
    if ($static_record && !empty($static_record[$field]) && is_string($static_record[$field])) {
        return wp_strip_all_tags($static_record[$field]);
    }

    if ('seo_title' === $field && 'team_member' === get_post_type($post_id)) {
        return wp_strip_all_tags(get_the_title($post_id) . ' | Prismpath Health Team');
    }

    if ('meta_description' === $field && 'team_member' === get_post_type($post_id)) {
        $role = get_the_excerpt($post_id);
        return wp_strip_all_tags(sprintf(
            'Meet %s, %s at Prismpath Health, part of the team supporting neuroaffirming mental health care.',
            get_the_title($post_id),
            $role ? $role : 'a clinician'
        ));
    }

    return '';
}

function prismpath_seo_admin_service_type(int $post_id): string
{
    $record = prismpath_seo_content_record($post_id);
    if ($record && !empty($record['schema_service_type']) && is_string($record['schema_service_type'])) {
        return wp_strip_all_tags($record['schema_service_type']);
    }

    $static_record = prismpath_seo_static_record($post_id);
    if ($static_record && !empty($static_record['schema_service_type']) && is_string($static_record['schema_service_type'])) {
        return wp_strip_all_tags($static_record['schema_service_type']);
    }

    return '';
}

function prismpath_seo_admin_faq_count(int $post_id): int
{
    $record = prismpath_seo_content_record($post_id);
    if ($record && !empty($record['faqs']) && is_array($record['faqs'])) {
        return count($record['faqs']);
    }

    return 0;
}

function prismpath_seo_admin_schema_label(int $post_id): string
{
    if ('team_member' === get_post_type($post_id)) {
        return 'Person';
    }

    if ((int) get_option('page_on_front') === $post_id) {
        return 'MedicalOrganization, WebSite, MedicalWebPage';
    }

    $service_type = prismpath_seo_admin_service_type($post_id);
    if ($service_type) {
        return 'MedicalWebPage, Service';
    }

    return 'MedicalWebPage';
}

function prismpath_seo_admin_indexable_posts(): array
{
    return get_posts(array(
        'post_type' => array('page', 'team_member'),
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => array('menu_order' => 'ASC', 'title' => 'ASC'),
    ));
}

function prismpath_seo_render_status_badge(bool $ok, string $label): string
{
    $class = $ok ? 'prismpath-seo-badge is-ok' : 'prismpath-seo-badge is-warning';
    return '<span class="' . esc_attr($class) . '">' . esc_html($label) . '</span>';
}

function prismpath_seo_render_admin_page(): void
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have permission to view this page.', 'prismpath-seo-engine'));
    }

    $posts = prismpath_seo_admin_indexable_posts();
    $missing_titles = 0;
    $missing_descriptions = 0;
    $service_schema_pages = 0;
    $faq_pages = 0;

    foreach ($posts as $post) {
        if (!prismpath_seo_admin_meta_value((int) $post->ID, 'seo_title')) {
            $missing_titles++;
        }
        if (!prismpath_seo_admin_meta_value((int) $post->ID, 'meta_description')) {
            $missing_descriptions++;
        }
        if (prismpath_seo_admin_service_type((int) $post->ID)) {
            $service_schema_pages++;
        }
        if (prismpath_seo_admin_faq_count((int) $post->ID) > 0) {
            $faq_pages++;
        }
    }

    $sitemap_url = home_url('/sitemap.xml');
    $robots_url = home_url('/robots.txt');
    ?>
    <div class="wrap prismpath-seo-admin">
        <h1><?php esc_html_e('Prismpath SEO Engine', 'prismpath-seo-engine'); ?></h1>
        <p class="description">
            <?php esc_html_e('Healthcare-safe metadata, schema, sitemap, robots, breadcrumbs, and AI-readable search output for Prismpath Health.', 'prismpath-seo-engine'); ?>
        </p>

        <style>
            .prismpath-seo-admin .prismpath-seo-grid { display: grid; gap: 16px; grid-template-columns: repeat(4, minmax(0, 1fr)); margin: 20px 0; }
            .prismpath-seo-admin .prismpath-seo-card { background: #fff; border: 1px solid #dcdcde; border-radius: 8px; padding: 16px; }
            .prismpath-seo-admin .prismpath-seo-card strong { display: block; font-size: 28px; line-height: 1.1; margin-top: 8px; }
            .prismpath-seo-admin .prismpath-seo-badge { display: inline-block; border-radius: 999px; padding: 4px 9px; font-size: 12px; font-weight: 700; }
            .prismpath-seo-admin .prismpath-seo-badge.is-ok { background: #d1e7dd; color: #0f5132; }
            .prismpath-seo-admin .prismpath-seo-badge.is-warning { background: #fff3cd; color: #664d03; }
            .prismpath-seo-admin .widefat td { vertical-align: top; }
            .prismpath-seo-admin code { white-space: normal; }
            @media (max-width: 1100px) { .prismpath-seo-admin .prismpath-seo-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
            @media (max-width: 700px) { .prismpath-seo-admin .prismpath-seo-grid { grid-template-columns: 1fr; } }
        </style>

        <div class="prismpath-seo-grid" aria-label="<?php esc_attr_e('SEO summary', 'prismpath-seo-engine'); ?>">
            <div class="prismpath-seo-card">
                <?php esc_html_e('Indexed pages tracked', 'prismpath-seo-engine'); ?>
                <strong><?php echo esc_html((string) count($posts)); ?></strong>
            </div>
            <div class="prismpath-seo-card">
                <?php esc_html_e('Service schema pages', 'prismpath-seo-engine'); ?>
                <strong><?php echo esc_html((string) $service_schema_pages); ?></strong>
            </div>
            <div class="prismpath-seo-card">
                <?php esc_html_e('FAQ schema pages', 'prismpath-seo-engine'); ?>
                <strong><?php echo esc_html((string) $faq_pages); ?></strong>
            </div>
            <div class="prismpath-seo-card">
                <?php esc_html_e('Metadata gaps', 'prismpath-seo-engine'); ?>
                <strong><?php echo esc_html((string) ($missing_titles + $missing_descriptions)); ?></strong>
            </div>
        </div>

        <h2><?php esc_html_e('Search Infrastructure', 'prismpath-seo-engine'); ?></h2>
        <table class="widefat striped" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"><?php esc_html_e('Sitemap', 'prismpath-seo-engine'); ?></th>
                    <td><a href="<?php echo esc_url($sitemap_url); ?>" target="_blank" rel="noopener"><?php echo esc_html($sitemap_url); ?></a></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Robots.txt', 'prismpath-seo-engine'); ?></th>
                    <td><a href="<?php echo esc_url($robots_url); ?>" target="_blank" rel="noopener"><?php echo esc_html($robots_url); ?></a></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Default robots meta', 'prismpath-seo-engine'); ?></th>
                    <td><code>index, follow, max-image-preview:large</code></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Search and 404 robots meta', 'prismpath-seo-engine'); ?></th>
                    <td><code>noindex, follow</code></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Legal entity', 'prismpath-seo-engine'); ?></th>
                    <td><?php echo esc_html(prismpath_seo_legal_dba_name()); ?></td>
                </tr>
            </tbody>
        </table>

        <h2><?php esc_html_e('Page SEO Inventory', 'prismpath-seo-engine'); ?></h2>
        <table class="widefat striped">
            <thead>
                <tr>
                    <th scope="col"><?php esc_html_e('Page', 'prismpath-seo-engine'); ?></th>
                    <th scope="col"><?php esc_html_e('SEO title', 'prismpath-seo-engine'); ?></th>
                    <th scope="col"><?php esc_html_e('Meta description', 'prismpath-seo-engine'); ?></th>
                    <th scope="col"><?php esc_html_e('Schema', 'prismpath-seo-engine'); ?></th>
                    <th scope="col"><?php esc_html_e('FAQ schema', 'prismpath-seo-engine'); ?></th>
                    <th scope="col"><?php esc_html_e('Actions', 'prismpath-seo-engine'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post) : ?>
                    <?php
                    $post_id = (int) $post->ID;
                    $title = prismpath_seo_admin_meta_value($post_id, 'seo_title');
                    $description = prismpath_seo_admin_meta_value($post_id, 'meta_description');
                    $faq_count = prismpath_seo_admin_faq_count($post_id);
                    ?>
                    <tr>
                        <td>
                            <strong><?php echo esc_html(get_the_title($post_id)); ?></strong><br>
                            <code><?php echo esc_html((string) get_permalink($post_id)); ?></code>
                        </td>
                        <td><?php echo prismpath_seo_render_status_badge((bool) $title, $title ? __('Set', 'prismpath-seo-engine') : __('Missing', 'prismpath-seo-engine')); ?></td>
                        <td>
                            <?php echo prismpath_seo_render_status_badge((bool) $description, $description ? __('Set', 'prismpath-seo-engine') : __('Missing', 'prismpath-seo-engine')); ?>
                            <?php if ($description) : ?>
                                <br><small><?php echo esc_html(wp_trim_words($description, 18)); ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html(prismpath_seo_admin_schema_label($post_id)); ?></td>
                        <td><?php echo esc_html($faq_count ? sprintf(_n('%d FAQ', '%d FAQs', $faq_count, 'prismpath-seo-engine'), $faq_count) : __('None', 'prismpath-seo-engine')); ?></td>
                        <td>
                            <a href="<?php echo esc_url(get_edit_post_link($post_id, '')); ?>"><?php esc_html_e('Edit', 'prismpath-seo-engine'); ?></a>
                            |
                            <a href="<?php echo esc_url((string) get_permalink($post_id)); ?>" target="_blank" rel="noopener"><?php esc_html_e('View', 'prismpath-seo-engine'); ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
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

function prismpath_seo_static_record(int $post_id): ?array
{
    if (!$post_id || !function_exists('prismpath_static_page_seo')) {
        return null;
    }

    $slug = get_post_field('post_name', $post_id);
    if (!$slug) {
        return null;
    }

    $record = prismpath_static_page_seo((string) $slug);
    return is_array($record) ? $record : null;
}

function prismpath_seo_truthy($value): bool
{
    if (is_bool($value)) {
        return $value;
    }

    if (is_numeric($value)) {
        return (int) $value === 1;
    }

    if (!is_string($value)) {
        return false;
    }

    return in_array(strtolower(trim($value)), array('1', 'true', 'yes', 'on'), true);
}

function prismpath_seo_schema_filter(array $schema): array
{
    return array_filter($schema, static function ($value): bool {
        return !($value === null || $value === '' || $value === array());
    });
}

function prismpath_seo_document_title(string $title): string
{
    if (!is_singular()) {
        if (is_404()) {
            return 'Page Not Found | Prismpath Health';
        }
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

    $static_record = prismpath_seo_static_record((int) $post_id);
    if ($static_record && !empty($static_record['seo_title'])) {
        return wp_strip_all_tags($static_record['seo_title']);
    }

    if ('team_member' === get_post_type($post_id)) {
        return wp_strip_all_tags(get_the_title($post_id) . ' | Prismpath Health Team');
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
    $static_record = prismpath_seo_static_record((int) get_the_ID());
    if ($static_record && !empty($static_record['meta_description'])) {
        return wp_strip_all_tags($static_record['meta_description']);
    }
    if (is_singular('team_member')) {
        $role = get_the_excerpt();
        return wp_strip_all_tags(sprintf(
            'Meet %s, %s at Prismpath Health, part of the team supporting neuroaffirming mental health care.',
            get_the_title(),
            $role ? $role : 'a clinician'
        ));
    }
    if (has_excerpt()) {
        return wp_strip_all_tags(get_the_excerpt());
    }
    return 'A clearer path to neuroaffirming mental health care for every brain and every family.';
}

function prismpath_seo_canonical_url(): string
{
    if (is_404() || is_search()) {
        return '';
    }

    if (is_singular()) {
        return (string) get_permalink();
    }

    if (is_front_page()) {
        return home_url('/');
    }

    $request = isset($GLOBALS['wp']->request) ? trim((string) $GLOBALS['wp']->request, '/') : '';
    return $request ? home_url('/' . $request . '/') : home_url('/');
}

function prismpath_seo_robots_content(): string
{
    if (is_404() || is_search()) {
        return 'noindex, follow';
    }

    return 'index, follow, max-image-preview:large';
}

function prismpath_seo_meta_tags(): void
{
    if (is_admin() || is_feed()) {
        return;
    }
    $title = is_front_page() ? 'Prismpath Health | Neuroaffirming Mental Health Care' : wp_get_document_title();
    $description = prismpath_seo_description();
    $url = prismpath_seo_canonical_url();
    $image = is_singular() && has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'full') : get_template_directory_uri() . '/assets/images/hero-family-prismpath-health.png';

    echo '<meta name="robots" content="' . esc_attr(prismpath_seo_robots_content()) . '">' . "\n";
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    if ($url) {
        echo '<link rel="canonical" href="' . esc_url($url) . '">' . "\n";
    }
    echo '<meta property="og:type" content="website">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
    if ($url) {
        echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
    }
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

function prismpath_seo_is_assoc_array(array $value): bool
{
    return array_keys($value) !== range(0, count($value) - 1);
}

function prismpath_seo_normalize_agent_schema_item($item): ?array
{
    if (is_string($item)) {
        $decoded = json_decode($item, true);
        $item = is_array($decoded) ? $decoded : null;
    }

    if (!is_array($item)) {
        return null;
    }

    foreach (array('schema', 'data', 'json') as $key) {
        if (isset($item[$key])) {
            return prismpath_seo_normalize_agent_schema_item($item[$key]);
        }
    }

    if (!empty($item['@type']) || !empty($item['@context']) || !empty($item['mainEntity'])) {
        if (empty($item['@context'])) {
            $item['@context'] = 'https://schema.org';
        }
        return $item;
    }

    return null;
}

function prismpath_seo_agent_schemas(int $post_id): array
{
    $override = get_post_meta($post_id, '_chroma_schema_override', true);
    if (!prismpath_seo_truthy($override)) {
        return array();
    }

    $candidates = array(
        get_post_meta($post_id, '_chroma_schema_data', true),
        get_post_meta($post_id, '_chroma_post_schemas', true),
    );

    $schemas = array();
    foreach ($candidates as $candidate) {
        if (!$candidate) {
            continue;
        }

        if (is_string($candidate)) {
            $decoded = json_decode($candidate, true);
            $candidate = is_array($decoded) ? $decoded : $candidate;
        }

        if (is_array($candidate) && prismpath_seo_is_assoc_array($candidate)) {
            $schema = prismpath_seo_normalize_agent_schema_item($candidate);
            if ($schema) {
                $schemas[] = $schema;
                continue;
            }
        }

        foreach ((array) $candidate as $item) {
            $schema = prismpath_seo_normalize_agent_schema_item($item);
            if ($schema) {
                $schemas[] = $schema;
            }
        }
    }

    return $schemas;
}

function prismpath_seo_organization_ref(): array
{
    return array('@id' => home_url('/#organization'));
}

function prismpath_seo_legal_dba_name(): string
{
    $legal_name = prismpath_seo_setting('legal_name', 'Lbee Health Practive Group PLLC');
    if (false !== stripos($legal_name, 'dba Prismpath Health')) {
        return $legal_name;
    }

    return trim($legal_name) . ' dba Prismpath Health';
}

function prismpath_seo_organization_schema(): array
{
    $same_as = array_filter(array(
        prismpath_seo_setting('facebook_url'),
        prismpath_seo_setting('instagram_url'),
        prismpath_seo_setting('linkedin_url'),
    ));

    return prismpath_seo_schema_filter(array(
        '@context' => 'https://schema.org',
        '@type' => 'MedicalOrganization',
        '@id' => home_url('/#organization'),
        'name' => 'Prismpath Health',
        'legalName' => prismpath_seo_legal_dba_name(),
        'alternateName' => array('Prismpath Health', 'Lbee Health Practive Group PLLC'),
        'url' => home_url('/'),
        'logo' => get_template_directory_uri() . '/assets/icons/icon-512.png',
        'image' => get_template_directory_uri() . '/assets/images/hero-family-prismpath-health.png',
        'description' => prismpath_seo_description(),
        'email' => prismpath_seo_setting('primary_email'),
        'telephone' => prismpath_seo_setting('phone'),
        'areaServed' => array('@type' => 'Country', 'name' => 'United States'),
        'medicalSpecialty' => array('MentalHealth', 'Psychiatric'),
        'sameAs' => $same_as,
    ));
}

function prismpath_seo_webpage_type(): string
{
    if (is_page('contact')) {
        return 'ContactPage';
    }

    if (is_page('team')) {
        return 'CollectionPage';
    }

    if (is_page('resources')) {
        return 'CollectionPage';
    }

    if (is_page(array('therapy', 'psychiatry', 'adhd-autism-assessments', 'occupational-therapy', 'whole-family-mental-health', 'approach', 'group-support', 'referral-partners', 'accommodations', 'insurance-payment'))) {
        return 'MedicalWebPage';
    }

    if (is_singular('page') && function_exists('prismpath_resource_record_by_slug') && prismpath_resource_record_by_slug((string) get_post_field('post_name', get_the_ID()))) {
        return 'MedicalWebPage';
    }

    return 'WebPage';
}

function prismpath_seo_webpage_schema(): array
{
    return prismpath_seo_schema_filter(array(
        '@context' => 'https://schema.org',
        '@type' => prismpath_seo_webpage_type(),
        '@id' => get_permalink() . '#webpage',
        'url' => get_permalink(),
        'name' => wp_get_document_title(),
        'description' => prismpath_seo_description(),
        'isPartOf' => array('@id' => home_url('/#website')),
        'publisher' => prismpath_seo_organization_ref(),
        'datePublished' => get_the_date('c'),
        'dateModified' => get_the_modified_date('c'),
    ));
}

function prismpath_seo_team_photo_url(int $post_id): string
{
    if (has_post_thumbnail($post_id)) {
        return (string) get_the_post_thumbnail_url($post_id, 'full');
    }

    if (function_exists('prismpath_team_photo_url')) {
        return (string) prismpath_team_photo_url($post_id);
    }

    return '';
}

function prismpath_seo_person_schema(int $post_id): array
{
    return prismpath_seo_schema_filter(array(
        '@context' => 'https://schema.org',
        '@type' => 'Person',
        '@id' => get_permalink($post_id) . '#person',
        'name' => get_the_title($post_id),
        'jobTitle' => get_the_excerpt($post_id),
        'url' => get_permalink($post_id),
        'image' => prismpath_seo_team_photo_url($post_id),
        'worksFor' => prismpath_seo_organization_ref(),
        'affiliation' => prismpath_seo_organization_ref(),
    ));
}

function prismpath_schema_output(): void
{
    $agent_schemas = is_singular() ? prismpath_seo_agent_schemas((int) get_the_ID()) : array();
    if ($agent_schemas) {
        foreach ($agent_schemas as $schema) {
            prismpath_jsonld($schema);
        }
        return;
    }

    if (is_front_page()) {
        prismpath_jsonld(prismpath_seo_organization_schema());
        prismpath_jsonld(array(
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => home_url('/#website'),
            'name' => 'Prismpath Health',
            'url' => home_url('/'),
            'publisher' => prismpath_seo_organization_ref(),
            'potentialAction' => array(
                '@type' => 'SearchAction',
                'target' => home_url('/?s={search_term_string}'),
                'query-input' => 'required name=search_term_string',
            ),
        ));
    }

    if (is_singular()) {
        prismpath_jsonld(prismpath_seo_webpage_schema());
    }

    if (is_singular('team_member')) {
        prismpath_jsonld(prismpath_seo_person_schema((int) get_the_ID()));
    }

    if (is_page(array('therapy', 'psychiatry', 'adhd-autism-assessments', 'occupational-therapy', 'whole-family-mental-health', 'group-support', 'referral-partners', 'accommodations'))) {
        $record = prismpath_seo_content_record((int) get_the_ID());
        prismpath_jsonld(prismpath_seo_schema_filter(array(
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            '@id' => get_permalink() . '#service',
            'name' => get_the_title(),
            'description' => prismpath_seo_description(),
            'serviceType' => $record['schema_service_type'] ?? null,
            'provider' => array(
                '@type' => 'MedicalOrganization',
                '@id' => home_url('/#organization'),
                'name' => 'Prismpath Health',
                'legalName' => prismpath_seo_legal_dba_name(),
            ),
            'audience' => array('@type' => 'Audience', 'audienceType' => 'Adults, caregivers, families, and referral partners'),
            'areaServed' => array('@type' => 'Country', 'name' => 'United States'),
            'availableChannel' => array('@type' => 'ServiceChannel', 'serviceUrl' => get_permalink(), 'availableLanguage' => 'English'),
            'url' => get_permalink(),
        )));
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
    $request_path = isset($_SERVER['REQUEST_URI']) ? (string) parse_url((string) wp_unslash($_SERVER['REQUEST_URI']), PHP_URL_PATH) : '';
    if ('/sitemap.xml' === $request_path) {
        return true;
    }

    if (isset($_GET['sitemap']) && 'xml' === sanitize_text_field(wp_unslash($_GET['sitemap']))) {
        return true;
    }

    global $wp_query;
    if (!is_object($wp_query)) {
        return false;
    }

    return '1' === get_query_var('prismpath_sitemap')
        || 'index' === get_query_var('sitemap');
}

function prismpath_sitemap_rewrite(): void
{
    add_rewrite_rule('^sitemap\.xml$', 'index.php?prismpath_sitemap=1', 'top');
}
add_action('init', 'prismpath_sitemap_rewrite');

function prismpath_sitemap_maybe_flush_rules(): void
{
    $version = '2026-05-09-custom-sitemap-v2';
    if (get_option('prismpath_sitemap_rewrite_version') === $version) {
        return;
    }

    prismpath_sitemap_rewrite();
    flush_rewrite_rules(false);
    update_option('prismpath_sitemap_rewrite_version', $version, false);
}
add_action('init', 'prismpath_sitemap_maybe_flush_rules', 20);

function prismpath_sitemap_query_vars(array $vars): array
{
    $vars[] = 'prismpath_sitemap';
    return $vars;
}
add_filter('query_vars', 'prismpath_sitemap_query_vars');

function prismpath_disable_core_sitemap_redirect(): void
{
    if ('index' === get_query_var('sitemap') || '1' === get_query_var('prismpath_sitemap')) {
        remove_action('template_redirect', 'redirect_canonical');
    }
}
add_action('template_redirect', 'prismpath_disable_core_sitemap_redirect', -2000);

function prismpath_prevent_sitemap_canonical_redirect($redirect_url, $requested_url = null)
{
    if (is_string($requested_url)) {
        $requested_path = (string) wp_parse_url($requested_url, PHP_URL_PATH);
        if ('/sitemap.xml' === $requested_path) {
            return false;
        }
    }

    return prismpath_is_sitemap_request() ? false : $redirect_url;
}
add_filter('redirect_canonical', 'prismpath_prevent_sitemap_canonical_redirect', 0, 2);

function prismpath_sitemap_xml_escape(string $value): string
{
    return function_exists('esc_xml') ? esc_xml($value) : esc_html($value);
}

function prismpath_custom_sitemap(): void
{
    if (!prismpath_is_sitemap_request()) {
        return;
    }
    status_header(200);
    header('Content-Type: application/xml; charset=' . get_bloginfo('charset'));

    $urls = array();
    $add_url = static function (string $loc, string $lastmod = '') use (&$urls): void {
        $loc = esc_url_raw($loc);
        if (!$loc) {
            return;
        }
        $urls[$loc] = array(
            'loc' => $loc,
            'lastmod' => $lastmod,
        );
    };

    $front_page_id = (int) get_option('page_on_front');
    $add_url(
        home_url('/'),
        $front_page_id ? get_post_modified_time('c', true, $front_page_id) : ''
    );

    $pages = get_posts(array('post_type' => array('page', 'team_member'), 'post_status' => 'publish', 'posts_per_page' => -1));
    foreach ($pages as $page) {
        $permalink = get_permalink($page);
        if ($permalink) {
            $add_url($permalink, get_post_modified_time('c', true, $page));
        }
    }

    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($urls as $url) {
        echo '  <url><loc>' . prismpath_sitemap_xml_escape($url['loc']) . '</loc>';
        if (!empty($url['lastmod'])) {
            echo '<lastmod>' . prismpath_sitemap_xml_escape($url['lastmod']) . '</lastmod>';
        }
        echo '</url>' . "\n";
    }
    echo '</urlset>';
    exit;
}
add_action('parse_request', 'prismpath_custom_sitemap', -1000);
add_action('template_redirect', 'prismpath_custom_sitemap', -1000);

function prismpath_sitemap_pre_handle_404($preempt, $query)
{
    if (!prismpath_is_sitemap_request()) {
        return $preempt;
    }

    prismpath_custom_sitemap();
    return true;
}
add_filter('pre_handle_404', 'prismpath_sitemap_pre_handle_404', -1000, 2);

function prismpath_robots_txt(string $output): string
{
    $output .= "Sitemap: " . home_url('/sitemap.xml') . "\n";
    return $output;
}
add_filter('robots_txt', 'prismpath_robots_txt');

function prismpath_schedule_seo_cron(): void
{
    prismpath_sitemap_rewrite();
    flush_rewrite_rules();
    prismpath_unschedule_seo_cron();
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
    prismpath_unschedule_seo_cron();
}
add_action('prismpath_monthly_seo_event', 'prismpath_monthly_seo_ping');
