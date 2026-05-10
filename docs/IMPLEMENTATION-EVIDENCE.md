# Implementation Evidence

Date: 2026-05-10

## Theme + Plugin SEO/GEO Launch Implementation Completed

- Re-enabled the non-blog resource hub requested for Google SEO, AI Overviews eligibility, GEO, and AI citation support.
- Published and seeded `/resources/` plus seven resource-guide pages:
  - Adult ADHD & Autism Assessment Guide.
  - Neuroaffirming Therapy for Adults.
  - Psychiatric Medication Management for Neurodivergent Adults.
  - Occupational Therapy for Sensory Regulation and Daily Life.
  - Whole Family Mental Health and Caregiver Support.
  - Accommodations and Documentation Support.
  - Insurance and Payment Guide.
- Added resource guide rendering through the theme instead of posts/blogs, with long-form sections, visible FAQs, related service links, references, updated dates, and consult CTAs.
- Expanded the metabox/seeder model so resource pages and service templates can seed and override SEO title, meta description, hero copy, long-form sections, FAQs, and related links without overwriting later editor content.
- Updated service templates to link to relevant resource guides and contact/next-step pages.
- Updated navigation and footer fallbacks to include the Resources hub.
- Hardened the SEO plugin:
  - WordPress core duplicate canonical output is removed so indexed pages emit one canonical.
  - Resource guides are treated as `MedicalWebPage`.
  - Resource hub is treated as `CollectionPage`.
  - Service, FAQ, breadcrumb, person, website, and organization schema remain aligned with visible page content.
  - Legal schema naming now uses `Lbee Health Practive Group PLLC dba Prismpath Health`.
- Replaced the inherited Chroma-style GEO feed internals with Prismpath-specific machine-readable facts at `/wp-json/chroma-agent/v1/geo-feed`.
  - Feed includes brand, legal DBA, virtual-care service-area policy, conservative claims policy, services, resources, public pages, team records, canonical URLs, last-modified values, record hashes, and reference URLs.
  - Public GEO feed exposes 5 services, 7 resources, and published site pages for AI citation context.
  - Protected Agent API routes remain authenticated; unauthenticated production checks return `401`.
- Updated crawler policy to allow Google and major AI crawlers in the plugin-generated robots output and in the static production `robots.txt` deployment override.
- Adjusted theme compatibility for the target host's WordPress runtime by lowering `Requires PHP` to `7.4` and replacing the PHP 8-only `str_ends_with()` call.

## Production SSH Deployment Verification Completed

- SSH access verified with the existing RSA key for `yg8zlbs@65.181.120.41`.
- Production WordPress install located at `/home/yg8zlbs/public_html`.
- Synced source folders into production:
  - `wp-content/themes/prismpath-health-theme`
  - `wp-content/plugins/prismpath-seo-engine`
  - `wp-content/plugins/prismpath-consult-form`
  - `wp-content/plugins/prismpath-lead-log`
  - `wp-content/plugins/chroma-agent-api`
- Activated production theme and plugins with WP-CLI:
  - Active theme: `prismpath-health-theme`.
  - Active plugins: `chroma-agent-api`, `prismpath-consult-form`, `prismpath-lead-log`, and `prismpath-seo-engine`.
- Set production permalinks to `/%postname%/` and flushed rewrite rules.
- Updated production site identity:
  - Blog name: `Prismpath Health`.
  - Tagline: `Whole-family neuroaffirming mental health care`.
- Production seeded pages verified as published, including all service pages, policy pages, `/resources/`, and all seven resource guides.
- Production route checks against `https://yg8zo397cu.wpdns.site` returned `200` for home, services, service pages, approach, insurance/payment, team, contact, group support, referral partners, accommodations, privacy, HIPAA, accessibility, resource hub, resource guides, sitemap, and GEO feed.
- Production SEO checks verified tested indexed pages emit one meta description, one canonical URL, `index, follow, max-image-preview:large`, and JSON-LD where expected.
- Production sitemap check verified `/sitemap.xml` returns XML and includes service, HIPAA, team, and resource URLs.
- Production GEO feed check verified:
  - `success: true`
  - Brand: `Prismpath Health`
  - Legal entity: `Lbee Health Practive Group PLLC dba Prismpath Health`
  - Service count: `5`
  - Resource count: `7`
  - Public crawler policy present.
- Production contact form checks:
  - Nonce renders.
  - Invalid POST redirects to `?prismpath_consult=error#consult`.
  - Valid POST redirects to `?prismpath_consult=sent#consult`.
  - Lead log records valid submissions.
  - Test lead records were deleted after validation.
- Production Agent API protected route checks:
  - `/wp-json/chroma-agent/v1/discovery`: `401` unauthenticated.
  - `/wp-json/chroma-agent/v1/resources`: `401` unauthenticated.
  - `/wp-json/chroma-agent/v1/seo/schema`: `401` unauthenticated.
- Production temporary-domain robots caveat:
  - A static `robots.txt` with AI crawler allow rules and sitemap was deployed to `/home/yg8zlbs/public_html/robots.txt`.
  - `https://yg8zo397cu.wpdns.site/robots.txt?deploy=20260510` returns the correct allow rules.
  - The normal temporary `https://yg8zo397cu.wpdns.site/robots.txt` URL is still serving a platform/CDN-cached `Disallow: /` response despite a successful Rocket.net CDN purge API response.
  - This must be rechecked on the final production domain before Google indexing; the source/site configuration is correct, but the temporary host/CDN edge is still returning the old robots body.

## Local Studio Completion Verification Completed

- Synced the latest theme and plugin source folders into `C:\Users\chara\Studio\prismpath`.
- Started WordPress Studio and verified `http://localhost:8882` is reachable.
- Verified local WordPress settings:
  - Active theme: `prismpath-health-theme`.
  - Active plugins: `chroma-agent-api`, `prismpath-consult-form`, `prismpath-lead-log`, and `prismpath-seo-engine`.
  - Permalink structure: `/%postname%/`.
  - Site URL and home URL: `http://localhost:8882`.
- Triggered the non-destructive seeder and verified missing SEO/template metabox values were filled.
- Added seeder cleanup for the default `Hello world!` starter post; local status is now `draft`.
- Verified required pages are published: Home, Services, Therapy, Psychiatry, ADHD & Autism Assessments, Occupational Therapy, Whole Family Mental Health, Approach, Insurance & Payment, Team, Contact, Group Support, Referral Partners, Accommodations, Privacy Policy, HIPAA Policy, and Accessibility Statement.
- Verified temporary resource pages remain `draft`, including `/resources/` and the prior resource-guide child pages.
- Verified the primary menu contains Prismpath routes for Services, Assessments, Whole Family Mental Health, Approach, Contact, and Insurance.
- Browser/HTTP route verification:
  - `200`: `/`, `/services/`, `/therapy/`, `/psychiatry/`, `/adhd-autism-assessments/`, `/occupational-therapy/`, `/whole-family-mental-health/`, `/approach/`, `/insurance-payment/`, `/team/`, `/bio/jonathan-hinds/`, `/contact/`, `/group-support/`, `/referral-partners/`, `/accommodations/`, `/privacy-policy/`, `/hipaa-policy/`, `/accessibility-statement/`, `/sitemap.xml` when redirects are followed, and `/robots.txt`.
  - `404`: `/not-a-real-page-for-seo-audit/`.
  - Local WordPress redirects `/sitemap.xml` to the built-in `/wp-sitemap.xml`; the Prismpath custom sitemap route also responds through `/?prismpath_sitemap=1` with `<urlset>` and `<lastmod>` entries.
- SEO/schema verification:
  - Indexed pages emit unique meta descriptions, local canonical URLs, and `index, follow, max-image-preview:large`.
  - 404 and search routes emit `noindex, follow` and no canonical URL.
  - Home emits `DataFeed`, `MedicalOrganization`, `WebSite`, and `WebPage` JSON-LD.
  - Service pages emit `MedicalWebPage`, `Service`, visible `FAQPage`, and `BreadcrumbList` JSON-LD.
  - Team bio pages emit `Person` JSON-LD.
  - JSON-LD parsed successfully on checked public routes.
- Content verification:
  - No checked public route contains `Prismpath Wellness`, `lbeehealth`, or public-facing `LBee Health`.
  - Legal text includes `Lbee Health Practive Group PLLC dba Prismpath Health`.
  - Pediatric ABA, speech, and pediatric occupational therapy references route to Chroma Early Start.
  - Insurance page lists Optum, Aetna, AvMed, Oscar, Blue Cross Blue Shield, Employers Health Network, Medicare, Cigna, and Humana with benefit-verification language and no individual coverage guarantee.
  - Contact form includes `Whole Family Mental Health`.
- Form verification:
  - Contact form renders with nonce.
  - Invalid POST redirects to `?prismpath_consult=error#consult`.
  - Valid POST redirects to `?prismpath_consult=sent#consult`.
  - Lead logging created local `prismpath_lead` records for the QA submissions.
- Agent API verification:
  - REST namespace responds at `/wp-json/chroma-agent/v1`.
  - Unauthenticated local HTTP access to protected Agent API routes returns `403` with `caa_https_required`.
  - Created a local Studio QA API key in the Agent API key table for validation.
  - Authenticated proxy-HTTPS checks passed for `/discovery`, `/seo/schema`, `/schema/seo`, and `/seo/options`.
  - Authenticated dry-run schema write passed for `/seo/schema/{post_id}` with no blocked alias keys.
- Lighthouse local rerun:
  - `/`, `/therapy/`, `/whole-family-mental-health/`, `/insurance-payment/`, `/contact/`, and `/hipaa-policy/` scored `100` for Performance, Accessibility, Best Practices, and SEO.
  - Lighthouse produced Windows temp-folder cleanup warnings after successful report generation; JSON scores were still produced and read.
- Screenshot evidence captured:
  - `docs/prismpath-local-home-desktop.png`
  - `docs/prismpath-local-home-mobile.png`
  - `docs/prismpath-local-therapy.png`
  - `docs/prismpath-local-whole-family.png`
  - `docs/prismpath-local-insurance.png`
  - `docs/prismpath-local-contact.png`
  - `docs/prismpath-local-hipaa.png`
  - `docs/prismpath-local-404.png`
- Production-only items remain intentionally gated because final production contact/domain/SMTP/API operator details are not all ready: real domain canonicals, production SMTP delivery, final production API operators, DNS/CDN/cache settings, hosting redirects, and rollback process.

## SEO Content Expansion Verification Completed

- Expanded service-style page content for Therapy, Psychiatry, ADHD & Autism Assessments, Occupational Therapy, Whole Family Mental Health, Approach, Group Support, Referral Partners, and Accommodations.
- Optimized the actual page and template pages rather than publishing blog/resource pages.
- Retired the temporary `/resources/` hub and resource child pages to draft.
- Added one-time theme content seeding for SEO titles, meta descriptions, insurance page updates, and temporary resource-page retirement.
- Verified local WordPress Studio routes return `200` for all service pages, insurance/payment, team, contact, privacy, accessibility, `/sitemap.xml`, and `/robots.txt`.
- Verified every checked public route has a canonical URL and unique meta description.
- Verified JSON-LD parses on all checked public routes.
- Verified service pages emit `Service` schema and pages with visible FAQs emit `FAQPage` schema.
- Verified no checked public route emits `Article` schema after removing the unrequested guide/blog layer.
- Verified `/resources/` and prior resource-guide child URLs return `404` locally after the retirement seeder runs.
- Verified `/sitemap.xml` includes service, insurance, whole-family, team, contact, policy, and accessibility pages, and does not include `/resources/`.
- Verified the Chroma Agent API schema endpoints are registered at `/wp-json/chroma-agent/v1/seo/schema` and `/wp-json/chroma-agent/v1/schema/seo`; local HTTP access correctly returns `caa_https_required`.
- Added a Prismpath page editor metabox for SEO title, meta description, hero title, intro copy, side-panel copy, service highlights, long-form sections, and FAQs.
- Added `inc/seeder.php` to seed the editable metabox values for every service-style template page from the approved Prismpath content model without overwriting later editor changes.
- Wired Agent API SEO allowlists to Prismpath-specific SEO and template-content meta keys.
- Wired the SEO plugin to render Agent API-managed schema overrides when `_chroma_schema_override` and schema payload meta are present.
- Verified the public rendered route audit found no `LBee`, `Prismpath Wellness`, `lbeehealth`, `childcare`, or unsupported bee-brand language.
- Verified the Whole Family content keeps pediatric ABA, speech therapy, and pediatric occupational therapy routed to Chroma Early Start.
- Verified the contact form still exposes a Whole Family Mental Health service option.
- Added Insurance & Payment coverage from the LBee codebase:
  - Published `/insurance-payment/`.
  - Added accepted plan logos for Optum, Aetna, AvMed, Oscar, Blue Cross Blue Shield, Employers Health Network, Medicare, Cigna, and Humana.
  - Added reusable insurance/payment section to `/services/` and relevant service-detail pages.
  - Preserved benefit-verification and patient-responsibility language instead of guaranteeing individual coverage before verification.
- Added and seeded `/hipaa-policy/` with HIPAA notice-style policy content, SEO metadata, sitemap coverage, and a footer link.
- Added legal entity disclosure as `Lbee Health Practive Group PLLC dba Prismpath Health` in legal pages, footer copyright, settings, and organization/service schema.
- Completed Lighthouse template audit evidence in `docs/LIGHTHOUSE-AUDIT.md`: all audited public templates scored `100` for Accessibility, Best Practices, and SEO; Studio Performance ranged from `97` to `100` because Lighthouse reported local root document response/FCP timing as the remaining limiter.
- Captured browser screenshot evidence outside the repo:
  - `C:\Users\chara\AppData\Local\Temp\prismpath-insurance-payment.png`

## Git Sync Outputs

- `prismpath-health-theme/` is the WordPress theme source folder.
- `plugins/prismpath-seo-engine/`, `plugins/prismpath-lead-log/`, `plugins/prismpath-consult-form/`, and `plugins/chroma-agent-api/` are the WordPress plugin source folders.
- ZIP artifacts are intentionally not tracked; deployment is by git/file sync into `wp-content/themes/` and `wp-content/plugins/`.

## Static Verification Completed

- PHP lint passed across all theme and plugin PHP files.
- Theme CSS verifier passed through `npm run build`.
- Audited and hardened the Prismpath SEO Engine for healthcare SEO:
  - Page titles and meta descriptions now fall back to the seeded static-page SEO model when editor meta is missing.
  - Public indexed pages emit `index, follow, max-image-preview:large`; search and 404 routes emit `noindex, follow`.
  - Homepage schema uses `MedicalOrganization` with stable `@id`, legal DBA naming, local logo/image assets, national virtual-care service area, and mental health specialties.
  - Service templates emit `MedicalWebPage`, `Service`, breadcrumb, and visible-FAQ schema where appropriate.
  - Team bios emit `Person` schema linked back to the Prismpath Health organization.
  - Agent API schema overrides now require a strict truthy `_chroma_schema_override` value before replacing default schema.
  - Sitemap output now includes `<lastmod>` values for published pages and team profiles.
  - Deprecated unauthenticated sitemap ping calls were removed/no-oped; sitemap discovery remains through `/robots.txt` and `/sitemap.xml`.
- Audited and hardened Chroma Agent API schema wiring:
  - Optional HMAC signatures now use the intended newline-delimited canonical message.
  - `_chroma_schema_override` is sanitized as a boolean while JSON-LD schema payload keys are preserved.
- Lighthouse-driven template fixes added optimized WebP assets, explicit image dimensions, inline minified CSS and navigation script loading, system font stacks, improved CTA contrast, and descriptive service-card link text.
- Local asset hardening completed: browser-requested images, favicons, touch icons, manifest, CSS, JS, and fonts are served from the theme/local WordPress install rather than outside asset hosts.
- Disabled WordPress remote emoji asset output so public pages no longer reference `s.w.org` emoji image bases.
- Public theme/plugin string audit returned no hits for legacy or incorrect brand strings:
  - `LBee`
  - `LBee Health`
  - `Prismpath Wellness`
  - `lbeehealth`

## WordPress Studio Verification Completed

- Installed the theme and all four production plugins into `C:\Users\chara\Studio\prismpath`.
- Started the Studio site at `http://localhost:8882`.
- Activated `prismpath-health-theme`.
- Activated `prismpath-seo-engine`, `prismpath-lead-log`, `prismpath-consult-form`, and `chroma-agent-api`.
- Verified WordPress recognizes the theme and plugins through Studio WP-CLI.
- Verified required pages were seeded and published, including `Whole Family Mental Health`.
- Verified the primary menu was created and assigned.
- Verified `/`, `/services/`, `/whole-family-mental-health/`, and `/contact/` respond locally.
- Verified `/sitemap.xml` returns XML and includes the Whole Family Mental Health URL.
- Verified `robots.txt` includes the Prismpath sitemap URL.
- Verified the rendered homepage title is `Prismpath Health - Whole-family neuroaffirming mental health care`.
- Captured desktop and mobile browser screenshots through Playwright:
  - `docs/prismpath-homepage-desktop.png`
  - `docs/prismpath-homepage-mobile.png`
  - `docs/prismpath-team-desktop.png`

## End-to-End QA Completed

- Verified required public routes return `200`: `/`, `/services/`, `/therapy/`, `/psychiatry/`, `/adhd-autism-assessments/`, `/occupational-therapy/`, `/whole-family-mental-health/`, `/approach/`, `/team/`, `/contact/`, `/group-support/`, `/referral-partners/`, `/accommodations/`, `/sitemap.xml`, and `/robots.txt`.
- Verified legacy redirects return `301` to new Prismpath routes for adult assessment, neuroaffirming therapy, occupational therapy, and referral partner paths.
- Verified the Whole Family page positions Prismpath as caregiver/family-system mental health support and routes pediatric ABA, speech, and occupational therapy to Chroma Early Start.
- Verified the Chroma Early Start pediatric therapy CTA points to `https://chromaearlystart.com/` and opens in a new tab.
- Verified the consult form renders a nonce, accepts a Whole Family Mental Health service selection, accepts text input, and shows no browser console errors.
- Verified the Agent API route namespace is registered at `/wp-json/chroma-agent/v1/*` and unauthenticated HTTP access is blocked with `caa_https_required`.
- Fixed a self-redirect issue on `/accommodations/`; the canonical page now returns `200`.
- Verified all public template routes have real titles/H1s and no public rendered LBee, Prismpath Wellness, or lbeehealth strings.
- Seeded 15 migrated team profiles with real bio content and theme-managed team photos.
- Published Privacy Policy and Accessibility Statement pages, and moved the default WordPress Sample Page to draft.

## Staging Visual QA Completed

- Staging URL checked: `https://yg8zo397cu.wpdns.site/`.
- Fixed staging image delivery by correcting deployed theme asset permissions to `755` for directories and `644` for files.
- Verified 35 browser-requested theme assets return successfully, including hero images, insurance logos, team photos, favicon, touch icon, manifest, CSS, and JavaScript.
- Audited 26 public routes at desktop and mobile widths, for 52 route/viewport checks total.
- Confirmed no viewport horizontal overflow, no broken loaded images, and no unexpected browser console errors on the checked routes.
- Confirmed the intentional fake 404 route returns `404` with no layout overflow.
- Fixed the `/resources/` desktop service-card grid so long guide titles no longer expand the five-column grid beyond the viewport.
- Added a visible Prismpath SEO admin menu and verified WordPress registers `Prismpath SEO` in the dashboard menu.
- Added and seeded `/about/` as a standalone About Us page with unique SEO title, meta description, canonical URL, and footer/menu access.
- Removed the legacy `/about` to `/approach/` redirect so the new About Us page can render.
- Verified staging `/team/` renders 15 team cards matching the migrated LBee TeamPage bio source.

## Source Structure Verified

- Theme source folder contains `prismpath-health-theme/` as the WordPress theme root.
- Plugin source folders each contain one plugin root.
- Deployment artifacts are source directories, not ZIP packages.
- Theme includes `style.css`, `functions.php`, `index.php`, templates, assets, settings, redirects, and activation seeding.

## Implemented Production Items

- Custom WordPress theme for Prismpath Health.
- Full public content rewrite around Prismpath Health.
- Homepage, service, contact, team, bio, and support templates.
- Required page/menu seeder on theme activation.
- Team profile seeder for migrated clinician bios.
- Privacy/accessibility page seeder and Sample Page cleanup.
- Legacy 301 redirect map.
- SEO/schema/sitemap/robots plugin.
- Consult form plugin with nonce, sanitization, validation, email, redirect state, and optional lead logging.
- Private lead-log plugin.
- Chroma Agent API plugin from Wptstchroma for API-key protected automation routes.
- Install, content, redirect, launch QA, and rollback documentation.

## Remaining External Gate

The source-level implementation is complete and has been verified in WordPress Studio. Final launch validation must still be executed in the target WordPress staging/production environment because SMTP delivery, production domain canonical URLs, analytics, indexing controls, and hosting rules are environment-specific.
