<?php
/**
 * Prismpath Health theme bootstrap.
 *
 * @package Prismpath_Health
 */

if (!defined('ABSPATH')) {
    exit;
}

define('PRISMPATH_VERSION', '1.0.0');
define('PRISMPATH_THEME_DIR', get_template_directory());
define('PRISMPATH_THEME_URI', get_template_directory_uri());

require_once PRISMPATH_THEME_DIR . '/inc/team-data.php';
require_once PRISMPATH_THEME_DIR . '/inc/setup.php';
require_once PRISMPATH_THEME_DIR . '/inc/settings.php';
require_once PRISMPATH_THEME_DIR . '/inc/template-tags.php';
require_once PRISMPATH_THEME_DIR . '/inc/editor-meta.php';
require_once PRISMPATH_THEME_DIR . '/inc/seeder.php';
require_once PRISMPATH_THEME_DIR . '/inc/enqueue.php';
require_once PRISMPATH_THEME_DIR . '/inc/redirects.php';
