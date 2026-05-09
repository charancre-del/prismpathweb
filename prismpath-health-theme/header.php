<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class('prismpath-site'); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main-content"><?php esc_html_e('Skip to main content', 'prismpath-health'); ?></a>

<header class="site-header" data-site-header>
    <div class="container header-inner">
        <a class="site-logo" href="<?php echo esc_url(home_url('/')); ?>">
            <?php prismpath_render_logo(); ?>
        </a>

        <nav class="primary-nav" aria-label="<?php esc_attr_e('Primary navigation', 'prismpath-health'); ?>">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'container' => false,
                'fallback_cb' => 'prismpath_primary_nav_fallback',
                'items_wrap' => '%3$s',
                'depth' => 1,
            ));
            ?>
        </nav>

        <a class="button button-coral header-cta" href="<?php echo esc_url(prismpath_booking_url()); ?>">
            <?php esc_html_e('Book a Consultation', 'prismpath-health'); ?>
        </a>

        <button class="mobile-toggle" type="button" data-mobile-toggle aria-expanded="false" aria-controls="mobile-menu">
            <span class="sr-only"><?php esc_html_e('Open navigation', 'prismpath-health'); ?></span>
            <span></span><span></span><span></span>
        </button>
    </div>
    <nav id="mobile-menu" class="mobile-nav" data-mobile-menu aria-label="<?php esc_attr_e('Mobile navigation', 'prismpath-health'); ?>">
        <?php
        wp_nav_menu(array(
            'theme_location' => 'primary',
            'container' => false,
            'fallback_cb' => 'prismpath_primary_nav_fallback',
            'items_wrap' => '%3$s',
            'depth' => 1,
        ));
        ?>
        <a class="button button-coral" href="<?php echo esc_url(prismpath_booking_url()); ?>">
            <?php esc_html_e('Book a Consultation', 'prismpath-health'); ?>
        </a>
    </nav>
</header>

<main id="main-content">
<?php
function prismpath_primary_nav_fallback(): void
{
    $items = array(
        '/services/' => 'Services',
        '/adhd-autism-assessments/' => 'Assessments',
        '/whole-family-mental-health/' => 'Whole Family Mental Health',
        '/resources/' => 'Resources',
        '/insurance-payment/' => 'Insurance',
        '/approach/' => 'Approach',
        '/contact/' => 'Contact',
    );
    foreach ($items as $url => $label) {
        echo '<a href="' . esc_url(home_url($url)) . '">' . esc_html($label) . '</a>';
    }
}
