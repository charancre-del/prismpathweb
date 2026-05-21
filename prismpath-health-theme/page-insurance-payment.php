<?php
/**
 * Insurance and payment page template.
 *
 * @package Prismpath_Health
 */

get_header();
$content = array(
    'title' => 'Insurance and payment options.',
    'intro' => 'Prismpath Health accepts Medicare and major commercial insurance plans, verifies benefits before scheduling, and offers self-pay pathways when insurance is not used or does not cover a service.',
);
if (function_exists('prismpath_page_content_overrides')) {
    $content = array_merge($content, prismpath_page_content_overrides((int) get_queried_object_id()));
}
?>
<section class="page-hero">
    <div class="container narrow">
        <?php if (!empty($content['eyebrow'])) : ?><p class="eyebrow"><?php echo esc_html($content['eyebrow']); ?></p><?php endif; ?>
        <h1><?php echo esc_html($content['title']); ?></h1>
        <p><?php echo esc_html($content['intro']); ?></p>
        <div class="hero-actions">
            <a class="button button-primary" href="#insurance-verification">Check Your Coverage</a>
            <a class="button button-outline" href="<?php echo esc_url(home_url('/services/')); ?>">Explore Services</a>
        </div>
    </div>
</section>

<?php get_template_part('template-parts/sections/insurance'); ?>

<section class="page-content">
    <div class="container detail-layout">
        <div class="prose">
            <section class="content-section">
                <h2>How benefits verification works</h2>
                <p>Prismpath can verify benefits before scheduling and provide an estimate of expected costs. Insurance may apply a service to a deductible, require copays or coinsurance, or have different rules by state, provider, service, and member plan.</p>
            </section>
            <section class="content-section">
                <h2>Assessment payment pathways</h2>
                <p>Assessment-related costs may be covered in whole or in part depending on the plan. Self-pay and CareCredit financing may be available. Deposits may be required to hold insurance-based appointments and are applied toward out-of-pocket responsibility such as copays, deductibles, or coinsurance.</p>
            </section>
            <section class="content-section">
                <h2>Self-pay options</h2>
                <p>Self-pay may be appropriate when someone does not want to use insurance, when a service is not covered, or when benefits do not fit the care pathway. Prismpath can review available options during the consultation process.</p>
            </section>
        </div>
        <aside class="detail-list">
            <h2>Accepted plans listed</h2>
            <ul>
                <?php foreach (prismpath_insurance_plans() as $plan) : ?>
                    <li><?php echo prismpath_icon('check'); ?><span><?php echo esc_html($plan['name']); ?></span></li>
                <?php endforeach; ?>
            </ul>
        </aside>
    </div>
</section>

<section id="insurance-verification" class="section insurance-form-section">
    <div class="container">
        <div class="section-heading">
            <p class="eyebrow">Benefits verification</p>
            <h2>Share your insurance details through Jotform.</h2>
            <p>Use Prismpath Health's secure insurance verification form to send the information our team needs for preliminary benefits review and next steps.</p>
        </div>
        <div class="insurance-form-wrap insurance-jotform-card">
            <div>
                <h3>Insurance Verification Form</h3>
                <p>You'll open the secure form in a new tab. Coverage, appointment timing, authorizations, and final patient responsibility depend on the plan and are not guaranteed by submitting the form.</p>
            </div>
            <a class="button button-coral" href="https://form.jotform.com/251107144019042" target="_blank" rel="noopener">Open Insurance Form</a>
        </div>
    </div>
</section>

<?php
get_template_part('template-parts/sections/consult');
get_footer();
