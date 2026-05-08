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

function prismpath_seed_required_pages(): void
{
    $pages = array(
        array('Home', 'home', 'A clearer path to mental health care for every brain and every family.'),
        array('Services', 'services', 'Neuroaffirming therapy, psychiatry, occupational therapy, assessments, and whole-family support.'),
        array('Therapy', 'therapy', 'Collaborative therapy that honors each person in the room.'),
        array('Psychiatry', 'psychiatry', 'Thoughtful psychiatric care and medication management.'),
        array('ADHD & Autism Assessments', 'adhd-autism-assessments', 'Respectful assessments for adults seeking clarity.'),
        array('Occupational Therapy', 'occupational-therapy', 'Practical supports for sensory regulation, routines, and daily life.'),
        array('Whole Family Mental Health', 'whole-family-mental-health', 'Family-systems support for caregivers, children, teens, and household rhythms.'),
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
