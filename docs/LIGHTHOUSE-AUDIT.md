# Lighthouse Audit Evidence

Date: 2026-05-09

Target: local WordPress Studio at `http://localhost:8882`

## Summary

All audited public templates reached `100` for Accessibility, Best Practices, and SEO after the final service-card link text fix. Performance ranged from `97` to `100` in local WordPress Studio, with the remaining variance tied to local root document response/FCP timing rather than missing image dimensions, unminified CSS, render-blocking theme assets, color contrast, layout shift, or generic link text.

Theme-level performance fixes completed:

- Added optimized WebP variants for hero, whole-family, insurance, and team images.
- Added responsive `srcset` variants for the hero and whole-family imagery.
- Added explicit image dimensions, async decoding, lazy loading where appropriate, and high-priority loading for the homepage hero image.
- Added generated `main.min.css` and wired the small production stylesheet to inline the minified CSS when available.
- Inlined the tiny production navigation script when available to avoid a render-blocking request.
- Added local favicon, touch icon, and web manifest assets, and disabled WordPress remote emoji asset output.
- Fixed CTA color contrast.
- Replaced generic `Learn more` service-card links with descriptive link text.

## Route Scores

| Route | Performance | Accessibility | Best Practices | SEO | Lighthouse server note |
| --- | ---: | ---: | ---: | ---: | --- |
| `/` | 100 | 100 | 100 | 100 | Root document took 710 ms |
| `/services/` | 100 | 100 | 100 | 100 | Root document took 1,050 ms |
| `/therapy/` | 100 | 100 | 100 | 100 | Root document took 700 ms |
| `/psychiatry/` | 100 | 100 | 100 | 100 | Root document took 1,190 ms |
| `/adhd-autism-assessments/` | 100 | 100 | 100 | 100 | Root document took 890 ms |
| `/occupational-therapy/` | 100 | 100 | 100 | 100 | Root document took 840 ms |
| `/whole-family-mental-health/` | 99 | 100 | 100 | 100 | Root document took 790 ms |
| `/approach/` | 99 | 100 | 100 | 100 | Root document took 700 ms |
| `/group-support/` | 99 | 100 | 100 | 100 | Root document took 730 ms |
| `/referral-partners/` | 98 | 100 | 100 | 100 | Root document took 990 ms |
| `/accommodations/` | 100 | 100 | 100 | 100 | Root document took 800 ms |
| `/insurance-payment/` | 100 | 100 | 100 | 100 | Root document took 810 ms |
| `/team/` | 100 | 100 | 100 | 100 | Root document took 1,130 ms |
| `/contact/` | 98 | 100 | 100 | 100 | Root document took 840 ms |
| `/privacy-policy/` | 97 | 100 | 100 | 100 | Root document took 1,160 ms |
| `/hipaa-policy/` | 99 | 100 | 100 | 100 | Root document took 690 ms |
| `/accessibility-statement/` | 100 | 100 | 100 | 100 | Root document took 740 ms |

## Production Recommendation

For a production `100` Performance run, retest the same templates on staging with full-page caching, optimized hosting/PHP workers, HTTPS, compression, and CDN/static asset caching enabled. The current theme package has addressed the template-level Lighthouse findings found during the Studio audit.
