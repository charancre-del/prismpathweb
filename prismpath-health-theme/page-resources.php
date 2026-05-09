<?php
/**
 * Resource hub template.
 *
 * @package Prismpath_Health
 */

get_header();
?>
<section class="page-hero">
    <div class="container narrow">
        <h1>Resources for neuroaffirming care decisions.</h1>
        <p>Plain-language guides for adults, caregivers, and referral partners exploring therapy, psychiatry, assessments, occupational therapy, whole-family support, and accommodations.</p>
    </div>
</section>

<section class="section resource-index-section">
    <div class="container">
        <div class="resource-card-grid">
            <?php foreach (prismpath_resource_pages() as $slug => $resource) : ?>
                <article class="resource-card">
                    <h2><?php echo esc_html($resource['title']); ?></h2>
                    <p><?php echo esc_html($resource['excerpt']); ?></p>
                    <a href="<?php echo esc_url(prismpath_resource_url($slug)); ?>">Read the guide</a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
get_template_part('template-parts/sections/consult');
get_footer();
