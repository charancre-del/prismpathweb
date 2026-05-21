<?php
/**
 * Careers page template.
 *
 * @package Prismpath_Health
 */

get_header();

$careers_email = sanitize_email(prismpath_setting('careers_email', prismpath_setting('primary_email', get_option('admin_email'))));
$careers_url = $careers_email
    ? 'mailto:' . $careers_email . '?subject=' . rawurlencode('Careers at Prismpath Health')
    : home_url('/contact/');
$content = array(
    'title' => 'Careers at Prismpath Health.',
    'intro' => 'Join a neuroaffirming mental health team building clearer, more respectful care pathways for adults, caregivers, and families.',
    'primary_cta_label' => 'Send Your Resume',
    'primary_cta_url' => $careers_url,
    'secondary_cta_label' => 'About Prismpath',
    'secondary_cta_url' => home_url('/about/'),
    'panel_heading' => 'Care that respects every brain.',
    'panel_body' => 'Our team works from a whole-person, relationship-centered philosophy grounded in compassion, curiosity, and practical support.',
);
if (function_exists('prismpath_page_content_overrides')) {
    $content = array_merge($content, prismpath_page_content_overrides((int) get_queried_object_id()));
}

$roles = array(
    array(
        'title' => 'Therapists',
        'icon' => 'chat',
        'summary' => 'Clinicians who bring neuroaffirming, trauma-informed care to adults, couples, caregivers, and families.',
    ),
    array(
        'title' => 'Psychiatric Providers',
        'icon' => 'brain',
        'summary' => 'Prescribers who value careful evaluation, collaborative planning, and whole-person medication support.',
    ),
    array(
        'title' => 'Assessment Clinicians',
        'icon' => 'search',
        'summary' => 'Providers experienced in respectful ADHD and Autism assessment, reporting, and practical recommendations.',
    ),
    array(
        'title' => 'Occupational Therapists',
        'icon' => 'hands',
        'summary' => 'OTs focused on sensory needs, executive function, routines, transitions, and daily life participation.',
    ),
    array(
        'title' => 'Care Coordination',
        'icon' => 'family',
        'summary' => 'Operations and support teammates who help clients and providers move through care with clarity.',
    ),
);

$values = array(
    'Neuroaffirming and strengths-based care',
    'Trauma-informed and nervous-system-aware practice',
    'LGBTQ+ affirming client and team culture',
    'Collaborative planning across disciplines',
    'Virtual care where provider licensure allows',
    'Respectful communication with adults, caregivers, and families',
);

$process_steps = array(
    array('label' => 'Send your resume', 'copy' => 'Share your background, licensure, states, clinical interests, and the kind of work you hope to do.'),
    array('label' => 'Introductory conversation', 'copy' => 'We look for alignment around care philosophy, provider scope, availability, and client population fit.'),
    array('label' => 'Team interview', 'copy' => 'Clinical and operational leaders discuss collaboration style, documentation, ethics, and practical workflow.'),
    array('label' => 'Credentialing and onboarding', 'copy' => 'Licensure, payer, privacy, and systems steps are reviewed before client-facing work begins.'),
);
?>
<section class="page-hero service-page-hero careers-hero">
    <div class="container split">
        <div>
            <?php if (!empty($content['eyebrow'])) : ?><p class="eyebrow"><?php echo esc_html($content['eyebrow']); ?></p><?php endif; ?>
            <h1><?php echo esc_html($content['title']); ?></h1>
            <p><?php echo esc_html($content['intro']); ?></p>
            <div class="hero-actions">
                <a class="button button-primary" href="<?php echo esc_url($content['primary_cta_url']); ?>"><?php echo esc_html($content['primary_cta_label']); ?></a>
                <a class="button button-outline" href="<?php echo esc_url($content['secondary_cta_url']); ?>"><?php echo esc_html($content['secondary_cta_label']); ?></a>
            </div>
        </div>
        <div class="service-panel">
            <span class="icon-circle"><?php echo prismpath_icon('check'); ?></span>
            <h2><?php echo esc_html($content['panel_heading']); ?></h2>
            <p><?php echo esc_html($content['panel_body']); ?></p>
        </div>
    </div>
</section>

<section class="page-content">
    <div class="container detail-layout">
        <article class="prose">
            <h2><?php esc_html_e('Join a team built around fit, dignity, and practical care.', 'prismpath-health'); ?></h2>
            <p><?php esc_html_e('Prismpath Health provides therapy, psychiatric care, occupational therapy, and ADHD and Autism assessments through a virtual care model where provider licensure allows. We are interested in clinicians and support professionals who want care to feel thoughtful, affirming, organized, and usable outside the appointment.', 'prismpath-health'); ?></p>

            <section class="content-section">
                <h2><?php esc_html_e('How we work', 'prismpath-health'); ?></h2>
                <p><?php esc_html_e('Our clinical culture is collaborative and client-centered. Providers consider sensory needs, executive function, communication, routines, identity, trauma history, and family context when those pieces matter to care. The goal is not one-size-fits-all wellness; it is support that fits real daily life.', 'prismpath-health'); ?></p>
            </section>

            <section class="content-section">
                <h2><?php esc_html_e('What team members can expect', 'prismpath-health'); ?></h2>
                <ul class="check-grid">
                    <li><?php echo prismpath_icon('check'); ?> <?php esc_html_e('Mission-aligned virtual mental health work', 'prismpath-health'); ?></li>
                    <li><?php echo prismpath_icon('check'); ?> <?php esc_html_e('A respectful, inclusive team environment', 'prismpath-health'); ?></li>
                    <li><?php echo prismpath_icon('check'); ?> <?php esc_html_e('Care pathways across therapy, psychiatry, OT, and assessment', 'prismpath-health'); ?></li>
                    <li><?php echo prismpath_icon('check'); ?> <?php esc_html_e('Room for thoughtful collaboration and clinical judgment', 'prismpath-health'); ?></li>
                </ul>
            </section>
        </article>

        <aside class="detail-list">
            <h2><?php esc_html_e('What we value', 'prismpath-health'); ?></h2>
            <ul>
                <?php foreach ($values as $value) : ?>
                    <li><?php echo prismpath_icon('check'); ?><span><?php echo esc_html($value); ?></span></li>
                <?php endforeach; ?>
            </ul>
        </aside>
    </div>
</section>

<section class="section careers-roles-section">
    <div class="container">
        <div class="section-heading align-left">
            <h2><?php esc_html_e('Who we are looking for.', 'prismpath-health'); ?></h2>
            <p><?php esc_html_e('We welcome interest from licensed and operations professionals who are aligned with neuroaffirming, trauma-informed, LGBTQ+ affirming care.', 'prismpath-health'); ?></p>
        </div>
        <div class="service-grid career-role-grid">
            <?php foreach ($roles as $role) : ?>
                <article class="service-card">
                    <span class="icon-circle"><?php echo prismpath_icon($role['icon']); ?></span>
                    <h2><?php echo esc_html($role['title']); ?></h2>
                    <p><?php echo esc_html($role['summary']); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="page-content">
    <div class="container detail-layout">
        <article class="prose">
            <section class="content-section">
                <h2><?php esc_html_e('Current opportunities', 'prismpath-health'); ?></h2>
                <p><?php esc_html_e('Specific openings may change as provider capacity, licensure coverage, and client needs evolve. If your background fits Prismpath Health, you are welcome to send your resume and a brief note about your licensure, specialties, preferred population, and availability.', 'prismpath-health'); ?></p>
                <div class="section-actions align-left">
                    <a class="button button-primary" href="<?php echo esc_url($careers_url); ?>"><?php esc_html_e('Send Your Resume', 'prismpath-health'); ?></a>
                    <a class="button button-outline" href="<?php echo esc_url(home_url('/services/')); ?>"><?php esc_html_e('Explore Care Pathways', 'prismpath-health'); ?></a>
                </div>
            </section>

            <section class="content-section">
                <h2><?php esc_html_e('Inclusive hiring', 'prismpath-health'); ?></h2>
                <p><?php esc_html_e('Prismpath Health is committed to respectful, inclusive hiring practices. We welcome candidates whose professional background, lived experience, and clinical perspective can help us provide thoughtful care for diverse adults, caregivers, and families.', 'prismpath-health'); ?></p>
            </section>
        </article>

        <aside class="detail-list career-process">
            <h2><?php esc_html_e('Hiring process', 'prismpath-health'); ?></h2>
            <ul>
                <?php foreach ($process_steps as $index => $step) : ?>
                    <li>
                        <span class="career-step-number"><?php echo esc_html((string) ($index + 1)); ?></span>
                        <span>
                            <strong><?php echo esc_html($step['label']); ?></strong>
                            <?php echo esc_html($step['copy']); ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>
    </div>
</section>

<section class="faq-support-section">
    <div class="container faq-support-grid single-column">
        <div class="support-panel">
            <h2><?php esc_html_e('Build care that feels clearer for every brain.', 'prismpath-health'); ?></h2>
            <p><?php esc_html_e('If your work is grounded in curiosity, collaboration, and respect for neurodivergent lives, we would be glad to hear from you.', 'prismpath-health'); ?></p>
            <div class="section-actions">
                <a class="button button-primary" href="<?php echo esc_url($careers_url); ?>"><?php esc_html_e('Send Your Resume', 'prismpath-health'); ?></a>
                <a class="button button-outline" href="<?php echo esc_url(home_url('/team/')); ?>"><?php esc_html_e('Meet the Team', 'prismpath-health'); ?></a>
            </div>
        </div>
    </div>
</section>
<?php
get_footer();
