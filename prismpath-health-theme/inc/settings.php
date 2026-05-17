<?php
/**
 * Global theme settings.
 *
 * @package Prismpath_Health
 */

if (!defined('ABSPATH')) {
    exit;
}

function prismpath_default_settings(): array
{
    return array(
        'primary_email' => 'hello@lbeehealth.com',
        'careers_email' => '',
        'legal_name' => 'Lbee Health Practive Group PLLC',
        'phone' => '561-730-2457',
        'text_number' => '561-448-4229',
        'mailing_address' => 'Miami, FL 33179',
        'booking_url' => '',
        'whole_family_booking_url' => '',
        'chroma_early_start_url' => 'https://chromaearlystart.com/',
        'facebook_url' => '',
        'instagram_url' => '',
        'linkedin_url' => '',
        'privacy_url' => home_url('/privacy-policy/'),
        'hipaa_url' => home_url('/hipaa-policy/'),
        'accessibility_url' => home_url('/accessibility-statement/'),
        'global_body_script' => '',
    );
}

function prismpath_get_settings(): array
{
    $saved = get_option('prismpath_global_settings', array());
    if (!is_array($saved)) {
        $saved = array();
    }
    return array_merge(prismpath_default_settings(), $saved);
}

function prismpath_setting(string $key, string $fallback = ''): string
{
    $settings = prismpath_get_settings();
    $value = $settings[$key] ?? $fallback;
    return is_string($value) ? $value : $fallback;
}

function prismpath_register_settings(): void
{
    register_setting('prismpath_settings', 'prismpath_global_settings', array(
        'type' => 'array',
        'sanitize_callback' => 'prismpath_sanitize_settings',
        'default' => prismpath_default_settings(),
    ));
}
add_action('admin_init', 'prismpath_register_settings');

function prismpath_sanitize_global_script($value): string
{
    $value = is_string($value) ? wp_unslash($value) : '';

    if (current_user_can('unfiltered_html')) {
        return $value;
    }

    return '';
}

function prismpath_sanitize_settings($input): array
{
    $input = is_array($input) ? $input : array();
    $clean = array();

    foreach (prismpath_default_settings() as $key => $default) {
        $value = $input[$key] ?? '';
        if ('global_body_script' === $key) {
            $clean[$key] = prismpath_sanitize_global_script($value);
        } elseif (substr($key, -4) === '_url') {
            $clean[$key] = esc_url_raw($value);
        } elseif (in_array($key, array('primary_email', 'careers_email'), true)) {
            $clean[$key] = sanitize_email($value);
        } else {
            $clean[$key] = sanitize_text_field($value);
        }
    }

    return $clean;
}

function prismpath_settings_menu(): void
{
    add_theme_page(
        __('Prismpath Settings', 'prismpath-health'),
        __('Prismpath Settings', 'prismpath-health'),
        'manage_options',
        'prismpath-settings',
        'prismpath_render_settings_page'
    );
}
add_action('admin_menu', 'prismpath_settings_menu');

function prismpath_render_settings_page(): void
{
    $settings = prismpath_get_settings();
    $fields = array(
        'primary_email' => 'Primary Email',
        'careers_email' => 'Careers Email',
        'legal_name' => 'Legal Entity Name',
        'phone' => 'Phone',
        'text_number' => 'Text Number',
        'mailing_address' => 'Mailing Address',
        'booking_url' => 'Main Booking URL',
        'whole_family_booking_url' => 'Whole Family Booking URL',
        'chroma_early_start_url' => 'Chroma Early Start URL',
        'facebook_url' => 'Facebook URL',
        'instagram_url' => 'Instagram URL',
        'linkedin_url' => 'LinkedIn URL',
        'privacy_url' => 'Privacy Policy URL',
        'hipaa_url' => 'HIPAA Policy URL',
        'accessibility_url' => 'Accessibility Statement URL',
    );
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Prismpath Health Settings', 'prismpath-health'); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('prismpath_settings'); ?>
            <table class="form-table" role="presentation">
                <?php foreach ($fields as $key => $label) : ?>
                    <tr>
                        <th scope="row"><label for="prismpath-<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label></th>
                        <td>
                            <input
                                id="prismpath-<?php echo esc_attr($key); ?>"
                                class="regular-text"
                                type="text"
                                name="prismpath_global_settings[<?php echo esc_attr($key); ?>]"
                                value="<?php echo esc_attr($settings[$key]); ?>"
                            />
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <h2><?php esc_html_e('Global Scripts', 'prismpath-health'); ?></h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="prismpath-global_body_script"><?php esc_html_e('Global Body Script', 'prismpath-health'); ?></label>
                    </th>
                    <td>
                        <textarea
                            id="prismpath-global_body_script"
                            class="large-text code"
                            rows="8"
                            name="prismpath_global_settings[global_body_script]"
                        ><?php echo esc_textarea($settings['global_body_script']); ?></textarea>
                        <p class="description"><?php esc_html_e('Outputs immediately after the opening body tag via wp_body_open. For trusted tracking snippets only.', 'prismpath-health'); ?></p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function prismpath_register_global_scripts_customizer(WP_Customize_Manager $wp_customize): void
{
    $wp_customize->add_section('prismpath_global_scripts', array(
        'title' => __('Global Scripts', 'prismpath-health'),
        'priority' => 180,
    ));

    $wp_customize->add_setting('prismpath_global_settings[global_body_script]', array(
        'type' => 'option',
        'default' => prismpath_default_settings()['global_body_script'],
        'sanitize_callback' => 'prismpath_sanitize_global_script',
    ));

    $wp_customize->add_control('prismpath_global_body_script', array(
        'label' => __('Global Body Script', 'prismpath-health'),
        'description' => __('Outputs immediately after the opening body tag via wp_body_open. For trusted tracking snippets only.', 'prismpath-health'),
        'section' => 'prismpath_global_scripts',
        'settings' => 'prismpath_global_settings[global_body_script]',
        'type' => 'textarea',
    ));
}
add_action('customize_register', 'prismpath_register_global_scripts_customizer');

function prismpath_render_global_body_script(): void
{
    $script = trim(prismpath_setting('global_body_script'));

    if ('' === $script) {
        return;
    }

    echo "\n" . $script . "\n";
}
add_action('wp_body_open', 'prismpath_render_global_body_script', 1);
