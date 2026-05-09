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
        'primary_email' => get_option('admin_email'),
        'legal_name' => 'Lbee Health Practive Group PLLC',
        'phone' => '',
        'text_number' => '',
        'booking_url' => '',
        'whole_family_booking_url' => '',
        'chroma_early_start_url' => 'https://chromaearlystart.com/',
        'facebook_url' => '',
        'instagram_url' => '',
        'linkedin_url' => '',
        'privacy_url' => home_url('/privacy-policy/'),
        'hipaa_url' => home_url('/hipaa-policy/'),
        'accessibility_url' => home_url('/accessibility-statement/'),
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

function prismpath_sanitize_settings($input): array
{
    $input = is_array($input) ? $input : array();
    $clean = array();

    foreach (prismpath_default_settings() as $key => $default) {
        $value = $input[$key] ?? '';
        if (str_ends_with($key, '_url')) {
            $clean[$key] = esc_url_raw($value);
        } elseif ('primary_email' === $key) {
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
        'legal_name' => 'Legal Entity Name',
        'phone' => 'Phone',
        'text_number' => 'Text Number',
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
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
