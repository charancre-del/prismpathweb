<?php
/**
 * About page template.
 *
 * @package Prismpath_Health
 */

get_header();
?>
<section class="page-hero">
    <div class="container narrow">
        <h1>About Prismpath Health.</h1>
        <p>Neuroaffirming mental health care, assessment, psychiatry, occupational therapy, and caregiver-centered support for adults and families.</p>
        <a class="button button-primary" href="<?php echo esc_url(home_url('/contact/')); ?>">Start a Conversation</a>
    </div>
</section>

<section class="page-content">
    <div class="container detail-layout">
        <div class="prose">
            <h2>Different does not mean broken.</h2>
            <p>Prismpath Health was built around a simple belief: people deserve care that helps them understand their brain, their context, their strengths, and their real-life needs without being reduced to a label.</p>
            <p>Our work is especially shaped by neuroaffirming care for adults with ADHD, Autism, anxiety, burnout, trauma histories, sensory needs, and complex family or caregiving responsibilities. We also support people who are still trying to understand what kind of care or assessment may fit.</p>

            <div class="content-section">
                <h2>What neuroaffirming care means here</h2>
                <p>Neuroaffirming care does not mean ignoring distress or avoiding practical change. It means we work collaboratively, respect lived experience, and focus on supports that fit the person rather than asking every person to fit the same model.</p>
                <ul class="check-grid">
                    <li><?php prismpath_icon('check'); ?> Care that listens before it labels</li>
                    <li><?php prismpath_icon('check'); ?> Strategies that support daily life</li>
                    <li><?php prismpath_icon('check'); ?> Assessment language that is careful and useful</li>
                    <li><?php prismpath_icon('check'); ?> Collaboration across therapy, psychiatry, OT, and referrals</li>
                </ul>
            </div>

            <div class="content-section">
                <h2>Whole-person, whole-family context</h2>
                <p>Mental health rarely lives in one appointment slot. Work, school, caregiving, sensory load, relationships, sleep, routines, identity, and access needs all matter. Prismpath Health considers those pieces as part of care planning when they are relevant.</p>
                <p>Our Whole Family Mental Health work is caregiver-centered and family-system aware. Pediatric ABA, speech, and pediatric occupational therapy services are routed through Chroma Early Start, so families can find the right pediatric pathway without confusing it with Prismpath adult mental health services.</p>
            </div>
        </div>

        <aside class="detail-list">
            <h2>Care pathways</h2>
            <ul>
                <li><?php prismpath_icon('chat'); ?> <a href="<?php echo esc_url(home_url('/therapy/')); ?>">Neuroaffirming therapy</a></li>
                <li><?php prismpath_icon('brain'); ?> <a href="<?php echo esc_url(home_url('/psychiatry/')); ?>">Psychiatric care</a></li>
                <li><?php prismpath_icon('check'); ?> <a href="<?php echo esc_url(home_url('/adhd-autism-assessments/')); ?>">ADHD & Autism assessments</a></li>
                <li><?php prismpath_icon('hands'); ?> <a href="<?php echo esc_url(home_url('/occupational-therapy/')); ?>">Adult occupational therapy</a></li>
                <li><?php prismpath_icon('family'); ?> <a href="<?php echo esc_url(home_url('/whole-family-mental-health/')); ?>">Whole Family Mental Health</a></li>
                <li><?php prismpath_icon('check'); ?> <a href="<?php echo esc_url(home_url('/team/')); ?>">Meet the team</a></li>
            </ul>
        </aside>
    </div>
</section>

<section class="faq-support-section">
    <div class="container faq-support-grid single-column">
        <div class="support-panel">
            <h2>Care shaped around real life.</h2>
            <p>We combine clinical insight, practical strategy, and respectful collaboration so care feels usable outside the session too.</p>
            <div class="section-actions">
                <a class="button button-primary" href="<?php echo esc_url(home_url('/services/')); ?>">Explore Services</a>
                <a class="button button-outline" href="<?php echo esc_url(home_url('/team/')); ?>">Meet Our Providers</a>
            </div>
        </div>
    </div>
</section>
<?php
get_footer();
