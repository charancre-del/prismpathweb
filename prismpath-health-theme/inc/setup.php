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

function prismpath_update_page_seo_meta(int $post_id, array $content): void
{
    if (!$post_id) {
        return;
    }

    if (!empty($content['meta_description'])) {
        update_post_meta($post_id, 'meta_description', sanitize_text_field($content['meta_description']));
    }

    if (!empty($content['seo_title'])) {
        update_post_meta($post_id, '_prismpath_seo_title', sanitize_text_field($content['seo_title']));
    }
}

function prismpath_static_page_seo(string $slug): ?array
{
    $pages = array(
        'services' => array(
            'seo_title' => 'Neuroaffirming Mental Health Services | Prismpath Health',
            'meta_description' => 'Explore Prismpath Health services for adult therapy, psychiatry, occupational therapy, ADHD and Autism assessment, caregiver support, and accommodations.',
        ),
        'insurance-payment' => array(
            'seo_title' => 'Insurance and Payment Options | Prismpath Health',
            'meta_description' => 'Prismpath Health accepts Medicare and major commercial plans, verifies benefits, and offers self-pay, CareCredit, and deposit pathways when appropriate.',
        ),
        'team' => array(
            'seo_title' => 'Prismpath Health Team | Neuroaffirming Mental Health Providers',
            'meta_description' => 'Meet the Prismpath Health team providing neuroaffirming therapy, psychiatric care, occupational therapy, assessment, and family mental health support.',
        ),
        'contact' => array(
            'seo_title' => 'Contact Prismpath Health | Book a Consultation',
            'meta_description' => 'Contact Prismpath Health to ask about neuroaffirming therapy, psychiatry, ADHD and Autism assessment, occupational therapy, and family support.',
        ),
        'privacy-policy' => array(
            'seo_title' => 'Privacy Policy | Prismpath Health',
            'meta_description' => 'Read how Prismpath Health handles website inquiry information, consultation requests, privacy review, and care-related privacy notices.',
        ),
        'hipaa-policy' => array(
            'seo_title' => 'HIPAA Policy and Notice of Privacy Practices | Prismpath Health',
            'meta_description' => 'Read Prismpath Health HIPAA policy information about protected health information, privacy rights, permitted uses, and how to ask questions.',
        ),
        'accessibility-statement' => array(
            'seo_title' => 'Accessibility Statement | Prismpath Health',
            'meta_description' => 'Prismpath Health is committed to clear, responsive, keyboard-friendly, and accessible website experiences for visitors with diverse access needs.',
        ),
    );

    return $pages[$slug] ?? null;
}

function prismpath_legal_name(): string
{
    return function_exists('prismpath_setting')
        ? prismpath_setting('legal_name', 'Lbee Health Practive Group PLLC')
        : 'Lbee Health Practive Group PLLC';
}

function prismpath_dba_notice(): string
{
    return '<p><strong>Legal entity notice:</strong> ' . esc_html(prismpath_legal_name()) . ' dba Prismpath Health.</p>';
}

function prismpath_create_child_page_if_missing(string $title, string $slug, int $parent_id, string $excerpt = ''): int
{
    $path = $parent_id ? get_post_field('post_name', $parent_id) . '/' . $slug : $slug;
    $existing = get_page_by_path($path);
    if (!$existing instanceof WP_Post) {
        $existing = get_page_by_path($slug);
    }

    if ($existing instanceof WP_Post) {
        $updates = array(
            'ID' => $existing->ID,
            'post_excerpt' => $excerpt,
            'post_status' => 'publish',
        );
        if ($parent_id && (int) $existing->post_parent !== $parent_id) {
            $updates['post_parent'] = $parent_id;
        }
        wp_update_post($updates);
        return (int) $existing->ID;
    }

    return (int) wp_insert_post(array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_title' => $title,
        'post_name' => $slug,
        'post_parent' => $parent_id,
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
        $existing_content = (string) $existing->post_content;
        $missing_legal_entity = false === strpos($existing_content, prismpath_legal_name());
        $missing_exact_dba_wording = false === strpos($existing_content, prismpath_legal_name() . ' dba Prismpath Health');
        if ('' === trim((string) $existing->post_content) || 'publish' !== $existing->post_status || $old_generated_content) {
            wp_update_post(array(
                'ID' => $existing->ID,
                'post_excerpt' => $excerpt,
                'post_content' => $content,
                'post_status' => 'publish',
            ));
        } elseif (in_array($slug, array('privacy-policy', 'hipaa-policy', 'accessibility-statement'), true) && ($missing_legal_entity || $missing_exact_dba_wording)) {
            $updated_content = preg_replace(
                '/<p><strong>Legal entity notice:<\/strong>.*?<\/p>/',
                prismpath_dba_notice(),
                $existing_content,
                1
            );
            if (!is_string($updated_content) || $updated_content === $existing_content) {
                $updated_content = prismpath_dba_notice() . $existing_content;
            }
            wp_update_post(array(
                'ID' => $existing->ID,
                'post_excerpt' => $excerpt,
                'post_content' => $updated_content,
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

function prismpath_retire_resource_pages(): void
{
    $slugs = array(
        'resources',
        'adult-adhd-autism-assessment-guide',
        'neuroaffirming-therapy-for-adults',
        'psychiatric-medication-management-neurodivergent-adults',
        'occupational-therapy-sensory-regulation-adults',
        'whole-family-mental-health-caregiver-support',
        'accommodations-documentation-support',
        'insurance-payment-guide',
    );

    foreach ($slugs as $slug) {
        $page = get_page_by_path($slug);
        if (!$page instanceof WP_Post) {
            $page = get_page_by_path('resources/' . $slug);
        }
        if ($page instanceof WP_Post && 'draft' !== $page->post_status) {
            wp_update_post(array(
                'ID' => $page->ID,
                'post_status' => 'draft',
            ));
        }
    }
}

function prismpath_remove_resources_menu_item($menu_id): void
{
    $items = wp_get_nav_menu_items($menu_id);
    if (!is_array($items)) {
        return;
    }

    foreach ($items as $item) {
        $url = untrailingslashit((string) $item->url);
        if ('Resources' === $item->title || untrailingslashit(home_url('/resources/')) === $url) {
            wp_delete_post((int) $item->ID, true);
        }
    }
}

function prismpath_seed_policy_pages(): void
{
    $privacy = prismpath_create_policy_page_if_missing(
        'Privacy Policy',
        'privacy-policy',
        prismpath_dba_notice() . '<p>Prismpath Health uses information submitted through this website to respond to inquiries, route consultation requests, and support care coordination. Please do not submit emergencies or detailed clinical history through the contact form.</p><p>Information may be shared with authorized team members or service providers when needed to operate the website, protect the site, and respond to requests. Care-related privacy notices may be provided separately when services begin.</p>',
        'How Prismpath Health handles website inquiries and privacy review.'
    );
    if ($privacy) {
        update_option('wp_page_for_privacy_policy', $privacy);
        prismpath_update_page_seo_meta($privacy, prismpath_static_page_seo('privacy-policy') ?? array());
    }

    $hipaa = prismpath_create_policy_page_if_missing(
        'HIPAA Policy',
        'hipaa-policy',
        prismpath_dba_notice() . '<h2>HIPAA Notice and Privacy Practices</h2><p>Prismpath Health is committed to protecting health information and handling protected health information in a manner consistent with applicable privacy and security requirements. This page is intended as website-facing HIPAA policy information and should be reviewed against the final clinical, legal, and operational notice before launch.</p><h2>How information may be used</h2><p>When services begin, health information may be used or disclosed for treatment, payment, and health care operations. Examples may include care coordination, appointment support, billing, benefits verification, quality review, and required administrative activities.</p><h2>Client privacy rights</h2><p>Clients may have rights to request access to records, ask for corrections, request certain restrictions, request confidential communications, receive an accounting of certain disclosures, and receive a copy of the final Notice of Privacy Practices. Rights may depend on applicable law, identity verification, and clinical or operational requirements.</p><h2>Website and emergency limitations</h2><p>Please do not submit emergencies, crisis needs, or detailed clinical history through the website contact form. If you are experiencing an emergency, call 911 or go to the nearest emergency department. Website inquiries are used to respond to requests and are not a substitute for a therapeutic relationship, medical advice, or emergency support.</p><h2>Questions or concerns</h2><p>Questions about privacy practices, records, or HIPAA-related requests should be directed to Prismpath Health through the Contact page or the final privacy contact designated by the practice. Concerns can be raised without retaliation. Final complaint rights and reporting details should be confirmed during legal review before public launch.</p>',
        'Prismpath Health HIPAA policy information and privacy practices notice.'
    );
    if ($hipaa) {
        prismpath_update_page_seo_meta($hipaa, prismpath_static_page_seo('hipaa-policy') ?? array());
    }

    $accessibility = prismpath_create_policy_page_if_missing(
        'Accessibility Statement',
        'accessibility-statement',
        prismpath_dba_notice() . '<p>Prismpath Health is committed to making its website usable and welcoming for visitors with diverse access needs. We aim for clear language, keyboard-friendly navigation, readable contrast, responsive layouts, and meaningful alternative text where images communicate content.</p><p>If you encounter an accessibility barrier, contact us through the Contact page so our team can review the issue and improve the experience.</p>',
        'Prismpath Health website accessibility commitment.'
    );
    if ($accessibility) {
        prismpath_update_page_seo_meta($accessibility, prismpath_static_page_seo('accessibility-statement') ?? array());
    }
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
    prismpath_retire_resource_pages();

    $pages = array(
        array('Home', 'home', 'A clearer path to mental health care for every brain and every family.'),
        array('Services', 'services', 'Neuroaffirming therapy, psychiatry, occupational therapy, assessments, and whole-family support.'),
        array('Therapy', 'therapy', prismpath_page_content('therapy')['intro']),
        array('Psychiatry', 'psychiatry', prismpath_page_content('psychiatry')['intro']),
        array('ADHD & Autism Assessments', 'adhd-autism-assessments', prismpath_page_content('adhd-autism-assessments')['intro']),
        array('Occupational Therapy', 'occupational-therapy', prismpath_page_content('occupational-therapy')['intro']),
        array('Whole Family Mental Health', 'whole-family-mental-health', prismpath_page_content('whole-family-mental-health')['intro']),
        array('Approach', 'approach', prismpath_page_content('approach')['intro']),
        array('Insurance & Payment', 'insurance-payment', 'Accepted plans, benefits verification, self-pay, CareCredit, and deposit pathways.'),
        array('Team', 'team', 'Meet the people behind Prismpath Health.'),
        array('Contact', 'contact', 'Start the conversation with Prismpath Health.'),
        array('Group Support', 'group-support', prismpath_page_content('group-support')['intro']),
        array('Referral Partners', 'referral-partners', prismpath_page_content('referral-partners')['intro']),
        array('Accommodations', 'accommodations', prismpath_page_content('accommodations')['intro']),
    );

    $home_id = 0;
    $page_ids_by_slug = array();
    foreach ($pages as $page) {
        $id = prismpath_create_page_if_missing($page[0], $page[1], $page[2]);
        $page_ids_by_slug[$page[1]] = $id;
        $content_record = prismpath_content_record_by_slug($page[1]);
        if ($content_record) {
            prismpath_update_page_seo_meta($id, $content_record);
        } else {
            prismpath_update_page_seo_meta($id, prismpath_static_page_seo($page[1]) ?? array());
        }
        if ('home' === $page[1]) {
            $home_id = $id;
        }
    }
    if (function_exists('prismpath_seed_all_page_editor_meta')) {
        prismpath_seed_all_page_editor_meta($page_ids_by_slug);
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
            'Insurance' => '/insurance-payment/',
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
    } else {
        $menu_items = wp_get_nav_menu_items($menu->term_id);
        $labels = is_array($menu_items) ? wp_list_pluck($menu_items, 'title') : array();
        prismpath_remove_resources_menu_item($menu->term_id);
        $extra_menu_items = array(
            'Insurance' => '/insurance-payment/',
        );
        foreach ($extra_menu_items as $label => $path) {
            if (!in_array($label, $labels, true)) {
                wp_update_nav_menu_item($menu->term_id, 0, array(
                    'menu-item-title' => $label,
                    'menu-item-url' => home_url($path),
                    'menu-item-status' => 'publish',
                ));
            }
        }
    }

    flush_rewrite_rules();
}
add_action('after_switch_theme', 'prismpath_seed_required_pages');

function prismpath_seed_content_updates(): void
{
    $target_version = '2026-05-09-legal-dba-wording-v9';
    if (get_option('prismpath_content_seed_version') === $target_version) {
        return;
    }

    prismpath_seed_required_pages();
    update_option('prismpath_content_seed_version', $target_version);
}
add_action('init', 'prismpath_seed_content_updates', 20);
