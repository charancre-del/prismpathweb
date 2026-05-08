# Strict Launch QA Gate

Do not launch until every item below passes on staging.

## WordPress

- Theme activates without fatal errors.
- All three plugins activate without fatal errors.
- Required pages exist and use the expected templates.
- Primary and footer navigation contain Prismpath Health URLs.
- Permalinks are set to Post name.
- Contact settings are populated with final production details.

## SEO

- `/?sitemap=xml` returns valid XML.
- `robots.txt` includes the sitemap URL.
- Canonical URLs use the new Prismpath Health domain.
- Homepage emits `MedicalOrganization` and `WebSite` schema.
- Service pages emit `Service` schema.
- Breadcrumb schema appears on non-home pages.
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

## Rollback

- Production site backup exists.
- Previous active theme/plugin package is available.
- DNS or hosting rollback contact/process is known before activation.
