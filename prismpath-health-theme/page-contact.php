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
?>
<section class="page-hero">
    <div class="container narrow">
        <h1>Start the conversation.</h1>
        <p>Tell us what kind of support you are looking for, and our team will help you find the clearest next step.</p>
    </div>
</section>
<section class="contact-options">
    <div class="container contact-grid">
        <?php if ($email) : ?><a class="contact-card" href="mailto:<?php echo esc_attr($email); ?>"><span>Email</span><?php echo esc_html($email); ?></a><?php endif; ?>
        <?php if ($phone) : ?><a class="contact-card" href="<?php echo esc_url(prismpath_phone_href($phone)); ?>"><span>Call</span><?php echo esc_html($phone); ?></a><?php endif; ?>
        <?php if ($text) : ?><a class="contact-card" href="<?php echo esc_url(prismpath_phone_href($text)); ?>"><span>Text</span><?php echo esc_html($text); ?></a><?php endif; ?>
        <div class="contact-card"><span>Care</span>Virtual care where provider licensure allows</div>
    </div>
</section>
<?php get_template_part('template-parts/sections/consult'); ?>
<?php get_footer(); ?>
