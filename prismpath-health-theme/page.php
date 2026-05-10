<?php
/**
 * Generic page template.
 *
 * @package Prismpath_Health
 */

get_header();

while (have_posts()) :
    the_post();
    $slug = (string) get_post_field('post_name', get_the_ID());
    $resource = function_exists('prismpath_resource_record_by_slug') ? prismpath_resource_record_by_slug($slug) : null;
    if ($resource) :
        $resource = array_merge($resource, function_exists('prismpath_page_content_overrides') ? prismpath_page_content_overrides((int) get_the_ID()) : array());
        $related_links = !empty($resource['related_links']) && is_array($resource['related_links'])
            ? $resource['related_links']
            : prismpath_related_links_for_slug($slug);
        ?>
        <section class="page-hero">
            <div class="container narrow">
                <p class="micro-proof"><?php echo esc_html(sprintf('Updated %s', get_the_modified_date('F Y'))); ?></p>
                <h1><?php echo esc_html($resource['title']); ?></h1>
                <p><?php echo esc_html($resource['intro']); ?></p>
            </div>
        </section>
        <section class="page-content">
            <div class="container detail-layout">
                <article class="prose">
                    <?php
                    if (trim(get_the_content())) {
                        the_content();
                    }
                    if (!empty($resource['sections']) && is_array($resource['sections'])) :
                        foreach ($resource['sections'] as $section) :
                            ?>
                            <section class="content-section">
                                <h2><?php echo esc_html($section['heading']); ?></h2>
                                <p><?php echo esc_html($section['body']); ?></p>
                            </section>
                            <?php
                        endforeach;
                    endif;
                    ?>
                </article>
                <aside class="detail-list">
                    <h2><?php esc_html_e('Related care', 'prismpath-health'); ?></h2>
                    <div class="related-list">
                        <?php foreach ($related_links as $link) : ?>
                            <a href="<?php echo esc_url($link['url']); ?>">
                                <span><?php echo esc_html($link['label']); ?></span>
                                <?php if (!empty($link['description'])) : ?><small><?php echo esc_html($link['description']); ?></small><?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <?php if (!empty($resource['references']) && is_array($resource['references'])) : ?>
                        <h2><?php esc_html_e('References', 'prismpath-health'); ?></h2>
                        <div class="related-list">
                            <?php foreach ($resource['references'] as $reference) : ?>
                                <a href="<?php echo esc_url($reference['url']); ?>" target="_blank" rel="noopener">
                                    <span><?php echo esc_html($reference['label']); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </aside>
            </div>
        </section>
        <?php if (!empty($resource['faqs']) && is_array($resource['faqs'])) : ?>
            <section class="faq-support-section">
                <div class="container faq-support-grid single-column">
                    <div class="support-panel faq-panel">
                        <h2><?php esc_html_e('Common questions', 'prismpath-health'); ?></h2>
                        <div class="faq-list">
                            <?php foreach ($resource['faqs'] as $faq) : ?>
                                <details>
                                    <summary><?php echo esc_html($faq['question']); ?></summary>
                                    <p><?php echo esc_html($faq['answer']); ?></p>
                                </details>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>
        <?php get_template_part('template-parts/sections/consult'); ?>
        <?php
        continue;
    endif;
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
