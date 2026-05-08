<?php
/**
 * Whole family section.
 *
 * @package Prismpath_Health
 */
?>
<section id="whole-family" class="section whole-family-section">
    <div class="split-full">
        <div class="whole-copy">
            <h2>Whole Family Mental Health</h2>
            <p>We support the whole system: parents, caregivers, children, teens, and the everyday rhythms that shape family life.</p>
            <ul class="check-grid">
                <li><?php echo prismpath_icon('check'); ?>Parent guidance and support</li>
                <li><?php echo prismpath_icon('check'); ?>Child and teen-informed care</li>
                <li><?php echo prismpath_icon('check'); ?>Family communication</li>
                <li><?php echo prismpath_icon('check'); ?>Co-regulation skills</li>
                <li><?php echo prismpath_icon('check'); ?>Everyday strategies that fit real life</li>
                <li><?php echo prismpath_icon('check'); ?>Coordinated, collaborative care</li>
            </ul>
            <a class="button button-amber" href="<?php echo esc_url(prismpath_whole_family_booking_url()); ?>">Explore Whole Family Support</a>
        </div>
        <img src="<?php echo esc_url(prismpath_asset('images/whole-family-path.png')); ?>" alt="Family walking together on a calm path" loading="lazy">
    </div>
</section>
