<?php
/**
 * Single post template.
 *
 * @package Prismpath_Health
 */

get_header();

$posts_page_id = (int) get_option('page_for_posts');
$blog_url = $posts_page_id ? (string) get_permalink($posts_page_id) : home_url('/blog/');

while (have_posts()) :
    the_post();
    ?>
    <section class="page-hero">
        <div class="container narrow">
            <p class="micro-proof"><?php echo esc_html(get_the_date('F j, Y')); ?></p>
            <h1><?php the_title(); ?></h1>
            <?php if (has_excerpt()) : ?>
                <p><?php echo esc_html(get_the_excerpt()); ?></p>
            <?php endif; ?>
        </div>
    </section>
    <section class="page-content">
        <div class="container narrow prose">
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('large', array('class' => 'blog-featured-image')); ?>
            <?php endif; ?>
            <?php the_content(); ?>
            <p class="blog-back-link"><a href="<?php echo esc_url($blog_url); ?>"><?php esc_html_e('Back to Blog', 'prismpath-health'); ?></a></p>
        </div>
    </section>
    <?php
endwhile;

get_footer();
