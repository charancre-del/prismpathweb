<?php
/**
 * Services overview.
 *
 * @package Prismpath_Health
 */
?>
<section id="services" class="section services-section">
    <div class="container">
        <div class="section-heading align-left">
            <h2>Comprehensive, neuroaffirming care that meets you where you are.</h2>
            <p>Each service is built around collaboration, nervous-system-aware support, and practical next steps.</p>
        </div>
        <div class="service-grid">
            <?php foreach (prismpath_services() as $service) : ?>
                <article class="service-card">
                    <span class="icon-circle"><?php echo prismpath_icon($service['icon']); ?></span>
                    <h3><?php echo esc_html($service['title']); ?></h3>
                    <p><?php echo esc_html($service['summary']); ?></p>
                    <a href="<?php echo esc_url($service['url']); ?>">Learn more about <?php echo esc_html($service['title']); ?></a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
