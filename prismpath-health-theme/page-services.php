<?php
/**
 * Services page template.
 *
 * @package Prismpath_Health
 */

get_header();
?>
<section class="page-hero">
    <div class="container narrow">
        <h1>Comprehensive, neuroaffirming care that meets you where you are.</h1>
        <p>Prismpath Health brings adult therapy, psychiatry, occupational therapy, assessments, and caregiver-centered whole-family support into one coordinated care path. Pediatric therapy services are available through Chroma Early Start.</p>
    </div>
</section>
<?php get_template_part('template-parts/sections/services'); ?>
<?php get_template_part('template-parts/sections/insurance'); ?>
<section class="section resource-index-section">
    <div class="container">
        <div class="section-heading">
            <h2>Care guides for the next step.</h2>
            <p>Read practical, conservative guides that explain assessment, therapy, psychiatry, occupational therapy, family support, and accommodations before you reach out.</p>
        </div>
        <div class="resource-card-grid">
            <?php foreach (array_slice(prismpath_resource_pages(), 0, 3, true) as $slug => $resource) : ?>
                <article class="resource-card">
                    <h2><?php echo esc_html($resource['title']); ?></h2>
                    <p><?php echo esc_html($resource['excerpt']); ?></p>
                    <a href="<?php echo esc_url(prismpath_resource_url($slug)); ?>">Read the guide</a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php get_template_part('template-parts/sections/consult'); ?>
<?php get_footer(); ?>
