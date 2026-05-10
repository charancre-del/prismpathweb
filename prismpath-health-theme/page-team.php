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
$team_count = (int) $team->found_posts;
?>
<section class="page-hero">
    <div class="container narrow">
        <h1>The people behind Prismpath Health.</h1>
        <p>Our clinicians bring professional training, practical care experience, and a deep respect for neurodivergent lives.</p>
        <?php if ($team_count > 0) : ?>
            <p class="micro-proof">Browse <?php echo esc_html((string) $team_count); ?> provider profiles across assessment, therapy, psychiatric leadership, and family-centered support.</p>
        <?php endif; ?>
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
                        <?php elseif (prismpath_team_photo_url(get_the_ID())) : ?>
                            <?php $team_photo = (string) get_post_meta(get_the_ID(), '_prismpath_team_photo', true); ?>
                            <img class="team-photo" src="<?php echo esc_url(prismpath_team_photo_url(get_the_ID())); ?>" alt="<?php the_title_attribute(); ?>"<?php echo prismpath_image_size_attrs('images/team/' . sanitize_file_name($team_photo)); ?> loading="lazy" decoding="async">
                        <?php endif; ?>
                        <h2><?php the_title(); ?></h2>
                        <p><?php echo esc_html(get_the_excerpt()); ?></p>
                    </a>
                </article>
            <?php endwhile; wp_reset_postdata(); ?>
        <?php else : ?>
            <p>Team profiles are being prepared for publication.</p>
        <?php endif; ?>
    </div>
</section>
<?php
get_footer();
