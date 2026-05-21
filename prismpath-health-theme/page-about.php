<?php
/**
 * About page template.
 *
 * @package Prismpath_Health
 */

get_header();
$content = array(
    'title' => 'About Prismpath Health.',
    'intro' => 'Neuroaffirming mental health care, assessment, psychiatry, occupational therapy, and caregiver-centered support for adults and families.',
    'primary_cta_label' => 'Start a Conversation',
    'primary_cta_url' => home_url('/contact/'),
);
if (function_exists('prismpath_page_content_overrides')) {
    $content = array_merge($content, prismpath_page_content_overrides((int) get_queried_object_id()));
}
$providers = new WP_Query(array(
    'post_type' => 'team_member',
    'posts_per_page' => -1,
    'orderby' => 'menu_order title',
    'order' => 'ASC',
));
?>
<section class="page-hero">
    <div class="container narrow">
        <?php if (!empty($content['eyebrow'])) : ?><p class="eyebrow"><?php echo esc_html($content['eyebrow']); ?></p><?php endif; ?>
        <h1><?php echo esc_html($content['title']); ?></h1>
        <p><?php echo esc_html($content['intro']); ?></p>
        <a class="button button-primary" href="<?php echo esc_url($content['primary_cta_url']); ?>"><?php echo esc_html($content['primary_cta_label']); ?></a>
    </div>
</section>

<section class="page-content">
    <div class="container detail-layout">
        <div class="prose">
            <h2>Different does not mean broken.</h2>
            <p>Prismpath Health was built around a simple belief: people deserve care that helps them understand their brain, their context, their strengths, and their real-life needs without being reduced to a label.</p>
            <p>Our work is especially shaped by neuroaffirming care for adults with ADHD, Autism, anxiety, burnout, trauma histories, sensory needs, and complex family or caregiving responsibilities. We also support people who are still trying to understand what kind of care or assessment may fit.</p>

            <div class="content-section">
                <h2>What neuroaffirming care means here</h2>
                <p>Neuroaffirming care does not mean ignoring distress or avoiding practical change. It means we work collaboratively, respect lived experience, and focus on supports that fit the person rather than asking every person to fit the same model.</p>
                <ul class="check-grid">
                    <li><?php prismpath_icon('check'); ?> Care that listens before it labels</li>
                    <li><?php prismpath_icon('check'); ?> Strategies that support daily life</li>
                    <li><?php prismpath_icon('check'); ?> Assessment language that is careful and useful</li>
                    <li><?php prismpath_icon('check'); ?> Collaboration across therapy, psychiatry, OT, and referrals</li>
                </ul>
            </div>

            <div class="content-section">
                <h2>Whole-person, whole-family context</h2>
                <p>Mental health rarely lives in one appointment slot. Work, school, caregiving, sensory load, relationships, sleep, routines, identity, and access needs all matter. Prismpath Health considers those pieces as part of care planning when they are relevant.</p>
                <p>Our Whole Family Mental Health work is caregiver-centered and family-system aware. Pediatric ABA, speech, and pediatric occupational therapy services are routed through Chroma Early Start, so families can find the right pediatric pathway without confusing it with Prismpath adult mental health services.</p>
            </div>
        </div>

        <aside class="detail-list">
            <h2>Care pathways</h2>
            <ul>
                <li><?php prismpath_icon('chat'); ?> <a href="<?php echo esc_url(home_url('/therapy/')); ?>">Neuroaffirming therapy</a></li>
                <li><?php prismpath_icon('brain'); ?> <a href="<?php echo esc_url(home_url('/psychiatry/')); ?>">Psychiatric care</a></li>
                <li><?php prismpath_icon('check'); ?> <a href="<?php echo esc_url(home_url('/adhd-autism-assessments/')); ?>">ADHD & Autism assessments</a></li>
                <li><?php prismpath_icon('hands'); ?> <a href="<?php echo esc_url(home_url('/occupational-therapy/')); ?>">Adult occupational therapy</a></li>
                <li><?php prismpath_icon('family'); ?> <a href="<?php echo esc_url(home_url('/whole-family-mental-health/')); ?>">Whole Family Mental Health</a></li>
                <li><?php prismpath_icon('check'); ?> <a href="<?php echo esc_url(home_url('/team/')); ?>">Meet the team</a></li>
            </ul>
        </aside>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading align-left">
            <h2>Our providers.</h2>
            <p>Meet the clinicians and care leaders carrying forward the legacy LBee Health team into Prismpath Health.</p>
        </div>
        <div class="card-grid team-grid">
            <?php if ($providers->have_posts()) : ?>
                <?php while ($providers->have_posts()) : $providers->the_post(); ?>
                    <article class="team-card">
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('prismpath-card', array('class' => 'team-photo')); ?>
                            <?php elseif (prismpath_team_photo_url(get_the_ID())) : ?>
                                <?php $team_photo = (string) get_post_meta(get_the_ID(), '_prismpath_team_photo', true); ?>
                                <img class="team-photo" src="<?php echo esc_url(prismpath_team_photo_url(get_the_ID())); ?>" alt="<?php the_title_attribute(); ?>"<?php echo prismpath_image_size_attrs('images/team/' . sanitize_file_name($team_photo)); ?> loading="lazy" decoding="async">
                            <?php endif; ?>
                            <h3><?php the_title(); ?></h3>
                            <p><?php echo esc_html(get_the_excerpt()); ?></p>
                        </a>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            <?php else : ?>
                <p>Provider profiles are being prepared for publication.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="faq-support-section">
    <div class="container faq-support-grid single-column">
        <div class="support-panel">
            <h2>Care shaped around real life.</h2>
            <p>We combine clinical insight, practical strategy, and respectful collaboration so care feels usable outside the session too.</p>
            <div class="section-actions">
                <a class="button button-primary" href="<?php echo esc_url(home_url('/services/')); ?>">Explore Services</a>
                <a class="button button-outline" href="<?php echo esc_url(home_url('/team/')); ?>">Meet Our Providers</a>
            </div>
        </div>
    </div>
</section>
<?php
get_footer();
