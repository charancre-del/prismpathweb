<?php
/**
 * Getting started process.
 *
 * @package Prismpath_Health
 */

$steps = array(
    array('Reach out', 'Share what you are looking for and how we can help.'),
    array('We connect', 'We match you with the right provider or team path.'),
    array('Your first visit', 'A collaborative session to understand your goals and next steps.'),
    array('Ongoing support', 'Care that grows with you and adapts to real life.'),
);
?>
<section class="section process-section">
    <div class="container">
        <div class="section-heading">
            <h2>Getting started is simple.</h2>
        </div>
        <ol class="process-list">
            <?php foreach ($steps as $index => $step) : ?>
                <li>
                    <span><?php echo esc_html((string) ($index + 1)); ?></span>
                    <h3><?php echo esc_html($step[0]); ?></h3>
                    <p><?php echo esc_html($step[1]); ?></p>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>
