# Strict Launch QA Gate

Do not launch until every item below passes on staging.

## WordPress

- Theme activates without fatal errors.
- All four production plugins activate without fatal errors.
- Required pages exist and use the expected templates.
- No blog posts are published unless intentionally requested later.
- Resource hub and resource-guide pages are published intentionally for Google SEO, AI Overviews eligibility, GEO, and AI citation support.
- Insurance & Payment page is published and lists accepted plans with benefit-verification language.
- HIPAA Policy page is published and final legal/privacy review is complete.
- Legal-facing content, footer copyright, and organization schema identify `Lbee Health Practive Group PLLC dba Prismpath Health`.
- Primary and footer navigation contain Prismpath Health URLs.
- Permalinks are set to Post name.
- Contact settings are populated with final production details.

## SEO

- `/sitemap.xml` returns valid XML.
- `robots.txt` includes the sitemap URL.
- Temporary hosting/CDN domains must be checked for platform-level robots overrides before indexing; final production domain must serve `Allow: /` and the sitemap URL without relying on cache-busting query strings.
- Canonical URLs use the new Prismpath Health domain.
- Homepage emits `MedicalOrganization` and `WebSite` schema.
- Service pages emit `Service` schema.
- Public service and template pages are SEO optimized with unique meta descriptions, canonical URLs, visible FAQ content where used, and conservative healthcare-safe copy.
- The Chroma Agent API schema routes are installed for managed schema operations.
- Prismpath SEO & Template Content metabox changes are reflected on the matching front-end templates.
- Agent API SEO allowlists include Prismpath SEO/title/meta and template-content override keys.
- FAQ schema appears only where matching FAQ content is visible on the page.
- Breadcrumb schema appears on non-home pages.
- Every indexed page has a unique meta description and canonical URL.
- Insurance copy does not guarantee individual member coverage before benefits verification.
- Legacy URLs in `REDIRECTS.csv` return 301 and land on the expected new URL.
- Search crawl finds no public LBee Health or Prismpath Wellness strings.

## Forms

- Valid consult form submission sends mail and redirects with success state.
- Invalid submission returns error state.
- Lead logging works when `prismpath-lead-log` is active.
- Form copy does not request detailed clinical history.

## Accessibility

- Keyboard navigation reaches menu, CTA buttons, form fields, and footer links.
- Focus states are visible.
- Inputs have labels.
- Color contrast passes for body text, CTAs, nav, and footer.
- Heading order is logical.
- Images have useful alt text or are marked decorative.

## Performance

- No layout shift in hero, services, consult form, or footer.
- Hero image dimensions are stable.
- CSS and JS load without 404s.
- Mobile navigation works without console errors.
- No large unused debug assets are included in production.
- Rendered pages should not request external image, font, favicon, manifest, CSS, JS, or emoji assets; expected public asset requests should resolve under the site host or theme asset directory.
- Lighthouse should be rerun on staging after production caching/hosting is enabled; local Studio audit evidence reached `100` for Accessibility, Best Practices, and SEO across audited templates, with Performance ranging from `97` to `100` because of local root document response/FCP timing.

## Rollback

- Production site backup exists.
- Previous active theme/plugin folders or git revision are available.
- DNS or hosting rollback contact/process is known before activation.
