<?php
/**
 * Insurance and payment page template.
 *
 * @package Prismpath_Health
 */

get_header();
?>
<section class="page-hero">
    <div class="container narrow">
        <h1>Insurance and payment options.</h1>
        <p>Prismpath Health accepts Medicare and major commercial insurance plans, verifies benefits before scheduling, and offers self-pay pathways when insurance is not used or does not cover a service.</p>
        <div class="hero-actions">
            <a class="button button-primary" href="<?php echo esc_url(home_url('/contact/#consult')); ?>">Check Your Coverage</a>
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

<?php
get_template_part('template-parts/sections/consult');
get_footer();
