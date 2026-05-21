<?php
/**
 * Front page template.
 *
 * @package Prismpath_Health
 */

get_header();
get_template_part('template-parts/sections/hero');
if (prismpath_home_section_enabled('trust')) {
    get_template_part('template-parts/sections/trust');
}
if (prismpath_home_section_enabled('services')) {
    get_template_part('template-parts/sections/services');
}
if (prismpath_home_section_enabled('approach')) {
    get_template_part('template-parts/sections/approach');
}
if (prismpath_home_section_enabled('process')) {
    get_template_part('template-parts/sections/process');
}
if (prismpath_home_section_enabled('whole_family')) {
    get_template_part('template-parts/sections/whole-family');
}
if (prismpath_home_section_enabled('ot')) {
    get_template_part('template-parts/sections/occupational-therapy');
}
if (prismpath_home_section_enabled('consult')) {
    get_template_part('template-parts/sections/consult');
}
get_footer();
