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
        echo '<div class="form-status" role="status">Thank you for reaching out. The Prismpath Health team will follow up.</div>';
    } elseif ('error' === $sent) {
        echo '<div class="form-status" role="alert">Please check the required fields and try again.</div>';
    }
    ?>
    <form class="prismpath-consult-form" method="post" action="">
        <?php wp_nonce_field('prismpath_consult_submit', 'prismpath_consult_nonce'); ?>
        <div class="form-grid">
            <label>First Name *
                <input type="text" name="first_name" autocomplete="given-name" required>
            </label>
            <label>Last Name *
                <input type="text" name="last_name" autocomplete="family-name" required>
            </label>
        </div>
        <div class="form-grid">
            <label>Email *
                <input type="email" name="email" autocomplete="email" required>
            </label>
            <label>Phone
                <input type="tel" name="phone" autocomplete="tel">
            </label>
        </div>
        <label>Pick a program
            <select name="service">
                <option value="">Select one...</option>
                <option>Therapy</option>
                <option>ADHD Assessments</option>
                <option>Autism Assessments</option>
                <option>Occupational Therapy</option>
                <option>Psychiatry</option>
                <option>Group Support</option>
                <option>Not sure yet</option>
            </select>
        </label>
        <label>How can we help? *
            <textarea name="message" rows="4" required placeholder="Please share only what is needed for scheduling and routing your inquiry."></textarea>
        </label>
        <label class="checkbox-label">
            <input type="checkbox" name="consent" value="1" required>
            <span>I understand this form is for administrative inquiries and is not for emergencies or detailed clinical history.</span>
        </label>
        <button class="button button-coral" type="submit" name="prismpath_consult_submit" value="1">Submit</button>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('prismpath_consult_form', 'prismpath_consult_form_shortcode');

function prismpath_program_signup_form_shortcode(): string
{
    $sent = isset($_GET['prismpath_signup']) ? sanitize_text_field(wp_unslash($_GET['prismpath_signup'])) : '';
    ob_start();
    if ('sent' === $sent) {
        echo '<div class="form-status" role="status">Thank you. You are on the program updates list.</div>';
    } elseif ('error' === $sent) {
        echo '<div class="form-status" role="alert">Please check the required fields and try again.</div>';
    }
    ?>
    <form class="prismpath-consult-form program-signup-form" method="post" action="">
        <?php wp_nonce_field('prismpath_signup_submit', 'prismpath_signup_nonce'); ?>
        <div class="form-grid">
            <label>First Name *
                <input type="text" name="signup_first_name" autocomplete="given-name" required>
            </label>
            <label>Last Name *
                <input type="text" name="signup_last_name" autocomplete="family-name" required>
            </label>
        </div>
        <label>Email *
            <input type="email" name="signup_email" autocomplete="email" required>
        </label>
        <button class="button button-coral" type="submit" name="prismpath_signup_submit" value="1">Yes, Please</button>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('prismpath_program_signup_form', 'prismpath_program_signup_form_shortcode');

function prismpath_insurance_verification_form_shortcode(): string
{
    $sent = isset($_GET['prismpath_insurance']) ? sanitize_text_field(wp_unslash($_GET['prismpath_insurance'])) : '';
    ob_start();
    if ('sent' === $sent) {
        echo '<div class="form-status" role="status">Thank you. The Prismpath Health team will review your insurance information and follow up with next steps.</div>';
    } elseif ('error' === $sent) {
        echo '<div class="form-status" role="alert">Please check the required fields and try again.</div>';
    } elseif ('upload' === $sent) {
        echo '<div class="form-status" role="alert">Please upload clear front and back images of your insurance card.</div>';
    }
    ?>
    <form class="prismpath-consult-form insurance-verification-form" method="post" action="" enctype="multipart/form-data">
        <?php wp_nonce_field('prismpath_insurance_submit', 'prismpath_insurance_nonce'); ?>
        <input type="text" name="insurance_website" value="" autocomplete="off" tabindex="-1" class="hp-field" aria-hidden="true">

        <div class="form-section">
            <h3>Client information</h3>
            <div class="form-grid">
                <label>First Name *
                    <input type="text" name="insurance_first_name" autocomplete="given-name" required>
                </label>
                <label>Last Name *
                    <input type="text" name="insurance_last_name" autocomplete="family-name" required>
                </label>
            </div>
            <label>Legal first name, if different from above
                <input type="text" name="insurance_legal_first_name">
            </label>
            <div class="form-grid">
                <label>Date of Birth *
                    <input type="date" name="insurance_dob" required>
                </label>
                <label>Phone Number *
                    <input type="tel" name="insurance_phone" autocomplete="tel" required>
                </label>
            </div>
            <label>Email *
                <input type="email" name="insurance_email" autocomplete="email" required>
            </label>
        </div>

        <div class="form-section">
            <h3>Address</h3>
            <label>Street Address *
                <input type="text" name="insurance_address_1" autocomplete="address-line1" required>
            </label>
            <label>Street Address Line 2
                <input type="text" name="insurance_address_2" autocomplete="address-line2">
            </label>
            <div class="form-grid form-grid-thirds">
                <label>City *
                    <input type="text" name="insurance_city" autocomplete="address-level2" required>
                </label>
                <label>State / Province *
                    <input type="text" name="insurance_state" autocomplete="address-level1" required>
                </label>
                <label>Postal / Zip Code *
                    <input type="text" name="insurance_zip" autocomplete="postal-code" required>
                </label>
            </div>
        </div>

        <div class="form-section">
            <h3>Identity and program</h3>
            <label>Gender Identity *
                <select name="insurance_gender_identity" required>
                    <option value="">Please select...</option>
                    <option>Woman</option>
                    <option>Man</option>
                    <option>Female-To-Male (FTM) / Transgender Man</option>
                    <option>Male-To-Female (MTF) / Transgender Woman</option>
                    <option>Genderqueer, neither exclusively woman or man</option>
                    <option>Other</option>
                    <option>Prefer not to say</option>
                </select>
            </label>
            <label>Pronouns *
                <select name="insurance_pronouns" required>
                    <option value="">Please select...</option>
                    <option>She/Her</option>
                    <option>He/Him</option>
                    <option>They/Them</option>
                    <option>Other</option>
                    <option>Prefer not to say</option>
                </select>
            </label>
            <label>Which program do you want to verify benefits for? *
                <select name="insurance_program" required>
                    <option value="">Please select...</option>
                    <option>Adult Autism Assessment</option>
                    <option>Adult ADHD Assessment</option>
                    <option>Combination Autism/ADHD Assessment</option>
                    <option>Therapy</option>
                    <option>Psychiatry</option>
                    <option>Occupational Therapy</option>
                    <option>Not sure yet</option>
                </select>
            </label>
        </div>

        <div class="form-section">
            <h3>Primary insurance</h3>
            <label>Insurance Company Name *
                <input type="text" name="insurance_company" required>
            </label>
            <div class="form-grid">
                <label>Insurance ID *
                    <input type="text" name="insurance_id" required>
                </label>
                <label>Group Number *
                    <input type="text" name="insurance_group_number" required>
                </label>
            </div>
            <label>Provider phone number from the back of card *
                <input type="tel" name="insurance_provider_phone" required>
            </label>
        </div>

        <div class="form-section">
            <h3>Secondary insurance, if applicable</h3>
            <label>Secondary Insurance Company
                <input type="text" name="insurance_secondary_company">
            </label>
            <div class="form-grid">
                <label>Secondary Insurance ID
                    <input type="text" name="insurance_secondary_id">
                </label>
                <label>Secondary Group Number
                    <input type="text" name="insurance_secondary_group_number">
                </label>
            </div>
            <label>Secondary provider phone number from the back of card
                <input type="tel" name="insurance_secondary_provider_phone">
            </label>
        </div>

        <div class="form-section">
            <h3>Insurance card upload</h3>
            <div class="form-grid">
                <label>Front of Insurance Card *
                    <input type="file" name="insurance_card_front" accept=".jpg,.jpeg,.png,.pdf,.webp" required>
                </label>
                <label>Back of Insurance Card *
                    <input type="file" name="insurance_card_back" accept=".jpg,.jpeg,.png,.pdf,.webp" required>
                </label>
            </div>
            <p class="form-help">Accepted file types: JPG, PNG, WebP, or PDF.</p>
        </div>

        <label class="checkbox-label">
            <input type="checkbox" name="insurance_consent" value="1" required>
            <span>I understand this form is for benefits verification and administrative follow-up. Coverage, appointment timing, authorizations, and final patient responsibility depend on my plan and are not guaranteed by submitting this form.</span>
        </label>

        <button class="button button-coral" type="submit" name="prismpath_insurance_submit" value="1">Submit Insurance Information</button>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('prismpath_insurance_verification_form', 'prismpath_insurance_verification_form_shortcode');

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

    $first_name = sanitize_text_field(wp_unslash($_POST['first_name'] ?? ''));
    $last_name = sanitize_text_field(wp_unslash($_POST['last_name'] ?? ''));
    $full_name = trim($first_name . ' ' . $last_name);
    $payload = array(
        'first_name' => $first_name,
        'last_name' => $last_name,
        'full_name' => $full_name,
        'email' => sanitize_email(wp_unslash($_POST['email'] ?? '')),
        'phone' => sanitize_text_field(wp_unslash($_POST['phone'] ?? '')),
        'service' => sanitize_text_field(wp_unslash($_POST['service'] ?? '')),
        'message' => sanitize_textarea_field(wp_unslash($_POST['message'] ?? '')),
        'submitted_at' => current_time('mysql'),
    );

    if (!$first_name || !$last_name || !$payload['email'] || !is_email($payload['email']) || !$payload['message'] || empty($_POST['consent'])) {
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

function prismpath_handle_program_signup_submission(): void
{
    if (!isset($_POST['prismpath_signup_submit'])) {
        return;
    }

    $nonce = isset($_POST['prismpath_signup_nonce']) ? sanitize_text_field(wp_unslash($_POST['prismpath_signup_nonce'])) : '';
    $redirect_fallback = home_url('/contact/#program-signup');
    $redirect_target = wp_get_referer() ?: $redirect_fallback;
    $redirect_url = wp_validate_redirect($redirect_target, $redirect_fallback);

    if (!$nonce || !wp_verify_nonce($nonce, 'prismpath_signup_submit')) {
        wp_safe_redirect(add_query_arg('prismpath_signup', 'error', $redirect_url));
        exit;
    }

    $first_name = sanitize_text_field(wp_unslash($_POST['signup_first_name'] ?? ''));
    $last_name = sanitize_text_field(wp_unslash($_POST['signup_last_name'] ?? ''));
    $email = sanitize_email(wp_unslash($_POST['signup_email'] ?? ''));
    $full_name = trim($first_name . ' ' . $last_name);

    if (!$first_name || !$last_name || !$email || !is_email($email)) {
        wp_safe_redirect(add_query_arg('prismpath_signup', 'error', $redirect_url));
        exit;
    }

    $payload = array(
        'first_name' => $first_name,
        'last_name' => $last_name,
        'full_name' => $full_name,
        'email' => $email,
        'submitted_at' => current_time('mysql'),
    );

    $to = prismpath_consult_setting('primary_email', get_option('admin_email'));
    $subject = 'New Prismpath Health program signup from ' . $full_name;
    $message = "New program signup:\n\n";
    foreach ($payload as $key => $value) {
        $message .= ucwords(str_replace('_', ' ', $key)) . ': ' . ($value ?: 'Not provided') . "\n";
    }

    wp_mail($to, $subject, $message);

    if (post_type_exists('prismpath_lead')) {
        wp_insert_post(array(
            'post_type' => 'prismpath_lead',
            'post_status' => 'publish',
            'post_title' => 'Program signup: ' . $full_name,
            'meta_input' => array(
                'lead_name' => $full_name,
                'lead_email' => $email,
                'lead_service' => 'Program updates',
                'lead_payload' => wp_json_encode($payload),
            ),
        ));
    }

    wp_safe_redirect(add_query_arg('prismpath_signup', 'sent', $redirect_url));
    exit;
}
add_action('template_redirect', 'prismpath_handle_program_signup_submission');

function prismpath_insurance_upload_error(string $redirect_url, string $type = 'upload'): void
{
    wp_safe_redirect(add_query_arg('prismpath_insurance', $type, $redirect_url));
    exit;
}

function prismpath_handle_insurance_card_upload(string $field_name, string $redirect_url): array
{
    if (empty($_FILES[$field_name]['name']) || !isset($_FILES[$field_name]['error']) || UPLOAD_ERR_OK !== (int) $_FILES[$field_name]['error']) {
        prismpath_insurance_upload_error($redirect_url);
    }

    $max_size = 10 * 1024 * 1024;
    if (!empty($_FILES[$field_name]['size']) && (int) $_FILES[$field_name]['size'] > $max_size) {
        prismpath_insurance_upload_error($redirect_url);
    }

    $allowed_mimes = array(
        'jpg|jpeg|jpe' => 'image/jpeg',
        'png' => 'image/png',
        'webp' => 'image/webp',
        'pdf' => 'application/pdf',
    );

    $file_type = wp_check_filetype_and_ext($_FILES[$field_name]['tmp_name'], $_FILES[$field_name]['name'], $allowed_mimes);
    if (empty($file_type['type'])) {
        prismpath_insurance_upload_error($redirect_url);
    }

    if (!function_exists('wp_handle_upload')) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    $uploads = wp_upload_dir();
    if (!empty($uploads['error']) || empty($uploads['basedir'])) {
        prismpath_insurance_upload_error($redirect_url);
    }

    $private_dir = trailingslashit($uploads['basedir']) . 'prismpath-private-insurance';
    if (!wp_mkdir_p($private_dir)) {
        prismpath_insurance_upload_error($redirect_url);
    }

    if (!file_exists(trailingslashit($private_dir) . '.htaccess')) {
        file_put_contents(trailingslashit($private_dir) . '.htaccess', "Require all denied\nDeny from all\n");
    }
    if (!file_exists(trailingslashit($private_dir) . 'index.php')) {
        file_put_contents(trailingslashit($private_dir) . 'index.php', "<?php\n// Silence is golden.\n");
    }

    $original_name = sanitize_file_name(wp_unslash($_FILES[$field_name]['name']));
    $file_name = wp_unique_filename($private_dir, $original_name);
    $destination = trailingslashit($private_dir) . $file_name;

    if (!move_uploaded_file($_FILES[$field_name]['tmp_name'], $destination)) {
        prismpath_insurance_upload_error($redirect_url);
    }
    chmod($destination, 0640);

    return array(
        'file' => $destination,
        'type' => $file_type['type'],
        'name' => $original_name,
        'stored_name' => $file_name,
    );
}

function prismpath_handle_insurance_submission(): void
{
    if (!isset($_POST['prismpath_insurance_submit'])) {
        return;
    }

    $nonce = isset($_POST['prismpath_insurance_nonce']) ? sanitize_text_field(wp_unslash($_POST['prismpath_insurance_nonce'])) : '';
    $redirect_fallback = home_url('/insurance-payment/#insurance-verification');
    $redirect_target = wp_get_referer() ?: $redirect_fallback;
    $redirect_url = wp_validate_redirect($redirect_target, $redirect_fallback);

    if (!$nonce || !wp_verify_nonce($nonce, 'prismpath_insurance_submit') || !empty($_POST['insurance_website'])) {
        wp_safe_redirect(add_query_arg('prismpath_insurance', 'error', $redirect_url));
        exit;
    }

    $first_name = sanitize_text_field(wp_unslash($_POST['insurance_first_name'] ?? ''));
    $last_name = sanitize_text_field(wp_unslash($_POST['insurance_last_name'] ?? ''));
    $email = sanitize_email(wp_unslash($_POST['insurance_email'] ?? ''));
    $required = array(
        $first_name,
        $last_name,
        sanitize_text_field(wp_unslash($_POST['insurance_dob'] ?? '')),
        sanitize_text_field(wp_unslash($_POST['insurance_phone'] ?? '')),
        $email,
        sanitize_text_field(wp_unslash($_POST['insurance_address_1'] ?? '')),
        sanitize_text_field(wp_unslash($_POST['insurance_city'] ?? '')),
        sanitize_text_field(wp_unslash($_POST['insurance_state'] ?? '')),
        sanitize_text_field(wp_unslash($_POST['insurance_zip'] ?? '')),
        sanitize_text_field(wp_unslash($_POST['insurance_gender_identity'] ?? '')),
        sanitize_text_field(wp_unslash($_POST['insurance_pronouns'] ?? '')),
        sanitize_text_field(wp_unslash($_POST['insurance_program'] ?? '')),
        sanitize_text_field(wp_unslash($_POST['insurance_company'] ?? '')),
        sanitize_text_field(wp_unslash($_POST['insurance_id'] ?? '')),
        sanitize_text_field(wp_unslash($_POST['insurance_group_number'] ?? '')),
        sanitize_text_field(wp_unslash($_POST['insurance_provider_phone'] ?? '')),
    );

    if (!$email || !is_email($email) || empty($_POST['insurance_consent']) || in_array('', $required, true)) {
        wp_safe_redirect(add_query_arg('prismpath_insurance', 'error', $redirect_url));
        exit;
    }

    $front_card = prismpath_handle_insurance_card_upload('insurance_card_front', $redirect_url);
    $back_card = prismpath_handle_insurance_card_upload('insurance_card_back', $redirect_url);

    $payload = array(
        'first_name' => $first_name,
        'last_name' => $last_name,
        'full_name' => trim($first_name . ' ' . $last_name),
        'legal_first_name' => sanitize_text_field(wp_unslash($_POST['insurance_legal_first_name'] ?? '')),
        'date_of_birth' => sanitize_text_field(wp_unslash($_POST['insurance_dob'] ?? '')),
        'phone' => sanitize_text_field(wp_unslash($_POST['insurance_phone'] ?? '')),
        'email' => $email,
        'address_1' => sanitize_text_field(wp_unslash($_POST['insurance_address_1'] ?? '')),
        'address_2' => sanitize_text_field(wp_unslash($_POST['insurance_address_2'] ?? '')),
        'city' => sanitize_text_field(wp_unslash($_POST['insurance_city'] ?? '')),
        'state' => sanitize_text_field(wp_unslash($_POST['insurance_state'] ?? '')),
        'zip' => sanitize_text_field(wp_unslash($_POST['insurance_zip'] ?? '')),
        'gender_identity' => sanitize_text_field(wp_unslash($_POST['insurance_gender_identity'] ?? '')),
        'pronouns' => sanitize_text_field(wp_unslash($_POST['insurance_pronouns'] ?? '')),
        'program' => sanitize_text_field(wp_unslash($_POST['insurance_program'] ?? '')),
        'insurance_company' => sanitize_text_field(wp_unslash($_POST['insurance_company'] ?? '')),
        'insurance_id' => sanitize_text_field(wp_unslash($_POST['insurance_id'] ?? '')),
        'group_number' => sanitize_text_field(wp_unslash($_POST['insurance_group_number'] ?? '')),
        'provider_phone' => sanitize_text_field(wp_unslash($_POST['insurance_provider_phone'] ?? '')),
        'secondary_company' => sanitize_text_field(wp_unslash($_POST['insurance_secondary_company'] ?? '')),
        'secondary_id' => sanitize_text_field(wp_unslash($_POST['insurance_secondary_id'] ?? '')),
        'secondary_group_number' => sanitize_text_field(wp_unslash($_POST['insurance_secondary_group_number'] ?? '')),
        'secondary_provider_phone' => sanitize_text_field(wp_unslash($_POST['insurance_secondary_provider_phone'] ?? '')),
        'front_card_file' => $front_card['stored_name'],
        'back_card_file' => $back_card['stored_name'],
        'submitted_at' => current_time('mysql'),
    );

    $to = prismpath_consult_setting('primary_email', get_option('admin_email'));
    $subject = 'New Prismpath Health insurance verification from ' . $payload['full_name'];
    $message = "New insurance verification request:\n\n";
    foreach ($payload as $key => $value) {
        $message .= ucwords(str_replace('_', ' ', $key)) . ': ' . ($value ?: 'Not provided') . "\n";
    }

    wp_mail($to, $subject, $message, array(), array($front_card['file'], $back_card['file']));

    if (post_type_exists('prismpath_lead')) {
        wp_insert_post(array(
            'post_type' => 'prismpath_lead',
            'post_status' => 'publish',
            'post_title' => 'Insurance verification: ' . $payload['full_name'],
            'meta_input' => array(
                'lead_name' => $payload['full_name'],
                'lead_email' => $payload['email'],
                'lead_phone' => $payload['phone'],
                'lead_service' => 'Insurance verification - ' . $payload['program'],
                'lead_payload' => wp_json_encode($payload),
            ),
        ));
    }

    wp_safe_redirect(add_query_arg('prismpath_insurance', 'sent', $redirect_url));
    exit;
}
add_action('template_redirect', 'prismpath_handle_insurance_submission');
