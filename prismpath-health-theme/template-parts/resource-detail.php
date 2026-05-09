<?php
/**
 * Shared resource article template.
 *
 * @package Prismpath_Health
 */

$slug = $args['slug'] ?? get_post_field('post_name', get_the_ID());
$resource = prismpath_resource_by_slug($slug);

if (!$resource) {
    return;
}
?>
<article>
    <section class="page-hero resource-hero">
        <div class="container narrow">
            <h1><?php echo esc_html($resource['title']); ?></h1>
            <p><?php echo esc_html($resource['excerpt']); ?></p>
            <div class="hero-actions">
                <a class="button button-primary" href="<?php echo esc_url(prismpath_booking_url()); ?>">Book a Consultation</a>
                <a class="button button-outline" href="<?php echo esc_url(home_url('/resources/')); ?>">Explore Resources</a>
            </div>
        </div>
    </section>

    <section class="page-content resource-article">
        <div class="container resource-layout">
            <div class="prose">
                <?php foreach ($resource['sections'] as $section) : ?>
                    <section class="content-section">
                        <h2><?php echo esc_html($section['heading']); ?></h2>
                        <p><?php echo esc_html($section['body']); ?></p>
                    </section>
                <?php endforeach; ?>
            </div>

            <aside class="detail-list">
                <h2>Related care paths</h2>
                <ul>
                    <?php foreach ($resource['related_services'] as $service_slug) :
                        $service = prismpath_page_content($service_slug);
                        ?>
                        <li>
                            <?php echo prismpath_icon('check'); ?>
                            <span><a href="<?php echo esc_url(home_url('/' . sanitize_title($service_slug) . '/')); ?>"><?php echo esc_html($service['title']); ?></a></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </aside>
        </div>
    </section>

    <section class="resource-support-section">
        <div class="container resource-support-grid single-column">
            <div class="support-panel faq-panel">
                <h2>Common questions</h2>
                <div class="faq-list">
                    <?php foreach ($resource['faqs'] as $faq) : ?>
                        <details>
                            <summary><?php echo esc_html($faq['question']); ?></summary>
                            <p><?php echo esc_html($faq['answer']); ?></p>
                        </details>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
</article>
<?php get_template_part('template-parts/sections/consult'); ?>
