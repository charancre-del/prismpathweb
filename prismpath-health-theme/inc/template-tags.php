<?php
/**
 * Template helpers and default content.
 *
 * @package Prismpath_Health
 */

if (!defined('ABSPATH')) {
    exit;
}

function prismpath_asset(string $path): string
{
    return PRISMPATH_THEME_URI . '/assets/' . ltrim($path, '/');
}

function prismpath_team_photo_url(int $post_id): string
{
    $photo = (string) get_post_meta($post_id, '_prismpath_team_photo', true);
    if ('' === $photo) {
        return '';
    }

    return prismpath_asset('images/team/' . sanitize_file_name($photo));
}

function prismpath_booking_url(string $fallback = '/contact/#consult'): string
{
    $url = prismpath_setting('booking_url', '');
    return $url ? $url : home_url($fallback);
}

function prismpath_whole_family_booking_url(): string
{
    $url = prismpath_setting('whole_family_booking_url', '');
    return $url ? $url : prismpath_booking_url('/contact/#consult');
}

function prismpath_chroma_early_start_url(): string
{
    $url = prismpath_setting('chroma_early_start_url', 'https://chromaearlystart.com/');
    return $url ? $url : 'https://chromaearlystart.com/';
}

function prismpath_phone_href(string $number): string
{
    return 'tel:' . preg_replace('/[^0-9+]/', '', $number);
}

function prismpath_render_logo(bool $light = false): void
{
    $text_class = $light ? 'logo-text logo-text-light' : 'logo-text';
    ?>
    <span class="brand-lockup" aria-label="<?php esc_attr_e('Prismpath Health', 'prismpath-health'); ?>">
        <span class="brand-mark" aria-hidden="true">
            <svg viewBox="0 0 64 64" role="img" focusable="false">
                <path d="M32 6 58 54H6L32 6Z" fill="none" stroke="currentColor" stroke-width="4" stroke-linejoin="round"/>
                <path d="M18 44c8-12 16-17 28-19" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                <path d="M22 50c8-10 17-15 31-17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".42"/>
                <path d="M31 12v42" stroke="currentColor" stroke-width="1.5" opacity=".35"/>
            </svg>
        </span>
        <span class="<?php echo esc_attr($text_class); ?>">
            <span>Prismpath</span>
            <span>Health</span>
        </span>
    </span>
    <?php
}

function prismpath_services(): array
{
    return array(
        array(
            'title' => 'Therapy',
            'slug' => 'therapy',
            'icon' => 'chat',
            'summary' => 'Individual, couples, and family therapy that honors each person in the room.',
            'url' => home_url('/therapy/'),
        ),
        array(
            'title' => 'Psychiatry',
            'slug' => 'psychiatry',
            'icon' => 'brain',
            'summary' => 'Thoughtful medication management and psychiatric care that sees the whole self.',
            'url' => home_url('/psychiatry/'),
        ),
        array(
            'title' => 'ADHD & Autism Assessments',
            'slug' => 'assessments',
            'icon' => 'search',
            'summary' => 'Comprehensive, respectful evaluations for adults seeking clarity and next steps.',
            'url' => home_url('/adhd-autism-assessments/'),
        ),
        array(
            'title' => 'Occupational Therapy',
            'slug' => 'occupational-therapy',
            'icon' => 'hands',
            'summary' => 'Practical support for sensory needs, routines, executive function, and daily life.',
            'url' => home_url('/occupational-therapy/'),
        ),
        array(
            'title' => 'Whole Family Mental Health',
            'slug' => 'whole-family-mental-health',
            'icon' => 'family',
            'summary' => 'Caregiver and family-system support, with pediatric therapy pathways through Chroma Early Start.',
            'url' => home_url('/whole-family-mental-health/'),
        ),
    );
}

function prismpath_service_by_slug(string $slug): ?array
{
    foreach (prismpath_services() as $service) {
        if ($slug === $service['slug']) {
            return $service;
        }
    }
    return null;
}

function prismpath_icon(string $name): string
{
    $icons = array(
        'chat' => '<svg viewBox="0 0 24 24"><path d="M4 5h16v11H8l-4 4V5Z"/><path d="M8 9h8M8 13h5"/></svg>',
        'brain' => '<svg viewBox="0 0 24 24"><path d="M9 4a4 4 0 0 0-4 4v1a4 4 0 0 0 0 8v1a3 3 0 0 0 5 2.2V4.8A3 3 0 0 0 9 4Zm6 0a3 3 0 0 0-1 .2v19a3 3 0 0 0 5-2.2v-1a4 4 0 0 0 0-8V8a4 4 0 0 0-4-4Z"/><path d="M10 9H7m7 3h4m-4 5h3"/></svg>',
        'search' => '<svg viewBox="0 0 24 24"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/><path d="M8 10h5M10.5 7.5v5"/></svg>',
        'hands' => '<svg viewBox="0 0 24 24"><path d="M7 11V5a2 2 0 0 1 4 0v8"/><path d="M11 10V4a2 2 0 0 1 4 0v9"/><path d="M5 12c0 6 3 9 8 9 4 0 7-3 7-8V8a2 2 0 0 0-4 0v5"/><path d="M4 14 2 12a2 2 0 0 1 3-3l2 2"/></svg>',
        'family' => '<svg viewBox="0 0 24 24"><circle cx="8" cy="7" r="3"/><circle cx="17" cy="8" r="2.5"/><path d="M3 21v-2a5 5 0 0 1 10 0v2"/><path d="M13 21v-1.2a4 4 0 0 1 8 0V21"/></svg>',
        'check' => '<svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></svg>',
    );
    return $icons[$name] ?? $icons['check'];
}

function prismpath_default_pages(): array
{
    return array(
        'therapy' => array(
            'title' => 'Neuroaffirming Therapy',
            'intro' => 'Therapy at Prismpath Health is collaborative, strengths-based, and responsive to how you process the world.',
            'points' => array('Individual, couples, and family therapy', 'Support for anxiety, burnout, identity, transitions, and relationships', 'Trauma-informed and LGBTQ+ affirming care', 'Coordination with psychiatry or occupational therapy when useful'),
        ),
        'psychiatry' => array(
            'title' => 'Psychiatric Care',
            'intro' => 'Thoughtful medication evaluation and ongoing psychiatric care that considers your goals, routines, sensory profile, and full context.',
            'points' => array('Psychiatric evaluation and medication management', 'Collaborative treatment planning', 'Coordination with therapy and OT', 'Virtual care where provider licensure allows'),
        ),
        'adhd-autism-assessments' => array(
            'title' => 'ADHD & Autism Assessments',
            'intro' => 'Respectful adult assessments designed to help you understand your mind, your history, and your next steps.',
            'points' => array('Masking-aware, culturally responsive evaluation', 'Clear report and recommendations', 'Support for underdiagnosed adults', 'Referral and care coordination when needed'),
        ),
        'occupational-therapy' => array(
            'title' => 'Occupational Therapy in Daily Life',
            'intro' => 'Occupational therapy helps turn insight into practical supports for the rhythms, environments, and routines of everyday life.',
            'points' => array('Sensory regulation and environment fit', 'Executive function systems', 'Routines, habits, and transitions', 'Work, school, home, and life skills'),
        ),
        'whole-family-mental-health' => array(
            'title' => 'Whole Family Mental Health',
            'intro' => 'Family-systems mental health support for parents and caregivers, with pediatric ABA, speech, and occupational therapy services available through Chroma Early Start.',
            'points' => array('Parent and caregiver guidance', 'Family communication and connection', 'Co-regulation and everyday strategies', 'Coordination across adult and pediatric providers', 'Pediatric therapy pathways through Chroma Early Start'),
        ),
        'approach' => array(
            'title' => 'A Neuroaffirming Approach to Whole-Person Care',
            'intro' => 'We see you as more than a diagnosis. Our care is grounded in collaboration, evidence, lived experience, and practical support.',
            'points' => array('Your brain is not broken', 'You are the expert on your life', 'Support should reduce masking, not reward it', 'Care should fit real daily life'),
        ),
        'group-support' => array(
            'title' => 'Group Support',
            'intro' => 'Connection can be part of care. Prismpath Health group offerings create structured, facilitated spaces for learning and community.',
            'points' => array('Professionally facilitated support', 'Flexible virtual participation', 'Identity-affirming community', 'Topic-based groups as available'),
        ),
        'referral-partners' => array(
            'title' => 'Referral Partners',
            'intro' => 'We collaborate with clinicians, schools, employers, and community partners who want affirming care pathways for the people they support.',
            'points' => array('Clear referral pathways', 'Respectful coordination', 'Updates with client consent', 'Support across therapy, psychiatry, OT, assessments, and family care'),
        ),
        'accommodations' => array(
            'title' => 'Accommodations Support',
            'intro' => 'We help clients understand their needs and navigate care documentation where clinically appropriate.',
            'points' => array('Care-aligned documentation support', 'Practical strategy planning', 'Coordination with treating providers', 'Guidance without overpromising outcomes'),
        ),
    );
}

function prismpath_page_content(string $slug): array
{
    $pages = prismpath_default_pages();
    return $pages[$slug] ?? array(
        'title' => get_the_title(),
        'intro' => get_the_excerpt() ?: 'Prismpath Health provides neuroaffirming mental health care for individuals, couples, caregivers, and families.',
        'points' => array(),
    );
}
