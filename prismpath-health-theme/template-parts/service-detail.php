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
?>
<section class="page-hero service-page-hero">
    <div class="container split">
        <div>
            <h1><?php echo esc_html($content['title']); ?></h1>
            <p><?php echo esc_html($content['intro']); ?></p>
            <div class="hero-actions">
                <a class="button button-primary" href="<?php echo esc_url($cta_url); ?>">Book a Consultation</a>
                <a class="button button-outline" href="<?php echo esc_url(home_url('/services/')); ?>">Explore Services</a>
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
<?php get_template_part('template-parts/sections/process'); ?>
<?php get_template_part('template-parts/sections/consult'); ?>
