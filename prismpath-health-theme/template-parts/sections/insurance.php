<?php
/**
 * Insurance and payment overview.
 *
 * @package Prismpath_Health
 */
?>
<section class="section insurance-section">
    <div class="container">
        <div class="section-heading">
            <h2>Insurance and payment options.</h2>
            <p>Prismpath accepts Medicare and major commercial plans and verifies benefits before scheduling so clients can understand estimated out-of-pocket costs.</p>
        </div>

        <div class="insurance-grid" aria-label="<?php esc_attr_e('Accepted insurance plans', 'prismpath-health'); ?>">
            <?php foreach (prismpath_insurance_plans() as $plan) : ?>
                <div class="insurance-card">
                    <img src="<?php echo esc_url(prismpath_asset('images/insurance/' . $plan['logo'])); ?>" alt="<?php echo esc_attr($plan['name']); ?>">
                </div>
            <?php endforeach; ?>
        </div>

        <div class="payment-grid">
            <?php foreach (prismpath_payment_options() as $option) : ?>
                <div class="payment-card">
                    <?php echo prismpath_icon('check'); ?>
                    <p><?php echo esc_html($option); ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <p class="insurance-note">Coverage, deductibles, copays, coinsurance, prior authorization, and patient responsibility vary by plan, provider, service, state, and member benefits. Prismpath verifies benefits before care and can discuss self-pay pathways when insurance is not used or does not cover a service.</p>

        <div class="section-actions">
            <a class="button button-primary" href="<?php echo esc_url(home_url('/insurance-payment/')); ?>">Review Insurance & Payment</a>
            <a class="button button-outline" href="<?php echo esc_url(home_url('/contact/#consult')); ?>">Check Your Coverage</a>
        </div>
    </div>
</section>
