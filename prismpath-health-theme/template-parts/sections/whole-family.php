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
            <p>Prismpath supports the family system through parent and caregiver guidance, communication support, and coordinated mental health planning. Pediatric ABA, speech, and occupational therapy services are available through Chroma Early Start.</p>
            <ul class="check-grid">
                <li><?php echo prismpath_icon('check'); ?>Parent guidance and support</li>
                <li><?php echo prismpath_icon('check'); ?>Pediatric therapy pathways through Chroma Early Start</li>
                <li><?php echo prismpath_icon('check'); ?>Family communication</li>
                <li><?php echo prismpath_icon('check'); ?>Co-regulation skills</li>
                <li><?php echo prismpath_icon('check'); ?>Everyday strategies that fit real life</li>
                <li><?php echo prismpath_icon('check'); ?>Coordinated, collaborative care</li>
            </ul>
            <a class="button button-amber" href="<?php echo esc_url(prismpath_whole_family_booking_url()); ?>">Explore Whole Family Support</a>
            <a class="text-link" href="<?php echo esc_url(prismpath_chroma_early_start_url()); ?>" target="_blank" rel="noopener">View pediatric therapy at Chroma Early Start</a>
        </div>
        <img src="<?php echo esc_url(prismpath_asset('images/whole-family-path.png')); ?>" alt="Family walking together on a calm path" loading="lazy">
    </div>
</section>
