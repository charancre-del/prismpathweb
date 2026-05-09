<?php
/**
 * Theme setup and custom post types.
 *
 * @package Prismpath_Health
 */

if (!defined('ABSPATH')) {
    exit;
}

function prismpath_theme_setup(): void
{
    load_theme_textdomain('prismpath-health', PRISMPATH_THEME_DIR . '/languages');

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script'));
    add_theme_support('responsive-embeds');
    add_theme_support('custom-logo', array(
        'height' => 120,
        'width' => 360,
        'flex-height' => true,
        'flex-width' => true,
    ));

    add_image_size('prismpath-hero', 1440, 960, true);
    add_image_size('prismpath-card', 720, 540, true);

    register_nav_menus(array(
        'primary' => __('Primary Menu', 'prismpath-health'),
        'footer' => __('Footer Menu', 'prismpath-health'),
    ));
}
add_action('after_setup_theme', 'prismpath_theme_setup');

function prismpath_register_team_member_cpt(): void
{
    register_post_type('team_member', array(
        'labels' => array(
            'name' => __('Team Members', 'prismpath-health'),
            'singular_name' => __('Team Member', 'prismpath-health'),
            'add_new_item' => __('Add Team Member', 'prismpath-health'),
            'edit_item' => __('Edit Team Member', 'prismpath-health'),
        ),
        'public' => true,
        'has_archive' => false,
        'menu_icon' => 'dashicons-groups',
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'page-attributes'),
        'rewrite' => array('slug' => 'bio', 'with_front' => false),
        'show_in_rest' => true,
    ));
}
add_action('init', 'prismpath_register_team_member_cpt');

function prismpath_allow_webp_uploads(array $mimes): array
{
    $mimes['webp'] = 'image/webp';
    return $mimes;
}
add_filter('upload_mimes', 'prismpath_allow_webp_uploads');

function prismpath_create_page_if_missing(string $title, string $slug, string $excerpt = ''): int
{
    $existing = get_page_by_path($slug);
    if ($existing instanceof WP_Post) {
        $current_excerpt = trim((string) $existing->post_excerpt);
        $old_generated_excerpt = false !== strpos($current_excerpt, 'children, teens')
            || false !== strpos($current_excerpt, 'child and teen');
        if ('' === $current_excerpt || $old_generated_excerpt) {
            wp_update_post(array(
                'ID' => $existing->ID,
                'post_excerpt' => $excerpt,
            ));
        }
        return (int) $existing->ID;
    }

    return (int) wp_insert_post(array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_title' => $title,
        'post_name' => $slug,
        'post_excerpt' => $excerpt,
        'post_content' => '',
    ));
}

function prismpath_create_policy_page_if_missing(string $title, string $slug, string $content, string $excerpt = ''): int
{
    $existing = get_page_by_path($slug);
    if ($existing instanceof WP_Post) {
        $old_generated_content = false !== strpos((string) $existing->post_content, 'review against the final production')
            || false !== strpos((string) $existing->post_content, 'reviewed against the final production')
            || false !== strpos((string) $existing->post_content, 'Final privacy practices should reflect');
        if ('' === trim((string) $existing->post_content) || 'publish' !== $existing->post_status || $old_generated_content) {
            wp_update_post(array(
                'ID' => $existing->ID,
                'post_excerpt' => $excerpt,
                'post_content' => $content,
                'post_status' => 'publish',
            ));
        }
        return (int) $existing->ID;
    }

    return (int) wp_insert_post(array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_title' => $title,
        'post_name' => $slug,
        'post_excerpt' => $excerpt,
        'post_content' => $content,
    ));
}

function prismpath_seed_site_identity(): void
{
    $current_name = trim((string) get_option('blogname'));
    if ('' === $current_name || in_array($current_name, array('Prismpath', 'Just another WordPress site'), true)) {
        update_option('blogname', 'Prismpath Health');
    }

    $current_description = trim((string) get_option('blogdescription'));
    if ('' === $current_description || 'Just another WordPress site' === $current_description) {
        update_option('blogdescription', 'Whole-family neuroaffirming mental health care');
    }
}

function prismpath_retire_default_sample_page(): void
{
    $sample = get_page_by_path('sample-page');
    if (!$sample instanceof WP_Post || 'Sample Page' !== $sample->post_title) {
        return;
    }

    wp_update_post(array(
        'ID' => $sample->ID,
        'post_status' => 'draft',
    ));
}

function prismpath_seed_policy_pages(): void
{
    $privacy = prismpath_create_policy_page_if_missing(
        'Privacy Policy',
        'privacy-policy',
        '<p>Prismpath Health uses information submitted through this website to respond to inquiries, route consultation requests, and support care coordination. Please do not submit emergencies or detailed clinical history through the contact form.</p><p>Information may be shared with authorized team members or service providers when needed to operate the website, protect the site, and respond to requests. Care-related privacy notices may be provided separately when services begin.</p>',
        'How Prismpath Health handles website inquiries and privacy review.'
    );
    if ($privacy) {
        update_option('wp_page_for_privacy_policy', $privacy);
    }

    prismpath_create_policy_page_if_missing(
        'Accessibility Statement',
        'accessibility-statement',
        '<p>Prismpath Health is committed to making its website usable and welcoming for visitors with diverse access needs. We aim for clear language, keyboard-friendly navigation, readable contrast, responsive layouts, and meaningful alternative text where images communicate content.</p><p>If you encounter an accessibility barrier, contact us through the Contact page so our team can review the issue and improve the experience.</p>',
        'Prismpath Health website accessibility commitment.'
    );
}

function prismpath_seed_team_members(): void
{
    foreach (prismpath_default_team_members() as $member) {
        $existing = get_page_by_path($member['slug'], OBJECT, 'team_member');
        $content = '<p>' . implode('</p><p>', array_map('esc_html', $member['bio'])) . '</p>';
        $postarr = array(
            'post_type' => 'team_member',
            'post_status' => 'publish',
            'post_title' => $member['name'],
            'post_name' => $member['slug'],
            'post_excerpt' => $member['role'],
            'post_content' => $content,
            'menu_order' => (int) $member['order'],
        );

        if ($existing instanceof WP_Post) {
            $postarr['ID'] = $existing->ID;
            if ('' !== trim((string) $existing->post_content)) {
                unset($postarr['post_content']);
            }
            wp_update_post($postarr);
            $post_id = (int) $existing->ID;
        } else {
            $post_id = (int) wp_insert_post($postarr);
        }

        if ($post_id && !empty($member['photo'])) {
            update_post_meta($post_id, '_prismpath_team_photo', sanitize_file_name($member['photo']));
        }
    }
}

function prismpath_seed_required_pages(): void
{
    prismpath_seed_site_identity();
    prismpath_retire_default_sample_page();

    $pages = array(
        array('Home', 'home', 'A clearer path to mental health care for every brain and every family.'),
        array('Services', 'services', 'Neuroaffirming therapy, psychiatry, occupational therapy, assessments, and whole-family support.'),
        array('Therapy', 'therapy', 'Collaborative therapy that honors each person in the room.'),
        array('Psychiatry', 'psychiatry', 'Thoughtful psychiatric care and medication management.'),
        array('ADHD & Autism Assessments', 'adhd-autism-assessments', 'Respectful assessments for adults seeking clarity.'),
        array('Occupational Therapy', 'occupational-therapy', 'Practical supports for sensory regulation, routines, and daily life.'),
        array('Whole Family Mental Health', 'whole-family-mental-health', 'Family-systems support for caregivers, with pediatric therapy pathways through Chroma Early Start.'),
        array('Approach', 'approach', 'Neuroaffirming, whole-person care grounded in collaboration.'),
        array('Team', 'team', 'Meet the people behind Prismpath Health.'),
        array('Contact', 'contact', 'Start the conversation with Prismpath Health.'),
        array('Group Support', 'group-support', 'Structured support and connection.'),
        array('Referral Partners', 'referral-partners', 'Referral pathways for partners and providers.'),
        array('Accommodations', 'accommodations', 'Support for care-aligned accommodations planning.'),
    );

    $home_id = 0;
    foreach ($pages as $page) {
        $id = prismpath_create_page_if_missing($page[0], $page[1], $page[2]);
        if ('home' === $page[1]) {
            $home_id = $id;
        }
    }

    if ($home_id) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $home_id);
    }

    prismpath_seed_policy_pages();
    prismpath_seed_team_members();

    $menu_name = 'Prismpath Primary';
    $menu = wp_get_nav_menu_object($menu_name);
    if (!$menu) {
        $menu_id = wp_create_nav_menu($menu_name);
        if (is_wp_error($menu_id)) {
            return;
        }
        $menu_items = array(
            'Services' => '/services/',
            'Assessments' => '/adhd-autism-assessments/',
            'Whole Family Mental Health' => '/whole-family-mental-health/',
            'Approach' => '/approach/',
            'Contact' => '/contact/',
        );
        foreach ($menu_items as $label => $path) {
            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => $label,
                'menu-item-url' => home_url($path),
                'menu-item-status' => 'publish',
            ));
        }
        $locations = get_theme_mod('nav_menu_locations', array());
        $locations['primary'] = $menu_id;
        set_theme_mod('nav_menu_locations', $locations);
    }

    flush_rewrite_rules();
}
add_action('after_switch_theme', 'prismpath_seed_required_pages');
