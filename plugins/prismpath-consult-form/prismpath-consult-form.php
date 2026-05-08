<?php
/**
 * Plugin Name: Prismpath Consult Form
 * Description: Secure consultation request form for Prismpath Health.
 * Version: 1.0.0
 * Author: Prismpath Health
 * Text Domain: prismpath-consult-form
 */

if (!defined('ABSPATH')) {
    exit;
}

function prismpath_consult_setting(string $key, string $default = ''): string
{
    if (function_exists('prismpath_setting')) {
        return prismpath_setting($key, $default);
    }

    $settings = get_option('prismpath_global_settings', array());
    $value = is_array($settings) ? ($settings[$key] ?? $default) : $default;
    return is_string($value) ? $value : $default;
}

function prismpath_consult_form_shortcode(): string
{
    $sent = isset($_GET['prismpath_consult']) ? sanitize_text_field(wp_unslash($_GET['prismpath_consult'])) : '';
    ob_start();
    if ('sent' === $sent) {
        echo '<div class="form-status" role="status">Thank you. Your message was sent, and the Prismpath Health team will follow up.</div>';
    } elseif ('error' === $sent) {
        echo '<div class="form-status" role="alert">Please check the required fields and try again.</div>';
    }
    ?>
    <form class="prismpath-consult-form" method="post" action="">
        <?php wp_nonce_field('prismpath_consult_submit', 'prismpath_consult_nonce'); ?>
        <div class="form-grid">
            <label>Full name *
                <input type="text" name="full_name" autocomplete="name" required>
            </label>
            <label>Email *
                <input type="email" name="email" autocomplete="email" required>
            </label>
        </div>
        <div class="form-grid">
            <label>Phone
                <input type="tel" name="phone" autocomplete="tel">
            </label>
            <label>State
                <input type="text" name="state" maxlength="40">
            </label>
        </div>
        <label>Preferred service
            <select name="service">
                <option value="">Select one...</option>
                <option>Therapy</option>
                <option>Psychiatry</option>
                <option>ADHD & Autism Assessments</option>
                <option>Occupational Therapy</option>
                <option>Whole Family Mental Health</option>
                <option>Not sure yet</option>
            </select>
        </label>
        <label>Preferred contact time
            <input type="text" name="contact_time" placeholder="Morning, afternoon, or a specific window">
        </label>
        <label>How can we help? *
            <textarea name="message" rows="4" required placeholder="Please share only what is needed for scheduling and routing your inquiry."></textarea>
        </label>
        <label class="checkbox-label">
            <input type="checkbox" name="consent" value="1" required>
            <span>I understand this form is for administrative inquiries and is not for emergencies or detailed clinical history.</span>
        </label>
        <button class="button button-coral" type="submit" name="prismpath_consult_submit" value="1">Send Request</button>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('prismpath_consult_form', 'prismpath_consult_form_shortcode');

function prismpath_handle_consult_submission(): void
{
    if (!isset($_POST['prismpath_consult_submit'])) {
        return;
    }

    $nonce = isset($_POST['prismpath_consult_nonce']) ? sanitize_text_field(wp_unslash($_POST['prismpath_consult_nonce'])) : '';
    $redirect_fallback = home_url('/contact/#consult');
    $redirect_target = wp_get_referer() ?: $redirect_fallback;
    $redirect_url = wp_validate_redirect($redirect_target, $redirect_fallback);

    if (!$nonce || !wp_verify_nonce($nonce, 'prismpath_consult_submit')) {
        wp_safe_redirect(add_query_arg('prismpath_consult', 'error', $redirect_url));
        exit;
    }

    $payload = array(
        'full_name' => sanitize_text_field(wp_unslash($_POST['full_name'] ?? '')),
        'email' => sanitize_email(wp_unslash($_POST['email'] ?? '')),
        'phone' => sanitize_text_field(wp_unslash($_POST['phone'] ?? '')),
        'state' => sanitize_text_field(wp_unslash($_POST['state'] ?? '')),
        'service' => sanitize_text_field(wp_unslash($_POST['service'] ?? '')),
        'contact_time' => sanitize_text_field(wp_unslash($_POST['contact_time'] ?? '')),
        'message' => sanitize_textarea_field(wp_unslash($_POST['message'] ?? '')),
        'submitted_at' => current_time('mysql'),
    );

    if (!$payload['full_name'] || !$payload['email'] || !is_email($payload['email']) || !$payload['message'] || empty($_POST['consent'])) {
        wp_safe_redirect(add_query_arg('prismpath_consult', 'error', $redirect_url));
        exit;
    }

    $to = prismpath_consult_setting('primary_email', get_option('admin_email'));
    $subject = 'New Prismpath Health consult request from ' . $payload['full_name'];
    $message = "New consultation request:\n\n";
    foreach ($payload as $key => $value) {
        $message .= ucwords(str_replace('_', ' ', $key)) . ': ' . ($value ?: 'Not provided') . "\n";
    }

    wp_mail($to, $subject, $message);

    if (post_type_exists('prismpath_lead')) {
        wp_insert_post(array(
            'post_type' => 'prismpath_lead',
            'post_status' => 'publish',
            'post_title' => 'Consult: ' . $payload['full_name'],
            'meta_input' => array(
                'lead_name' => $payload['full_name'],
                'lead_email' => $payload['email'],
                'lead_phone' => $payload['phone'],
                'lead_service' => $payload['service'],
                'lead_payload' => wp_json_encode($payload),
            ),
        ));
    }

    wp_safe_redirect(add_query_arg('prismpath_consult', 'sent', $redirect_url));
    exit;
}
add_action('template_redirect', 'prismpath_handle_consult_submission');
