<?php
/**
 * Contact page template.
 *
 * @package Prismpath_Health
 */

get_header();
$email = prismpath_setting('primary_email', get_option('admin_email'));
$phone = prismpath_setting('phone', '');
$text = prismpath_setting('text_number', '');
$address = prismpath_setting('mailing_address', 'Miami, FL 33179');
?>
<section class="page-hero">
    <div class="container narrow">
        <h1>Contact Us.</h1>
        <p>We want to be part of your journey. Reach out and our team can help verify insurance, answer questions, or book you directly.</p>
    </div>
</section>
<section class="contact-options">
    <div class="container contact-grid">
        <?php if ($email) : ?><a class="contact-card" href="mailto:<?php echo esc_attr($email); ?>"><span>Email</span><?php echo esc_html($email); ?></a><?php endif; ?>
        <?php if ($phone) : ?><a class="contact-card" href="<?php echo esc_url(prismpath_phone_href($phone)); ?>"><span>Call</span><?php echo esc_html($phone); ?></a><?php endif; ?>
        <?php if ($text) : ?><a class="contact-card" href="<?php echo esc_url(prismpath_phone_href($text)); ?>"><span>Text</span><?php echo esc_html($text); ?></a><?php endif; ?>
        <?php if ($address) : ?><div class="contact-card"><span>Location</span><?php echo esc_html($address); ?></div><?php endif; ?>
    </div>
</section>
<?php get_template_part('template-parts/sections/consult'); ?>
<section id="program-signup" class="page-content program-signup-section">
    <div class="container detail-layout">
        <div class="prose">
            <h2>Sign up to learn about upcoming programs.</h2>
            <p>Share your email and we will send updates when new Prismpath Health groups, programs, or care pathways become available.</p>
        </div>
        <div class="service-panel">
            <?php
            if (shortcode_exists('prismpath_program_signup_form')) {
                echo do_shortcode('[prismpath_program_signup_form]');
            } else {
                echo '<p class="notice">Activate the Prismpath Consult Form plugin to display the program signup form.</p>';
            }
            ?>
        </div>
    </div>
</section>
<?php get_footer(); ?>
