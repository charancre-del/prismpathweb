<?php
/**
 * Blog index template.
 *
 * @package Prismpath_Health
 */

get_header();

$posts_page_id = (int) get_option('page_for_posts');
$blog_title = $posts_page_id ? get_the_title($posts_page_id) : __('Blog', 'prismpath-health');
$blog_intro = $posts_page_id ? get_the_excerpt($posts_page_id) : '';
if (!$blog_intro) {
    $blog_intro = __('Neuroaffirming mental health notes, practice updates, and resources from Prismpath Health.', 'prismpath-health');
}
?>
<section class="page-hero blog-hero">
    <div class="container blog-hero-layout">
        <div class="blog-hero-copy">
            <p class="eyebrow"><?php esc_html_e('Prismpath Journal', 'prismpath-health'); ?></p>
            <h1><?php echo esc_html($blog_title); ?></h1>
            <p><?php echo esc_html($blog_intro); ?></p>
        </div>
        <div class="blog-hero-topics" aria-label="<?php esc_attr_e('Blog topics', 'prismpath-health'); ?>">
            <span><?php esc_html_e('Neuroaffirming care', 'prismpath-health'); ?></span>
            <span><?php esc_html_e('Assessment clarity', 'prismpath-health'); ?></span>
            <span><?php esc_html_e('Daily-life supports', 'prismpath-health'); ?></span>
        </div>
    </div>
</section>

<section class="page-content blog-index">
    <div class="container">
        <?php if (have_posts()) : ?>
            <div class="blog-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <article <?php post_class('blog-card'); ?>>
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('prismpath-card', array('class' => 'blog-card-image')); ?>
                            <?php endif; ?>
                            <div class="blog-card-body">
                                <p class="blog-meta"><?php echo esc_html(get_the_date('F j, Y')); ?></p>
                                <h2><?php the_title(); ?></h2>
                                <?php if (has_excerpt()) : ?>
                                    <p><?php echo esc_html(get_the_excerpt()); ?></p>
                                <?php else : ?>
                                    <p><?php echo esc_html(wp_trim_words(get_the_content(), 26)); ?></p>
                                <?php endif; ?>
                                <span class="text-link"><?php esc_html_e('Read Article', 'prismpath-health'); ?></span>
                            </div>
                        </a>
                    </article>
                <?php endwhile; ?>
            </div>
            <div class="blog-pagination">
                <?php the_posts_pagination(array(
                    'mid_size' => 1,
                    'prev_text' => __('Previous', 'prismpath-health'),
                    'next_text' => __('Next', 'prismpath-health'),
                )); ?>
            </div>
        <?php else : ?>
            <div class="support-panel blog-empty">
                <h2><?php esc_html_e('Articles are being prepared.', 'prismpath-health'); ?></h2>
                <p><?php esc_html_e('Prismpath Health blog posts will appear here once they are published. In the meantime, explore services or contact the team for support.', 'prismpath-health'); ?></p>
                <div class="section-actions">
                    <a class="button button-primary" href="<?php echo esc_url(home_url('/services/')); ?>"><?php esc_html_e('Explore Services', 'prismpath-health'); ?></a>
                    <a class="button button-outline" href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Contact Us', 'prismpath-health'); ?></a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php
get_footer();
