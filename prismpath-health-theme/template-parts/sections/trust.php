<?php
/**
 * Homepage trust strip.
 *
 * @package Prismpath_Health
 */
?>
<section class="trust-strip">
    <div class="container trust-inner reveal">
        <span class="trust-label"><?php echo esc_html(prismpath_home_field('trust_label')); ?></span>
        <div class="trust-tags">
            <?php foreach (prismpath_home_lines('trust_tags') as $tag) : ?>
                <span><?php echo esc_html($tag); ?></span>
            <?php endforeach; ?>
        </div>
    </div>
</section>
