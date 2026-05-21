<?php
/**
 * Getting started process.
 *
 * @package Prismpath_Health
 */

$steps = prismpath_home_pipe_rows('process_steps', array('title', 'description'));
?>
<section class="section process-section journey-section">
    <div class="container">
        <div class="section-heading reveal">
            <p class="eyebrow"><?php echo esc_html(prismpath_home_field('process_eyebrow')); ?></p>
            <h2><?php echo esc_html(prismpath_home_field('process_title')); ?></h2>
        </div>
        <ol class="process-list">
            <?php foreach ($steps as $index => $step) : ?>
                <li class="reveal">
                    <span><?php echo esc_html((string) ($index + 1)); ?></span>
                    <h3><?php echo esc_html($step['title']); ?></h3>
                    <p><?php echo esc_html($step['description']); ?></p>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>
