<?php
/**
 * Homepage hero.
 *
 * @package Prismpath_Health
 */
?>
<?php $hero_image = prismpath_home_field('hero_image'); ?>
<section class="home-hero section-reveal">
    <div class="container hero-grid">
        <div class="hero-copy reveal">
            <p class="hero-eyebrow"><span class="pulse-dot" aria-hidden="true"></span><?php echo esc_html(prismpath_home_field('hero_eyebrow')); ?></p>
            <h1><?php echo esc_html(prismpath_home_field('hero_title')); ?></h1>
            <p class="hero-lede"><?php echo esc_html(prismpath_home_field('hero_intro')); ?></p>
            <p class="hero-aside"><?php echo esc_html(prismpath_home_field('hero_partner_note')); ?></p>
            <div class="hero-actions">
                <a class="button button-primary" href="<?php echo esc_url(prismpath_home_field('hero_primary_url', prismpath_booking_url())); ?>"><?php echo esc_html(prismpath_home_field('hero_primary_label')); ?></a>
                <a class="button button-outline" href="<?php echo esc_url(prismpath_home_field('hero_secondary_url', home_url('/services/'))); ?>"><?php echo esc_html(prismpath_home_field('hero_secondary_label')); ?></a>
            </div>
            <p class="hero-meta"><span aria-hidden="true"></span><?php echo esc_html(prismpath_home_field('hero_microcopy')); ?></p>
        </div>
        <div class="hero-visual reveal">
            <div class="hero-card-main">
                <?php if ($hero_image) : ?>
                    <img src="<?php echo esc_url(prismpath_optimized_asset($hero_image)); ?>" alt=""<?php echo prismpath_image_size_attrs($hero_image); ?><?php echo prismpath_responsive_image_attrs($hero_image, '(max-width: 760px) 92vw, 42vw'); ?> loading="eager" fetchpriority="high" decoding="async">
                <?php endif; ?>
                <div class="hero-card-overlay">
                    <p class="hero-quote"><?php echo esc_html(prismpath_home_field('hero_ethos_quote')); ?></p>
                    <p class="hero-quote-attr"><?php echo esc_html(prismpath_home_field('hero_ethos_label')); ?></p>
                </div>
            </div>
            <div class="hero-mini-card">
                <span class="icon-circle"><?php echo prismpath_icon('brain'); ?></span>
                <span><strong><?php esc_html_e('Neuroaffirming', 'prismpath-health'); ?></strong><small><?php esc_html_e('Strengths-based care', 'prismpath-health'); ?></small></span>
            </div>
            <div class="hero-badge"><?php echo esc_html(prismpath_home_field('hero_badge')); ?></div>
        </div>
    </div>
</section>
