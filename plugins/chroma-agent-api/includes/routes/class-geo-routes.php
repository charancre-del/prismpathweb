<?php

namespace ChromaAgentAPI\Routes;

use ChromaAgentAPI\Utils;
use WP_REST_Request;

if (!defined('ABSPATH')) {
    exit;
}

class Geo_Routes
{
    private const NS = 'chroma-agent/v1';
    private const CACHE_KEY = 'chroma_agent_prismpath_geo_feed_v1';
    private const CACHE_TTL = 900;
    private const CONTRACT_VERSION = '2026-05-09.prismpath.1';

    public static function init(): void
    {
        add_action('save_post', [__CLASS__, 'on_post_change'], 10, 2);
        add_action('deleted_post', [__CLASS__, 'on_post_delete']);
        add_action('updated_option', [__CLASS__, 'on_option_change'], 10, 3);
        add_action('wp_head', [__CLASS__, 'output_geo_discovery_link'], 1);
        add_action('wp_head', [__CLASS__, 'output_geo_schema_signpost'], 2);
        add_filter('robots_txt', [__CLASS__, 'append_geo_robots_rules'], 20, 2);
    }

    public static function register(): void
    {
        register_rest_route(self::NS, '/geo-feed', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'get_geo_feed'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route(self::NS, '/geo-feed/(?P<record_id>[A-Za-z0-9_-]+)', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'get_geo_feed_record'],
            'permission_callback' => '__return_true',
        ]);
    }

    public static function describe_contract(): array
    {
        return [
            'route' => '/wp-json/' . self::NS . '/geo-feed',
            'detail_route' => '/wp-json/' . self::NS . '/geo-feed/{record_id}',
            'contract_version' => self::CONTRACT_VERSION,
            'public' => true,
            'cache_ttl_seconds' => self::CACHE_TTL,
            'filters' => [
                'modified_since' => 'ISO-8601 timestamp. Returns only records updated after this time.',
                'ids' => 'Comma-delimited page IDs or record IDs.',
                'refresh' => 'Truthy value bypasses the cached dataset.',
            ],
            'top_level_fields' => [
                'success',
                'cached',
                'contract_version',
                'generated_at_gmt',
                'source',
                'filters',
                'summary',
                'feed_hash',
                'brand',
                'service_area',
                'ai_citation_policy',
                'services',
                'resources',
                'pages',
                'team',
            ],
        ];
    }

    public static function public_feed_url(): string
    {
        return rest_url(self::NS . '/geo-feed');
    }

    public static function get_geo_feed(WP_REST_Request $request)
    {
        $base = self::get_base_dataset(Utils::truthy($request->get_param('refresh')));
        $filters = self::build_filters($request);
        $filtered = self::filter_dataset($base, $filters);

        return rest_ensure_response([
            'success' => true,
            'cached' => $base['cached'],
            'contract_version' => self::CONTRACT_VERSION,
            'generated_at_gmt' => $base['generated_at_gmt'],
            'source' => [
                'namespace' => self::NS,
                'route' => '/geo-feed',
                'authority' => 'Prismpath Health WordPress',
            ],
            'filters' => self::normalize_filter_output($filters),
            'summary' => [
                'service_count' => count($filtered['services']),
                'resource_count' => count($filtered['resources']),
                'page_count' => count($filtered['pages']),
                'team_count' => count($filtered['team']),
            ],
            'feed_hash' => self::hash_payload($filtered),
            'brand' => $base['brand'],
            'service_area' => $base['service_area'],
            'ai_citation_policy' => $base['ai_citation_policy'],
            'services' => $filtered['services'],
            'resources' => $filtered['resources'],
            'pages' => $filtered['pages'],
            'team' => $filtered['team'],
        ]);
    }

    public static function get_geo_feed_record(WP_REST_Request $request)
    {
        $record_id = sanitize_title((string) $request->get_param('record_id'));
        $base = self::get_base_dataset(Utils::truthy($request->get_param('refresh')));

        foreach (['services', 'resources', 'pages', 'team'] as $group) {
            foreach ($base[$group] as $record) {
                $candidates = array_filter([
                    (string) ($record['record_id'] ?? ''),
                    (string) ($record['post_id'] ?? ''),
                    (string) ($record['slug'] ?? ''),
                ]);

                if (in_array($record_id, array_map('sanitize_title', $candidates), true)) {
                    return rest_ensure_response([
                        'success' => true,
                        'cached' => $base['cached'],
                        'contract_version' => self::CONTRACT_VERSION,
                        'generated_at_gmt' => $base['generated_at_gmt'],
                        'source' => [
                            'namespace' => self::NS,
                            'route' => '/geo-feed/' . $record_id,
                            'authority' => 'Prismpath Health WordPress',
                        ],
                        'record_group' => $group,
                        'record' => $record,
                    ]);
                }
            }
        }

        return new \WP_Error('caa_geo_record_not_found', 'GEO feed record not found.', ['status' => 404]);
    }

    public static function on_post_change(int $post_id, $post): void
    {
        if (wp_is_post_revision($post_id) || !is_object($post)) {
            return;
        }

        if (in_array((string) $post->post_type, ['page', 'team_member'], true)) {
            delete_transient(self::CACHE_KEY);
        }
    }

    public static function on_post_delete(int $post_id): void
    {
        $post = get_post($post_id);
        if ($post && in_array((string) $post->post_type, ['page', 'team_member'], true)) {
            delete_transient(self::CACHE_KEY);
        }
    }

    public static function on_option_change(string $option, $old_value, $value): void
    {
        if (in_array($option, ['blogname', 'blogdescription', 'prismpath_global_settings'], true)) {
            delete_transient(self::CACHE_KEY);
        }
    }

    public static function output_geo_discovery_link(): void
    {
        if (!self::is_public_feed_available()) {
            return;
        }

        echo '<link rel="alternate" type="application/json" title="Prismpath Health GEO Feed" href="' . esc_url(self::public_feed_url()) . '">' . "\n";
    }

    public static function output_geo_schema_signpost(): void
    {
        if (!self::is_public_feed_available() || !is_front_page()) {
            return;
        }

        $geo_url = self::public_feed_url();
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'DataFeed',
            '@id' => $geo_url . '#feed',
            'name' => 'Prismpath Health Public GEO Feed',
            'url' => $geo_url,
            'encodingFormat' => 'application/json',
            'description' => 'Machine-readable public feed for Prismpath Health services, resource guides, service area, legal entity, and AI citation context.',
            'about' => [
                '@id' => trailingslashit(home_url('/')) . '#organization',
            ],
        ];

        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    }

    public static function append_geo_robots_rules(string $output, bool $public): string
    {
        if (!self::is_public_feed_available()) {
            return $output;
        }

        $output = rtrim($output);
        if ($output !== '') {
            $output .= "\n";
        }

        $path = wp_parse_url(self::public_feed_url(), PHP_URL_PATH);
        if (!is_string($path) || $path === '') {
            $path = '/wp-json/' . self::NS . '/geo-feed';
        }

        $bots = ['Googlebot', 'Google-Extended', 'OAI-SearchBot', 'GPTBot', 'ChatGPT-User', 'ClaudeBot', 'Claude-Web', 'PerplexityBot'];
        $lines = [];
        foreach ($bots as $bot) {
            $lines[] = 'User-agent: ' . $bot;
            $lines[] = 'Allow: /';
            $lines[] = 'Allow: ' . $path;
        }
        $lines[] = 'User-agent: *';
        $lines[] = 'Allow: /';
        $lines[] = 'Allow: ' . $path;

        return $output . implode("\n", $lines) . "\n";
    }

    private static function is_public_feed_available(): bool
    {
        return Utils::truthy(get_option(Utils::OPTION_ENABLED, 1));
    }

    private static function get_base_dataset(bool $refresh): array
    {
        $cached = get_transient(self::CACHE_KEY);
        if (!$refresh && is_array($cached)) {
            $cached['cached'] = true;
            return $cached;
        }

        $dataset = [
            'cached' => false,
            'generated_at_gmt' => gmdate('c'),
            'brand' => self::get_brand_payload(),
            'service_area' => [
                'type' => 'Virtual care',
                'country' => 'United States',
                'availability_note' => 'Virtual care is available where provider licensure and clinical appropriateness allow.',
                'state_pages_policy' => 'Publish state or city landing pages only when licensure, availability, and service details are verified.',
            ],
            'ai_citation_policy' => [
                'brand_name' => 'Prismpath Health',
                'legal_entity' => self::legal_dba_name(),
                'claims_policy' => [
                    'No diagnosis guarantees.',
                    'No medication guarantees.',
                    'No insurance coverage guarantees.',
                    'No accommodation approval guarantees.',
                    'Pediatric ABA, speech therapy, and pediatric occupational therapy route to Chroma Early Start.',
                ],
                'public_crawler_policy' => 'Allow Google and major AI crawlers unless legal or privacy review changes this policy.',
            ],
            'services' => self::get_service_records(),
            'resources' => self::get_resource_records(),
            'pages' => self::get_page_records(),
            'team' => self::get_team_records(),
        ];

        set_transient(self::CACHE_KEY, $dataset, self::CACHE_TTL);

        return $dataset;
    }

    private static function get_brand_payload(): array
    {
        return [
            'name' => 'Prismpath Health',
            'legal_entity' => self::legal_dba_name(),
            'site_url' => home_url('/'),
            'description' => get_bloginfo('description'),
            'contact' => [
                'email' => self::public_setting('primary_email'),
                'phone' => self::public_setting('phone'),
                'booking_url' => self::public_setting('booking_url'),
            ],
            'related_pediatric_provider' => [
                'name' => 'Chroma Early Start',
                'url' => self::public_setting('chroma_early_start_url', 'https://chromaearlystart.com/'),
                'scope' => 'Pediatric ABA, speech therapy, and pediatric occupational therapy.',
            ],
        ];
    }

    private static function get_service_records(): array
    {
        if (!function_exists('prismpath_services')) {
            return [];
        }

        $records = [];
        foreach (prismpath_services() as $service) {
            $slug = (string) ($service['slug'] ?? '');
            $content = function_exists('prismpath_content_record_by_slug') ? prismpath_content_record_by_slug($slug) : null;
            $page = get_page_by_path($slug);
            $record = [
                'record_id' => 'service-' . $slug,
                'post_id' => $page instanceof \WP_Post ? (int) $page->ID : null,
                'slug' => $slug,
                'name' => (string) ($service['title'] ?? ''),
                'canonical_url' => home_url('/' . $slug . '/'),
                'last_updated_gmt' => $page instanceof \WP_Post ? self::post_modified_gmt((int) $page->ID) : null,
                'record_hash' => null,
                'service_type' => $content['schema_service_type'] ?? null,
                'short_description' => $content['intro'] ?? ($service['summary'] ?? ''),
                'audience' => 'Adults, caregivers, family systems, and referral partners.',
                'payment_note' => 'Insurance verification, self-pay, CareCredit, and deposits may apply depending on service and plan.',
                'related_resource_url' => self::first_related_resource_url($slug),
            ];
            $record['record_hash'] = self::hash_payload($record);
            $records[] = $record;
        }

        return $records;
    }

    private static function get_resource_records(): array
    {
        if (!function_exists('prismpath_resource_pages')) {
            return [];
        }

        $records = [];
        foreach (prismpath_resource_pages() as $slug => $resource) {
            $page = get_page_by_path('resources/' . $slug);
            if (!$page instanceof \WP_Post) {
                $page = get_page_by_path((string) $slug);
            }

            $record = [
                'record_id' => 'resource-' . $slug,
                'post_id' => $page instanceof \WP_Post ? (int) $page->ID : null,
                'slug' => (string) $slug,
                'title' => (string) ($resource['title'] ?? ''),
                'canonical_url' => function_exists('prismpath_resource_url') ? prismpath_resource_url((string) $slug) : home_url('/resources/' . $slug . '/'),
                'last_updated_gmt' => $page instanceof \WP_Post ? self::post_modified_gmt((int) $page->ID) : null,
                'record_hash' => null,
                'service_type' => $resource['schema_service_type'] ?? null,
                'short_description' => (string) ($resource['intro'] ?? ''),
                'related_service_url' => !empty($resource['related_service']) ? home_url('/' . trim((string) $resource['related_service'], '/') . '/') : null,
                'faq_count' => is_array($resource['faqs'] ?? null) ? count($resource['faqs']) : 0,
                'reference_urls' => self::reference_urls($resource['references'] ?? []),
            ];
            $record['record_hash'] = self::hash_payload($record);
            $records[] = $record;
        }

        return $records;
    }

    private static function get_page_records(): array
    {
        $posts = get_posts([
            'post_type' => 'page',
            'post_status' => 'publish',
            'posts_per_page' => 200,
            'orderby' => 'menu_order title',
            'order' => 'ASC',
            'no_found_rows' => true,
        ]);

        $records = [];
        foreach ($posts as $post) {
            $slug = (string) $post->post_name;
            $record = [
                'record_id' => 'page-' . $slug,
                'post_id' => (int) $post->ID,
                'slug' => $slug,
                'title' => get_the_title($post),
                'canonical_url' => get_permalink($post),
                'last_updated_gmt' => self::post_modified_gmt((int) $post->ID),
                'record_hash' => null,
                'page_type' => self::page_type($slug),
                'meta_description' => self::meta_description((int) $post->ID),
            ];
            $record['record_hash'] = self::hash_payload($record);
            $records[] = $record;
        }

        return $records;
    }

    private static function get_team_records(): array
    {
        $posts = get_posts([
            'post_type' => 'team_member',
            'post_status' => 'publish',
            'posts_per_page' => 200,
            'orderby' => 'menu_order title',
            'order' => 'ASC',
            'no_found_rows' => true,
        ]);

        $records = [];
        foreach ($posts as $post) {
            $record = [
                'record_id' => 'team-' . $post->post_name,
                'post_id' => (int) $post->ID,
                'slug' => (string) $post->post_name,
                'name' => get_the_title($post),
                'role' => get_the_excerpt($post),
                'canonical_url' => get_permalink($post),
                'last_updated_gmt' => self::post_modified_gmt((int) $post->ID),
                'record_hash' => null,
                'works_for' => 'Prismpath Health',
            ];
            $record['record_hash'] = self::hash_payload($record);
            $records[] = $record;
        }

        return $records;
    }

    private static function build_filters(WP_REST_Request $request): array
    {
        $ids = array_filter(array_map('trim', explode(',', (string) $request->get_param('ids'))));
        $modified_since = trim((string) $request->get_param('modified_since'));

        return [
            'ids' => $ids,
            'modified_since_ts' => $modified_since !== '' ? strtotime($modified_since) ?: 0 : 0,
        ];
    }

    private static function filter_dataset(array $base, array $filters): array
    {
        $out = [];
        foreach (['services', 'resources', 'pages', 'team'] as $group) {
            $out[$group] = array_values(array_filter($base[$group], static function (array $record) use ($filters): bool {
                $ids = $filters['ids'];
                if ($ids) {
                    $candidates = array_filter([
                        (string) ($record['record_id'] ?? ''),
                        (string) ($record['post_id'] ?? ''),
                        (string) ($record['slug'] ?? ''),
                    ]);
                    if (!array_intersect(array_map('sanitize_title', $ids), array_map('sanitize_title', $candidates))) {
                        return false;
                    }
                }

                $modified_since = (int) ($filters['modified_since_ts'] ?? 0);
                if ($modified_since > 0) {
                    $updated = strtotime((string) ($record['last_updated_gmt'] ?? '')) ?: 0;
                    return $updated > $modified_since;
                }

                return true;
            }));
        }

        return $out;
    }

    private static function normalize_filter_output(array $filters): array
    {
        return [
            'ids' => $filters['ids'],
            'modified_since' => !empty($filters['modified_since_ts']) ? gmdate('c', (int) $filters['modified_since_ts']) : null,
        ];
    }

    private static function first_related_resource_url(string $service_slug): ?string
    {
        if (!function_exists('prismpath_related_links_for_slug')) {
            return null;
        }
        $links = prismpath_related_links_for_slug($service_slug);
        foreach ($links as $link) {
            if (!empty($link['url']) && false !== strpos((string) $link['url'], '/resources/')) {
                return (string) $link['url'];
            }
        }
        return null;
    }

    private static function public_setting(string $key, string $fallback = ''): ?string
    {
        $value = function_exists('prismpath_setting') ? prismpath_setting($key, $fallback) : $fallback;
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    private static function legal_dba_name(): string
    {
        $legal = self::public_setting('legal_name', 'Lbee Health Practive Group PLLC') ?: 'Lbee Health Practive Group PLLC';
        if (false !== stripos($legal, 'dba Prismpath Health')) {
            return $legal;
        }
        return $legal . ' dba Prismpath Health';
    }

    private static function page_type(string $slug): string
    {
        if ($slug === 'resources') {
            return 'resource_hub';
        }
        if (function_exists('prismpath_resource_record_by_slug') && prismpath_resource_record_by_slug($slug)) {
            return 'resource_guide';
        }
        if (function_exists('prismpath_service_by_slug') && prismpath_service_by_slug($slug)) {
            return 'service';
        }
        if (in_array($slug, ['privacy-policy', 'hipaa-policy', 'accessibility-statement'], true)) {
            return 'legal_policy';
        }
        return 'site_page';
    }

    private static function meta_description(int $post_id): ?string
    {
        $meta = trim((string) get_post_meta($post_id, 'meta_description', true));
        if ($meta !== '') {
            return $meta;
        }
        $excerpt = trim(wp_strip_all_tags((string) get_the_excerpt($post_id)));
        return $excerpt === '' ? null : $excerpt;
    }

    private static function reference_urls($references): array
    {
        if (!is_array($references)) {
            return [];
        }
        $urls = [];
        foreach ($references as $reference) {
            if (is_array($reference) && !empty($reference['url'])) {
                $urls[] = esc_url_raw((string) $reference['url']);
            }
        }
        return array_values(array_filter($urls));
    }

    private static function post_modified_gmt(int $post_id): ?string
    {
        $modified = get_post_modified_time('c', true, $post_id);
        return is_string($modified) && $modified !== '' ? $modified : null;
    }

    private static function hash_payload($value): string
    {
        return md5((string) wp_json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
}
