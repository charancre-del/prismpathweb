<?php
/**
 * Whole family section.
 *
 * @package Prismpath_Health
 */
?>
<?php $family_image = prismpath_home_field('whole_family_image'); ?>
<section id="whole-family" class="section whole-family-section">
    <div class="container family-grid">
        <div class="family-visual reveal">
            <?php if ($family_image) : ?>
                <img src="<?php echo esc_url(prismpath_optimized_asset($family_image)); ?>" alt="Family walking together on a calm path"<?php echo prismpath_image_size_attrs($family_image); ?><?php echo prismpath_responsive_image_attrs($family_image, '(max-width: 900px) 92vw, 42vw'); ?> loading="lazy" decoding="async">
            <?php endif; ?>
            <div class="family-feature family-feature-1"><span></span><?php esc_html_e('Caregiver guidance', 'prismpath-health'); ?></div>
            <div class="family-feature family-feature-2"><span></span><?php esc_html_e('Co-regulation skills', 'prismpath-health'); ?></div>
            <div class="family-feature family-feature-3"><span></span><?php esc_html_e('Coordinated care', 'prismpath-health'); ?></div>
        </div>
        <div class="whole-copy reveal">
            <p class="eyebrow"><?php echo esc_html(prismpath_home_field('whole_family_eyebrow')); ?></p>
            <h2><?php echo esc_html(prismpath_home_field('whole_family_title')); ?></h2>
            <p><?php echo esc_html(prismpath_home_field('whole_family_intro')); ?></p>
            <ul class="check-grid">
                <?php foreach (prismpath_home_lines('whole_family_points') as $point) : ?>
                    <li><?php echo prismpath_icon('check'); ?><?php echo esc_html($point); ?></li>
                <?php endforeach; ?>
            </ul>
            <div class="section-actions align-left">
                <a class="button button-primary" href="<?php echo esc_url(prismpath_whole_family_booking_url()); ?>"><?php echo esc_html(prismpath_home_field('whole_family_primary_label')); ?></a>
                <a class="button button-outline" href="<?php echo esc_url(prismpath_chroma_early_start_url()); ?>" target="_blank" rel="noopener"><?php echo esc_html(prismpath_home_field('whole_family_secondary_label')); ?></a>
            </div>
        </div>
    </div>
</section>
