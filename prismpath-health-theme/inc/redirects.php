<?php
/**
 * Legacy URL redirects.
 *
 * @package Prismpath_Health
 */

if (!defined('ABSPATH')) {
    exit;
}

function prismpath_legacy_redirect_map(): array
{
    return array(
        '/about-neuroaffirming-mental-health-care-assessments-support' => '/approach/',
        '/adultautism' => '/adhd-autism-assessments/',
        '/adult-autism-assessment' => '/adhd-autism-assessments/',
        '/adult-autism-evaluation' => '/adhd-autism-assessments/',
        '/workingadults' => '/adhd-autism-assessments/',
        '/neuroaffirming-therapy' => '/therapy/',
        '/sensory-based-adult-autism-adhd-occupational-therapy' => '/occupational-therapy/',
        '/sensory-based-adult-autism-ADHD-occupational-therapy' => '/occupational-therapy/',
        '/expert-neurodivergent-care-team' => '/team/',
        '/meet-our-team' => '/team/',
        '/referralpartners' => '/referral-partners/',
        '/accommodations' => '/accommodations/',
        '/group-peer-support' => '/group-support/',
        '/group-programs' => '/group-support/',
        '/neuroaffirming-adult-psychiatric-care' => '/psychiatry/',
    );
}

function prismpath_redirect_legacy_paths(): void
{
    if (is_admin() || wp_doing_ajax()) {
        return;
    }

    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $path = '/' . trim((string) $path, '/');
    if ('/' === $path) {
        return;
    }

    $map = prismpath_legacy_redirect_map();
    if (isset($map[$path])) {
        if (untrailingslashit($path) === untrailingslashit($map[$path])) {
            return;
        }
        wp_safe_redirect(home_url($map[$path]), 301);
        exit;
    }
}
add_action('template_redirect', 'prismpath_redirect_legacy_paths', 0);
