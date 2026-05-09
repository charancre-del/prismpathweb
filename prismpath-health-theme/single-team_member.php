<?php
/**
 * Team member detail template.
 *
 * @package Prismpath_Health
 */

get_header();

while (have_posts()) :
    the_post();
    ?>
    <section class="page-hero bio-hero">
        <div class="container bio-layout">
            <div>
                <a class="back-link" href="<?php echo esc_url(home_url('/team/')); ?>">Back to team</a>
                <h1><?php the_title(); ?></h1>
                <?php if (has_excerpt()) : ?><p><?php echo esc_html(get_the_excerpt()); ?></p><?php endif; ?>
            </div>
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('prismpath-card', array('class' => 'bio-photo')); ?>
            <?php elseif (prismpath_team_photo_url(get_the_ID())) : ?>
                <?php $team_photo = (string) get_post_meta(get_the_ID(), '_prismpath_team_photo', true); ?>
                <img class="bio-photo" src="<?php echo esc_url(prismpath_team_photo_url(get_the_ID())); ?>" alt="<?php the_title_attribute(); ?>"<?php echo prismpath_image_size_attrs('images/team/' . sanitize_file_name($team_photo)); ?> loading="lazy" decoding="async">
            <?php endif; ?>
        </div>
    </section>
    <section class="page-content">
        <div class="container narrow prose"><?php the_content(); ?></div>
    </section>
    <?php
endwhile;

get_footer();
