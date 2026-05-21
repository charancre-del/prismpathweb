<?php
/**
 * Consult CTA and form.
 *
 * @package Prismpath_Health
 */
?>
<section id="consult" class="section consult-section">
    <div class="container consult-layout">
        <div class="reveal">
            <h2><?php echo esc_html(prismpath_home_field('consult_title')); ?></h2>
            <p><?php echo esc_html(prismpath_home_field('consult_intro')); ?></p>
            <ul class="trust-list">
                <?php foreach (prismpath_home_lines('consult_trust') as $point) : ?>
                    <li><?php echo prismpath_icon('check'); ?><?php echo esc_html($point); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="consult-form-wrap reveal">
            <?php
            if (shortcode_exists('prismpath_consult_form')) {
                echo do_shortcode('[prismpath_consult_form]');
            } else {
                echo '<p class="notice">Activate the Prismpath Consult Form plugin to display the consultation form.</p>';
                echo '<a class="button button-primary" href="' . esc_url(prismpath_booking_url()) . '">Book a Consultation</a>';
            }
            ?>
        </div>
    </div>
</section>
