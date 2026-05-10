<?php
/**
 * Admin metaboxes for editable page SEO and template copy.
 *
 * @package Prismpath_Health
 */

if (!defined('ABSPATH')) {
    exit;
}

function prismpath_editor_supported_page_slugs(): array
{
    return array_merge(array_keys(prismpath_default_pages()), array_keys(prismpath_resource_pages()));
}

function prismpath_editor_field_value(int $post_id, string $key): string
{
    return (string) get_post_meta($post_id, $key, true);
}

function prismpath_editor_add_page_metaboxes(): void
{
    add_meta_box(
        'prismpath-page-seo-content',
        __('Prismpath SEO & Template Content', 'prismpath-health'),
        'prismpath_editor_render_page_metabox',
        'page',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'prismpath_editor_add_page_metaboxes');

function prismpath_editor_render_page_metabox(WP_Post $post): void
{
    $slug = (string) $post->post_name;
    $defaults = prismpath_content_record_by_slug($slug);
    wp_nonce_field('prismpath_save_page_meta', 'prismpath_page_meta_nonce');
    ?>
    <p class="description">
        <?php esc_html_e('Use these fields to override seeded Prismpath template copy. Leave a field blank to keep the default content model.', 'prismpath-health'); ?>
    </p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row"><label for="prismpath_seo_title"><?php esc_html_e('SEO title', 'prismpath-health'); ?></label></th>
                <td>
                    <input class="large-text" id="prismpath_seo_title" name="prismpath_seo_title" type="text" value="<?php echo esc_attr(prismpath_editor_field_value($post->ID, '_prismpath_seo_title')); ?>" placeholder="<?php echo esc_attr($defaults['seo_title'] ?? ''); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="prismpath_meta_description"><?php esc_html_e('Meta description', 'prismpath-health'); ?></label></th>
                <td>
                    <textarea class="large-text" id="prismpath_meta_description" name="prismpath_meta_description" rows="3" maxlength="320" placeholder="<?php echo esc_attr($defaults['meta_description'] ?? ''); ?>"><?php echo esc_textarea(prismpath_editor_field_value($post->ID, 'meta_description')); ?></textarea>
                </td>
            </tr>
            <?php if (in_array($slug, prismpath_editor_supported_page_slugs(), true)) : ?>
                <tr>
                    <th scope="row"><label for="prismpath_page_hero_title"><?php esc_html_e('Hero title', 'prismpath-health'); ?></label></th>
                    <td>
                        <input class="large-text" id="prismpath_page_hero_title" name="prismpath_page_hero_title" type="text" value="<?php echo esc_attr(prismpath_editor_field_value($post->ID, '_prismpath_page_hero_title')); ?>" placeholder="<?php echo esc_attr($defaults['title'] ?? ''); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="prismpath_page_intro"><?php esc_html_e('Intro copy', 'prismpath-health'); ?></label></th>
                    <td>
                        <textarea class="large-text" id="prismpath_page_intro" name="prismpath_page_intro" rows="4" placeholder="<?php echo esc_attr($defaults['intro'] ?? ''); ?>"><?php echo esc_textarea(prismpath_editor_field_value($post->ID, '_prismpath_page_intro')); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="prismpath_page_panel_heading"><?php esc_html_e('Side panel heading', 'prismpath-health'); ?></label></th>
                    <td>
                        <input class="large-text" id="prismpath_page_panel_heading" name="prismpath_page_panel_heading" type="text" value="<?php echo esc_attr(prismpath_editor_field_value($post->ID, '_prismpath_page_panel_heading')); ?>" placeholder="<?php esc_attr_e('Care shaped around real life.', 'prismpath-health'); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="prismpath_page_panel_body"><?php esc_html_e('Side panel body', 'prismpath-health'); ?></label></th>
                    <td>
                        <textarea class="large-text" id="prismpath_page_panel_body" name="prismpath_page_panel_body" rows="3" placeholder="<?php esc_attr_e('We combine clinical insight, practical strategy, and respectful collaboration so care feels usable outside the session too.', 'prismpath-health'); ?>"><?php echo esc_textarea(prismpath_editor_field_value($post->ID, '_prismpath_page_panel_body')); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="prismpath_page_points"><?php esc_html_e('What this can include', 'prismpath-health'); ?></label></th>
                    <td>
                        <textarea class="large-text code" id="prismpath_page_points" name="prismpath_page_points" rows="6" placeholder="<?php echo esc_attr(implode("\n", $defaults['points'] ?? array())); ?>"><?php echo esc_textarea(prismpath_editor_field_value($post->ID, '_prismpath_page_points')); ?></textarea>
                        <p class="description"><?php esc_html_e('Enter one item per line.', 'prismpath-health'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="prismpath_page_sections"><?php esc_html_e('Long-form sections', 'prismpath-health'); ?></label></th>
                    <td>
                        <textarea class="large-text code" id="prismpath_page_sections" name="prismpath_page_sections" rows="8"><?php echo esc_textarea(prismpath_editor_field_value($post->ID, '_prismpath_page_sections')); ?></textarea>
                        <p class="description"><?php esc_html_e('Use blocks separated by a blank line. First line is the section heading; remaining lines are the section body.', 'prismpath-health'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="prismpath_page_faqs"><?php esc_html_e('FAQs', 'prismpath-health'); ?></label></th>
                    <td>
                        <textarea class="large-text code" id="prismpath_page_faqs" name="prismpath_page_faqs" rows="8"><?php echo esc_textarea(prismpath_editor_field_value($post->ID, '_prismpath_page_faqs')); ?></textarea>
                        <p class="description"><?php esc_html_e('Use blocks separated by a blank line. First line is the question; remaining lines are the answer.', 'prismpath-health'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="prismpath_page_related_links"><?php esc_html_e('Related links', 'prismpath-health'); ?></label></th>
                    <td>
                        <textarea class="large-text code" id="prismpath_page_related_links" name="prismpath_page_related_links" rows="5"><?php echo esc_textarea(prismpath_editor_field_value($post->ID, '_prismpath_page_related_links')); ?></textarea>
                        <p class="description"><?php esc_html_e('Enter one per line as Label | URL | optional description. Leave blank to use the default related links.', 'prismpath-health'); ?></p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php
}

function prismpath_editor_save_page_metabox(int $post_id): void
{
    if (!isset($_POST['prismpath_page_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['prismpath_page_meta_nonce'])), 'prismpath_save_page_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $text_fields = array(
        'prismpath_seo_title' => '_prismpath_seo_title',
        'prismpath_page_hero_title' => '_prismpath_page_hero_title',
        'prismpath_page_panel_heading' => '_prismpath_page_panel_heading',
    );
    foreach ($text_fields as $input => $meta_key) {
        $value = isset($_POST[$input]) ? sanitize_text_field(wp_unslash($_POST[$input])) : '';
        prismpath_editor_update_or_delete_meta($post_id, $meta_key, $value);
    }

    $textarea_fields = array(
        'prismpath_meta_description' => 'meta_description',
        'prismpath_page_intro' => '_prismpath_page_intro',
        'prismpath_page_panel_body' => '_prismpath_page_panel_body',
        'prismpath_page_points' => '_prismpath_page_points',
        'prismpath_page_sections' => '_prismpath_page_sections',
        'prismpath_page_faqs' => '_prismpath_page_faqs',
        'prismpath_page_related_links' => '_prismpath_page_related_links',
    );
    foreach ($textarea_fields as $input => $meta_key) {
        $value = isset($_POST[$input]) ? sanitize_textarea_field(wp_unslash($_POST[$input])) : '';
        prismpath_editor_update_or_delete_meta($post_id, $meta_key, $value);
    }
}
add_action('save_post_page', 'prismpath_editor_save_page_metabox');

function prismpath_editor_update_or_delete_meta(int $post_id, string $meta_key, string $value): void
{
    if ('' === trim($value)) {
        delete_post_meta($post_id, $meta_key);
        return;
    }

    update_post_meta($post_id, $meta_key, $value);
}

function prismpath_parse_lines_meta(string $value): array
{
    $items = preg_split('/\R/u', $value);
    if (!is_array($items)) {
        return array();
    }

    return array_values(array_filter(array_map('trim', $items)));
}

function prismpath_parse_block_pairs_meta(string $value, string $first_key, string $second_key): array
{
    $blocks = preg_split('/\R\s*\R/u', trim($value));
    if (!is_array($blocks)) {
        return array();
    }

    $pairs = array();
    foreach ($blocks as $block) {
        $lines = array_values(array_filter(array_map('trim', preg_split('/\R/u', $block) ?: array())));
        if (count($lines) < 2) {
            continue;
        }
        $first = array_shift($lines);
        $second = trim(implode(' ', $lines));
        if ('' !== $first && '' !== $second) {
            $pairs[] = array($first_key => $first, $second_key => $second);
        }
    }

    return $pairs;
}

function prismpath_page_content_overrides(int $post_id): array
{
    $overrides = array();
    $field_map = array(
        '_prismpath_page_hero_title' => 'title',
        '_prismpath_page_intro' => 'intro',
        '_prismpath_page_panel_heading' => 'panel_heading',
        '_prismpath_page_panel_body' => 'panel_body',
    );

    foreach ($field_map as $meta_key => $content_key) {
        $value = trim((string) get_post_meta($post_id, $meta_key, true));
        if ('' !== $value) {
            $overrides[$content_key] = $value;
        }
    }

    $points = prismpath_parse_lines_meta((string) get_post_meta($post_id, '_prismpath_page_points', true));
    if ($points) {
        $overrides['points'] = $points;
    }

    $sections = prismpath_parse_block_pairs_meta((string) get_post_meta($post_id, '_prismpath_page_sections', true), 'heading', 'body');
    if ($sections) {
        $overrides['sections'] = $sections;
    }

    $faqs = prismpath_parse_block_pairs_meta((string) get_post_meta($post_id, '_prismpath_page_faqs', true), 'question', 'answer');
    if ($faqs) {
        $overrides['faqs'] = $faqs;
    }

    $related = prismpath_parse_related_links_meta((string) get_post_meta($post_id, '_prismpath_page_related_links', true));
    if ($related) {
        $overrides['related_links'] = $related;
    }

    return $overrides;
}

function prismpath_parse_related_links_meta(string $value): array
{
    $lines = preg_split('/\R/u', trim($value));
    if (!is_array($lines)) {
        return array();
    }

    $links = array();
    foreach ($lines as $line) {
        $parts = array_map('trim', explode('|', (string) $line));
        if (count($parts) < 2 || '' === $parts[0] || '' === $parts[1]) {
            continue;
        }
        $links[] = array(
            'label' => $parts[0],
            'url' => $parts[1],
            'description' => $parts[2] ?? '',
        );
    }

    return $links;
}
