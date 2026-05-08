<?php
/**
 * 404 template.
 *
 * @package Prismpath_Health
 */

get_header();
?>
<section class="page-hero">
    <div class="container narrow">
        <h1><?php esc_html_e('This path does not lead anywhere yet.', 'prismpath-health'); ?></h1>
        <p><?php esc_html_e('The page may have moved during the Prismpath Health rebrand. Start from the homepage or contact us for help.', 'prismpath-health'); ?></p>
        <a class="button button-primary" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Return Home', 'prismpath-health'); ?></a>
    </div>
</section>
<?php
get_footer();
