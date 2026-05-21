<?php
/**
 * Occupational therapy section.
 *
 * @package Prismpath_Health
 */
?>
<section class="section ot-section">
    <div class="container ot-layout">
        <div class="reveal">
            <h2><?php echo esc_html(prismpath_home_field('ot_title')); ?></h2>
            <p><?php echo esc_html(prismpath_home_field('ot_intro')); ?></p>
            <div class="ot-points">
                <?php foreach (prismpath_home_lines('ot_points') as $point) : ?>
                    <span><?php echo esc_html($point); ?></span>
                <?php endforeach; ?>
            </div>
            <a class="button button-outline" href="<?php echo esc_url(home_url('/occupational-therapy/')); ?>">Explore Occupational Therapy</a>
        </div>
        <div class="soft-panel reveal">
            <p><?php echo esc_html(prismpath_home_field('ot_quote')); ?></p>
        </div>
    </div>
</section>
