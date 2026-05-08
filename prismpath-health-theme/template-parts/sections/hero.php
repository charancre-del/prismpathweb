<?php
/**
 * Homepage hero.
 *
 * @package Prismpath_Health
 */
?>
<section class="home-hero">
    <div class="hero-media" aria-hidden="true">
        <img src="<?php echo esc_url(prismpath_asset('images/hero-family-prismpath-health.png')); ?>" alt="" loading="eager">
    </div>
    <div class="container hero-content">
        <div class="hero-copy">
            <h1>A clearer path to mental health care for every brain and every family.</h1>
            <p>Neuroaffirming therapy, psychiatric care, occupational therapy, and ADHD & Autism assessments for adults, caregivers, and families.</p>
            <div class="hero-actions">
                <a class="button button-primary" href="<?php echo esc_url(prismpath_booking_url()); ?>">Start Your Journey</a>
                <a class="button button-outline" href="<?php echo esc_url(home_url('/services/')); ?>">Explore Services</a>
            </div>
            <p class="micro-proof">Virtual care nationwide where provider licensure allows.</p>
        </div>
    </div>
</section>
