<?php
/**
 * Fallback template.
 *
 * @package Prismpath_Health
 */

get_header();
?>
<section class="page-hero">
    <div class="container narrow">
        <h1><?php echo esc_html(get_bloginfo('name') ?: 'Prismpath Health'); ?></h1>
        <p>A clearer path to neuroaffirming mental health care.</p>
    </div>
</section>
<section class="page-content">
    <div class="container narrow prose">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <article <?php post_class(); ?>>
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <?php the_excerpt(); ?>
                </article>
            <?php endwhile; ?>
        <?php else : ?>
            <p>No content found.</p>
        <?php endif; ?>
    </div>
</section>
<?php
get_footer();
