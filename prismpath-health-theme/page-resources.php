<?php
/**
 * Resource hub template.
 *
 * @package Prismpath_Health
 */

get_header();
$resources = prismpath_resource_pages();
?>
<section class="page-hero">
    <div class="container narrow">
        <h1><?php esc_html_e('Guides for clearer next steps.', 'prismpath-health'); ?></h1>
        <p><?php esc_html_e('Conservative, people-first resources for adult neuroaffirming care, assessment, caregiver support, accommodations, and payment planning.', 'prismpath-health'); ?></p>
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
