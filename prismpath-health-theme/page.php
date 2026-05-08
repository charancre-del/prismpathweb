<?php
/**
 * Generic page template.
 *
 * @package Prismpath_Health
 */

get_header();

while (have_posts()) :
    the_post();
    ?>
    <section class="page-hero">
        <div class="container narrow">
            <h1><?php the_title(); ?></h1>
            <?php if (has_excerpt()) : ?>
                <p><?php echo esc_html(get_the_excerpt()); ?></p>
            <?php endif; ?>
        </div>
    </section>
    <section class="page-content">
        <div class="container narrow prose">
            <?php the_content(); ?>
        </div>
    </section>
    <?php
endwhile;

get_footer();
