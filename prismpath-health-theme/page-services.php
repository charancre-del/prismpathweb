<?php
/**
 * Services page template.
 *
 * @package Prismpath_Health
 */

get_header();
$content = array(
    'title' => 'Comprehensive, neuroaffirming care that meets you where you are.',
    'intro' => 'Prismpath Health brings adult therapy, psychiatry, occupational therapy, assessments, and caregiver-centered whole-family support into one coordinated care path. Pediatric therapy services are available through Chroma Early Start.',
);
if (function_exists('prismpath_page_content_overrides')) {
    $content = array_merge($content, prismpath_page_content_overrides((int) get_queried_object_id()));
}
?>
<section class="page-hero">
    <div class="container narrow">
        <?php if (!empty($content['eyebrow'])) : ?><p class="eyebrow"><?php echo esc_html($content['eyebrow']); ?></p><?php endif; ?>
        <h1><?php echo esc_html($content['title']); ?></h1>
        <p><?php echo esc_html($content['intro']); ?></p>
        <?php if (!empty($content['primary_cta_label']) && !empty($content['primary_cta_url'])) : ?>
            <div class="hero-actions">
                <a class="button button-primary" href="<?php echo esc_url($content['primary_cta_url']); ?>"><?php echo esc_html($content['primary_cta_label']); ?></a>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php get_template_part('template-parts/sections/services'); ?>
<?php get_template_part('template-parts/sections/insurance'); ?>
<?php get_template_part('template-parts/sections/consult'); ?>
<?php get_footer(); ?>
