    </main>

    <?php
    $email = prismpath_setting('primary_email', get_option('admin_email'));
    $phone = prismpath_setting('phone', '');
    $text = prismpath_setting('text_number', '');
    ?>
    <footer class="site-footer">
        <div class="container footer-grid">
            <div class="footer-brand">
                <?php prismpath_render_logo(true); ?>
                <p>Neuroaffirming mental health care for individuals, couples, caregivers, and family systems.</p>
                <p class="footer-small">Virtual care nationwide where provider licensure allows.</p>
            </div>

            <div>
                <h2>Explore</h2>
                <nav class="footer-nav" aria-label="<?php esc_attr_e('Footer services', 'prismpath-health'); ?>">
                    <a href="<?php echo esc_url(home_url('/services/')); ?>">Services</a>
                    <a href="<?php echo esc_url(home_url('/adhd-autism-assessments/')); ?>">Assessments</a>
                    <a href="<?php echo esc_url(home_url('/whole-family-mental-health/')); ?>">Whole Family Mental Health</a>
                    <a href="<?php echo esc_url(home_url('/resources/')); ?>">Resources</a>
                    <a href="<?php echo esc_url(home_url('/insurance-payment/')); ?>">Insurance & Payment</a>
                    <a href="<?php echo esc_url(home_url('/approach/')); ?>">Approach</a>
                </nav>
            </div>

            <div>
                <h2>Contact</h2>
                <div class="footer-contact">
                    <?php if ($email) : ?><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a><?php endif; ?>
                    <?php if ($phone) : ?><a href="<?php echo esc_url(prismpath_phone_href($phone)); ?>">Call <?php echo esc_html($phone); ?></a><?php endif; ?>
                    <?php if ($text) : ?><a href="<?php echo esc_url(prismpath_phone_href($text)); ?>">Text <?php echo esc_html($text); ?></a><?php endif; ?>
                    <span>Virtual care where provider licensure allows</span>
                </div>
            </div>

            <div>
                <h2>Important</h2>
                <nav class="footer-nav" aria-label="<?php esc_attr_e('Footer important links', 'prismpath-health'); ?>">
                    <a href="<?php echo esc_url(home_url('/team/')); ?>">Our Team</a>
                    <a href="<?php echo esc_url(home_url('/contact/')); ?>">Contact</a>
                    <a href="<?php echo esc_url(prismpath_setting('privacy_url', home_url('/privacy-policy/'))); ?>">Privacy Policy</a>
                    <a href="<?php echo esc_url(prismpath_setting('accessibility_url', home_url('/accessibility-statement/'))); ?>">Accessibility Statement</a>
                </nav>
            </div>
        </div>
        <div class="container footer-bottom">
            <p>&copy; <?php echo esc_html(gmdate('Y')); ?> Prismpath Health. All rights reserved.</p>
            <a href="<?php echo esc_url(home_url('/sitemap.xml')); ?>">Site Map</a>
        </div>
    </footer>

    <?php wp_footer(); ?>
</body>
</html>
