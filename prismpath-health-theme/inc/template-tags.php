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

function prismpath_insurance_plans(): array
{
    return array(
        array('name' => 'Optum', 'logo' => 'optum.png'),
        array('name' => 'Aetna', 'logo' => 'aetna.png'),
        array('name' => 'AvMed', 'logo' => 'avmed.png'),
        array('name' => 'Oscar', 'logo' => 'oscar.png'),
        array('name' => 'Blue Cross Blue Shield', 'logo' => 'bcbs.png'),
        array('name' => 'Employers Health Network', 'logo' => 'ehn.png'),
        array('name' => 'Medicare', 'logo' => 'medicare.png'),
        array('name' => 'Cigna', 'logo' => 'cigna.png'),
        array('name' => 'Humana', 'logo' => 'humana.png'),
    );
}

function prismpath_payment_options(): array
{
    return array(
        'Benefits verification before scheduling so clients can understand estimated costs.',
        'Self-pay pathways when a plan does not cover a service or a client prefers not to use insurance.',
        'CareCredit financing for assessment-related out-of-pocket costs when appropriate.',
        'Deposits may be used to hold insurance-based assessment appointments and are applied toward copays, deductibles, coinsurance, or other patient responsibility.',
    );
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
            'slug' => 'adhd-autism-assessments',
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
            'seo_title' => 'Neuroaffirming Therapy for Adults | Prismpath Health',
            'meta_description' => 'Adult neuroaffirming therapy for anxiety, burnout, relationships, identity, transitions, and daily life, with insurance verification where available.',
            'intro' => 'Therapy at Prismpath Health is collaborative, strengths-based, and responsive to how you process the world.',
            'points' => array('Individual, couples, and family therapy', 'Support for anxiety, burnout, identity, transitions, and relationships', 'Trauma-informed and LGBTQ+ affirming care', 'Coordination with psychiatry or occupational therapy when useful', 'Insurance verification and self-pay pathways'),
            'schema_service_type' => 'Mental health therapy',
            'sections' => array(
                array('heading' => 'Therapy that starts with context', 'body' => 'Care is shaped around your lived experience, communication style, sensory needs, culture, relationships, and goals. Sessions may focus on anxiety, depression, burnout, masking, identity, trauma recovery, major transitions, parenting stress, or relationship patterns. The goal is not to force a standard version of wellness, but to build language, strategies, and support that fit your actual life.'),
                array('heading' => 'What sessions may include', 'body' => 'Depending on clinical fit, therapy may include reflective work, skills practice, nervous-system awareness, relationship support, planning for difficult conversations, and coordination with other Prismpath providers. Care is collaborative and adjusted over time as your needs change.'),
                array('heading' => 'A careful fit for neurodivergent adults', 'body' => 'Many adults come to therapy after years of being misunderstood, overcorrected, or asked to mask discomfort. Prismpath uses a neuroaffirming frame, which means therapy can name real challenges without treating neurodivergence itself as a problem to erase.'),
            ),
            'faqs' => array(
                array('question' => 'Do I need a diagnosis to start therapy?', 'answer' => 'No. Many clients begin therapy while they are still exploring patterns, identity, stress, or support needs. If assessment becomes useful, your provider can discuss options.'),
                array('question' => 'Can therapy coordinate with psychiatry or occupational therapy?', 'answer' => 'When clinically appropriate and with consent, Prismpath providers can coordinate care so therapy, psychiatric support, and occupational therapy are working from a shared understanding.'),
            ),
            'related_links' => array('neuroaffirming-therapy-for-adults', 'psychiatric-medication-management-neurodivergent-adults', 'occupational-therapy-sensory-regulation-adults'),
        ),
        'psychiatry' => array(
            'title' => 'Psychiatric Care',
            'seo_title' => 'Psychiatric Care and Medication Management | Prismpath Health',
            'meta_description' => 'Thoughtful psychiatric evaluation and medication management for adults, with insurance verification and virtual care where licensure allows.',
            'intro' => 'Thoughtful medication evaluation and ongoing psychiatric care that considers your goals, routines, sensory profile, and full context.',
            'points' => array('Psychiatric evaluation and medication management', 'Collaborative treatment planning', 'Coordination with therapy and OT', 'Virtual care where provider licensure allows', 'Benefits verification before your first appointment'),
            'schema_service_type' => 'Psychiatric care and medication management',
            'sections' => array(
                array('heading' => 'Medication support with the whole person in view', 'body' => 'Psychiatric care at Prismpath is designed to be careful, collaborative, and practical. Your provider considers current symptoms, medical and mental health history, sensory factors, sleep, routines, stressors, preferences, and prior medication experiences before discussing options.'),
                array('heading' => 'Evaluation, planning, and follow-up', 'body' => 'The process may include diagnostic clarification, medication review, education about risks and benefits, and follow-up visits to monitor fit. Medication is one possible tool, not the only definition of care.'),
                array('heading' => 'Coordinated support when helpful', 'body' => 'With consent, psychiatry can coordinate with therapy, occupational therapy, assessment, or outside providers. This helps reduce fragmented care and supports treatment planning that reflects daily life.'),
            ),
            'faqs' => array(
                array('question' => 'Does a psychiatry visit guarantee medication?', 'answer' => 'No. A psychiatric evaluation may lead to medication options, therapy recommendations, care coordination, additional assessment, or another plan depending on clinical fit.'),
                array('question' => 'Can psychiatric care be virtual?', 'answer' => 'Prismpath offers virtual care where provider licensure and clinical appropriateness allow. Some needs may require local or in-person resources.'),
            ),
            'related_links' => array('psychiatric-medication-management-neurodivergent-adults', 'adult-adhd-autism-assessment-guide', 'neuroaffirming-therapy-for-adults'),
        ),
        'adhd-autism-assessments' => array(
            'title' => 'ADHD & Autism Assessments',
            'seo_title' => 'Adult ADHD and Autism Assessments | Prismpath Health',
            'meta_description' => 'Respectful adult ADHD and Autism assessments with insurance, self-pay, CareCredit, and deposit pathways explained before scheduling.',
            'intro' => 'Respectful adult assessments designed to help you understand your mind, your history, and your next steps.',
            'points' => array('Masking-aware, culturally responsive evaluation', 'Clear report and recommendations', 'Support for underdiagnosed adults', 'Referral and care coordination when needed', 'Insurance, self-pay, CareCredit, and deposit guidance'),
            'schema_service_type' => 'Adult ADHD and Autism assessment',
            'sections' => array(
                array('heading' => 'Assessment for adults seeking clarity', 'body' => 'Adults often seek ADHD or Autism assessment after years of adapting, masking, being misread, or wondering why common strategies never quite fit. Prismpath assessments are designed to look at patterns across development, school, work, relationships, sensory experience, executive functioning, communication, and daily life.'),
                array('heading' => 'What the process may explore', 'body' => 'Assessment may include clinical interviews, standardized measures, history review, and discussion of strengths, needs, and differential considerations. The purpose is to support clarity and next steps, not to reduce your experience to a label.'),
                array('heading' => 'Recommendations that are usable', 'body' => 'When appropriate, results can inform therapy, psychiatry, occupational therapy, accommodations planning, or referrals. Reports and recommendations are written to be practical and respectful, while avoiding promises about outcomes or approvals by outside institutions.'),
            ),
            'faqs' => array(
                array('question' => 'Can adults be assessed for ADHD or Autism?', 'answer' => 'Yes. Many adults pursue evaluation later in life, especially when earlier concerns were missed, masked, or explained in other ways.'),
                array('question' => 'Does an assessment guarantee a diagnosis?', 'answer' => 'No. The assessment process is designed to evaluate fit carefully and provide recommendations based on clinical findings.'),
                array('question' => 'Can I use insurance or self-pay for an assessment?', 'answer' => 'Prismpath can verify insurance benefits where available and can discuss self-pay, CareCredit, and deposit options before scheduling. Coverage and patient responsibility depend on the plan, state, provider, deductible, and service.'),
            ),
            'related_links' => array('adult-adhd-autism-assessment-guide', 'insurance-payment-guide', 'accommodations-documentation-support', 'neuroaffirming-therapy-for-adults'),
        ),
        'occupational-therapy' => array(
            'title' => 'Occupational Therapy in Daily Life',
            'seo_title' => 'Occupational Therapy for Adults | Prismpath Health',
            'meta_description' => 'Adult occupational therapy support for sensory regulation, executive functioning, routines, and daily life, with insurance and self-pay options.',
            'intro' => 'Occupational therapy helps turn insight into practical supports for the rhythms, environments, and routines of everyday life.',
            'points' => array('Sensory regulation and environment fit', 'Executive function systems', 'Routines, habits, and transitions', 'Work, school, home, and life skills', 'Insurance verification and self-pay options'),
            'schema_service_type' => 'Adult occupational therapy',
            'sections' => array(
                array('heading' => 'Practical support for daily life', 'body' => 'Occupational therapy focuses on participation: the routines, tasks, environments, and transitions that make up a day. For neurodivergent adults, OT may support sensory regulation, executive functioning, energy management, self-care, work or school systems, home routines, and sustainable habits.'),
                array('heading' => 'Sensory and executive function strategies', 'body' => 'Care may include sensory mapping, environmental changes, planning tools, routine design, transition supports, and problem-solving around demands that feel harder than they look from the outside. Strategies are tested and adjusted so they are usable, not just ideal on paper.'),
                array('heading' => 'Connected care when it helps', 'body' => 'OT can coordinate with therapy, psychiatry, assessment, or accommodation planning when the same daily-life patterns show up across different parts of care.'),
            ),
            'faqs' => array(
                array('question' => 'Is occupational therapy only for children?', 'answer' => 'No. Occupational therapy can support adults with daily routines, sensory needs, executive functioning, work, school, home, and life skills.'),
                array('question' => 'Does Prismpath provide pediatric OT?', 'answer' => 'Prismpath focuses this pathway on adult occupational therapy. Pediatric therapy services are available through Chroma Early Start.'),
            ),
            'related_links' => array('occupational-therapy-sensory-regulation-adults', 'adult-adhd-autism-assessment-guide', 'accommodations-documentation-support'),
        ),
        'whole-family-mental-health' => array(
            'title' => 'Whole Family Mental Health',
            'seo_title' => 'Whole Family Mental Health and Caregiver Support | Prismpath Health',
            'meta_description' => 'Caregiver-centered mental health support for family systems, communication, co-regulation, routines, and coordinated care planning.',
            'intro' => 'Family-systems mental health support for parents and caregivers, with pediatric ABA, speech, and occupational therapy services available through Chroma Early Start.',
            'points' => array('Parent and caregiver guidance', 'Family communication and connection', 'Co-regulation and everyday strategies', 'Coordination across adult and pediatric providers', 'Pediatric therapy pathways through Chroma Early Start'),
            'schema_service_type' => 'Caregiver and family mental health support',
            'sections' => array(
                array('heading' => 'Support for the family system', 'body' => 'Whole Family Mental Health helps caregivers understand patterns, reduce shame, improve communication, and build routines that support the people in the household. The work may include parent guidance, caregiver stress support, co-regulation strategies, family communication, and coordination with care teams.'),
                array('heading' => 'Caregiver support is mental health support', 'body' => 'Parents and caregivers often carry the invisible load of planning, advocacy, transitions, school communication, and emotional labor. Prismpath supports the adults in the system so the whole family has more usable tools and more room to breathe.'),
                array('heading' => 'Pediatric therapy pathways', 'body' => 'When a child needs ABA, speech therapy, or pediatric occupational therapy, Prismpath routes those services to Chroma Early Start. Prismpath can remain focused on caregiver-centered mental health and care coordination while pediatric specialists address child therapy needs.'),
            ),
            'faqs' => array(
                array('question' => 'Is Whole Family Mental Health pediatric therapy?', 'answer' => 'No. Prismpath provides caregiver and family-system mental health support. Pediatric ABA, speech therapy, and pediatric occupational therapy are available through Chroma Early Start.'),
                array('question' => 'Can caregivers participate without their child being a Prismpath client?', 'answer' => 'Caregiver support may be appropriate even when a child receives services elsewhere. Clinical fit, consent, and care coordination needs are reviewed case by case.'),
            ),
            'related_links' => array('whole-family-mental-health-caregiver-support', 'neuroaffirming-therapy-for-adults', 'occupational-therapy-sensory-regulation-adults'),
        ),
        'approach' => array(
            'title' => 'A Neuroaffirming Approach to Whole-Person Care',
            'seo_title' => 'Neuroaffirming Mental Health Approach | Prismpath Health',
            'meta_description' => 'Prismpath Health provides neuroaffirming, whole-person mental health care grounded in collaboration, context, and practical support.',
            'intro' => 'We see you as more than a diagnosis. Our care is grounded in collaboration, evidence, lived experience, and practical support.',
            'points' => array('Your brain is not broken', 'You are the expert on your life', 'Support should reduce masking, not reward it', 'Care should fit real daily life'),
            'schema_service_type' => 'Neuroaffirming mental health care',
            'sections' => array(
                array('heading' => 'Affirming does not mean vague', 'body' => 'Neuroaffirming care can be warm and clinically careful at the same time. Prismpath names real distress, impairment, trauma, burnout, sensory overload, and executive-function challenges without framing neurodivergence as a defect.'),
                array('heading' => 'Care that respects autonomy', 'body' => 'Treatment planning is collaborative. Providers listen for what has worked, what has caused harm, and what feels realistic. The aim is to support choice, communication, daily function, and self-understanding.'),
                array('heading' => 'Integrated when useful', 'body' => 'Therapy, psychiatry, occupational therapy, assessment, and family support can stand alone or coordinate when it helps. The right level of coordination depends on clinical need and consent.'),
            ),
            'faqs' => array(
                array('question' => 'What does neuroaffirming care mean at Prismpath?', 'answer' => 'It means care is designed to respect neurodivergent experience while still addressing distress, support needs, safety, relationships, and daily-life goals.'),
                array('question' => 'Is every service right for every person?', 'answer' => 'No. The best care pathway depends on clinical fit, goals, provider scope, licensure, and practical needs.'),
            ),
            'related_links' => array('neuroaffirming-therapy-for-adults', 'adult-adhd-autism-assessment-guide', 'whole-family-mental-health-caregiver-support'),
        ),
        'group-support' => array(
            'title' => 'Group Support',
            'seo_title' => 'Neuroaffirming Group Support | Prismpath Health',
            'meta_description' => 'Facilitated group support for connection, learning, and neuroaffirming mental health skills when groups are available.',
            'intro' => 'Connection can be part of care. Prismpath Health group offerings create structured, facilitated spaces for learning and community.',
            'points' => array('Professionally facilitated support', 'Flexible virtual participation', 'Identity-affirming community', 'Topic-based groups as available'),
            'schema_service_type' => 'Mental health group support',
            'sections' => array(
                array('heading' => 'Structured connection', 'body' => 'Group support can reduce isolation and create a place to learn from others with similar patterns or life experiences. Groups may focus on neurodivergent adulthood, burnout, executive functioning, caregiver support, relationships, or other topics as offerings become available.'),
                array('heading' => 'Clinical boundaries matter', 'body' => 'Groups are facilitated spaces, not a replacement for emergency care or every form of individual treatment. Participants receive information about fit, expectations, privacy, and next steps before joining.'),
            ),
            'faqs' => array(
                array('question' => 'Are groups always available?', 'answer' => 'No. Group offerings depend on staffing, scheduling, clinical fit, and enrollment. Contact Prismpath to ask about current options.'),
                array('question' => 'Can group support replace individual care?', 'answer' => 'Sometimes group support is enough for a specific need, but many people benefit from individual therapy, psychiatry, OT, or assessment as well.'),
            ),
            'related_links' => array('neuroaffirming-therapy-for-adults', 'whole-family-mental-health-caregiver-support'),
        ),
        'referral-partners' => array(
            'title' => 'Referral Partners',
            'seo_title' => 'Referral Partners for Neuroaffirming Mental Health Care | Prismpath Health',
            'meta_description' => 'Referral pathways for clinicians, schools, employers, and community partners seeking neuroaffirming adult and family mental health care.',
            'intro' => 'We collaborate with clinicians, schools, employers, and community partners who want affirming care pathways for the people they support.',
            'points' => array('Clear referral pathways', 'Respectful coordination', 'Updates with client consent', 'Support across therapy, psychiatry, OT, assessments, and family care'),
            'schema_service_type' => 'Mental health referral coordination',
            'sections' => array(
                array('heading' => 'A clearer referral path', 'body' => 'Referral partners may contact Prismpath when an adult, caregiver, couple, family, employee, student, or client may benefit from neuroaffirming therapy, psychiatry, occupational therapy, assessment, or caregiver-centered support.'),
                array('heading' => 'Coordination with consent', 'body' => 'Prismpath coordinates carefully and only shares care updates when appropriate consent and privacy requirements are in place. Referral communication should not include unnecessary clinical detail through public website forms.'),
            ),
            'faqs' => array(
                array('question' => 'Can referral partners send detailed records through the website?', 'answer' => 'No. The website form is for basic inquiries. Detailed clinical records should only be shared through approved, secure processes.'),
                array('question' => 'Does Prismpath guarantee acceptance of referrals?', 'answer' => 'No. Fit depends on clinical scope, provider availability, licensure, and client needs.'),
            ),
            'related_links' => array('adult-adhd-autism-assessment-guide', 'accommodations-documentation-support', 'whole-family-mental-health-caregiver-support'),
        ),
        'accommodations' => array(
            'title' => 'Accommodations Support',
            'seo_title' => 'Accommodations and Documentation Support | Prismpath Health',
            'meta_description' => 'Care-aligned accommodations support for adults, with ongoing therapy, psychiatry, insurance, and self-pay pathways when clinically appropriate.',
            'intro' => 'We help clients understand their needs and navigate care documentation where clinically appropriate.',
            'points' => array('Care-aligned documentation support', 'Practical strategy planning', 'Coordination with treating providers', 'Guidance without overpromising outcomes'),
            'schema_service_type' => 'Accommodations support',
            'sections' => array(
                array('heading' => 'Support for understanding needs', 'body' => 'Accommodations support can help clarify what makes work, school, testing, routines, or environments more accessible. The process may draw from assessment findings, therapy, psychiatry, occupational therapy, and daily-life observations.'),
                array('heading' => 'Documentation without guarantees', 'body' => 'When clinically appropriate, providers may support documentation or recommendations. Prismpath cannot guarantee that a school, employer, testing board, insurer, or other third party will approve a requested accommodation.'),
                array('heading' => 'Practical planning matters too', 'body' => 'Documentation is only one part of support. Prismpath can also help translate needs into routines, communication strategies, environmental changes, and care coordination.'),
                array('heading' => 'Ongoing care and payment pathways', 'body' => 'When someone is interested in ongoing therapy or medication management, Prismpath can discuss insurance verification and self-pay options. Assessment-related costs may also include deposit or financing pathways depending on the service and plan.'),
            ),
            'faqs' => array(
                array('question' => 'Can Prismpath guarantee accommodations?', 'answer' => 'No. Prismpath can provide clinically appropriate support and documentation when indicated, but outside organizations make their own decisions.'),
                array('question' => 'Do I need an assessment first?', 'answer' => 'Sometimes an assessment is helpful or required by an outside organization. In other cases, current clinical care may be enough to guide planning.'),
            ),
            'related_links' => array('accommodations-documentation-support', 'adult-adhd-autism-assessment-guide', 'occupational-therapy-sensory-regulation-adults'),
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

function prismpath_resource_pages(): array
{
    return array(
        'adult-adhd-autism-assessment-guide' => array(
            'title' => 'Adult ADHD & Autism Assessment Guide',
            'seo_title' => 'Adult ADHD and Autism Assessment Guide | Prismpath Health',
            'meta_description' => 'A conservative guide to adult ADHD and Autism assessment, including when evaluation may help, what it can explore, and what results can support.',
            'excerpt' => 'A practical guide for adults considering ADHD or Autism assessment.',
            'sections' => array(
                array('heading' => 'Why adults seek assessment', 'body' => 'Many adults consider ADHD or Autism assessment after recognizing long-standing patterns in attention, sensory experience, communication, social exhaustion, burnout, transitions, or executive functioning. Some people have wondered for years. Others arrive after a child, partner, friend, or clinician names a pattern that finally makes sense.'),
                array('heading' => 'What assessment can and cannot do', 'body' => 'An assessment can organize history, current functioning, strengths, support needs, and differential considerations. It can inform recommendations for therapy, psychiatry, occupational therapy, accommodations, or further care. It should not be presented as a guarantee of a diagnosis, service approval, workplace outcome, or school accommodation.'),
                array('heading' => 'What to prepare', 'body' => 'Helpful information may include childhood patterns, school and work history, prior diagnoses or evaluations, medication history, sensory patterns, executive-function challenges, relationship patterns, and examples of supports that help. You do not need to have every answer before reaching out.'),
                array('heading' => 'After the assessment', 'body' => 'Next steps may include a written report, feedback conversation, care recommendations, referrals, accommodations discussion, or coordinated treatment planning. Good assessment should leave you with clearer language and practical options.'),
            ),
            'faqs' => array(
                array('question' => 'Can ADHD and Autism be identified in adulthood?', 'answer' => 'Yes. Many adults pursue assessment after earlier patterns were missed, masked, or interpreted through another lens.'),
                array('question' => 'Will an assessment automatically lead to medication or accommodations?', 'answer' => 'No. Assessment findings can inform care planning, but medication decisions and third-party accommodations are separate processes.'),
            ),
            'related_services' => array('adhd-autism-assessments', 'therapy', 'accommodations'),
        ),
        'neuroaffirming-therapy-for-adults' => array(
            'title' => 'Neuroaffirming Therapy for Adults',
            'seo_title' => 'Neuroaffirming Therapy for Adults | Prismpath Health Resources',
            'meta_description' => 'Learn what neuroaffirming therapy can look like for adults navigating masking, burnout, identity, anxiety, relationships, and daily life.',
            'excerpt' => 'How adult therapy can support real life without treating neurodivergence as a defect.',
            'sections' => array(
                array('heading' => 'What neuroaffirming therapy means', 'body' => 'Neuroaffirming therapy respects neurodivergent experience while still taking distress seriously. It can address anxiety, depression, trauma, relationship stress, masking, burnout, identity, sensory overwhelm, and life transitions without making the goal to appear less neurodivergent.'),
                array('heading' => 'Common therapy themes', 'body' => 'Adults may use therapy to understand patterns, recover from chronic invalidation, practice boundaries, navigate work or school demands, support relationships, process late identification, or build routines that reduce overload. Therapy can also help translate insight into language for everyday communication.'),
                array('heading' => 'A collaborative care plan', 'body' => 'The right plan may include individual therapy, couples or family work, psychiatry, occupational therapy, assessment, or outside supports. Coordination should happen only when useful and with appropriate consent.'),
            ),
            'faqs' => array(
                array('question' => 'Is neuroaffirming therapy only for people with a formal diagnosis?', 'answer' => 'No. Therapy can be appropriate for people who are diagnosed, self-identifying, questioning, or seeking support for related patterns.'),
                array('question' => 'Does affirming care ignore hard symptoms?', 'answer' => 'No. Affirming care can address real distress and support needs while avoiding shame-based or masking-only goals.'),
            ),
            'related_services' => array('therapy', 'psychiatry', 'occupational-therapy'),
        ),
        'psychiatric-medication-management-neurodivergent-adults' => array(
            'title' => 'Psychiatric Medication Management for Neurodivergent Adults',
            'seo_title' => 'Psychiatric Medication Management for Neurodivergent Adults | Prismpath Health',
            'meta_description' => 'A careful overview of psychiatric medication management for neurodivergent adults, including evaluation, collaboration, and follow-up.',
            'excerpt' => 'What thoughtful medication support can include for neurodivergent adults.',
            'sections' => array(
                array('heading' => 'Medication as one possible support', 'body' => 'Psychiatric medication can be helpful for some adults, but it is not the only path to care. A careful evaluation considers symptoms, goals, prior medication experiences, health history, sleep, routines, sensory patterns, and current stressors.'),
                array('heading' => 'Why follow-up matters', 'body' => 'Medication management includes monitoring benefits, side effects, fit, adherence barriers, and changing life demands. The plan may change over time as needs become clearer or circumstances shift.'),
                array('heading' => 'Care coordination', 'body' => 'Medication decisions may be more useful when the prescriber understands therapy goals, occupational therapy strategies, assessment findings, and daily-life context. Coordination should be clinically appropriate and consent-based.'),
            ),
            'faqs' => array(
                array('question' => 'Is medication required for ADHD or anxiety care?', 'answer' => 'No. Medication may be considered when clinically appropriate, but therapy, OT, environmental support, and routines may also be part of care.'),
                array('question' => 'Can medication management be virtual?', 'answer' => 'Virtual psychiatric care may be available where provider licensure and clinical appropriateness allow.'),
            ),
            'related_services' => array('psychiatry', 'therapy', 'adhd-autism-assessments'),
        ),
        'occupational-therapy-sensory-regulation-adults' => array(
            'title' => 'Occupational Therapy for Sensory Regulation and Daily Life',
            'seo_title' => 'Occupational Therapy for Sensory Regulation and Daily Life | Prismpath Health',
            'meta_description' => 'Adult occupational therapy can support sensory regulation, executive functioning, routines, transitions, and daily-life participation.',
            'excerpt' => 'A guide to adult OT for sensory needs, routines, executive functioning, and participation.',
            'sections' => array(
                array('heading' => 'OT focuses on participation', 'body' => 'Occupational therapy supports the activities and roles that make up everyday life. For adults, that can include self-care, work, school, home routines, communication, transitions, transportation, meal planning, rest, and social participation.'),
                array('heading' => 'Sensory regulation in real environments', 'body' => 'Sensory support may include identifying patterns, reducing unnecessary overload, changing environments, planning recovery time, and building strategies for transitions or high-demand moments. The goal is practical fit, not perfect performance.'),
                array('heading' => 'Executive function and routines', 'body' => 'OT can help design systems for planning, initiation, follow-through, organization, habit formation, and task completion. Effective supports are tested against real barriers and adjusted over time.'),
            ),
            'faqs' => array(
                array('question' => 'Can adults benefit from occupational therapy?', 'answer' => 'Yes. Adult OT can support daily-life skills, routines, sensory needs, executive functioning, and participation in work, school, home, and community life.'),
                array('question' => 'Where should families go for pediatric OT?', 'answer' => 'Pediatric occupational therapy pathways are available through Chroma Early Start.'),
            ),
            'related_services' => array('occupational-therapy', 'adhd-autism-assessments', 'accommodations'),
        ),
        'whole-family-mental-health-caregiver-support' => array(
            'title' => 'Whole Family Mental Health and Caregiver Support',
            'seo_title' => 'Whole Family Mental Health and Caregiver Support | Prismpath Health Resources',
            'meta_description' => 'Caregiver-centered mental health support can help family systems with communication, co-regulation, routines, advocacy, and coordinated care.',
            'excerpt' => 'How caregiver support can strengthen the family system while pediatric therapy stays with Chroma Early Start.',
            'sections' => array(
                array('heading' => 'The caregiver load is real', 'body' => 'Caregivers often manage schedules, school communication, transitions, advocacy, emotional repair, sensory needs, sibling dynamics, and their own stress at the same time. Whole Family Mental Health recognizes that supporting caregivers can support the entire household.'),
                array('heading' => 'What family-system work may include', 'body' => 'Care may focus on communication, co-regulation, parent guidance, routines, expectations, repair after conflict, boundaries, and coordination with therapy or school teams. It can also support caregivers who are neurodivergent themselves.'),
                array('heading' => 'Where pediatric therapy fits', 'body' => 'Prismpath keeps pediatric ABA, speech therapy, and pediatric occupational therapy routed to Chroma Early Start. Prismpath can focus on caregiver-centered mental health and care coordination while pediatric specialists address child therapy services.'),
            ),
            'faqs' => array(
                array('question' => 'Is Whole Family Mental Health the same as child therapy?', 'answer' => 'No. Prismpath supports caregivers and family systems. Pediatric ABA, speech therapy, and pediatric OT are available through Chroma Early Start.'),
                array('question' => 'Can caregiver support include coordination with a child provider?', 'answer' => 'Coordination may be possible when appropriate consent, privacy requirements, and clinical fit are in place.'),
            ),
            'related_services' => array('whole-family-mental-health', 'therapy', 'occupational-therapy'),
        ),
        'accommodations-documentation-support' => array(
            'title' => 'Accommodations and Documentation Support',
            'seo_title' => 'Accommodations and Documentation Support | Prismpath Health Resources',
            'meta_description' => 'Understand how clinical care, assessment, and daily-life needs can inform accommodations planning without promising third-party approval.',
            'excerpt' => 'A careful guide to accommodations planning, clinical documentation, and practical supports.',
            'sections' => array(
                array('heading' => 'Accommodations begin with needs', 'body' => 'Useful accommodations are connected to real barriers. A person may need support with sensory load, task initiation, time, communication, transitions, testing, documentation, meetings, or environmental demands.'),
                array('heading' => 'Documentation has limits', 'body' => 'Providers may be able to document clinically relevant needs when appropriate. Outside organizations such as schools, employers, testing boards, and insurers decide their own approval processes. No provider can guarantee a specific outcome.'),
                array('heading' => 'Strategies alongside paperwork', 'body' => 'Planning may include communication scripts, environmental adjustments, routine design, assistive tools, care coordination, and therapy or OT strategies. Paperwork can be helpful, but daily-life supports matter too.'),
            ),
            'faqs' => array(
                array('question' => 'Can Prismpath write accommodation letters?', 'answer' => 'When clinically appropriate, a provider may support documentation. The specific format and limits depend on the care relationship and request.'),
                array('question' => 'Are accommodations guaranteed if I have documentation?', 'answer' => 'No. Third parties make their own decisions. Documentation can support a request, but it cannot guarantee approval.'),
            ),
            'related_services' => array('accommodations', 'adhd-autism-assessments', 'occupational-therapy'),
        ),
        'insurance-payment-guide' => array(
            'title' => 'Insurance and Payment Options',
            'seo_title' => 'Insurance and Payment Options | Prismpath Health Resources',
            'meta_description' => 'Learn about Prismpath Health insurance plans, benefits verification, self-pay options, CareCredit, deposits, and patient responsibility.',
            'excerpt' => 'A practical guide to accepted plans, benefits verification, self-pay, CareCredit, and deposits.',
            'sections' => array(
                array('heading' => 'Accepted insurance plans', 'body' => 'Prismpath accepts Medicare and major commercial plans. Listed plans include Optum, Aetna, AvMed, Oscar, Blue Cross Blue Shield, Employers Health Network, Medicare, Cigna, and Humana.'),
                array('heading' => 'Benefits verification first', 'body' => 'Before scheduling, the team can verify benefits and provide an estimated cost picture. Actual coverage, deductible application, copays, coinsurance, prior authorization, and patient responsibility depend on the plan, state, provider, service, and member benefits.'),
                array('heading' => 'Self-pay and financing', 'body' => 'Self-pay may be available when a client does not want to use insurance or when a service is not covered by a plan. For assessments, Prismpath can discuss CareCredit financing and deposit workflows when appropriate.'),
                array('heading' => 'How deposits work', 'body' => 'For insurance-based assessment appointments, deposits may be used to hold the appointment and are applied toward out-of-pocket costs such as copays, deductibles, coinsurance, or other patient responsibility. If responsibility is lower than the deposit, the difference may be refunded after successful program completion.'),
            ),
            'faqs' => array(
                array('question' => 'Which insurance plans does Prismpath list?', 'answer' => 'Prismpath lists Optum, Aetna, AvMed, Oscar, Blue Cross Blue Shield, Employers Health Network, Medicare, Cigna, and Humana. Benefits still need to be verified for the specific service, provider, state, and member plan.'),
                array('question' => 'Can I self-pay?', 'answer' => 'Yes. Self-pay pathways may be available if insurance is not used or if a service is not covered by a plan.'),
                array('question' => 'Is CareCredit available?', 'answer' => 'CareCredit financing may be discussed for assessment-related out-of-pocket costs when appropriate.'),
            ),
            'related_services' => array('adhd-autism-assessments', 'therapy', 'psychiatry', 'occupational-therapy'),
        ),
    );
}

function prismpath_resource_by_slug(string $slug): ?array
{
    $resources = prismpath_resource_pages();
    return $resources[$slug] ?? null;
}

function prismpath_content_record_by_slug(string $slug): ?array
{
    $pages = prismpath_default_pages();
    if (isset($pages[$slug])) {
        return $pages[$slug];
    }

    return prismpath_resource_by_slug($slug);
}

function prismpath_resource_url(string $slug): string
{
    return home_url('/resources/' . sanitize_title($slug) . '/');
}
