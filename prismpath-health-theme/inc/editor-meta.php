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
    return array_merge(
        array(
            'about',
            'careers',
            'contact',
            'insurance-payment',
            'resources',
            'services',
            'team',
        ),
        array_keys(prismpath_default_pages()),
        array_keys(prismpath_resource_pages())
    );
}

function prismpath_editor_field_value(int $post_id, string $key): string
{
    return (string) get_post_meta($post_id, $key, true);
}

function prismpath_home_post_id(): int
{
    $front_id = (int) get_option('page_on_front');
    if ($front_id > 0) {
        return $front_id;
    }

    return (int) get_queried_object_id();
}

function prismpath_home_defaults(): array
{
    return array(
        'show_trust' => '1',
        'show_services' => '1',
        'show_whole_family' => '1',
        'show_approach' => '1',
        'show_ot' => '1',
        'show_process' => '1',
        'show_consult' => '1',
        'hero_eyebrow' => 'Now welcoming new clients - virtual care where licensure allows',
        'hero_title' => 'A clearer path to mental health care for every brain and every family.',
        'hero_intro' => 'Neuroaffirming therapy, psychiatric care, occupational therapy, and ADHD & Autism assessments for adults, caregivers, and families.',
        'hero_partner_note' => 'Pediatric ABA, speech, and occupational therapy services are available through Chroma Early Start.',
        'hero_primary_label' => 'Start Your Journey',
        'hero_primary_url' => prismpath_booking_url(),
        'hero_secondary_label' => 'Explore Services',
        'hero_secondary_url' => home_url('/services/'),
        'hero_microcopy' => 'Most major insurance accepted - in-network and private pay options available.',
        'hero_ethos_quote' => 'You are not a problem to be fixed. You are a person to be understood.',
        'hero_ethos_label' => 'Our practice ethos',
        'hero_badge' => 'Whole-self care, since day one',
        'hero_image' => 'images/hero-family-prismpath-health.png',
        'trust_label' => 'Care that affirms',
        'trust_tags' => "Neuroaffirming\nTrauma-informed\nLGBTQ+ affirming\nFamily-centered\nEvidence-informed",
        'services_eyebrow' => 'What we offer',
        'services_title' => 'Comprehensive care, built around you.',
        'services_intro' => 'Each service is built around collaboration, nervous-system-aware support, and practical next steps.',
        'approach_eyebrow' => 'Our approach',
        'approach_title' => 'More than a diagnosis. A whole person.',
        'approach_intro' => 'We see strengths first. We move at the pace of trust. And we build care around the nervous system you actually have - not the one a textbook describes.',
        'approach_quote' => 'Insight matters. So do the systems, environments, and supports that help your day actually work.',
        'approach_values' => "Strengths-based | We start with what is working - and build from there.\nNervous-system aware | Care that respects regulation, rest, and real capacity.\nCollaborative | You stay the expert on your life. We bring tools and partnership.\nPractical | Insight is paired with strategies that fit your everyday.",
        'whole_family_eyebrow' => 'Whole-family support',
        'whole_family_title' => 'Care that holds the whole system.',
        'whole_family_intro' => 'Prismpath supports the family system through parent and caregiver guidance, communication support, and coordinated mental health planning.',
        'whole_family_points' => "Parent guidance and support\nPediatric therapy pathways through Chroma Early Start\nFamily communication\nCo-regulation skills\nEveryday strategies that fit real life\nCoordinated, collaborative care",
        'whole_family_primary_label' => 'Explore Whole Family Support',
        'whole_family_secondary_label' => 'View pediatric therapy at Chroma Early Start',
        'whole_family_image' => 'images/whole-family-path.png',
        'ot_title' => 'Occupational Therapy in Daily Life',
        'ot_intro' => 'We help you build skills and supports that make daily life feel more workable, confident, and sustainable.',
        'ot_points' => "Sensory regulation\nExecutive function\nRoutines and habits\nWork/life skills\nConfidence in daily life",
        'ot_quote' => 'Insight matters. So do the systems, environments, and supports that help your day actually work.',
        'process_eyebrow' => 'How it works',
        'process_title' => 'Getting started is simple.',
        'process_steps' => "Reach out | Share what you are looking for and how we can help.\nWe connect | We match you with the right provider or team path.\nYour first visit | A collaborative session to understand your goals and next steps.\nOngoing support | Care that grows with you and adapts to real life.",
        'consult_title' => "Tell us what kind of support you're looking for.",
        'consult_intro' => 'We can verify your insurance, answer questions, or help you book the right next step. No pressure, no rush - just a thoughtful first conversation.',
        'consult_trust' => "Neuroaffirming\nTrauma-informed\nLGBTQ+ affirming\nVirtual care where licensure allows",
    );
}

function prismpath_home_meta_key(string $key): string
{
    return '_prismpath_home_' . $key;
}

function prismpath_home_field(string $key, string $fallback = ''): string
{
    $defaults = prismpath_home_defaults();
    $post_id = prismpath_home_post_id();
    $value = $post_id > 0 ? trim((string) get_post_meta($post_id, prismpath_home_meta_key($key), true)) : '';

    if ('' !== $value) {
        return $value;
    }

    return (string) ($defaults[$key] ?? $fallback);
}

function prismpath_home_lines(string $key): array
{
    return prismpath_parse_lines_meta(prismpath_home_field($key));
}

function prismpath_home_pipe_rows(string $key, array $keys): array
{
    $rows = array();
    foreach (prismpath_home_lines($key) as $line) {
        $parts = array_map('trim', explode('|', $line));
        if (count($parts) < count($keys)) {
            continue;
        }
        $row = array();
        foreach ($keys as $index => $row_key) {
            $row[$row_key] = $parts[$index] ?? '';
        }
        $rows[] = $row;
    }

    return $rows;
}

function prismpath_home_section_enabled(string $section): bool
{
    $defaults = prismpath_home_defaults();
    $post_id = prismpath_home_post_id();
    $key = 'show_' . $section;
    $meta = $post_id > 0 ? get_post_meta($post_id, prismpath_home_meta_key($key), true) : '';
    if ('' !== $meta) {
        return '1' === (string) $meta;
    }

    return '1' === (string) ($defaults[$key] ?? '1');
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

    add_meta_box(
        'prismpath-home-content',
        __('Prismpath Homepage Sections', 'prismpath-health'),
        'prismpath_editor_render_home_metabox',
        'page',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'prismpath_editor_add_page_metaboxes');

function prismpath_editor_render_text_field(int $post_id, string $input, string $label, string $placeholder = ''): void
{
    ?>
    <tr>
        <th scope="row"><label for="<?php echo esc_attr($input); ?>"><?php echo esc_html($label); ?></label></th>
        <td><input class="large-text" id="<?php echo esc_attr($input); ?>" name="<?php echo esc_attr($input); ?>" type="text" value="<?php echo esc_attr(prismpath_editor_field_value($post_id, prismpath_home_meta_key(str_replace('prismpath_home_', '', $input)))); ?>" placeholder="<?php echo esc_attr($placeholder); ?>"></td>
    </tr>
    <?php
}

function prismpath_editor_render_textarea_field(int $post_id, string $input, string $label, string $placeholder = '', int $rows = 3, string $description = ''): void
{
    ?>
    <tr>
        <th scope="row"><label for="<?php echo esc_attr($input); ?>"><?php echo esc_html($label); ?></label></th>
        <td>
            <textarea class="large-text<?php echo $rows > 3 ? ' code' : ''; ?>" id="<?php echo esc_attr($input); ?>" name="<?php echo esc_attr($input); ?>" rows="<?php echo esc_attr((string) $rows); ?>" placeholder="<?php echo esc_attr($placeholder); ?>"><?php echo esc_textarea(prismpath_editor_field_value($post_id, prismpath_home_meta_key(str_replace('prismpath_home_', '', $input)))); ?></textarea>
            <?php if ($description) : ?><p class="description"><?php echo esc_html($description); ?></p><?php endif; ?>
        </td>
    </tr>
    <?php
}

function prismpath_editor_render_home_metabox(WP_Post $post): void
{
    $front_id = (int) get_option('page_on_front');
    if ($front_id > 0 && $front_id !== (int) $post->ID) {
        echo '<p class="description">' . esc_html__('These fields appear on the page assigned as the site homepage.', 'prismpath-health') . '</p>';
        return;
    }

    $defaults = prismpath_home_defaults();
    $sections = array(
        'trust' => __('Trust strip', 'prismpath-health'),
        'services' => __('Services', 'prismpath-health'),
        'whole_family' => __('Whole family', 'prismpath-health'),
        'approach' => __('Approach', 'prismpath-health'),
        'ot' => __('Occupational therapy', 'prismpath-health'),
        'process' => __('Process', 'prismpath-health'),
        'consult' => __('Consult form', 'prismpath-health'),
    );
    ?>
    <p class="description"><?php esc_html_e('Homepage content overrides. Blank fields use the theme defaults.', 'prismpath-health'); ?></p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row"><?php esc_html_e('Visible sections', 'prismpath-health'); ?></th>
                <td>
                    <?php foreach ($sections as $section => $label) : ?>
                        <?php
                        $meta_key = prismpath_home_meta_key('show_' . $section);
                        $stored = get_post_meta($post->ID, $meta_key, true);
                        $checked = '' === $stored ? '1' === (string) ($defaults['show_' . $section] ?? '1') : '1' === (string) $stored;
                        ?>
                        <label style="display:block;margin:.25rem 0;">
                            <input type="checkbox" name="prismpath_home_show_<?php echo esc_attr($section); ?>" value="1" <?php checked($checked); ?>>
                            <?php echo esc_html($label); ?>
                        </label>
                    <?php endforeach; ?>
                </td>
            </tr>
            <?php
            prismpath_editor_render_text_field($post->ID, 'prismpath_home_hero_eyebrow', __('Hero eyebrow', 'prismpath-health'), $defaults['hero_eyebrow']);
            prismpath_editor_render_text_field($post->ID, 'prismpath_home_hero_title', __('Hero title', 'prismpath-health'), $defaults['hero_title']);
            prismpath_editor_render_textarea_field($post->ID, 'prismpath_home_hero_intro', __('Hero intro', 'prismpath-health'), $defaults['hero_intro']);
            prismpath_editor_render_textarea_field($post->ID, 'prismpath_home_hero_partner_note', __('Hero partner note', 'prismpath-health'), $defaults['hero_partner_note']);
            prismpath_editor_render_text_field($post->ID, 'prismpath_home_hero_primary_label', __('Hero primary CTA label', 'prismpath-health'), $defaults['hero_primary_label']);
            prismpath_editor_render_text_field($post->ID, 'prismpath_home_hero_primary_url', __('Hero primary CTA URL', 'prismpath-health'), $defaults['hero_primary_url']);
            prismpath_editor_render_text_field($post->ID, 'prismpath_home_hero_secondary_label', __('Hero secondary CTA label', 'prismpath-health'), $defaults['hero_secondary_label']);
            prismpath_editor_render_text_field($post->ID, 'prismpath_home_hero_secondary_url', __('Hero secondary CTA URL', 'prismpath-health'), $defaults['hero_secondary_url']);
            prismpath_editor_render_text_field($post->ID, 'prismpath_home_hero_microcopy', __('Hero microcopy', 'prismpath-health'), $defaults['hero_microcopy']);
            prismpath_editor_render_textarea_field($post->ID, 'prismpath_home_hero_ethos_quote', __('Hero practice ethos quote', 'prismpath-health'), $defaults['hero_ethos_quote']);
            prismpath_editor_render_text_field($post->ID, 'prismpath_home_hero_ethos_label', __('Hero ethos label', 'prismpath-health'), $defaults['hero_ethos_label']);
            prismpath_editor_render_text_field($post->ID, 'prismpath_home_hero_badge', __('Hero badge', 'prismpath-health'), $defaults['hero_badge']);
            prismpath_editor_render_text_field($post->ID, 'prismpath_home_hero_image', __('Hero image asset path', 'prismpath-health'), $defaults['hero_image']);
            prismpath_editor_render_text_field($post->ID, 'prismpath_home_trust_label', __('Trust strip label', 'prismpath-health'), $defaults['trust_label']);
            prismpath_editor_render_textarea_field($post->ID, 'prismpath_home_trust_tags', __('Trust tags', 'prismpath-health'), $defaults['trust_tags'], 5, __('One tag per line.', 'prismpath-health'));
            prismpath_editor_render_text_field($post->ID, 'prismpath_home_services_eyebrow', __('Services eyebrow', 'prismpath-health'), $defaults['services_eyebrow']);
            prismpath_editor_render_text_field($post->ID, 'prismpath_home_services_title', __('Services title', 'prismpath-health'), $defaults['services_title']);
            prismpath_editor_render_textarea_field($post->ID, 'prismpath_home_services_intro', __('Services intro', 'prismpath-health'), $defaults['services_intro']);
            prismpath_editor_render_text_field($post->ID, 'prismpath_home_approach_eyebrow', __('Approach eyebrow', 'prismpath-health'), $defaults['approach_eyebrow']);
            prismpath_editor_render_text_field($post->ID, 'prismpath_home_approach_title', __('Approach title', 'prismpath-health'), $defaults['approach_title']);
            prismpath_editor_render_textarea_field($post->ID, 'prismpath_home_approach_intro', __('Approach intro', 'prismpath-health'), $defaults['approach_intro']);
            prismpath_editor_render_textarea_field($post->ID, 'prismpath_home_approach_quote', __('Approach practice ethos quote', 'prismpath-health'), $defaults['approach_quote']);
            prismpath_editor_render_textarea_field($post->ID, 'prismpath_home_approach_values', __('Approach value cards', 'prismpath-health'), $defaults['approach_values'], 6, __('One per line as Title | Description.', 'prismpath-health'));
            prismpath_editor_render_text_field($post->ID, 'prismpath_home_whole_family_eyebrow', __('Whole family eyebrow', 'prismpath-health'), $defaults['whole_family_eyebrow']);
            prismpath_editor_render_text_field($post->ID, 'prismpath_home_whole_family_title', __('Whole family title', 'prismpath-health'), $defaults['whole_family_title']);
            prismpath_editor_render_textarea_field($post->ID, 'prismpath_home_whole_family_intro', __('Whole family intro', 'prismpath-health'), $defaults['whole_family_intro']);
            prismpath_editor_render_textarea_field($post->ID, 'prismpath_home_whole_family_points', __('Whole family points', 'prismpath-health'), $defaults['whole_family_points'], 6, __('One point per line.', 'prismpath-health'));
            prismpath_editor_render_text_field($post->ID, 'prismpath_home_whole_family_primary_label', __('Whole family CTA label', 'prismpath-health'), $defaults['whole_family_primary_label']);
            prismpath_editor_render_text_field($post->ID, 'prismpath_home_whole_family_secondary_label', __('Chroma CTA label', 'prismpath-health'), $defaults['whole_family_secondary_label']);
            prismpath_editor_render_text_field($post->ID, 'prismpath_home_whole_family_image', __('Whole family image asset path', 'prismpath-health'), $defaults['whole_family_image']);
            prismpath_editor_render_text_field($post->ID, 'prismpath_home_ot_title', __('OT title', 'prismpath-health'), $defaults['ot_title']);
            prismpath_editor_render_textarea_field($post->ID, 'prismpath_home_ot_intro', __('OT intro', 'prismpath-health'), $defaults['ot_intro']);
            prismpath_editor_render_textarea_field($post->ID, 'prismpath_home_ot_points', __('OT points', 'prismpath-health'), $defaults['ot_points'], 5, __('One point per line.', 'prismpath-health'));
            prismpath_editor_render_textarea_field($post->ID, 'prismpath_home_ot_quote', __('OT practice ethos quote', 'prismpath-health'), $defaults['ot_quote']);
            prismpath_editor_render_text_field($post->ID, 'prismpath_home_process_eyebrow', __('Process eyebrow', 'prismpath-health'), $defaults['process_eyebrow']);
            prismpath_editor_render_text_field($post->ID, 'prismpath_home_process_title', __('Process title', 'prismpath-health'), $defaults['process_title']);
            prismpath_editor_render_textarea_field($post->ID, 'prismpath_home_process_steps', __('Process steps', 'prismpath-health'), $defaults['process_steps'], 5, __('One per line as Title | Description.', 'prismpath-health'));
            prismpath_editor_render_text_field($post->ID, 'prismpath_home_consult_title', __('Consult title', 'prismpath-health'), $defaults['consult_title']);
            prismpath_editor_render_textarea_field($post->ID, 'prismpath_home_consult_intro', __('Consult intro', 'prismpath-health'), $defaults['consult_intro']);
            prismpath_editor_render_textarea_field($post->ID, 'prismpath_home_consult_trust', __('Consult trust points', 'prismpath-health'), $defaults['consult_trust'], 5, __('One point per line.', 'prismpath-health'));
            ?>
        </tbody>
    </table>
    <?php
}

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
                    <th scope="row"><label for="prismpath_page_eyebrow"><?php esc_html_e('Hero eyebrow', 'prismpath-health'); ?></label></th>
                    <td>
                        <input class="large-text" id="prismpath_page_eyebrow" name="prismpath_page_eyebrow" type="text" value="<?php echo esc_attr(prismpath_editor_field_value($post->ID, '_prismpath_page_eyebrow')); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="prismpath_page_primary_cta_label"><?php esc_html_e('Primary CTA label', 'prismpath-health'); ?></label></th>
                    <td>
                        <input class="large-text" id="prismpath_page_primary_cta_label" name="prismpath_page_primary_cta_label" type="text" value="<?php echo esc_attr(prismpath_editor_field_value($post->ID, '_prismpath_page_primary_cta_label')); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="prismpath_page_primary_cta_url"><?php esc_html_e('Primary CTA URL', 'prismpath-health'); ?></label></th>
                    <td>
                        <input class="large-text" id="prismpath_page_primary_cta_url" name="prismpath_page_primary_cta_url" type="text" value="<?php echo esc_attr(prismpath_editor_field_value($post->ID, '_prismpath_page_primary_cta_url')); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="prismpath_page_secondary_cta_label"><?php esc_html_e('Secondary CTA label', 'prismpath-health'); ?></label></th>
                    <td>
                        <input class="large-text" id="prismpath_page_secondary_cta_label" name="prismpath_page_secondary_cta_label" type="text" value="<?php echo esc_attr(prismpath_editor_field_value($post->ID, '_prismpath_page_secondary_cta_label')); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="prismpath_page_secondary_cta_url"><?php esc_html_e('Secondary CTA URL', 'prismpath-health'); ?></label></th>
                    <td>
                        <input class="large-text" id="prismpath_page_secondary_cta_url" name="prismpath_page_secondary_cta_url" type="text" value="<?php echo esc_attr(prismpath_editor_field_value($post->ID, '_prismpath_page_secondary_cta_url')); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="prismpath_page_image"><?php esc_html_e('Hero/support image asset path', 'prismpath-health'); ?></label></th>
                    <td>
                        <input class="large-text" id="prismpath_page_image" name="prismpath_page_image" type="text" value="<?php echo esc_attr(prismpath_editor_field_value($post->ID, '_prismpath_page_image')); ?>" placeholder="<?php esc_attr_e('images/example.png', 'prismpath-health'); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="prismpath_page_ethos_quote"><?php esc_html_e('Practice ethos quote', 'prismpath-health'); ?></label></th>
                    <td>
                        <textarea class="large-text" id="prismpath_page_ethos_quote" name="prismpath_page_ethos_quote" rows="3"><?php echo esc_textarea(prismpath_editor_field_value($post->ID, '_prismpath_page_ethos_quote')); ?></textarea>
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
        'prismpath_page_eyebrow' => '_prismpath_page_eyebrow',
        'prismpath_page_primary_cta_label' => '_prismpath_page_primary_cta_label',
        'prismpath_page_secondary_cta_label' => '_prismpath_page_secondary_cta_label',
        'prismpath_page_image' => '_prismpath_page_image',
        'prismpath_page_panel_heading' => '_prismpath_page_panel_heading',
    );
    foreach ($text_fields as $input => $meta_key) {
        $value = isset($_POST[$input]) ? sanitize_text_field(wp_unslash($_POST[$input])) : '';
        prismpath_editor_update_or_delete_meta($post_id, $meta_key, $value);
    }

    $textarea_fields = array(
        'prismpath_meta_description' => 'meta_description',
        'prismpath_page_intro' => '_prismpath_page_intro',
        'prismpath_page_primary_cta_url' => '_prismpath_page_primary_cta_url',
        'prismpath_page_secondary_cta_url' => '_prismpath_page_secondary_cta_url',
        'prismpath_page_ethos_quote' => '_prismpath_page_ethos_quote',
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

    $front_id = (int) get_option('page_on_front');
    if ($front_id > 0 && $front_id !== $post_id) {
        return;
    }

    $home_text_fields = array(
        'hero_eyebrow',
        'hero_title',
        'hero_primary_label',
        'hero_primary_url',
        'hero_secondary_label',
        'hero_secondary_url',
        'hero_microcopy',
        'hero_ethos_label',
        'hero_badge',
        'hero_image',
        'trust_label',
        'services_eyebrow',
        'services_title',
        'approach_eyebrow',
        'approach_title',
        'whole_family_eyebrow',
        'whole_family_title',
        'whole_family_primary_label',
        'whole_family_secondary_label',
        'whole_family_image',
        'ot_title',
        'process_eyebrow',
        'process_title',
        'consult_title',
    );
    foreach ($home_text_fields as $field) {
        $input = 'prismpath_home_' . $field;
        $value = isset($_POST[$input]) ? sanitize_text_field(wp_unslash($_POST[$input])) : '';
        prismpath_editor_update_or_delete_meta($post_id, prismpath_home_meta_key($field), $value);
    }

    $home_url_fields = array(
        'hero_primary_url',
        'hero_secondary_url',
    );
    foreach ($home_url_fields as $field) {
        $input = 'prismpath_home_' . $field;
        $value = isset($_POST[$input]) ? esc_url_raw(wp_unslash($_POST[$input])) : '';
        prismpath_editor_update_or_delete_meta($post_id, prismpath_home_meta_key($field), $value);
    }

    $home_textarea_fields = array(
        'hero_intro',
        'hero_partner_note',
        'hero_ethos_quote',
        'trust_tags',
        'services_intro',
        'approach_intro',
        'approach_quote',
        'approach_values',
        'whole_family_intro',
        'whole_family_points',
        'ot_intro',
        'ot_points',
        'ot_quote',
        'process_steps',
        'consult_intro',
        'consult_trust',
    );
    foreach ($home_textarea_fields as $field) {
        $input = 'prismpath_home_' . $field;
        $value = isset($_POST[$input]) ? sanitize_textarea_field(wp_unslash($_POST[$input])) : '';
        prismpath_editor_update_or_delete_meta($post_id, prismpath_home_meta_key($field), $value);
    }

    foreach (array('trust', 'services', 'whole_family', 'approach', 'ot', 'process', 'consult') as $section) {
        $input = 'prismpath_home_show_' . $section;
        update_post_meta($post_id, prismpath_home_meta_key('show_' . $section), isset($_POST[$input]) ? '1' : '0');
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
        '_prismpath_page_eyebrow' => 'eyebrow',
        '_prismpath_page_primary_cta_label' => 'primary_cta_label',
        '_prismpath_page_primary_cta_url' => 'primary_cta_url',
        '_prismpath_page_secondary_cta_label' => 'secondary_cta_label',
        '_prismpath_page_secondary_cta_url' => 'secondary_cta_url',
        '_prismpath_page_image' => 'image',
        '_prismpath_page_ethos_quote' => 'ethos_quote',
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
