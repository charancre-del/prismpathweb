# Install Guide

1. Sync `prismpath-health-theme/` to `wp-content/themes/prismpath-health-theme/`.
2. Sync plugins into `wp-content/plugins/`:
   - `plugins/prismpath-lead-log/`
   - `plugins/prismpath-consult-form/`
   - `plugins/prismpath-seo-engine/`
   - `plugins/chroma-agent-api/`
3. Activate `prismpath-health-theme`.
4. Activate plugins in this order:
   - `prismpath-lead-log`
   - `prismpath-consult-form`
   - `prismpath-seo-engine`
   - `chroma-agent-api`
5. Go to Appearance -> Prismpath Settings and set:
   - Primary email
   - Phone
   - Text number
   - Main booking URL
   - Whole Family booking URL
   - Chroma Early Start URL
   - Social links
   - Privacy and accessibility URLs
6. If using Agent API, create API keys only for trusted operators and require HTTPS in production.
7. Confirm Settings -> Reading uses the generated Home page.
8. Confirm Settings -> Permalinks is set to Post name.
9. Confirm Appearance -> Menus has `Prismpath Primary` assigned to Primary Menu.
10. Add clinician profiles under Team Members if they were not imported from the legacy site.

The theme activation seeder creates missing required pages and a primary menu. It does not overwrite existing pages.
