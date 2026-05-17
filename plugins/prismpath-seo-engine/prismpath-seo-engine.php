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
    return implode(', ', prismpath_seo_admin_schema_types($post_id));
}

function prismpath_seo_admin_schema_types(int $post_id): array
{
    $agent_status = prismpath_seo_agent_schema_status($post_id);
    if (!empty($agent_status['valid']) && !empty($agent_status['types'])) {
        return $agent_status['types'];
    }

    return prismpath_seo_schema_types_for_post($post_id);
}

function prismpath_seo_admin_schema_source_label(int $post_id): string
{
    $agent_status = prismpath_seo_agent_schema_status($post_id);
    if (empty($agent_status['override'])) {
        return __('Auto', 'prismpath-seo-engine');
    }

    return !empty($agent_status['valid'])
        ? __('Agent override', 'prismpath-seo-engine')
        : __('Auto fallback', 'prismpath-seo-engine');
}

function prismpath_seo_admin_schema_override_label(int $post_id): string
{
    $agent_status = prismpath_seo_agent_schema_status($post_id);
    if (empty($agent_status['override'])) {
        return __('Inactive', 'prismpath-seo-engine');
    }

    return !empty($agent_status['valid'])
        ? __('Active and valid', 'prismpath-seo-engine')
        : __('Active but invalid', 'prismpath-seo-engine');
}

function prismpath_seo_admin_indexable_posts(): array
{
    return get_posts(array(
        'post_type' => array('page', 'post', 'team_member'),
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
    $agent_override_pages = 0;

    foreach ($posts as $post) {
        $post_id = (int) $post->ID;
        if (!prismpath_seo_admin_meta_value((int) $post->ID, 'seo_title')) {
            $missing_titles++;
        }
        if (!prismpath_seo_admin_meta_value((int) $post->ID, 'meta_description')) {
            $missing_descriptions++;
        }
        if (in_array('Service', prismpath_seo_admin_schema_types($post_id), true)) {
            $service_schema_pages++;
        }
        if (prismpath_seo_admin_faq_count((int) $post->ID) > 0) {
            $faq_pages++;
        }
        if (!empty(prismpath_seo_agent_schema_status($post_id)['override'])) {
            $agent_override_pages++;
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
            .prismpath-seo-admin .prismpath-seo-grid { display: grid; gap: 16px; grid-template-columns: repeat(5, minmax(0, 1fr)); margin: 20px 0; }
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
            <div class="prismpath-seo-card">
                <?php esc_html_e('Agent schema overrides', 'prismpath-seo-engine'); ?>
                <strong><?php echo esc_html((string) $agent_override_pages); ?></strong>
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
                    <th scope="col"><?php esc_html_e('Schema source', 'prismpath-seo-engine'); ?></th>
                    <th scope="col"><?php esc_html_e('Agent override', 'prismpath-seo-engine'); ?></th>
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
                    $agent_status = prismpath_seo_agent_schema_status($post_id);
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
                        <td><?php echo esc_html(prismpath_seo_admin_schema_source_label($post_id)); ?></td>
                        <td>
                            <?php echo prismpath_seo_render_status_badge(empty($agent_status['override']) || !empty($agent_status['valid']), prismpath_seo_admin_schema_override_label($post_id)); ?>
                        </td>
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
    if (is_home() && !is_front_page()) {
        $posts_page_id = (int) get_option('page_for_posts');
        if ($posts_page_id) {
            $custom = get_post_meta($posts_page_id, '_prismpath_seo_title', true);
            if ($custom) {
                return wp_strip_all_tags($custom);
            }

            $static_record = prismpath_seo_static_record($posts_page_id);
            if ($static_record && !empty($static_record['seo_title'])) {
                return wp_strip_all_tags($static_record['seo_title']);
            }
        }

        return 'Blog | Prismpath Health';
    }

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
    if (is_home() && !is_front_page()) {
        $posts_page_id = (int) get_option('page_for_posts');
        if ($posts_page_id) {
            $custom = get_post_meta($posts_page_id, 'meta_description', true);
            if ($custom) {
                return wp_strip_all_tags($custom);
            }

            $static_record = prismpath_seo_static_record($posts_page_id);
            if ($static_record && !empty($static_record['meta_description'])) {
                return wp_strip_all_tags($static_record['meta_description']);
            }

            if (has_excerpt($posts_page_id)) {
                return wp_strip_all_tags(get_the_excerpt($posts_page_id));
            }
        }

        return 'Read Prismpath Health blog updates and neuroaffirming mental health resources for adults, caregivers, ADHD, Autism, therapy, psychiatry, and occupational therapy.';
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

    if (is_home() && !is_front_page()) {
        $posts_page_id = (int) get_option('page_for_posts');
        return $posts_page_id ? (string) get_permalink($posts_page_id) : home_url('/blog/');
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
    if (!$schema) {
        return;
    }

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

    if (!empty($item['@graph']) && is_array($item['@graph'])) {
        if (empty($item['@context'])) {
            $item['@context'] = 'https://schema.org';
        }
        return $item;
    }

    if (!empty($item['@type'])) {
        if (empty($item['@context'])) {
            $item['@context'] = 'https://schema.org';
        }
        return $item;
    }

    return null;
}

function prismpath_seo_agent_schema_status(int $post_id): array
{
    static $cache = array();
    if (isset($cache[$post_id])) {
        return $cache[$post_id];
    }

    $override = get_post_meta($post_id, '_chroma_schema_override', true);
    if (!prismpath_seo_truthy($override)) {
        $cache[$post_id] = array(
            'override' => false,
            'valid' => false,
            'schemas' => array(),
            'types' => array(),
            'source' => 'auto',
        );
        return $cache[$post_id];
    }

    $candidates = array(
        get_post_meta($post_id, '_chroma_schema_data', true),
        get_post_meta($post_id, '_chroma_post_schemas', true),
    );

    $schemas = array();
    $invalid_sources = 0;
    foreach ($candidates as $candidate) {
        if (!$candidate) {
            continue;
        }

        if (is_string($candidate)) {
            $decoded = json_decode($candidate, true);
            if (is_array($decoded)) {
                $candidate = $decoded;
            } else {
                $invalid_sources++;
                continue;
            }
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

    $schemas = prismpath_seo_schema_dedupe_nodes($schemas);
    $valid = !empty($schemas);
    $cache[$post_id] = array(
        'override' => true,
        'valid' => $valid,
        'schemas' => $schemas,
        'types' => $valid ? prismpath_seo_schema_types_from_nodes($schemas) : array(),
        'source' => $valid ? 'agent' : 'auto_fallback',
        'invalid_sources' => $invalid_sources,
    );

    return $cache[$post_id];
}

function prismpath_seo_agent_schemas(int $post_id): array
{
    $status = prismpath_seo_agent_schema_status($post_id);
    return !empty($status['valid']) ? $status['schemas'] : array();
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

function prismpath_seo_schema_clean(array $schema): array
{
    foreach ($schema as $key => $value) {
        if (is_array($value)) {
            $value = prismpath_seo_schema_clean($value);
        }

        if ($value === null || $value === '' || $value === array()) {
            unset($schema[$key]);
            continue;
        }

        $schema[$key] = $value;
    }

    return $schema;
}

function prismpath_seo_schema_dedupe_nodes(array $nodes): array
{
    $deduped = array();
    $seen = array();

    foreach ($nodes as $node) {
        if (!is_array($node)) {
            continue;
        }

        $node = prismpath_seo_schema_clean($node);
        if (!$node) {
            continue;
        }

        $key = !empty($node['@id'])
            ? (string) $node['@id']
            : md5((string) wp_json_encode($node));

        if (isset($seen[$key])) {
            continue;
        }

        $seen[$key] = true;
        $deduped[] = $node;
    }

    return $deduped;
}

function prismpath_seo_schema_types_from_nodes(array $nodes): array
{
    $types = array();
    $add_type = static function ($type) use (&$types): void {
        foreach ((array) $type as $item) {
            if (is_string($item) && $item && !in_array($item, $types, true)) {
                $types[] = $item;
            }
        }
    };

    foreach ($nodes as $node) {
        if (!is_array($node)) {
            continue;
        }

        if (!empty($node['@type'])) {
            $add_type($node['@type']);
        }

        if (!empty($node['@graph']) && is_array($node['@graph'])) {
            foreach ($node['@graph'] as $graph_node) {
                if (is_array($graph_node) && !empty($graph_node['@type'])) {
                    $add_type($graph_node['@type']);
                }
            }
        }
    }

    return $types;
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

function prismpath_seo_website_schema(): array
{
    return array(
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
    );
}

function prismpath_seo_current_post_id(): int
{
    if (is_home() && !is_front_page()) {
        return (int) get_option('page_for_posts');
    }

    if (is_front_page()) {
        return (int) get_option('page_on_front');
    }

    return is_singular() ? (int) get_the_ID() : 0;
}

function prismpath_seo_resource_record_for_slug(string $slug): ?array
{
    if (!$slug || !function_exists('prismpath_resource_record_by_slug')) {
        return null;
    }

    $record = prismpath_resource_record_by_slug($slug);
    return is_array($record) ? $record : null;
}

function prismpath_seo_current_schema_context(): array
{
    $post_id = prismpath_seo_current_post_id();
    $slug = $post_id ? (string) get_post_field('post_name', $post_id) : '';
    $url = prismpath_seo_canonical_url();
    $record = $post_id ? prismpath_seo_content_record($post_id) : null;
    $resource_record = prismpath_seo_resource_record_for_slug($slug);

    $context = array(
        'post_id' => $post_id,
        'post_type' => $post_id ? (string) get_post_type($post_id) : '',
        'slug' => $slug,
        'url' => $url ?: home_url('/'),
        'title' => wp_get_document_title(),
        'description' => prismpath_seo_description(),
        'record' => $record,
        'resource_record' => $resource_record,
        'static_record' => $post_id ? prismpath_seo_static_record($post_id) : null,
        'is_front' => is_front_page(),
        'is_blog_index' => is_home() && !is_front_page(),
        'is_singular' => is_singular(),
        'is_post' => is_singular('post'),
        'is_team_member' => is_singular('team_member'),
    );

    return apply_filters('prismpath_seo_schema_context', $context);
}

function prismpath_seo_webpage_type_for_context(array $context)
{
    if (!empty($context['is_blog_index'])) {
        return 'CollectionPage';
    }

    if (!empty($context['is_team_member'])) {
        return 'ProfilePage';
    }

    $slug = (string) ($context['slug'] ?? '');
    if ('about' === $slug) {
        return array('AboutPage', 'ProfilePage');
    }

    if ('contact' === $slug) {
        return 'ContactPage';
    }

    if (in_array($slug, array('team', 'resources'), true)) {
        return 'CollectionPage';
    }

    if (prismpath_seo_context_is_medical_page($context)) {
        return 'MedicalWebPage';
    }

    return 'WebPage';
}

function prismpath_seo_webpage_type(): string
{
    $type = prismpath_seo_webpage_type_for_context(prismpath_seo_current_schema_context());
    return is_array($type) ? (string) reset($type) : (string) $type;
}

function prismpath_seo_context_is_medical_page(array $context): bool
{
    $slug = (string) ($context['slug'] ?? '');
    if (!empty($context['resource_record'])) {
        return true;
    }

    $medical_slugs = array(
        'therapy',
        'psychiatry',
        'adhd-autism-assessments',
        'occupational-therapy',
        'whole-family-mental-health',
        'approach',
        'group-support',
        'referral-partners',
        'accommodations',
        'insurance-payment',
    );

    return in_array($slug, $medical_slugs, true);
}

function prismpath_seo_context_has_service_schema(array $context): bool
{
    if (!empty($context['resource_record']) || 'page' !== ($context['post_type'] ?? '')) {
        return false;
    }

    $record = $context['record'] ?? null;
    return is_array($record) && !empty($record['schema_service_type']);
}

function prismpath_seo_webpage_main_entity(array $context): ?array
{
    $slug = (string) ($context['slug'] ?? '');
    if ('about' === $slug) {
        return prismpath_seo_organization_ref();
    }

    if ('contact' === $slug) {
        return array('@id' => ($context['url'] ?? home_url('/contact/')) . '#contact');
    }

    if (!empty($context['is_team_member'])) {
        return array('@id' => ($context['url'] ?? get_permalink()) . '#person');
    }

    if (prismpath_seo_context_has_service_schema($context)) {
        return array('@id' => ($context['url'] ?? get_permalink()) . '#service');
    }

    if (!empty($context['is_post'])) {
        return array('@id' => ($context['url'] ?? get_permalink()) . '#article');
    }

    if (in_array($slug, array('team', 'resources'), true) || !empty($context['is_blog_index'])) {
        return array('@id' => ($context['url'] ?? get_permalink()) . '#itemlist');
    }

    return null;
}

function prismpath_seo_webpage_schema(array $context = array()): array
{
    $context = $context ?: prismpath_seo_current_schema_context();
    $post_id = (int) ($context['post_id'] ?? 0);
    $url = (string) ($context['url'] ?? prismpath_seo_canonical_url());
    $type = prismpath_seo_webpage_type_for_context($context);

    return prismpath_seo_schema_filter(array(
        '@type' => $type,
        '@id' => $url . '#webpage',
        'url' => $url,
        'name' => $context['title'] ?? wp_get_document_title(),
        'description' => $context['description'] ?? prismpath_seo_description(),
        'isPartOf' => array('@id' => home_url('/#website')),
        'publisher' => prismpath_seo_organization_ref(),
        'about' => 'about' === ($context['slug'] ?? '') ? prismpath_seo_organization_ref() : null,
        'mainEntity' => prismpath_seo_webpage_main_entity($context),
        'mentions' => 'about' === ($context['slug'] ?? '') ? array('@id' => $url . '#itemlist') : null,
        'specialty' => prismpath_seo_context_is_medical_page($context) ? 'MentalHealth' : null,
        'datePublished' => $post_id ? get_the_date('c', $post_id) : null,
        'dateModified' => $post_id ? get_the_modified_date('c', $post_id) : null,
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

function prismpath_seo_service_schema(array $context): array
{
    if (!prismpath_seo_context_has_service_schema($context)) {
        return array();
    }

    $record = $context['record'] ?? array();
    $url = (string) ($context['url'] ?? get_permalink());

    return prismpath_seo_schema_filter(array(
        '@type' => 'Service',
        '@id' => $url . '#service',
        'name' => $context['title'] ?? get_the_title(),
        'description' => $context['description'] ?? prismpath_seo_description(),
        'serviceType' => is_array($record) ? ($record['schema_service_type'] ?? null) : null,
        'provider' => prismpath_seo_organization_ref(),
        'audience' => array('@type' => 'Audience', 'audienceType' => 'Adults, caregivers, families, and referral partners'),
        'areaServed' => array('@type' => 'Country', 'name' => 'United States'),
        'availableChannel' => array('@type' => 'ServiceChannel', 'serviceUrl' => $url, 'availableLanguage' => 'English'),
        'url' => $url,
    ));
}

function prismpath_seo_team_item_list_schema(array $context): array
{
    $members = get_posts(array(
        'post_type' => 'team_member',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => array('menu_order' => 'ASC', 'title' => 'ASC'),
    ));

    if (!$members) {
        return array();
    }

    $items = array();
    foreach ($members as $index => $member) {
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => get_the_title($member),
            'item' => get_permalink($member),
        );
    }

    return array(
        '@type' => 'ItemList',
        '@id' => ($context['url'] ?? home_url('/team/')) . '#itemlist',
        'name' => 'Prismpath Health providers',
        'itemListElement' => $items,
    );
}

function prismpath_seo_blog_item_list_schema(array $context): array
{
    $posts = get_posts(array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => 10,
        'orderby' => 'date',
        'order' => 'DESC',
    ));

    if (!$posts) {
        return array();
    }

    $items = array();
    foreach ($posts as $index => $post) {
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => get_the_title($post),
            'item' => get_permalink($post),
        );
    }

    return array(
        '@type' => 'ItemList',
        '@id' => ($context['url'] ?? home_url('/blog/')) . '#itemlist',
        'name' => 'Prismpath Health blog posts',
        'itemListElement' => $items,
    );
}

function prismpath_seo_resource_item_list_schema(array $context): array
{
    if ('resources' !== ($context['slug'] ?? '') || !function_exists('prismpath_resource_pages')) {
        return array();
    }

    $resources = prismpath_resource_pages();
    if (!is_array($resources) || !$resources) {
        return array();
    }

    $items = array();
    $position = 1;
    foreach ($resources as $slug => $resource) {
        if (!is_array($resource) || empty($resource['title'])) {
            continue;
        }

        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position,
            'name' => wp_strip_all_tags((string) $resource['title']),
            'item' => function_exists('prismpath_resource_url') ? prismpath_resource_url((string) $slug) : home_url('/resources/' . trim((string) $slug, '/') . '/'),
        );
        $position++;
    }

    return array(
        '@type' => 'ItemList',
        '@id' => ($context['url'] ?? home_url('/resources/')) . '#itemlist',
        'name' => 'Prismpath Health resources',
        'itemListElement' => $items,
    );
}

function prismpath_seo_blog_posting_schema(array $context): array
{
    if (empty($context['is_post']) || empty($context['post_id'])) {
        return array();
    }

    $post_id = (int) $context['post_id'];
    $image = has_post_thumbnail($post_id) ? get_the_post_thumbnail_url($post_id, 'full') : get_template_directory_uri() . '/assets/images/hero-family-prismpath-health.png';
    $author_name = get_the_author_meta('display_name', (int) get_post_field('post_author', $post_id));

    return prismpath_seo_schema_filter(array(
        '@type' => array('BlogPosting', 'Article'),
        '@id' => ($context['url'] ?? get_permalink($post_id)) . '#article',
        'headline' => get_the_title($post_id),
        'description' => $context['description'] ?? prismpath_seo_description(),
        'image' => $image,
        'datePublished' => get_the_date('c', $post_id),
        'dateModified' => get_the_modified_date('c', $post_id),
        'author' => array('@type' => 'Person', 'name' => $author_name ?: 'Prismpath Health'),
        'publisher' => prismpath_seo_organization_ref(),
        'mainEntityOfPage' => array('@id' => ($context['url'] ?? get_permalink($post_id)) . '#webpage'),
        'url' => $context['url'] ?? get_permalink($post_id),
    ));
}

function prismpath_seo_faq_schema(array $context): array
{
    $record = $context['record'] ?? null;
    if (!is_array($record) || empty($record['faqs']) || !is_array($record['faqs'])) {
        return array();
    }

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

    if (!$questions) {
        return array();
    }

    return array(
        '@type' => 'FAQPage',
        '@id' => ($context['url'] ?? get_permalink()) . '#faq',
        'mainEntity' => $questions,
    );
}

function prismpath_seo_contact_point_schema(array $context): array
{
    if ('contact' !== ($context['slug'] ?? '')) {
        return array();
    }

    return prismpath_seo_schema_filter(array(
        '@type' => 'ContactPoint',
        '@id' => ($context['url'] ?? home_url('/contact/')) . '#contact',
        'contactType' => 'customer support',
        'email' => prismpath_seo_setting('primary_email'),
        'telephone' => prismpath_seo_setting('phone'),
        'areaServed' => array('@type' => 'Country', 'name' => 'United States'),
        'availableLanguage' => 'English',
    ));
}

function prismpath_seo_job_posting_schema(array $context): array
{
    if ('careers' !== ($context['slug'] ?? '') || empty($context['post_id'])) {
        return array();
    }

    $raw = get_post_meta((int) $context['post_id'], '_prismpath_job_postings', true);
    if (is_string($raw)) {
        $decoded = json_decode($raw, true);
        $raw = is_array($decoded) ? $decoded : array();
    }

    $jobs = is_array($raw) && prismpath_seo_is_assoc_array($raw) ? array($raw) : (array) $raw;
    $nodes = array();
    foreach ($jobs as $index => $job) {
        if (!is_array($job)) {
            continue;
        }

        $required = array('title', 'description', 'datePosted', 'employmentType');
        foreach ($required as $key) {
            if (empty($job[$key])) {
                continue 2;
            }
        }

        $nodes[] = prismpath_seo_schema_filter(array(
            '@type' => 'JobPosting',
            '@id' => ($context['url'] ?? home_url('/careers/')) . '#job-' . ($index + 1),
            'title' => wp_strip_all_tags((string) $job['title']),
            'description' => wp_kses_post((string) $job['description']),
            'datePosted' => sanitize_text_field((string) $job['datePosted']),
            'validThrough' => !empty($job['validThrough']) ? sanitize_text_field((string) $job['validThrough']) : null,
            'employmentType' => sanitize_text_field((string) $job['employmentType']),
            'hiringOrganization' => prismpath_seo_organization_ref(),
            'jobLocationType' => !empty($job['jobLocationType']) ? sanitize_text_field((string) $job['jobLocationType']) : 'TELECOMMUTE',
            'applicantLocationRequirements' => array('@type' => 'Country', 'name' => 'United States'),
        ));
    }

    return $nodes;
}

function prismpath_seo_post_has_complete_job_posting(int $post_id): bool
{
    $raw = get_post_meta($post_id, '_prismpath_job_postings', true);
    if (is_string($raw)) {
        $decoded = json_decode($raw, true);
        $raw = is_array($decoded) ? $decoded : array();
    }

    $jobs = is_array($raw) && prismpath_seo_is_assoc_array($raw) ? array($raw) : (array) $raw;
    foreach ($jobs as $job) {
        if (!is_array($job)) {
            continue;
        }

        if (!empty($job['title']) && !empty($job['description']) && !empty($job['datePosted']) && !empty($job['employmentType'])) {
            return true;
        }
    }

    return false;
}

function prismpath_seo_breadcrumb_schema(array $context): array
{
    if (!empty($context['is_front'])) {
        return array();
    }

    $url = (string) ($context['url'] ?? prismpath_seo_canonical_url());
    return array(
        '@type' => 'BreadcrumbList',
        '@id' => $url . '#breadcrumb',
        'itemListElement' => array(
            array('@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => home_url('/')),
            array('@type' => 'ListItem', 'position' => 2, 'name' => wp_get_document_title(), 'item' => $url ?: home_url('/')),
        ),
    );
}

function prismpath_seo_schema_registry(array $context): array
{
    $registry = array(
        'organization' => 'prismpath_seo_build_organization_node',
        'website' => 'prismpath_seo_build_website_node',
        'webpage' => 'prismpath_seo_build_webpage_node',
        'person' => 'prismpath_seo_build_person_node',
        'service' => 'prismpath_seo_build_service_node',
        'team_item_list' => 'prismpath_seo_build_team_item_list_node',
        'blog_item_list' => 'prismpath_seo_build_blog_item_list_node',
        'resource_item_list' => 'prismpath_seo_build_resource_item_list_node',
        'blog_posting' => 'prismpath_seo_build_blog_posting_node',
        'faq' => 'prismpath_seo_build_faq_node',
        'contact_point' => 'prismpath_seo_build_contact_point_node',
        'job_posting' => 'prismpath_seo_build_job_posting_node',
        'breadcrumb' => 'prismpath_seo_build_breadcrumb_node',
    );

    return apply_filters('prismpath_seo_schema_registry', $registry, $context);
}

function prismpath_seo_build_organization_node(array $context)
{
    if (empty($context['is_front']) && !in_array(($context['slug'] ?? ''), array('about', 'contact'), true)) {
        return array();
    }

    $schema = prismpath_seo_organization_schema();
    unset($schema['@context']);
    if ('contact' === ($context['slug'] ?? '')) {
        $schema['contactPoint'] = array('@id' => ($context['url'] ?? home_url('/contact/')) . '#contact');
    }

    return $schema;
}

function prismpath_seo_build_website_node(array $context)
{
    return !empty($context['is_front']) ? prismpath_seo_website_schema() : array();
}

function prismpath_seo_build_webpage_node(array $context)
{
    if (is_404() || is_search() || empty($context['url'])) {
        return array();
    }

    return prismpath_seo_webpage_schema($context);
}

function prismpath_seo_build_person_node(array $context)
{
    return !empty($context['is_team_member']) && !empty($context['post_id'])
        ? prismpath_seo_person_schema((int) $context['post_id'])
        : array();
}

function prismpath_seo_build_service_node(array $context)
{
    return prismpath_seo_service_schema($context);
}

function prismpath_seo_build_team_item_list_node(array $context)
{
    return in_array(($context['slug'] ?? ''), array('about', 'team'), true)
        ? prismpath_seo_team_item_list_schema($context)
        : array();
}

function prismpath_seo_build_blog_item_list_node(array $context)
{
    return !empty($context['is_blog_index']) ? prismpath_seo_blog_item_list_schema($context) : array();
}

function prismpath_seo_build_resource_item_list_node(array $context)
{
    return prismpath_seo_resource_item_list_schema($context);
}

function prismpath_seo_build_blog_posting_node(array $context)
{
    return prismpath_seo_blog_posting_schema($context);
}

function prismpath_seo_build_faq_node(array $context)
{
    return prismpath_seo_faq_schema($context);
}

function prismpath_seo_build_contact_point_node(array $context)
{
    return prismpath_seo_contact_point_schema($context);
}

function prismpath_seo_build_job_posting_node(array $context)
{
    return prismpath_seo_job_posting_schema($context);
}

function prismpath_seo_build_breadcrumb_node(array $context)
{
    return prismpath_seo_breadcrumb_schema($context);
}

function prismpath_seo_schema_nodes(array $context = array()): array
{
    $context = $context ?: prismpath_seo_current_schema_context();
    $nodes = array();

    foreach (prismpath_seo_schema_registry($context) as $callback) {
        if (!is_callable($callback)) {
            continue;
        }

        $node = call_user_func($callback, $context);
        if (!$node) {
            continue;
        }

        if (is_array($node) && prismpath_seo_is_assoc_array($node)) {
            $nodes[] = $node;
            continue;
        }

        foreach ((array) $node as $nested_node) {
            if (is_array($nested_node)) {
                $nodes[] = $nested_node;
            }
        }
    }

    $nodes = prismpath_seo_schema_dedupe_nodes($nodes);
    return apply_filters('prismpath_seo_schema_nodes', $nodes, $context);
}

function prismpath_seo_schema_graph(array $nodes): array
{
    return array(
        '@context' => 'https://schema.org',
        '@graph' => array_values(prismpath_seo_schema_dedupe_nodes($nodes)),
    );
}

function prismpath_seo_schema_types_for_post(int $post_id): array
{
    $post_type = (string) get_post_type($post_id);
    $slug = (string) get_post_field('post_name', $post_id);
    $record = prismpath_seo_content_record($post_id);
    $resource_record = prismpath_seo_resource_record_for_slug($slug);
    $types = array();

    if ((int) get_option('page_on_front') === $post_id) {
        $types = array('MedicalOrganization', 'WebSite', 'WebPage');
    } elseif ((int) get_option('page_for_posts') === $post_id || 'blog' === $slug) {
        $types = array('CollectionPage', 'ItemList', 'BreadcrumbList');
    } elseif ('post' === $post_type) {
        $types = array('WebPage', 'BlogPosting', 'Article', 'BreadcrumbList');
    } elseif ('team_member' === $post_type) {
        $types = array('ProfilePage', 'Person', 'BreadcrumbList');
    } elseif ('about' === $slug) {
        $types = array('MedicalOrganization', 'AboutPage', 'ProfilePage', 'ItemList', 'BreadcrumbList');
    } elseif ('team' === $slug) {
        $types = array('CollectionPage', 'ItemList', 'BreadcrumbList');
    } elseif ('resources' === $slug) {
        $types = array('CollectionPage', 'ItemList', 'BreadcrumbList');
    } elseif ('contact' === $slug) {
        $types = array('MedicalOrganization', 'ContactPage', 'ContactPoint', 'BreadcrumbList');
    } elseif (in_array($slug, array('privacy-policy', 'hipaa-policy', 'accessibility-statement', 'careers'), true)) {
        $types = array('WebPage', 'BreadcrumbList');
        if ('careers' === $slug && prismpath_seo_post_has_complete_job_posting($post_id)) {
            $types[] = 'JobPosting';
        }
    } elseif ($resource_record) {
        $types = array('MedicalWebPage', 'BreadcrumbList');
    } elseif (is_array($record) && !empty($record['schema_service_type'])) {
        $types = array('MedicalWebPage', 'Service', 'BreadcrumbList');
    } else {
        $types = array('WebPage', 'BreadcrumbList');
    }

    if (is_array($record) && !empty($record['faqs']) && is_array($record['faqs'])) {
        $types[] = 'FAQPage';
    }

    return array_values(array_unique($types));
}

function prismpath_schema_output(): void
{
    if (is_admin() || is_feed() || is_404() || is_search()) {
        return;
    }

    $post_id = prismpath_seo_current_post_id();
    $agent_schemas = $post_id ? prismpath_seo_agent_schemas($post_id) : array();
    if ($agent_schemas) {
        prismpath_jsonld(count($agent_schemas) === 1 ? $agent_schemas[0] : prismpath_seo_schema_graph($agent_schemas));
        return;
    }

    $nodes = prismpath_seo_schema_nodes();
    if ($nodes) {
        prismpath_jsonld(prismpath_seo_schema_graph($nodes));
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

    $pages = get_posts(array('post_type' => array('page', 'post', 'team_member'), 'post_status' => 'publish', 'posts_per_page' => -1));
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
