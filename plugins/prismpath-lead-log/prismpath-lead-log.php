<?php
/**
 * Plugin Name: Prismpath Lead Log
 * Description: Private lead log CPT for Prismpath Health consultation inquiries.
 * Version: 1.0.0
 * Author: Prismpath Health
 * Text Domain: prismpath-lead-log
 */

if (!defined('ABSPATH')) {
    exit;
}

function prismpath_lead_log_register_cpt(): void
{
    register_post_type('prismpath_lead', array(
        'labels' => array(
            'name' => __('Prismpath Leads', 'prismpath-lead-log'),
            'singular_name' => __('Prismpath Lead', 'prismpath-lead-log'),
            'menu_name' => __('Prismpath Leads', 'prismpath-lead-log'),
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-feedback',
        'supports' => array('title'),
        'capability_type' => 'post',
        'capabilities' => array('create_posts' => 'do_not_allow'),
        'map_meta_cap' => true,
    ));
}
add_action('init', 'prismpath_lead_log_register_cpt');

function prismpath_lead_log_columns(array $columns): array
{
    return array(
        'cb' => $columns['cb'] ?? '',
        'title' => __('Lead', 'prismpath-lead-log'),
        'lead_service' => __('Service', 'prismpath-lead-log'),
        'lead_name' => __('Name', 'prismpath-lead-log'),
        'lead_email' => __('Email', 'prismpath-lead-log'),
        'lead_phone' => __('Phone', 'prismpath-lead-log'),
        'date' => __('Date', 'prismpath-lead-log'),
    );
}
add_filter('manage_prismpath_lead_posts_columns', 'prismpath_lead_log_columns');

function prismpath_lead_log_column_content(string $column, int $post_id): void
{
    $map = array(
        'lead_service' => 'lead_service',
        'lead_name' => 'lead_name',
        'lead_email' => 'lead_email',
        'lead_phone' => 'lead_phone',
    );

    if (!isset($map[$column])) {
        return;
    }

    $value = get_post_meta($post_id, $map[$column], true);
    if ('lead_email' === $column && $value) {
        echo '<a href="mailto:' . esc_attr($value) . '">' . esc_html($value) . '</a>';
        return;
    }

    echo esc_html($value ?: '-');
}
add_action('manage_prismpath_lead_posts_custom_column', 'prismpath_lead_log_column_content', 10, 2);
