<?php
/**
 * Services overview.
 *
 * @package Prismpath_Health
 */
?>
<section id="services" class="section services-section">
    <div class="container">
        <div class="section-heading align-left reveal">
            <p class="eyebrow"><?php echo esc_html(prismpath_home_field('services_eyebrow')); ?></p>
            <h2><?php echo esc_html(prismpath_home_field('services_title')); ?></h2>
            <p><?php echo esc_html(prismpath_home_field('services_intro')); ?></p>
        </div>
        <div class="service-grid">
            <?php foreach (prismpath_services() as $index => $service) : ?>
                <article class="service-card reveal service-card-<?php echo esc_attr((string) ($index + 1)); ?>">
                    <span class="icon-circle"><?php echo prismpath_icon($service['icon']); ?></span>
                    <h3><?php echo esc_html($service['title']); ?></h3>
                    <p><?php echo esc_html($service['summary']); ?></p>
                    <a href="<?php echo esc_url($service['url']); ?>">Learn more about <?php echo esc_html($service['title']); ?></a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
