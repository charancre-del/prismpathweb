<?php
/**
 * Consult CTA and form.
 *
 * @package Prismpath_Health
 */
?>
<section id="consult" class="section consult-section">
    <div class="container consult-layout">
        <div>
            <h2>We're here to help.</h2>
            <p>Tell us what kind of support you are looking for. We can verify your insurance, answer questions, or help you book the right next step.</p>
            <ul class="trust-list">
                <li><?php echo prismpath_icon('check'); ?>Neuroaffirming</li>
                <li><?php echo prismpath_icon('check'); ?>Trauma-informed</li>
                <li><?php echo prismpath_icon('check'); ?>LGBTQ+ affirming</li>
                <li><?php echo prismpath_icon('check'); ?>Virtual care where licensure allows</li>
            </ul>
        </div>
        <div class="consult-form-wrap">
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
