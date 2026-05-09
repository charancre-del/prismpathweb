<?php
/**
 * Shared service-style page content.
 *
 * @package Prismpath_Health
 */

$slug = $args['slug'] ?? '';
$content = prismpath_page_content($slug);
$service = prismpath_service_by_slug($slug);
$cta_url = 'whole-family-mental-health' === $slug ? prismpath_whole_family_booking_url() : prismpath_booking_url();
$secondary_url = home_url('/services/');
$secondary_label = 'Explore Services';
$secondary_external = false;
if ('whole-family-mental-health' === $slug) {
    $secondary_url = prismpath_chroma_early_start_url();
    $secondary_label = 'Pediatric Therapy at Chroma Early Start';
    $secondary_external = true;
}
?>
<section class="page-hero service-page-hero">
    <div class="container split">
        <div>
            <h1><?php echo esc_html($content['title']); ?></h1>
            <p><?php echo esc_html($content['intro']); ?></p>
            <div class="hero-actions">
                <a class="button button-primary" href="<?php echo esc_url($cta_url); ?>">Book a Consultation</a>
                <a class="button button-outline" href="<?php echo esc_url($secondary_url); ?>"<?php echo $secondary_external ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo esc_html($secondary_label); ?></a>
            </div>
        </div>
        <div class="service-panel">
            <span class="icon-circle"><?php echo $service ? prismpath_icon($service['icon']) : prismpath_icon('check'); ?></span>
            <h2>Care shaped around real life.</h2>
            <p>We combine clinical insight, practical strategy, and respectful collaboration so care feels usable outside the session too.</p>
        </div>
    </div>
</section>
<section class="page-content service-detail">
    <div class="container split reverse">
        <div class="prose">
            <?php
            while (have_posts()) :
                the_post();
                if (trim(get_the_content())) {
                    the_content();
                } else {
                    echo '<p>' . esc_html($content['intro']) . '</p>';
                }
            endwhile;
            if (!empty($content['sections']) && is_array($content['sections'])) :
                foreach ($content['sections'] as $section) :
                    ?>
                    <section class="content-section">
                        <h2><?php echo esc_html($section['heading']); ?></h2>
                        <p><?php echo esc_html($section['body']); ?></p>
                    </section>
                    <?php
                endforeach;
            endif;
            ?>
        </div>
        <aside class="detail-list">
            <h2>What this can include</h2>
            <ul>
                <?php foreach ($content['points'] as $point) : ?>
                    <li><?php echo prismpath_icon('check'); ?><span><?php echo esc_html($point); ?></span></li>
                <?php endforeach; ?>
            </ul>
        </aside>
    </div>
</section>
<?php if (!empty($content['related_links']) || !empty($content['faqs'])) : ?>
<section class="resource-support-section">
    <div class="container resource-support-grid">
        <?php if (!empty($content['related_links']) && is_array($content['related_links'])) : ?>
            <div class="support-panel">
                <h2>Helpful next reads</h2>
                <div class="related-list">
                    <?php foreach ($content['related_links'] as $resource_slug) :
                        $resource = prismpath_resource_by_slug($resource_slug);
                        if (!$resource) {
                            continue;
                        }
                        ?>
                        <a href="<?php echo esc_url(prismpath_resource_url($resource_slug)); ?>">
                            <span><?php echo esc_html($resource['title']); ?></span>
                            <small><?php echo esc_html($resource['excerpt']); ?></small>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if (!empty($content['faqs']) && is_array($content['faqs'])) : ?>
            <div class="support-panel faq-panel">
                <h2>Common questions</h2>
                <div class="faq-list">
                    <?php foreach ($content['faqs'] as $faq) : ?>
                        <details>
                            <summary><?php echo esc_html($faq['question']); ?></summary>
                            <p><?php echo esc_html($faq['answer']); ?></p>
                        </details>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>
<?php
if (in_array($slug, array('therapy', 'psychiatry', 'adhd-autism-assessments', 'occupational-therapy', 'accommodations'), true)) {
    get_template_part('template-parts/sections/insurance');
}
?>
<?php get_template_part('template-parts/sections/process'); ?>
<?php get_template_part('template-parts/sections/consult'); ?>
