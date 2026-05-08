<?php
/**
 * Team page template.
 *
 * @package Prismpath_Health
 */

get_header();
$team = new WP_Query(array(
    'post_type' => 'team_member',
    'posts_per_page' => -1,
    'orderby' => 'menu_order title',
    'order' => 'ASC',
));
?>
<section class="page-hero">
    <div class="container narrow">
        <h1>The people behind Prismpath Health.</h1>
        <p>Our clinicians bring professional training, practical care experience, and a deep respect for neurodivergent lives.</p>
    </div>
</section>
<section class="team-list">
    <div class="container card-grid team-grid">
        <?php if ($team->have_posts()) : ?>
            <?php while ($team->have_posts()) : $team->the_post(); ?>
                <article class="team-card">
                    <a href="<?php the_permalink(); ?>">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('prismpath-card', array('class' => 'team-photo')); ?>
                        <?php endif; ?>
                        <h2><?php the_title(); ?></h2>
                        <p><?php echo esc_html(get_the_excerpt()); ?></p>
                    </a>
                </article>
            <?php endwhile; wp_reset_postdata(); ?>
        <?php else : ?>
            <p>No team members have been added yet.</p>
        <?php endif; ?>
    </div>
</section>
<?php
get_footer();
