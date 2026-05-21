<?php
/**
 * Resource hub template.
 *
 * @package Prismpath_Health
 */

get_header();
$resources = prismpath_resource_pages();
$content = array(
    'title' => 'Guides for clearer next steps.',
    'intro' => 'Conservative, people-first resources for adult neuroaffirming care, assessment, caregiver support, accommodations, and payment planning.',
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
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="service-grid">
            <?php foreach ($resources as $slug => $resource) : ?>
                <article class="service-card">
                    <span class="icon-circle"><?php echo prismpath_icon('check'); ?></span>
                    <h2><?php echo esc_html($resource['title']); ?></h2>
                    <p><?php echo esc_html($resource['intro']); ?></p>
                    <a href="<?php echo esc_url(prismpath_resource_url((string) $slug)); ?>"><?php esc_html_e('Read guide', 'prismpath-health'); ?></a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php get_template_part('template-parts/sections/consult'); ?>
<?php get_footer(); ?>
