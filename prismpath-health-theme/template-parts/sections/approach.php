<?php
/**
 * Approach section.
 *
 * @package Prismpath_Health
 */
?>
<section id="approach" class="section approach-section">
    <div class="container approach-grid">
        <div class="reveal">
            <p class="eyebrow"><?php echo esc_html(prismpath_home_field('approach_eyebrow')); ?></p>
            <h2><?php echo esc_html(prismpath_home_field('approach_title')); ?></h2>
            <p><?php echo esc_html(prismpath_home_field('approach_intro')); ?></p>
            <blockquote><?php echo esc_html(prismpath_home_field('approach_quote')); ?></blockquote>
            <a class="text-link" href="<?php echo esc_url(home_url('/approach/')); ?>"><?php esc_html_e('Learn about our approach', 'prismpath-health'); ?></a>
        </div>
        <div class="approach-values reveal">
            <?php foreach (prismpath_home_pipe_rows('approach_values', array('title', 'description')) as $index => $value) : ?>
                <article class="value-item">
                    <span><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                    <h3><?php echo esc_html($value['title']); ?></h3>
                    <p><?php echo esc_html($value['description']); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
