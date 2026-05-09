<?php
/**
 * Homepage hero.
 *
 * @package Prismpath_Health
 */
?>
<section class="home-hero">
    <div class="hero-media" aria-hidden="true">
        <?php $hero_image = 'images/hero-family-prismpath-health.png'; ?>
        <img src="<?php echo esc_url(prismpath_optimized_asset($hero_image)); ?>" alt=""<?php echo prismpath_image_size_attrs($hero_image); ?><?php echo prismpath_responsive_image_attrs($hero_image, '(max-width: 760px) 100vw, 58vw'); ?> loading="eager" fetchpriority="high" decoding="async">
    </div>
    <div class="container hero-content">
        <div class="hero-copy">
            <h1>A clearer path to mental health care for every brain and every family.</h1>
            <p>Neuroaffirming therapy, psychiatric care, occupational therapy, and ADHD & Autism assessments for adults, caregivers, and families.</p>
            <p class="micro-proof">Pediatric ABA, speech, and occupational therapy services are available through Chroma Early Start.</p>
            <div class="hero-actions">
                <a class="button button-primary" href="<?php echo esc_url(prismpath_booking_url()); ?>">Start Your Journey</a>
                <a class="button button-outline" href="<?php echo esc_url(home_url('/services/')); ?>">Explore Services</a>
            </div>
            <p class="micro-proof">Virtual care nationwide where provider licensure allows.</p>
        </div>
    </div>
</section>
